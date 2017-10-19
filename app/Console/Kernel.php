<?php

namespace App\Console;

use App\CfResult;
use App\ExchangeRate;
use App\Order;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->call(function () {
//            $this->getRate();
//        })->daily();

        $schedule->call(function () {
            $this->dealRefund();
        })->everyFiveMinutes();

        $schedule->call(function () {
            $this->makeCfr();
        })->everyFiveMinutes();

        $schedule->call(function () {
            $this->makeRefund();
        })->everyFiveMinutes();

        $schedule->call(function () {
            DB::beginTransaction();
            try {
                $list = Order::where('type', Order::TYPE_REFUND)->where('status', Order::STATUS_PAID)->get();
                foreach ($list as $order) {
                    $bill = DB::table('bills')->where('oid', $order->id)->first();
                    if ($bill) {
                        $yin = $bill->in;
                        DB::table('bills')->where('id', $bill->id)->update(['in' => 0, 'bin' => $order->price]);
                        Log::error('cfr退单：order-id ' . $order->id . ' bill-id ' . $bill->id . ' bill原始in ' . $yin . ' order-price' . $order->price);
                    }
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('账单脚本异常');
                Log::error($e);
            }


        })->daily();

        $schedule->call(function () {
            DB::beginTransaction();
            try {

                $list = Order::where('type', Order::TYPE_DEL_PAID)->where('status', Order::STATUS_PAID)->get();
                foreach ($list as $order) {
                    $bill = DB::table('bills')->where('oid', $order->id)->first();
                    if ($bill) {
                        $yin = $bill->in;
                        DB::table('bills')->where('id', $bill->id)->update(['in' => 0, 'bin' => $order->price]);
                        Log::error('补偿退单：order-id ' . $order->id . ' bill-id ' . $bill->id . ' bill原始in ' . $yin . ' order-price' . $order->price);
                    }
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('账单脚本异常');
                Log::error($e);
            }


        })->daily();

        $schedule->call(function () {
            DB::beginTransaction();
            try {

                $list = Order::where('type', Order::TYPE_CONSUME)->get();
                foreach ($list as $order) {
                    $bill = DB::table('bills')->where('oid', $order->id)->first();
                    if ($bill) {
                        DB::table('bills')->where('id', $bill->id)->update(['bout' => $order->balance]);
                        Log::error('消费：order-id ' . $order->id . ' bill-id ' . $bill->id . ' order-balance' . $order->balance);
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('账单脚本异常');
                Log::error($e);
            }


        })->daily();
    }

    /**
     * cfr失败退款
     */
    private function dealRefund()
    {
        $list = CfResult::where('status', 0)->get();
        foreach ($list as $v) {
            $v->refund();
        }
    }

    /**
     * 已经支付的执行子任务
     */
    private function makeCfr()
    {
        Order::makeCfr();
    }

    /**
     * 未支付超时退单
     */
    private function makeRefund()
    {
        Order::makeRefund();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * 获取汇率
     */
    private function getRate()
    {
        $showapi_appid  = '40811';
        $showapi_secret = 'eab8fb3f739540ea8dcb649bd4f57512';
        $paramArr       = [
            'showapi_appid' => $showapi_appid,
            'code'          => ""
        ];
        $param          = $this->createParam($paramArr, $showapi_secret);
        $url            = 'http://route.showapi.com/105-30?' . $param;
        $client         = new Client();
        $res            = $client->request('GET', $url);
        $body           = $res->getBody()->getContents();
        $arr            = json_decode($body, true);
        if (isset($arr['showapi_res_body']['list'])) {
            foreach ($arr['showapi_res_body']['list'] as $v) {
                if (in_array($v['code'], ['USD', 'CAD', 'GBP', 'EUR', 'JPY'])) {
                    $one          = ExchangeRate::where('apiname', $v['code'])->first();
                    $apirate      = ($v['hui_in'] + $v['hui_out'] + $v['chao_in'] + $v['chao_out']) / 400;
                    $one->apirate = $apirate;
                    $one->save();
                }
            }
        }
    }

    private function createParam($paramArr, $showapi_secret)
    {
        $paraStr = "";
        $signStr = "";
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $signStr .= $key . $val;
                $paraStr .= $key . '=' . urlencode($val) . '&';
            }
        }
        $signStr .= $showapi_secret;
        $sign    = strtolower(md5($signStr));
        $paraStr .= 'showapi_sign=' . $sign;
        return $paraStr;
    }
}

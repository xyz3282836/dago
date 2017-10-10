<?php

namespace App\Console;

use App\Bill;
use App\CfResult;
use App\ExchangeRate;
use App\Order;
use App\User;
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
            $arr = [21596,21570,21547,21535,21531,21516,21439,21436,21413,21392,21322,21321,21317];

            foreach ($arr as $v) {
                \DB::transaction(function () use($v){
                    $order = Order::find($v);
                    if($order){
                        $user = User::find($order->uid);
                        $bill = Bill::where('orderid',$order->orderid)->first();
                        $old = $user->balance;
                        $user->balance -= $order->price;
                        $user->save();

                        $bill->delete();
                        $order->delete();
                        Log::error('uid为'.$user->id.'的用户原始账号余额'.$old.' 还原后，余额'.$user->balance);
                    }

                });


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

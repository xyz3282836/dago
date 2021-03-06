<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/5/12
 * Time: 上午9:59
 */

namespace App\Http\Controllers;


use App\Bill;
use App\ClickFarm;
use App\Exceptions\MsgException;
use App\Order;
use Auth;
use Exception;
use Log;

class PayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'result']);
    }

    /**
     * get
     * 充值
     */
    public function getRecharge()
    {
        //充值时资料要完善
        return view('pay.recharge', ['role' => Auth::user()->role]);
    }

    public function getRechargeBalance()
    {
        return view('pay.rechargeb', ['role' => Auth::user()->role]);
    }

    /**
     * post
     * 支付宝支付
     */
    public function recharge()
    {
        $this->validate(request(), [
            'amount' => 'required|numeric|min:1',
        ]);
        $amount = request('amount');
        $one    = Order::rechargeGolds($amount);

        $gateway = get_alipay();
        $request = $gateway->purchase();
        $request->setBizContent([
            'out_trade_no' => $one->orderid,
            'total_amount' => $amount,
            'subject'      => '充值金币',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ]);

        $response    = $request->send();
        $redirectUrl = $response->getRedirectUrl();
        return redirect($redirectUrl);
    }

    public function rechargeBalance()
    {
        $this->validate(request(), [
            'amount' => 'required|numeric|min:1',
        ]);
        $amount = request('amount');
        $one    = Order::rechargeBalance($amount);

        $gateway = get_alipay();
        $request = $gateway->purchase();
        $request->setBizContent([
            'out_trade_no' => $one->orderid,
            'total_amount' => $amount,
            'subject'      => '充值余额',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ]);

        $response    = $request->send();
        $redirectUrl = $response->getRedirectUrl();
        return redirect($redirectUrl);
    }


    /**
     * 删除订单
     */
    public function delOrder()
    {
        $id  = request('id', 0);
        $one = Order::find($id);
        if (!$one) {
            return error(MODEL_NOT_FOUNT);
        }
        if ($one->uid != Auth::user()->id) {
            return error(NO_ACCESS);
        }
        if ($one->status != Order::STATUS_UNPAID) {
            return error(NO_ACCESS);
        }
        Order::delOrder($one);
        return success();
    }

    /**
     * 支付订单，跳转
     */
    public function jumpAlipay()
    {
        $id  = request('id', 0);
        $one = Order::find($id);
        if (!$one) {
            return error(MODEL_NOT_FOUNT);
        }
        if ($one->uid != Auth::user()->id) {
            return error(NO_ACCESS);
        }
        if ($one->status != 1) {
            return error('已支付');
        }
        switch ($one->type) {
            case Order::TYPE_RECHARGE:
                $subject = '充值金币';
                $amount  = round($one->golds / $one->rate, 2);
                break;
            case Order::TYPE_RECHARGE_BALANCE:
                $subject = '充值余额';
                $amount  = round($one->golds / $one->rate, 2);
                break;
            case Order::TYPE_CONSUME:
                $subject = '代购支付';
                $amount  = round($one->price - $one->balance, 2);
                break;
        }
        $gateway = get_alipay();
        $request = $gateway->purchase();
        $request->setBizContent([
            'out_trade_no' => $one->orderid,
            'total_amount' => (string)$amount,
            'subject'      => $subject,
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ]);
        $response    = $request->send();
        $redirectUrl = $response->getRedirectUrl();
        return redirect($redirectUrl);
    }

    /**
     * get/post
     * 充值回调
     */
    public function result()
    {
        $gateway = get_alipay();
        $request = $gateway->completePurchase();
        $request->setParams(array_merge($_POST, $_GET));
        $flag    = false;
        $typeurl = 'billlist';
        try {
            $response = $request->send();
            if ($response->isPaid()) {
                $data           = $response->getData();
                $orderid        = $data['out_trade_no'];
                $alipay_orderid = $data['trade_no'];
                $model          = Order::where('orderid', $orderid)->first();
                if ($model->status == Order::STATUS_UNPAID) {
                    switch ($model->type) {
                        case Order::TYPE_RECHARGE:
                            $typeurl = 'rechargelist';
                            Order::payRechargeGolds($model, $alipay_orderid);
                            break;
                        case Order::TYPE_RECHARGE_BALANCE:
                            $typeurl = 'rechargeblist';
                            Order::payRechargeBalance($model, $alipay_orderid);
                            break;
                        case Order::TYPE_CONSUME:
                            $typeurl = 'orderlist';
                            Order::payOrder($model, $alipay_orderid);
                            break;
                    }
                    $flag = true;
                } elseif ($model->status == Order::STATUS_PAID) {
                    $flag = true;
                } elseif ($model->status == Order::STATUS_DEL) {
                    Log::error('付款已删除订单，平台order-id' . $model->id . '，支付宝订单号' . $alipay_orderid . '，数据库支付宝单号' . $model->alipay_orderid);
                    $flag = true;
                    if ($model->alipay_orderid == '') {
                        Order::errorBack($model, $alipay_orderid);
                    }
                }
            } else {
                $flag = false;
            }
        } catch (Exception $e) {
            Log::error('支付回调失败：');
            Log::error($e);
            $flag = false;
        } finally {
            if ($flag) {
                $json = 'success';
            } else {
                $json = 'fail';
            }
            if (request()->isMethod('get')) {
                return redirect($typeurl);
            } else {
                die($json);
            }
        }
    }

    /**
     * list
     * 充值
     */
    public function listRecharge()
    {
        $list = Order::where('uid', Auth::user()->id)->where('type', Order::TYPE_RECHARGE)->orderBy('id', 'desc')->paginate(10);
        return view('pay.list_recharge')->with('tname', '充值金币记录列表')->with('list', $list);
    }

    public function listRechargeBalance()
    {
        $list = Order::where('uid', Auth::user()->id)->where('type', Order::TYPE_RECHARGE_BALANCE)->orderBy('id', 'desc')->paginate(10);
        return view('pay.list_rechargeb')->with('tname', '充值余额记录列表')->with('list', $list);
    }

    /**
     * 支付刷单任务
     * @return string
     */
    public function postPay()
    {
        $ids   = request('id');
        $start = request('startd');
        $end   = request('endd');
        $ptype = request('ptype');

        $user = Auth::user();
        $list = ClickFarm::where('uid', $user->id)->where('status', 1)->whereIn('id', $ids)->get();
        if (count($list) == 0) {
            return error(MODEL_NOT_FOUNT);
        }
        //计算总金币 总价格
        $golds = 0;
        $price = 0.00;
        foreach ($list as $one) {
            $golds += $one->golds;
            $price += $one->amount;
        }
        //金币不够
        if ($ptype == 'cycle') {
            if (strtotime($start) > strtotime($end)) {
                return error('开始时间不能大于结束时间');
            }
            $days  = diffBetweenTwoDays($start, $end);
            $golds *= $days;
            $price *= $days;
        }
        if (($user->golds - $user->lock_golds) < $golds) {
            return error(NO_ENOUGH_GOLDS);
        }
        if ($ptype == 'cycle') {
            //TODO 验证日期
            $days = diffBetweenTwoDays($start, $end);
            for ($i = 0; $i < $days; $i++) {
                $day = date('Y-m-d H:i:s', strtotime($start) + 86400 * $i + rand(0, 86400));
                if ($i == 0) {
                    if ($start == date('Y-m-d')) {
                        $day = date('Y-m-d H:i:s');
                    }
                    foreach ($list as $v) {
                        $v->start_time = $day;
                        $v->save();
                    }
                    continue;
                }
                foreach ($list as $v) {
                    $one             = $v->replicate();
                    $one->start_time = $day;
                    $one->save();
                    $ids[] = $one->id;
                }
            }
            $list = ClickFarm::where('uid', $user->id)->where('status', 1)->whereIn('id', $ids)->get();
        } else {
            foreach ($list as $v) {
                $v->start_time = date('Y-m-d H:i:s');
                $v->save();
            }
        }

        $balance = $user->balance - $user->lock_balance;

        if (round($price, 2) > round($balance, 2)) {
            //余额+充值 跳转 不生成bill
            $one = Order::consumeByPartRecharge($price, $golds, $balance, $list);
            return success(['type' => 'p', 'id' => $one->id]);
        }
        //余额 生成bill
        $one = Order::consumeByBalance($price, $golds, $list);
        return success(['type' => 'b', 'id' => $one->id]);
    }

    /**
     * 流水账单
     */
    public function listBill()
    {
        $start = request('start', date("Y-m-d", strtotime("-30 days")));
        $dend  = $end = request('end', date("Y-m-d"));
        if ($end == date("Y-m-d")) {
            $dend = date('Y-m-d H:i:s');
        }
        $type = request('type', -1);

        $table = Bill::where('uid', Auth::user()->id);
        if ($start != null && $end != null) {
            $table->whereBetween('created_at', [$start, $dend]);
        }

        if ($type >= 0) {
            $table->where('type', $type);
        } else {
            $table->where('type', '<', 15);
        }
        $list = $table->orderBy('id', 'desc')->paginate(10);
        return view('pay.list_bill')->with('tname', '账单列表')->with('list', $list)->with([
            'start' => $start,
            'end'   => $end,
            'type'  => $type
        ]);
    }

    /**
     * 流水账单详情
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws Exception
     */
    public function billDesc()
    {
        $type   = request('type');
        $taskid = request('taskid');

        switch ($type) {
            case 1:
                return redirect('viewrecharge/' . $taskid);
            case 2:
                return redirect('viewclickfarm/' . $taskid);
            default:
                throw new MsgException();
        }
    }

    /**
     * 已支付退单
     * @return string
     */
    public function cancelOrder()
    {
        $id    = request('id', 0);
        $model = Order::find($id);
        if (!$model) {
            return error(MODEL_NOT_FOUNT);
        }
        if ($model->uid != Auth::user()->id) {
            return error(NO_ACCESS);
        }
        if ($model->status != Order::STATUS_FROZEN) {
            return error('订单已经开始执行，无法退单');
        }
        Order::afterChargeback($model);
        return success();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/4/28
 * Time: 上午9:58
 */

namespace App;

use App\Events\CfResults;
use App\Exceptions\MsgException;
use Auth;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Log;

class Order extends Model
{
    const STATUS_DEL          = 0;//已删除
    const STATUS_UNPAID       = 1;//待付款
    const STATUS_PAID         = 2;//已付款
    const STATUS_UNDERWAY     = 3;//进行中
    const STATUS_FULL_SUCCESS = 4;//全部完成
    const STATUS_FULL_FAILURE = 5;//全部失败
    const STATUS_PART_FAILURE = 6;//部分失败
    const STATUS_FROZEN       = 8;//下单后冻结时间

    const TYPE_RECHARGE     = 1;//充值
    const TYPE_CONSUME      = 2;//消费
    const TYPE_REFUND       = 3;//退款
    const TYPE_EVALUATE     = 4;//评价
    const TYPE_UPLOAD_IMG   = 5;//上传图片
    const TYPE_UPLOAD_VIDEO = 6;//上传视频
    const TYPE_DEL_PAID     = 7;//删除后付款 订单异常补偿
    const TYPE_PROMOTION    = 8;//点赞需求

    const PTYPE_ALIPAY = 1;
    const PTYPE_GOLD   = 0;

    protected $fillable = [
        'uid', 'type', 'payment_type', 'orderid', 'alipay_orderid', 'balance', 'price', 'golds', 'rate', 'status'
    ];
    protected $appends = ['type_text', 'status_text', 'payment_type_text', 'pay'];

    /**
     * 充值金币
     * @param $amount
     * @param $user
     * @return mixed
     * @throws MsgException
     */
    public static function rechargeGolds($amount)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $golds = $amount * gconfig('rmbtogold');
            $one   = self::create([
                'uid'          => $user->id,
                'type'         => self::TYPE_RECHARGE,
                'payment_type' => self::PTYPE_ALIPAY,
                'orderid'      => get_order_id(),
                'golds'        => $golds,
                'rate'         => gconfig('rmbtogold'),
                'status'       => self::STATUS_UNPAID
            ]);
            DB::commit();
            return $one;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new MsgException();
        }
    }

    /**
     * 完成充值金币
     * @param Order $one
     * @param $alipay_orderid
     * @throws MsgException
     */
    public static function payRechargeGolds(self $one, $alipay_orderid)
    {
        $user = User::find($one->uid);
        DB::beginTransaction();
        try {
            $one->status         = self::STATUS_PAID;
            $one->alipay_orderid = $alipay_orderid;
            $one->save();
            $user->golds            += $one->golds;
            $user->cumulative_golds += $one->golds;
            //升级
            $level     = $user->level;
            $golds     = $one->golds / $one->rate;
            $levellist = Role::get(['name', 'once', 'more', 'type'])->keyBy('name');
            foreach ($levellist as $k => $v) {
                $flag = false;
                if ($level < $k) {
                    switch ($v->type) {
                        case 1:
                            if ($golds >= $v->once) {
                                $level = $k;
                            } else {
                                $flag = true;
                            }
                            break;
                        case 2:
                            if ($golds >= $v->more) {
                                $level = $k;
                            } else {
                                $flag = true;
                            }
                            break;
                    }
                    if ($flag) {
                        break;
                    }
                } else {
                    continue;
                }
            }
            $user->level = $level;
            $user->save();
            Bill::create([
                'uid'            => $user->id,
                'oid'            => $one->id,
                'type'           => Bill::TYPE_RECHARGE,
                'orderid'        => $one->orderid,
                'alipay_orderid' => $one->alipay_orderid,
                'gin'            => $one->golds,
                'rate'           => gconfig('rmbtogold'),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            Log::error('回调充值支付失败：');
            Log::error($e);
            DB::rollBack();
            throw new Exception();
        }
    }

    /**
     * @deprecated
     * old
     * 完成充值金币
     * @param Order $one
     * @param $alipay_orderid
     * @throws MsgException
     */
    public static function oldPayRechargeGolds(self $one, $alipay_orderid)
    {
        $user = User::find($one->uid);
        DB::beginTransaction();
        try {
            $one->status         = self::STATUS_PAID;
            $one->alipay_orderid = $alipay_orderid;
            $one->save();
            $user->golds = $user->golds + $one->golds;
            $amount      = $one->golds / $one->rate;
            //有效期
            if ($amount >= gconfig('cost.vip')) {
//                //有效期
//                $adddays = floor($amount / gconfig('cost.vip')) * gconfig('vip.days');
//                if ($user->validity == null || strtotime($user->validity) < time()) {
//                    $validity = date('Y-m-d', strtotime('+ ' . ($adddays + 1) . ' days')) . ' 00:00:00';
//                } else {
//                    $validity = date('Y-m-d H:i:s', strtotime('+ ' . $adddays . ' days', strtotime($user->validity)));
//                }
//                VipBill::create([
//                    'uid'      => $user->id,
//                    'oid'      => $one->id,
//                    'days'     => $adddays,
//                    'validity' => $validity,
//                ]);
                $user->level = 2;
//                $user->validity = $validity;
            }
            $user->save();
            Bill::create([
                'uid'            => $user->id,
                'oid'            => $one->id,
                'type'           => Bill::TYPE_RECHARGE,
                'orderid'        => $one->orderid,
                'alipay_orderid' => $one->alipay_orderid,
                'gin'            => $one->golds,
                'rate'           => gconfig('rmbtogold'),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new MsgException();
        }
    }

    /**
     * 消费电子需求
     * @param $list
     * @param $golds
     * @param null $user
     * @throws MsgException
     */
    public static function consumePromotion($list, $golds, $user = null)
    {
        $user = $user == null ? Auth::user() : $user;
        DB::beginTransaction();
        try {
            $one         = self::create([
                'uid'          => $user->id,
                'type'         => self::TYPE_PROMOTION,
                'payment_type' => self::PTYPE_GOLD,
                'orderid'      => get_order_id(),
                'golds'        => $golds,
                'rate'         => gconfig('rmbtogold'),
                'status'       => self::STATUS_PAID
            ]);
            $user->golds -= $golds;
            $user->save();
            foreach ($list as $k => $v) {
                $list[$k]['oid'] = $one->id;
            }
            Promotion::insert($list);
            Bill::create([
                'uid'     => $user->id,
                'oid'     => $one->id,
                'type'    => Bill::TYPE_PROMOTION,
                'orderid' => $one->orderid,
                'gout'    => $golds,
                'rate'    => gconfig('rmbtogold'),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('点赞');
            Log::error($e);
            throw new MsgException('');
        }
    }

    /**
     * 充值+余额 支付
     * @param $price
     * @param $golds
     * @param $balance
     * @param $list
     * @return mixed
     * @throws MsgException
     */
    public static function consumeByPartRecharge($price, $golds, $balance, $list)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $user->lock_golds   = $user->lock_golds + $golds;
            $user->lock_balance = $user->lock_balance + $balance;
            $user->save();
            $one = self::create([
                'uid'          => $user->id,
                'type'         => self::TYPE_CONSUME,
                'payment_type' => self::PTYPE_ALIPAY,
                'orderid'      => get_order_id(),
                'balance'      => $balance,
                'price'        => $price,
                'golds'        => $golds,
                'rate'         => gconfig('rmbtogold'),
                'status'       => self::STATUS_UNPAID
            ]);
            foreach ($list as $model) {
                $model->oid    = $one->id;
                $model->status = 2;
                $model->save();
            }
            DB::commit();
            return $one;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new MsgException();
        }
    }

    /**
     * 完成消费付款
     * @param Order $one
     * @param $alipay_orderid
     * @throws MsgException
     */
    public static function payOrder(self $one, $alipay_orderid)
    {
        $user = User::find($one->uid);
//        $list = ClickFarm::where('oid', $one->id)->get();
        DB::beginTransaction();
        try {
            $one->status         = self::STATUS_FROZEN;
            $one->alipay_orderid = $alipay_orderid;
            $one->save();
            $user->lock_golds   = $user->lock_golds - $one->golds;
            $user->golds        = $user->golds - $one->golds;
            $user->lock_balance = $user->lock_balance - $one->balance;
            $user->balance      = $user->balance - $one->balance;
            $user->save();
            Bill::create([
                'uid'            => $user->id,
                'oid'            => $one->id,
                'type'           => Bill::TYPE_CONSUME,
                'orderid'        => $one->orderid,
                'alipay_orderid' => $one->alipay_orderid,
                'out'            => $one->price - $one->balance,
                'gout'           => $one->golds,
                'rate'           => gconfig('rmbtogold'),
            ]);
//            foreach ($list as $model) {
//                event(new CfResults($model));
//            }
            DB::commit();
        } catch (\Throwable $e) {
            Log::error('回调消费支付失败：');
            Log::error($e);
            DB::rollBack();
            throw new Exception();
        }
    }

    /**
     * 订单异常补偿
     * @param Order $one
     * @throws MsgException
     */
    public static function errorBack(self $one, $alipay_orderid)
    {
        DB::beginTransaction();
        try {
            $user                = $one->user;
            $user->balance       += $one->price - $one->balance;
            $one->alipay_orderid = $alipay_orderid;
            $order               = Order::create([
                'uid'     => $user->id,
                'type'    => Order::TYPE_DEL_PAID,
                'orderid' => get_order_id(),
                'price'   => $one->price - $one->balance,
                'rate'    => gconfig('rmbtogold'),
                'status'  => Order::STATUS_PAID
            ]);
            Bill::create([
                'uid'     => $user->id,
                'oid'     => $order->id,
                'type'    => Bill::TYPE_DEL_PAID,
                'orderid' => $order->orderid,
                'in'      => $one->price - $one->balance,
                'rate'    => gconfig('rmbtogold'),
            ]);
            $user->save();
            $one->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('订单异常补偿：');
            Log::error($e);
            throw new MsgException();
        }
    }

    /**
     * 下单后取消订单
     * @throws MsgException
     */
    public static function afterChargeback(self $one)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $user->balance += $one->price;
            $user->golds   += $one->golds;
            $one->status   = Order::STATUS_DEL;
            $order         = Order::create([
                'uid'     => $user->id,
                'type'    => Order::TYPE_REFUND,
                'orderid' => get_order_id(),
                'price'   => $one->price,
                'golds'   => $one->golds,
                'rate'    => gconfig('rmbtogold'),
                'status'  => Order::STATUS_PAID
            ]);
            Bill::create([
                'uid'     => $user->id,
                'oid'     => $order->id,
                'type'    => Bill::TYPE_REFUND,
                'orderid' => $order->orderid,
                'in'      => $one->price,
                'gin'     => $one->golds,
                'rate'    => gconfig('rmbtogold'),
            ]);
            $user->save();
            $one->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('下单后取消订单：');
            Log::error($e);
            throw new MsgException();
        }
    }

    /**
     * 余额 支付
     * @param $price
     * @param $golds
     * @param $list
     * @throws MsgException
     */
    public static function consumeByBalance($price, $golds, $list)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $user->golds   = $user->golds - $golds;
            $user->balance = $user->balance - $price;
            $user->save();
            $orderid = get_order_id();
            $one     = self::create([
                'uid'          => $user->id,
                'type'         => self::TYPE_CONSUME,
                'payment_type' => self::PTYPE_ALIPAY,
                'orderid'      => $orderid,
                'balance'      => $price,
                'price'        => $price,
                'golds'        => $golds,
                'rate'         => gconfig('rmbtogold'),
                'status'       => self::STATUS_FROZEN
            ]);
            Bill::create([
                'uid'     => $user->id,
                'oid'     => $one->id,
                'type'    => Bill::TYPE_CONSUME,
                'orderid' => $one->orderid,
                'gout'    => $golds,
                'rate'    => gconfig('rmbtogold'),
            ]);
            foreach ($list as $model) {
                $model->oid    = $one->id;
                $model->status = 2;
                $model->save();
                //event(new CfResults($model));
            }
            DB::commit();
            return $one;
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            throw new MsgException();
        }
    }

    public static function evaluateByGold($payarr)
    {
        if ($payarr[0] == 0) {
            return true;
        }
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $user->golds = $user->golds - $payarr[0];
            $user->save();
            foreach ($payarr[1] as $k => $v) {
                $orderid = get_order_id();
                $type    = self::transType($k);
                $one     = self::create([
                    'uid'          => $user->id,
                    'type'         => $type,
                    'payment_type' => self::PTYPE_GOLD,
                    'orderid'      => $orderid,
                    'golds'        => $v['gold'],
                    'rate'         => gconfig('rmbtogold'),
                    'status'       => self::STATUS_PAID
                ]);
                Bill::create([
                    'uid'     => $user->id,
                    'oid'     => $one->id,
                    'type'    => $type,
                    'orderid' => $one->orderid,
                    'gout'    => $v['gold'],
                    'rate'    => gconfig('rmbtogold'),
                ]);
            }
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            //report($e);
            Log::error($e);
            return false;
        }
    }

    public static function transType($str)
    {
        switch ($str) {
            case 'evaluate':
                $type = self::TYPE_EVALUATE;
                break;
            case 'euploadpic':
                $type = self::TYPE_UPLOAD_IMG;
                break;
            case 'euploadvideo':
                $type = self::TYPE_UPLOAD_VIDEO;
                break;
            default:
                $type = 0;
        }
        return $type;
    }

    /**
     * 未支付超时退单
     */
    public static function makeRefund()
    {
        $list = self::where('status', self::STATUS_UNPAID)->get();
//        $time = gconfig('order.beforepay.frozentime');
        foreach ($list as $v) {
            if ((strtotime($v->updated_at) + 60 * 10080) < time()) {
//                self::delOrder($v);
            }
        }
    }

    /**
     * 删除订单
     * @param Order $order
     * @throws MsgException
     */
    public static function delOrder(self $order)
    {
        DB::beginTransaction();
        try {
            $user               = $order->user;
            $user->lock_golds   = $user->lock_golds - $order->golds;
            $user->lock_balance = $user->lock_balance - $order->balance;
            $user->save();
            $order->status = self::STATUS_DEL;
            $order->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new MsgException();
        }
    }

    /**
     * 已经支付的执行子任务
     */
    public static function makeCfr()
    {
        $list = self::where('status', self::STATUS_FROZEN)->where('type', self::TYPE_CONSUME)->get();
//        $time = gconfig('order.afterpay.frozentime');
        foreach ($list as $v) {
            if ((strtotime($v->updated_at) + 60 * 5) < time()) {
                self::dealOrder($v);
            }
        }
    }

    /**
     * 支付后执行子任务
     * @param Order $order
     */
    public static function dealOrder(self $order)
    {
        DB::beginTransaction();
        try {
            $order->status = Order::STATUS_PAID;
            $list          = ClickFarm::where('oid', $order->id)->get();
            foreach ($list as $model) {
                event(new CfResults($model));
            }
            $order->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('支付后执行子任务');
            Log::error($e);
        }
    }

    public function cfs()
    {
        return $this->hasMany(ClickFarm::class, 'oid');
    }

    public function cfrs()
    {
        return $this->hasOne(CfResults::class, 'oid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function getTypeTextAttribute()
    {
        $arr = config('linepro.order_type');
        if (!isset($arr[$this->type])) {
            return '未定义';
        }
        return $arr[$this->type];
    }

    public function getStatusTextAttribute()
    {
        $arr = config('linepro.order_status');
        if (!isset($arr[$this->status])) {
            return '未定义';
        }
        return $arr[$this->status];
    }

    public function getPaymentTypeTextAttribute()
    {
        $arr = config('linepro.order_ptype');
        if (!isset($arr[$this->payment_type])) {
            return '未定义';
        }
        return $arr[$this->payment_type];
    }

    public function getPayAttribute()
    {
        return round($this->price - $this->balance, 2);
    }

    public function scopeType($query, $type)
    {
        if (!in_array($type, [1, 2, 3, 4, 5, 6, 7])) {
            return $query;
        }

        return $query->where('type', $type);
    }
}
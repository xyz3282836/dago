<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/4/28
 * Time: 上午9:58
 */

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    const TYPE_SYS          = 0;//系统送
    const TYPE_RECHARGE     = 1;//充值送
    const TYPE_CONSUME      = 2;//正常订单
    const TYPE_REFUND       = 3;//退款
    const TYPE_EVALUATE     = 4;//评价
    const TYPE_UPLOAD_IMG   = 5;//上传图片
    const TYPE_UPLOAD_VIDEO = 6;//上传视频
    const TYPE_DEL_PAID     = 7;//订单异常补偿

    protected $appends = ['type_text'];
    protected $fillable = [
        'uid', 'type', 'orderid', 'alipay_orderid', 'in', 'out', 'gin', 'gout', 'rate', 'oid'
    ];

    /**
     * 系统送
     * @param $gold
     * @param null $uid
     */
    public static function getGoldBySys($golds, $user = null)
    {
        $user        = $user != null ? $user : Auth::user();
        $user->golds = $golds;
        $user->save();
        self::create([
            'uid'     => $user->id,
            'type'    => self::TYPE_SYS,
            'orderid' => get_order_id(),
            'gin'     => $golds,
            'rate'    => gconfig('rmbtogold'),
        ]);
    }

    public function getTypeTextAttribute()
    {
        $arr = config('linepro.bill_type');
        return $arr[$this->type];
    }

}
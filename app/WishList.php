<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    const STATUS_REFUND  = -1;//已退款
    const STATUS_FAIL    = 0;//失败
    const STATUS_WAITING = 1;//处理中
    const STATUS_SYNC    = 2;//已同步
    const STATUS_SUCCESS = 3;//成功

    protected $appends = ['status_text', 'flag'];

    public function getStatusTextAttribute()
    {
        $arr = config('linepro.wishlist_statuss');
        if (!isset($arr[$this->status])) {
            return '未定义';
        }
        return $arr[$this->status];
    }

    public function getFlagAttribute()
    {
        return ExchangeRate::getFlag($this->from_site);
    }
}

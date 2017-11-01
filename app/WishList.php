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

    protected $appends = ['status_text', 'flag', 'rank_text'];

    public function getStatusTextAttribute()
    {
        $arr = config('linepro.wishlist_statuss');
        if (!isset($arr[$this->status])) {
            return '未定义';
        }
        return $arr[$this->status];
    }

    public function getRankTextAttribute()
    {
        if ($this->rank == 0) {
            return '暂无排名';
        }
        $page = $this->rank / 20;
        $id   = $this->rank % 20;
        $page++;
        if ($id == 0) {
            $page--;
            $id = 20;
        }
        return '第' . $page . '页 ' . $id . '名';
    }

    public function getFlagAttribute()
    {
        return ExchangeRate::getFlag($this->from_site);
    }
}

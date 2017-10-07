<?php

/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/5/12
 * Time: 下午2:05
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    const STATUS_REFUND  = -1;//已退款
    const STATUS_FAIL    = 0;//失败
    const STATUS_WAITING = 1;//处理中
    const STATUS_SYNC    = 2;//已同步
    const STATUS_SUCCESS = 3;//成功

}
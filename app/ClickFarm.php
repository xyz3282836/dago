<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/4/22
 * Time: 下午3:01
 */

namespace App;


use Auth;
use Illuminate\Database\Eloquent\Model;

class ClickFarm extends Model
{
    protected $appends = ['status_text', 'delivery_type_text', 'from_site_text', 'time_type_text', 'final_price_text', 'flag', 'search_type_text', 'fba_text'];

    public function getSearchTypeTextAttribute()
    {
        $arr = config('linepro.cf_search_type');
        if (Auth::user()->level == 1) {
            return $arr[0];
        }
        if (!isset($arr[$this->search_type])) {
            return '未定义';
        }
        return $arr[$this->search_type];
    }

    public function getFbaTextAttribute()
    {
        $arr = config('linepro.cf_fba');
        if (!isset($arr[$this->is_fba])) {
            return '未定义';
        }
        return $arr[$this->is_fba];
    }

    public function getStatusTextAttribute()
    {
        $arr = config('linepro.cf_status');
        if (!isset($arr[$this->status])) {
            return '未定义';
        }
        return $arr[$this->status];
    }

    public function getDeliveryTypeTextAttribute()
    {
        $arr = config('linepro.delivery_type');
        if (!isset($arr[$this->delivery_type])) {
            return '未定义';
        }
        return $arr[$this->delivery_type];
    }

    public function getFromSiteTextAttribute()
    {
        $arr = config('linepro.from_site');
        if (!isset($arr[$this->from_site])) {
            return '未定义';
        }
        return $arr[$this->from_site];
    }

    public function getFlagAttribute()
    {
        return ExchangeRate::getFlag($this->from_site);
    }

    public function getTimeTypeTextAttribute()
    {
        $arr = config('linepro.time_type');
        if (!isset($arr[$this->time_type])) {
            return '未定义';
        }
        return $arr[$this->time_type];
    }

    public function getFinalPriceTextAttribute()
    {
        return $this->final_price . get_currency($this->from_site);
    }

}
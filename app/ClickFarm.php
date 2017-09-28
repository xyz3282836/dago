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
        return $arr[$this->search_type];
    }

    public function getFbaTextAttribute()
    {
        $arr = config('linepro.cf_fba');
        return $arr[$this->is_fba];
    }

    public function getStatusTextAttribute()
    {
        $arr = config('linepro.cf_status');
        return $arr[$this->status];
    }

    public function getDeliveryTypeTextAttribute()
    {
        $arr = config('linepro.delivery_type');
        return $arr[$this->delivery_type];
    }

    public function getFromSiteTextAttribute()
    {
        $arr = config('linepro.from_site');
        return $arr[$this->from_site];
    }

    public function getFlagAttribute()
    {
        return ExchangeRate::getFlag($this->from_site);
    }

    public function getTimeTypeTextAttribute()
    {
        $arr = config('linepro.time_type');
        return $arr[$this->time_type];
    }

    public function getFinalPriceTextAttribute()
    {
        return $this->final_price . get_currency($this->from_site);
    }

}
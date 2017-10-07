<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/5/11
 * Time: 上午11:14
 */

namespace App\Http\Controllers;


use App\Promotion;
use Auth;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $asin   = request('asin', '');
        $status = request('status', 'all');
        $eid    = request('eid', '');
        $site   = request('site', 0);
        $asin   = $asin == null ? '' : $asin;
        $eid    = $eid == null ? '' : $eid;

        $list = Promotion::where('uid', Auth::user()->id);
        if ($asin != '') {
            $list = $list->where('asin', $asin);
        }
        if ($eid != '') {
            $list = $list->where('eid', $eid);
        }
        if ($site > 0) {
            $list = $list->where('from_site', $site);
        }
        if ($status != 'all') {
            $list = $list->where('status', $status);
        }
        return view('promotion.list')->with([
            'list'   => $list,
            'eid'    => $eid,
            'asin'   => $asin,
            'site'   => $site,
            'status' => $status,
        ]);
    }

    public function add(){
        return view('promotion.add');
    }
}
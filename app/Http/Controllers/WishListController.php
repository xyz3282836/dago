<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/5/11
 * Time: 上午11:14
 */

namespace App\Http\Controllers;


use App\Action;
use App\Order;
use App\Promotion;
use App\WishList;
use Auth;
use Carbon\Carbon;

class WishListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $asin     = request('asin', '');
        $status   = request('status', 'all');
        $keywords = request('keywords', '');
        $site     = request('site', 0);
        $asin     = $asin == null ? '' : $asin;
        $keywords = $keywords == null ? '' : $keywords;

        $list = WishList::where('uid', Auth::user()->id);
        if ($asin != '') {
            $list = $list->where('asin', $asin);
        }
        if ($keywords != '') {
            $list = $list->where('eid', $keywords);
        }
        if ($site > 0) {
            $list = $list->where('from_site', $site);
        }
        if ($status != 'all') {
            if ($status == 1) {
                $list = $list->whereIn('status', [1, 2]);
            } else {
                $list = $list->where('status', $status);
            }
        }
        $list = $list->orderBy('id', 'desc')->paginate(config('linepro.perpage'));
        return view('wishlist.list')->with([
            'list'     => $list,
            'keywords' => $keywords,
            'asin'     => $asin,
            'site'     => $site,
            'status'   => $status,
        ]);
    }

    public function add()
    {
        $btndisable = '';
        if (!Auth::user()->checkAction('wishlist')) {
            $btndisable = 'disabled';
        }
        return view('wishlist.add')->with('btn', $btndisable);
    }

    public function postAdd()
    {
        $list  = request('data');
        $golds = 0;
        $dbarr = [];
        $user  = Auth::user();
        if (!$user->checkAction('wishlist')) {
            return error(Action::where('name', 'wishlist')->value('auth_desc'));
        }
        $daygold = $user->getActionGold('eup');
        foreach ($list as $v) {
            $tmparr  = [
                'uid'        => $user->id,
                'start'      => $v['date'][0],
                'end'        => $v['date'][1],
                'keywords'   => $v['keywords'],
                'num'        => $v['num'],
                'from_site'  => $v['from_site'],
                'asin'       => $v['asin'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'golds'      => $daygold * $v['num']
            ];
            $dbarr[] = $tmparr;
        }
        if (($user->golds - $user->lock_golds) < $golds) {
            return error(NO_ENOUGH_GOLDS);
        }

        return success();

    }
}
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
            $list = $list->where('keywords', $keywords);
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
        $list    = $list->orderBy('id', 'desc')->paginate(config('linepro.perpage'));
        $daygold = Auth::user()->getActionGold('wishlist');
        return view('wishlist.list')->with([
            'daygold'  => $daygold,
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
        $daygold = $user->getActionGold('wishlist');
        foreach ($list as $v) {
            $start = date('Y-m-d', strtotime($v['date'][0]));
            $end   = date('Y-m-d', strtotime($v['date'][1]));
            if (strtotime($v['date'][0]) < strtotime(date('Y-m-d'))) {
                return error('开始时间不能早于今天' . date('Y-m-d'));
            }
            if (strtotime($v['date'][0]) > strtotime($v['date'][1])) {
                return error('结束时间早于开始时间');
            }
            $tmparr  = [
                'uid'        => $user->id,
                'start'      => $start,
                'end'        => $end,
                'keywords'   => $v['keywords'],
                'num'        => $v['num'],
                'from_site'  => $v['from_site'],
                'asin'       => $v['asin'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'golds'      => $daygold * $v['num'] * diffBetweenTwoDays($start, $end)
            ];
            $golds   += $tmparr['golds'];
            $dbarr[] = $tmparr;
        }
        if (($user->golds - $user->lock_golds) < $golds) {
            return error(NO_ENOUGH_GOLDS);
        }
        Order::consumeWishlist($dbarr, $golds, $user);
        return success();

    }
}
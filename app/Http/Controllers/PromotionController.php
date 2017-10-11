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
use Auth;
use Carbon\Carbon;

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
        $list = $list->orderBy('id', 'desc')->paginate(config('linepro.perpage'));
        return view('promotion.list')->with([
            'list'   => $list,
            'eid'    => $eid,
            'asin'   => $asin,
            'site'   => $site,
            'status' => $status,
        ]);
    }

    public function add()
    {
        return view('promotion.add');
    }

    public function postAdd()
    {
        $list  = request('data');
        $golds = 0;
        $dbarr = [];
        $user  = Auth::user();
        if (!$user->checkAction('eup')) {
            return error(Action::where('name', 'eup')->value('auth_desc'));
        }
        if (!$user->checkAction('edown')) {
            return error(Action::where('name', 'edown')->value('auth_desc'));
        }
        $upgold   = $user->getActionGold('eup');
        $downgold = $user->getActionGold('edown');
        foreach ($list as $v) {
            $p   = '/https:\/\/www.amazon.(com|co.uk|ca|de|fr|co.jp|es|it)\/(review|gp\/customer-reviews)\/([0-9A-Z]+)/';
            $url = trim($v['url']);
            $arr = [];
            preg_match($p, $url, $arr);
            $tmparr = [
                'uid'        => $user->id,
                'url'        => $url,
                'eid'        => $arr[3],
                'num'        => $v['num'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            if ($v['type'] != 'true') {
                $tmparr['type']  = 2;
                $tmparr['golds'] = $downgold * $v['num'];
            } else {
                $tmparr['type']  = 1;
                $tmparr['golds'] = $upgold * $v['num'];
            }
            $golds   += $tmparr['golds'];
            $dbarr[] = $tmparr;
        }
        if (($user->golds - $user->lock_golds) < $golds) {
            return error(NO_ENOUGH_GOLDS);
        }
        Order::consumePromotion($dbarr, $golds, $user);
        return success();

    }
}
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
use App\Qa;
use Auth;
use Carbon\Carbon;

class QaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $asin   = request('asin', '');
        $status = request('status', 'all');
        $site   = request('site', 0);
        $asin   = $asin == null ? '' : $asin;

        $list = Qa::where('uid', Auth::user()->id);
        if ($asin != '') {
            $list = $list->where('asin', $asin);
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
        $gold = Auth::user()->getActionGold('qa');
        return view('qa.list')->with([
            'onegold' => $gold,
            'list'    => $list,
            'asin'    => $asin,
            'site'    => $site,
            'status'  => $status,
        ]);
    }

    public function add()
    {
        $btndisable = '';
        if (!Auth::user()->checkAction('qa')) {
            $btndisable = 'disabled';
        }
        return view('qa.add')->with('btn', $btndisable);
    }

    public function postAdd()
    {
        $list  = request('data');
        $golds = 0;
        $dbarr = [];
        $user  = Auth::user();
        if (!$user->checkAction('qa')) {
            return error(Action::where('name', 'qa')->value('auth_desc'));
        }
        $daygold = $user->getActionGold('qa');
        foreach ($list as $v) {
            $tmparr  = [
                'uid'        => $user->id,
                'q'          => $v['q'],
                'from_site'  => $v['from_site'],
                'asin'       => $v['asin'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'golds'      => $daygold
            ];
            $golds   += $tmparr['golds'];
            $dbarr[] = $tmparr;
        }
        if (($user->golds - $user->lock_golds) < $golds) {
            return error(NO_ENOUGH_GOLDS);
        }
        Order::consumeQa($dbarr, $golds, $user);
        return success();

    }
}
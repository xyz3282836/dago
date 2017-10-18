<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/5/11
 * Time: 上午11:14
 */

namespace App\Http\Controllers;


use App\Action;
use App\Banner;
use App\CfResult;
use App\ClickFarm;
use App\Exceptions\MsgException;
use App\Order;
use Auth;
use Carbon\Carbon;

class CfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 刷单任务
     * add:get
     */
    public function getAddClickFarm()
    {
        $site      = request('site', 1);
        $trans     = gconfig('cost.transport');
        $rmbtogold = gconfig('rmbtogold');
        $user      = Auth::user();
        return view('cf.add_clickfarm')->with([
            'rate'      => get_rate($site),
            'ctext'     => get_currency($site),
            'srate'     => json_encode(get_srate($user)),
            'trans'     => $trans,
            'rmbtogold' => $rmbtogold,
            'ad'        => Banner::getAd(4),
            'user'      => $user,
            's1'        => $user->getActionGold('sdefault'),
            's2'        => $user->getActionGold('scpc'),
            's3'        => $user->getActionGold('swishlist'),
            's4'        => $user->getActionGold('fbm'),
            's5'        => $user->getActionGold('seckill'),
        ]);
    }

    /**
     * 刷单任务
     * add:post
     */
    public function postAddClickFarm()
    {
        $this->validate(request(), [
            'asin'          => 'required|min:1|max:24',
            'amazon_url'    => 'required|active_url',
            'amazon_pic'    => 'required|active_url',
            'amazon_title'  => 'required|min:2|max:1024',
            'shop_id'       => 'required|min:2|max:50',
            'shop_name'     => 'max:50',
            'final_price'   => 'required',
            'task_num'      => 'required|integer',
            'delivery_addr' => 'max:50',
            'keyword'       => 'max:100',
            'from_site'     => 'required|integer',
            'delivery_type' => 'required|integer',
            'is_fba'        => 'required|integer',
            'is_ld'         => 'required|integer',
        ]);

        $pdata = request()->all();

        if ($pdata['is_ld'] == 1) {
            if (!Auth::user()->checkAction('seckill')) {
                return error(Action::where('name', 'seckill')->value('auth_desc'));
            }
        }

        switch ($pdata['is_fba']) {
            case 0:
                if (!Auth::user()->checkAction('fbm')) {
                    return error(Action::where('name', 'fbm')->value('auth_desc'));
                }
                if ($pdata['final_price'] * get_rate($pdata['from_site']) < gconfig('fbm.low.price')) {
                    return error('商品金额过低，存在刷单风险，请选择其他商品');
                }
                break;
            case 1:
                if ($pdata['final_price'] * get_rate($pdata['from_site']) < gconfig('fba.low.price')) {
                    return error('商品金额过低，存在刷单风险，请选择其他商品');
                }
                break;
            default:
                throw new MsgException();
        }

        if ($pdata['delivery_type'] == 1) {
            $pdata['delivery_addr'] = '';
        }
        $pdata['bd']          = request('bd') == null ? '' : request('bd');
        $pdata['keyword']     = request('keyword') == null ? '' : request('keyword');
        $pdata['search_type'] = request('search_type') == null ? 1 : request('search_type');
        $pdata['amount']      = get_amount_clickfarm($pdata);
        $pdata['mixdata']     = json_encode([
            'lower_classification1' => '',
            'lower_classification2' => '',
            'lower_classification3' => '',
            'lower_classification4' => '',
            'outside_website'       => '',
            'place'                 => '',
            'category'              => 1,
            'results'               => null,
            'first_attribute'       => '',
            'second_attribute'      => '',
            'refine'                => null,
            'attribute_group1'      => '',
            'attribute1'            => '',
            'attribute_group2'      => '',
            'attribute2'            => '',
            'attribute_group3'      => '',
            'attribute3'            => '',
            'sort_by'               => 1,
            'page'                  => 1,
            'ba_place'              => 1,
            'ba_asin'               => '',
        ]);

        $model                = new ClickFarm;
        $model->uid           = Auth::user()->id;
        $model->platform_type = 1;
        $model->asin          = $pdata['asin'];
        $model->task_num      = $pdata['task_num'];
        $model->final_price   = $pdata['final_price'];
        $model->shop_id       = $pdata['shop_id'];
        $model->amazon_url    = $pdata['amazon_url'];
        $model->amazon_pic    = $pdata['amazon_pic'];
        $model->amazon_title  = $pdata['amazon_title'];
        $model->delivery_addr = $pdata['delivery_addr'];
        $model->from_site     = $pdata['from_site'];
        $model->delivery_type = $pdata['delivery_type'];
        $model->shop_name     = $pdata['shop_name'];
        $model->bd            = $pdata['bd'];
        $model->keyword       = $pdata['keyword'];
        $model->search_type   = $pdata['search_type'];
        $model->is_fba        = $pdata['is_fba'];
        $model->is_ld         = $pdata['is_ld'];
        //1.0
        $model->time_type        = 1;
        $model->discount_code    = '';
        $model->is_reviews       = 0;
        $model->is_link          = 0;
        $model->is_sellerrank    = 0;
        $model->contrast_asin    = '';
        $model->brower           = 1;
        $model->priority         = 1;
        $model->flow_port        = 1;
        $model->flow_source      = 1;
        $model->browse_step      = 1;
        $model->mixdata          = $pdata['mixdata'];
        $model->interval_time    = 1;
        $model->start_time       = Carbon::now();
        $model->customer_message = '';
        $model->amount           = $pdata['amount'];
        get_cf_price($model);
        $model->save();
        return redirect('card');
    }

    /**
     * 购物车 刷单任务
     * list
     */
    public function listCardClickFarm()
    {
        $list = ClickFarm::where('uid', Auth::user()->id)->where('status', 1)->orderBy('id', 'desc')->get();
        return view('cf.list_card')->with('tname', '购物车商品列表')->with('list', $list)->with('ad', Banner::getAd(3));
    }

    /**
     * 订单列表
     */
    public function listOrder()
    {
        $start = request('start', date("Y-m-d", strtotime("-30 days")));
        $dend  = $end = request('end', date("Y-m-d"));
        if ($end == date("Y-m-d")) {
            $dend = date('Y-m-d H:i:s');
        }
        $type = request('type', 0);
        $list = Order::with('cfs')->where('uid', Auth::user()->id)->where('type', Order::TYPE_CONSUME);
        switch ($type) {
            case 1:
                $list = $list->where('status', 1);
                break;
            case 2:
                $list = $list->where('status', '>', 1);
                break;
            default:
                $list = $list->where('status', '>', 0);
        }

        $list = $list->whereBetween('created_at', [$start, $dend])->orderBy('id', 'desc')->paginate(config('linepro.perpage'));
        return view('cf.list_order')->with('tname', '订单管理')->with('list', $list)->with([
            'start' => $start,
            'end'   => $end,
            'type'  => $type,
            'ad'    => Banner::getAd(3)
        ]);
    }

    /**
     * 购物车删除
     * @return string
     */
    public function postCancle()
    {
        $id    = request('id', 0);
        $model = ClickFarm::find($id);
        if (!$model) {
            return error(MODEL_NOT_FOUNT);
        }
        if ($model->uid != Auth::user()->id) {
            return error(NO_ACCESS);
        }
        if ($model->status != 1) {
            return error('已经删除，请刷新页面');
        }
        $model->status = 0;
        $model->save();
        return success();
    }

    /**
     * 刷单任务结果表
     * list
     */
    public function listCfResult($id)
    {
        $model = ClickFarm::find($id);
        if (!$model) {
            return error(MODEL_NOT_FOUNT);
        }
        $list = CfResult::where('cfid', $id);
        $list = $list->orderBy('id', 'desc')->paginate(config('linepro.perpage'));
        return view('cf.list_all_cfr')->with('tname', '已购买商品代购任务列表')->with([
            'list' => $list,
            'cf'   => $model,
        ]);
    }

    /**
     * 所有刷单任务表
     * @return $this
     */
    public function listMycfr()
    {
        $type = request('type', 1);
        $asin = request('asin', '');
        $site = request('site', 0);
        $asin = $asin == null ? '' : $asin;
        $list = CfResult::with('cf')->where('uid', Auth::user()->id);
        if ($asin != '') {
            $list = $list->where('asin', $asin);
        }
        if ($site > 0) {
            $list = $list->where('from_site', $site);
        }
        switch ($type) {
            case 2:
                $list = $list->where('estatus', 1);
                break;
            case 3:
                $list = $list->whereIn('estatus', [2, 3, 4]);
                break;
            case 4:
                $list = $list->where('estatus', 5);
                break;
            case 1:
            default:

        }
        $list = $list->where('status', '>', 0)->orderBy('id', 'created_at')->paginate(config('linepro.perpage'));
        return view('cf.list_all_cfr')->with('tname', '评价任务列表')->with([
            'list' => $list,
            'type' => $type,
            'asin' => $asin,
            'site' => $site,
        ]);
    }

    /**
     * 刷单评价
     */
    public function evaluate()
    {
        $id      = request('id', 0);
        $star    = request('star');
        $title   = trim(request('title'));
        $epic    = request('epic') == null ? '[]' : json_encode(request('epic'));
        $evideo  = request('evideo') == null ? '[]' : json_encode(request('evideo'));
        $content = str_replace(chr(194) . chr(160), ' ', trim(request('content')));

        if (mb_strlen($title, 'utf-8') >= 200) {
            return error('评价标题最多200个字符');
        }
        if (mb_strlen($content, 'utf-8') >= 10240) {
            return error('评价正文最多10240个字符');
        }
        $model = CfResult::find($id);
        if (!$model) {
            return error(MODEL_NOT_FOUNT);
        }
        $user = Auth::user();
        if ($model->uid != $user->id) {
            return error(NO_ACCESS);
        }
        if ($model->estatus == 1) {
            if (!$user->is_evaluate) {
                return error('系统检测您的留评率过高，存在刷单风险，已暂停本账号评价能力');
            }
        }
        if (in_array($model->estatus, [4, 5, 6])) {
            return error('评价已经提交同步，不可更改');
        }
        $cf = $model->cf;
        if ($cf->is_fba == 0) {
            return error('非亚马逊发货，不可评价');
        }
        $site = $cf->from_site;
        if ($site == 6) {
            if (mb_strlen($content, 'utf-8') < 70) {
                return error('评价文字必须大于60日文字符');
            }
        } elseif (in_array($site, [3, 4, 5, 8, 10])) {
            $p = '/^([\S]+[\s]+){19,}/';
            if (!preg_match($p, $content)) {
                return error('评价文字必须大于20个单词');
            }
        }

        //扣金币
        $payarr = [];
        if ($model->title == '') {
            $payarr['evaluate'] = 1;
        }

        if ($model->estatus == 1 || $model->estatus == 7) {
            $model->estatus = CfResult::ESTATUS_SUBMIT;
        }
        if (count(json_decode($epic, true)) > $model->epicnum) {
            $payarr['euploadpic'] = count(json_decode($epic, true)) - $model->epicnum;
            $model->epicnum       = count(json_decode($epic, true));
        }
        if (count(json_decode($evideo, true)) > $model->evideonum) {
            $payarr['euploadvideo'] = count(json_decode($evideo, true)) - $model->evideonum;
            $model->evideonum       = count(json_decode($evideo, true));
        }

        $paygold = Auth::user()->getConsumeGold($payarr);

        if ($paygold === false) {
            return error('无权限此操作');
        }
        if ((Auth::user()->golds - Auth::user()->lock_golds) < $paygold[0]) {
            return error('金币不够，请充值');
        }

        if (!Order::evaluateByGold($paygold)) {
            return error('支付异常');
        }

        $model->star    = $star;
        $model->title   = $title;
        $model->content = $content;
        $model->epic    = $epic;
        $model->evideo  = $evideo;
        $model->save();
        $model->evaluate($user);
        return success();
    }
}
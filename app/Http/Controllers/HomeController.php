<?php

namespace App\Http\Controllers;

use App\Action;
use App\CfResult;
use App\VipBill;
use Auth;
use Cache;
use DB;
use Hash;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        event(new Vip(Auth::user()));
        $list = CfResult::where('status', 0)->get();
        foreach ($list as $v) {
            $v->refund();
        }
        return view('home');
    }

    /**
     * get 修改密码
     */
    public function getUpPwd()
    {
        return view('auth.passwords.up');
    }

    public function checkPromotionUrl()
    {
        return success();
    }

    /**
     * 个人资料
     */
    public function getMy()
    {
        $user = Auth::user();
        return view('my.my')->with('user', $user);
    }

    /**
     * get 地址完善
     */
    public function getAddr()
    {
        $user = Auth::user();
        return view('my.addr')->with('user', $user);
    }

    /**
     * post 修改密码
     */
    public function postUpPwd(Request $request)
    {
        if (!Hash::check($request->input('opassword'), Auth::user()->getAuthPassword())) {
            return redirect('uppwd')
                ->withErrors(['opassword' => '密码错误']);
        }
        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
        ]);
        DB::table('users')->where('id', Auth::user()->id)->update(['password' => bcrypt($request->input('password'))]);
        return redirect('uppwd')
            ->with(['status' => '密码修改成功']);
    }

    /**
     * post 地址完善
     */
    public function postAddr()
    {
        $user = Auth::user();
        if ($user->idcardpic == '') {
            $this->validate(request(), [
                'mobile'        => 'required|regex:/^1[345789][0-9]{9}$/|unique:users',
                'shipping_addr' => 'required|min:5|max:50',
                'real_name'     => 'required|min:2|max:6',
                'idcardpic'     => 'required',
                'idcardno'      => ['required', 'regex:/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/'],
            ]);
            $pdata['mobile']        = request('mobile');
            $pdata['shipping_addr'] = request('shipping_addr');
            $pdata['real_name']     = request('real_name');
            $pdata['idcardpic']     = request('idcardpic');
            $pdata['idcardno']      = request('idcardno');
        } else {
            $this->validate(request(), [
                'shipping_addr' => 'required|min:5|max:50',
            ]);
            $pdata['shipping_addr'] = request('shipping_addr');
        }
        $user->update($pdata);
        return redirect('addr')
            ->with(['status' => '资料修改成功']);
    }

    /**
     * 上传文件
     * @param Request $request
     * @return string
     */
    public function upload(Request $request)
    {
        switch (request('type')) {
            case 'idcard':
                $file = $request->file('upimg');
                $ext  = $file->getClientOriginalExtension();
                if (!in_array(strtolower($ext), ['jpeg', 'png', 'jpg'])) {
                    return error('文件类型不合法');
                }
                $filename = time() . rand(100000, 999999) . '.' . $ext;
                $file->move('../public/upfile/idcard/', $filename);
                $fullname = '/upfile/idcard/' . $filename;
                break;
            case 'video':
                $file     = $request->file('upvideo');
                $ext      = $file->getClientOriginalExtension();
                $filename = time() . rand(100000, 999999) . '.' . $ext;
                $file->move('../public/upfile/video/', $filename);
                $fullname = '/upfile/video/' . $filename;
                break;
            case 'epic':
                if (!Auth::user()->checkAction('euploadpic')) {
                    return error(Action::where('name', 'euploadpic')->value('auth_desc'));
                }
                $file = $request->file('file');
                $ext  = $file->getClientOriginalExtension();
                if (!in_array(strtolower($ext), ['jpeg', 'png', 'jpg'])) {
                    return error('文件类型不合法');
                }
                $filename = time() . rand(100000, 999999) . '.' . $ext;
                $file->move('../public/upfile/epic/', $filename);
                $fullname = '/upfile/epic/' . $filename;
                break;
            default:
                return error(ERROR_PARAM);
        }

        return success($fullname);
    }

    /**
     * 会员有效期记录
     */
    public function listVip()
    {
        if (Auth::user()->level == 2) {
//            $tname = '会有有效期截止 <span class="color-red">' . substr(Auth::user()->validity, 0, 10) . '</span>';
            $tname = '会有有效期 <span class="color-red">长期</span>';
        } else {
            $tname = '会员有效期记录';
        }
        $list = VipBill::where('uid', Auth::user()->id)->orderBy('id', 'desc')->paginate(config('linepro.perpage'));
        return view('my.list_vip')->with('tname', $tname)->with('list', $list);
    }

    public function updateNotice()
    {
        $uid = Auth::user()->id;
        Cache::forever('notice-' . $uid, time());
        return success();
    }

}

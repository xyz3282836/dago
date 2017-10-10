<?php

namespace App\Http\Controllers;

use App\Faq;
use App\Order;

class IndexController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * 运行环境信息
     */
    public function getInfo()
    {
        Order::makeCfr();
//        phpinfo();
    }

    public function captcha()
    {
        return captcha_img('flat');
    }

    public function faqs()
    {
        return view('index.faq')->with('list', Faq::getFaqs());
    }
}
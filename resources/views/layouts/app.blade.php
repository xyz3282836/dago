<!DOCTYPE html>
<html lang="{{ gconfig('site.name') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ gconfig('site.name') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/iview.css') }}" rel="stylesheet">
    <link href="{{ mix('css/all.css') }}" rel="stylesheet">
    {{--<link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">--}}
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/Ladda/1.0.0/ladda-themeless.min.css" rel="stylesheet">

    @yield('csslib')

    <style>
        .color-red{
            color:red;
        }
        .color-green{
            color:#2ab27b;
        }
        .ladda-button[data-style=contract] {
            width: auto;
        }
        .btn-sm.ladda-button[data-style=contract][data-loading] {
            width: 30px;
        }
        .ladda-button[data-style=contract][data-loading][type=submit] {
            width: 36px;
        }
        .margin-bottom-30{
            margin-bottom: 30px;
        }
        table td{
            max-width: 150px;
        }
        table th,table td{
            text-align: center;
            vertical-align:middle!important;
        }
        table,.font-yahei,form{
            font-family: "Microsoft YaHei";
        }
        .navbar-brand {
            padding: 0;
        }
        .margin-bottom-15{
            margin-bottom: 15px;
        }
        .breadcrumb {
             background-color: transparent;
        }
        .flag-icon {
            width: 4em;
            line-height: 4em;
        }
        table .pic{
            width: 170px;
        }
        table .site{
            width: 130px;
        }
        table .asin{
            width: 130px;
        }
        table .ymxorderid{
            width: 190px;
        }
        table .yunid{
            width: 200px;
        }
        table .status{
            width: 100px;
        }
        table .action{
            width: 200px;
        }
        table .break{
            word-break: break-all;
        }
        .panel-default {
            border-color: rgb(222,236,245);
        }
        .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
            border: 1px solid rgb(222,236,245);
        }

        [v-cloak] {
            display: none;
        }
        .dropdown-menu .rows{
            text-align: center;
        }
        .dropdown-menu .rightb{
            /*border-left: 1px solid #555;*/
        }
        .dropdown-menu .col-xs-4,.dropdown-menu .col-xs-12 {
            padding: 5px;
        }
        .dropdown-menu .col-xs-4 a,.dropdown-menu .col-xs-12 a {
            font-weight: 400;
            line-height: 1.6;
            color: #333;
            white-space: nowrap;
        }
    </style>
    @yield('css')
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        @section('body')
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container-fluid">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{App\Banner::getLogo()}}" alt="" style="margin-left: 25px">
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="javascript:;">帮助QQ交流群:238866069</a></li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}"><i class="fa fa-user-circle" aria-hidden="true"></i> 登入</a></li>
                            <li><a href="{{ route('register') }}">注册</a></li>
                        @else
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    我的海淘 <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{url('orderlist')}}">我的订单</a>
                                    </li>
                                    <li>
                                        <a href="{{url('mycfrlist')}}">我要评价</a>
                                    </li>
                                    <li>
                                        <a href="{{url('promotionlist')}}">我要点赞</a>
                                    </li>
{{--                                    <li><a href="{{ url('addr') }}">达购转运</a></li>--}}
                                </ul>
                            </li>
                            <li><a href="{{ url('card') }}"><i class="fa fa-shopping-cart" aria-hidden="true"></i> 购物车</a></li>
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <i class="fa fa-heart" aria-hidden="true"></i> 海淘心愿 <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ url('qalist') }}">产品Q&A</a>
                                    </li>
                                    <li>
                                        <a href="{{url('wishlist')}}">添加心愿单</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{Auth::user()->role->desc .' : '. Auth::user()->name}} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu" style="width: 270px;padding:5px">
                                    <div class="rows">
                                        <div class="col-xs-4"><a href="javascript:;">{{Auth::user()->golds - Auth::user()->lock_golds}}<img width="15" src="/img/gold.png" /></a></div>
                                        <div class="col-xs-4 rightb"><a href="{{ url('recharge') }}">充值金币</a></div>
                                        <div class="col-xs-4 rightb"><a href="{{ url('rechargelist') }}">充值记录</a></div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="rows">
                                        <div class="col-xs-4"><a href="javascript:;">{{Auth::user()->balance - Auth::user()->lock_balance}}元</a></div>
                                        <div class="col-xs-4 rightb"><a href="javascript:;" style="color: gray;">充值余额</a></div>
                                        <div class="col-xs-4 rightb"><a href="javascript:;" style="color: gray;">充值记录</a></div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="rows">
                                        <div class="col-xs-4"><a href="{{ url('my') }}">个人资料</a></div>
                                        <div class="col-xs-4 rightb"><a href="{{ url('uppwd') }}">修改密码</a></div>
                                        <div class="col-xs-4"><a href="{{url('billlist')}}">账单</a></div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="rows">
                                        <div class="col-xs-12">
                                            <a href="{{ route('logout') }}"
                                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                                注销
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </li>
                            <li><a href="{{ url('recharge') }}">升级</a></li>
                        @endif
                        <li><a href="{{ url('faqs') }}">帮助</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')

        @show
    </div>
    {{--<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>--}}
    {{--<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}
    {{--<script src="https://cdn.bootcss.com/vue/2.2.6/vue.js"></script>--}}
    {{--<script src="https://cdn.bootcss.com/axios/0.16.1/axios.min.js"></script>--}}
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="https://cdn.bootcss.com/layer/3.0.1/layer.min.js"></script>
    <script src="https://cdn.bootcss.com/Ladda/1.0.0/spin.min.js"></script>
    <script src="https://cdn.bootcss.com/Ladda/1.0.0/ladda.min.js"></script>
    @yield('jslib')

    <script>
        $(function () {
            Ladda.bind( 'table .ladda-button', { timeout: 1500 } );
            Ladda.bind( 'form .btn.ladda-button[type=submit]', { timeout: 3000 } );
        });
        function formatDate(date, fmt) {
            if (/(y+)/.test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length));
            }
            let o = {
                'M+': date.getMonth() + 1,
                'd+': date.getDate(),
                'h+': date.getHours(),
                'm+': date.getMinutes(),
                's+': date.getSeconds()
            };
            for (let k in o) {
                if (new RegExp(`(${k})`).test(fmt)) {
                    let str = o[k] + '';
                    fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? str : padLeftZero(str));
                }
            }
            return fmt;
        }
        function padLeftZero(str) {
            return ('00' + str).substr(str.length);
        }
    </script>
    @yield('js')

    {!! gconfig('site.overview1') !!}
    {!! gconfig('site.overview2') !!}
</body>
</html>

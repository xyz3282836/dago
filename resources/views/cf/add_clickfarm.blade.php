@extends('layouts.app')

@section('csslib')
@endsection

@section('css')
    <style type="text/css">
        .col-md-6.control-label,.col-md-1.control-label {
            text-align: left;
            font-size: 1.3em;
        }
        .col-md-1.control-label a{
            color: black;
        }
        .ad{
            width: 150px;
            height: 150px;
            position: fixed;
            padding: 10px;
            align-items:center;
            justify-content: center;
        }
        .ad img{
            width: 150px;
            height: 150px;
            margin-bottom: 20px;
        }
        .layui-layer-page .layui-layer-content {
            padding: 20px 30px;
        }
        .rate{
            font-size: 12px;
            width: 150px;
        }
    </style>
@endsection

@section('jslib')
    <script src="https://cdn.bootcss.com/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">添加代购任务</div>
                    <div class="panel-body">
                        <div class="ad">
                            <div><a target="_blank" href="{{$ad['link']}}"><img src="{{$ad['pic']}}" alt=""></a></div>
                            <div>
                                <table class="rate table table-bordered table-condensed">
                                    <thead>
                                    <tr>
                                        <th>国家</th>
                                        <th>单位</th>
                                        {{--<th>实时汇率</th>--}}
                                        <th>达购汇率</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(v,k) in ratetable">
                                        <td v-text="v.c"></td>
                                        <td v-text="v.name"></td>
                                        {{--<td v-text="v.apirate"></td>--}}
                                        <td width="60" v-text="v.rate"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <form id="dgform" class="form-horizontal" data-toggle="validator" role="form" method="POST" action="{{ url('addclickfarm') }}">
                            {{ csrf_field() }}
                            {{--asin--}}
                            <div class="form-group {{ $errors->has('asin') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 购买的ASIN</label>
                                <div class="col-md-6">
                                    <input readonly type="text" placeholder="" class="form-control" minlength="1" maxlength="24" name="asin" value="{{request('asin')}}" required>
                                    <input type="hidden" name="bd" value="{{request('bd')}}">
                                    <p class="help-block with-errors">{{ $errors->first('asin') }}</p>
                                </div>
                            </div>

                            {{--url--}}
                            <div class="form-group {{ $errors->has('amazon_url') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 亚马逊产品url</label>
                                <div class="col-md-6">
                                    <input readonly type="URL" class="form-control" name="amazon_url" value="{{request('detailUrl')}}" required>
                                    <p class="help-block with-errors">{{ $errors->first('amazon_url') }}</p>
                                </div>
                            </div>

                            {{--pic--}}
                            <div class="form-group {{ $errors->has('amazon_pic') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label"><span class="color-red">*</span>
                                    亚马逊产品图片url</label>
                                <div class="col-md-6">

                                    <input readonly type="URL" placeholder="" class="form-control" name="amazon_pic" value="{{request('picUrl')}}" required>
                                    <p class="help-block with-errors">{{ $errors->first('amazon_pic') }}</p>
                                    <img src="{{request('picUrl')}}" width="150" alt="">
                                </div>
                            </div>

                            {{--title--}}
                            <div class="form-group {{ $errors->has('amazon_title') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label"><span class="color-red">*</span>
                                    亚马逊产品title</label>
                                <div class="col-md-6">
                                    <input readonly type="text" placeholder="" minlength="2" maxlength="50" value="{{request('title')}}" class="form-control" name="amazon_title" required>
                                    <p class="help-block with-errors">{{ $errors->first('amazon_title') }}</p>
                                </div>
                            </div>

                            @if(\Auth::user()->checkAction('sdefault'))
                            {{--keyword--}}
                            <div class="form-group {{ $errors->has('keyword') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">
                                    使用指定关键词搜索</label>
                                <div class="col-md-6">
                                    <input type="text" placeholder="" maxlength="100" value="" class="form-control" name="keyword" style="border: 1px solid #f7820a;">
                                    <p class="help-block with-errors">{{ $errors->first('keyword') }}</p>
                                </div>
                            </div>

                            {{--keyword search--}}
                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 搜索方式</label>
                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input @click="searchprice = {{$s1}}" type="radio" value="1" name="search_type" checked>搜索成交即可
                                    </label>
                                    @if(\Auth::user()->checkAction('scpc'))
                                    <label class="radio-inline">
                                        <input @click="searchprice = {{$s1+$s2}}" type="radio" value="2" name="search_type">通过CPC成交(sponsored)
                                    </label>
                                    @endif
                                    @if(\Auth::user()->checkAction('swishlist'))
                                    <label class="radio-inline">
                                        <input @click="searchprice = {{$s1+$s3}}" type="radio" value="3" name="search_type">添加WishList成交
                                    </label>
                                    @endif
                                    <p class="help-block with-errors"></p>
                                    <Alert type="warning">
                                        <template slot="desc">
                                            {!! \App\Faq::getFaqA(20) !!}
                                        </template>
                                    </Alert>
                                </div>
                            </div>

                            @endif

                            {{--店铺id--}}
                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 店铺名称</label>
                                <div class="col-md-6">
                                    <input readonly type="text" placeholder="" maxlength="50" value="{{request('shopName')}}" class="form-control" name="shop_name" required>
                                    <input type="hidden" name="shop_id" value="{{request('shopId')}}">
                                    <p class="help-block with-errors"></p>
                                </div>
                            </div>

                            {{--发货方式--}}
                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 发货方式</label>
                                <div class="col-md-6">
                                    <label class="radio-inline" v-for="(v,k) in is_fbac" v-cloak>
                                        <input disabled type="radio" v-model="is_fba" :value="k">@{{ v }}
                                    </label>
                                    <input type="hidden" name="is_fba" v-model="is_fba">
                                    <br>
                                    <p class="help-block with-errors"></p>
                                    <Alert type="warning" v-show="is_fba == 0">
                                        <template slot="desc">
                                            {!! \App\Faq::getFaqA(21) !!}
                                        </template>
                                    </Alert>
                                </div>
                            </div>

                            {{--秒杀--}}
                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 秒杀商品</label>
                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input disabled type="radio" v-model="is_ld" value="1">是
                                    </label>
                                    <label class="radio-inline">
                                        <input disabled type="radio" v-model="is_ld" value="0">否
                                    </label>
                                    <input type="hidden" name="is_ld" v-model="is_ld">
                                </div>
                            </div>

                            {{--单价--}}
                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span>
                                    商品价格</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input readonly type="number" required class="form-control" name="final_price" min="0" max="999999" v-model="final_price">
                                        <input type="hidden" name="from_site" value="{{request('site')}}">
                                        <div class="input-group-addon">{{$ctext}}</div>
                                    </div>
                                    <span v-show="(getUnitPrice < {{gconfig('fbm.low.price')}} && is_fba == 0) || (getUnitPrice < {{gconfig('fba.low.price')}} && is_fba == 1)" class="color-red">商品金额过低，存在刷单风险，请选择其他商品</span>
                                    <p class="help-block with-errors"></p>
                                </div>
                            </div>

                            {{--<div class="form-group">--}}
                                {{--<label class="col-md-4 control-label"><span class="color-red">*</span> 下单方式</label>--}}
                                {{--<div class="col-md-6">--}}
                                    {{--<label class="radio-inline" v-for="(v,k) in time_typec">--}}
                                        {{--<input type="radio" v-model="time_type" name="time_type" :value="k" required>@{{ v }}--}}
                                    {{--</label>--}}
                                    {{--<p class="help-block with-errors"></p>--}}
                                {{--</div>--}}
                                {{--<label class="col-md-1 control-label">--}}
                                    {{--<a href="{{url('faqs')}}" target="_blank">?</a>--}}
                                {{--</label>--}}
                            {{--</div>--}}

                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 配送方式</label>
                                <div class="col-md-6">
                                    <label class="radio-inline" v-for="(v,k) in delivery_typec" v-cloak>
                                        <input type="radio" v-model="delivery_type" name="delivery_type" :value="k" required>@{{ v }}
                                    </label>
                                    <p class="help-block with-errors"></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 使用prime会员代购</label>
                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input type="radio" v-model="is_prime" value="1">是
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" v-model="is_prime" value="0">否
                                    </label>
                                    <input type="hidden" name="is_prime" v-model="is_prime">
                                </div>
                            </div>

                            <div class="form-group" v-show="delivery_type == 2">
                                <label class="col-md-4 control-label">国内转运地址</label>
                                <div class="col-md-6">
                                    <input type="text" placeholder="" class="form-control" name="delivery_addr" maxlength="50" value="{{Auth::user()->shipping_addr}}">
                                    <p class="help-block with-errors"></p>
                                </div>
                            </div>

                            {{--num--}}
                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 代购件数</label>
                                <div class="col-md-6">
                                    <input type="number" placeholder="" class="form-control" name="task_num" min="1" max="9999" v-model="task_num" required>
                                    <p>多件商品时，为保护您的隐私，我们会使用不同账号进行代购</p>
                                    <p class="help-block with-errors"></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label"> 国内转运费</label>
                                <label class="col-md-6 control-label" v-text="gettrans"></label>
                                <label class="col-md-1 control-label">
                                    <a href="javascript:;" @click="getOne">?</a>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label"> 所需<img width="15" src="/img/gold.png" /></label>
                                <label class="col-md-6 control-label"><span v-text="getservice"></span><img width="15" src="/img/gold.png" />&nbsp;&nbsp;&nbsp;&nbsp;(1<img width="15" src="/img/gold.png" />=0.01元)</label>
                                <label class="col-md-1 control-label">
                                    <a href="javascript:;" @click="getTwo">?</a>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label"> 共计</label>
                                <label class="col-md-6 control-label" v-text="getall"></label>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary ladda-button" data-style="contract">
                                        提交
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>
        const APP = new Vue({
            el: '#app',
            methods: {
                getOne(){
                    var one = {!! \App\Faq::getFaq(2) !!};
                    layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['800px', '600px'], //宽高
                        content: one.a
                    });
                },
                getTwo(){
                    var one = {!! \App\Faq::getFaq(3) !!};
                    layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['800px', '600px'], //宽高
                        content: one.a
                    });
                }
            },
            mounted: function () {
                this.$nextTick(()=>{

                })
            },
            computed: {
                getUnitPrice() {
                    return (this.final_price * this.rate).toFixed(2);
                },
                getall() {
                    return (this.task_num * this.final_price * this.rate + this.alltrans).toFixed(2) + '元   (售价'+this.final_price+'* 数量'+this.task_num+'* 汇率'+this.rate+' + 运费'+this.alltrans+')';
                },
                getservice() {
                    var tmp = 0;
                    if(this.is_fba == 1){
                        tmp = Number((this.task_num * this.final_price * this.rate * this.rmbtogold * this.srate[this.time_type].rate).toFixed(0));
                        tmp = tmp < Number(this.srate[this.time_type].mingolds) * this.task_num ? Number(this.srate[this.time_type].mingolds) * this.task_num : tmp;
                    }else{
                        tmp += {{$s4}} * this.task_num;
                    }
                    tmp += this.searchprice * this.task_num + {{$s5 + $s6}} * this.task_num;
                    return tmp;
                },
                gettrans() {
                    this.delivery_type == 1 ? this.alltrans = 0 : this.alltrans = this.task_num * this.trans;
                    return this.alltrans.toFixed(2) + '元';
                }
            },
            data: {
                searchprice:{{$s1}},
                ratetable:{!! json_encode(\App\ExchangeRate::getPanel()) !!},
                alltrans: 0,
                rate:{{$rate}},
                rmbtogold:{{$rmbtogold}},
                trans:{{$trans}},
                srate: {!! $srate !!},
                final_price:{{request('totalPrice')}},
                task_num: 0,
                time_type: 1,
                is_fba: {{request('isFba')}},
                is_ld: {{request('isLd',0)}},
                is_fbac: {!! json_encode(config('linepro.is_fba')) !!},
                delivery_type: 1,
                delivery_typec: {!! json_encode(config('linepro.delivery_type')) !!},
                is_prime: 1
            }
        });
        $('#dgform').validator().on('submit', function (e) {
            if(APP.is_fba == 0){
                if("{{$user->checkAction('fbm')?'true':'false'}}" != 'true'){
                    layer.msg('FBM{{\app\Action::where('name', 'fbm')->value('auth_desc')}}');
                    return false;
                }
                if(APP.getUnitPrice < {{gconfig('fbm.low.price')}}){
                    layer.msg('商品金额过低，存在刷单风险，请选择其他商品');
                    return false;
                }
            }
            if(APP.is_fba == 1){
                if(APP.getUnitPrice < {{gconfig('fba.low.price')}}){
                    layer.msg('商品金额过低，存在刷单风险，请选择其他商品');
                    return false;
                }
            }
            if(APP.is_ld == 1){
                if("{{$user->checkAction('seckill')?'true':'false'}}" != 'true'){
                    layer.msg('秒杀{{\app\Action::where('name', 'seckill')->value('auth_desc')}}');
                    return false;
                }
            }
            if(APP.is_prime == 1){
                if("{{$user->checkAction('prime')?'true':'false'}}" != 'true'){
                    layer.msg('prime{{\app\Action::where('name', 'prime')->value('auth_desc')}}');
                    return false;
                }
            }
        })
    </script>
@endsection
@extends('layouts.app')
@section('csslib')
    <link href="{{url('flagicon/css/flag-icon.min.css')}}" rel="stylesheet">
@endsection

@section('jslib')
@endsection
@section('css')
    <style type="text/css">
        .breadcrumb {
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <ol class="breadcrumb">
                        <li><a href="/">首页</a></li>
                        <li class="active">心愿单</li>
                    </ol>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        什么是搜索后添加购物车和心愿单？[点击这里查看帮助]
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <img width="100%" src="{{URL::asset('img/wishlist.png')}}" alt="">
                                        </div>
                                        <div class="col-xs-6">
                                            {!! \App\Faq::getFaqA(22) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <i-Form id="pform" ref="formInline" inline>
                            <Form-Item prop="site">
                                <i-Select v-model="site" style="width:100px">
                                    <i-Option v-for="(v,k) in sitec" :value="k" :key="k" v-cloak>@{{ v }}</i-Option>
                                </i-Select>
                                <input type="hidden" name="site" v-model="site">
                            </Form-Item>
                            <Form-Item prop="status">
                                <i-Select v-model="status" style="width:100px">
                                    <i-Option v-for="(v,k) in statusc" :value="k" :key="k" v-cloak>@{{ v }}</i-Option>
                                </i-Select>
                                <input type="hidden" name="status" v-model="status">
                            </Form-Item>
                            <Form-Item prop="asin">
                                <i-Input name="asin" v-model="asin" placeholder="ASIN">
                                </i-Input>
                            </Form-Item>
                            <Form-Item prop="keywords">
                                <i-Input name="keywords" v-model="keywords" placeholder="关键词">
                                </i-Input>
                            </Form-Item>
                            <Form-Item>
                                <i-Button type="primary" @click="handleSubmit">查询</i-Button>
                                <i-Button type="success" @click="goadd">新增心愿单</i-Button>
                            </Form-Item>
                        </i-Form>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>发布时间</th>
                                <th>站点</th>
                                <th>ASIN</th>
                                <th>关键词</th>
                                <th>单天添加数量</th>
                                <th>计划执行周期</th>
                                <th>金币单价</th>
                                <th>总花费金币</th>
                                <th>目前状态</th>
                                <th>关键词搜索排名</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($list as $v)
                            <tr>
                                <td>{{substr($v->created_at,0,10)}}</td>
                                <td>
                                   <span class="flag-icon flag-icon-{{$v->flag}}"></span>
                                </td>
                                <td v-text="'{{$v->asin}}' == ''? '获取中':'{{$v->asin}}'"></td>
                                <td>{{$v->keywords}}</td>
                                <td>
                                   {{$v->num}}
                                </td>
                                <td>
                                    {{$v->start}}-{{$v->end}}<br>
                                    (<span v-text="dateDiff('{{$v->start}}','{{$v->end}}')"></span> 天)
                                </td>
                                <td>{{$daygold}} <img width="12" src="/img/gold.png" /></td>
                                <td>{{$v->golds}} <img width="12" src="/img/gold.png" /></td>
                                <td>{{$v->status_text}}</td>
                                <td>
                                    <a target="_blank" href="{{$v->url}}">查看效果</a>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="99">暂无数据</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        @if($list)
                            {!!  $list->appends(['site'=>$site,'status'=>$status,'asin'=>$asin,'keywords'=>$keywords])->links() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var app = new Vue({
            el: '#app',
            data:{
                site: "{{$site}}",
                status:"{{$status}}",
                asin:"{{$asin}}",
                keywords:"{{$keywords}}",
                sitec: {!! json_encode(config('linepro.cfr_sitec')) !!},
                statusc: {!! json_encode(config('linepro.wishlist_statusc')) !!},
            },
            methods:{
                handleSubmit(){
                    $('#pform').submit();
                },
                goadd(){
                    window.location = '{{url('wish/add')}}'
                },
                dateDiff(sDate1, sDate2) { //sDate1和sDate2是2006-12-18格式
                    var aDate, oDate1, oDate2, iDays;
                    aDate = sDate1.split("-");
                    oDate1 = new Date(aDate[1] + '-' + aDate[2] + '-' + aDate[0]);
                    aDate = sDate2.split("-");
                    oDate2 = new Date(aDate[1] + '-' + aDate[2] + '-' + aDate[0]);
                    iDays = parseInt(Math.abs(oDate1 - oDate2) / 1000 / 60 / 60 / 24);
                    return iDays + 1;
                },
            }
        });
    </script>
@endsection
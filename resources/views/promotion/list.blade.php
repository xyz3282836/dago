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
                        <li class="active">我要点赞</li>
                    </ol>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        不知道怎么发布需求？[点击这里查看帮助]
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <img width="100%" src="{{URL::asset('img/promotion.gif')}}" alt="">
                                        </div>
                                        <div class="col-xs-6">
                                            {!! \App\Faq::getFaqA(18) !!}
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
                            <Form-Item prop="eid">
                                <i-Input name="eid" v-model="eid" placeholder="评价详情ID">
                                </i-Input>
                            </Form-Item>
                            <Form-Item>
                                <i-Button type="primary" @click="handleSubmit">查询</i-Button>
                                <i-Button type="success" @click="goadd">发布新点赞需求</i-Button>
                            </Form-Item>
                        </i-Form>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>需求发布时间</th>
                                <th>站点</th>
                                <th>ASIN</th>
                                <th>评价详情ID</th>
                                <th>需求</th>
                                <th>花费金币</th>
                                <th>状态</th>
                                <th>操作</th>
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
                               <td>{{$v->eid}}</td>
                               <td>
                                    @if($v->type == 1)
                                        {{$v->num}}个 <Icon type="thumbsup" size="16"></Icon>
                                    @else
                                       {{$v->num}}个 <Icon type="thumbsdown" size="16"></Icon>
                                    @endif
                               </td>
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
                            {!!  $list->appends(['site'=>$site,'status'=>$status,'asin'=>$asin,'eid'=>$eid])->links() !!}
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
                eid:"{{$eid}}",
                sitec: {!! json_encode(config('linepro.cfr_sitec')) !!},
                statusc: {!! json_encode(config('linepro.promotion_statusc')) !!},
            },
            methods:{
                handleSubmit(){
                    $('#pform').submit();
                },
                goadd(){
                    window.location = '{{url('promotion/add')}}'
                }
            }
        });
    </script>
@endsection
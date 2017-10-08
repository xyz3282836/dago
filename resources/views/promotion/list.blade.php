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
                        <Alert closable>
                            自定义关闭内容
                            自定义关闭内容
                            自定义关闭内容
                            自定义关闭内容
                            <span slot="close">我知道了</span>
                        </Alert>


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
                            </Form-Item>
                        </i-Form>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>需求发布时间</th>
                                <th>站点</th>
                                <th>ASIN</th>
                                <th>评价详情ID</th>
                                <th>类型</th>
                                <th>花费金币</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($list as $v)
                            <tr>
                               <td>{{$v->created_at}}</td>
                               <td>
                                   <span class="flag-icon" class="flag-icon-{{$v->flag}}"></span>
                               </td>
                               <td>{{$v->asin}}</td>
                               <td>{{$v->eid}}</td>
                               <td>{{$v->type_text}}</td>
                               <td>{{$v->golds}} <img width="12" src="/img/gold.png" /></td>
                               <td>{{$v->status_text}}</td>
                               <td>

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
                }
            }
        });
    </script>
@endsection
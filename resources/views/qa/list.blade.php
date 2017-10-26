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
                        <li class="active">亚马逊Q&A</li>
                    </ol>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        什么是亚马逊Q&A？[点击这里查看帮助]
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <img width="100%" src="{{URL::asset('img/qa.png')}}" alt="">
                                        </div>
                                        <div class="col-xs-6">
                                            {!! \App\Faq::getFaqA(23) !!}
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
                            <Form-Item>
                                <i-Button type="primary" @click="handleSubmit">查询</i-Button>
                                <i-Button type="success" @click="goadd">新增Q&A</i-Button>
                            </Form-Item>
                        </i-Form>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>发布时间</th>
                                <th>站点</th>
                                <th>ASIN</th>
                                <th>问题</th>
                                <th>目前状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($list as $v)
                            <tr>
                                <td>{{substr($v->created_at,0,10)}}</td>
                                <td>
                                   <span class="flag-icon flag-icon-{{$v->flag}}"></span>
                                </td>
                                <td>{{$v->asin}}</td>
                                <td>{{$v->q}}</td>
                                <td>{{$v->status_text}}</td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="99">暂无数据</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        @if($list)
                            {!!  $list->appends(['site'=>$site,'status'=>$status,'asin'=>$asin])->links() !!}
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
                sitec: {!! json_encode(config('linepro.cfr_sitec')) !!},
                statusc: {!! json_encode(config('linepro.qa_statusc')) !!},
            },
            methods:{
                handleSubmit(){
                    $('#pform').submit();
                },
                goadd(){
                    window.location = '{{url('qa/add')}}'
                },
            }
        });
    </script>
@endsection
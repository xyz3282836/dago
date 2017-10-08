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

                        <Table border :columns="tcolumns" :data="tdata"></Table>

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
                tcolumns: [
                    {
                        title: '需求发布时间',
                        key: 'created_at',
                    },
                    {
                        title: '站点',
                        key: 'site'
                    },
                    {
                        title: 'ASIN',
                        key: 'asin'
                    },
                    {
                        title: '评价详情ID',
                        key: 'edi'
                    },
                    {
                        title: '需求',
                        key: 'up'
                    },
                    {
                        title: '花费金币',
                        key: 'golds'
                    },
                    {
                        title: '状态',
                        key: 'status_text'
                    },
                    {
                        title: '操作',
                        key: 'action',
                        width: 150,
                        align: 'center',
                        render: (h, params) => {
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: 'primary',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            this.show(params.index)
                                        }
                                    }
                                }, '查看'),
                            ]);
                        }
                    }
                ],
                tdata: [
                    {
                        name: '王小明',
                        age: 18,
                        address: '北京市朝阳区芍药居'
                    },
                    {
                        name: '张小刚',
                        age: 25,
                        address: '北京市海淀区西二旗'
                    },
                    {
                        name: '李小红',
                        age: 30,
                        address: '上海市浦东新区世纪大道'
                    },
                    {
                        name: '周小伟',
                        age: 26,
                        address: '深圳市南山区深南大道'
                    }
                ]
            },
            methods:{
                handleSubmit(){
                    $('#pform').submit();
                }
            }
        });
    </script>
@endsection
@extends('layouts.app')
@section('csslib')
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
                        <li><a href="{{url('qalist')}}">亚马逊Q&A</a></li>
                        <li class="active">添加Q&A</li>
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

                        <Row>
                            <i-Col span="3" class-name="text-center">
                                <span style="font-size: 16px">站点</span>
                            </i-Col>
                            <i-Col span="4" offset="1" class-name="text-center">
                                <span style="font-size: 16px">ASIN</span>
                            </i-Col>
                            <i-Col span="8" offset="1" class-name="text-center">
                                <span style="font-size: 16px">问题</span>
                            </i-Col>
                        </Row>
                        <br>
                        <i-Form ref="formDynamic" :model="formDynamic">
                            <Row
                                v-for="(item, index) in formDynamic.items"
                                :key="index">
                                <i-Col span="3" class-name="text-center">
                                    <Form-Item
                                            :prop="'items.' + index + '.from_site'"
                                            :rules="[{required: true, message: '站点不能为空', trigger: 'blur'}]">
                                        <i-Select v-model="item.from_site">
                                            <i-Option v-for="(v,k) in sitec" :value="k" :key="k" v-cloak>@{{ v }}</i-Option>
                                        </i-Select>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="4" offset="1" class-name="text-center">
                                    <Form-Item
                                            :prop="'items.' + index + '.asin'"
                                            :rules="[{required: true, message: 'ASIN不能为空', trigger: 'blur'}]">
                                        <i-Input type="text" v-model="item.asin" placeholder="请输入亚马孙的ASIN"></i-Input>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="8" offset="1" class-name="text-center">
                                    <Form-Item
                                            :prop="'items.' + index + '.q'"
                                            :rules="[{required: true, message: '问题不能为空', trigger: 'blur'}]">
                                        <i-Input type="text" v-model="item.q" placeholder="请输入您的问题"></i-Input>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="1" offset="1">
                                    <Poptip
                                            confirm
                                            title="有未保存的内容，是否确定删除？"
                                            @on-ok="handleRemove(index)">
                                        <i-Button type="ghost" icon="close-round">删除</i-Button>
                                    </Poptip>
                                </i-Col>
                            </Row>
                            <Form-Item>
                                <Row>
                                    <i-Col span="3">
                                    <i-Button type="dashed" long @click="handleAdd" icon="plus-round">添加新的任务</i-Button>
                                    </i-Col>
                                </Row>
                            </Form-Item>
                            <Form-Item>
                                <p>合计结算：<span v-text="allgold"></span> <img width="15" src="/img/gold.png"></p>
                            </Form-Item>
                            <Form-Item>
                                <i-Button {{$btn}} type="primary" @click="handleSubmit('formDynamic')">提交</i-Button>
                                <span class="color-red" v-if="'{{$btn}}' == 'disabled'" v-text="'{{\app\Action::where('name', 'qa')->value('auth_desc')}}'"></span>
                                {{--<i-Button type="ghost" @click="handleReset('formDynamic')" style="margin-left: 8px">重置</i-Button>--}}
                            </Form-Item>
                        </i-Form>
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
                sitec: {!! json_encode(config('linepro.from_sitec')) !!},
                onegold:{{\Auth::user()->getActionGold('qa')}},
                formDynamic: {
                    items: [
                        {
                            from_site: '1',
                            asin:'',
                            q:'',
                        }
                    ]
                }
            },
            computed:{
                allgold(){
                    var golds = 0;
                    this.formDynamic.items.forEach((v) => {
                        if(v.q != '' && v.asin != ''){
                            golds += this.onegold;
                        }
                    });
                    return golds.toFixed(0);
                }
            },
            methods:{
                handleSubmit (name) {
                    this.$refs[name].validate((valid) => {
                        if (valid) {
                            this.$Modal.confirm({
                                title: '确认扣款支付吗？',
                                content: '提交后金币无法回退，请一定核对清晰站点、ASIN，以免执行失败',
                                loading: true,
                                onOk: () => {
                                    axios.post("{{url('addqa')}}", {data:this.formDynamic.items}).then(res => {
                                        if (res.data.code) {
                                            this.$Message.success('提交成功!');
                                            window.location = '/qalist';
                                        } else {
                                            this.$Notice.error({
                                                title: '提交失败',
                                                desc: res.data.msg
                                            });
                                        }
                                        this.$Modal.remove();
                                    });
                                }
                            });
                        }
                    })
                },
//                handleReset (name) {
//                    this.$refs[name].resetFields();
//                },
                handleAdd () {
                    this.formDynamic.items.push({
                        from_site: '1',
                        asin:'',
                        q:'',
                    });
                },
                handleRemove (index) {
                    this.formDynamic.items.splice(index, 1);
                },
            }
        });
    </script>
@endsection
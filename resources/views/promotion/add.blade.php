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
        .ivu-switch {
            border-color: #19be6b;
            background-color: #19be6b;
        }
        .ivu-switch-checked {
            border-color: #39f;
            background-color: #39f;
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
                        <li><a href="{{url('promotionlist')}}">我要点赞</a></li>
                        <li class="active">发布新点赞需求</li>
                    </ol>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        需要点赞推广？
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

                        <Row>
                            <i-Col span="8" class-name="text-center">
                                需求推广review详情链接
                            </i-Col>
                            <i-Col span="3" offset="1" class-name="text-center">
                                <Icon type="thumbsup"></Icon>或者<Icon type="thumbsdown"></Icon>
                            </i-Col>
                            <i-Col span="3" offset="1" class-name="text-center">
                                需求数量
                            </i-Col>
                            <i-Col span="3" offset="1" class-name="text-center">
                                需花费金币
                            </i-Col>
                        </Row>
                        <br>
                        <i-Form ref="formDynamic" :model="formDynamic">
                            <Row
                                v-for="(item, index) in formDynamic.items"
                                :key="index">
                                <i-Col span="8">
                                    <Form-Item
                                            :prop="'items.' + index + '.url'"
                                            :rules="[{required: true, message: '该评价详情链接不能为空', trigger: 'blur'},{validator:validateUrl, trigger: 'blur'}]">
                                        <i-Input type="text" v-model="item.url" placeholder="请输入评价详情链接"></i-Input>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="3" offset="1" class-name="text-center">
                                    <Form-Item >
                                        <i-Switch v-model="item.type" size="large">
                                            <span slot="open">点<Icon type="thumbsup"></Icon></span>
                                            <span slot="close">点<Icon type="thumbsdown"></Icon></span>
                                        </i-Switch>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="3" offset="1">
                                    <Form-Item
                                            :prop="'items.' + index + '.num'"
                                            :rules="[{ type: 'integer', min: 1, message: '该需求量最小大于0且为整数', trigger: 'change' }]">
                                        <i-Input :number="true" type="number" step="1" v-model="item.num" placeholder="需求量"></i-Input>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="3" offset="1" class-name="text-center">
                                    &nbsp;
                                    <span v-if="checkNum(item.num)">
                                        <span v-if="item.type == true"> @{{ goldUp }}<img width="15" src="/img/gold.png">  × @{{ item.num }} = @{{ (goldUp * item.num) .toFixed(0)}}<img width="15" src="/img/gold.png"></span>
                                        <span v-if="item.type == false"> @{{ goldDown }}<img width="15" src="/img/gold.png">  × @{{ item.num }} = @{{ (goldDown * item.num).toFixed(0) }}<img width="15" src="/img/gold.png"></span>
                                    </span>
                                    &nbsp;
                                </i-Col>
                                <i-Col span="3" offset="1">
                                    <i-Button type="ghost" @click="handleRemove(index)" icon="close-round">删除</i-Button>
                                </i-Col>
                            </Row>
                            <Form-Item>
                                <Row>
                                    <i-Col span="3">
                                    <i-Button type="dashed" long @click="handleAdd" icon="plus-round">添加新的推广需求</i-Button>
                                    </i-Col>
                                </Row>
                            </Form-Item>
                            <Form-Item>
                                <p>合计结算：<span v-text="allgold"></span><img width="15" src="/img/gold.png"></p>
                            </Form-Item>
                            <Form-Item>
                                <i-Button type="primary" :loading="loading" @click="handleSubmit('formDynamic')">
                                    <span v-if="!loading">提交</span>
                                    <span v-else>Loading...</span>
                                </i-Button>
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
        var validateUrl = (rule,value,callback) => {
            axios.post("{{url('checkpromotionurl')}}", {url:value}).then(function (d) {
                var data = d.data;
                if (data.code) {
                    callback();
                } else {
                    callback(new Error('该评价详情链接不正确，请仔细检查核对后，重新下单'))
                }
            });
        };
        var app = new Vue({
            el: '#app',
            data:{
                loading: false,
                goldUp:{{\Auth::user()->getActionGold('eup')}},
                goldDown:{{\Auth::user()->getActionGold('edown')}},
                formDynamic: {
                    items: [
                        {
                            url: '',
                            type:true,
                            num:''
                        }
                    ]
                }
            },
            computed:{
                allgold(){
                    var golds = 0;
                    this.formDynamic.items.forEach((v) => {
                        if(v.num > 0){
                            switch (v.type){
                                case true:
                                    golds += this.goldUp * v.num;
                                    break;
                                case false:
                                    golds += this.goldDown * v.num;
                                    break;
                            }
                        }
                    });
                    return golds.toFixed(0);
                }
            },
            methods:{
                handleSubmit (name) {
                    this.$refs[name].validate((valid) => {
                        if (valid) {
                            this.loading = true;
                            axios.post("{{url('addpromotion')}}", {data:this.formDynamic.items}).then(res => {
                                if (res.data.code) {
                                    this.$Message.success('提交成功!');
                                    window.location = '/promotionlist';
                                } else {
                                    this.$Notice.error({
                                        title: '提交失败',
                                        desc: res.data.msg
                                    });
                                }
                                this.loading = false;
                            })
                        }
                    })
                },
//                handleReset (name) {
//                    this.$refs[name].resetFields();
//                },
                handleAdd () {
                    this.formDynamic.items.push({
                        url: '',
                        type:true,
                        num:''
                    });
                },
                handleRemove (index) {
                    this.formDynamic.items.splice(index, 1);
                },
                checkNum(num){
                    if(Number.isInteger(num)){
                        if(num > 0){
                            return true;
                        }
                    }
                    return false;
                }
            }
        });
    </script>
@endsection
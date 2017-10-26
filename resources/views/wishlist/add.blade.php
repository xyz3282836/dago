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
                        <li><a href="{{url('wishlist')}}">心愿单</a></li>
                        <li class="active">搜索添加心愿单</li>
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

                        <Row>
                            <i-Col span="2" class-name="text-center">
                                <span style="font-size: 16px">站点</span>
                            </i-Col>
                            <i-Col span="3" offset="1" class-name="text-center">
                                <span style="font-size: 16px">ASIN</span>
                            </i-Col>
                            <i-Col span="3" offset="1" class-name="text-center">
                                <span style="font-size: 16px">关键词</span>
                            </i-Col>
                            <i-Col span="3" offset="1" class-name="text-center">
                                <span style="font-size: 16px">单天添加心愿单账号数量</span>
                            </i-Col>
                            <i-Col span="3" offset="1" class-name="text-center">
                                <span style="font-size: 16px">选择起始时间</span>
                            </i-Col>
                            <i-Col span="3" offset="1" class-name="text-center">
                                <span style="font-size: 16px">需花费金币</span>
                            </i-Col>
                        </Row>
                        <br>
                        <i-Form ref="formDynamic" :model="formDynamic">
                            <Row
                                v-for="(item, index) in formDynamic.items"
                                :key="index">
                                <i-Col span="2" class-name="text-center">
                                    <Form-Item
                                            :prop="'items.' + index + '.from_site'"
                                            :rules="[{required: true, message: '站点不能为空', trigger: 'blur'}]">
                                        <i-Select v-model="item.from_site">
                                            <i-Option v-for="(v,k) in sitec" :value="k" :key="k" v-cloak>@{{ v }}</i-Option>
                                        </i-Select>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="3" offset="1" class-name="text-center">
                                    <Form-Item
                                            :prop="'items.' + index + '.asin'"
                                            :rules="[{required: true, message: 'ASIN不能为空', trigger: 'blur'}]">
                                        <i-Input type="text" v-model="item.asin" placeholder="请输入ASIN"></i-Input>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="3" offset="1" class-name="text-center">
                                    <Form-Item
                                            :prop="'items.' + index + '.keywords'"
                                            :rules="[{required: true, message: '关键词不能为空', trigger: 'blur'}]">
                                        <i-Input type="text" v-model="item.keywords" placeholder="请输入关键词"></i-Input>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="3" offset="1">
                                    <Form-Item
                                            :prop="'items.' + index + '.num'"
                                            :rules="[{ type: 'integer', min: 1, message: '该需求量最小大于0且为整数', trigger: 'change' }]">
                                        <i-Input :number="true" type="number" step="1" v-model="item.num" placeholder="数量"></i-Input>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="3" offset="1" class-name="text-center">
                                    <Form-Item
                                            :prop="'items.' + index + '.date'"
                                            :rules="[{validator:validateDateRange, trigger: 'change'}]">
                                        <Date-Picker :options="options3" v-model="item.date" format="yyyy-MM-dd" type="daterange" placement="bottom-end" placeholder="选择日期" style="width: 200px"></Date-Picker>
                                    </Form-Item>
                                </i-Col>
                                <i-Col span="3" offset="1" class-name="text-center">
                                    &nbsp;
                                    <span v-if="checkNum(item.num) && item.date.length > 0" v-cloak>
                                        @{{ goldDay }} <img width="15" src="/img/gold.png">  × @{{ item.num }} × @{{ getDays(item.date) }} = @{{ (goldDay * item.num * getDays(item.date)) .toFixed(0)}}<img width="15" src="/img/gold.png"></span>
                                    </span>
                                    &nbsp;
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
                                <span class="color-red" v-if="'{{$btn}}' == 'disabled'" v-text="'{{\app\Action::where('name', 'wishlist')->value('auth_desc')}}'"></span>
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
        var validateDateRange = (rule,value,callback) => {
            if(value.length == 0){
                callback(new Error('起始时间不能为空'));
            }else{
                callback();
            }
        };
        var app = new Vue({
            el: '#app',
            data:{
                options3: {
                    disabledDate (date) {
                        return date && ((date.valueOf() < {{msectime()}} - 86400000));
                    }
                },
                sitec: {!! json_encode(config('linepro.from_sitec')) !!},
                goldDay:{{\Auth::user()->getActionGold('wishlist')}},
                formDynamic: {
                    items: [
                        {
                            from_site: '1',
                            asin:'',
                            keywords:'',
                            num:'',
                            date:[],
                        }
                    ]
                }
            },
            computed:{
                allgold(){
                    var golds = 0;
                    this.formDynamic.items.forEach((v) => {
                        if(v.num > 0 && v.date.length > 0){
                            golds += this.goldDay * v.num * this.getDays(v.date);
                        }
                    });
                    return golds.toFixed(0);
                }
            },
            methods:{
                dateDiff(sDate1, sDate2) { //sDate1和sDate2是2006-12-18格式
                    var aDate, oDate1, oDate2, iDays;
                    aDate = sDate1.split("-");
                    oDate1 = new Date(aDate[1] + '-' + aDate[2] + '-' + aDate[0]);
                    aDate = sDate2.split("-");
                    oDate2 = new Date(aDate[1] + '-' + aDate[2] + '-' + aDate[0]);
                    iDays = parseInt(Math.abs(oDate1 - oDate2) / 1000 / 60 / 60 / 24);
                    return iDays + 1;
                },
                getDays(date){
                    var start = formatDate(date[0],'yyyy-MM-dd');
                    var end = formatDate(date[1],'yyyy-MM-dd');
                    return this.dateDiff(start,end);
                },
                handleSubmit (name) {
                    this.$refs[name].validate((valid) => {
                        if (valid) {
                            this.$Modal.confirm({
                                title: '确认扣款支付吗？',
                                content: '提交评价成功后即作下单，金币无法回退',
                                loading: true,
                                onOk: () => {
                                    axios.post("{{url('addwish')}}", {data:this.formDynamic.items}).then(res => {
                                        if (res.data.code) {
                                            this.$Message.success('提交成功!');
                                            window.location = '/wishlist';
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
                        keywords:'',
                        num:'',
                        date:[],
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
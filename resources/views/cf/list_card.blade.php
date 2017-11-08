@extends('layouts.app')
@section('css')
    <style type="text/css">
        .ad{
            width: 500px;
            height: 42px;
            position: absolute;
            padding: 10px;
            display: flex;
            align-items:center;
            justify-content: center;
            right: 15px;
        }
        .ad img{
            width: 500px;
            height: 42px;
        }
        table .limit{
            text-align: left;
            height: 120px;
            line-height: 30px;

            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
        }
        .breadcrumb{
            margin-bottom: 0;
        }
    </style>
@endsection
@section('csslib')
    <link href="{{url('flagicon/css/flag-icon.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="ad">
                        <a target="_blank" href="{{$ad['link']}}"><img src="{{$ad['pic']}}" alt=""></a>
                    </div>
                    <ol class="breadcrumb">
                        <li><a href="/">首页</a></li>
                        <li class="active">购物车</li>
                    </ol>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" :checked="allc" @click="selectall">全选
                                        </label>
                                    </div>
                                </th>
                                <th>商品图片</th>
                                <th>ASIN</th>
                                <th>亚马逊商品标题</th>
                                <th>商铺ID</th>
                                <th>站点</th>
                                <th>配送方式</th>
                                <th>下单模式</th>
                                <th>送货地址</th>
                                {{--<th>下单方式</th>--}}
                                <th>单价</th>
                                <th>数量</th>
                                <th>汇率</th>
                                <th>手续费<img width="18" src="/img/gold.png" /></th>
                                <th>国内转运费</th>
                                <th>合计总价</th>
                                <th>订单生成时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="one in list">
                                <td>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" v-model="ids" :value="one.id">
                                        </label>
                                    </div>
                                </td>
                                <td><a :href="one.amazon_pic" target="_blank"><img :src="one.amazon_pic" width="100" alt=""></a></td>
                                <td v-text="one.asin"></td>
                                <td>
                                    <div class="limit">
                                        <a :href="one.amazon_url" v-text="one.amazon_title"></a>
                                    </div>
                                </td>
                                <td>
                                    @{{ one.shop_name }}<br>
                                    @{{ one.shop_id }}
                                    <br>
                                    <span style="font-size:12px;color:#464c5b;">(@{{ one.is_ld == 1?'秒杀':one.fba_text }})</span>
                                    <br v-if="one.prime == 1">
                                    <span v-if="one.prime == 1"><img height="12" src="/img/prime.png" /></span>
                                </td>
                                <td><span class="flag-icon" :class="'flag-icon-'+one.flag"></span></td>
                                <td v-text="one.delivery_type_text"></td>
                                <td>
                                    <span style="font-size:14px;font-weight:bold;color:#464c5b;" v-text="one.search_type_text"></span><br>
                                    <span v-if="one.keyword != ''">关键词搜索：@{{ one.keyword }}</span>
                                </td>
                                <td v-text="one.delivery_addr"></td>
                                {{--<td v-text="one.time_type_text"></td>--}}
                                <td v-text="one.final_price_text"></td>
                                <td v-text="one.task_num"></td>
                                <td v-text="one.rate"></td>
                                <td v-text="one.golds">G</td>
                                <td v-text="one.transport">元</td>
                                <td v-text="one.amount">元</td>
                                <td v-text="one.created_at"></td>
                                <td>
                                    <button v-if="one.status == 1" class="btn btn-danger btn-sm ladda-button"
                                            data-style="contract" @click="cancle(one.id)">删除
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="cardlist.length == 0">
                                <td colspan="99">暂无数据</td>
                            </tr>
                            </tbody>
                        </table>
                        <Card style="width:400px">
                            <p slot="title">计划任务</p>
                            <i-Form ref="formCustom" :model="formCustom" :rules="ruleValidate">
                                <Form-Item>
                                    <Radio-Group v-model="plantype">
                                        <Radio label="once">一次性任务</Radio>
                                        <Radio label="cycle">周期性任务</Radio>
                                    </Radio-Group>
                                </Form-Item>
                                <Form-Item label="安排代购时间" v-if="plantype == 'cycle'" label-width="90">
                                    <Row>
                                        <i-Col span="11">
                                            <Form-Item prop="start">
                                                <Date-Picker :on-change="sdates(formCustom.start)" size="large" type="date" v-model="formCustom.start" :options="options3" placeholder="选择开始日期" style="width: 125px"></Date-Picker>
                                            </Form-Item>
                                        </i-Col>
                                        <i-Col span="2" style="text-align: center">-</i-Col>
                                        <i-Col span="11">
                                            <Form-Item prop="end">
                                                <Date-Picker :on-change="edates(formCustom.end)" size="large" type="date" v-model="formCustom.end" :options="options3" placeholder="选择结束日期" style="width: 125px"></Date-Picker>
                                            </Form-Item>
                                        </i-Col>
                                    </Row>
                                </Form-Item>
                                <Alert type="warning">
                                    <template slot="desc">
                                        {!! \App\Faq::getFaqA(19) !!}
                                    </template>
                                </Alert>
                            </i-Form>
                        </Card>
                        <div class="pull-right">
                            <div class="col-xs-6">
                                <p>总金币：<span class="color-red" v-text="allGold"></span> <img width="18" src="/img/gold.png" /></p>
                                <p>抵扣金币：<span class="color-red" v-text="payg"></span> <img width="18" src="/img/gold.png" /></p>
                                <p>需要充值：<span class="color-red" v-text="needPay"></span> 元</p>
                            </div>
                            <div class="col-xs-6">
                                <p>总价格：<span class="color-red" v-text="allPrice"></span> 元</p>
                                <p>抵扣余额：<span class="color-red" v-text="rmb"></span> 元</p>
                                <p>待支付：<span class="color-red" v-text="needRmb"></span> 元</p>

                            </div>
                            <button :disabled="ids.length == 0" class="btn btn-danger btn-md ladda-button"
                                    data-style="contract" @click="payall('formCustom')">支付下单
                            </button>
                        </div>
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
            data: {
                formCustom:{
                    start:'',
                    end:''
                },
                ruleValidate: {
                    start: [
                        { required: true, type: 'date', message: '请选择日期', trigger: 'change' }
                    ],
                    end: [
                        { required: true, type: 'date', message: '请选择日期', trigger: 'change' }
                    ],
                },
                plantype:'once',
                options3: {
                    disabledDate (date) {
                        return date && ((date.valueOf() < {{msectime()}} - 86400000) || (date.valueOf() > {{msectime()}} + 86400000 * 4));
                    }
                },
                startd:'{{date('Y-m-d')}}',
                endd:'{{date('Y-m-d')}}',
                grate:{{gconfig('rmbtogold')}},
                list:{!! $list !!},
                cardlist:{!! $list->keyBy('id') !!},
                allc: true,
                ids: [],
                allids: [],
                deduction_gold: {{Auth::user()->golds - Auth::user()->lock_golds}},
                deduction_balance: {{Auth::user()->balance - Auth::user()->lock_balance}},
            },
            methods: {
                dateDiff(sDate1, sDate2) { //sDate1和sDate2是2006-12-18格式
                    var aDate, oDate1, oDate2, iDays;
                    aDate = sDate1.split("-");
                    oDate1 = new Date(aDate[1] + '-' + aDate[2] + '-' + aDate[0]);
                    aDate = sDate2.split("-");
                    oDate2 = new Date(aDate[1] + '-' + aDate[2] + '-' + aDate[0]);
                    iDays = parseInt(Math.abs(oDate1 - oDate2) / 1000 / 60 / 60 / 24);
                    return iDays + 1;
                },
                sdates(date){
                    if(date != '' && date != undefined){
                        this.startd = formatDate(date,'yyyy-MM-dd');
                    }
                },
                edates(date){
                    if(date != '' && date != undefined){
                        this.endd = formatDate(date,'yyyy-MM-dd');
                    }
                },
                payall(name) {
                    if(this.plantype == 'once'){
                        this.pay();
                    }else{
                        this.$refs[name].validate((valid) => {
                            if (valid) {
                                this.pay();
                            }
                        })
                    }
                },
                pay(){
                    var ids = this.ids;
                    var startd = this.startd;
                    var endd = this.endd;
                    var ptype = this.plantype;
                    layer.confirm('确定支付？', {
                        btn: ['是','再想想'],
                        closeBtn: 0
                    }, function(index){
                        layer.close(index);
                        axios.post("{{url('pay')}}", {id: ids,startd:startd,endd:endd,ptype:ptype}).then(function (d) {
                            var data = d.data;
                            if (!data.code) {
                                layer.msg(data.msg, {icon: 2});
                            } else {
                                layer.msg('操作成功', {icon: 1});
                                if(data.data.type == 'b'){
                                    window.location.href = "/orderlist#o-"+data.data.id;
                                }else{
                                    layer.confirm('即将前往支付宝扫描付款？', {
                                        btn: ['是'],
                                        closeBtn: 0
                                    }, function(index){
                                        layer.close(index);
                                        window.open('/jumppay?id='+ data.data.id)
                                        layer.confirm('支付完成？', {
                                            btn: ['已完成支付','支付遇到问题'],
                                            closeBtn: 0
                                        }, function(index){
                                            layer.close(index);
                                            window.location.href = "/orderlist#o-"+data.data.id;
                                        }, function(index){
                                            layer.close(index);
                                            layer.msg('请联系管理员')
                                            window.location.href = "/orderlist?type=1#o-"+data.data.id;
                                        });
                                    });
                                }
                            }
                        })
                    }, function(index){
                        layer.close(index);
                    });
                },
                cancle: function (id) {
                    axios.post("{{url('canclecf')}}", {id: id}).then(function (d) {
                        var data = d.data;
                        if (!data.code) {
                            layer.msg(data.msg, {icon: 2});
                        } else {
                            layer.msg('操作成功', {icon: 1});
                            window.location.reload()
                        }
                    })
                },
                selectall(){
                    this.allc = !this.allc;
                    this.allc ? this.ids = this.allids : this.ids = []
                },
            },
            computed:{
                payg(){
                    if(this.deduction_gold >= this.allGold){
                        return this.allGold;
                    }else{
                        return this.deduction_gold;
                    }
                },
                rmb(){
                    if(this.deduction_balance >= this.allPrice){
                        return this.allPrice;
                    }else{
                        return this.deduction_balance;
                    }
                },
                needPay(){
                    return ((this.allGold - this.payg)/this.grate).toFixed(2);
                },
                needRmb(){
                    return (this.allPrice - this.rmb).toFixed(2);
                },
                allPrice(){
                    var price = 0;
                    this.ids.forEach((v) => {
                        price += Number(this.cardlist[v].amount);
                    });
                    var days = 1;
                    if(this.plantype == 'cycle'){
                        days = this.dateDiff(this.startd,this.endd);
                    }
                    return (price * days).toFixed(2);
                },
                allGold(){
                    var golds = 0;
                    this.ids.forEach((v) => {
                        golds += Number(this.cardlist[v].golds);
                    });
                    var days = 1;
                    if(this.plantype == 'cycle'){
                        days = this.dateDiff(this.startd,this.endd);
                    }
                    return golds * days;
                }
            },
            mounted: function () {
                this.$nextTick(() => {
                    this.list.forEach((v, k) => {
                        this.allids.push(v.id)
                        })
                    this.ids = this.allids
                    }
                )
            }
        });
    </script>
@endsection
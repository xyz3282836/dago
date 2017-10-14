@extends('layouts.app')
@section('csslib')
    <link href="{{url('flagicon/css/flag-icon.min.css')}}" rel="stylesheet">
@endsection

@section('jslib')
@endsection
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
        .table .table {
            background-color: white;
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
                        <li class="active">订单管理</li>
                    </ol>
                    <div class="panel-body">
                        <form class="form-inline margin-bottom-30" action="{{url('orderlist')}}" method="get">
                            <div class="form-group">
                                <Date-Picker :on-change="dates(date)" size="large" type="daterange" v-model="date"  :options="options2" placement="bottom-start" placeholder="选择日期" style="width: 220px"></Date-Picker>
                                <input type="hidden" name="start" v-model="start">
                                <input type="hidden" name="end" v-model="end">
                            </div>
                            <div class="form-group">
                                {{--<select class="form-control select-sm" name="type" required v-model="type">--}}
                                    {{--<option v-for="(v,k) in typec" v-text="v" :value="k"></option>--}}
                                {{--</select>--}}
                                <i-Select v-model="type" style="width:100px" size="large">
                                    <i-Option v-for="(v,k) in typec" :value="k" :key="k" v-cloak>@{{ v }}</i-Option>
                                </i-Select>
                                <input type="hidden" name="type" v-model="type">
                            </div>
                            <button type="submit" class="btn btn-primary ladda-button" data-style="contract">查询</button>
                        </form>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                {{--<th>#</th>--}}
                                <th>时间</th>
                                <th>订单号</th>
                                <th>总价</th>
                                <th>余额支出</th>
                                <th>充值支出</th>
                                <th>金币支出</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($list as $v)
                                <tr id="o-{{$v->id}}">
                                    {{--<td>{{$v->id}}</td>--}}
                                    <td>{{$v->created_at}}</td>
                                    <td class="break">{{$v->orderid}}</td>
                                    <td>{{$v->price}} 元</td>
                                    <td>{{$v->balance}} 元</td>
                                    <td>{{$v->pay}} 元</td>
                                    <td>{{$v->golds}} <img width="12" src="/img/gold.png" /></td>
                                    <td>
                                        @if($v->status == 1)
                                            <a href="javascript:;" @click="pay({{$v->id}})" class="btn btn-success btn-sm">支付订单</a>
                                            <button class="btn btn-danger btn-sm ladda-button"
                                                    data-style="contract" @click="del({{$v->id}})">取消订单
                                            </button>
                                        @elseif($v->status == \App\Order::STATUS_FROZEN)
                                            <button class="btn btn-danger btn-sm ladda-button"
                                                    data-style="contract" @click="cancel({{$v->id}})">退单
                                            </button>
                                        @else
                                            {{$v->status_text}}
                                        @endif

                                    </td>
                                </tr>
                                @if($v->type == 2)
                                <tr>
                                    <td>
                                        <table class="table table-bordered">
                                            <thead>
                                            <th>下单模式</th>
                                            <th>商铺ID</th>
                                            <th>计划任务时间</th>
                                            </thead>
                                            <tbody>
                                            @foreach($v->cfs as $vv)
                                                <tr>
                                                    <td style="height: 136px">
                                                        <span style="font-size:14px;font-weight:bold;color:#464c5b;">{{$vv->search_type_text}}</span><br>
                                                        @if($vv->keyword != '')
                                                            <span>关键词搜索：{{ $vv->keyword }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $vv->shop_id }}
                                                        <br>
                                                        <span style="font-size:12px;color:#464c5b;">({{$vv->fba_text}})</span>
                                                    </td>
                                                    <td>
                                                        {{substr($vv->start_time,0,10)}}<br>
                                                        {{substr($vv->start_time,10,6)}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                    <td colspan="99">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>商品图片</th>
                                                <th>商品标题</th>
                                                <th>站点</th>
                                                {{--<th>下单方式</th>--}}
                                                <th>单价</th>
                                                <th>货币汇率</th>
                                                <th>商品数量</th>
                                                {{--<th>转运费</th>--}}
                                                <th>手续费率</th>
                                                <th>手续费</th>
                                                <th>合计总价</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($v->cfs as $vv)
                                                <tr>
                                                    <td><a href="{{$vv->amazon_pic}}" target="_blank"><img src="{{$vv->amazon_pic}}" width="100" alt=""></a></td>
                                                    <td>
                                                        <div class="limit">
                                                            <a href="{{$vv->amazon_url}}">{{$vv->amazon_title}}</a>
                                                        </div>
                                                    </td>
                                                    <td><span class="flag-icon flag-icon-{{App\ExchangeRate::getFlag($vv->from_site)}}"></span></td>
{{--                                                    <td>{{$vv->time_type_text}}</td>--}}
                                                    <td>{{$vv->final_price_text}}</td>
                                                    <td>{{$vv->rate}}</td>
                                                    <td>{{$vv->task_num}}</td>
                                                    {{--<td>{{$vv->transport}} 元</td>--}}
                                                    <td>{{$vv->srate * 100}} %</td>
                                                    <td>{{$vv->golds}} <img width="12" src="/img/gold.png" /></td>
                                                    <td>{{$vv->amount}} 元</td>
                                                    <td>
                                                        @if($v->status > 1)
                                                            @if($v->status == 8)
                                                                代下单任务派遣中，这几分钟内仍然可以取消订单哟~
                                                                @else
                                                                <a class="btn btn-primary btn-sm" href="{{url('viewclickfarm/'.$vv->id)}}">详情</a>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="99">暂无数据</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        @if($list)
                            {!!  $list->appends(['start'=>$start,'end'=>$end,'type'=>$type])->links() !!}

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
            data: {
                type: {{$type}},
                typec: {!! json_encode(config('linepro.order_statuss')) !!},
                date:[
                    new Date('{{$start}}'),new Date('{{$end}}')
                ],
                start:'{{$start}}',
                end:'{{$end}}',
                options2: {
                    shortcuts: [
                        {
                            text: '最近一周',
                            value () {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                                return [start, end];
                            }
                        },
                        {
                            text: '最近一个月',
                            value () {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                                return [start, end];
                            }
                        },
                        {
                            text: '最近三个月',
                            value () {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                                return [start, end];
                            }
                        }
                    ]
                }
            },
            methods:{
                del(id) {
                    axios.post("{{url('delorder')}}", {id: id}).then(function (d) {
                        var data = d.data;
                        if (!data.code) {
                            layer.msg(data.msg, {icon: 2});
                        } else {
                            layer.msg('操作成功', {icon: 1});
                            window.location.reload();
                        }
                    })
                },
                cancel(id) {
                    axios.post("{{url('cancelorder')}}", {id: id}).then(function (d) {
                        var data = d.data;
                        if (!data.code) {
                            layer.msg(data.msg, {icon: 2});
                        } else {
                            layer.msg('操作成功', {icon: 1});
                            window.location.reload();
                        }
                    })
                },
                pay(id) {
                    window.open('/jumppay?id='+ id);
                    layer.confirm('支付完成？', {
                        btn: ['已完成支付','支付遇到问题'],
                        closeBtn: 0
                    }, function(index){
                        layer.close(index);
                        window.location.href = "{{url('orderlist')}}";
                    }, function(index){
                        layer.close(index);
                        window.location.href = "{{url('orderlist')}}";
                    });
                },
                dates(date){
                    this.start = formatDate(date[0],'yyyy-MM-dd');
                    this.end = formatDate(date[1],'yyyy-MM-dd');
                }
            }

        })
    </script>
@endsection
@extends('layouts.app')
@section('csslib')
@endsection

@section('jslib')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{$tname}}</div>
                    <div class="panel-body">

                        <form class="form-inline margin-bottom-30" action="{{url('billlist')}}" method="get">
                            <div class="form-group">
                                <Date-Picker :on-change="dates(date)" size="large" type="daterange" v-model="date"  :options="options2" placement="bottom-start" placeholder="选择日期" style="width: 220px"></Date-Picker>
                                <input type="hidden" name="start" v-model="start">
                                <input type="hidden" name="end" v-model="end">
                            </div>
                            <div class="form-group">
                                <i-Select v-model="type" style="width:110px" size="large">
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
                                <th>类型</th>
                                <th>订单号</th>
                                <th>支付宝订单号</th>
                                <th>人民币收入</th>
                                <th>人民币支出</th>
                                <th>金币收入</th>
                                <th>金币支出</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($list as $v)
                                <tr>
{{--                                    <td>{{$v->id}}</td>--}}
                                    <td>{{$v->created_at}}</td>
                                    <td>{{$v->type_text}}</td>
                                    <td class="break">{{$v->orderid}}</td>
                                    <td class="break">{{$v->alipay_orderid}}</td>
                                    <td :class="{'color-red': {{$v->in}}>0.00}">+ {{$v->in}}</td>
                                    <td :class="{'color-green': {{$v->out}}>0.00}">- {{$v->out}}</td>
                                    <td :class="{'color-red': {{$v->gin}}>0.00}">+ {{$v->gin}}</td>
                                    <td :class="{'color-green': {{$v->gout}}>0.00}">- {{$v->gout}}</td>
                                </tr>
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
        new Vue({
            el: '#app',
            data:{
                type:"{{$type}}",
                typec: {!! json_encode(config('linepro.bill_types')) !!},
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
                dates(date){
                    this.start = formatDate(date[0],'yyyy-MM-dd');
                    this.end = formatDate(date[1],'yyyy-MM-dd');
                }
            }
        })
    </script>
@endsection
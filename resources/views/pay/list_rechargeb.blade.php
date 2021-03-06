@extends('layouts.app')

@section('css')
    <style type="text/css">

    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{$tname}}</div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                {{--<th>#</th>--}}
                                <th>充值单号</th>
                                <th>支付宝单号</th>
                                <th>充值余额</th>
                                <th>充值时间</th>
                                <th>充值类型</th>
                                <th>状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($list as $v)
                                <tr>
                                    {{--                                    <td>{{$v->id}}</td>--}}
                                    <td class="break">{{$v->orderid}}</td>
                                    <td class="break">{{$v->alipay_orderid}}</td>
                                    <td class="color-red">{{$v->price}} 元</td>
                                    <td>{{$v->created_at}}</td>
                                    <td>{{$v->payment_type_text}}</td>
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
                            {!!  $list->links() !!}

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

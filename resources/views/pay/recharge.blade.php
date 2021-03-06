@extends('layouts.app')
@section('csslib')
@endsection
@section('css')
    <style type="text/css">
        .col-md-6.control-label {
            text-align: left;
        }
    </style>
@endsection
@section('jslib')
    <script src="https://cdn.bootcss.com/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <Alert type="warning">充值金币（平台虚拟货币，仅可抵扣平台服务手续费，不可用于支付产品本金，如需充值余额，请点击 <a href="/rechargeb">充值余额</a>）</Alert>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('recharge/pay') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 充值方式</label>
                                <div class="col-md-6">
                                    <label class="radio-inline" v-for="(v,k) in typec" v-cloak>
                                        <input type="radio" v-model="type" name="type" :value="k" required>@{{ v }}
                                    </label>
                                    <p class="help-block with-errors"></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label"><span class="color-red">*</span> 充值金额</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" placeholder="" class="form-control" step="{{$role->gold_step}}" min="{{$role->gold_recharge}}" max="999999" maxlength="6" name="amount" v-model="amount" required>
                                        <div class="input-group-addon">元</div>
                                    </div>
                                    <p class="help-block with-errors"></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label"> 金币</label>
                                <label class="col-md-6 control-label" v-text="getgolds"></label>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary ladda-button" data-style="contract">
                                        提交
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <img src="{{\App\Banner::getAd(5)['pic']}}" alt="">
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        new Vue({
            el: '#app',
            data:{
                type:'1',
                amount:{{$role->gold_recharge}},
                rate:{{gconfig('rmbtogold')}},
                typec: {!! json_encode(config('linepro.order_ptypec')) !!},
            },
            computed:{
                getgolds(){
                    return (this.amount * this.rate).toFixed(0)
                }
            }
        })
    </script>
@endsection
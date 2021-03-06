@extends('layouts.app')
@section('css')
    <style type="text/css">
        .layui-layer-page .layui-layer-content {
            padding: 20px 30px;
        }
        .one{
            display: inline;
        }
    </style>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">注册</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">用户名</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" maxlength="35" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                            <label for="mobile" class="col-md-4 control-label">手机号</label>
                            <div class="col-md-6">
                                <input id="mobile" pattern="1[345789][0-9]{9}" type="text" class="form-control" name="mobile" value="{{ old('mobile') }}" placeholder="重要，订单异常时客服会电话联系您" title="请输入正确的手机号码" required>
                                @if ($errors->has('mobile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">确认密码</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                            <label for="captcha" class="col-md-4 control-label">验证码</label>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <input id="captcha" class="form-control" type="captcha" name="captcha" required>
                                    @if ($errors->has('captcha'))
                                        <span class="help-block">
                                                <strong>验证码输入错误</strong>
                                            </span>
                                    @endif
                                </div>
                                <span class="col-md-1 refereshrecapcha" @click="refreshCaptcha">
                                    {!! captcha_img('flat')  !!}
                                    </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label class="one">
                                        <input type="checkbox" name="yes" required>
                                    </label><a href="JavaScript:;" @click="mzsm">免责申明</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary ladda-button" data-style="contract">
                                    注册
                                </button>
                            </div>
                        </div>
                    </form>
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
            methods: {
                refreshCaptcha:function(){
                    axios.get("{{url('refereshcapcha')}}").then(function (d) {
                        $('.refereshrecapcha').html(d.data);
                    })
                },
                mzsm:function () {
                    var one = {!! \App\Faq::getFaq(15) !!};
                    layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['800px', '600px'], //宽高
                        content: one.a
                    });
                }
            },
            mounted: function () {
                this.$nextTick(()=>{
                })
            },
            data:{
            }
        });
    </script>
@endsection
@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">登入</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email"
                                           value="{{ old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
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
                                        <label>
                                            <input type="checkbox"
                                                   name="remember" {{ old('remember') ? 'checked' : '' }}> 记住我
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary ladda-button" data-style="contract">
                                        登入
                                    </button>

                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        忘记密码?
                                    </a>

                                    <a class="btn btn-link" href="{{ route('register') }}">
                                        尚未注册？
                                    </a>
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

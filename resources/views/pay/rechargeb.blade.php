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
                    <div class="panel-heading">余额充值页面</div>
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="panel-body">
                        <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
                            <Form-Item label="姓名" prop="name">
                                <Input-Number v-model="value"></Input-Number>
                            </Form-Item>
                        </Form>
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
                value:100
            }
        })
    </script>
@endsection
@extends('layouts.app')
@section('csslib')
@endsection

@section('jslib')
@endsection
@section('css')
    <style type="text/css">
        .breadcrumb{
            margin-bottom: 0;
        }
        table .limit{
            word-wrap: break-word;
            text-align: left;
            height: 60px;
            line-height: 20px;

            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
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
                        <li><a href="{{url('orderlist')}}">订单管理</a></li>
                        <li class="active">订单详情</li>
                    </ol>
                    <div class="panel-body">
                        <div class="media margin-bottom-15">
                            <div class="media-left media-middle">
                                <a href="#">
                                    <a href="{{$cf->amazon_pic}}" target="_blank"><img src="{{$cf->amazon_pic}}" width="100" alt=""></a>
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">{{$cf->amazon_title}}</h4>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    {{--<th>#</th>--}}
                                    {{--<th>店铺id</th>--}}
                                    <th class="asin">ASIN</th>
                                    <th class="ymxorderid">亚马逊订单号</th>
                                    {{--<th>物流</th>--}}
                                    <th class="yunid">物流单号</th>
                                    <th>评价详情</th>
                                    <th class="status">状态</th>
                                    <th class="action">评价</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($list as $v)
                                <tr>
{{--                                    <td>{{$v->id}}</td>--}}
                                    {{--<td>{{$v->shop_id}}</td>--}}
                                    <td>{{$v->asin}}</td>
                                    <td class="break">{{$v->amazon_orderid}}</td>
{{--                                    <td>{{$v->amazon_logistics_company}}</td>--}}
                                    <td class="break">{{$v->amazon_logistics_orderid}}</td>
                                    <td width="300" style="text-align: left">
                                        <p>评价星级：@if($v->estatus > 1){{$v->star}} @endif</p>
                                        <p>评价标题：{{$v->title}}</p>
                                        <div class="limit">
                                            评价内容：{{$v->content}}
                                        </div>
                                    </td>
                                    <td>{{$v->status_text}}</td>
                                    <td>
                                        @if(in_array($v->estatus,[1,2,3]))
                                            <button class="btn btn-primary btn-sm" @click="initForm('formValidate',{{$v->id}})">{{$v->estatus_text}}</button>
                                        @elseif($v->estatus == 7)
                                            <button class="btn btn-primary btn-sm" @click="initForm('formValidate',{{$v->id}})">{{$v->estatus_text}}</button>
                                        @elseif($v->estatus == 5)
                                            {{$v->estatus_text}} <a href="{{$v->amazon_review_id}}" target="_blank">查看</a>
                                        @else
                                            {{$v->estatus_text}}
                                        @endif
                                        @if($v->estatus != 5)
                                            <br>
                                            预计{{$v->etime}}后留评
                                        @endif
                                        @if($v->estatus == 7)
                                            <br>
                                            <span class="color-red">评价文字重复</span>
                                        @endif
                                    </td>
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
    <Modal
            v-model="modal1"
            title="评价">

        <i-Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
            <Form-Item label="星级" prop="star">
                <Rate v-model="formValidate.star"></Rate>
            </Form-Item>
            <Form-Item label="标题" prop="title">
                <i-Input v-model="formValidate.title" placeholder="输入标题"></i-Input>
            </Form-Item>
            <Form-Item label="评价正文" prop="content">
                <i-Input v-model="formValidate.content" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="请输入..."></i-Input>
            </Form-Item>
            <Form-Item label="评价图片" prop="epic">
                <div class="iview-upload-list" v-for="item in uploadList">
                    <template v-if="item.status === 'finished'">
                        <img :src="item.url">
                        <div class="iview-upload-list-cover">
                            <Icon type="ios-eye-outline" @click.native="handleView(item.name)"></Icon>
                            <Icon type="ios-trash-outline" @click.native="handleRemove(item)"></Icon>
                        </div>
                    </template>
                    <template v-else>
                        <Progress v-if="item.showProgress" :percent="item.percentage" hide-info></Progress>
                    </template>
                </div>
                <Upload
                        ref="upload"
                        :show-upload-list="false"
                        :default-file-list="defaultList"
                        :on-success="handleSuccess"
                        :format="['jpg','jpeg','png']"
                        :max-size="2048"
                        :on-format-error="handleFormatError"
                        :on-exceeded-size="handleMaxSize"
                        :before-upload="handleBeforeUpload"
                        type="drag"
                        action="{{url('upload?type=epic&_token='.csrf_token())}}"
                        style="display: inline-block;width:58px;">
                    <div style="width: 58px;height:58px;line-height: 58px;">
                        <Icon type="camera" size="20"></Icon>
                    </div>
                </Upload>

            </Form-Item>
            <Form-Item>
                <i-Button type="primary" @click="handleSubmit('formValidate')">提交</i-Button>
            </Form-Item>
        </i-Form>
    </Modal>
    <Modal title="查看图片" v-model="visible">
        <img :src="imgName" v-if="visible" style="width: 100%">
    </Modal>
@endsection

@section('js')
    <script>
        var app = new Vue({
            el: '#app',
            data:{
                list:{!! $list->keyBy('id') !!},
                imgName: '',
                visible: false,
                uploadList: [],

                modal1: false,

                formValidate: {
                    id:0,
                    star: 0,
                    title: '',
                    content: '',
                    epic: [],
                },

                defaultList: [],

                ruleValidate: {
                    title: [
                        { required: true, message: '标题不能为空', trigger: 'blur' },
                    ],
                    content: [
                        { required: true, message: '请输入评价正文', trigger: 'blur' },
                    ],
                }
            },
            methods: {
                handleView (name) {
                    this.imgName = name;
                    this.visible = true;
                },
                handleRemove (file) {
                    // 从 upload 实例删除数据
                    const fileList = this.$refs.upload.fileList;
                    this.$refs.upload.fileList.splice(fileList.indexOf(file), 1);

                    this.formValidate.epic.splice(this.formValidate.epic.indexOf({name:file.name,url:file.url}),1);
                },
                handleSuccess (res, file) {
                    // 因为上传过程为实例，这里模拟添加 url
                    if(res.code){
                        file.url  = res.data;
                        file.name = res.data;
                        this.formValidate.epic.push({name:file.name,url:file.url});
                    }else{
                        this.$Notice.warning({
                            title: '上传错误',
                            desc: res.msg
                        });
                        this.handleRemove(file)
                    }
                },
                handleFormatError (file) {
                    this.$Notice.warning({
                        title: '文件格式不正确',
                        desc: '文件 ' + file.name + ' 格式不正确，请上传 jpg 或 png 格式的图片。'
                    });
                },
                handleMaxSize (file) {
                    this.$Notice.warning({
                        title: '超出文件大小限制',
                        desc: '文件 ' + file.name + ' 太大，不能超过 2M。'
                    });
                },
                handleBeforeUpload () {
                    const check = this.uploadList.length < 5;
                    if (!check) {
                        this.$Notice.warning({
                            title: '最多只能上传 5 张图片。'
                        });
                    }
                    return check;
                },
                handleSubmit (name) {
                    this.$refs[name].validate((valid) => {
                        if (valid) {
                            axios.post("{{url('cf/evaluate')}}",app.formValidate).then(res => {
                                if(res.data.code){
                                    window.location.reload()
                                }else{
                                    this.$Notice.error({
                                        title: '评价错误',
                                        desc: res.data.msg
                                    });
                                }
                            })
                        }
                    })
                },
                initForm(name,id){
                    this.$refs[name].resetFields();
                    this.modal1 = true;
                    this.formValidate.id = this.list[id].id;
                    this.formValidate.title = this.list[id].title;
                    this.formValidate.star = this.list[id].star;
                    this.formValidate.epic = this.list[id].epic;
                    this.formValidate.content = this.list[id].content;
                    this.syncUpList(this.formValidate.epic);
                },
                syncUpList(epic){
                    const upload = this.$refs.upload;
                    upload.fileList = epic.map(item => {
                        item.status = 'finished';
                        item.percentage = 100;
                        item.uid = Date.now() + upload.tempIndex++;
                        return item;
                    });
                    this.uploadList = upload.fileList;
                }
            },
            mounted: function () {
                this.$nextTick(() => {
                })
            },
        });
    </script>
@endsection
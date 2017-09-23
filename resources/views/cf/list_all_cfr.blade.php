@extends('layouts.app')
@section('csslib')
    <link href="{{url('flagicon/css/flag-icon.min.css')}}" rel="stylesheet">
@endsection

@section('jslib')
@endsection
@section('css')
    <style type="text/css">
        .breadcrumb {
            margin-bottom: 0;
        }

        table .limit {
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
                    {{--<div class="panel-heading">--}}
                    {{----}}
                    {{--</div>--}}
                    <ol class="breadcrumb">
                        <li><a href="/">首页</a></li>
                        @if(request()->path() == 'mycfrlist')
                        <li class="active">评价任务</li>
                        @else
                        <li><a href="{{url('orderlist')}}">订单管理</a></li>
                        <li class="active">评价任务</li>
                        @endif
                    </ol>
                    <div class="panel-body">
                        @if(request()->path() == 'mycfrlist')
                        <form class="form-inline margin-bottom-30" action="{{url('mycfrlist')}}" method="get">
                            <div class="form-group">
                                <i-Input v-model="asin" name="asin" size="large" placeholder="ASIN"></i-Input>
                            </div>
                            <div class="form-group">
                                <i-Select v-model="site" style="width:100px" size="large">
                                    <i-Option v-for="(v,k) in sitec" :value="k" :key="k" v-cloak>@{{ v }}</i-Option>
                                </i-Select>
                                <input type="hidden" name="site" v-model="site">
                            </div>
                            <div class="form-group">
                                <i-Select v-model="type" style="width:110px" size="large">
                                    <i-Option v-for="(v,k) in typec" :value="k" :key="k" v-cloak>@{{ v }}</i-Option>
                                </i-Select>
                                <input type="hidden" name="type" v-model="type">
                            </div>
                            <button type="submit" class="btn btn-primary ladda-button" data-style="contract">查询</button>
                        </form>
                        @else
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
                        @endif
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                {{--<th>#</th>--}}
                                <th class="pic">商品图片</th>
                                <th class="site">站点</th>
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
                                    {{--<td>{{$v->id}}</td>--}}
                                    <td><a href="{{$v->cf->amazon_pic}}" target="_blank"><img
                                                    src="{{$v->cf->amazon_pic}}" width="100" alt=""></a></td>
                                    <td><span class="flag-icon flag-icon-{{$v->cf->flag}}"></span></td>
                                    {{--                                    <td>{{$v->shop_id}}</td>--}}
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
                                            <button class="btn btn-primary btn-sm"
                                                    @click="initForm('formValidate',{{$v->id}})">{{$v->estatus_text}}</button>
                                        @elseif($v->estatus == 7)
                                            <button class="btn btn-primary btn-sm"
                                                    @click="initForm('formValidate',{{$v->id}})">{{$v->estatus_text}}</button>
                                        @elseif($v->estatus == 5)
                                            {{$v->estatus_text}} <a href="{{$v->amazon_review_id}}"
                                                                    target="_blank">查看</a>
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
                            @if(request()->path() == 'mycfrlist')
                            {!!  $list->appends(['type'=>$type,'asin'=>$asin,'site'=>$site])->links() !!}
                            @else
                            {!!  $list->links() !!}
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Modal
            :mask-closable="false"
            width="600"
            v-model="modal1"
            cancel-text=''
            :footer-hide='true'
            title="评价">

        <i-Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
            <Form-Item label="星级" prop="star">
                <Rate v-model="formValidate.star"></Rate>
            </Form-Item>
            <Form-Item label="标题" prop="title">
                <i-Input v-model="formValidate.title" placeholder="输入标题"></i-Input>
            </Form-Item>
            <Form-Item label="评价正文" prop="content">
                <i-Input v-model="formValidate.content" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                         placeholder="请输入..."></i-Input>
            </Form-Item>
            <Form-Item label="评价图片" prop="epic">
                <div class="iview-upload-list" v-for="item in uploadList">
                    <template v-if="item.status === 'finished'">
                        <img :src="item.url">
                        <div class="iview-upload-list-cover">
                            <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                            <Icon type="ios-trash-outline" @click.native="handleRemove(item)"></Icon>
                        </div>
                    </template>
                    <template v-else>
                        <i-Progress v-if="item.showProgress" :percent="item.percentage" hide-info></i-Progress>
                    </template>
                </div>
                <Upload
                        multiple
                        ref="upload"
                        :show-upload-list="false"
                        :default-file-list="defaultList"
                        :on-success="handleSuccess"
                        accept="image/jpeg"
                        :format="['jpeg','jpg']"
                        :max-size="3072"
                        :on-format-error="handleFormatError"
                        :on-exceeded-size="handleMaxSize"
                        :before-upload="handleBeforeUpload"
                        :data="token"
                        type="drag"
                        action="{{url('upload?type=epic')}}"
                        :on-error="handleError"
                        style="display: inline-block;width:58px;">
                    <div style="width: 58px;height:58px;line-height: 58px;">
                        <Icon type="camera" size="20"></Icon>
                    </div>
                </Upload>

            </Form-Item>

            <Form-Item label="评价视频" prop="evideo">
                <Upload
                        ref="vupload"
                        :default-file-list="defaultList"
                        :on-success="vhandleSuccess"
                        :data="qiniutoken"
                        :format="['mp4']"
                        accept="video/mp4"
                        :max-size="51200"
                        :on-format-error="vhandleFormatError"
                        :on-exceeded-size="vhandleMaxSize"
                        :on-error="handleError"
                        :on-remove="vhandleRemove"
                        :before-upload="vhandleBeforeUpload"
                        action="http://upload.qiniu.com">
                    <i-Button type="ghost" icon="ios-cloud-upload-outline">上传视频</i-Button>
                </Upload>
            </Form-Item>
            <Alert type="warning">
                <template slot="desc">
                    <ul>
                        <li>· 仅会员可使用传图、传视频功能。{{gconfig('vip.imgvideo.text')}}</li>
                        <li>· 格式：JPG/PNG单图最大3M，最多5张；视频仅支持MP4，最大50M</li>
                        <li>· 相关金币一经扣减无法返还，请谨慎操作！扣除后将在账单显示相应内容</li>
                    </ul>
                </template>
            </Alert>
            <Form-Item  label="金币结算">
                <i-Table :border="true" :columns="columns1" :data="data1" size="small"></i-Table>
                <p>总计需求：<span v-text="allgold" class="color-red"></span></p>
            </Form-Item>
            <Form-Item>
                <i-Button type="primary" @click="handleSubmit('formValidate')">提交</i-Button>
            </Form-Item>
        </i-Form>
    </Modal>
    <Modal title="查看图片" v-model="visible">
        <img :src="imgUrl" v-if="visible" style="width: 100%">
    </Modal>
@endsection

@section('js')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                consume:{

                },
                columns1: [
                    {
                        title: '服务项目',
                        key: 'desc'
                    },
                    {
                        title: '手续费单价',
                        key: 'gold'
                    },
                    {
                        title: '数量',
                        key: 'num'
                    },
                    {
                        title: '所需金币',
                        key: 'allgold'
                    }
                ],
                data1: {!! \Auth::user()->getEpageTable() !!},
                qiniutoken: {},
                token: {_token: '{{csrf_token()}}'},
                list: {!! $list->keyBy('id') !!},

                @if(request()->path() == 'mycfrlist')
                asin: "{{$asin}}",
                type: "{{$type}}",
                typec: {!! json_encode(config('linepro.cfr_typec')) !!},
                site: "{{$site}}",
                sitec: {!! json_encode(config('linepro.cfr_sitec')) !!},
                @endif

                imgUrl: '',
                visible: false,
                uploadList: [],
                vuploadList: [],

                modal1: false,

                formValidate: {
                    id: 0,
                    star: 0,
                    title: '',
                    content: '',
                    epic: [],
                    evideo: '',
                },

                defaultList: [],

                ruleValidate: {
                    title: [
                        {required: true, message: '标题不能为空', trigger: 'blur'},
                    ],
                    content: [
                        {required: true, message: '请输入评价正文', trigger: 'blur'},
                    ],
                }
            },
            watch:{

            },
            computed:{
                egold: function(){
                    if(this.formValidate.title != ''){
                        this.data1[0].num = 1 - this.consume.enum;
                    }else{
                        this.data1[0].num = 0;
                    }
                    this.data1[0].allgold = this.data1[0].gold * this.data1[0].num;
                    return this.data1[0].allgold;
                },
                epicgold: function(){
                    if(this.data1[1] == undefined){
                        return 0;
                    }
                    if(this.formValidate.epic.length > this.consume.epicnum){
                        this.data1[1].num = this.formValidate.epic.length - this.consume.epicnum;
                    }else{
                        this.data1[1].num = 0;
                    }
                    this.data1[1].allgold = this.data1[1].gold * this.data1[1].num;
                    return this.data1[1].allgold;
                },
                evideogold: function(){
                    if(this.data1[2] == undefined){
                        return 0;
                    }
                    if(this.formValidate.evideo.length > this.consume.evideonum){
                        this.data1[2].num = this.formValidate.evideo.length - this.consume.evideonum;
                    }else{
                        this.data1[2].num = 0;
                    }
                    this.data1[2].allgold = this.data1[2].gold * this.data1[2].num;
                    return this.data1[2].allgold;
                },
                allgold: function(){
                    return this.egold + this.epicgold + this.evideogold;
                }
            },
            methods: {
                handleError(error, file) {
                    this.$Notice.warning({
                        title: '上传错误',
                        desc: error
                    });
                },
                handleView(url) {
                    this.imgUrl = url;
                    this.visible = true;
                },
                vhandleRemove(file) {
                    var index = -1;
                    this.formValidate.evideo.forEach((v,k)=>{
                        if(v.url == file.url){
                            index = k;
                        }
                    });
                    this.formValidate.evideo.splice(index,1);
                },
                vremoveRepeat(file){
                    const fileList = this.$refs.vupload.fileList;
                    this.$refs.vupload.fileList.splice(fileList.indexOf(file), 1);
                },
                handleRemove(file) {
                    // 从 upload 实例删除数据
                    const fileList = this.$refs.upload.fileList;
                    this.$refs.upload.fileList.splice(fileList.indexOf(file), 1);

                    var index = -1;
                    this.formValidate.epic.forEach((v,k)=>{
                        if(v.url == file.url){
                            index = k;
                        }
                    });
                    this.formValidate.epic.splice(index,1);
                },
                handleSuccess(res, file) {
                    if (res.code) {
                        file.url  = res.data;
                        this.formValidate.epic.push({name: file.name,url:file.url});
                    } else {
                        this.$Notice.warning({
                            title: '上传错误',
                            desc: res.msg
                        });
                        this.handleRemove(file)
                    }
                },
                vhandleSuccess(res, file) {
                    file.url = "{{gconfig('qiniu.domain')}}" + res.key;
                    var isrepeat = false;
                    this.formValidate.evideo.forEach((v)=>{
                        if(v.url == file.url){
                            isrepeat = true;
                        }
                    });
                    if(isrepeat){
                        this.vremoveRepeat(file);
                        this.$Notice.warning({
                            title: '视频重复',
                            desc: ''
                        });
                    }else{
                        this.formValidate.evideo.push({name: file.name,url:file.url});
                    }
                },
                handleFormatError(file) {
                    this.$Notice.warning({
                        title: '文件格式不正确',
                        desc: '文件 ' + file.name + ' 格式不正确，请上传 jpeg/jpg 格式的图片。'
                    });
                },
                vhandleFormatError(file) {
                    this.$Notice.warning({
                        title: '文件格式不正确',
                        desc: '文件 ' + file.name + ' 格式不正确，请上传 mp4 格式的视频。'
                    });
                },
                handleMaxSize(file) {
                    this.$Notice.warning({
                        title: '超出文件大小限制',
                        desc: '文件 ' + file.name + ' 太大，不能超过 2M。'
                    });
                },
                vhandleMaxSize(file) {
                    this.$Notice.warning({
                        title: '超出文件大小限制',
                        desc: '文件 ' + file.name + ' 太大，不能超过 50M。'
                    });
                },
                handleBeforeUpload() {
                    @if(\Auth::user()->checkAction('euploadpic'))
                    const check = this.uploadList.length < 5;
                    if (!check) {
                        this.$Notice.warning({
                            title: '最多只能上传 5 张图片。'
                        });
                    }
                    return check;
                    @else
                    this.$Notice.error({
                        title: '请升级会员，暂无权限'
                    });
                    return false;
                    @endif
                },
                vhandleBeforeUpload() {
                    @if(\Auth::user()->checkAction('euploadvideo'))
                    const check = this.vuploadList.length < 1;
                    if (!check) {
                        this.$Notice.warning({
                            title: '最多只能上传 1 个视频。'
                        });
                    }
                    return check;
                    @else
                    this.$Notice.error({
                        title: '请升级会员，暂无权限'
                    });
                    return false;
                    @endif
                },
                handleSubmit(name) {
                    this.$refs[name].validate((valid) => {
                        if (valid) {
                            axios.post("{{url('evaluate')}}", app.formValidate).then(res => {
                                if (res.data.code) {
                                    window.location.reload()
                                } else {
                                    this.$Notice.error({
                                        title: '评价错误',
                                        desc: res.data.msg
                                    });
                                }
                            })
                        }
                    })
                },
                initForm(name, id) {
                    this.$refs[name].resetFields();
                    this.modal1 = true;
                    this.formValidate.id = this.list[id].id;
                    this.formValidate.title = this.list[id].title;
                    this.formValidate.star = this.list[id].star;
                    this.formValidate.epic = JSON.parse(this.list[id].epic);
                    this.formValidate.evideo = JSON.parse(this.list[id].evideo);
                    this.formValidate.content = this.list[id].content;
                    this.syncUpList(this.formValidate.epic, this.formValidate.evideo);

                    if(this.formValidate.title == ''){
                        this.consume.enum = 0;
                    }else{
                        this.consume.enum = 1;
                    }
                    this.consume.epicnum = this.list[id].epicnum;
                    this.consume.evideonum = this.list[id].evideonum;
                },
                syncUpList(epic,evideo) {
                    const upload = this.$refs.upload;
                    const vupload = this.$refs.vupload;
                    upload.fileList = [];
                    vupload.fileList = [];
                    epic.map(item => {
                        const one = {};
                        one.name = item.name;
                        one.url = item.url;
                        one.status = 'finished';
                        one.percentage = 100;
                        one.uid = Date.now() + upload.tempIndex++;
                        upload.fileList.push(one);
                    });
                    evideo.map(item => {
                        const one = {};
                        one.name = item.name;
                        one.url = item.url;
                        one.status = 'finished';
                        one.percentage = 100;
                        one.uid = Date.now() + vupload.tempIndex++;
                        vupload.fileList.push(one)
                    });
                    this.uploadList  = upload.fileList;
                    this.vuploadList = vupload.fileList;
                },
            },
            mounted: function () {
                this.$nextTick(()=>{
                    axios.get("{{url('getuptoken')}}").then(res => {
                        this.qiniutoken = {
                            token:res.data.data
                        };
                    })
                })
            },
        });
    </script>
@endsection
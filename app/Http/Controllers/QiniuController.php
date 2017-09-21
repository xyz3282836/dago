<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/9/19
 * Time: 上午9:40
 */

namespace App\Http\Controllers;


use Qiniu\Auth;

class QiniuController extends Controller
{

    public $auth;
    public $bucket;
    public function __construct()
    {
        $this->middleware('auth');

        $accessKey = gconfig('qiniu.ak');
        $secretKey = gconfig('qiniu.sk');

        $this->auth = new Auth($accessKey, $secretKey);
        $this->bucket = gconfig('qiniu.bucket');
    }

    public function getToken(){
        return success($this->auth->uploadToken($this->bucket));
    }

}
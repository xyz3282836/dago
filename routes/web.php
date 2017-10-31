<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//cf
Route::get('addTask', 'CfController@getAddClickFarm');
Route::post('addclickfarm', 'CfController@postAddClickFarm');
Route::get('card', 'CfController@listCardClickFarm');
Route::post('canclecf', 'CfController@postCancle');
Route::get('viewclickfarm/{id}', 'CfController@listCfResult');
Route::get('mycfrlist', 'CfController@listMycfr');
Route::get('orderlist', 'CfController@listOrder');

//auth
Route::post('evaluate', 'CfController@evaluate');

//获取环境信息
Route::get('getinfo', 'IndexController@getInfo');


//my
Route::get('uppwd', 'HomeController@getUpPwd');
Route::get('my', 'HomeController@getMy');
Route::post('uppwd', 'HomeController@postUpPwd');
Route::get('addr', 'HomeController@getAddr');
Route::post('addr', 'HomeController@postAddr');
Route::get('viplist', 'HomeController@listVip');

//recharge 充值
Route::get('recharge', 'PayController@getRecharge');
Route::post('recharge/pay', 'PayController@recharge');
Route::any('recharge/result', 'PayController@result');
Route::get('rechargelist', 'PayController@listRecharge');

//pay 支付产品
Route::post('pay', 'PayController@postPay');
Route::get('jumppay', 'PayController@jumpAlipay');
Route::post('delorder', 'PayController@delOrder');
Route::post('cancelorder', 'PayController@cancelOrder');

//资金流水
Route::get('billlist', 'PayController@listBill');
Route::get('getbilldesc', 'PayController@billDesc');

//upfile
Route::post('upload', 'HomeController@upload');
Route::get('upnotice', 'HomeController@updateNotice');


//captcha
Route::get('refereshcapcha', 'IndexController@captcha');

//faq
Route::get('faqs', 'IndexController@faqs');

//qiniu
Route::get('getuptoken', 'QiniuController@getToken');

//current.version
Route::get('current.version', function () {
    return 1;
});

//zan
Route::get('promotionlist', 'PromotionController@list');
Route::get('promotion/add', 'PromotionController@add');
Route::post('checkpromotionurl', 'HomeController@checkPromotionUrl');
Route::post('addpromotion', 'PromotionController@postAdd');

//wishlist
Route::get('wishlist', 'WishListController@list');
Route::get('wish/add', 'WishListController@add');
Route::post('addwish', 'WishListController@postAdd');

//qa
Route::get('qalist', 'QaController@list');
Route::get('qa/add', 'QaController@add');
Route::post('addqa', 'QaController@postAdd');
<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('system', 'HomeController@system');

    $router->resource('config','ConfigController');

    $router->resource('faq','FaqController');

    $router->resource('banner','BannerController');

    $router->resource('user','UserController');

    $router->resource('order','OrderController');

    $router->resource('rate','RateController');

    //上传图片(富文本编辑器需要使用)
    $router->post('upload', 'FileController@upload');
});

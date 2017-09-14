<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
    public function system()
    {
        return Admin::content(function (Content $content) {

            $content->header('Dashboard');
            $content->description('Description...');

            $content->row(Dashboard::title());

            $content->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
        });
    }

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Dashboard');
            $content->description('Description...');
            $user_count     = User::count();
            $vip_count      = User::where('level', 2)->count();
            $order_count    = Order::where('type', Order::TYPE_CONSUME)->count();
            $recharge_count = Order::where('type', Order::TYPE_RECHARGE)->count();
            $content->row(function ($row) use ($user_count, $vip_count, $order_count, $recharge_count) {
                $row->column(3, new InfoBox('用户', 'users', 'aqua', admin_url('user?&type=all'), $user_count));
                $row->column(3, new InfoBox('会员', 'user', 'green', admin_url('user?&type=2'), $vip_count));
                $row->column(3, new InfoBox('消费订单', 'shopping-cart', 'yellow', admin_url('order?&type=2'), $order_count));
                $row->column(3, new InfoBox('充值订单', 'dollar', 'red', admin_url('order?&type=1'), $recharge_count));
            });

        });
    }
}

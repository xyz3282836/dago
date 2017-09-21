<?php

namespace App\Admin\Controllers;


use App\Action;
use App\Http\Controllers\Controller;
use App\Role;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class RoleController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Role::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('标识');
            $grid->desc('前台显示')->editable();

            $grid->action('功能')->pluck('id')->implode(',')->checkbox(Action::all()->pluck('desc', 'id'), [1, 2]);

            $grid->once('单次充值(￥)')->editable();
            $grid->more('累加充值(￥)')->editable();

            $grid->type('类型')->radio([
                1 => '单次充值',
                2 => '累加充值',
            ]);

//            $grid->service_one_rate('限时')->editable();
//            $grid->service_three_rate('普通')->editable();
//            $grid->service_one_min('限时金币')->editable();
//            $grid->service_three_min('普通金币')->editable();
//            $grid->weight('权重')->editable();

            $grid->gold_recharge('最少充值金币(￥)')->editable();
            $grid->gold_step('金币充值步长(￥)')->editable();
            $grid->balance_recharge('最少充值余额(￥)')->editable();
            $grid->balance_step('余额充值步长(￥)')->editable();

        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Role::class, function (Form $form) {

            $form->tab('角色信息', function ($form) {
                $form->text('name', '标识')->rules('required');
                $form->text('desc', '前台显示')->rules('required');

//              $form->multipleSelect('action', '功能')->options(Action::all()->pluck('desc', 'id'))->rules('required');
                if(request()->method() == 'PUT'){
                    $form->checkbox('action', '功能')->options(Action::all()->pluck('desc', 'id'));
                }

                $form->number('once', '单次充值(￥)')->rules('required');
                $form->number('more', '累加充值(￥)')->rules('required');
                $form->radio('type', '类型')->options(['1' => '单次充值', '2' => '累加充值'])->default('1')->rules('required');

                $form->text('service_one_rate', '限时下单服务费率')->rules('required');
                $form->text('service_three_rate', '普通下单服务费率')->rules('required');
                $form->number('service_one_min', '限时下单最少金币')->rules('required');
                $form->number('service_three_min', '普通下单最少金币')->rules('required');
                $form->number('weight', '权重(￥)')->rules('required');

                $form->number('gold_recharge', '最少充值金币(￥)')->rules('required');
                $form->number('gold_step', '金币充值步长(￥)')->rules('required');
                $form->number('balance_recharge', '最少充值余额(￥)')->rules('required');
                $form->number('balance_step', '余额充值步长(￥)')->rules('required');

                $form->display('created_at', 'Created At');
                $form->display('updated_at', 'Updated At');
            })->tab('功能信息列表', function ($form) {
                $form->hasMany('actiondesc', '', function (Form\NestedForm $form) {
                    $form->radio('aid', '功能')->options(Action::all()->pluck('desc', 'id'))->rules('required');
                    $form->number('service_gold', '指定金币')->rules('required');
                    $form->text('service_rate', '百分比')->rules('required');
                    $form->radio('type', '类型')->options(['1' => '指定金币', '2' => '百分比'])->default('1')->rules('required');
                });

            });
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }
}

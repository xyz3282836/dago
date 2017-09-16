<?php

namespace App\Admin\Controllers;

use App\RoleAction;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RoleActionController extends Controller
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

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(RoleAction::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->role()->desc('会员')->label();
            $grid->action()->desc('功能')->label();
            $grid->service_gold('指定金币')->editable();
            $grid->service_rate('百分比')->editable();
            $grid->type('类型')->radio([
                1 => '指定金币',
                2 => '百分比',
            ]);
            $grid->updated_at('更新');

            $grid->disableCreation();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(RoleAction::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->display('role.desc', '角色');
            $form->display('action.desc', '功能');
            $form->number('service_gold', '指定金币')->rules('required');
            $form->text('service_rate', '百分比')->rules('required');
            $form->radio('type', '类型')->options(['1' => '指定金币', '2' => '百分比'])->default('1')->rules('required');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2017/6/22
 * Time: 下午3:49
 */

namespace App\Admin\Controllers;


use App\Faq;
use App\Http\Controllers\Controller;
use Cache;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class FaqController extends Controller
{

    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('header');
            $content->description('description');
            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(Faq::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->order('排序权重')->editable()->sortable();
            $grid->q('问题')->editable();
            $grid->a('答案');
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('header');
            $content->description('description');
            $content->body($this->form()->edit($id));
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('header');
            $content->description('description');
            $content->body($this->form());
        });
    }

    protected function form()
    {
        return Admin::form(Faq::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('order', '排序权重')->rules('required|integer');
            $form->text('q', '问题')->rules('required|min:2');
            $form->weditor('a', '回答')->rules('required');
        });
    }

    public function store()
    {
        Cache::forget('faqs');
        return $this->form()->store();
    }

    public function update($id)
    {
        Cache::forget('faqs');
        return $this->form()->update($id);
    }

    public function destroy($id)
    {
        if ($this->form()->destroy($id)) {
            Cache::forget('faqs');
            return response()->json([
                'status'  => true,
                'message' => trans('admin::lang.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('admin::lang.delete_failed'),
            ]);
        }
    }
}
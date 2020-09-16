<?php

namespace App\Admin\Controllers;

use App\Models\Post;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PostController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Post';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Post());

        $grid->column('id', __('Id'));

//        $grid->column('author_id', __('Author Id'));
        $grid->column('author.name', __('Author'));

        $grid->column('title', __('Title'));
        $grid->column('anons', __('Anons'));
        $grid->column('content', __('Content'));
        $grid->column('publish', __('Publish'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->column('comments', 'Comments count')->display(function ($comments) {
            $count = count($comments);
            return "<span class='label label-warning'>{$count}</span>";
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Post::findOrFail($id));

        $show->field('id', __('Id'));
        //$show->field('author_id', __('Author id'));
        $show->field('author.name', __('Author'));
        $show->field('title', __('Title'));
        $show->field('anons', __('Anons'));
        $show->field('content', __('Content'));
        $show->field('publish', __('Publish'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Post());

        //$form->number('author_id', __('Author Id'));
        $adminUsers = Administrator::select('id','name')->get()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });
        $form->select('author_id', __('Author'))->options($adminUsers)->required();
        $form->text('title', __('Title'))->required();
        $form->text('anons', __('Anons'))->required()->help('help...');
        $form->textarea('content', __('Content'))->required();
        $form->switch('publish', __('Publish'));

        return $form;
    }
}

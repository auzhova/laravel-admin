<?php

namespace App\Admin\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CommentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Comment';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Comment());

        $grid->column('id', __('Id'));
        //$grid->column('user_id', __('User id'));
        $grid->column('user.name', __('User'));
        //$grid->column('post_id', __('Post id'));
        $grid->column('post.title', __('Post'));
        $grid->column('parent_id', __('Parent id'));
        $grid->column('text', __('Text'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Comment::findOrFail($id));

        $show->field('id', __('Id'));
//        $show->field('user_id', __('User id'));
        $show->field('user.name', __('User'));
//        $show->field('post_id', __('Post id'));
        $show->field('post.title', __('Post'));
        $show->field('parent_id', __('Parent id'));
        $show->field('text', __('Text'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Comment());

//        $form->number('user_id', __('User id'));
        $adminUsers = Administrator::select('id','name')->get()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });
        $form->select('user_id', __('User'))->options($adminUsers)->required();
//        $form->number('post_id', __('Post id'));
        $posts = Post::select('id','title')->get()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['title']];
        });
        $form->select('post_id', __('Post'))->options($posts)->required();
        //$form->number('parent_id', __('Parent id'));
        $comments = Comment::select('id','text')->get()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['text']];
        });
        $form->select('parent_id', __('Parent comment'))->options($comments);
        $form->textarea('text', __('Text'))->required();
        $form->switch('status', __('Status'));

        return $form;
    }
}

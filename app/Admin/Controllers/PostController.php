<?php

namespace App\Admin\Controllers;

use App\Models\Comment;
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

    private $files = null;

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

        $form->file('files->doc1', 'Документ 1')->removable()->hidePreview();
        $form->file('files->doc2', 'Документ 2')->removable()->hidePreview();

        // Subtable fields
        $allComments = Comment::select('id','text')->get()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['text']];
        });
        $form->hasMany('comments', function (Form\NestedForm $form) use($adminUsers, $allComments) {
            $form->select('user_id', __('User'))->options($adminUsers);
            $form->select('parent_id', __('Parent comment'))->options($allComments);
            $form->text('text', __('Text'));
            $form->switch('status', __('Status'));
        });

        $form->saving(function (Form $form) {
            $this->files = $form->model()->files;
        });

        $form->saved(function (Form $form) {
            if($this->files && $this->files != $form->model()->files){
                $form->model()->update(['files' => array_merge($this->files, $form->model()->files)]);
            }
        });

        return $form;
    }
}

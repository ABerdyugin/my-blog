<?php


namespace app\controllers;


use app\models\Comment;
use app\views\CommentView;
use core\Controller;

class CommentController extends Controller
{
	/**
	 * @var Comment $model
	 */
	protected $model;
	/**
	 * @var CommentView $view
	 */
	protected $view;

	public function __construct()
	{
		$this->model = new Comment();
		$this->view = new CommentView();
	}

	public function getCountForPost($postId)
	{
		$clause = ["blog_id" => $postId];
		return $this->model->getCountForPost($clause);
	}

	public function getCommentsByPost($postId)
	{
		return $this->model->getData('blog_comments',"AND `blog_id`='$postId'");
	}

}
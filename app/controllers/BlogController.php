<?php


namespace app\controllers;


use app\models\Blog;
use app\views\BlogView;
use core\Controller;

class BlogController extends Controller
{
	/**
	 * @var Blog
	 */
	private $model;
	/**
	 * @var BlogView
	 */
	private $view;
	/**
	 * @var CommentController $comments
	 */
	private $comments;

	protected $title = "Blog Page";


	public function __construct()
	{
		$this->model = new Blog();
		$this->view = new BlogView();
		$this->comments = new CommentController();
	}


	public function actionIndex($routes)
	{

		$data = array(
			'page-header' => 'Блог'
		);
		$pageContent = "";
		$content = $this->model->getData('blog', "", "LIMIT 0,5");
		foreach ($content as $id => $entry) {
			$params = array(
				'item-link' => '/blog/' . $entry['id'],
				'item-title' => $entry['title'],
				'item-cut-content' => $entry['cutcontent'],
				'item-created' => $this->formatCreated($entry['dateadd']),
				'item-author' => $entry['user_id'], //TODO поменять на выборку имени автора поста
				'item-views' => $this->pluralViews($entry['views']),
				'item-comments' => $this->pluralComments($this->comments->getCountForPost($entry['id'])),
				'item-poster' => $entry['poster'],
			);
			$pageContent .= $this->view->buildPartial("list-item", $params);
		}
		$data['page-content'] = $pageContent;

		$this->view->buildLayout(false, $data);
	}

	/**
	 * @param array|null $routes
	 */
	public function actionShow($routes)
	{
		$data = array(
			'page-header' => 'Блог'
		);

		$pageContent = "";
		$id = array_shift($routes);
		$post = $this->model->getItem('blog', $id);
		$params = array(
			'post-poster' => $post['poster'],
			'post-title' => $post['title'],
			'post-cut-content' => $post['cutcontent'],
			'post-content' => $post['content'],
			'post-author' => $post['user_id'], //TODO поменять на выборку имени автора поста
			'post-comments' => $this->pluralComments($this->comments->getCountForPost($post['id'])),
			'post-views' => $this->pluralViews($post['views']),
			'post-created' => $this->formatCreated($post['dateadd']),
			'post-comment-list' => $this->getCommentList($post['id'])
		);
		$pageContent .= $this->view->buildPartial("item", $params);
		$data['page-content'] = $pageContent;
		$this->view->buildLayout(false, $data);
	}

	protected function getCommentList($postId)
	{
		$data = array();
		$commentData = $this->comments->getCommentsByPost($postId);
		foreach ($commentData as $comment){
			$data[] = array(
				"comment-author" => $comment['user_id'], //TODO поменять на выборку имени автора комментария
				"comment-content" => $comment['content']
			);
		}
		return $data;
	}

	/**
	 * @param string $date
	 * @return false|string
	 */
	private function formatCreated($date)
	{
		return date('d.m.Y в H:i', strtotime($date));
	}

	/**
	 * @param int $n
	 * @return string
	 */
	private function pluralComments($n)
	{
		if (!$n) {
			return "Комментариев нет";
		}
		$forms = ['комментарий', 'комментария', 'коментариев'];
		return parent::pluralForm($n, $forms);
	}

	/**
	 * @param int $n
	 * @return string
	 */
	private function pluralViews($n)
	{
		if (!$n) {
			return "Просмотров нет";
		}
		$forms = ['просмотр', 'просмотра', 'просмотров'];
		return parent::pluralForm($n, $forms);
	}
}
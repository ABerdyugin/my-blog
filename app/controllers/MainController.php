<?php


namespace app\controllers;


use app\views\MainView;
use core\Controller;

class MainController extends Controller
{

	/**
	 * @var MainView
	 */
	private $view;

	private $suTitle = "Главная страница";

	public function __construct()
	{
		$this->view = new MainView();
		$this->title .= " - " . $this->suTitle;
	}

	public function actionIndex($routes)
	{
		$data = array(
			"site-title" => $this->title,
			'page-header' => $this->suTitle,
			"page-content" => $this->view->buildPartial("content")
		);
		$this->view->buildLayout(false, $data);
	}


	public function actionLogin($routes){
echo $this;
	}
}
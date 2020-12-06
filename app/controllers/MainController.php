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

	public function __construct()
	{
		$this->view = new MainView();
	}

	public function actionIndex($routes)
	{
		$data = array();
		$this->view->buildLayout(false, $data);
	}
}
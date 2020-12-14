<?php


namespace app\controllers;


use app\models\User;
use app\views\UserView;
use core\Controller;

class UserController extends Controller
{
	/**
	 * @var User $model
	 */
	private $model;
	/**
	 * @var UserView $view
	 */
	private $view;

	private $subTitle = "";


	public function __construct()
	{
		$this->model = new User();
		$this->view = new UserView();
	}

	public function actionLogin($routes)
	{
		$data = array(
			"site-title" => $this->title,
			'page-header' => $this->subTitle,
		);
		$login = $this->isLogged();
		if ($login['result']) {

			header("Location: /");
			die();
		} else {
			$params = $login;

			$this->subTitle = "Авторизация";
			$this->title .= " - " . $this->subTitle;

			$data["page-content"] = $this->view->buildPartial("login", $params);
		}
		$this->view->buildLayout(false, $data);
	}

	public function actionLogout($routes)
	{
		session_destroy();
		header("Location: /");
		die();
	}

	public function getName($userId)
	{
		return $this->model->getUserName($userId);
	}

	/**
	 * @return true|array
	 */
	protected function isLogged()
	{
		$result = array();
		if ($_SESSION['isLogged'] !== true) {
			$isName = isset($_POST['login']) && trim($_POST['login']) != "" ? 1 : 0;
			$isPass = isset($_POST['paswd']) && trim($_POST['paswd']) != "" ? 2 : 0;
			$fill = $isPass | $isName;

			if ($fill == 3) {
				$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
				$password = filter_input(INPUT_POST, 'paswd', FILTER_SANITIZE_STRING);

				$isAuth = $this->model->getUser($login, $password);
				if ($isAuth !== false) {
					$_SESSION['isLogged'] = true;
					$_SESSION['user_id'] = $isAuth['id'];
					$_SESSION['login'] = $isAuth['login'];
					$_SESSION['password'] = $isAuth['password'];
					$_SESSION['isAdmin'] = $isAuth['isAdmin'];
				} else {
					$result = array(
						'result' => false,
						"error" => true,
						"error-type" => "mismatch"
					);

					return $result;
				}
				$result['result'] = true;

				return $result;
			} else {
				$result['result'] = false;
				if (isset($_POST['login']) && isset($_POST['paswd'])) {
					$result['error'] = true;
					switch ($fill) {
						case 0:
							$result['error-type'] = "empty-all";
							break;
						case 1:
							$result['error-type'] = "empty-password";
							break;
						case 2:
							$result['error-type'] = "empty-login";
							break;
					}
				}
			}

			return $result;
		} else {
			$result['result'] = true;

			return $result;
		}
	}

    public static function isAdminNow()
    {
        return $_SESSION['isAdmin'];
	}


}
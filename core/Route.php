<?php

//use app\controllers;

class Route
{
	public static $CONTROLLER_NAME = "Main";

	public function __construct()
	{
	}

	public function start()
	{
		$controllerName = Route::$CONTROLLER_NAME;
		$actionName = "index";


		$routes = explode('/', strpos($_SERVER['REQUEST_URI'], '?') > 0 ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) : $_SERVER['REQUEST_URI']);
		array_shift($routes);
		if (!empty($routes[0])) {
			$controllerName = array_shift($routes);
		}
		switch ($controllerName) {
			case "login":
			case "logout":
			array_unshift($routes, $controllerName);
			$controllerName = "user";
				break;
		}
		$controllerClass = 'app\\controllers\\' . ucfirst($controllerName) . 'Controller';
		if (!class_exists($controllerClass)) {
			$controllerClass = 'app\\controllers\\' . Route::$CONTROLLER_NAME . 'Controller';
			array_unshift($routes, $controllerName);
		}
		$controller = new $controllerClass;

		if (!empty($routes[0])) {
			$actionName = array_shift($routes);
			if (is_numeric($actionName)) {
				array_push($routes, $actionName);
				$actionName = array_shift($routes);
			}
		}

		$actionMethod = 'action' . ucfirst($actionName);
		if (!method_exists($controller, $actionMethod)) {
			if (is_numeric($actionName)) {
				$actionMethod = "actionShow";
				array_push($routes, $actionName);
			} else {
				header("HTTP/1.0 404 Not Found");
				die('Error. 404 Not Found. ');
			}
		}
		$controller->$actionMethod($routes);

	}

	public static function init()
	{
		spl_autoload_register(array('static', 'autoLoad'));
	}

	public static function autoLoad($className)
	{
		$classFile = false;
		$className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
		$classFile = ROOT_PATH . DIRECTORY_SEPARATOR . $className . '.php';
		if (!file_exists($classFile)) {
			$classArray = explode(DIRECTORY_SEPARATOR, $classFile);
			array_pop($classArray);
			array_push($classArray, Route::$CONTROLLER_NAME . "Controller");
			$classFile = implode(DIRECTORY_SEPARATOR, $classArray) . ".php";
		}
		require_once $classFile;
	}


}
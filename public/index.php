<?php
session_start();
define('ROOT_PATH', dirname(dirname(__FILE__)));

require_once ROOT_PATH . '/core/Route.php';

Route::init();
$app = new Route();
$app->start();

<?php


namespace app\views;


use core\View;

class MainView extends View
{

	public function buildPartial($chunk, $params = null)
	{
		$chunk = "main/" . $chunk;
		return parent::buildPartial($chunk, $params);
	}
}
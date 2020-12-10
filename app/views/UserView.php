<?php


namespace app\views;


use core\View;


class UserView extends View
{


	public function buildPartial($chunk, $params = null)
	{
		$chunk = 'user/' . $chunk;
		return parent::buildPartial($chunk, $params);
	}



}
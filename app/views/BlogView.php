<?php


namespace app\views;


use core\View;

class BlogView extends View
{

	public function buildPartial($chunk, $params = null)
	{
		$chunk = 'blog/' . $chunk;
		return parent::buildPartial($chunk, $params);
	}
}
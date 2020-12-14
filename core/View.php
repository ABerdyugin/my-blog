<?php

namespace core;

class View
{
	public $layoutName = 'layout';

	public function buildLayout($layout = false, $params = null)
	{
		include ROOT_PATH . "/app/views/" . ($layout != false ? $layout : $this->layoutName) . ".php";

	}

	public function buildPartial($chunk, $params = null)
	{
		ob_start();
		include ROOT_PATH . "/app/views/" . $chunk . ".php";

		$output = ob_get_contents();
		ob_end_clean();
		return $output;

	}

	public static function chunk($chunk, $params = null)
	{
		include ROOT_PATH . "/app/views/" . str_replace('.', '/', $chunk) . ".php";
	}
}
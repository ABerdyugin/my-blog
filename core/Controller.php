<?php

namespace core;

class Controller
{

	/**
	 * @var string
	 */
	protected $mainContent = '';
	protected $title = "Site Template";


	/**
	 * @param bool $clear
	 * @return string
	 */
	public function getMainContent($clear = false)
	{
		$tmp = $this->mainContent;
		if ($clear) $this->setMainContent('');
		return $tmp;
	}

	/**
	 * @param string $mainContent
	 * @param bool $add
	 */
	public function setMainContent($mainContent, $add = false)
	{
		if ($add) {
			$this->mainContent .= $mainContent;
		} else {
			$this->mainContent = $mainContent;
		}
	}

	/**
	 * @param int $n
	 * @param array $forms
	 * @param bool $k
	 * @return string
	 */
	public static function pluralForm($n, $forms, $k = true)
	{
		return ($k ? $n . ' ' : '') . ($n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]));
	}
}
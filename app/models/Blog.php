<?php


namespace app\models;


use core\Model;

class Blog extends Model
{
	protected $sort = '';

	public function getData($model, $where = '', $limit = ''): array
	{
		$this->sort = 'ORDER BY `dateadd` DESC';
		return parent::getData($model, $where, $limit);
	}
}
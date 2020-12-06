<?php


namespace app\models;


use core\Model;

class Comment extends Model
{
	protected $modelName = 'blog_comments';

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @param array $clause
	 * @return int
	 */
	public function getCountForPost($clause)
	{
		$clauseArray = array('');
		foreach ($clause as $key => $value) {
			array_push($clauseArray, sprintf(" `%s`='%s' ", $key, $value));
		}
		$where = implode("AND", $clauseArray);
		$model = $this->modelName;
		return $this->getCount($model, $where);
	}

}
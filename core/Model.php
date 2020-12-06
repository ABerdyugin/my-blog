<?php

namespace core;

use PDO;
use PDOException;

class Model
{
	/**
	 * @var PDO;
	 */
	public $link;
	private $host = 'localhost';
	private $database = 'bench';
	private $user = 'root';
	private $password = 'mysql';

	protected $sort = ' ';


	public function __construct()
	{
		$dsn = sprintf('mysql:dbname=%s;host=%s', $this->database, $this->host);
		$user = $this->user;
		$password = $this->password;
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		);
		$dbh = null;
		try {
			$dbh = new PDO($dsn, $user, $password, $options);
		} catch (PDOException $e) {
			echo 'Подключение не удалось: ' . $e->getMessage();
		}
		$this->link = $dbh;
	}

	/**
	 * @param $model
	 * @param string $where
	 * @param string $limit
	 * @return array
	 */
	public function getData($model, $where = '', $limit = '')
	{
		$query = "SELECT * FROM `{$model}` WHERE 1 {$where} {$this->sort} {$limit}";
		$sth = $this->link->query($query);

		if ($sth) {
			return $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		return array();
	}

	/**
	 * @param $model
	 * @param string $where
	 * @param string $limit
	 * @return int
	 */
	public function getCount($model, $where = '', $limit = '')
	{
		$query = "SELECT * FROM `{$model}` WHERE 1 {$where} {$limit}";
		$sth = $this->link->query($query);
		return $sth->rowCount();
	}

	/**
	 * @param $model
	 * @param $id
	 * @return mixed
	 */
	public function getItem($model, $id)
	{
		$query = "SELECT * FROM `{$model}` WHERE `id`={$id}";
		$sth = $this->link->query($query, PDO::FETCH_ASSOC);
		return $sth->fetch();
	}

	/**
	 * @param $model
	 * @param $id
	 * @return false|int
	 */
	public function delete($model, $id)
	{
		$query = "DELETE FROM `{$model}` WHERE `id`='{$id}'";
		return $this->link->exec($query);
	}

	public function sortByField($field, $direction = 'ASC')
	{
		$this->sort = " ORDER BY `{$field}` {$direction} ";
	}

	public function insertData($model, $data)
	{

	}
}
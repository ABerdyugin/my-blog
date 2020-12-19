<?php

namespace core;

use mysql_xdevapi\Exception;
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
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
     * @param string $model
     * @param string $where
     * @return false|array
     */
    public function getRow($model, $where)
    {
        $query = "SELECT * FROM `{$model}` WHERE {$where}";
        $sth = $this->link->query($query);

        if ($sth) {
            return $sth->fetch(PDO::FETCH_ASSOC);
        }

    }

    /**
     * @param $model
     * @param string $where
     * @return int
     */
    public function getCount($model, $where = '')
    {
        $query = "SELECT COUNT(*) as `count` FROM `{$model}` WHERE 1 {$where}";
        $sth = $this->link->query($query);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
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
        try{
            $query = "DELETE FROM `{$model}` WHERE `id`='{$id}'";
            return $this->link->exec($query);
        }catch (PDOException $e){
            return false;
        }
    }

    public function sortByField($field, $direction = 'ASC')
    {
        $this->sort = " ORDER BY `{$field}` {$direction} ";
    }

    public function insertData($model, $data)
    {
        $keys = array_keys($data);
        $keys = array_map(array($this, 'escape_mysql_identifier'), $keys);
        $fields = implode(",", $keys);
        $table = $this->escape_mysql_identifier($model);
        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $this->link->prepare($sql)->execute(array_values($data));
        return $this->link->lastInsertId();
    }

    function escape_mysql_identifier($field)
    {
        return "`" . $field . "`";
    }

    /**
     * @param string $model
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($model, $id, $data)
    {
        $dataArray = array();
        foreach ($data as $key => $value) {
            $dataArray[] = sprintf("`%s`='%s'", $key, $value);
        }
        $dataString = implode(", ", $dataArray);
        $query = "UPDATE $model SET {$dataString} WHERE `id`='$id'";
        $sth = $this->link->prepare($query);
        return $sth->execute();
    }

}
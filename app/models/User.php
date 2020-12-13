<?php


namespace app\models;

use core\Model;

class User extends Model
{
    /**
     *
     */
    protected $modelName = "";

    public function __construct()
    {
        $this->modelName = "users";
        parent::__construct();
    }


    public function getUserName($userId)
    {
        $userData = $this->getItem($this->modelName, $userId);
        return $userData['login'];
    }

    public function isAdmin($userId)
    {
        $userData = $this->getItem($this->modelName, $userId);
        return ($userData['isAdmin'] == 1);
    }

    /**
     * @param $login
     * @param $password
     * @return array|false
     */
    public function getUser($login, $password)
    {
        $where = sprintf("`login`='%s' AND `password`='%s'", $login, md5($password));
        return $this->getRow($this->modelName, $where);

    }

    public static function logged()
    {
        return $_SESSION['isLogged'];
    }
}
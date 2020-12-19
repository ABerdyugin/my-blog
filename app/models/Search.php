<?php


namespace app\models;


use app\controllers\UserController;
use app\views\BlogView;

class Search extends Blog
{


    public function getResult($s, $limit = "LIMIT 0,5")
    {
        $where = $this->buildWhere($s);
        return $this->getData('blog', $where, $limit);
    }

    public function buildWhere($s)
    {
        return $where = " AND (`cutcontent` LIKE '%$s%' OR `content` LIKE '%$s%') ";
    }
}
<?php


namespace app\models;


use core\Model;
use PDO;

class Comment extends Model
{
    protected $modelName = 'blog_comments';

    protected $sort = ' ORDER BY `date_add` ASC ';

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

    /**
     * @param int $postId
     * @return array
     */
    public function getFullData($postId)
    {
        $query = "SELECT `bc`.`id`,`bc`.`content`,`u`.`login`,`u`.`id` as `user_id` FROM `blog_comments` AS `bc`  
            LEFT JOIN `users` AS `u` ON (`bc`.`user_id`=`u`.`id`)
            WHERE `bc`.`blog_id`='$postId' 
            ORDER BY `bc`.`date_add` ASC";
        $sth = $this->link->query($query);
        $result = array();

        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $result[] = array_merge($row, array(
                'show' => $_SESSION['isAdmin'] || $row['user_id'] == $_SESSION['user_id']
            ));
        }
        return $result;
        //return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

}

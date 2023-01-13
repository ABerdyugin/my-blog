<?php


namespace app\models;


use core\Model;

class Blog extends Model
{
    protected $modelName = "blog";
    protected $sort = '';


    public function getData($model, $where = '', $limit = '')
    {
        $this->sort = 'ORDER BY `date_add` DESC';
        return parent::getData($model, $where, $limit);
    }

    /**
     * @param integer $postId
     * @return false|integer
     */
    public function getAuthor($postId)
    {
        $post = $this->getItem($this->modelName, $postId);
        return $post !== false ? $post['user_id'] : false;
    }
}

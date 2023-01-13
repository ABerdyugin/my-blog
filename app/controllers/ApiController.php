<?php


namespace app\controllers;


use app\models\Comment;
use app\models\User;
use core\Controller;

class ApiController extends Controller
{
    /**
     * @var Comment
     */
    protected $model;

    public function __construct()
    {
        header("Content-Type: application/json");
    }

    public function actionComment($routes)
    {
        $this->model = new Comment();
        $act = array_shift($routes);
        $id = intval(array_shift($routes));
        switch ($act) {
            case 'add':
                echo $this->addComment($id);
                break;
            case 'delete':
                $commentId = intval(array_shift($routes));
                echo $this->deleteComment($id, $commentId);
                break;
            case 'list':
                echo $this->listComment($id);
                break;
            default:
                $result = ["error" => 'No input data'];
                echo json_encode($result);
                break;
        }
    }

    /**
     * @param int $postId
     * @return string
     */
    private function addComment($postId)
    {
        if (User::logged()) {
            $this->model->insertData('blog_comments', array(
                'blog_id' => $postId,
                'content' => filter_input(INPUT_POST, 'comment-content', FILTER_SANITIZE_STRING),
                'user_id' => $_SESSION['user_id'],
                'date_add' => date("Y-m-d H:i:s")
            ));
        }
        return $this->listComment($postId);
    }

    /**
     * @param int $postId
     * @param int $id
     * @return string
     */
    private function deleteComment($postId, $id)
    {
        $comment = $this->model->getItem('blog_comments', $id);
        if ($comment['blog_id'] == $postId &&
            User::logged() &&
            ($_SESSION['isAdmin'] || $_SESSION['user_id'] == $comment['user_id'])
        ) {
            $this->model->delete('blog_comments', $id);
        }
        return $this->listComment($postId);
    }

    /**
     * @param int $postId
     * @return string
     */
    private function listComment($postId)
    {
        $comments = $this->model->getFullData($postId);

        return json_encode($comments);
    }
}

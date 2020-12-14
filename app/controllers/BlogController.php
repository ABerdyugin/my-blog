<?php


namespace app\controllers;


use app\models\Blog;
use app\models\User;
use app\views\BlogView;
use core\Controller;

class BlogController extends Controller
{
    /**
     * @var Blog
     */
    private $model;
    /**
     * @var BlogView
     */
    private $view;
    /**
     * @var CommentController $comments
     */
    private $comments;
    /**
     * @var UserController $user
     */
    private $user;

    protected $subTitle = "Блог";


    public function __construct()
    {
        $this->model = new Blog();
        $this->view = new BlogView();
        $this->comments = new CommentController();
        $this->user = new UserController();
        $this->title .= " - " . $this->subTitle;
    }


    public function actionIndex($routes)
    {
        $_SESSION['blog-post-id'] = false;

        $data = array(
            'site-title' => $this->title,
            'page-header' => 'Блог'
        );
        $pageContent = "";
        $content = $this->model->getData('blog', "", "LIMIT 0,5");
        foreach ($content as $id => $entry) {
            $params = array(
                'item-link' => '/blog/' . $entry['id'],
                'item-title' => $entry['title'],
                'item-cut-content' => $entry['cutcontent'],
                'item-created' => $this->formatCreatedDate($entry['dateadd']),
                'item-author' => $this->user->getName($entry['user_id']),
                'item-views' => $this->pluralViews($entry['views']),
                'item-comments' => $this->pluralComments($this->comments->getCountForPost($entry['id'])),
                'item-poster' => $entry['poster'],
            );
            $pageContent .= $this->view->buildPartial("list-item", $params);
        }
        $data['page-content'] = $pageContent;

        $this->view->buildLayout(false, $data);
    }

    /**
     * @param array|null $routes
     */
    public function actionShow($routes)
    {
        $pageContent = "";
        $id = array_shift($routes);
        $_SESSION['blog-post-id'] = $id;
        $post = $this->model->getItem('blog', $id);
        $params = array(
            'post-id' => $post['id'],
            'post-poster' => $post['poster'],
            'post-title' => $post['title'],
            'post-cut-content' => $post['cutcontent'],
            'post-content' => $post['content'],
            'post-author' => $this->user->getName($post['user_id']),
            'post-comments' => $this->pluralComments($this->comments->getCountForPost($post['id'])),
            'post-views' => $this->pluralViews($post['views']),
            'post-created' => $this->formatCreatedDate($post['dateadd']),
            'post-comment-list' => $this->getCommentList($post['id'])
        );
        $pageContent .= $this->view->buildPartial("item", $params);
        $this->title .= " - " . $post['title'];
        $data = array(
            'post-id' => $post['id'],
            'site-title' => $this->title,
            'page-header' => 'Блог',
            'page-content' => $pageContent
        );
        $this->view->buildLayout(false, $data);
    }

    /**
     * @param array $routes
     */
    public function actionAdd($routes)
    {
        if (!User::logged()) {
            header("Location: /blog");
            die();
        }
        $params = array(
            'site-title' => $this->title . " - Добавление материала",
            'page-header' => 'Добавление материала',
            "action" => "insert",
            "route" => "/blog/add"
        );
        $act = filter_input(INPUT_POST, "act", FILTER_SANITIZE_STRING);
        if ($act != "insert") {
            $params['page-content'] = $this->view->buildPartial("form", $params);
            $this->view->buildLayout(false, $params);
        } else {
            $data = $this->getFormData();
            if ($this->checkRequired($data, $params)) {
                $postId = $this->model->insertData("blog", $data);
                $posterName = $this->loadPoster($postId);
                if ($posterName != false) {
                    $this->model->update("blog", $postId, array(
                        "poster" => $posterName
                    ));
                }
                header("Location: /blog/" . $postId);
                die();
            }
        }

    }

    /**
     * @param array $routes
     */
    public function actionEdit($routes)
    {
        $postId = array_shift($routes);

        $params = array(
            'site-title' => $this->title . " - Изменение материала",
            'page-header' => 'Изменение материала',
            "action" => "edit",
            "route" => "/blog/$postId/edit"
        );
        if ((User::logged() && BlogController::isAuthor()) || UserController::isAdminNow()) {
            $act = filter_input(INPUT_POST, "act", FILTER_SANITIZE_STRING);
            if ($act != "edit") {
                $post = $this->model->getItem("blog", $postId);
                $params = array_merge(array(
                    'post-id' => $post['id'],
                    'post-title' => $post['title'],
                    'post-poster' => $post['poster'],
                    'post-cutcontent' => $post['cutcontent'],
                    'post-content' => $post['content'],
                ), $params);
                $params['page-content'] = $this->view->buildPartial("form", $params);
                $this->view->buildLayout(false, $params);
            } else {
                $data = $this->getFormData();
                if ($this->checkRequired($data, $params)) {
                    $this->model->update("blog", $postId, $data);
                }
            }
        } else {
            header("Location: /blog/" . $postId);
            die();
        }
    }

    /**
     * @param array $routes
     */
    public function actionDelete($routes)
    {
        $postId = array_shift($routes);
        if ($this->model->delete("blog", $postId)) {
            header("Location: /blog/");
        } else {
            header("Location: /blog/" . $postId);
        }
    }

    /**
     * @param integer $postId
     * @return array
     */
    protected function getCommentList($postId)
    {
        $data = array();
        $commentData = $this->comments->getCommentsByPost($postId);
        foreach ($commentData as $comment) {
            $data[] = array(
                "comment-author" => $this->user->getName($comment['user_id']),
                "comment-content" => $comment['content']
            );
        }
        return $data;
    }

    /**
     * @param string $date
     * @return false|string
     */
    private function formatCreatedDate($date)
    {
        return date('d.m.Y в H:i', strtotime($date));
    }

    /**
     * @param int $n
     * @return string
     */
    private function pluralComments($n)
    {
        if (!$n) {
            return "Комментариев нет";
        }
        $forms = ['комментарий', 'комментария', 'коментариев'];
        return parent::pluralForm($n, $forms);
    }

    /**
     * @param int $n
     * @return string
     */
    private function pluralViews($n)
    {
        if (!$n) {
            return "Просмотров нет";
        }
        $forms = ['просмотр', 'просмотра', 'просмотров'];
        return parent::pluralForm($n, $forms);
    }

    /**
     * @param integer $postId
     * @return false|integer
     */
    private function getAuthorId($postId)
    {
        return $this->model->getAuthor($postId);
    }

    /**
     * @param int $postId
     * @return bool
     */
    protected function loadPoster($postId)
    {
        if (is_uploaded_file($_FILES['post-poster']['tmp_name'])) {
            $srcName = $_FILES['post-poster']['name'];
            $fileExt = substr($srcName, strpos($srcName, "."));
            $fileName = sprintf("%d%s", $postId, $fileExt);
            $destFile = $_SERVER['DOCUMENT_ROOT'] . "/img/" . $fileName;
            if (move_uploaded_file($_FILES['post-poster']['tmp_name'], $destFile))
                return $fileName;
        }
        return false;
    }

    /**
     * @return array $data
     */
    protected function getFormData()
    {
        return array(
            "title" => filter_input(INPUT_POST, "post-title", FILTER_SANITIZE_STRING),
            "cutcontent" => htmlspecialchars_decode(filter_input(INPUT_POST, "post-cutcontent", FILTER_SANITIZE_SPECIAL_CHARS)),
            "content" => htmlspecialchars_decode(filter_input(INPUT_POST, "post-content", FILTER_SANITIZE_SPECIAL_CHARS)),
            "user_id" => $_SESSION['user_id'],
            "dateadd" => date("Y-m-d H:i:s"),
        );
    }

    /**
     * @param array $data
     * @param array $params
     * @return bool
     */
    protected function checkRequired($data, $params)
    {
        if ((int)strlen(trim($data['title'])) < 1 || (int)strlen(trim($data['content'])) < 1) {
            $params = array_merge(array(
                'post-title' => $data['title'],
                'post-cutcontent' => $data['cutcontent'],
                'post-content' => $data['content'],
            ), $params);
            $params['page-content'] = $this->view->buildPartial("form", $params);
            $this->view->buildLayout(false, $params);
            die();
        }
        return true;
    }

    /**
     * @return bool
     */
    public static function isAuthor()
    {
        $authorId = false;
        $userId = $_SESSION['user_id'];
        $postId = $_SESSION['blog-post-id'];
        if ($postId != false) {
            $blog = new BlogController();
            $authorId = $blog->getAuthorId($postId);
        }
        if ($authorId == $userId) {
            return true;
        }
        return false;
    }
}
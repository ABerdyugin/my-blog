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
    protected $model;
    /**
     * @var BlogView
     */
    protected $view;
    /**
     * @var CommentController $comments
     */
    protected $comments;
    /**
     * @var UserController $user
     */
    protected $user;

    protected $subTitle = "Блог";

    protected $p;


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
        $p = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
        $this->p = !$p ? 1 : $p;
        $data = array(
            'site-title' => $this->title,
            'page-header' => 'Блог'
        );
        $pageContent = "";
        $limit = sprintf(" LIMIT %d, 5",
            ($this->p - 1) * 5
        );
        $content = $this->model->getData('blog', "", $limit);
        foreach ($content as $id => $entry) {
            $pageContent .= $this->getListParams($entry);
        }
        $pageContent .= $this->getPaginator('/blog/?');

        $data['page-content'] = $pageContent;

        $this->view->buildLayout(false, $data);
    }

    protected function getListParams($entry)
    {
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
        return $this->view->buildPartial("list-item", $params);
    }

    /**
     * @param array|null $routes
     * @param null $error
     */
    public function actionShow($routes, $error = null)
    {
        $id = array_shift($routes);
        $this->increaseCounter($id);
        $pageContent = "";
        $_SESSION['blog-post-id'] = $id;
        $post = $this->model->getItem('blog', $id);
        $params = array(
            'user-id' => $_SESSION['user_id'],
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
        if($error !== null){
            $params['error'] = true;
            $params['error-text'] = $error;
        }
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

                if (!$postId) {
                    $this->buildError($params, $data, "Ошибка добавления материала");
                }
                $posterName = $this->loadPoster($postId);
                if ($posterName != false) {
                    $this->model->update("blog", $postId, array(
                        "poster" => $posterName
                    ));
                }
                header("Location: /blog/" . $postId);
                die();
            } else {
                $this->buildError($params, $data, "Заполнены не все поля отмеченные звездочкой '*'");
            }
        }

    }

    /**
     * @param array $routes
     */
    public function actionEdit($routes)
    {
        $postId = intval(array_shift($routes));
        $params = array(
            'site-title' => $this->title . " - Изменение материала",
            'page-header' => 'Изменение материала',
            "action" => "edit",
            "route" => "/blog/$postId/edit"
        );
        if ((User::logged() && BlogController::isAuthor()) || UserController::isAdminNow()) {
            $act = filter_input(INPUT_POST, "act", FILTER_SANITIZE_STRING);
            if ($act != "update") {
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
                die();
            } else {
                $data = $this->getFormData();

                if ($this->checkRequired($data, $params)) {
                    $result = $this->model->update("blog", $postId, $data);
                    if (!$result) {
                        $this->buildError($params, $data, "Ошибка изменения материала");
                    }
                    $posterName = $this->loadPoster($postId);
                    if ($posterName != false) {
                        $this->model->update("blog", $postId, array(
                            "poster" => $posterName
                        ));
                    } else if (filter_input(INPUT_POST, "delete-poster", FILTER_SANITIZE_NUMBER_INT) == 1) {
                        {
                            $this->deletePoster($postId);
                            $this->model->update("blog", $postId, array(
                                "poster" => false
                            ));
                        }

                    }
                    header("Location: /blog/" . $postId);
                    die();
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
        $postId = intval(array_shift($routes));
        if ((User::logged() && BlogController::isAuthor()) || UserController::isAdminNow()) {
            if ($this->model->delete("blog", $postId)) {
                $this->deletePoster($postId);
                header("Location: /blog/");
                die();
            } else {
                $this->actionShow([$postId], "Ошибка при удалении страницы");
            }
        } else {
            header("Location: /blog/");
            die();
        }
    }

    protected function buildError($params, $data, $errorString)
    {
        $params = array_merge(array(
            'post-title' => $data['title'],
            'post-cutcontent' => $data['cutcontent'],
            'post-content' => $data['content'],
            'error' => true,
            'error-text' => $errorString
        ), $params);
        $params['page-content'] = $this->view->buildPartial("form", $params);
        $this->view->buildLayout(false, $params);
        die();
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
    protected function formatCreatedDate($date)
    {
        return date('d.m.Y в H:i', strtotime($date));
    }

    /**
     * @param int $n
     * @return string
     */
    protected function pluralComments($n)
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
    protected function pluralViews($n)
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
    protected function getAuthorId($postId)
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
        $data = array(
            "title" => filter_input(INPUT_POST, "post-title", FILTER_SANITIZE_STRING),
            "cutcontent" => htmlspecialchars_decode(filter_input(INPUT_POST, "post-cutcontent", FILTER_SANITIZE_SPECIAL_CHARS)),
            "content" => htmlspecialchars_decode(filter_input(INPUT_POST, "post-content", FILTER_SANITIZE_SPECIAL_CHARS)),
        );
        if ($_POST['act'] == "insert") {
            $data["poster"] = "";
            $data["user_id"] = $_SESSION['user_id'];
            $data["dateadd"] = date("Y-m-d H:i:s");
        }
        return $data;
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
            return false;
        }
        return true;
    }

    /**
     * @param int $postId
     */
    private function deletePoster($postId)
    {
        $post = $this->model->getItem('blog', $postId);
        $posterPath = sprintf("%s/img/%s", $_SERVER['DOCUMENT_ROOT'], $post['poster']);
        unlink($posterPath);
    }

    /**
     * @param int $postId
     */

    private function increaseCounter($postId)
    {
        if (!User::logged() || !BlogController::visited($postId)) {
            $post = $this->model->getItem("blog", $postId);
            $viewCounter = $post['views'] + 1;
            $this->model->update("blog", $postId, array("views" => $viewCounter));
            $_SESSION['visited'][$postId] = 1;
        }
    }

    protected function getPaginator($link, $where = '')
    {

        $itemCount = $this->model->getCount('blog', $where);


        $data = array(
            "page-list-count" => floor($itemCount / 5),
            'page-list-current' => $this->p,
            'page-list-link' => $link
        );
        return $this->view->buildPartial('page-list', $data);
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

    public static function visited($postId)
    {
        return $_SESSION['visited'][$postId];

    }
}
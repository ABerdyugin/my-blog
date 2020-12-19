<?php


namespace app\controllers;


use app\models\Search;
use core\Controller;

class SearchController extends BlogController
{
    protected $model;

    protected $subTitle = "Поиск";

    protected $p;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Search();
    }

    public function actionIndex($routes)
    {
        $_SESSION['blog-post-id'] = false;

        $p = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
        $this->p = !$p ? 1 : $p;

        $limit = sprintf(" LIMIT %d, 5",
            ($this->p - 1) * 5
        );

        $queryString = filter_input(INPUT_GET, 'search_text', FILTER_SANITIZE_STRING);
        $pageContent = "";
        $content = $this->model->getResult($queryString, $limit);
        $where = $this->model->buildWhere($queryString);
        $searchCount = $this->model->getCount('blog', $where);
        $data = array(
            'site-title' => $this->title,
            'page-header' => 'Поиск',
            'search-string' => $queryString,
            //'search-count' => $searchCount,
            'search-plural' => $this->pluralSearch($searchCount)
        );
        $pageContent .= $this->view->buildPartial('search-result', $data);
        foreach ($content as $id => $entry) {
            $pageContent .= $this->getListParams($entry);
        }

        $pageContent .= $this->getPaginator(sprintf('/search/?search_text=%s&amp;', $queryString), '');

        $data['page-content'] = $pageContent;

        $this->view->buildLayout(false, $data);
    }


    /**
     * @param int $n
     * @return string
     */
    protected function pluralSearch($n)
    {
        if (!$n) {
            return "не найдено ничего";
        }
        $prefix = parent::pluralForm($n, ['найдена ', 'найдено ', 'найдено '], false);
        return $prefix . parent::pluralForm($n, ['запись', 'записи', 'записей']);
    }

}
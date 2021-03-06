<?php


    namespace App\classes\controllers;


    use App\classes\abstract\Controller;
    use App\classes\models\Article;

    class News extends Controller
    {
        protected array $articles;

        public function __construct($params)
        {
            parent::__construct($params);
            $this->title = 'Статьи';
            $this->articles = Article::getAll();
            $this->content = $this->page->assign('news', $this->articles)->render('news');
        }
    }

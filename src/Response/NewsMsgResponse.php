<?php

namespace Response;

class NewsMsgResponse extends AbstractResponse
{
    protected $articles;

    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }

    public function newsMsg()
    {
        $itemKey = ['title', 'description', 'picUrl', 'url'];

        $articles = $this->articles;

        foreach ($articles as $item) {
            foreach ($itemKey as $val) {
                if (!array_key_exists($val, $item)) {
                    $articles = array();

                    $articles['item']['title'] = 'Sorry';
                    $articles['item']['description'] = '发生一些未知错误';
                    $articles['item']['picUrl'] = '#';
                    $articles['item']['url'] = '#';

                    break;
                }
            }
        }

        include_once(__DIR__.'/../views/news.php.xml');
    }
}

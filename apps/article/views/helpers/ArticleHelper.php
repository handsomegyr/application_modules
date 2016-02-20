<?php
namespace Webcms\Article\Helpers;

use Webcms\Article\Models\Article;

class ArticleHelper extends \Phalcon\Tag
{

    /**
     * 新闻公告
     */
    static function getNewsList($page = 1, $limit = 3)
    {
        $modelArticle = new Article();
        return $modelArticle->getNewsList($page, $limit);
    }
}
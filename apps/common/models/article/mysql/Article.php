<?php
namespace App\Common\Models\Article\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Article extends Base
{

    /**
     * 文章-文章管理
     * This model is mapped to the table iarticle_article
     */
    public function getSource()
    {
        return 'iarticle_article';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['article_time'] = $this->changeToMongoDate($data['article_time']);
        $data['is_show'] = $this->changeToBoolean($data['is_show']);
        return $data;
    }
}
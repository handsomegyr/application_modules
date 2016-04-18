<?php
namespace App\Common\Models\Mysql\Member;

use App\Common\Models\Mysql\Base;

class News extends Base
{

    /**
     * 会员-动态管理
     * This model is mapped to the table imember_news
     */
    public function getSource()
    {
        return 'imember_news';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['news_time'] = $this->changeToMongoDate($data['news_time']);
        return $data;
    }
}
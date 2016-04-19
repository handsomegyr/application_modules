<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Collect extends Base
{

    /**
     * 商品关注表管理
     * This model is mapped to the table igoods_collect
     */
    public function getSource()
    {
        return 'igoods_collect';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['collect_time'] = $this->changeToMongoDate($data['collect_time']);
        return $data;
    }
}
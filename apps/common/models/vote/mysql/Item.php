<?php
namespace Webcms\Common\Models\Mysql\Vote;

use Webcms\Common\Models\Mysql\Base;

class Item extends Base
{

    /**
     * 投票-选项表管理
     * This model is mapped to the table ivote_item
     */
    public function getSource()
    {
        return 'ivote_item';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_closed'] = $this->changeToBoolean($data['is_closed']);
        return $data;
    }
}


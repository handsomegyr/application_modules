<?php
namespace Webcms\Common\Models\Mysql\Store;

use Webcms\Common\Models\Mysql\Base;

class Store extends Base
{

    /**
     * 门店-门店表管理
     * This model is mapped to the table istore_store
     */
    public function getSource()
    {
        return 'istore_store';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}
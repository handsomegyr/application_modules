<?php
namespace Webcms\Common\Models\Mysql\System;

use Webcms\Common\Models\Mysql\Base;

class ErrorLog extends Base
{

    /**
     * 错误日志
     * This model is mapped to the table errorlog
     */
    public function getSource()
    {
        return 'errorlog';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['happen_time'] = $this->changeToMongoDate($data['happen_time']);
        return $data;
    }
}
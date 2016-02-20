<?php
namespace Webcms\Common\Models\Mysql\Sms;

use Webcms\Common\Models\Mysql\Base;

class Settings extends Base
{

    /**
     * 短信-短信设置表管理
     * This model is mapped to the table isms_settings
     */
    public function getSource()
    {
        return 'isms_settings';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}
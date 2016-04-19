<?php
namespace App\Common\Models\Mail\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Settings extends Base
{

    /**
     * 邮件-邮件设置表管理
     * This model is mapped to the table imail_settings
     */
    public function getSource()
    {
        return 'imail_settings';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_auth'] = $this->changeToBoolean($data['is_auth']);
        $data['is_smtp'] = $this->changeToBoolean($data['is_smtp']);
        return $data;
    }
}
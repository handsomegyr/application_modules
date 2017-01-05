<?php
namespace App\Common\Models\Site\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Suggestion extends Base
{

    /**
     * 网站-投诉与建议表管理
     * This model is mapped to the table isite_suggestion
     */
    public function getSource()
    {
        return 'isite_suggestion';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
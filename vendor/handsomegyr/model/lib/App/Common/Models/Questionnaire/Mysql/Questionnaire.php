<?php
namespace App\Common\Models\Questionnaire\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Questionnaire extends Base
{

    /**
     * 问卷-问卷管理
     * This model is mapped to the table iquestionnaire_questionnaire
     */
    public function getSource()
    {
        return 'iquestionnaire_questionnaire';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_rand'] = $this->changeToBoolean($data['is_rand']);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        
        return $data;
    }
}


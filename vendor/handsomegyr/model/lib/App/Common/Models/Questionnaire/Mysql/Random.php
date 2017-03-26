<?php
namespace App\Common\Models\Questionnaire\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Random extends Base
{

    /**
     * 问卷-随机题库管理
     * This model is mapped to the table iquestionnaire_random
     */
    public function getSource()
    {
        return 'iquestionnaire_random';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['finish_time'] = $this->changeToMongoDate($data['finish_time']);
        $data['is_finish'] = $this->changeToBoolean($data['is_finish']);
        $data['question_ids'] = $this->changeToArray($data['question_ids']);
        return $data;
    }
}

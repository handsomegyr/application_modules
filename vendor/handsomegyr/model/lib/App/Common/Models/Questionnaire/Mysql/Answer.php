<?php
namespace App\Common\Models\Questionnaire\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Answer extends Base
{

    /**
     * 问卷-答案管理
     * This model is mapped to the table iquestionnaire_answer
     */
    public function getSource()
    {
        return 'iquestionnaire_answer';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['answer_list'] = $this->changeToArray($data['answer_list']);
        $data['answer_time'] = $this->changeToMongoDate($data['answer_time']);
        return $data;
    }
}
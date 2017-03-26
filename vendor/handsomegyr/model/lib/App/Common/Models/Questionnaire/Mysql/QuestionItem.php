<?php
namespace App\Common\Models\Questionnaire\Mysql;

use App\Common\Models\Base\Mysql\Base;

class QuestionItem extends Base
{

    /**
     * 问卷-题目选项管理
     * This model is mapped to the table iquestionnaire_question_item
     */
    public function getSource()
    {
        return 'iquestionnaire_question_item';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_other'] = $this->changeToBoolean($data['is_other']);
        return $data;
    }
}


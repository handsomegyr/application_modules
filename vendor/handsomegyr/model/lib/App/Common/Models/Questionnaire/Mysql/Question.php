<?php
namespace App\Common\Models\Questionnaire\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Question extends Base
{

    /**
     * 问卷-题目管理
     * This model is mapped to the table iquestionnaire_question
     */
    public function getSource()
    {
        return 'iquestionnaire_question';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_required'] = $this->changeToBoolean($data['is_required']);
        return $data;
    }
}

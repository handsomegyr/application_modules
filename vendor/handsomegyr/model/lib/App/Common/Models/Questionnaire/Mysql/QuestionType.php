<?php
namespace App\Common\Models\Questionnaire\Mysql;

use App\Common\Models\Base\Mysql\Base;

class QuestionType extends Base
{

    /**
     * 问卷-题型管理
     * This model is mapped to the table iquestionnaire_question_type
     */
    public function getSource()
    {
        return 'iquestionnaire_question_type';
    }
}


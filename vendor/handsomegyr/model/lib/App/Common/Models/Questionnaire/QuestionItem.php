<?php
namespace App\Common\Models\Questionnaire;

use App\Common\Models\Base\Base;

class QuestionItem extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Questionnaire\Mysql\QuestionItem());
    }
}

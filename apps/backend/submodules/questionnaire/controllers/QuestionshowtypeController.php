<?php

namespace App\Backend\Submodules\Questionnaire\Controllers;

use App\Backend\Submodules\Questionnaire\Models\QuestionShowType;

/**
 * @title({name="题目展现方式"})
 *
 * @name 题目展现方式
 */
class QuestionshowtypeController extends \App\Backend\Controllers\FormController
{
    private $modelQuestionShowType;

    public function initialize()
    {
        $this->modelQuestionShowType = new QuestionShowType();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        $schemas['code'] = array(
            'name' => '展现方式值',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['name'] = array(
            'name' => '展现方式名',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '题目展现方式';
    }

    protected function getModel()
    {
        return $this->modelQuestionShowType;
    }
}

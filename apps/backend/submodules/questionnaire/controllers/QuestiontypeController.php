<?php

namespace App\Backend\Submodules\Questionnaire\Controllers;

use App\Backend\Submodules\Questionnaire\Models\QuestionType;

/**
 * @title({name="问卷题型管理"})
 *
 * @name 问卷题型管理
 */
class QuestiontypeController extends \App\Backend\Controllers\FormController
{

    private $modelQuestionType;

    public function initialize()
    {
        $this->modelQuestionType = new QuestionType();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['code'] = array(
            'name' => '分类值',
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
            'name' => '分类名',
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
        return '问卷题型';
    }

    protected function getModel()
    {
        return $this->modelQuestionType;
    }
}

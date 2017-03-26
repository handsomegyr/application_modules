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

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
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
                'is_show' => false
            )
        );
        $schemas['name'] = array(
            'name' => '分类名',
            'data' => array(
                'type' => 'string',
                'length' => 30
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
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
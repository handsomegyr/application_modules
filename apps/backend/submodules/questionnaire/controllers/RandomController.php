<?php

namespace App\Backend\Submodules\Questionnaire\Controllers;

use App\Backend\Submodules\Questionnaire\Models\Random;
use App\Backend\Submodules\Questionnaire\Models\Questionnaire;

/**
 * @title({name="随机题库管理"})
 *
 * @name 随机题库管理
 */
class RandomController extends \App\Backend\Controllers\FormController
{

    private $modelRandom;

    private $modelQuestionnaire;

    public function initialize()
    {
        $this->modelRandom = new Random();
        $this->modelQuestionnaire = new Questionnaire();

        $this->questionnaireIdItems = $this->modelQuestionnaire->getAll();
        parent::initialize();
    }

    private $questionnaireIdItems = null;

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();

        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 255,
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
        $schemas['questionnaire_id'] = array(
            'name' => '所属问卷',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionnaireIdItems
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->questionnaireIdItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionnaireIdItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['question_ids'] = array(
            'name' => '题目列表,逗号分隔',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
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
        $schemas['is_finish'] = array(
            'name' => '是否完成',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['finish_time'] = array(
            'name' => '完成时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        return '随机题库';
    }

    protected function getModel()
    {
        return $this->modelRandom;
    }
}

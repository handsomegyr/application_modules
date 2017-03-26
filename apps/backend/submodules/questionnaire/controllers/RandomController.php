<?php
namespace App\Backend\Submodules\Questionnaire\Controllers;

use App\Backend\Submodules\Questionnaire\Models\Random;
use App\Backend\Submodules\Questionnaire\Models\Question;
use App\Backend\Submodules\Questionnaire\Models\Questionnaire;

/**
 * @title({name="随机题库管理"})
 *
 * @name 随机题库管理
 */
class RandomController extends \App\Backend\Controllers\FormController
{

    private $modelRandom;

    private $modelQuestion;

    private $modelQuestionnaire;

    public function initialize()
    {
        $this->modelRandom = new Random();
        $this->modelQuestion = new Question();
        $this->modelQuestionnaire = new Questionnaire();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
        
        $schemas['questionnaire_id'] = array(
            'name' => '所属问卷',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelQuestionnaire->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'questionnaire_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelQuestionnaire->getAll()
            )
        );
        
        $schemas['question_ids'] = array(
            'name' => '题目列表',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_finish'] = array(
            'name' => '是否完成',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['finish_time'] = array(
            'name' => '完成时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        return '随机题库';
    }

    protected function getModel()
    {
        return $this->modelRandom;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $questionnaireList = $this->modelQuestionnaire->getAll();
        $questionList = $this->modelQuestion->getAll();
        
        foreach ($list['data'] as &$item) {
            $item['questionnaire_name'] = isset($questionnaireList[$item['questionnaire_id']]) ? $questionnaireList[$item['questionnaire_id']] : "--";
        }
        return $list;
    }
}
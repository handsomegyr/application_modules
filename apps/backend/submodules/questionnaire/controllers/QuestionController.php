<?php
namespace App\Backend\Submodules\Questionnaire\Controllers;

use App\Backend\Submodules\Questionnaire\Models\Question;
use App\Backend\Submodules\Questionnaire\Models\QuestionType;
use App\Backend\Submodules\Questionnaire\Models\Questionnaire;

/**
 * @title({name="问卷题目管理"})
 *
 * @name 问卷题目管理
 */
class QuestionController extends \App\Backend\Controllers\FormController
{

    private $modelQuestion;

    private $modelQuestionType;

    private $modelQuestionnaire;

    public function initialize()
    {
        $this->modelQuestion = new Question();
        $this->modelQuestionType = new QuestionType();
        $this->modelQuestionnaire = new Questionnaire();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
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
        
        $schemas['question_type'] = array(
            'name' => '题目题型',
            'data' => array(
                'type' => 'integer',
                'length' => '1'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelQuestionType->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'question_type_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['name'] = array(
            'name' => '题目名',
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
        
        $schemas['is_required'] = array(
            'name' => '是否必填',
            'data' => array(
                'type' => 'boolean',
                'length' => 1
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
        
        $schemas['content'] = array(
            'name' => '题目内容',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea', // ueditor
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['correct_answer'] = array(
            'name' => '正确答案,逗号分隔',
            'data' => array(
                'type' => 'string',
                'length' => 20
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['correct_hint'] = array(
            'name' => '正确提示',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea', // ueditor
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['picture'] = array(
            'name' => '图片',
            'data' => array(
                'type' => 'file',
                'length' => 300,
                'file' => array(
                    'path' => $this->modelQuestion->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['score'] = array(
            'name' => '分数',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['correct_times'] = array(
            'name' => '正确次数',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'input_type' => 'number',
                'is_show' => false,
                'condition_type' => 'period'
            )
        );
        
        $schemas['wrong_times'] = array(
            'name' => '错误次数',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'input_type' => 'number',
                'is_show' => false,
                'condition_type' => 'period'
            )
        );
        
        $schemas['show_order'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['next_question_id'] = array(
            'name' => '下一题',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelQuestion->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'next_question_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelQuestion->getAll()
            )
        );
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1000
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '问卷题目';
    }

    protected function getModel()
    {
        return $this->modelQuestion;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $questionnaireList = $this->modelQuestionnaire->getAll();
        $questionTypeList = $this->modelQuestionType->getAll();
        $questionList = $this->modelQuestion->getAll();
        foreach ($list['data'] as &$item) {
            $item['questionnaire_name'] = isset($questionnaireList[$item['questionnaire_id']]) ? $questionnaireList[$item['questionnaire_id']] : "--";
            $item['question_type_name'] = isset($questionTypeList[$item['question_type']]) ? $questionTypeList[$item['question_type']] : "--";
            $item['next_question_name'] = isset($questionList[$item['next_question_id']]) ? $questionList[$item['next_question_id']] : "--";
        }
        return $list;
    }
}
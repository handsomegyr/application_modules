<?php
namespace App\Backend\Submodules\Questionnaire\Controllers;

use App\Backend\Submodules\Questionnaire\Models\Random;
use App\Backend\Submodules\Questionnaire\Models\Question;
use App\Backend\Submodules\Questionnaire\Models\Questionnaire;
use App\Backend\Submodules\Questionnaire\Models\Answer;

/**
 * @title({name="问卷答案管理"})
 *
 * @name 问卷答案管理
 */
class AnswerController extends \App\Backend\Controllers\FormController
{

    private $modelAnswer;

    private $modelRandom;

    private $modelQuestion;

    private $modelQuestionnaire;

    public function initialize()
    {
        $this->modelAnswer = new Answer();
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
        
        $schemas['user_name'] = array(
            'name' => '用户名',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        
        $schemas['user_headimgurl'] = array(
            'name' => '用户头像',
            'data' => array(
                'type' => 'string',
                'length' => 300
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['questionnaire_id'] = array(
            'name' => '问卷编号',
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
        
        $schemas['random_id'] = array(
            'name' => '随机问卷编号',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['answer_list'] = array(
            'name' => '用户答题',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['score'] = array(
            'name' => '得分',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['question_num'] = array(
            'name' => '题目数量',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['correct_num'] = array(
            'name' => '正确数量',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['wrong_num'] = array(
            'name' => '错误数量',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['noanswer_num'] = array(
            'name' => '未答题数量',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['answer_time'] = array(
            'name' => '答题时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
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
        return '问卷答案';
    }

    protected function getModel()
    {
        return $this->modelAnswer;
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
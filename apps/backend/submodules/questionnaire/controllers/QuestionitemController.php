<?php
namespace App\Backend\Submodules\Questionnaire\Controllers;

use App\Backend\Submodules\Questionnaire\Models\QuestionItem;
use App\Backend\Submodules\Questionnaire\Models\Question;

/**
 * @title({name="题目选项管理"})
 *
 * @name 题目选项管理
 */
class QuestionitemController extends \App\Backend\Controllers\FormController
{

    private $modelQuestionItem;

    private $modelQuestion;

    public function initialize()
    {
        $this->modelQuestionItem = new QuestionItem();
        $this->modelQuestion = new Question();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['question_id'] = array(
            'name' => '所属题目',
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
                    return $this->modelQuestion->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'question_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelQuestion->getAll()
            )
        );
        
        $schemas['key'] = array(
            'name' => '选项',
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
        
        $schemas['content'] = array(
            'name' => '内容',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea', // 'ueditor',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
                
        $schemas['is_other'] = array(
            'name' => '是否其他',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => false
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
        
        $schemas['used_times'] = array(
            'name' => '被选次数',
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
                'is_show' => true
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
                'required' => true
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
                // single
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
        return '题目选项';
    }

    protected function getModel()
    {
        return $this->modelQuestionItem;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $questionList = $this->modelQuestion->getAll();
        foreach ($list['data'] as &$item) {
            $item['question_name'] = isset($questionList[$item['question_id']]) ? $questionList[$item['question_id']] : "--";
        }
        return $list;
    }
}
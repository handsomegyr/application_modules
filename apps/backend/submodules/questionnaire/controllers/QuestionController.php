<?php

namespace App\Backend\Submodules\Questionnaire\Controllers;

use App\Backend\Submodules\Questionnaire\Models\Question;
use App\Backend\Submodules\Questionnaire\Models\QuestionType;
use App\Backend\Submodules\Questionnaire\Models\Questionnaire;
use App\Backend\Submodules\Questionnaire\Models\QuestionCategory;
use App\Backend\Submodules\Questionnaire\Models\QuestionShowType;

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

    private $modelQuestionShowType;

    private $modelQuestionCategory;


    public function initialize()
    {
        $this->modelQuestion = new Question();
        $this->modelQuestionType = new QuestionType();
        $this->modelQuestionnaire = new Questionnaire();
        $this->modelQuestionShowType = new QuestionShowType();
        $this->modelQuestionCategory = new QuestionCategory();

        $this->questionnaireIdItems = $this->modelQuestionnaire->getAll();
        $this->questionTypeItems = $this->modelQuestionType->getAll();
        $this->questionIdItems = $this->modelQuestion->getAll();
        $this->questionCategoryItems = $this->modelQuestionCategory->getAll();
        $this->questionShowTypeItems = $this->modelQuestionShowType->getAll();
        parent::initialize();
    }

    private $questionnaireIdItems = null;
    private $questionTypeItems = null;
    private $questionIdItems = null;
    private $questionCategoryItems = null;
    private $questionShowTypeItems = null;


    protected function getSchemas2($schemas)
    {
        $schemas['name'] = array(
            'name' => '题目名',
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
                'is_editable' => true
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
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ""
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
        $schemas['question_category'] = array(
            'name' => '题目分类',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionCategoryItems
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->questionCategoryItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionCategoryItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['question_type'] = array(
            'name' => '题目题型',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionTypeItems
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->questionTypeItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionTypeItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['is_required'] = array(
            'name' => '是否必填',
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
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['content'] = array(
            'name' => '题目内容',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
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
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['correct_answer'] = array(
            'name' => '正确答案,逗号分隔',
            'data' => array(
                'type' => 'string',
                'length' => 20,
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
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['correct_hint'] = array(
            'name' => '正确提示',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['next_question_id'] = array(
            'name' => '下一题',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionIdItems
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->questionIdItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionIdItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['picture'] = array(
            'name' => '图片',
            'data' => array(
                'type' => 'file',
                'length' => 300,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelQuestion->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        $schemas['video'] = array(
            'name' => '视频',
            'data' => array(
                'type' => 'file',
                'length' => 300,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelQuestion->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    //display()方法来通过传入的回调函数来处理当前列的值：
                    return $column->display(function () use ($column) {
                        return $column->downloadable();
                    });
                }
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        $schemas['voice'] = array(
            'name' => '音频',
            'data' => array(
                'type' => 'file',
                'length' => 300,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelQuestion->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    //display()方法来通过传入的回调函数来处理当前列的值：
                    return $column->display(function () use ($column) {
                        return $column->downloadable();
                    });
                }
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        $schemas['score'] = array(
            'name' => '分数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['is_show'] = array(
            'name' => '是否显示',
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
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['show_style'] = array(
            'name' => '显示样式',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionShowTypeItems
            ),
            'list' => array(
                'is_show' => true,
                'is_editable' => true,
                'items' => $this->questionShowTypeItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->questionShowTypeItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['show_order'] = array(
            'name' => '显示顺序',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['correct_times'] = array(
            'name' => '正确次数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['wrong_times'] = array(
            'name' => '错误次数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
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
}

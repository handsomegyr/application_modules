<?php

namespace App\Backend\Submodules\Vote\Controllers;

use App\Backend\Submodules\Vote\Models\Period;
use App\Backend\Submodules\Vote\Models\Subject;

/**
 * @title({name="投票排行期管理"})
 *
 * @name 投票排行期管理
 */
class PeriodController extends \App\Backend\Controllers\FormController
{

    private $modelPeriod;

    private $modelSubject;

    public function initialize()
    {
        $this->modelPeriod = new Period();
        $this->modelSubject = new Subject();
        $this->subjectList = $this->modelSubject->getAll();
        parent::initialize();
    }
    private $subjectList = null;
    protected function getSchemas()
    {
        $schemas = parent::getSchemas();

        $schemas['subject_id'] = array(
            'name' => '投票主题ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->subjectList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->subjectList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->subjectList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['period'] = array(
            'name' => '当前期数',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => ""
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
        return '投票排行期';
    }

    protected function getModel()
    {
        return $this->modelPeriod;
    }
}

<?php
namespace App\Backend\Submodules\Vote\Controllers;

use App\Backend\Models\Vote\Period;
use App\Backend\Models\Vote\Subject;

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
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['subject_id'] = array(
            'name' => '所属主题',
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
                'items' => function ()
                {
                    return $this->modelSubject->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'subject_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'Periods' => $this->modelSubject->getAll()
            )
        );
        
        $schemas['period'] = array(
            'name' => '当前期数',
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

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $subjectList = $this->modelSubject->getAll();
        foreach ($list['data'] as &$item) {
            $item['subject_name'] = $subjectList[$item['subject_id']];
        }
        return $list;
    }
}
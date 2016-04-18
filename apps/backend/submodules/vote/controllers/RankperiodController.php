<?php
namespace App\Backend\Submodules\Vote\Controllers;

use App\Backend\Submodules\Vote\Models\RankPeriod;
use App\Backend\Submodules\Vote\Models\Subject;

/**
 * @title({name="投票每期排行管理"})
 *
 * @name 投票每期排行管理
 */
class RankperiodController extends \App\Backend\Controllers\FormController
{

    private $modelRankPeriod;

    private $modelSubject;

    public function initialize()
    {
        $this->modelRankPeriod = new RankPeriod();
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
                'items' => $this->modelSubject->getAll()
            )
        );
        
        $schemas['period'] = array(
            'name' => '排行期数',
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
        
        $schemas['name'] = array(
            'name' => '名称',
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
        
        $schemas['desc'] = array(
            'name' => '内容',
            'data' => array(
                'type' => 'html',
                'length' => 1000
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'ueditor',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['vote_count'] = array(
            'name' => '投票数',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'number',
                'is_show' => false,
                'condition_type' => 'period' // single
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
                'required' => 1
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
        return '投票每期排行';
    }

    protected function getModel()
    {
        return $this->modelRankPeriod;
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
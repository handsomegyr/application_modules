<?php

namespace App\Backend\Submodules\Activity\Controllers;

use App\Backend\Submodules\Activity\Models\Activity;
use App\Backend\Submodules\Activity\Models\Category;

/**
 * @title({name="活动管理"})
 *
 * @name 活动管理
 */
class ActivityController extends \App\Backend\Controllers\FormController
{

    private $modelActivity;

    private $modelCategory;

    public function initialize()
    {
        $this->modelActivity = new Activity();
        $this->modelCategory = new Category();

        $this->categoryList = $this->modelCategory->getAll();

        parent::initialize();
    }

    private $categoryList = null;

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();

        $schemas['category'] = array(
            'name' => '所属分类',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['name'] = array(
            'name' => '活动名称',
            'data' => array(
                'type' => 'string',
                'length' => '30'
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);

        $schemas['start_time'] = array(
            'name' => '开始时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['end_time'] = array(
            'name' => '截止时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 2 - 1)
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['is_actived'] = array(
            'name' => '是否激活',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => true
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
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['is_paused'] = array(
            'name' => '是否暂停',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
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
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['config'] = array(
            'name' => '活动配置',
            'data' => array(
                'type' => 'json',
                'length' => '1024',
                'defaultValue' => '{}'
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
            ),
            'export' => array(
                'is_show' => false
            )
        );

        // $schemas['imgs'] = array(
        //     'name' => '活动图多张',
        //     'data' => array(
        //         'type' => 'multifile',
        //         'length' => 1024,
        //         'defaultValue' => ''
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'multipleImage',
        //         'is_show' => true,
        //         'items' => ''
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => 'img',
        //     ),
        //     'search' => array(
        //         'is_show' => false
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );

        return $schemas;
    }

    protected function getName()
    {
        return '活动';
    }

    protected function getModel()
    {
        return $this->modelActivity;
    }
}

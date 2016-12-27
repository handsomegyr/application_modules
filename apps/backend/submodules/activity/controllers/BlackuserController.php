<?php
namespace App\Backend\Submodules\Activity\Controllers;

use App\Backend\Submodules\Activity\Models\BlackUser;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="活动黑名单用户管理"})
 *
 * @name 活动黑名单用户管理
 */
class BlackuserController extends \App\Backend\Controllers\FormController
{

    private $modelBlackUser;

    private $modelActivity;

    public function initialize()
    {
        $this->modelBlackUser = new BlackUser();
        $this->modelActivity = new Activity();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['activity_id'] = array(
            'name' => '活动名称',
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
                'items' => $this->modelActivity->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'activity_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelActivity->getAll()
            )
        );
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
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
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '活动黑名单用户';
    }

    protected function getModel()
    {
        return $this->modelBlackUser;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $activityList = $this->modelActivity->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = isset($activityList[$item['activity_id']]) ? $activityList[$item['activity_id']] : "--";
        }
        return $list;
    }
}
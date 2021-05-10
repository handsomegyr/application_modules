<?php

namespace App\Backend\Submodules\Activity\Controllers;

use App\Backend\Submodules\Activity\Models\User;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="活动用户管理"})
 *
 * @name 活动用户管理
 */
class UserController extends \App\Backend\Controllers\FormController
{

    private $modelUser;

    private $modelActivity;

    public function initialize()
    {
        $this->modelUser = new User();
        $this->modelActivity = new Activity();
        $this->activityList = $this->modelActivity->getAll();
        parent::initialize();
    }

    private $activityList = null;

    protected function getSchemas2($schemas)
    {
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
                'items' => $this->activityList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->activityList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->activityList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    $column->style('width:10%;word-break:break-all;');
                }
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['nickname'] = array(
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['headimgurl'] = array(
            'name' => '用户头像',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['worth'] = array(
            'name' => '价值',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['worth2'] = array(
            'name' => '价值2',
            'data' => array(
                'type' => 'integer',
                'length' => 10
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['redpack_user'] = array(
            'name' => '微信红包账号',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    $column->style('width:10%;word-break:break-all;');
                }
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['thirdparty_user'] = array(
            'name' => '第3方账号',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    $column->style('width:10%;word-break:break-all;');
                }
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );


        $schemas['contact_name'] = array(
            'name' => '联系用户',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['contact_mobile'] = array(
            'name' => '联系手机',
            'data' => array(
                'type' => 'string',
                'length' => 20
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'phone',
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
        $schemas['contact_address'] = array(
            'name' => '联系地址',
            'data' => array(
                'type' => 'string',
                'length' => 255
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['scene'] = array(
            'name' => '场景值',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['log_time'] = array(
            'name' => '记录时间',
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        // $schemas['mission_complete_num'] = array(
        //     'name' => '任务完成数',
        //     'data' => array(
        //         'type' => 'string',
        //         'length' => 50
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'number',
        //         'is_show' => false
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         // 扩展设置
        //         'extensionSettings' => function ($column, $Grid) {
        //             //display()方法来通过传入的回调函数来处理当前列的值：
        //             $column->display(function () {
        //                 $mission_num = 0;
        //                 $memo = \json_decode($this->memo, true);
        //                 $is_mission_finish_list = empty($memo['is_mission_finish_list']) ? array() : $memo['is_mission_finish_list'];
        //                 if (!empty($is_mission_finish_list)) {
        //                     if (!empty($is_mission_finish_list['is_mission1_finished'])) {
        //                         $mission_num++;
        //                     }
        //                     if (!empty($is_mission_finish_list['is_mission2_finished'])) {
        //                         $mission_num++;
        //                     }
        //                     if (!empty($is_mission_finish_list['is_mission3_finished'])) {
        //                         $mission_num++;
        //                     }
        //                     if (!empty($is_mission_finish_list['is_mission4_finished'])) {
        //                         $mission_num++;
        //                     }
        //                 }

        //                 return $mission_num;
        //             });
        //         }
        //     ),
        //     'search' => array(
        //         'is_show' => false
        //     ),
        //     'export' => array(
        //         'is_show' => true,
        //         // 扩展设置
        //         'getFormattedValue' => function ($record) {
        //             $mission_num = 0;
        //             $memo = \json_decode($record->memo, true);
        //             $is_mission_finish_list = empty($memo['is_mission_finish_list']) ? array() : $memo['is_mission_finish_list'];
        //             if (!empty($is_mission_finish_list)) {
        //                 if (!empty($is_mission_finish_list['is_mission1_finished'])) {
        //                     $mission_num++;
        //                 }
        //                 if (!empty($is_mission_finish_list['is_mission2_finished'])) {
        //                     $mission_num++;
        //                 }
        //                 if (!empty($is_mission_finish_list['is_mission3_finished'])) {
        //                     $mission_num++;
        //                 }
        //                 if (!empty($is_mission_finish_list['is_mission4_finished'])) {
        //                     $mission_num++;
        //                 }
        //             }
        //             return $mission_num;
        //         }
        //     )
        // );

        $schemas['memo'] = array(
            'name' => '备注',
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
                'is_show' => true
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '活动用户';
    }

    protected function getModel()
    {
        return $this->modelUser;
    }
}

<?php
namespace App\Backend\Submodules\Live\Controllers;

use App\Backend\Submodules\Live\Models\Room;
use App\Backend\Submodules\Live\Models\Auchor;
/**
 * @title({name="房间管理"})
 *
 * @name 房间管理
 */
class RoomController extends \App\Backend\Controllers\FormController
{

    private $modelRoom;
    private $modelAuchor;

    public function initialize()
    {
        $this->modelRoom = new Room();
        $this->modelAuchor = new Auchor();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        
        $schemas['auchor_id'] = array(
            'name' => '主播名称',
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
                'items' => $this->modelAuchor->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'auchor_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelAuchor->getAll()
            )
        );
        
        $schemas['name'] = array(
            'name' => '房间名称',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
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
                'input_type' => 'text',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['start_time'] = array(
            'name' => '房间开启时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
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
                'input_type' => 'datetimepicker',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['end_time'] = array(
            'name' => '房间关闭时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
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
                'input_type' => 'datetimepicker',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['is_opened'] = array(
            'name' => '房间是否开启',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => true,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['headline'] = array(
            'name' => '房间简介',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 200
            ),
            'validation' => array(
                'required' => true
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
        
        $schemas['bg_pic'] = array(
            'name' => '房间背景图',
            'data' => array(
                'type' => 'file',
                'defaultValue' => '',
                'length' => 300,
                'file' => array(
                    'path' => $this->modelRoom->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['cover_pic'] = array(
            'name' => '房间封面图',
            'data' => array(
                'type' => 'file',
                'defaultValue' => '',
                'length' => 300,
                'file' => array(
                    'path' => $this->modelRoom->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_test'] = array(
            'name' => '是否是测试房间',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => false,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['show_order'] = array(
            'name' => '显示顺序',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
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
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_direct'] = array(
            'name' => '是否直接进入房间',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => false,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        
        $schemas['live_start_time'] = array(
            'name' => '直播开启时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
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
                'input_type' => 'datetimepicker',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['live_end_time'] = array(
            'name' => '直播关闭时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
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
                'input_type' => 'datetimepicker',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['live_push_url'] = array(
            'name' => '直播推流地址',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 300
            ),
            'validation' => array(
                'required' => true
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['live_play_url'] = array(
            'name' => '直播播放地址',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 300
            ),
            'validation' => array(
                'required' => true
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['live_replay_url'] = array(
            'name' => '直播重播地址',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['live_paused_bg_pic'] = array(
            'name' => '直播暂停背景图',
            'data' => array(
                'type' => 'file',
                'defaultValue' => '',
                'length' => 300,
                'file' => array(
                    'path' => $this->modelRoom->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['live_closed_bg_pic'] = array(
            'name' => '直播结束背景图',
            'data' => array(
                'type' => 'file',
                'defaultValue' => '',
                'length' => 300,
                'file' => array(
                    'path' => $this->modelRoom->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['live_closed_redirect_url'] = array(
            'name' => '直播结束跳转地址',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['live_is_closed'] = array(
            'name' => '直播是否结束',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => false,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['live_is_paused'] = array(
            'name' => '直播是否暂停',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => false,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['live_is_replay'] = array(
            'name' => '直播是否回放',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => true,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['share_settings'] = array(
            'name' => '分享配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['robot_settings'] = array(
            'name' => '机器人配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['item_settings'] = array(
            'name' => '房间项目配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['behavior_settings'] = array(
            'name' => '交互行为配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['plugin_settings'] = array(
            'name' => '插件配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['view_settings'] = array(
            'name' => '授权观看配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['task_settings'] = array(
            'name' => '任务配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['emoji_settings'] = array(
            'name' => '表情包配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['category_settings'] = array(
            'name' => '栏目配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['coupon_settings'] = array(
            'name' => '优惠券配置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['banner_settings'] = array(
            'name' => '横幅广告设置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['tag_settings'] = array(
            'name' => '标签设置',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        $schemas['view_max_num'] = array(
            'name' => '房间最大围观人数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '10000',
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
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['view_random_num'] = array(
            'name' => '围观人数随机数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '1',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['view_base_num'] = array(
            'name' => '围观人数基数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['view_num'] = array(
            'name' => '真实围观人数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['view_num_virtual'] = array(
            'name' => '虚拟围观人数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['view_peak_num'] = array(
            'name' => '围观峰值',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '10000',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['like_random_num'] = array(
            'name' => '点赞随机数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '1',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['like_base_num'] = array(
            'name' => '点赞基数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['like_num'] = array(
            'name' => '真实点赞人数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['like_num_virtual'] = array(
            'name' => '虚拟点赞人数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '房间';
    }

    protected function getModel()
    {
        return $this->modelRoom;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $auchorList = $this->modelAuchor->getAll();
        foreach ($list['data'] as &$item) {
            $item['start_time'] = date("Y-m-d H:i:s", $item['start_time']->sec);
            $item['end_time'] = date("Y-m-d H:i:s", $item['end_time']->sec);
            $item['live_start_time'] = date("Y-m-d H:i:s", $item['live_start_time']->sec);
            $item['live_end_time'] = date("Y-m-d H:i:s", $item['live_end_time']->sec);
            $item['auchor_name'] = isset($auchorList[$item['auchor_id']]) ? $auchorList[$item['auchor_id']] : "--";
        }
        return $list;
    }
}
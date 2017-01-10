<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Event;
use App\Backend\Submodules\Weixincard\Models\Card;

/**
 * @title({name="事件推送管理"})
 *
 * @name 事件推送管理
 */
class EventController extends \App\Backend\Controllers\FormController
{

    private $modelEvent;

    private $modelCard;

    public function initialize()
    {
        $this->modelEvent = new Event();
        $this->modelCard = new Card();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['card_id'] = array(
            'name' => '微信卡券',
            'data' => array(
                'type' => 'string',
                'length' => '32'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelCard->getAllWithCardId();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'card_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['card_code'] = array(
            'name' => '自定义码',
            'data' => array(
                'type' => 'string',
                'length' => '20'
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
        
        $schemas['code'] = array(
            'name' => '券码',
            'data' => array(
                'type' => 'string',
                'length' => '12'
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
        
        $schemas['pwd'] = array(
            'name' => '券密码',
            'data' => array(
                'type' => 'string',
                'length' => '8'
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
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['start_time'] = array(
            'name' => '开始有效期',
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
                'is_show' => false
            )
        );
        
        $schemas['end_time'] = array(
            'name' => '截止有效期',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 30 - 1)
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
        
        $schemas['is_deposited'] = array(
            'name' => '是否已导入',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
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
        
        $schemas['is_consumed'] = array(
            'name' => '是否已核销',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
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
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => '1000'
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
        return '事件推送';
    }

    protected function getModel()
    {
        return $this->modelEvent;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $cardList = $this->modelCard->getAllWithCardId();
        foreach ($list['data'] as &$item) {
            $item['card_name'] = isset($cardList[$item['card_id']]) ? $cardList[$item['card_id']] : "--";
            $item['start_time'] = date("Y-m-d H:i:s", $item['start_time']->sec);
            $item['end_time'] = date("Y-m-d H:i:s", $item['end_time']->sec);
        }
        
        return $list;
    }

    /**
     * 导入code
     * 开发者需调用该接口将自定义code 导入微信卡券后台，由微信侧代理存储并下发
     * code，本接口仅用于支持微信摇卡券活动。
     * 注：
     * 1）单次调用接口传入code 的数量上限为100 个。
     * 2）每一个 code 均不能为空串，且不能重复填入相同code，否则会导入失败。
     * 3）导入失败支持重复导入，提示成功为止。
     *
     * 一次性处理,不用计划任务配置
     * 如果导入的数据量过大,那么可以使用多进程 来导入数据
     * 可以先用getidsAction获取每个进程的数据处理范围
     */
    public function depositecodeAction()
    {
        // http://www.applicationmodule.com:10080/admin/weixincard/Event/depositecode?card_id=p4ELSv5zS98NBYuq8D1l2HcgRou0
        try {
            $this->view->disable();
            $weixin = $this->getWeixin();
            $this->modelEvent->setWeixin($weixin);
            
            $card_id = $this->get('card_id', '');
            if (empty($card_id)) {
                throw new \Exception("card_id未指定", - 1);
            }
            // 导入自定义卡券code处理
            $this->modelEvent->depositeCode($card_id);
            
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
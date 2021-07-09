<?php

namespace App\Campaign\Controllers;

/**
 * 砍价事例
 *
 * @author Administrator
 *        
 */
class BargainController extends ControllerBase
{

    protected $modelBargain = null;

    protected $modelLog = null;

    protected $modelApi = null;

    protected function doCampaignInitialize()
    {
        $this->modelBargain = new \App\Bargain\Models\Bargain();
        $this->modelLog = new \App\Bargain\Models\Log();
        $this->modelApi = new \App\Bargain\Services\Api();
        $this->view->disable();
    }

    public function indexAction()
    {
        die('index');
    }

    /**
     * 发起砍价物的接口
     */
    public function launchAction()
    {
        // http://www.myapplicationmodule.com/campaign/bargain/launch?activity_id=58d4d7b4f3b9530b008b4567&FromUserName=guoyongrong&nickname=guoyongrong&headimgurl=headimgurl&bargain_code=bargain_code1&bargain_name=bargain_name1&worth=1000&quantity=1&bargain_from=100&bargain_to=200&worth_min=500&bargain_max=800&bargain_num_limit=10&bargain_period=24&is_both_bargain=0&start_time=1490284800&end_time=1553356800
        try {
            // 活动
            $activity_id = $this->get('activity_id', '');

            // 用户
            $FromUserName = $this->get('FromUserName', '');
            $nickname = $this->get('nickname', '');
            $headimgurl = $this->get('headimgurl', '');

            // 砍价物
            $bargain_code = $this->get('bargain_code', '');
            $bargain_name = $this->get('bargain_name', '');
            $worth = intval($this->get('worth', '0'));
            $quantity = intval($this->get('quantity', '1'));

            // 砍价设置
            $bargain_from = intval($this->get('bargain_from', '0'));
            $bargain_to = intval($this->get('bargain_to', '0'));
            $worth_min = intval($this->get('worth_min', '0'));
            $bargain_max = intval($this->get('bargain_max', '0'));
            $bargain_num_limit = intval($this->get('bargain_num_limit', '0'));
            $bargain_period = intval($this->get('bargain_period', '0')); // 时间间隔

            // 以下信息都是待定的
            $is_both_bargain = $this->get('is_both_bargain', '0');
            $is_both_bargain = empty($is_both_bargain) ? false : true;

            $start_time = intval($this->get('start_time', '0'));
            $end_time = intval($this->get('end_time', '0'));

            // 以微信号作为发起人的唯一标识,发起砍价，获取砍价物ID
            $memo = array(
                'test' => 'xxx'
            );
            $ret = $this->modelApi->launchBargain($activity_id, $FromUserName, $nickname, $headimgurl, $bargain_code, $bargain_name, $worth, $quantity, $bargain_from, $bargain_to, $worth_min, $bargain_max, $bargain_num_limit, $is_both_bargain, $start_time, $end_time, $bargain_period, $this->now, $memo);
            if (empty($ret['error_code'])) {
                echo $this->result('OK', $ret['result']);
                return true;
            } else {
                echo $this->error($ret['error_code'], $ret['error_msg']);
                return false;
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取某人的砍价物信息的接口
     */
    public function getAction()
    {
        // http://www.myapplicationmodule.com/campaign/bargain/get?bargain_id=58d4dd2af3b9530d008b4568
        try {
            $bargain_id = $this->get("bargain_id", '');
            if (empty($bargain_id)) {
                echo $this->error(-1, '砍价物ID为空');
                return false;
            }
            $bargainInfo = $this->modelBargain->getInfoById($bargain_id);
            if (empty($bargainInfo)) {
                echo $this->error(-2, '砍价物ID不正确');
                return false;
            }

            $ret = array();
            $ret['bargainInfo'] = $bargainInfo; // 砍价信息

            // 是否能再砍
            $isCanBargain = false;
            $bargainCheckResult = $this->modelApi->checkBargain($bargainInfo, $this->now);
            if (empty($bargainCheckResult['error_code'])) {
                $isCanBargain = true;
            }
            $ret['isCanBargain'] = $isCanBargain;

            echo $this->result('OK', $ret);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取某人的砍价日志列表信息的接口
     */
    public function getloglistAction()
    {
        // http://www.myapplicationmodule.com/campaign/bargain/getloglist?bargain_id=58d4dd2af3b9530d008b4568
        try {
            $bargain_id = $this->get("bargain_id", '');
            if (empty($bargain_id)) {
                echo $this->error(-1, '砍价物ID为空');
                return false;
            }

            // 获取砍价物的砍价日志列表
            $otherCondition = array();
            $otherCondition['bargain_id'] = $bargain_id;
            $logList = $this->modelLog->getList(1, 10, $otherCondition);
            $ret = array();
            $ret['logList'] = $logList; // 砍价日志列表信息

            echo $this->result('OK', $ret);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 砍价的接口
     */
    public function doAction()
    {
        // http://www.myapplicationmodule.com/campaign/bargain/do?FromUserName=handsomegyr&nickname=handsomegyr&headimgurl=handsomegyr&bargain_id=58d4dd2af3b9530d008b4568
        try {
            $FromUserName = $this->get('FromUserName', ''); // 砍价者微信号
            $nickname = $this->get('nickname', ''); // 砍价者微信昵称
            $headimgurl = $this->get('headimgurl', ''); // 砍价者微信头像

            $bargain_id = $this->get('bargain_id', ''); // 砍价物ID
            if (empty($bargain_id)) {
                echo $this->error(-1, 'bargain_id为空');
                return false;
            }
            if (empty($FromUserName)) {
                echo $this->error(-2, 'FromUserName为空');
                return false;
            }

            $bargainInfo = $this->modelBargain->getInfoById($bargain_id);
            if (empty($bargainInfo)) {
                echo $this->error(-3, 'bargain_id不正确');
                return false;
            }

            // 砍价物的发起者 和砍价者是同一个人的时候,这个跟实际业务有关.
            if ($bargainInfo['user_id'] == $FromUserName) {
                echo $this->error(-4, '自己不能对自己的砍价物进行砍价');
                return false;
            }

            // 砍价处理
            $memo = array();
            $client_ip = getIp();
            $ret = $this->modelApi->bargain($FromUserName, $nickname, $headimgurl, $bargain_id, $client_ip, $this->now, $memo);
            if (empty($ret['error_code'])) {
                echo $this->result('OK', $ret['result']);
                return true;
            } else {
                echo $this->error($ret['error_code'], $ret['error_msg']);
                return false;
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 关闭砍价物的接口
     * 场景
     * 发起人对他的某个砍价物进行购买,下单时,就认为该砍价物已关闭,所以需要调用此接口
     */
    public function closeAction()
    {
        // http://www.myapplicationmodule.com/campaign/bargain/close?bargain_id=58d4dd2af3b9530d008b4568&FromUserName=guoyongrong
        try {
            $FromUserName = $this->get('FromUserName', ''); // 砍价者微信号

            $bargain_id = $this->get('bargain_id', ''); // 砍价物ID
            if (empty($bargain_id)) {
                echo $this->error(-1, 'bargain_id为空');
                return false;
            }
            if (empty($FromUserName)) {
                echo $this->error(-2, 'FromUserName为空');
                return false;
            }
            $bargainInfo = $this->modelBargain->getInfoById($bargain_id);
            if (empty($bargainInfo)) {
                echo $this->error(-3, 'bargain_id不正确');
                return false;
            }
            // 砍价物的发起者 和砍价者是同一个人的时候,这个跟实际业务有关.
            if ($bargainInfo['user_id'] != $FromUserName) {
                echo $this->error(-4, '他人不能对自己的砍价物进行关闭');
                return false;
            }

            // 关闭砍价物信息
            $this->modelBargain->doClosed($bargainInfo['_id'], $this->now);
            echo $this->result('OK');
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }
}

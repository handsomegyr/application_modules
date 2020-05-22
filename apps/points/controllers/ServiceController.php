<?php

namespace App\Points\Controllers;

/**
 * 积分服务
 *
 * @author Admin
 *        
 */
class ServiceController extends ControllerBase
{

    private $modelPointLog = null;

    private $modelPointUser = null;

    private $modelMember = null;

    private $modelPointsService = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelPointUser = new \App\Points\Models\User();
        $this->modelPointLog = new \App\Points\Models\Log();
        $this->modelMember = new \App\Member\Models\Member();

        $this->modelPointsService = new \App\Points\Service\Api();
    }

    /**
     * 获取某个时间范围内的积分明细
     * 会员-我的福分
     *
     * @return array
     */
    public function getuserpointsdetaillistAction()
    {
        // http://member.1yyg.com/JPData?action=getUserPointsDetailList&FIdx=1&EIdx=10&region=4&beginTime=&endTime=&isCount=1&fun=jsonp1451610431999&_=1451610432530
        // jsonp1451610431999({"code":0,"str":{"totalCount":13,"listItems":[{"logTime":"2015.12.27 00:30:01","logPointNum":"3","logDescript":"2015-12-25评论获得福分"},{"logTime":"2015.12.26 00:30:03","logPointNum":"9","logDescript":"2015-12-24评论获得福分"},{"logTime":"2015.12.23 00:30:01","logPointNum":"3","logDescript":"2015-12-21评论获得福分"},{"logTime":"2015.12.20 21:36:51","logPointNum":"1","logDescript":"云购商品编码(2542873)支付1元获得福分"},{"logTime":"2015.12.20 21:36:24","logPointNum":"2","logDescript":"云购商品编码(2509590)支付2元获得福分"},{"logTime":"2015.12.20 21:32:20","logPointNum":"1","logDescript":"云购商品编码(2450928)支付1元获得福分"},{"logTime":"2015.12.20 21:19:00","logPointNum":"1","logDescript":"云购商品编码(2542665)支付1元获得福分"},{"logTime":"2015.12.20 21:19:00","logPointNum":"2","logDescript":"云购商品编码(2509588)支付2元获得福分"},{"logTime":"2015.11.27 23:50:25","logPointNum":"1","logDescript":"云购商品编码(2162400)支付1元获得福分"},{"logTime":"2015.11.19 10:30:28","logPointNum":"1","logDescript":"云购商品编码(1992345)支付1元获得福分"}]}})
        // http://www.applicationmodule.com/points/service/getuserpointsdetaillist?beginTime=2015-12-01&endTime=2015-12-01&page=1&limit=10
        try {
            $beginTime = $this->get('beginTime', '');
            if (empty($beginTime)) {
                echo ($this->error(-1, '开始时间为空'));
                return false;
            }
            $endTime = $this->get('endTime', '');
            if (empty($endTime)) {
                echo ($this->error(-2, '结束时间为空'));
                return false;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));
            $ret = array(
                'total' => 0,
                'datas' => array()
            );

            if (!empty($_SESSION['member_id'])) {
                $otherConditions = array();
                if (!empty($beginTime)) {
                    $beginTime = strtotime($beginTime . " 00:00:00");
                }
                if (!empty($endTime)) {
                    $endTime = strtotime($endTime . " 23:59:59");
                }
                $logList = $this->modelPointLog->getUserPointsDetailList($_SESSION['member_id'], POINTS_CATEGORY1, $page, $limit, $beginTime, $endTime, $otherConditions);
                $datas = array();
                if (!empty($logList['datas'])) {
                    foreach ($logList['datas'] as $log) {
                        // "logTime":"2015.12.27 00:30:01",
                        // "logPointNum":"3",
                        // "logDescript":"2015-12-25评论获得福分"
                        $datas[] = array(
                            'logTime' => date('Y-m-d H:i:s', $log['add_time']->sec),
                            'logPointNum' => empty($log['is_consumed']) ? abs($log['points']) : -abs($log['points']),
                            'logDescript' => $log['desc']
                        );
                    }
                }
                $ret = array(
                    'total' => $logList['total'],
                    'datas' => $datas
                );
            }
            echo $this->result('OK', $ret);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 充值到云购账户的接口
     * 会员-我的福分
     *
     * @return array
     */
    public function rechargepredepositAction()
    {
        // http://www.applicationmodule.com/points/service/rechargepredeposit?predeposit=10
        try {
            $predeposit = intval($this->get('predeposit', '0'));
            if (empty($predeposit)) {
                echo ($this->error(-1, '金额为空'));
                return false;
            }
            if (empty($_SESSION['member_id'])) {
                echo ($this->error(-2, '用户为空'));
                return false;
            }

            $memberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            if (empty($memberInfo)) {
                echo ($this->error(-5, '用户不存在'));
                return false;
            }

            // 福分账户
            $pointUserInfo = $this->modelPointUser->getInfoByUserId($_SESSION['member_id'], POINTS_CATEGORY1);
            if (empty($pointUserInfo)) {
                echo ($this->error(-3, '福分账户不存在'));
                return false;
            }

            if (empty($pointUserInfo['current']) || $pointUserInfo['current'] < $predeposit * 100) {
                echo ($this->error(-4, '用户福分不足'));
                return false;
            }

            // 充值处理
            $this->rechargePredeposit($memberInfo, $predeposit);

            echo $this->result('OK');
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取某个时间范围内的佣金明细
     *
     * @return array
     */
    public function getmembercentercommissionlistAction()
    {
        // http://www.applicationmodule.com/points/service/getmembercentercommissionlist?beginTime=2015-12-01&endTime=2015-12-01&page=1&limit=10
        try {
            $beginTime = $this->get('beginTime', '');
            if (empty($beginTime)) {
                echo ($this->error(-1, '开始时间为空'));
                return false;
            }
            $endTime = $this->get('endTime', '');
            if (empty($endTime)) {
                echo ($this->error(-2, '结束时间为空'));
                return false;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));

            if (!empty($_SESSION['member_id'])) {
                $logList = array(
                    'total' => 0,
                    'datas' => array()
                );
            } else {
                $logList = array(
                    'total' => 0,
                    'datas' => array()
                );
            }
            echo $this->result('OK', $logList);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    private function rechargePredeposit($memberInfo, $predeposit)
    {
        try {
            $this->modelPointUser->begin();
            // lock
            $pointUserInfo = $this->modelPointUser->findOne(array(
                'user_id' => $memberInfo['_id'],
                'category' => intval(POINTS_CATEGORY1),
                '__FOR_UPDATE__' => true
            ));
            $memo = array();

            $register_name = $this->modelMember->getRegisterName($memberInfo);

            $uniqueId = getNewId();
            // 福分账户减
            $points = $predeposit * 100;
            $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $memberInfo['_id'], $register_name, $memberInfo['avatar'], $uniqueId, $this->now, -$points, "提取", "已提取{$points}福分");
            // 预付款账户增加
            $this->modelPointsService->addOrReduce(POINTS_CATEGORY3, $memberInfo['_id'], $register_name, $memberInfo['avatar'], $uniqueId, $this->now, $predeposit * 100, "充值", "已充值￥{$predeposit}已到您的云购账户");

            $this->modelPointUser->commit();
        } catch (\Exception $e) {
            $this->modelMember->rollback();
            throw $e;
        }
    }
}

<?php

namespace App\Payment\Controllers;

/**
 * 关于支付的各种其他服务
 *
 * @author Admin
 *        
 */
class ServiceController extends ControllerBase
{

    private $modelLog = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelLog = new \App\Payment\Models\Log();
    }

    /**
     * 获取账户记录的接口
     * 我的账户
     *
     * @return boolean
     */
    public function getmembercenteruserbcrecordAction()
    {
        // http://member.1yyg.com/JPData?action=getMemberCenterUserBCRecord&FIdx=1&EIdx=10&type=0&region=4&beginTime=&endTime=&isCount=1&fun=jsonp1451609919809&_=1451609920286
        // jsonp1451609919809({"code":0,"str":{"totalCount":16,"listItems":[{"logMoeny":"-1","typeName":"云购商品","logTime":"2015-12-20 21:36:51","logType":"2"},{"logMoeny":"1","typeName":"微信支付","logTime":"2015-12-20 21:36:50","logType":"1"},{"logMoeny":"-2","typeName":"云购商品","logTime":"2015-12-20 21:36:24","logType":"2"},{"logMoeny":"1","typeName":"微信支付","logTime":"2015-12-20 21:36:23","logType":"1"},{"logMoeny":"1","typeName":"微信支付","logTime":"2015-12-20 21:33:23","logType":"1"},{"logMoeny":"-1","typeName":"云购商品","logTime":"2015-12-20 21:32:20","logType":"2"},{"logMoeny":"-1","typeName":"云购商品","logTime":"2015-12-20 21:19:00","logType":"2"},{"logMoeny":"-2","typeName":"云购商品","logTime":"2015-12-20 21:19:00","logType":"2"},{"logMoeny":"3","typeName":"微信支付","logTime":"2015-12-20 21:16:32","logType":"1"},{"logMoeny":"1","typeName":"微信支付","logTime":"2015-11-27 23:58:26","logType":"1"}]}})
        // http://www.jizigou.com/payment/service/getmembercenteruserbcrecord?type=0&page=1&limit=10&beginTime=2015-12-01&endTime=2015-12-01
        try {
            // 全部充值记录消费记录转账记录
            $type = intval($this->get('type', '0'));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));
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

            $ret = array();
            if (empty($_SESSION['member_id'])) {
                $ret['total'] = 0;
                $ret['datas'] = array();
            } else {
                if (!empty($beginTime)) {
                    $beginTime = strtotime($beginTime . " 00:00:00");
                }
                if (!empty($endTime)) {
                    $endTime = strtotime($endTime . " 23:59:59");
                }

                $list = $this->modelLog->getuserbcrecord($_SESSION['member_id'], $page, $limit, $type, $beginTime, $endTime);

                $datas = array();
                if (!empty($list['datas'])) {
                    foreach ($list['datas'] as $item) {
                        // "logMoeny":"-1",
                        // "typeName":"云购商品",
                        // "logTime":"2015-12-20 21:36:51",
                        // "logType":"2"
                        $datas[] = array(
                            'logMoney' => abs($item['money']),
                            'logTime' => date('Y-m-d H:i:s', $item['log_time']->sec),
                            'typeName' => $item['desc'],
                            'logType' => $item['type']
                        );
                    }
                }
                $ret['total'] = $list['total'];
                $ret['datas'] = $datas;
            }
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}

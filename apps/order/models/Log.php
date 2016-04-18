<?php
namespace App\Order\Models;

class Log extends \App\Common\Models\Order\Log
{

    /**
     * 生成订单日志表信息
     *
     * @throws \Exception
     * @return array
     */
    public function log($order_id, $orderState, $msg, $role, $user_id, $user_name)
    {
        $data = array();
        $data['order_id'] = $order_id;
        $data['order_state'] = $orderState;
        $data['log_time'] = getCurrentTime();
        $data['msg'] = $msg;
        $data['role'] = $role;
        $data['user_id'] = $user_id;
        $data['user_name'] = $user_name;
        $orderLogInfo = $this->insert($data);
        return $orderLogInfo;
    }

    /**
     * 根据订单ID获取日志列表
     *
     * @param string $order_id            
     * @return array
     */
    public function getListByOrderId($order_id)
    {
        $query = array();
        $query['order_id'] = $order_id;
        $sort = array();
        $sort['log_time'] = 1;
        $list = $this->findAll($query, $sort);
        return $list;
    }

    public function getLogName(array $logInfo)
    {
        if ($logInfo['role'] == \App\Order\Models\Log::ROLE_BUYER) {
            return '会员本人';
        } elseif ($logInfo['role'] == \App\Order\Models\Log::ROLE_SYSTEM) {
            return '云购系统';
        } else {
            return $logInfo['user_name'];
        }
    }
}
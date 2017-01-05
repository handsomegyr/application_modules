<?php
namespace App\Exchange\Models;

class Log extends \App\Common\Models\Exchange\Log
{

    public function addLog($result_code, $result_msg, $user_id, $prize_id, $rule_id, $quantity, $score, $success_id = '', array $memo = array())
    {
        $data = array();
        $data['result_code'] = $result_code;
        $data['result_msg'] = $result_msg;
        $data['user_id'] = $user_id;
        $data['prize_id'] = $prize_id;
        $data['rule_id'] = $rule_id;
        $data['quantity'] = $quantity;
        $data['score'] = $score;
        $data['success_id'] = $success_id;
        $data['memo'] = $memo;
        $data = $this->insert($data);
        return $data;
    }
}
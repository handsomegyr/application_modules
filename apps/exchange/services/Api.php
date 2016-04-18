<?php
namespace App\Exchange\Services;

class Api
{

    private $modelRule;

    private $modelLog;

    private $modelSuccess;

    private $modelLimit;

    private $modelPrize;

    private $modelCode;

    private $_isInstance = false;

    public function __construct()
    {
        $this->modelRule = new \App\Exchange\Models\Rule();
        $this->modelLog = new \App\Exchange\Models\Log();
        $this->modelSuccess = new \App\Exchange\Models\Success();
        $this->modelLimit = new \App\Exchange\Models\Limit();
        $this->modelPrize = new \App\Prize\Models\Prize();
        $this->modelCode = new \App\Prize\Models\Code();
    }

    /**
     * 做兑换动作
     *
     * @param string $activity_id            
     * @param string $user_id            
     * @param string $rule_id            
     * @param number $quantity            
     * @param number $score            
     * @param array $callbacks            
     * @param array $user_info            
     * @param array $user_contact            
     * @param array $memo            
     * @return array
     */
    public function doExchange($activity_id, $user_id, $rule_id, $quantity = 1, $score = 0, array $callbacks = array(), array $user_info = array(), array $user_contact = array(), array $memo = array('memo'=>''))
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        try {
            if ($this->_isInstance) {
                $ret['error_code'] = - 100;
                $ret['error_msg'] = '每个抽奖实例只能执行一次doExchange方法，如需反复抽奖，请分别实例化Service_Api类';
                return $ret;
            }
            
            $this->_isInstance = true;
            
            // 控制活动流速，降低刷风险，同时只能有一个请求执行成功逻辑，将导致无法满足100%中奖要求
            $objLock = new \iLock(cacheKey(__FILE__, __CLASS__, __METHOD__, $user_id));
            if ($objLock->lock()) {
                $ret['error_code'] = - 99;
                $ret['error_msg'] = '处于锁定状态，请稍后尝试';
                return $ret;
            }
            
            $score = intval($score);
            $quantity = intval($quantity);
            
            // 获取规则信息
            $ruleInfo = $this->modelRule->getInfoById($rule_id);
            if (empty($ruleInfo)) {
                $ret['error_code'] = - 1;
                $ret['error_msg'] = "没有找到rule_id:{$rule_id}的兑换规则信息";
                return $ret;
            }
            
            // 获取奖品信息
            $prize_id = $ruleInfo['prize_id'];
            $prizeInfo = $this->modelPrize->getInfoById($prize_id);
            if (empty($prizeInfo)) {
                $ret['error_code'] = - 2;
                $ret['error_msg'] = "没有找到prize_id:{$prize_id}的奖品信息";
                return $ret;
            }
            
            // 检查时间
            $nNow = time();
            if ($ruleInfo['start_time']->sec >= $nNow) {
                $ret['error_code'] = - 3;
                $ret['error_msg'] = "兑换未开始";
                return $ret;
            }
            
            if ($ruleInfo['end_time']->sec < $nNow) {
                $ret['error_code'] = - 4;
                $ret['error_msg'] = "兑换已结束";
                return $ret;
            }
            
            // 检查数量
            if ($ruleInfo['quantity'] < $quantity) {
                $ret['error_code'] = - 5;
                $ret['error_msg'] = "奖品数量不足";
                return $ret;
            }
            
            // 需要积分
            if (! empty($ruleInfo['score'])) {
                if ($score < $ruleInfo['score'] * $quantity) {
                    $ret['error_code'] = - 6;
                    $ret['error_msg'] = "积分不足";
                    return $ret;
                }
            }
            // 检查兑换限制
            $this->modelLimit->setSuccessModel($this->modelSuccess);
            if (! $this->modelLimit->checkLimit($prize_id, $user_id, $quantity)) {
                $ret['error_code'] = - 7;
                $ret['error_msg'] = "兑换数量限制!";
                return $ret;
            }
            try {
                $this->modelRule->begin();
                
                // 上锁
                $ruleInfo = $this->modelRule->findOne(array(
                    '_id' => $rule_id,
                    '__FOR_UPDATE__' => true
                ));
                
                // 扣除数量
                $newRuleInfo = $this->modelRule->exchange($rule_id, $quantity);
                $exchange_score = $newRuleInfo['score'];
                
                // 发虚拟券
                $prizeCodeInfo = array();
                // 如果是虚拟奖品并且需要发放券码的时候
                if ($prizeInfo['is_virtual'] && $prizeInfo['is_need_virtual_code']) {
                    // 获取一条券码记录
                    $prizeCodeInfo = $this->modelCode->getCode($prize_id, $activity_id);
                    if (! $prizeCodeInfo) {
                        throw new \Exception('虚拟券不足!', - 8);
                    }
                }
                
                // 添加兑换记录
                $successInfo = $this->modelSuccess->addSuccess($user_id, $prize_id, $quantity, $exchange_score, $newRuleInfo, $prizeCodeInfo, $prizeInfo, $user_info, $user_contact, $memo);
                
                // 额外的处理
                if (! empty($callbacks)) {
                    foreach ($callbacks as $item) {
                        call_user_func_array($item, array(
                            $successInfo
                        ));
                    }
                }
                
                $this->modelRule->commit();
                // 记录日志
                $logInfo = $this->modelLog->addLog(0, '兑换成功', $user_id, $prize_id, $rule_id, $quantity, $exchange_score, $successInfo['_id'], $memo);
                
                $ret['result'] = $successInfo;
            } catch (\Exception $e) {
                $this->modelRule->rollback();
                $logInfo = $this->modelLog->addLog($e->getCode(), $e->getMessage(), $user_id, $prize_id, $rule_id, $quantity, $exchange_score, '', $memo);
                throw $e;
            }
        } catch (\Exception $e) {
            $ret['error_code'] = $e->getCode();
            $ret['error_msg'] = $e->getMessage();
        }
        return $ret;
    }
}
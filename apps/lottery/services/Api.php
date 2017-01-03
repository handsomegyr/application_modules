<?php
namespace App\Lottery\Services;

class Api
{

    private $_code;

    private $_exchange;

    private $_limit;

    private $_prize;

    private $_record;

    private $_rule;

    private $_source;

    private $_activity_id;

    private $_uniqueId;

    public $_isInstance = false;

    public function __construct()
    {
        $this->_code = new \App\Prize\Models\Code();
        $this->_exchange = new \App\Lottery\Models\Exchange();
        $this->_limit = new \App\Lottery\Models\Limit();
        $this->_prize = new \App\Prize\Models\Prize();
        $this->_record = new \App\Lottery\Models\Record();
        $this->_rule = new \App\Lottery\Models\Rule();
        $this->_source = new \App\System\Models\Source();
    }

    /**
     * 做抽奖动作
     *
     * @param string $activity_id            
     * @param string $identity_id            
     * @param array $prize_ids
     *            如果传入一组特定的奖品的话,那么就是抽该组奖品
     * @param array $exclude_prize_ids            
     * @param string $source            
     * @param array $user_info            
     * @param array $identityContact            
     * @param array $memo            
     */
    public function doLottery($activity_id, $identity_id, array $prize_ids = array(), array $exclude_prize_ids = array(), $source = 'weixin', array $user_info = array(), $identityContact = array(), array $memo = array())
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        
        try {
            if ($this->_isInstance) {
                $ret['error_code'] = - 100;
                $ret['error_msg'] = '每个抽奖实例只能执行一次doLottery方法，如需反复抽奖，请分别实例化Service_Api类';
                return $ret;
            }
            
            $this->_isInstance = true;
            
            // 控制活动流速，降低刷风险，同时只能有一个请求执行成功逻辑，将导致无法满足100%中奖要求
            $objLock = new \iLock(cacheKey($activity_id, $identity_id));
            if ($objLock->lock()) {
                $ret['error_code'] = - 99;
                $ret['error_msg'] = '抽奖处于锁定状态，请稍后尝试';
                return $ret;
            }
            
            // 检测是否存在未领取或者未激活的中奖奖品，有的话，再次让其 中同样的奖品完善个人信息。
            $invalidExchange = $this->_exchange->getExchangeInvalidById($identity_id, $activity_id, $prize_ids);
            if (! empty($invalidExchange)) {
                $invalidExchange['exchange_id'] = $invalidExchange['_id'];
                $ret['result'] = $invalidExchange;
                return $ret;
            }
            try {
                $this->_exchange->begin();
                // 检查中奖情况和中奖限制条件的关系
                $this->_limit->setExchangeModel($this->_exchange);
                $limit = $this->_limit->checkLimit($activity_id, $identity_id, 'all');
                if ($limit == false) {
                    throw new \Exception('到达抽奖限制的上限制', - 3);
                }
                
                // 检查中奖规则，检测用户是否中奖
                $this->_rule->setLimitModel($this->_limit); // 装在limit,不再重新加载数据
                $rule = $this->_rule->lottery($activity_id, $identity_id, $prize_ids);
                if ($rule == false) {
                    throw new \Exception('很遗憾，您没有中奖', - 4);
                }
                
                // LOCK
                $rule = $this->_rule->findOne(array(
                    '_id' => $rule['_id'],
                    '__FOR_UPDATE__' => true
                ));
                
                // 更新中奖信息
                if (! $this->_rule->updateRemain($rule)) {
                    throw new \Exception('竞争争夺奖品失败', - 5);
                }
                // throw new \Exception("测试", 999);
                // 竞争到奖品，根据奖品的属性标记状态
                $prizeInfo = $this->_prize->getPrizeInfo($rule['prize_id']);
                
                $result = array();
                $result['identity_id'] = $identity_id;
                $result['prizeInfo'] = $prizeInfo;
                
                // 是否即时生效
                $isValid = ! empty($prizeInfo['is_valid']) ? true : false;
                // 虚拟物品
                if (! empty($prizeInfo['is_virtual'])) {
                    // 发放虚拟奖品
                    if (! empty($prizeInfo['is_need_virtual_code'])) {
                        $code = $this->_code->getCode($rule['prize_id'], $activity_id);
                        if ($code == false) {
                            throw new \Exception('虚拟券不足!', - 6);
                        }
                    }
                }
                
                // 记录中奖记录
                $prizeCode = ! empty($code) ? $code : array();
                
                // 记录信息
                $exchangeInfo = $this->_exchange->record($activity_id, $rule['prize_id'], $prizeInfo, $prizeCode, $identity_id, $user_info, $identityContact, $isValid, $source, $memo);
                if (! empty($exchangeInfo)) {
                    $exchangeInfo['exchange_id'] = $exchangeInfo['_id'];
                } else {
                    throw new \Exception('中奖信息记录失败', - 7);
                }
                $this->_exchange->commit();
                $this->_record->record($activity_id, $identity_id, $source, 1, "恭喜您中奖了！");
                $ret['result'] = $exchangeInfo;
            } catch (\Exception $e) {
                $this->_exchange->rollback();
                $this->_record->record($activity_id, $identity_id, $source, $e->getCode(), $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            $ret['error_code'] = $e->getCode();
            $ret['error_msg'] = $e->getMessage();
        }
        return $ret;
    }
}
<?php
namespace App\Campaign\Controllers;

/**
 * 签到事例
 *
 * @author Administrator
 *        
 */
class SignController extends ControllerBase
{

    private $modelSign;

    private $modelLog;

    public function initialize()
    {
        $this->modelSign = new \App\Sign\Models\Sign();
        $this->modelLog = new \App\Sign\Models\Log();
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 签到接口
     */
    public function doAction()
    {
        // http://www.applicationmodule.com/campaign/sign/do?activity_id=xxx&user_id=xxxx
        try {
            $activity_id = $this->get("activity_id", '');
            $user_id = trim($this->get('user_id', ''));
            if (empty($user_id)) {
                echo ($this->error(- 1, "用户ID不能为空"));
                return false;
            }
            if (empty($activity_id)) {
                echo ($this->error(- 2, "活动ID不能为空"));
                return false;
            }
            
            // 限制检查
            $this->modelLimit->setLogModel($this->modelLog);
            $isPassed = $this->modelLimit->checkLimit($activity_id, $subjectId, $itemId, $user_id, 1, array(
                $activity_id
            ), array(
                $subjectId
            ));
            if (! $isPassed) { // 未通过
                echo ($this->error(- 8, "无法再次投票"));
                return false;
            }
            
            // 增加log
            $this->modelLog->log($activity_id, $subjectId, $itemId, $user_id);
            
            // 发送成功
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}
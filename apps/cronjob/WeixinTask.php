<?php
class WeixinTask extends \Phalcon\CLI\Task
{
    // 监控任务
    private $activity_id = 6;

    /**
     * 获取accesstoken
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixin getaccesstoken
     * @param array $params            
     */
    public function getaccesstokenAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();

        try {
            $modelAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
            $now = \App\Common\Utils\Helper::getCurrentTime($now);
            $query = array(
                'access_token_expire' => array(
                    '$lte' => $now
                )
            );
            $sort = array('_id' => 1);
            $authorizerList = $modelAuthorizer->findAll($query, $sort);

            if (!empty($authorizerList)) {
                foreach ($authorizerList as $authorizerItem) {

                    // 进行锁定处理
                    $component_appid = $authorizerItem['component_appid'];
                    $appid = $authorizerItem['appid'];

                    $lock = new \iLock(cacheKey(__FILE__, __CLASS__, __METHOD__, 'component_appid:' . $component_appid . ' appid:' . $appid));
                    $lock->setExpire(3600);
                    if ($lock->lock()) {
                        continue;
                    }

                    try {
                        // 更新
                        $modelAuthorizer->getTokenByAppid($component_appid, $appid);
                    } catch (\Exception $e) {
                        $modelActivityErrorLog->log($this->activity_id, $e, $now);
                    }
                }
            }
        } catch (\Exception $e) {
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }
    }

    /**
     * 获取关注用户列表
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixin getsubscribeuserlist
     * @param array $params            
     */
    public function getsubscribeuserlistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();

        try {
            $modelAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
            $query = array();
            $sort = array('_id' => 1);
            $authorizerList = $modelAuthorizer->findAll($query, $sort);

            if (!empty($authorizerList)) {
                foreach ($authorizerList as $authorizerItem) {

                    // 进行锁定处理
                    $component_appid = $authorizerItem['component_appid'];
                    $authorizer_appid = $authorizerItem['appid'];

                    $lock = new \iLock(cacheKey(__FILE__, __CLASS__, __METHOD__, 'component_appid:' . $component_appid . ' authorizer_appid:' . $authorizer_appid));
                    $lock->setExpire(3600);
                    if ($lock->lock()) {
                        continue;
                    }

                    try {
                        // 获取
                        $weixinopenService = new \App\Weixin2\Services\WeixinService($authorizer_appid, $component_appid);
                        $weixinopenService->syncSubscribeUserList($now, $authorizerItem['preview_openid']);
                    } catch (\Exception $e) {
                        $modelActivityErrorLog->log($this->activity_id, $e, $now);
                    }
                }
            }
        } catch (\Exception $e) {
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }
    }
}

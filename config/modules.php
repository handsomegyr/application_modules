<?php
/**
 * Register application modules
 */
$application->registerModules(array(
    'admin' => array(
        'className' => 'App\Backend\Module',
        'path' => APP_PATH . 'apps/backend/Module.php'
    ),
    'admin/prize' => array(
        'className' => 'App\Backend\Prize\Module',
        'path' => APP_PATH . 'apps/backend/submodules/prize/Module.php'
    ),
    'admin/exchange' => array(
        'className' => 'App\Backend\Exchange\Module',
        'path' => APP_PATH . 'apps/backend/submodules/exchange/Module.php'
    ),
    'admin/activity' => array(
        'className' => 'App\Backend\Activity\Module',
        'path' => APP_PATH . 'apps/backend/submodules/activity/Module.php'
    ),
    'admin/weixin' => array(
        'className' => 'App\Backend\Weixin\Module',
        'path' => APP_PATH . 'apps/backend/submodules/weixin/Module.php'
    ),
    'admin/lottery' => array(
        'className' => 'App\Backend\Lottery\Module',
        'path' => APP_PATH . 'apps/backend/submodules/lottery/Module.php'
    ),
    'admin/system' => array(
        'className' => 'App\Backend\System\Module',
        'path' => APP_PATH . 'apps/backend/submodules/system/Module.php'
    ),
    'admin/goods' => array(
        'className' => 'App\Backend\Goods\Module',
        'path' => APP_PATH . 'apps/backend/submodules/goods/Module.php'
    ),
    'admin/tencent' => array(
        'className' => 'App\Backend\Tencent\Module',
        'path' => APP_PATH . 'apps/backend/submodules/tencent/Module.php'
    ),
    'admin/mail' => array(
        'className' => 'App\Backend\Mail\Module',
        'path' => APP_PATH . 'apps/backend/submodules/mail/Module.php'
    ),
    'admin/sms' => array(
        'className' => 'App\Backend\Sms\Module',
        'path' => APP_PATH . 'apps/backend/submodules/sms/Module.php'
    ),
    'admin/message' => array(
        'className' => 'App\Backend\Message\Module',
        'path' => APP_PATH . 'apps/backend/submodules/message/Module.php'
    ),
    'admin/payment' => array(
        'className' => 'App\Backend\Payment\Module',
        'path' => APP_PATH . 'apps/backend/submodules/payment/Module.php'
    ),
    'admin/member' => array(
        'className' => 'App\Backend\Member\Module',
        'path' => APP_PATH . 'apps/backend/submodules/member/Module.php'
    ),
    'admin/points' => array(
        'className' => 'App\Backend\Points\Module',
        'path' => APP_PATH . 'apps/backend/submodules/points/Module.php'
    ),
    'admin/invitation' => array(
        'className' => 'App\Backend\Invitation\Module',
        'path' => APP_PATH . 'apps/backend/submodules/invitation/Module.php'
    ),
    'admin/store' => array(
        'className' => 'App\Backend\Store\Module',
        'path' => APP_PATH . 'apps/backend/submodules/store/Module.php'
    ),
    'admin/site' => array(
        'className' => 'App\Backend\Site\Module',
        'path' => APP_PATH . 'apps/backend/submodules/site/Module.php'
    ),
    'admin/post' => array(
        'className' => 'App\Backend\Post\Module',
        'path' => APP_PATH . 'apps/backend/submodules/post/Module.php'
    ),
    'admin/order' => array(
        'className' => 'App\Backend\Order\Module',
        'path' => APP_PATH . 'apps/backend/submodules/order/Module.php'
    ),
    'admin/article' => array(
        'className' => 'App\Backend\Article\Module',
        'path' => APP_PATH . 'apps/backend/submodules/article/Module.php'
    ),
    'admin/freight' => array(
        'className' => 'App\Backend\Freight\Module',
        'path' => APP_PATH . 'apps/backend/submodules/freight/Module.php'
    ),
    'admin/vote' => array(
        'className' => 'App\Backend\Vote\Module',
        'path' => APP_PATH . 'apps/backend/submodules/vote/Module.php'
    ),
    'admin/questionnaire' => array(
        'className' => 'App\Backend\Questionnaire\Module',
        'path' => APP_PATH . 'apps/backend/submodules/questionnaire/Module.php'
    ),
    'admin/task' => array(
        'className' => 'App\Backend\Task\Module',
        'path' => APP_PATH . 'apps/backend/submodules/task/Module.php'
    ),
    'admin/weixinredpack' => array(
        'className' => 'App\Backend\Weixinredpack\Module',
        'path' => APP_PATH . 'apps/backend/submodules/weixinredpack/Module.php'
    ),
    'admin/weixincard' => array(
        'className' => 'App\Backend\Weixincard\Module',
        'path' => APP_PATH . 'apps/backend/submodules/weixincard/Module.php'
    ),
    'admin/bargain' => array(
        'className' => 'App\Backend\Bargain\Module',
        'path' => APP_PATH . 'apps/backend/submodules/bargain/Module.php'
    ),
    'admin/cronjob' => array(
        'className' => 'App\Backend\Cronjob\Module',
        'path' => APP_PATH . 'apps/backend/submodules/cronjob/Module.php'
    ),
    'admin/sign' => array(
        'className' => 'App\Backend\Sign\Module',
        'path' => APP_PATH . 'apps/backend/submodules/sign/Module.php'
    ),
    'admin/alipay' => array(
        'className' => 'App\Backend\Alipay\Module',
        'path' => APP_PATH . 'apps/backend/submodules/alipay/Module.php'
    ),
    'admin/live' => array(
        'className' => 'App\Backend\Live\Module',
        'path' => APP_PATH . 'apps/backend/submodules/live/Module.php'
    ),
    'weixin' => array(
        'className' => 'App\Weixin\Module',
        'path' => APP_PATH . 'apps/weixin/Module.php'
    ),
    'exchange' => array(
        'className' => 'App\Exchange\Module',
        'path' => APP_PATH . 'apps/exchange/Module.php'
    ),
    'prize' => array(
        'className' => 'App\Prize\Module',
        'path' => APP_PATH . 'apps/prize/Module.php'
    ),
    'system' => array(
        'className' => 'App\System\Module',
        'path' => APP_PATH . 'apps/system/Module.php'
    ),
    'invitation' => array(
        'className' => 'App\Invitation\Module',
        'path' => APP_PATH . 'apps/invitation/Module.php'
    ),
    'payment' => array(
        'className' => 'App\Payment\Module',
        'path' => APP_PATH . 'apps/payment/Module.php'
    ),
    'lottery' => array(
        'className' => 'App\Lottery\Module',
        'path' => APP_PATH . 'apps/lottery/Module.php'
    ),
    'service' => array(
        'className' => 'App\Service\Module',
        'path' => APP_PATH . 'apps/service/Module.php'
    ),
    'points' => array(
        'className' => 'App\Points\Module',
        'path' => APP_PATH . 'apps/points/Module.php'
    ),
    'tencent' => array(
        'className' => 'App\Tencent\Module',
        'path' => APP_PATH . 'apps/tencent/Module.php'
    ),
    'order' => array(
        'className' => 'App\Order\Module',
        'path' => APP_PATH . 'apps/order/Module.php'
    ),
    'member' => array(
        'className' => 'App\Member\Module',
        'path' => APP_PATH . 'apps/member/Module.php'
    ),
    'goods' => array(
        'className' => 'App\Goods\Module',
        'path' => APP_PATH . 'apps/goods/Module.php'
    ),
    'store' => array(
        'className' => 'App\Store\Module',
        'path' => APP_PATH . 'apps/store/Module.php'
    ),
    'site' => array(
        'className' => 'App\Site\Module',
        'path' => APP_PATH . 'apps/site/Module.php'
    ),
    'message' => array(
        'className' => 'App\Message\Module',
        'path' => APP_PATH . 'apps/message/Module.php'
    ),
    'post' => array(
        'className' => 'App\Post\Module',
        'path' => APP_PATH . 'apps/post/Module.php'
    ),
    'article' => array(
        'className' => 'App\Article\Module',
        'path' => APP_PATH . 'apps/article/Module.php'
    ),
    'freight' => array(
        'className' => 'App\Freight\Module',
        'path' => APP_PATH . 'apps/freight/Module.php'
    ),
    'task' => array(
        'className' => 'App\Task\Module',
        'path' => APP_PATH . 'apps/task/Module.php'
    ),
    'yungou' => array(
        'className' => 'App\Yungou\Module',
        'path' => APP_PATH . 'apps/yungou/Module.php'
    ),
    'mail' => array(
        'className' => 'App\Mail\Module',
        'path' => APP_PATH . 'apps/mail/Module.php'
    ),
    'sms' => array(
        'className' => 'App\Sms\Module',
        'path' => APP_PATH . 'apps/sms/Module.php'
    ),
    'campaign' => array(
        'className' => 'App\Campaign\Module',
        'path' => APP_PATH . 'apps/campaign/Module.php'
    ),
    'install' => array(
        'className' => 'App\Install\Module',
        'path' => APP_PATH . 'apps/install/Module.php'
    ),
    'alipay' => array(
        'className' => 'App\Alipay\Module',
        'path' => APP_PATH . 'apps/alipay/Module.php'
    ),
    'live' => array(
        'className' => 'App\Live\Module',
        'path' => APP_PATH . 'apps/live/Module.php'
    ),
));
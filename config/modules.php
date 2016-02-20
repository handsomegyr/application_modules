<?php
/**
 * Register application modules
 */
$application->registerModules(array(
    'admin' => array(
        'className' => 'Webcms\Backend\Module',
        'path' => APP_PATH . 'apps/backend/Module.php'
    ),
    'admin/prize' => array(
        'className' => 'Webcms\Backend\Prize\Module',
        'path' => APP_PATH . 'apps/backend/submodules/prize/Module.php'
    ),
    'admin/exchange' => array(
        'className' => 'Webcms\Backend\Exchange\Module',
        'path' => APP_PATH . 'apps/backend/submodules/exchange/Module.php'
    ),
    'admin/weixin' => array(
        'className' => 'Webcms\Backend\Weixin\Module',
        'path' => APP_PATH . 'apps/backend/submodules/weixin/Module.php'
    ),
    'admin/lottery' => array(
        'className' => 'Webcms\Backend\Lottery\Module',
        'path' => APP_PATH . 'apps/backend/submodules/lottery/Module.php'
    ),
    'admin/system' => array(
        'className' => 'Webcms\Backend\System\Module',
        'path' => APP_PATH . 'apps/backend/submodules/system/Module.php'
    ),
    'admin/goods' => array(
        'className' => 'Webcms\Backend\Goods\Module',
        'path' => APP_PATH . 'apps/backend/submodules/goods/Module.php'
    ),
    'admin/tencent' => array(
        'className' => 'Webcms\Backend\Tencent\Module',
        'path' => APP_PATH . 'apps/backend/submodules/tencent/Module.php'
    ),
    'admin/mail' => array(
        'className' => 'Webcms\Backend\Mail\Module',
        'path' => APP_PATH . 'apps/backend/submodules/mail/Module.php'
    ),
    'admin/sms' => array(
        'className' => 'Webcms\Backend\Sms\Module',
        'path' => APP_PATH . 'apps/backend/submodules/sms/Module.php'
    ),
    'admin/message' => array(
        'className' => 'Webcms\Backend\Message\Module',
        'path' => APP_PATH . 'apps/backend/submodules/message/Module.php'
    ),
    'admin/payment' => array(
        'className' => 'Webcms\Backend\Payment\Module',
        'path' => APP_PATH . 'apps/backend/submodules/payment/Module.php'
    ),
    'admin/member' => array(
        'className' => 'Webcms\Backend\Member\Module',
        'path' => APP_PATH . 'apps/backend/submodules/member/Module.php'
    ),
    'admin/points' => array(
        'className' => 'Webcms\Backend\Points\Module',
        'path' => APP_PATH . 'apps/backend/submodules/points/Module.php'
    ),
    'admin/invitation' => array(
        'className' => 'Webcms\Backend\Invitation\Module',
        'path' => APP_PATH . 'apps/backend/submodules/invitation/Module.php'
    ),
    'admin/store' => array(
        'className' => 'Webcms\Backend\Store\Module',
        'path' => APP_PATH . 'apps/backend/submodules/store/Module.php'
    ),
    'admin/site' => array(
        'className' => 'Webcms\Backend\Site\Module',
        'path' => APP_PATH . 'apps/backend/submodules/site/Module.php'
    ),
    'admin/post' => array(
        'className' => 'Webcms\Backend\Post\Module',
        'path' => APP_PATH . 'apps/backend/submodules/post/Module.php'
    ),
    'admin/order' => array(
        'className' => 'Webcms\Backend\Order\Module',
        'path' => APP_PATH . 'apps/backend/submodules/order/Module.php'
    ),
    'admin/article' => array(
        'className' => 'Webcms\Backend\Article\Module',
        'path' => APP_PATH . 'apps/backend/submodules/article/Module.php'
    ),
    'admin/freight' => array(
        'className' => 'Webcms\Backend\Freight\Module',
        'path' => APP_PATH . 'apps/backend/submodules/freight/Module.php'
    ),
    'admin/task' => array(
        'className' => 'Webcms\Backend\Task\Module',
        'path' => APP_PATH . 'apps/backend/submodules/task/Module.php'
    ),
    'weixin' => array(
        'className' => 'Webcms\Weixin\Module',
        'path' => APP_PATH . 'apps/weixin/Module.php'
    ),
    'exchange' => array(
        'className' => 'Webcms\Exchange\Module',
        'path' => APP_PATH . 'apps/exchange/Module.php'
    ),
    'prize' => array(
        'className' => 'Webcms\Prize\Module',
        'path' => APP_PATH . 'apps/prize/Module.php'
    ),
    'system' => array(
        'className' => 'Webcms\System\Module',
        'path' => APP_PATH . 'apps/system/Module.php'
    ),
    'invitation' => array(
        'className' => 'Webcms\Invitation\Module',
        'path' => APP_PATH . 'apps/invitation/Module.php'
    ),
    'payment' => array(
        'className' => 'Webcms\Payment\Module',
        'path' => APP_PATH . 'apps/payment/Module.php'
    ),
    'lottery' => array(
        'className' => 'Webcms\Lottery\Module',
        'path' => APP_PATH . 'apps/lottery/Module.php'
    ),
    'service' => array(
        'className' => 'Webcms\Service\Module',
        'path' => APP_PATH . 'apps/service/Module.php'
    ),
    'points' => array(
        'className' => 'Webcms\Points\Module',
        'path' => APP_PATH . 'apps/points/Module.php'
    ),
    'tencent' => array(
        'className' => 'Webcms\Tencent\Module',
        'path' => APP_PATH . 'apps/tencent/Module.php'
    ),
    'order' => array(
        'className' => 'Webcms\Order\Module',
        'path' => APP_PATH . 'apps/order/Module.php'
    ),
    'member' => array(
        'className' => 'Webcms\Member\Module',
        'path' => APP_PATH . 'apps/member/Module.php'
    ),
    'goods' => array(
        'className' => 'Webcms\Goods\Module',
        'path' => APP_PATH . 'apps/goods/Module.php'
    ),
    'store' => array(
        'className' => 'Webcms\Store\Module',
        'path' => APP_PATH . 'apps/store/Module.php'
    ),
    'site' => array(
        'className' => 'Webcms\Site\Module',
        'path' => APP_PATH . 'apps/site/Module.php'
    ),
    'message' => array(
        'className' => 'Webcms\Message\Module',
        'path' => APP_PATH . 'apps/message/Module.php'
    ),
    'post' => array(
        'className' => 'Webcms\Post\Module',
        'path' => APP_PATH . 'apps/post/Module.php'
    ),
    'article' => array(
        'className' => 'Webcms\Article\Module',
        'path' => APP_PATH . 'apps/article/Module.php'
    ),
    'freight' => array(
        'className' => 'Webcms\Freight\Module',
        'path' => APP_PATH . 'apps/freight/Module.php'
    ),
    'task' => array(
        'className' => 'Webcms\Task\Module',
        'path' => APP_PATH . 'apps/task/Module.php'
    ),
    'yungou' => array(
        'className' => 'Webcms\Yungou\Module',
        'path' => APP_PATH . 'apps/yungou/Module.php'
    ),
    'mail' => array(
        'className' => 'Webcms\Mail\Module',
        'path' => APP_PATH . 'apps/mail/Module.php'
    ),
    'sms' => array(
        'className' => 'Webcms\Sms\Module',
        'path' => APP_PATH . 'apps/sms/Module.php'
    )
));
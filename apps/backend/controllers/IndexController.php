<?php

namespace App\Backend\Controllers;

use App\Backend\Models\User;
use App\Backend\Models\Input;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * @title({name="管理中心"})
 *
 * @name 管理中心
 */
class IndexController extends \App\Backend\Controllers\ControllerBase
{

    protected $formName = "首页";

    /**
     * @var \App\Backend\Models\User
     */
    private $modelUser = NULL;

    public function initialize()
    {
        $this->modelUser = new User();
        parent::initialize();
    }

    /**
     * @title({name="显示首页"})
     *
     * @name 显示首页
     */
    public function indexAction()
    {
        session_destroy();
        $this->view->setVar('formName', '首页');
        $envs = [
            ['name' => 'PHP version',       'value' => 'PHP/' . PHP_VERSION],
            ['name' => 'Laravel version',   'value' => '5.8.35'],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => $_SERVER['SERVER_SOFTWARE']],

            ['name' => 'Cache driver',      'value' => 'memcached'],
            ['name' => 'Session driver',    'value' => 'memcached'],
            ['name' => 'Queue driver',      'value' => 'redis'],

            ['name' => 'Timezone',          'value' => 'Asia/Shanghai'],
            ['name' => 'Locale',            'value' => 'zh-CN'],
            ['name' => 'Env',               'value' => 'local'],
            ['name' => 'URL',               'value' => '/admin/index/index'],
        ];

        $this->view->setVar('envs', $envs);

        $extensions = [
            'helpers' => [
                'name' => 'laravel-admin-ext/helpers',
                'link' => 'https://github.com/laravel-admin-extensions/helpers',
                'icon' => 'gears',
            ],
            'log-viewer' => [
                'name' => 'laravel-admin-ext/log-viewer',
                'link' => 'https://github.com/laravel-admin-extensions/log-viewer',
                'icon' => 'database',
            ],
            'backup' => [
                'name' => 'laravel-admin-ext/backup',
                'link' => 'https://github.com/laravel-admin-extensions/backup',
                'icon' => 'copy',
            ],
            'config' => [
                'name' => 'laravel-admin-ext/config',
                'link' => 'https://github.com/laravel-admin-extensions/config',
                'icon' => 'toggle-on',
            ],
            'api-tester' => [
                'name' => 'laravel-admin-ext/api-tester',
                'link' => 'https://github.com/laravel-admin-extensions/api-tester',
                'icon' => 'sliders',
            ],
            'media-manager' => [
                'name' => 'laravel-admin-ext/media-manager',
                'link' => 'https://github.com/laravel-admin-extensions/media-manager',
                'icon' => 'file',
            ],
            'scheduling' => [
                'name' => 'laravel-admin-ext/scheduling',
                'link' => 'https://github.com/laravel-admin-extensions/scheduling',
                'icon' => 'clock-o',
            ],
            'reporter' => [
                'name' => 'laravel-admin-ext/reporter',
                'link' => 'https://github.com/laravel-admin-extensions/reporter',
                'icon' => 'bug',
            ],
            'redis-manager' => [
                'name' => 'laravel-admin-ext/redis-manager',
                'link' => 'https://github.com/laravel-admin-extensions/redis-manager',
                'icon' => 'flask',
            ],
        ];

        $installedExtensions = array();
        foreach ($extensions as &$extension) {
            $name = explode('/', $extension['name']);
            $extension['installed'] = array_key_exists(end($name), $installedExtensions);
        }
        $this->view->setVar('extensions', $extensions);

        $json = file_get_contents(APP_PATH . 'composer.json');

        $dependencies = json_decode($json, true)['require'];

        // Admin::script("$('.dependencies').slimscroll({height:'510px',size:'3px'});");

        $this->view->setVar('dependencies', $dependencies);
    }

    /**
     * @title({name="注销"})
     *
     * @name 注销
     */
    public function logoutAction()
    {
        try {
            session_destroy();
            $this->modelUser->clearCookies();
            $url = $this->getUrl("login");
            $this->_redirect($url);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="显示登录页面"})
     *
     * @name 显示登录页面
     */
    public function loginAction()
    {
        try {
            $this->disableLayout();
            $this->view->setVar('form_act', $this->getUrl("signin"));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="登录"})
     *
     * @name 登录
     */
    public function signinAction()
    {
        session_unset();
        try {
            $this->view->disable();
            $input = $this->getLoginFilterInput();
            if ($input->isValid()) {

                $token = $this->request->get('_token', array(
                    'trim'
                ), '');
                $this->checkToken($token);
                /* 检查密码是否正确 */
                $userInfo = $this->modelUser->checkLogin($input->username, $input->password);
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            // 登陆处理
            $this->modelUser->login($userInfo);
            // 登录成功
            if (intval($input->remember)) {
                $this->modelUser->storeInCookies($userInfo);
            }

            $url = $this->getUrl("index");
            //$this->_redirect($url);

            unset($_SESSION['toastr']);
            $_SESSION['toastr']['type'] = "success";
            $_SESSION['toastr']['message'] = '登录成功!';
            $_SESSION['toastr']['options'] = array();

            // 不是来自于其他系统登录
            $_SESSION['is_login_from_other_system'] = false;

            // 返回信息
            $ret = array();
            $ret['redirect'] = $url;
            $this->makeJsonResult($ret, 'ok');
        } catch (\Exception $e) {
            // die($e->getMessage());
            // throw $e;
            $this->makeJsonError($e->getMessage());
        }
    }

    public function signin4othersAction()
    {
        session_unset();
        try {
            $this->view->disable();
            $username = $this->request->get('username', array(
                'trim',
                'string'
            ), '');
            $password = $this->request->get('password', array(
                'trim',
                'string'
            ), '');

            /* 检查密码是否正确 */
            $userInfo = $this->modelUser->checkLogin($username, $password);

            // 登陆处理
            $this->modelUser->login($userInfo);

            // 从其他系统进行登录的
            $_SESSION['is_login_from_other_system'] = true;

            // 返回信息
            $ret = $_SESSION;
            return $this->makeJsonResult($ret, 'ok');
        } catch (\Exception $e) {
            // die($e->getMessage());
            // throw $e;
            return $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="会话保持"})
     *
     * @name 会话保持
     */
    public function keeploginAction()
    {
        $this->view->disable();
        ini_set('default_socket_timeout', -1); // 不超时
        // 返回信息
        $ret = array();
        $ret['token'] = $_SESSION['csrf_token'];
        $this->makeJsonResult($ret, 'ok');
    }

    protected function getLoginFilterInput()
    {
        $input = new Input();
        $input->username = $this->request->get('username', array(
            'trim',
            'string'
        ), '');
        $input->password = $this->request->get('password', array(
            'trim',
            'string'
        ), '');
        $input->captcha = $this->request->get('captcha', array(
            'trim',
            'string'
        ), '');
        $input->remember = $this->request->get('remember', array(
            'trim',
            'int'
        ), 0);

        $input->isValid = function ($fieldName = null) use ($input) {
            $data = $this->request->get();
            $validation = new Validation();

            $validation->add('username', new PresenceOf(array(
                'message' => 'You must enter your username.'
            )));

            $validation->add('password', new PresenceOf(array(
                'message' => 'You must enter your password.'
            )));

            $messages = $validation->validate($data);
            $messages = $messages->filter($fieldName);
            $input->messages = $messages;
            if (!empty($messages)) {
                return false;
            } else {
                return true;
            }
        };

        $input->getMessages = function () use ($input) {
            return empty($input->messages) ? array() : $input->messages;
        };

        return $input;
    }
}

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

    protected $formName = "Dashboard";

    private $modelUser = NULL;

    public function initialize()
    {
        $this->modelUser = new User();
        parent::initialize();
    }

    /**
     * @title({name="显示Dashboard页面"})
     *
     * @name 显示Dashboard页面
     */
    public function indexAction()
    {}

    /**
     * @title({name="注销"})
     *
     * @name 注销
     */
    public function logoutAction()
    {
        try {
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
        try {
            $input = $this->getLoginFilterInput();
            if ($input->isValid()) {
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
            $this->_redirect($url);
        } catch (\Exception $e) {
            die($e->getMessage());
            throw $e;
        }
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
        
        $input->isValid = function ($fieldName = null) use($input)
        {
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
            if (! empty($messages)) {
                return false;
            } else {
                return true;
            }
        };
        
        $input->getMessages = function () use($input)
        {
            return empty($input->messages) ? array() : $input->messages;
        };
        
        return $input;
    }
}


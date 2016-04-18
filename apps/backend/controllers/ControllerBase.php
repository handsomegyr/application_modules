<?php
namespace App\Backend\Controllers;

use Phalcon\Mvc\View;

class ControllerBase extends \App\Common\Controllers\ControllerBase
{

    protected function initialize()
    {
        parent::initialize();
        
        $this->tag->prependTitle('INVO | ');
        $this->view->setVar("resourceUrl", "/backend/metronic.bootstrap/");
    }

    protected function _getValidationMessage($input)
    {
        $messageInfo = "";
        foreach ($input->getMessages() as $messageID => $message) {
            if (is_array($message)) {
                $messageInfo .= "Validation failure '$messageID':<br/>";
                foreach ($message as $key => $value) {
                    $messageInfo .= "Validation failure '$key': $value<br/>";
                }
            } else {
                $messageInfo .= "Validation failure '$messageID': $message<br/>";
            }
        }
        return $messageInfo;
    }

    public function sysMsg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = true)
    {
        if (count($links) == 0) {
            $links[0]['text'] = '返回上一页';
            $links[0]['href'] = 'javascript:history.go(-1)';
        }
        
        $this->view->setVar('ur_here', '系统信息');
        $this->view->setVar('msg_detail', $msg_detail);
        $this->view->setVar('msg_type', $msg_type);
        $this->view->setVar('links', $links);
        $this->view->setVar('default_url', $links[0]['href']);
        $this->view->setVar('auto_redirect', $auto_redirect);
        // $this->view->pick("error/message");
        $this->view->partial("error/message");
    }

    public function makeJsonResult($content = '', $message = '', $append = array())
    {
        $this->makeJsonResponse($content, 0, $message, $append);
    }

    public function makeJsonError($msg)
    {
        $this->makeJsonResponse('', 1, $msg);
    }

    /**
     * 创建一个JSON格式的数据
     *
     * @access public
     * @param string $content            
     * @param integer $error            
     * @param string $message            
     * @param array $append            
     * @return void
     */
    public function makeJsonResponse($content = '', $error = "0", $message = '', $append = array())
    {
        $res = array(
            'error' => $error,
            'message' => $message,
            'content' => $content
        );
        
        if (! empty($append)) {
            foreach ($append as $key => $val) {
                $res[$key] = $val;
            }
        }
        $this->response->setJsonContent($res)->send();
    }

    public function disableLayout()
    {
        $this->view->disableLevel(array(
            View::LEVEL_LAYOUT => true,
            View::LEVEL_MAIN_LAYOUT => true
        ));
    }
}

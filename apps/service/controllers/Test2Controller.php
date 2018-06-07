<?php
namespace App\Service\Controllers;

class Test2Controller extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    public function indexAction()
    {
        try {
            // http://www.jizigou.com/service/test2/index
            // http://www.applicationmodule.com/service/test2/index
            $modelMsgCount = new \App\Message\Models\MsgCount(\App\Common\Models\Base\Base::MONGODB);
            // $modelMsgCount->setDebug(true);
            $modelMsgCount->setPhql(false);
            
            echo ("<br/>save start:<br/>");
            $datas = array(
                'user_id' => 'guoyongrong' . uniqid(),
                'sysMsgCount' => 1,
                'privMsgCount' => 2,
                'friendMsgCount' => 3,
                'replyMsgCount' => 4
            );
            
            $ret = $modelMsgCount->save($datas);
            print_r($ret);
            $id = $ret['_id'];
            echo ("<br/>save end <br/>");
            
            echo ("<br/>insert start:<br/>");
            $datas = array(
                'user_id' => 'guoyongrong' . uniqid(),
                'sysMsgCount' => 1,
                'privMsgCount' => 2,
                'friendMsgCount' => 3,
                'replyMsgCount' => 4
            );
            
            $ret = $modelMsgCount->insert($datas);
            print_r($ret);
            $id = $ret['_id'];
            echo ("<br/>insert end <br/>");
            
            echo ("<br/>update start:<br/>");
            $query = array(
                '_id' => $id
            );
            $datas = array(
                'sysMsgCount' => 4,
                'privMsgCount' => 3,
                'friendMsgCount' => 2,
                'replyMsgCount' => 1
            );
            $ret = $modelMsgCount->update($query, array(
                '$set' => $datas
            ));
            print_r($ret);
            echo ("<br/>update end <br/>");
            
            echo ("<br/>count start:<br/>");
            $num = $modelMsgCount->count(array());
            echo ('count1:' . $num . "<br/>");
            $num = $modelMsgCount->count(array(
                '_id' => 'xxxxxxx'
            ));
            echo ('count2:' . $num . "<br/>");
            echo ("<br/>count end <br/>");
            
            echo ("<br/>findOne start:<br/>");
            $info = $modelMsgCount->findOne(array(
                '_id' => $id
            ));
            echo ("findOne1:<br/>");
            print_r($info);
            echo ("<br/>");
            $info = $modelMsgCount->findOne(array(
                '_id' => 'xxxxxxx'
            ));
            echo ("findOne2:<br/>");
            print_r($info);
            echo ("<br/>findOne end <br/>");
            
            echo ("<br/>find start:<br/>");
            $list = $modelMsgCount->find(array(
                '_id' => $id
            ), array(
                '_id' => - 1,
                '__MODIFY_TIME__' => 1
            ), 0, 1);
            
            echo ("find1:<br/>");
            print_r($list);
            echo ("<br/>");
            
            $list = $modelMsgCount->find(array(
                '_id' => 'xxxxxxx'
            ), array(
                '_id' => - 1,
                '__MODIFY_TIME__' => 1
            ), 0, 1);
            
            echo ("find2:<br/>");
            print_r($list);
            echo ("<br/>find end <br/>");
            
            echo ("<br/>findAll start:<br/>");
            $list = $modelMsgCount->findAll(array(
                '_id' => $id
            ), array(
                '_id' => - 1,
                '__MODIFY_TIME__' => 1
            ));
            
            echo ("findAll1:<br/>");
            print_r($list);
            echo ("<br/>");
            
            $list = $modelMsgCount->findAll(array(
                '_id' => 'xxxxxxx'
            ), array(
                '_id' => - 1,
                '__MODIFY_TIME__' => 1
            ));
            
            echo ("findAll2:<br/>");
            print_r($list);
            echo ("<br/>findAll end <br/>");
            
            echo ("<br/>distinct start:<br/>");
            $list = $modelMsgCount->distinct('user_id', array(
                '_id' => $id
            ));
            
            echo ("distinct1:<br/>");
            print_r($list);
            echo ("<br/>");
            
            $list = $modelMsgCount->distinct('user_id', array(
                '_id' => 'xxxxxxx'
            ));
            
            echo ("distinct2:<br/>");
            print_r($list);
            echo ("<br/>distinct end <br/>");
            
            echo ("<br/>sum start:<br/>");
            $fields = array(
                'sysMsgCount'
            );
            $groups = array(
                '_id'
            );
            
            $list = $modelMsgCount->sum(array(
                '_id' => $id
            ), $fields, $groups);
            
            echo ("sum1:<br/>");
            print_r($list);
            echo ("<br/>");
            
            $list = $modelMsgCount->sum(array(
                '_id' => 'xxxxxxx'
            ), $fields, $groups);
            
            echo ("sum2:<br/>");
            print_r($list);
            echo ("<br/>sum end <br/>");
            
            echo ("<br/>findAndModify start:<br/>");
            $total_amount = 2;
            $options = array();
            $options['query'] = array(
                '_id' => $id,
                'sysMsgCount' => array(
                    '$gte' => $total_amount
                )
            );
            $options['update'] = array(
                '$inc' => array(
                    'privMsgCount' => $total_amount,
                    'sysMsgCount' => - $total_amount
                )
            );
            $options['new'] = true; // 返回更新之后的值
            $rst = $modelMsgCount->findAndModify($options);
            if (empty($rst['ok'])) {
                throw new \Exception("findAndModify执行错误，返回结果为:" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new \Exception("findAndModify执行错误，返回结果为:" . json_encode($rst));
            }
            $ret = $rst['value'];
            print_r($ret);
            echo ("<br/>");
            echo ("<br/>findAndModify end<br/>");
            
            echo ("<br/>remove start:<br/>");
            $query = array(
                '_id' => $id
            );
            $ret = $modelMsgCount->remove($query);
            print_r($ret);
            die("<br/>remove end <br/>");
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}


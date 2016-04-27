<?php
namespace App\Weixinredpack\Models;

class User extends \App\Common\Models\Weixinredpack\User
{

    public function record($FromUserName, $re_openid)
    {
        $datas = array(
            'FromUserName' => $FromUserName,
            're_openid' => $re_openid
        );
        return $this->insert($datas);
    }

    public function getInfoByFromUserName($FromUserName)
    {
        $info = $this->findOne(array(
            'FromUserName' => $FromUserName
        ));
        return $info;
    }

    public function updateReopenid($id, $re_openid)
    {
        $data = array(
            're_openid' => $re_openid
        );
        $this->update(array(
            '_id' => $id
        ), array(
            '$set' => $data
        ));
    }
    
    public function updateWithdrawDate($id, $is_withdrawed_date)
    {
        $data = array(
            'is_withdrawed_date' => $is_withdrawed_date
        );
        $this->update(array(
            '_id' => $id
        ), array(
            '$set' => $data
        ));
    }
    
}
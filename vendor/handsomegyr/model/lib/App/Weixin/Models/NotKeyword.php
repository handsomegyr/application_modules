<?php
namespace App\Weixin\Models;

class NotKeyword extends \App\Common\Models\Weixin\NotKeyword
{

    public function record($msg)
    {
        $query = array(
            'msg' => $msg
        );
        $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__, $msg);
        $cache = $this->getDI()->get('cache'); // Zend_Registry::get('cache');
        $count = $cache->get($cacheKey);
        if (empty($count)) {
            $count = $this->count($query);
            $expire_time = 3600; // 1小时
            $cache->save($cacheKey, $count, $expire_time);
        }
        if ($count > 0) {
            $this->update($query, array(
                '$inc' => array(
                    'times' => 1
                )
            ));
        } else {
            $data = array();
            $data['msg'] = $msg;
            $data['times'] = 1;
            $this->insert($data);
        }
    }
}
<?php
namespace App\Member\Models;

class Report extends \App\Common\Models\Member\Report
{

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    public function log($from_user_id, $from_user_nickname, $from_user_email, $from_user_mobile, $from_user_register_by, $to_user_id, $to_user_nickname, $to_user_email, $to_user_mobile, $to_user_register_by, $type, $content)
    {
        $data = array();
        $data['from_user_id'] = $from_user_id;
        $data['from_user_nickname'] = $from_user_nickname;
        $data['from_user_email'] = $from_user_email;
        $data['from_user_mobile'] = $from_user_mobile;
        $data['from_user_register_by'] = $from_user_register_by;
        $data['to_user_id'] = $to_user_id;
        $data['to_user_nickname'] = $to_user_nickname;
        $data['to_user_email'] = $to_user_email;
        $data['to_user_mobile'] = $to_user_mobile;
        $data['to_user_register_by'] = $to_user_register_by;
        $data['type'] = $type;
        $data['content'] = $content;
        $data['report_time'] = getCurrentTime();
        return $this->insert($data);
    }

    /**
     * 忽略好友
     *
     * @param string $to_user_id            
     * @param string $applyId            
     */
    public function ignore($to_user_id, $applyId)
    {
        $query = array();
        if (! empty($applyId)) {
            $query['_id'] = $applyId;
        }
        $query['to_user_id'] = $to_user_id;
        return $this->remove($query);
    }

    /**
     * 同意好友
     *
     * @param string $to_user_id            
     * @param string $applyId            
     */
    public function agree($to_user_id, $applyId)
    {
        $query = array();
        if (! empty($applyId)) {
            $query['_id'] = $applyId;
        }
        $query['to_user_id'] = $to_user_id;
        $data = array();
        $data['state'] = self::STATE1;
        $data['agree_time'] = getCurrentTime();
        return $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 删除好友
     *
     * @param string $to_user_id            
     * @param string $from_user_id            
     */
    public function delete($to_user_id, $from_user_id)
    {
        $query1 = array();
        $query1['to_user_id'] = $to_user_id;
        $query1['from_user_id'] = $from_user_id;
        
        $query2 = array();
        $query2['from_user_id'] = $to_user_id;
        $query2['to_user_id'] = $from_user_id;
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        return $this->remove($query);
    }

    public function check($from_user_id, $to_user_id)
    {
        $query1 = array();
        $query1['from_user_id'] = $from_user_id;
        $query1['to_user_id'] = $to_user_id;
        
        $query2 = array();
        $query2['from_user_id'] = $to_user_id;
        $query2['to_user_id'] = $from_user_id;
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        
        return $this->findOne($query);
    }
}
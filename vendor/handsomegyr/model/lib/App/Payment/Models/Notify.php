<?php
namespace App\Payment\Models;

class Notify extends \App\Common\Models\Payment\Notify
{

    /**
     * 默认排序方式
     *
     * @param number $dir            
     * @return array
     */
    public function getDefaultSort()
    {
        $sort = array();
        $sort['notify_time'] = - 1;
        return $sort;
    }

    /**
     * 默认查询条件
     *
     * @return array
     */
    public function getDefaultQuery()
    {
        $query = array();
        return $query;
    }

    public function recordLog($out_trade_no, $content)
    {
        $data = array();
        $data['out_trade_no'] = $out_trade_no;
        $data['content'] = $content;
        $data['notify_time'] = getCurrentTime();
        return $this->insert($data);
    }
}
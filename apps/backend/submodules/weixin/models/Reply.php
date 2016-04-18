<?php
namespace App\Backend\Models\Weixin;

class Reply extends \App\Common\Models\Weixin\Reply
{
    
    use \App\Backend\Models\Base;

    /**
     * 获取全部回复类型
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['_id']] = $item['keyword'];
        }
        return $list;
    }
}
<?php
namespace Webcms\Backend\Models\Weixin;

class MsgType extends \Webcms\Common\Models\Weixin\MsgType
{
    
    use \Webcms\Backend\Models\Base;

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
            $list[$item['value']] = $item['key'];
        }
        return $list;
    }
}
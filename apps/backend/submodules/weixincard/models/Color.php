<?php
namespace App\Backend\Submodules\Weixincard\Models;

class Color extends \App\Common\Models\Weixincard\Color
{
    
    use \App\Backend\Models\Base;

    /**
     * 获取全部
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
            $list[$item['value']] = $item['name'];
        }
        return $list;
    }

    /**
     * 记录
     *
     * @param string $value            
     * @param string $name            
     */
    public function record($value, $name)
    {
        $data = array();
        $data['value'] = strtoupper($value);
        $data['name'] = $name;
        $this->insert($data);
    }
}
<?php
namespace Webcms\System\Models;

class Source extends \Webcms\Common\Models\System\Source
{

    /**
     * 获取全部来源类型
     */
    public function getSource()
    {
        $rst = $this->findAll(array());
        return array_map(function ($row)
        {
            return $row['value'];
        }, $rst);
    }
}
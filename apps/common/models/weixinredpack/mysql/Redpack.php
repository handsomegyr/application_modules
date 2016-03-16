<?php
namespace Webcms\Common\Models\Mysql\Weixinredpack;

use Webcms\Common\Models\Mysql\Base;

class Redpack extends Base
{

    /**
     * 微信红包-红包信息
     * This model is mapped to the table iweixinredpack_redpack
     */
    public function getSource()
    {
        return 'iweixinredpack_redpack';
    }
}

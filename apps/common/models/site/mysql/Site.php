<?php
namespace Webcms\Common\Models\Mysql\Site;

use Webcms\Common\Models\Mysql\Base;

class Site extends Base
{

    /**
     * 网站-网站表管理
     * This model is mapped to the table isite_site
     */
    public function getSource()
    {
        return 'isite_site';
    }
}
<?php
namespace Webcms\Common\Models\Mysql\Article;

use Webcms\Common\Models\Mysql\Base;

class Category extends Base
{

    /**
     * 文章-分类管理
     * This model is mapped to the table iarticle_category
     */
    public function getSource()
    {
        return 'iarticle_category';
    }
}
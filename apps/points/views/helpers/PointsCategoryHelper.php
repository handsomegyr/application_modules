<?php
namespace Webcms\Points\Helpers;

use Webcms\Points\Models\Category;

class PointsCategoryHelper extends \Phalcon\Tag
{

    /**
     * 获取所有
     *
     * @return array
     */
    static public function getAll()
    {
        $modelPointsCategory = new Category();
        return $modelPointsCategory->getAll();
    }
}
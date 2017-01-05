<?php
namespace App\System\Views\Helpers;

use App\System\Models\Area;

class AreaHelper extends \Phalcon\Tag
{

    /**
     * 获取省份列表
     *
     * @return array
     */
    static public function getProvinceList()
    {
        $modelArea = new Area();
        return $modelArea->getProvinces();
    }
    
    /**
     * 获取省份列表
     *
     * @return array
     */
    static public function getAddress($province, $city, $district, $address, $separator = " ")
    {
        $modelArea = new Area();
        return $modelArea->getAddress($province, $city, $district, $address, $separator);
    }
}
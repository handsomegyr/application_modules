<?php
namespace App\System\Views\Helpers;

use App\System\Models\Enum;

class EnumHelper extends \Phalcon\Tag
{

    /**
     * 获取性别列表
     *
     * @return array
     */
    static public function getSexList()
    {
        $modelEnum = new Enum();
        return $modelEnum->getSexList();
    }

    /**
     * 获取星座列表
     *
     * @return array
     */
    static public function getConstellationList()
    {
        $modelEnum = new Enum();
        $constellationList = $modelEnum->getConstellationList();
        return $modelEnum->getConstellationList();
    }

    /**
     * 获取月收入列表
     *
     * @return array
     */
    static public function getMonthlyIncomeList()
    {
        $modelEnum = new Enum();
        return $modelEnum->getMonthlyIncomeList();
    }
}
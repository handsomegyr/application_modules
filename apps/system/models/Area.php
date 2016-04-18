<?php
namespace App\System\Models;

class Area extends \App\Common\Models\System\Area
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'name' => 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 获取省份列表
     *
     * @return array
     */
    public function getProvinces()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $provinceList = $cache->get($key);
        if (empty($provinceList)) {
            $query = $this->getQuery();
            $query['level'] = 1;
            $sort = $this->getDefaultSort();
            $list = $this->findAll($query, $sort);
            $provinceList = array();
            if (! empty($list)) {
                foreach ($list as $item) {
                    $provinceList[$item['code']] = $item['name'];
                }
            }
            if (! empty($provinceList)) {
                $cache->save($key, $provinceList, 60 * 60 * 24); // 24小时
            }
        }
        return $provinceList;
    }

    /**
     * 获取城市列表
     *
     * @return array
     */
    public function getCitys($province)
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $province);
        $cache = $this->getDI()->get("cache");
        $provinceList = $cache->get($key);
        if (empty($provinceList)) {
            $query = $this->getQuery();
            $query['parent_code'] = $province;
            $query['level'] = 2;
            $sort = $this->getDefaultSort();
            $list = $this->findAll($query, $sort);
            $provinceList = array();
            if (! empty($list)) {
                foreach ($list as $item) {
                    $provinceList[$item['code']] = $item['name'];
                }
            }
            if (! empty($provinceList)) {
                $cache->save($key, $provinceList, 60 * 60 * 24); // 24小时
            }
        }
        return $provinceList;
    }

    /**
     * 获取区县列表
     *
     * @return array
     */
    public function getDistricts($city)
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $city);
        $cache = $this->getDI()->get("cache");
        $provinceList = $cache->get($key);
        if (empty($provinceList)) {
            
            $query = $this->getQuery();
            $query['parent_code'] = $city;
            $query['level'] = 3;
            $sort = $this->getDefaultSort();
            $list = $this->findAll($query, $sort);
            $provinceList = array();
            if (! empty($list)) {
                foreach ($list as $item) {
                    $provinceList[$item['code']] = $item['name'];
                }
            }
            if (! empty($provinceList)) {
                $cache->save($key, $provinceList, 60 * 60 * 24); // 24小时
            }
        }
        return $provinceList;
    }

    public function getAddress($province, $city, $district, $address, $separator = " ")
    {
        $provinceList = $this->getProvinces();
        $cityList = $this->getCitys($province);
        $districtList = $this->getDistricts($city);
        $provinceName = $provinceList[$province];
        $cityName = $cityList[$city];
        if ($cityName == '市辖区' || $cityName == '县') {
            $cityName = "";
        }
        if (! empty($district)) {
            $districtName = $districtList[$district];
        } else {
            $districtName = "";
        }
        return $provinceName . $separator . $cityName . $separator . $districtName . $separator . $address;
    }
}
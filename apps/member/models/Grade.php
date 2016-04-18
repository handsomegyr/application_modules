<?php
namespace App\Member\Models;

class Grade extends \App\Common\Models\Member\Grade
{

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    public function getDefaultSort()
    {
        $sort = array();
        $sort['level'] = - 1;
        return $sort;
    }

    /**
     * 获取所有列表信息
     *
     * @return array
     */
    public function getAll()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $gradeList = $cache->get($key);
        if (empty($gradeList)) {
            $query = $this->getQuery();
            $sort = $this->getDefaultSort();
            $list = $this->findAll($query, $sort);
            $gradeList = array();
            if (! empty($list)) {
                foreach ($list as $item) {
                    $gradeList[$item['_id']] = $item;
                }
            }
            if (! empty($gradeList)) {
                $cache->save($key, $gradeList, 60 * 60 * 24 * 30); // 30天
            }
        }
        return $gradeList;
    }

    /**
     * 根据经验值，获取会员等级信息
     *
     * @return array
     */
    public function getGradeInfo($exp = 0)
    {
        $gradeList = $this->getAll();
        $gradeId = "";
        foreach ($gradeList as $key => $grade) {
            if ($grade['exp_from'] <= $exp && $exp < $grade['exp_to']) {
                return array(
                    'current' => $grade,
                    'next' => isset($gradeList[$gradeId]) ? $gradeList[$gradeId] : null
                );
            } else {
                $gradeId = $key;
            }
        }
    }
}
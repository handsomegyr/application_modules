<?php
namespace Webcms\Vote\Models;

class Period extends \Webcms\Common\Models\Vote\Period
{

    /**
     * 根据主题ID获取信息
     *
     * @param string $subject_id            
     * @return array
     */
    public function getInfoBySubject($subject_id)
    {
        $query = array(
            'subject_id' => $subject_id
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据主题ID获取最新的排行期数
     *
     * @param string $subject_id            
     * @return number
     */
    public function getLatestPeriod($subject_id)
    {
        $info = $this->getInfoBySubject($subject_id);
        if (empty($info)) {
            $data = array();
            $data['subject_id'] = $subject_id;
            $data['period'] = 1;
            $info = $this->insert($data);
            return $info['period'];
        } else {
            $options = array(
                "query" => array(
                    "_id" => $info['_id']
                ),
                "update" => array(
                    '$inc' => array(
                        'period' => 1
                    )
                ),
                "new" => true
            );
            $return_result = $this->findAndModify($options);
            return $return_result["value"]['period'];
        }
    }

    /**
     * 获取列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditon            
     * @param array $sort            
     * @param array $cacheInfo            
     * @return array
     */
    public function getList($page = 1, $limit = 10, array $otherConditon = array(), array $sort = null, array $cacheInfo = array('isCache'=>false,'cacheKey'=>null,'expire_time'=>null))
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort(- 1);
        }
        $condition = array();
        if (! empty($otherConditon)) {
            $condition = array_merge($condition, $otherConditon);
        }
        $list = array();
        
        if (! empty($cacheInfo) && ! empty($cacheInfo['isCache']) && ! empty($cacheInfo['cacheKey'])) {
            $cache = Zend_Registry::get('cache');
            $cacheKey = md5($cacheInfo['cacheKey'] . 'page' . $page . 'limit' . $limit . "_condition_" . md5(serialize($condition)) . "_sort_" . md5(serialize($sort)));
            $list = $cache->load($cacheKey);
        }
        
        if (empty($list)) {
            $list = $this->find($condition, $sort, ($page - 1) * $limit, $limit);
        }
        
        if (! empty($cacheInfo) && ! empty($cacheInfo['isCache']) && ! empty($cacheInfo['cacheKey'])) {
            $cache->save($list, $cacheKey, array(), empty($cacheInfo['expire_time']) ? null : $cacheInfo['expire_time']);
        }
        
        return array(
            'condition' => $condition,
            'list' => $list
        );
    }
}
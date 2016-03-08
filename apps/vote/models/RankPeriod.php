<?php
namespace Webcms\Vote\Models;

class RankPeriod extends \Webcms\Common\Models\Vote\RankPeriod
{

    protected $name = 'iVote_rank_period';

    protected $dbName = 'vote';

    /**
     * 默认排序
     *
     * @param number $sort            
     * @return array
     */
    public function getDefaultSort($sort = -1)
    {
        $sort = array(
            'period' => - 1,
            'vote_count' => - 1,
            'show_order' => - 1,
            '_id' => $sort
        );
        return $sort;
    }

    /**
     * 生成
     *
     * @param string $subject_id            
     * @param number $period            
     * @param string $name            
     * @param string $desc            
     * @param array $subjects            
     * @param number $vote_count            
     * @param boolean $is_closed            
     * @param number $show_order            
     * @param array $memo            
     * @return array
     */
    public function create($subject_id, $period, $name, $desc, array $subjects, $vote_count, $show_order, array $memo)
    {
        $data = array();
        $data['subject_id'] = $subject_id;
        $data['period'] = $period;
        $data['name'] = $name;
        $data['desc'] = $desc;
        $data['subjects'] = $subjects;
        $data['vote_count'] = intval($vote_count);
        $data['show_order'] = intval($show_order);
        $data['memo'] = $memo;
        
        $info = $this->insert($data);
        return $info;
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
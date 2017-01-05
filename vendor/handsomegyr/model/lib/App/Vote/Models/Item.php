<?php
namespace App\Vote\Models;

class Item extends \App\Common\Models\Vote\Item
{

    /**
     * 默认排序
     *
     * @param number $sort            
     * @return array
     */
    public function getDefaultSort($sort = -1)
    {
        $sort = array(
            'show_order' => - 1,
            '_id' => $sort
        );
        return $sort;
    }

    /**
     * 根据投票数排序
     *
     * @param number $sort            
     * @return array
     */
    public function getRankSort($sort = -1)
    {
        $sort = array(
            'vote_count' => $sort
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array(
            "is_closed" => false
        ); // 显示
        return $query;
    }

    /**
     * 根据name获取信息
     *
     * @param string $name            
     * @return array
     */
    public function getInfoByName($name)
    {
        $query = array(
            'name' => $name
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据name列表获取信息
     *
     * @param string $name            
     * @return array
     */
    public function getListByNames(array $nameList)
    {
        $query = array(
            'name' => array(
                '$in' => $nameList
            )
        );
        $ret = $this->findAll($query);
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $item) {
                $list[$item['name']] = $item;
            }
        }
        return $list;
    }

    /**
     * 根据主题ID获取投票项目列表
     *
     * @param string $activityId            
     * @return array
     */
    public function getListBySubjectId($subjectId)
    {
        $query = $this->getQuery();
        $query['subject_id'] = $subjectId;
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        return $ret;
    }

    /**
     * 增加投票数
     *
     * @param string $itemId            
     * @param number $vote_count            
     */
    public function incVoteCount($itemId, $vote_count = 1)
    {
        $query = array(
            '_id' => ($itemId)
        );
        $this->update($query, array(
            '$inc' => array(
                'vote_count' => $vote_count
            )
        ));
    }

    /**
     * 我的排名
     *
     * @param array $myInfo            
     * @param array $otherConditions            
     * @return number
     */
    public function getRank($myInfo, array $otherConditions = array())
    {
        $query = $this->getQuery();
        $query['_id'] = array(
            '$ne' => $myInfo['_id']
        );
        $query['vote_count'] = array(
            '$gt' => $myInfo['vote_count']
        ); // 按投票次数
        if (! empty($otherConditions)) {
            foreach ($otherConditions as $key => $value) {
                $query[$key] = $value;
            }
        }
        $num = $this->count($query);
        return $num + 1;
    }

    /**
     * 生成投票项
     *
     * @param string $name            
     * @param string $desc            
     * @param array $subjects            
     * @param number $vote_count            
     * @param boolean $is_closed            
     * @param number $show_order            
     * @param array $memo            
     * @return array
     */
    public function create($name, $desc, array $subjects, $vote_count = 0, $is_closed = false, $show_order = 0, array $memo = array())
    {
        $data = array();
        $data['name'] = $name;
        $data['desc'] = $desc;
        $data['subjects'] = $subjects;
        $data['vote_count'] = intval($vote_count);
        $data['is_closed'] = $is_closed;
        $data['show_order'] = intval($show_order);
        $data['rank_period'] = 0;
        $data['memo'] = $memo;
        
        $info = $this->insert($data);
        return $info;
    }

    /**
     * 设置排行期数
     *
     * @param string $itemId            
     * @param number $rank_period            
     */
    public function updateRankPeriod($itemId, $rank_period)
    {
        $query = array(
            '_id' => ($itemId)
        );
        $this->update($query, array(
            '$set' => array(
                'rank_period' => $rank_period
            )
        ));
    }

    /**
     * 按照名字设置排行期数
     *
     * @param string $name            
     * @param number $rank_period            
     */
    public function updateRankPeriodByName($name, $rank_period)
    {
        $query = array(
            'name' => $name
        );
        $this->update($query, array(
            '$set' => array(
                'rank_period' => $rank_period
            )
        ));
    }

    /**
     * 切换显示状态
     *
     * @param string $id            
     * @param boolean $is_closed            
     */
    public function toggleIsClosed($id, $is_closed = false)
    {
        $data = array();
        $data['is_closed'] = $is_closed;
        $this->update(array(
            "_id" => ($id)
        ), array(
            '$set' => $data
        ));
    }

    /**
     * 获取排名,排除了重复名称
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditon            
     * @param array $sort            
     * @param array $cacheInfo            
     * @throws Exception
     * @return array
     */
    public function getRankList($page = 1, $limit = 10, array $otherConditon = array(), array $sort = null, array $cacheInfo = array('isCache'=>false,'cacheKey'=>null,'expire_time'=>null))
    {
        if (empty($sort)) {
            $sort = array(
                'vote_count' => - 1
            );
        }
        $condition = $this->getQuery();
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
            $rst = $this->aggregate(array(
                array(
                    '$match' => $condition
                ),
                array(
                    '$sort' => $sort
                ),
                array(
                    '$group' => array(
                        '_id' => '$name',
                        'value' => array(
                            '$first' => array(
                                'name' => '$name',
                                'vote_count' => '$vote_count',
                                'desc' => '$desc',
                                'subjects' => '$subjects',
                                '__CREATE_TIME__' => '$__CREATE_TIME__',
                                '_id' => '$_id',
                                'is_closed' => '$is_closed',
                                'show_order' => '$show_order',
                                'rank_period' => '$rank_period',
                                'memo' => '$memo'
                            )
                        )
                    )
                ),
                array(
                    '$sort' => array(
                        'value.vote_count' => - 1,
                        'value.__CREATE_TIME__' => 1
                    )
                ),
                array(
                    '$skip' => ($page - 1) * $limit
                ),
                array(
                    '$limit' => $limit
                )
            ));
            
            if (empty($rst['ok'])) {
                throw new \Exception("获取排名失败");
            }
            
            if (empty($rst['result'])) {
                throw new \Exception("获取排名失败");
            }
            
            $list = $rst['result'];
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
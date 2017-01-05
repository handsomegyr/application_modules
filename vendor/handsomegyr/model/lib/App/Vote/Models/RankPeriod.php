<?php
namespace App\Vote\Models;

class RankPeriod extends \App\Common\Models\Vote\RankPeriod
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
     * @param number $vote_count            
     * @param boolean $is_closed            
     * @param number $show_order            
     * @param array $memo            
     * @return array
     */
    public function create($subject_id, $period, $name, $desc, $vote_count, $show_order, array $memo)
    {
        $data = array();
        $data['subject_id'] = $subject_id;
        $data['period'] = $period;
        $data['name'] = $name;
        $data['desc'] = $desc;
        $data['vote_count'] = intval($vote_count);
        $data['show_order'] = intval($show_order);
        $data['memo'] = $memo;
        
        $info = $this->insert($data);
        return $info;
    }

}
<?php
namespace App\Questionnaire\Models;

class QuestionItem extends \App\Common\Models\Questionnaire\QuestionItem
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
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 根据题目ID获取选项列表
     *
     * @param string $question_id            
     * @return array
     */
    public function getListByQuestionId($question_id)
    {
        $query = $this->getQuery();
        $query['question_id'] = $question_id;
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        return $ret;
    }

    /**
     * 根据题目IDs获取选项列表
     *
     * @param string $question_ids            
     * @return array
     */
    public function getListByQuestionIds(array $question_ids)
    {
        $query = $this->getQuery();
        $query['question_id'] = array(
            '$in' => $question_ids
        );
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        return $ret;
    }

    /**
     * 增加使用数
     *
     * @param string $itemId            
     * @param number $num            
     */
    public function incUsedCount($itemId, $num = 1)
    {
        $query = array(
            '_id' => ($itemId)
        );
        $this->update($query, array(
            '$inc' => array(
                'used_times' => $num
            )
        ));
    }
}
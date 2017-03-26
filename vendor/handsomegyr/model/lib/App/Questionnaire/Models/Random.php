<?php
namespace App\Questionnaire\Models;

class Random extends \App\Common\Models\Questionnaire\Random
{

    /**
     * 获取随机问卷
     *
     * @param string $user_id            
     * @param string $questionnaire_id            
     * @param boolean $is_finish            
     * @return array
     */
    public function getInfoByUserIdAndQuestionnaireId($user_id, $questionnaire_id, $is_finish = false)
    {
        $result = $this->findOne(array(
            'user_id' => $user_id,
            'questionnaire_id' => $questionnaire_id,
            'is_finish' => $is_finish
        ));
        return $result;
    }

    /**
     * 生成随机问卷
     *
     * @param string $user_id            
     * @param string $questionnaire_id            
     * @param array $question_ids            
     */
    public function create($user_id, $questionnaire_id, array $question_ids)
    {
        $data = array();
        $data['user_id'] = $user_id;
        $data['questionnaire_id'] = $questionnaire_id;
        $data['question_ids'] = json_encode($question_ids);
        $data['is_finish'] = false;
        return $this->insert($data);
    }

    public function finish($randomId)
    {
        $rst = $this->update(array(
            '_id' => $randomId,
            'is_finish' => false
        ), array(
            '$set' => array(
                'is_finish' => true,
                'finish_time' => new \MongoDate()
            )
        ));
        
        return $rst;
    }
}
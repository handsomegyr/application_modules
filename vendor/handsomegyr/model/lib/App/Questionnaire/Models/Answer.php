<?php
namespace App\Questionnaire\Models;

class Answer extends \App\Common\Models\Questionnaire\Answer
{

    public function record($userId, $user_name, $user_headimgurl, $questionnaire_id, $random_id, array $answer_list, $score, $question_num, $correct_num, $wrong_num, $noanswer_num, array $memo = array())
    {
        $data = array();
        $data['user_id'] = $userId;
        $data['user_name'] = $user_name;
        $data['user_headimgurl'] = $user_headimgurl;
        $data['questionnaire_id'] = $questionnaire_id;
        $data['random_id'] = $random_id;
        $data['answer_list'] = $answer_list;
        $data['score'] = $score;
        
        $data['question_num'] = $question_num;
        $data['correct_num'] = $correct_num;
        $data['wrong_num'] = $wrong_num;
        $data['noanswer_num'] = $noanswer_num;
        
        $data['answer_time'] = new \MongoDate();
        $data['memo'] = $memo;
        return $this->insert($data);
    }
}
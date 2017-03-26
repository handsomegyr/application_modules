<?php
namespace App\Questionnaire\Models;

class Question extends \App\Common\Models\Questionnaire\Question
{

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

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
     * 根据问卷ID获取题目列表
     *
     * @param string $questionnaireId            
     * @return array
     */
    public function getListByQuestionnaireId($questionnaireId)
    {
        $query = $this->getQuery();
        $query['questionnaire_id'] = $questionnaireId;
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        return $ret;
    }

    /**
     * 根据问卷ID获取随机题目列表
     *
     * @param string $questionnaireId            
     * @return array
     */
    public function getRandomListByQuestionnaireId($questionnaireId, $randomNum)
    {
        // 获取所有的题目列表
        $list = $this->getListByQuestionnaireId($questionnaireId);
        
        $ret = array();
        if (! empty($list)) {
            $rand_keys = array_rand($list, $randomNum);
            if (! is_array($rand_keys)) {
                $rand_keys = array(
                    $rand_keys
                );
            }
            foreach ($rand_keys as $key) {
                $ret[] = $list[$key];
            }
        }
        return $ret;
    }

    public function checkAnswers(array $questionList, array $userAnswers)
    {
        $score = 0;
        $correct_num = 0;
        $wrong_num = 0;
        $noanswer_num = 0;
        $answerList = array();
        $question_num = 0;
        foreach ($questionList as $questionInfo) {
            $question_num ++;
            if (isset($userAnswers[$questionInfo['_id']])) {
                // 检查答案
                $ret = $this->checkAnswer($questionInfo, $userAnswers[$questionInfo['_id']]);
                $answerList[] = $ret;
                
                $score += $ret['score'];
                $correct_num += $ret['correct_num'];
                $wrong_num += $ret['wrong_num'];
            } else {
                $noanswer_num ++;
            }
        }
        // $correct = round($correct_num * 100 / $question_num);
        return array(
            'score' => $score,
            'correct_num' => $correct_num,
            'wrong_num' => $wrong_num,
            'noanswer_num' => $noanswer_num,
            // 'correct' => $correct,
            'answer_list' => $answerList,
            'question_num' => $question_num
        );
    }

    /**
     * 验证答题 //暂不考虑填空题
     *
     * @param array $questionInfo            
     * @param array $userAnswer            
     */
    protected function checkAnswer(array $questionInfo, array $userAnswer)
    {
        $correct_num = 0;
        $wrong_num = 0;
        $score = 0;
        $result = false;
        
        // 有正确答案，进行判断
        if (! empty($questionInfo['correct_answer'])) {
            $arrAnswerKey = array();
            foreach ($userAnswer as $answerInfo) {
                $arrAnswerKey[] = trim(strtoupper($answerInfo['key']));
            }
            asort($arrAnswerKey); // 排序
            $strAnswerKey = implode('', $arrAnswerKey);
            
            // 逗号分隔
            $arrCorrectkey = explode(',', trim($questionInfo['correct_answer']));
            foreach ($arrCorrectkey as &$key) {
                $key = trim(strtoupper($key));
            }
            asort($arrCorrectkey); // 排序
            $strCorrectKey = implode('', $arrCorrectkey);
            
            if (trim($strCorrectKey) == trim($strAnswerKey)) {
                $correct_num ++;
                $result = true;
                $score = $questionInfo['score'];
            } else {
                $wrong_num ++;
            }
        } else {
            // 无正确答案，默认都正确
            $result = true;
            $score = $questionInfo['score'];
            $correct_num ++;
        }
        $ret = array(
            'question_id' => $questionInfo['_id'],
            'answers' => $userAnswer,
            'result' => $result,
            'score' => intval($score),
            'wrong_num' => $wrong_num,
            'correct_num' => $correct_num
        );
        return $ret;
    }

    /**
     * 增加正确错误数
     *
     * @param string $id            
     * @param boolean $is_right            
     * @param number $num            
     */
    public function incCorrectWrongCount($id, $is_right = true, $num = 1)
    {
        $query = array(
            '_id' => ($id)
        );
        $this->update($query, array(
            '$inc' => array(
                ($is_right ? 'correct_times' : 'wrong_times') => $num
            )
        ));
    }
}
<?php

namespace App\Campaign\Controllers;

/**
 * 问卷调查事例
 *
 * @author Administrator
 *        
 */
class QuestionnaireController extends ControllerBase
{

    private $modelQuestionnaire;

    private $modelQuestionItem;

    private $modelQuestion;

    private $modelAnswer;

    private $modelRandom;

    protected function doCampaignInitialize()
    {
        $this->modelQuestionnaire = new \App\Questionnaire\Models\Questionnaire();
        $this->modelQuestionItem = new \App\Questionnaire\Models\QuestionItem();
        $this->modelQuestion = new \App\Questionnaire\Models\Question();
        $this->modelAnswer = new \App\Questionnaire\Models\Answer();
        $this->modelRandom = new \App\Questionnaire\Models\Random();
        $this->view->disable();
    }

    /**
     * 获取问卷接口
     */
    public function getAction()
    {
        // http://www.myapplicationmodule.com/campaign/questionnaire/get?questionnaireId=58af94ff6a2e0e0b008b4567&userId=xxx
        try {
            $userId = $this->get("userId", '');
            if (empty($userId)) {
                echo ($this->error(-1, "用户ID不能为空"));
                return false;
            }
            $questionnaireId = $this->get("questionnaireId", '');
            if (empty($questionnaireId)) {
                echo ($this->error(-2, "问卷ID不能为空"));
                return false;
            }

            // 获取问卷信息
            $questionnaireInfo = $this->modelQuestionnaire->getInfoById($questionnaireId);
            if (empty($questionnaireInfo)) {
                echo ($this->error(-3, "该问卷ID的问卷不存在"));
                return false;
            }
            // 获取问卷题目和选项
            $questions = array();
            if (empty($questionnaireInfo['is_rand'])) {
                $randomId = '';
                // 非随机，获取所有题目
                $questionList = $this->modelQuestion->getListByQuestionnaireId($questionnaireId);
            } else {
                // 检查是否存在未完成的随机问卷
                $randomInfo = $this->modelRandom->getInfoByUserIdAndQuestionnaireId($userId, $questionnaireId, false);
                // 没有未完成的随机问卷
                if (empty($randomInfo)) {
                    // 不存在，重新生成一套随机问卷
                    $questionList = $this->modelQuestion->getRandomListByQuestionnaireId($questionnaireId, $questionnaireInfo['rand_number']);
                    if (!empty($questionList)) {
                        $questionIds = array();
                        foreach ($questionList as $quesionInfo) {
                            $questionIds[] = $quesionInfo['_id'];
                        }
                        // 生成
                        $randomInfo = $this->modelRandom->create($userId, $questionnaireId, $questionIds);
                    }
                } else {
                    // 存在的话
                    $questionIds = $randomInfo['question_ids'];
                    $questionList = $this->modelQuestion->getListByIds($questionIds);
                }
                $randomId = $randomInfo['_id'];
            }

            if (empty($questionList)) {
                echo ($this->error(-4, "该问卷的题目未设置"));
                return false;
            }
            // 获取所有的题目选项
            foreach ($questionList as $questionInfo) {
                $questionItemList = $this->modelQuestionItem->getListByQuestionId($questionInfo['_id']);
                if (empty($questionItemList)) {
                    echo ($this->error(-5, "该问卷的问卷题目的选项未设置"));
                    return false;
                }
                $items = array();
                foreach ($questionItemList as $questionItemInfo) {
                    $items[] = array(
                        'question_item_id' => $questionItemInfo['_id'],
                        'key' => $questionItemInfo['key'],
                        'score' => $questionItemInfo['score'],
                        'content' => $questionItemInfo['content'],
                        'pic_url' => $questionItemInfo['pic_url'],
                        'video_url' => $questionItemInfo['video_url'],
                        'voice_url' => $questionItemInfo['voice_url'],
                        'is_other' => $questionItemInfo['is_other'],
                        'next_question_id' => $questionItemInfo['next_question_id'],
                    );
                }
                $questions[] = array(
                    'question_id' => $questionInfo['_id'],
                    'name' => $questionInfo['name'],
                    'correct_answer' => $questionInfo['correct_answer'],
                    'type' => $questionInfo['question_type'],
                    'is_required' => $questionInfo['is_required'],
                    'content' => $questionInfo['content'],
                    'picture' => $questionInfo['picture'],
                    'video' => $questionInfo['video'],
                    'voice' => $questionInfo['voice'],
                    'score' => $questionInfo['score'],
                    'question_category' => intval($questionInfo['question_category']),
                    'show_style' => intval($questionInfo['show_style']),
                    'next_question_id' => $questionInfo['next_question_id'],
                    'items' => $items
                );
            }
            $ret = array();
            $ret['questionnaireInfo'] = array(
                'name' => $questionnaireInfo['name'],
                'randomId' => $randomId,
                'questions' => $questions
            );
            // 发送成功
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取问卷接口
     */
    public function answerAction()
    {
        // http://www.myapplicationmodule.com/campaign/questionnaire/answer?questionnaireId=58af94ff6a2e0e0b008b4567&userId=xxx&nickname=guoyongrong&headimgurl=xxx&randomId=&answers={"58afa31a6a2e0e00018b4567":[{key:"A",content:''}],"58b64bda89972ff1008b4567":[{key:"A",content:''},{key:"B",content:''},{key:"C",content:'海南'}]}
        try {
            $userId = $this->get("userId", '');
            $nickname = $this->get("nickname", '');
            $headimgurl = $this->get("headimgurl", '');
            if (empty($userId)) {
                echo ($this->error(-1, "用户ID不能为空"));
                return false;
            }
            $questionnaireId = $this->get("questionnaireId", '');
            if (empty($questionnaireId)) {
                echo ($this->error(-2, "问卷ID不能为空"));
                return false;
            }
            $randomId = $this->get("randomId", '');
            // $answers = $this->get("answers", '');
            $answers = '{
    "58afa31a6a2e0e00018b4567": [
        {
            "item_id": "58afa85e6a2e0e01018b4568", 
            "key": "A", 
            "content": ""
        }
    ], 
    "58b64bda89972ff1008b4567": [
        {
            "item_id": "58b64bf589972fef008b4567",
            "key": "A", 
            "content": ""
        }, 
        {
            "item_id": "58b64c0989972ff3008b4567",
            "key": "B", 
            "content": ""
        }, 
        {
            "item_id": "58b64c1b89972fee008b4567",
            "key": "C", 
            "content": "海南"
        }
    ]
}';
            $answers = json_decode($answers, true);
            if (empty($answers)) {
                echo ($this->error(-3, "该问卷的答题信息不能为空"));
                return false;
            }

            // 答题
            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $userId, $questionnaireId);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-40499, "上次操作还未完成,请等待");
                return false;
            }

            // 获取问卷信息
            $questionnaireInfo = $this->modelQuestionnaire->getInfoById($questionnaireId);
            if (empty($questionnaireInfo)) {
                echo ($this->error(-5, "该问卷ID的问卷不存在"));
                return false;
            }

            // 如果是随机问卷的话
            if (!empty($questionnaireInfo['is_rand'])) {
                if (empty($randomId)) {
                    echo ($this->error(-4, "随机问卷ID不能为空"));
                    return false;
                }
                $randomInfo = $this->modelRandom->getInfoById($randomId);
                if (empty($randomInfo)) {
                    echo ($this->error(-6, "该随机问卷不存在"));
                    return false;
                }
                if ($randomInfo['questionnaire_id'] != $questionnaireId || $randomInfo['user_id'] != $userId) {
                    echo ($this->error(-7, "该随机问卷不存在"));
                    return false;
                }
                if (!empty($randomInfo['is_finish'])) {
                    echo ($this->error(-8, "该随机问卷已答题过了"));
                    return false;
                }
            }

            // 根据阅卷ID获取题目列表
            $questionList = $this->modelQuestion->getListByQuestionnaireId($questionnaireInfo['_id']);
            if (empty($questionList)) {
                echo ($this->error(-9, "该问卷的题目列表不存在"));
                return false;
            }
            // 检查用户答案
            $ret = $this->modelQuestion->checkAnswers($questionList, $answers);

            // 完成随机问卷
            if ($randomId) {
                $this->modelRandom->finish($randomId, $this->now);
            }
            // 记录答案
            $answerInfo = $this->modelAnswer->record($userId, $nickname, $headimgurl, $questionnaireId, $randomId, $ret['answer_list'], $ret['score'], $ret['question_num'], $ret['correct_num'], $ret['wrong_num'], $ret['noanswer_num'], $this->now);

            // 进行统计处理
            if (!empty($ret['answer_list'])) {
                foreach ($ret['answer_list'] as $question) {
                    foreach ($question['answers'] as $item) {
                        $this->modelQuestionItem->incUsedCount($item['item_id'], 1);
                    }
                    $this->modelQuestion->incCorrectWrongCount($question['question_id'], $question['result'], 1);
                }
            }
            // 发送成功
            echo ($this->result("OK", array(
                'answerInfo' => $ret
            )));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}

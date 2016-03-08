<?php
namespace Webcms\Vote\Controllers;

class IndexController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 首页
     */
    public function indexAction()
    {
        $num = 0;
        $limit = 3;
        do {
            $num ++;
            echo 'num:' . $num . '<br/>';
            if ($num >= $limit) { // 如果到达上限的时候
                break;
            }
        } while (1);
        die('aaaaaaaaaaaa');
        // 获取某活动下的所有投票主题
        $activityId = "53d091d64a9619e6538b459e";
        $modelSubject = new Vote_Model_Subject();
        $subjectList = $modelSubject->getListByActivityId($activityId);
        // echo json_encode($subjectList);
        // die;
        
        // 根据主题获取该主题下的所有投票选项
        $modelItem = new Vote_Model_Item();
        $itemList = array();
        foreach ($subjectList as $subject) {
            $subjectId = ($subject['_id']);
            $itemList[$subjectId] = $modelItem->getListBySubjectId($subjectId);
        }
        echo json_encode($itemList);
        die();
    }

    /**
     * 投票接口
     */
    public function voteAction()
    {
        $modelActivity = new Vote_Model_Activity();
        $modelSubject = new Vote_Model_Subject();
        $modelItem = new Vote_Model_Item();
        $modelLog = new Vote_Model_Log();
        $modelLimit = new Vote_Model_Limit();
        $this->getHelper('viewRenderer')->setNoRender(true);
        
        try {
            $activityId = $this->get("activityId", '');
            $subjectId = $this->get("subjectId", '');
            $itemId = $this->get("itemId", '');
            $FromUserName = trim($this->get('FromUserName', ''));
            if (empty($FromUserName)) {
                echo ($this->error(- 1, "微信ID不能为空"));
                return false;
            }
            if (empty($activityId)) {
                echo ($this->error(- 2, "活动ID不能为空"));
                return false;
            }
            if (empty($subjectId)) {
                echo ($this->error(- 3, "主题ID不能为空"));
                return false;
            }
            if (empty($itemId)) {
                echo ($this->error(- 4, "选项ID不能为空"));
                return false;
            }
            $activityInfo = $modelActivity->getInfoById($activityId);
            if (empty($activityInfo)) {
                echo ($this->error(- 5, "活动ID无效"));
                return false;
            }
            $subjectInfo = $modelSubject->getInfoById($subjectId);
            if (empty($subjectInfo)) {
                echo ($this->error(- 6, "主题ID无效"));
                return false;
            }
            
            $itemInfo = $modelItem->getInfoById($itemId);
            if (empty($itemInfo)) {
                echo ($this->error(- 7, "投票选项ID无效"));
                return false;
            }
            
            // 限制检查
            $isPassed = $modelLimit->checkLimit($activityId, $subjectId, $itemId, $FromUserName, 1, array(
                $activityId
            ));
            if (! $isPassed) { // 未通过
                echo ($this->error(- 8, "无法再次投票"));
                return false;
            }
            
            // 增加投票log
            $modelLog->log($activityId, $subjectId, $itemId, $FromUserName);
            $modelItem->incVoteCount($itemId);
            $modelSubject->incVoteCount($subjectId);
            $modelActivity->incVoteCount($activityId);
            
            // 发送成功
            echo ($this->result("OK"));
        } catch (Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
        
        die('OK');
    }

    /**
     * 同步每期排行帮信息
     */
    public function syncRankPeroidAction()
    {
        try {
            set_time_limit(0);
            // 54225fcf4896191d3b8b484f
            $subject_id = $this->get('subject_id', "");
            if (empty($subject_id)) {
                echo "请设置一个投票主题";
                return false;
            }
            $limit = $this->get('limit', 20);
            
            $modelItem = new Vote_Model_Item();
            $modelPeriod = new Vote_Model_Period();
            $modelRankPeriod = new Vote_Model_RankPeriod();
            
            $num = 0;
            $isPeriodGot = 0;
            $nameList = array();
            $list = array();
            
            do {
                $rankNameList = $modelRankPeriod->distinct("name", array());
                
                $sort = array(
                    'vote_count' => - 1
                );
                $otherConditon = array();
                $otherConditon['subjects'] = $subject_id;
                $otherConditon['name'] = array(
                    '$nin' => $rankNameList
                );
                $otherConditon['rank_period'] = 0;
                $ret = $modelItem->getList(1, 200, $otherConditon, $sort);
                
                if (! empty($ret['list']['datas'])) {
                    if (empty($isPeriodGot)) {
                        $period = $modelPeriod->getLatestPeriod($subject_id);
                        $isPeriodGot = 1;
                    }
                    foreach ($ret['list']['datas'] as $item) {
                        if (! in_array($item['name'], $nameList)) {
                            $nameList[] = $item['name'];
                            $num ++;
                            $modelRankPeriod->create($subject_id, $period, $item['name'], $item['desc'], $item['subjects'], $item['vote_count'], $item['show_order'], $item['memo']);
                            // $modelItem->updateRankPeriod(($item['_id']), $period);
                            $modelItem->updateRankPeriodByName($item['name'], $period);
                        }
                        if ($num >= $limit) { // 如果到达上限的时候
                            break;
                        }
                    }
                    if ($num >= $limit) { // 如果到达上限的时候
                        break;
                    }
                } else {
                    break;
                }
            } while (1);
            return true;
        } catch (Exception $e) {
            var_dump($e);
            return false;
        }
    }
}


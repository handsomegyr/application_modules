<?php
namespace Webcms\Weixinredpack\Controllers;

class IndexController extends ControllerBase
{

    private $modelSubject;

    private $modelItem;

    private $modelLog;

    private $modelLimit;

    private $modelPeriod;

    private $modelRankPeriod;

    public function initialize()
    {
        $this->modelSubject = new \Webcms\Weixinredpack\Models\Subject();
        $this->modelItem = new \Webcms\Weixinredpack\Models\Item();
        $this->modelLog = new \Webcms\Weixinredpack\Models\Log();
        $this->modelLimit = new \Webcms\Weixinredpack\Models\Limit();
        $this->modelPeriod = new \Webcms\Weixinredpack\Models\Period();
        $this->modelRankPeriod = new \Webcms\Weixinredpack\Models\RankPeriod();
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 首页
     */
    public function indexAction()
    {
        // http://phalconm4local/vote/index/index
        
        // 获取某活动下的所有投票主题
        $activityId = YUNGOU_ACTIVITY_ID;
        $subjectList = $this->modelSubject->getListByActivityId($activityId);
        
        // 根据主题获取该主题下的所有投票选项
        $itemList = array();
        foreach ($subjectList as $subject) {
            $subjectId = ($subject['_id']);
            $itemList[$subjectId] = $this->modelItem->getListBySubjectId($subjectId);
        }
        echo var_dump($subjectList, $itemList);
    }

    /**
     * 投票接口
     */
    public function voteAction()
    {
        // http://phalconm4local/vote/index/vote?FromUserName=xxxx
        try {
            $activityId = $this->get("activityId", YUNGOU_ACTIVITY_ID);
            $subjectId = $this->get("subjectId", '56de9e0a7f50ea8411000029');
            $itemId = $this->get("itemId", '56dea0517f50ea3812000029');
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
            
            $subjectInfo = $this->modelSubject->getInfoById($subjectId);
            if (empty($subjectInfo)) {
                echo ($this->error(- 6, "主题ID无效"));
                return false;
            }
            
            $itemInfo = $this->modelItem->getInfoById($itemId);
            if (empty($itemInfo)) {
                echo ($this->error(- 7, "投票选项ID无效"));
                return false;
            }
            
            // 限制检查
            $this->modelLimit->setLogModel($this->modelLog);
            $isPassed = $this->modelLimit->checkLimit($activityId, $subjectId, $itemId, $FromUserName, 1, array(
                $activityId
            ), array(
                $subjectId
            ));
            if (! $isPassed) { // 未通过
                echo ($this->error(- 8, "无法再次投票"));
                return false;
            }
            
            // 增加投票log
            $this->modelLog->log($activityId, $subjectId, $itemId, $FromUserName);
            $this->modelItem->incVoteCount($itemId);
            $this->modelSubject->incVoteCount($subjectId);
            
            // 发送成功
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 同步每期排行帮信息
     */
    public function syncrankperoidAction()
    {
        // http://phalconm4local/vote/index/syncrankperoid
        try {
            set_time_limit(0);
            $subject_id = $this->get('subject_id', "56de9e0a7f50ea8411000029");
            $limit = $this->get('limit', 20);
            if (empty($subject_id)) {
                echo ($this->error(- 3, "主题ID不能为空"));
                return false;
            }
            
            $num = 0;
            $isPeriodGot = 0;
            $nameList = array();
            $list = array();
            
            do {
                // $this->modelRankPeriod->setDebug(true);
                // $this->modelRankPeriod->setPhql(true);
                $rankNameList = $this->modelRankPeriod->distinct("name", array());
                $sort = array(
                    'vote_count' => - 1
                );
                $otherConditon = $this->modelItem->getQuery();
                $otherConditon['subject_id'] = $subject_id;
                $otherConditon['name'] = array(
                    '$nin' => $rankNameList
                );
                $otherConditon['rank_period'] = 0;
                $ret = $this->modelItem->find($otherConditon, $sort, 0, 200);
                if (! empty($ret['datas'])) {
                    if (empty($isPeriodGot)) {
                        $period = $this->modelPeriod->getLatestPeriod($subject_id);
                        $isPeriodGot = 1;
                    }
                    foreach ($ret['datas'] as $item) {
                        if (! in_array($item['name'], $nameList)) {
                            $nameList[] = $item['name'];
                            $num ++;
                            $this->modelRankPeriod->create($subject_id, $period, $item['name'], $item['desc'], $item['vote_count'], $item['show_order'], $item['memo']);
                            // $modelItem->updateRankPeriod(($item['_id']), $period);
                            $this->modelItem->updateRankPeriodByName($item['name'], $period);
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
            // 发送成功
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}


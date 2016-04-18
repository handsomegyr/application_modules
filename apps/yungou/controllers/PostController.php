<?php
namespace App\Yungou\Controllers;

/**
 * 云购
 * 晒单
 *
 * @author Kan
 *        
 */
class PostController extends ControllerBase
{

    private $modelPost = null;

    private $modelVote = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->setLayout('index');
        $this->modelPost = new \App\Post\Models\Post();
        $this->modelVote = new \App\Post\Models\Vote();
    }

    /**
     * 晒单列表页
     */
    public function indexAction()
    {
        // http://webcms.didv.cn/yungou/post/index?i=56360fd4adfb3842018b4569&r=10
        $page = $this->get('page', '1');
        $size = $this->get('size', '40');
        $i = $this->get('i', ''); // 晒单分类
        $this->assign('i', $i);
        $r = $this->get('r', '10'); // 排序方式
        $this->assign('r', $r);
        
        // 检索条件
        $query = $this->getQuery($i, $r);
        $query['state'] = \App\Post\Models\Post::STATE2;
        // 排序
        $sort = $this->getSort($r);
        
        // 获取晒单分页列表信息
        $postList = $this->modelPost->getPageList($page, $size, $query, $sort);
        $this->assign('postList', $postList);
        // 创建分页信息
        $url = $this->getSelfUrl();
        $sch = array(
            'i' => $i,
            'r' => $r
        );
        $pager = createPager($url, $postList['total'], $page, $size, $sch);
        $this->assign('pager', $pager);
    }

    /**
     * 单个晒单信息页
     */
    public function detailAction()
    {
        // http://webcms.didv.cn/yungou/post/detail?id=xxx
        $post_id = $this->get('id', '');
        if (empty($post_id)) {
            $this->goToError();
            return;
        }
        // 增加阅读数
        $this->modelPost->incReadNum($post_id);
        
        $postInfo = $this->modelPost->getInfoById($post_id);
        if (empty($postInfo)) {
            $this->goToError();
            return;
        }
        
        if ($postInfo['state'] != \App\Post\Models\Post::STATE2) {
            $this->goToError();
            return;
        }
        $this->assign('postInfo', $postInfo);
        
        $isVoted = false;
        if (! empty($_SESSION['member_id'])) {
            $voteInfo = $this->modelVote->getInfoByPostIdAndUserId($post_id, $_SESSION['member_id']);
            if (! empty($voteInfo)) {
                $isVoted = true;
            }
        }
        $this->assign('isVoted', $isVoted);
    }

    /**
     * 根据 最新 精华 推荐 人气等排序字段进行排序
     *
     * @param number $r            
     * @return array
     */
    private function getSort($r)
    {
        $sort = array();
        
        if ($r == 10) { // 最新r=10
            $sort = array(
                'post_time' => - 1
            );
        } elseif ($r == 20) { // 人气r=20
            $sort = array(
                'vote_num' => - 1,
                'reply_num' => - 1
            );
        } elseif ($r == 30) { // 推荐r=30
            $sort = array(
                'post_time' => 1
            );
        } elseif ($r == 40) { // 精华r=40
            $sort = array(
                'point' => - 1
            );
        }
        return $sort;
    }

    /**
     * 根据最新 精华 推荐 人气等排序字段获取列表
     *
     * @param string $i            
     */
    private function getQuery($i, $r)
    {
        $query = array();
        if (! empty($i)) {
            // 第1层分类
            $query['gc_id_1'] = $i;
        }
        if ($r == 30) { // 推荐r=30
            $query = array(
                'is_recommend' => true
            );
        }
        return $query;
    }
}


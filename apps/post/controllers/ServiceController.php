<?php

namespace App\Post\Controllers;

class ServiceController extends ControllerBase
{

    private $modelMemberNews = null;

    private $modelVote = null;

    private $modelReply = null;

    private $modelReplyMsg = null;

    private $modelMsgCount = null;

    private $modelMember = null;

    private $modelPost = null;

    private $modelGoods = null;

    private $modelPointsUser = null;

    private $modelMemberGrade = null;

    private $modelOrderGoods = null;

    private $modelPointsService = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelMemberNews = new \App\Member\Models\News();
        $this->modelMember = new \App\Member\Models\Member();
        $this->modelReply = new \App\Post\Models\Reply();
        $this->modelVote = new \App\Post\Models\Vote();
        $this->modelPost = new \App\Post\Models\Post();
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelPointsUser = new \App\Points\Models\User();
        $this->modelMemberGrade = new \App\Member\Models\Grade();
        $this->modelOrderGoods = new \App\Order\Models\Goods();
        $this->modelReplyMsg = new \App\Message\Models\ReplyMsg();
        $this->modelMsgCount = new \App\Message\Models\MsgCount();

        $this->modelPointsService = new \App\Points\Service\Api();
    }

    /**
     * 分页获取最新的晒单记录
     *
     * @return boolean
     */
    public function getrecpostlistAction()
    {
        // http://api.1yyg.com/JPData?action=getRecPostList&fun=jQuery18105005730031989515_1451570349417&_=1451570350554
        // jQuery18105005730031989515_1451570349417({'code':0,'listItems':[{'postID':131317,'postTitle':'裸车提宝马，方便！','userWeb':1008122257,'userName':'兴兴家次着汽车','userPhoto':'20151225183527893.jpg','postTime':'今天 17:28','postImg':'20151231172821873.jpg','postContent':'首先感谢云购还有云友们，有你们我才有机会中宝马，中宝马那天下午，收到云购发的中奖短信，简直太神奇了，我真的…'},{'postID':131222,'postTitle':'裸车宝马己经提到了','userWeb':1010315121,'userName':'很久没有听到信息','userPhoto':'00000000000000000.jpg','postTime':'今天 15:04','postImg':'20151231150422269.jpg','postContent':'真心的感谢一元云购，随然在等待提车的日子太难熬了，当一元云购工作人员知道我去提车的时候，心情真的是太激动了…'},{'postID':130891,'postTitle':'平安夜收到的苹果','userWeb':1010775271,'userName':'再见了2015','userPhoto':'20151231073108328.jpg','postTime':'今天 08:59','postImg':'20151231085953117.jpg','postContent':'一直在和朋友开玩笑，说平安夜云购会送我苹果，朋友们都说我想多了，没想到在平安夜还真让我中了一个苹果，谢谢云…'},{'postID':130350,'postTitle':'银思恒','userWeb':1010610564,'userName':'粤U6868','userPhoto':'20151230015247698.jpg','postTime':'昨天 10:47','postImg':'20151230104727119.jpg','postContent':'一直以来在云购里混了也有一定时间大大小小多多少少也中过商品，一直以来都是很看重银思恒手环的，想要一对送给我…'},{'postID':130318,'postTitle':'10元喜中尤尼克斯羽毛球拍','userWeb':1006531710,'userName':'龙的传人PXN','userPhoto':'20141019170156778.jpg','postTime':'昨天 10:12','postImg':'20151230101252007.jpg','postContent':'在中了这个球拍之后几个月才想起有晒单这回事，所以现在才晒单。我平时挺喜欢打羽毛球的，加上在学校我加入了羽毛…'},{'postID':130316,'postTitle':'我的第2云','userWeb':1011661472,'userName':'坚持中奥迪不是梦','userPhoto':'20151214180525171.jpg','postTime':'昨天 10:15','postImg':'20151230101504342.jpg','postContent':'真的 真的没想到我的第2云来的这么快 ，好激动！一元云购真给力！这次的快递也给力 很快三天就到了！身边的亲朋好…'},{'postID':129932,'postTitle':'很好的一个手镯','userWeb':1010136372,'userName':'汝甚叼','userPhoto':'20151111212358657.jpg','postTime':'12月29日 09:42','postImg':'20151229094209967.jpg','postContent':'算是意外的中奖吧！大投的没一次中，只有随便乱点的刚好中了，云购确实没那么容易中到。但是感觉还是运气重要，人…'},{'postID':129564,'postTitle':'茅台茅台，到手了','userWeb':1011440439,'userName':'云狗给我中一次会死不','userPhoto':'20151211092156315.jpg','postTime':'12月28日 10:35','postImg':'20151228103526129.jpg','postContent':'此时此刻我的心情无比开心，得到这个奖首先要感谢爸爸妈妈老婆小孩，没有你们的支持不会得到这瓶茅台，感谢云购让…'},{'postID':129533,'postTitle':'云购你没让我失望','userWeb':1011502496,'userName':'越来越坑卸载不玩了','userPhoto':'20151213065129880.jpg','postTime':'12月28日 10:10','postImg':'20151228101056130.jpg','postContent':'身边的一个朋友介绍我玩的，玩了几百块钱的，没想到真的中了个iPad。也算是比较大的奖励了吧。他们都羡慕死我了，…'}]})
        // http://www.myapplicationmodule.com.com/post/service/getrecpostlist
        try {
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '9'));

            $ret = array();
            $ret['total'] = 0;
            $ret['datas'] = array();

            $otherConditions = array();
            $otherConditions['state'] = \App\Post\Models\Post::STATE2;
            $list = $this->modelPost->getPageList($page, $limit, $otherConditions);
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {

                    // 'postID':131317,
                    // 'postTitle':'裸车提宝马，方便！',
                    // 'userWeb':1008122257,
                    // 'userName':'兴兴家次着汽车',
                    // 'userPhoto':'20151225183527893.jpg',
                    // 'postTime':'今天 17:28',
                    // 'postImg':'20151231172821873.jpg',
                    // 'postContent':'首先感谢云购还有云友们，有你们我才有机会中宝马，中宝马那天下午，收到云购发的中奖短信，简直太神奇了，我真的…'

                    $picArr = explode(',', $item['pic']);
                    foreach ($picArr as &$pic) {
                        $pic = $this->modelPost->getImagePath($this->baseUrl, $pic);
                    }
                    $item['pic'] = implode(',', $picArr);
                    $datas[] = array(
                        'postID' => $item['_id'],
                        'postTitle' => $item['title'],
                        'userWeb' => $item['goods_info']['prize_buyer_id'],
                        'userName' => getBuyerName($item['goods_info']['prize_buyer_name'], $item['goods_info']['prize_buyer_register_by']),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['goods_info']['prize_buyer_avatar']),
                        'postTime' => date('Y-m-d H:i:s', $item['post_time']->sec),
                        'postImg' => $item['pic'],
                        'postContent' => $item['content']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 分页获取某个商品ID的过去晒单记录
     *
     * @return boolean
     */
    public function getpostoldperiodlistAction()
    {
        // http://post.1yyg.com/JPData?action=getPostOldPeriodList&FIdx=1&EIdx=5&codeID=2634870&isCount=0&fun=jsonp1451809154925&_=1451809350949
        // jsonp1451809154948({"code":0,"count":0,"listItems":[{"codePeriod":2230,"userName":"中奖就OK","userSName":"中奖就OK","userWeb":"1010278784","userPhoto":"20151229135504588.jpg","postID":129475},{"codePeriod":2207,"userName":"云购真的能中6S嘛","userSName":"云购真的","userWeb":"1011284108","userPhoto":"20151229222820512.jpg","postID":129957},{"codePeriod":2138,"userName":"不玩了老中不了","userSName":"不玩了老","userWeb":"1011686477","userPhoto":"00000000000000000.jpg","postID":129214},{"codePeriod":2136,"userName":"再不中手机要杀人了","userSName":"再不中手","userWeb":"1010732302","userPhoto":"20151218005644646.jpg","postID":129830},{"codePeriod":2122,"userName":"南方电视台广告部小张","userSName":"南方电视","userWeb":"1011459778","userPhoto":"00000000000000000.jpg","postID":128231}]})
        // http://www.myapplicationmodule.com.com/post/service/getpostoldperiodlist&page=1&limit=5&codeID=xxxx
        try {
            $codeID = ($this->get('codeID', ''));
            if (empty($codeID)) {
                echo ($this->error(-1, 'codeID为空'));
                return false;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));

            $goodsInfo = $this->modelGoods->getInfoById($codeID);
            if (empty($goodsInfo)) {
                echo ($this->error(-2, 'codeID不正确'));
                return false;
            }

            $ret = array();
            $ret['total'] = 0;
            $ret['datas'] = array();

            $otherConditions = array();
            $otherConditions['goods_commonid'] = $goodsInfo['goods_commonid'];
            $otherConditions['state'] = \App\Post\Models\Post::STATE2;
            $list = $this->modelPost->getPageList($page, $limit, $otherConditions);
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "codePeriod":2230,
                    // "userName":"中奖就OK",
                    // "userSName":"中奖就OK",
                    // "userWeb":"1010278784",
                    // "userPhoto":"20151229135504588.jpg",
                    // "postID":129475
                    $picArr = explode(',', $item['pic']);
                    foreach ($picArr as &$pic) {
                        $pic = $this->modelPost->getImagePath($this->baseUrl, $pic);
                    }
                    $item['pic'] = implode(',', $picArr);
                    $datas[] = array(
                        "codePeriod" => $item['goods_info']['period'],
                        'userName' => getBuyerName($item['goods_info']['prize_buyer_name'], $item['goods_info']['prize_buyer_register_by']),
                        'userSName' => getBuyerName($item['goods_info']['prize_buyer_name'], $item['goods_info']['prize_buyer_register_by']),
                        'userWeb' => $item['goods_info']['prize_buyer_id'],
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['goods_info']['prize_buyer_avatar']),
                        'postID' => $item['_id'],
                        'postTitle' => $item['title'],
                        'postTime' => date('Y-m-d H:i:s', $item['post_time']->sec),
                        'postImg' => $item['pic'],
                        'postContent' => $item['content']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 分页TA的其他晒单的接口
     *
     * @return boolean
     */
    public function getpostotherlistAction()
    {
        // http://www.myapplicationmodule.com.com/post/service/getpostotherlist&page=1&limit=5&postid=xxxx
        try {
            $postid = ($this->get('postid', ''));
            if (empty($postid)) {
                echo ($this->error(-1, 'postid为空'));
                return false;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));

            $postInfo = $this->modelPost->getInfoById($postid);
            if (empty($postid)) {
                echo ($this->error(-2, 'postid不正确'));
                return false;
            }
            $ret = array();
            $ret['total'] = 0;
            $ret['datas'] = array();

            $otherConditions = array();
            $otherConditions['_id'] = array(
                '$ne' => $postid
            );
            $otherConditions['buyer_id'] = $postInfo['buyer_id'];
            $otherConditions['state'] = \App\Post\Models\Post::STATE2;
            $list = $this->modelPost->getPageList($page, $limit, $otherConditions);
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "codePeriod":2230,
                    // "userName":"中奖就OK",
                    // "userSName":"中奖就OK",
                    // "userWeb":"1010278784",
                    // "userPhoto":"20151229135504588.jpg",
                    // "postID":129475
                    $picArr = explode(',', $item['pic']);
                    foreach ($picArr as &$pic) {
                        $pic = $this->modelPost->getImagePath($this->baseUrl, $pic);
                    }
                    $item['pic'] = implode(',', $picArr);
                    $datas[] = array(
                        "codePeriod" => $item['goods_info']['period'],
                        'userName' => getBuyerName($item['goods_info']['prize_buyer_name'], $item['goods_info']['prize_buyer_register_by']),
                        'userSName' => getBuyerName($item['goods_info']['prize_buyer_name'], $item['goods_info']['prize_buyer_register_by']),
                        'userWeb' => $item['goods_info']['prize_buyer_id'],
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['goods_info']['prize_buyer_avatar']),
                        'postID' => $item['_id'],
                        'postTitle' => $item['title'],
                        'postTime' => date('Y-m-d H:i:s', $item['post_time']->sec),
                        'postImg' => $item['pic'],
                        'postContent' => $item['content'],
                        'reply_num' => $item['reply_num'],
                        'vote_num' => $item['vote_num']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取晒单列表的接口
     *
     * @return boolean
     */
    public function getmembercenteruserpostsingleAction()
    {
        // http://member.1yyg.com/JPData?action=getMemberCenterUserPostSingle&FIdx=1&EIdx=5&State=0&isStat=1&isCount=1&fun=jsonp1451609747062&_=1451609747498
        // jsonp1451745114364({"code":0,"totalCount":1,"postCount":1,"unPostCount":0,"listItems":[{"codeID":"2489811","codePeriod":"74","goodsSName":"川宇（Kawau）Micro SD/T-Flash TF读卡器 C289","goodsPic":"20151215155940353.jpg","codePrice":"20","codeRNO":"10000007","codeRTime":"2015-12-18 10:39:22.280","postID":"132289","postTitle":"终于中奖啦！","postPic":"20160102141114933.jpg","postContent":"嗯呵呵O(∩_∩)O~，货真价实的读卡器，包装很好~~~物流还不错，基本上都不错，没有什么问题，128G的内存卡感觉真好，可以随便拍照片，随便传电影，再不用担心存储已满的焦急心情…","postTime":"2016-01-02 14:14","postState":"2","postFailReason":"","postPoint":"700","codeType":"0"}]})
        // http://www.myapplicationmodule.com.com/post/service/getmembercenteruserpostsingle?page=1&limit=5&state=0
        try {
            $state = intval($this->get('state', '0'));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));
            if (empty($_SESSION['member_id'])) {
                echo ($this->error(-1, '非法访问'));
                return false;
            }
            $ret = array();
            $ret['total'] = 0;
            $ret['datas'] = array();

            $otherConditions = array();

            if ($state == 1) {
                // 已晒单
                $otherConditions['state'] = \App\Post\Models\Post::STATE2;
            } elseif ($state == 2) {
                // 未晒单
                $otherConditions['state'] = \App\Post\Models\Post::STATE_NONE;
            } elseif ($state == 3) {
                // 待审核
                $otherConditions['state'] = \App\Post\Models\Post::STATE0;
            } elseif ($state == 4) {
                // 未通过
                $otherConditions['state'] = \App\Post\Models\Post::STATE1;
            } elseif ($state == 5) {
                // 审核通过
                $otherConditions['state'] = \App\Post\Models\Post::STATE2;
            }

            $list = $this->modelPost->getPageListByBuyerId($_SESSION['member_id'], $page, $limit, $otherConditions);
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "codeID":"2489811",
                    // "codePeriod":"74",
                    // "goodsSName":"川宇（Kawau）Micro SD/T-Flash TF读卡器 C289",
                    // "goodsPic":"20151215155940353.jpg",
                    // "codePrice":"20",
                    // "codeRNO":"10000007",
                    // "codeRTime":"2015-12-18 10:39:22.280",
                    // "postID":"132289",
                    // "postTitle":"终于中奖啦！",
                    // "postPic":"20160102141114933.jpg",
                    // "postContent":"嗯呵呵O(∩_∩)O~，货真价实的读卡器，包装很好~~~物流还不错，基本上都不错，没有什么问题，128G的内存卡感觉真好，可以随便拍照片，随便传电影，再不用担心存储已满的焦急心情…",
                    // "postTime":"2016-01-02 14:14",
                    // "postState":"2",
                    // "postFailReason":"",
                    // "postPoint":"700",
                    // "codeType":"0"
                    $picArr = explode(',', $item['pic']);
                    foreach ($picArr as &$pic) {
                        $pic = $this->modelPost->getImagePath($this->baseUrl, $pic);
                    }
                    $item['pic'] = implode(',', $picArr);
                    $datas[] = array(
                        'codeID' => $item['goods_id'],
                        'codePeriod' => $item['goods_info']['period'],
                        'goodsSName' => $item['goods_info']['name'],
                        'goodsPic' => $this->modelGoods->getImagePath($this->baseUrl, $item['goods_info']['image']),
                        'codePrice' => showPrice($item['goods_info']['price'], 2),
                        'codeRNO' => $item['goods_info']['prize_code'],
                        'codeRTime' => getMilliTime4Show($item['goods_info']['prize_time']),
                        'postId' => $item['_id'],
                        'postTitle' => $item['title'],
                        'postPic' => $item['pic'],
                        'postContent' => $item['content'],
                        'postTime' => date('Y-m-d H:i:s', $item['post_time']->sec),
                        'postState' => $item['state'],
                        'postFailReason' => $item['fail_reason'],
                        'postPoint' => $item['point'],
                        'codeType' => 0,
                        'goodsID' => $item['goods_commonid']
                    );
                }
            }
            $ret['postCount'] = $this->modelPost->getPostCountByBuyerId($_SESSION['member_id']);
            $ret['unPostCount'] = $this->modelPost->getUnPostCountByBuyerId($_SESSION['member_id']);
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 会员-首页
     * 获取云友最新晒单
     */
    public function getpagelistpostsingleAction()
    {
        // http://post.1yyg.com/JPData?action=GetPageListPostSingle&FIdx=1&EIdx=9&fun=jsonp1452177216666&_=1452177216988
        // jsonp1452177216666({"code":0,"count":97666,"listItems":[{"postID":135141,"postTitle":"小米充电宝","postContent":"小米不愧是号称中国的“苹果”。质量比其他的那些杂牌子好得多了。就是个头有点大。总体…","postTime":"今天 17:29","cutpicture":"20160107173932573.jpg","postHits":13,"postReplyCount":49,"userName":"2016年新款3系BMW","userWeb":"1010292566","userPhoto":"20151126044505718.jpg"},{"postID":135134,"postTitle":"中充电宝","postContent":"非常开心收到中奖的信息，中了个充电宝，小米充电宝实用耐用，所以玩玩云购，感谢云哥给…","postTime":"今天 17:21","cutpicture":"20160107172232224.jpg","postHits":6,"postReplyCount":42,"userName":"倒蛋先生","userWeb":"1011267683","userPhoto":"20151201142923989.jpg"},{"postID":135038,"postTitle":"新年新运气","postContent":"从2015年就陆续有在1元云购购买东西，刚开始就是觉得好玩~好奇～难道几块钱真的能买到几…","postTime":"今天 17:18","cutpicture":"20160107173139222.jpg","postHits":19,"postReplyCount":40,"userName":"1元无福啊","userWeb":"1011839185","userPhoto":"00000000000000000.jpg"},{"postID":135131,"postTitle":"谢谢云购给我一个小惊喜…","postContent":"一直想要个保温杯，就特么中了，花了4块钱中的，有点物超所值的赶脚、保温杯样式好看，就…","postTime":"今天 17:18","cutpicture":"20160107171952059.jpg","postHits":4,"postReplyCount":11,"userName":"是不是忘了安徽省","userWeb":"1011556951","userPhoto":"00000000000000000.jpg"},{"postID":135130,"postTitle":"第一次晒单…","postContent":"怎么说呢，也不是第一次中奖，虽然没什么大奖，能中还是挺开心的，买了几次哪个黑冰没中…","postTime":"今天 17:17","cutpicture":"20160107172100121.jpg","postHits":5,"postReplyCount":11,"userName":"云狗老子怒了","userWeb":"1011620908","userPhoto":"20151223232113623.jpg"},{"postID":134684,"postTitle":"小米充电宝","postContent":"很期待有个充电宝，就在昨天给我中到了一个，中到后很怕是骗人或着不发货的，结果今天真…","postTime":"今天 17:14","cutpicture":"20160107175434336.jpg","postHits":3,"postReplyCount":9,"userName":"俺从农村来_____看看","userWeb":"1011979341","userPhoto":"20151230003432551.jpg"},{"postID":135110,"postTitle":"喜中金章了","postContent":"云购又让我中了三个金章，开心。希望云购越办越好，多多中奖。也希望没中的云友们不要灰…","postTime":"今天 17:10","cutpicture":"20160107172542762.jpg","postHits":9,"postReplyCount":15,"userName":"恭喜恭喜恭喜啊","userWeb":"1010524844","userPhoto":"20160105160744215.jpg"},{"postID":135116,"postTitle":"喜中小米…排插！","postContent":"看中的就是后面三个USB插口，方便使用！买这这么多次云购，都是中低价的东西，真的不甘心…","postTime":"今天 17:09","cutpicture":"20160107171357470.jpg","postHits":5,"postReplyCount":11,"userName":"佛祖留我心中啥由我定","userWeb":"1011213006","userPhoto":"20160107222502108.jpg"},{"postID":135104,"postTitle":"喜中苹果手表","postContent":"买了18人次中到的手表今天到手了。挺帅气，挺喜欢。中时还有点小激动。云购了三千多块钱…","postTime":"今天 16:53","cutpicture":"20160107165911852.jpg","postHits":6,"postReplyCount":11,"userName":"中必中中中中","userWeb":"1008157963","userPhoto":"20160106211647722.jpg"}]})
        // http://www.myapplicationmodule.com.com/post/service/getpagelistpostsingle?page=1&limit=9
        try {
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '9'));

            $ret = array();
            $ret['total'] = 0;
            $ret['datas'] = array();

            $otherConditions = array();
            if (!empty($_SESSION['member_id'])) {
                $otherConditions['buyer_id'] = array(
                    '$ne' => $_SESSION['member_id']
                );
            }
            $otherConditions['state'] = \App\Post\Models\Post::STATE2;
            $list = $this->modelPost->getPageList($page, $limit, $otherConditions);
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "postID":135141,
                    // "postTitle":"小米充电宝",
                    // "postContent":"小米不愧是号称中国的“苹果”。质量比其他的那些杂牌子好得多了。就是个头有点大。总体…",
                    // "postTime":"今天 17:29",
                    // "cutpicture":"20160107173932573.jpg",
                    // "postHits":13,
                    // "postReplyCount":49,
                    // "userName":"2016年新款3系BMW",
                    // "userWeb":"1010292566",
                    // "userPhoto":"20151126044505718.jpg"

                    $picArr = explode(',', $item['pic']);
                    foreach ($picArr as &$pic) {
                        $pic = $this->modelPost->getImagePath($this->baseUrl, $pic);
                    }
                    $item['pic'] = $picArr; // implode(',', $picArr);
                    $datas[] = array(
                        'postID' => $item['_id'],
                        'postTitle' => $item['title'],
                        'postContent' => $item['content'],
                        'postTime' => date('Y-m-d H:i:s', $item['post_time']->sec),
                        'cutpicture' => $item['pic'][0],
                        'postHits' => $item['vote_num'],
                        'postReplyCount' => $item['reply_num'],
                        'userName' => getBuyerName($item['goods_info']['prize_buyer_name'], $item['goods_info']['prize_buyer_register_by']),
                        'userWeb' => $item['goods_info']['prize_buyer_id'],
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['goods_info']['prize_buyer_avatar'])
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 添加晒单的接口
     */
    public function insertpostsingleAction()
    {
        // http://member.1yyg.com/JPData?action=insertPostSingle&postCodeID=xxx&postTitle=&postContent=xxxx&postAllPic=a.pic,b.pic
        // http://www.myapplicationmodule.com.com/post/service/insertpostsingle?postCodeID=xxx&postTitle=&postContent=xxxx&postAllPic=a.pic,b.pic
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $postCodeID = ($this->get('postCodeID', ''));
            $postTitle = urldecode($this->get('postTitle', ''));
            $postContent = urldecode($this->get('postContent', ''));
            $postAllPic = ($this->get('postAllPic', ''));

            $goodsInfo = $this->modelGoods->getInfoById($postCodeID);
            if (empty($goodsInfo)) {
                echo ($this->error('-1', 'postCodeID不正确'));
                return false;
            }

            $postInfo = $this->modelPost->getInfoByBuyerIdAndGoodsId($_SESSION['member_id'], $postCodeID);
            if (empty($postInfo)) {
                echo ($this->error('-2', '记录不存在'));
                return false;
            }

            if ($postInfo['state'] == \App\Post\Models\Post::STATE2) {
                echo ($this->error('-3', '该记录已审核通过，无法在修改'));
                return false;
            }

            $list = $this->modelPost->insertPostSingle($postInfo['_id'], $postTitle, $postContent, $postAllPic, $goodsInfo);
            echo ($this->result("OK", $list));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 晒单详情页
     *
     * 回复帖子的接口
     * 回复某帖子某回复的接口
     */
    public function insertpostreplyAction()
    {
        // http://www.myapplicationmodule.com.com/post/service/insertpostreply?postid=125584&originalContent=运气很好运气很好&code=&refReplyId=0&refFloor=0
        // ({"code":0})
        // http://www.myapplicationmodule.com.com/post/service/insertpostreply?postid=125584&originalContent=真的很好&code=&refReplyId=1655353&refFloor=0
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $postid = ($this->get('postid', ''));
            $originalContent = urldecode($this->get('originalContent', ''));
            $code = ($this->get('code', ''));
            $refReplyId = ($this->get('refReplyId', ''));
            $refFloor = intval($this->get('refFloor', '0'));
            $floor = 0;
            $postInfo = $this->modelPost->getInfoById($postid);
            if (!empty($refReplyId) && $refFloor) {
                $info = $this->modelReply->getInfoById($refReplyId);
                if (empty($info)) {
                    echo ($this->error('-1', '回复ID不正确'));
                    return false;
                }
            }
            $memberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            $user_name = $this->modelMember->getRegisterName($memberInfo);

            if (!empty($info)) {
                // 增加回复数量
                $this->modelReply->incNum($info['ref_reply_id']);
                $newReplyInfo =  $this->modelReply->getInfoById($info['ref_reply_id']);
                $floor = $newReplyInfo['num'];
                $list = $this->modelReply->insertpostreply($postid, $memberInfo['_id'], $user_name, $memberInfo['avatar'], $memberInfo['register_by'], $originalContent, $info['user_id'], $info['user_name'], $info['user_avatar'], $info['user_register_by'], $info['user_content'], $info['ref_reply_id'], $floor, $refFloor);
                $this->modelReplyMsg->log($postid, $memberInfo['_id'], $user_name, $memberInfo['avatar'], $memberInfo['register_by'], $originalContent, $info['user_id'], $info['user_name'], $info['user_avatar'], $info['user_register_by'], $info['user_content']);
                $this->modelMsgCount->incReplyMsgCount($info['user_id']);
            } else {
                $list = $this->modelReply->insertpostreply($postid, $memberInfo['_id'], $user_name, $memberInfo['avatar'], $memberInfo['register_by'], $originalContent, $postInfo['buyer_id'], $postInfo['goods_info']['prize_buyer_name'], $postInfo['goods_info']['prize_buyer_avatar'], $postInfo['goods_info']['prize_buyer_register_by'], '', $refReplyId, $floor, $refFloor);
                $this->modelReplyMsg->log($postid, $memberInfo['_id'], $user_name, $memberInfo['avatar'], $memberInfo['register_by'], $originalContent, $postInfo['buyer_id'], $postInfo['goods_info']['prize_buyer_name'], $postInfo['goods_info']['prize_buyer_avatar'], $postInfo['goods_info']['prize_buyer_register_by'], '');
                $this->modelMsgCount->incReplyMsgCount($postInfo['buyer_id']);
            }
            $this->modelMsgCount->incReplyMsgCount($memberInfo['_id']);
            echo ($this->result("OK", $list));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 删除某帖子某回复的接口
     */
    public function deletepostreplyAction()
    {
        // http://post.1yyg.com/JPData?action=DeletePostReply&replyId=1655429&fun=jsonp1450961626941&_=1450965382588
        // http://www.myapplicationmodule.com.com/post/service/deletepostreply?replyId=125584
        try {
            $replyId = ($this->get('replyId', ''));
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $info = $this->modelReply->getInfoById($replyId);
            if (empty($info)) {
                echo ($this->error('-1', '回复ID不正确'));
                return false;
            }
            if ($info['user_id'] != $_SESSION['member_id']) {
                echo ($this->error('-2', '不能删除他人的回复'));
                return false;
            }
            $this->modelReply->setIsDel($replyId);

            if (!empty($info['ref_reply_id'])) {
                $this->modelReply->incDelNum($info['ref_reply_id']);
            }
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取晒单评论的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getpostreplybyidAction()
    {
        // http://post.1yyg.com/JPData?action=getPostReplyByID&FIdx=1&EIdx=10&postid=132478&fun=jsonp1451809026141&_=1451809026842
        // jsonp1451809026141({"code":0,"count":27,"data":[{"replyUserName":"15986****34","replyUserWeb":"1012147538","replyContent":"尼玛，骗纸！再不中奖真要投诉你们的真实性","replyTime":"1分钟前","replyID":"1673875","replyCount":"0","userPhoto":"20151230005003267.jpg"},{"replyUserName":"中奖群334722316","replyUserWeb":"1007940261","replyContent":"看看我名名字吧 中奖机会高65","replyTime":"38分钟前","replyID":"1673827","replyCount":"0","userPhoto":"20151208112441824.jpg"},{"replyUserName":"云骗子老子不玩了","replyUserWeb":"1012373950","replyContent":"骗人的吧","replyTime":"51分钟前","replyID":"1673808","replyCount":"0","userPhoto":"20151231130851593.jpg"},{"replyUserName":"给我一次中奖呗","replyUserWeb":"1012480909","replyContent":"云购你好，我听朋友说一元中手机是真的吗？我手机照相机坏了，能有好运中奖吗？希望好运噢","replyTime":"58分钟前","replyID":"1673803","replyCount":"0","userPhoto":"20151230135008374.jpg"},{"replyUserName":"2016年愿望云购中奥迪","replyUserWeb":"1011810656","replyContent":"云购假太假了","replyTime":"今天 14:52","replyID":"1673763","replyCount":"0","userPhoto":"20160102115625243.jpg"},{"replyUserName":"15919****83","replyUserWeb":"1009346492","replyContent":"沾沾喜气","replyTime":"今天 14:28","replyID":"1673681","replyCount":"0","userPhoto":"00000000000000000.jpg"},{"replyUserName":"13875****03","replyUserWeb":"1011049224","replyContent":"毛都没中娘希皮","replyTime":"今天 14:27","replyID":"1673673","replyCount":"0","userPhoto":"00000000000000000.jpg"},{"replyUserName":"中个老婆1314","replyUserWeb":"1008993167","replyContent":"宝马来了","replyTime":"今天 14:23","replyID":"1673662","replyCount":"0","userPhoto":"20160103142654862.jpg"},{"replyUserName":"中奖群334722316","replyUserWeb":"1007940261","replyContent":"想中的看我名名字 中奖机会高65 \n赶快把","replyTime":"今天 14:21","replyID":"1673657","replyCount":"0","userPhoto":"20151208112441824.jpg"},{"replyUserName":"18520****76","replyUserWeb":"1009617946","replyContent":"没中过卸载了，朋友中了，我又心动了！希望这次能中个大奖！哈哈哈","replyTime":"今天 12:54","replyID":"1673572","replyCount":"0","userPhoto":"00000000000000000.jpg"}]})
        // http://www.myapplicationmodule.com.com/post/service/getpostreplybyid?page=1&limit=10&postid=125584
        try {
            $postid = ($this->get('postid', ''));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));
            $query = array(
                'ref_reply_id' => ''
            );
            $sort = array();
            $list = $this->modelReply->getPageListByPostId($postid, $page, $limit, $query, $sort);

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    $user_ids[] = $item['user_id'];
                }
                $memberList = $this->modelMember->getListByIds($user_ids);

                foreach ($list['datas'] as $item) {
                    // "replyUserName": "郭永荣",
                    // "replyUserWeb": "1010381532",
                    // "replyContent": "运气很好运气很好",
                    // "replyTime": "1分钟前",
                    // "replyID": "1655353",
                    // "replyCount": "0",
                    // "userPhoto": "20151106195125381.jpg"
                    if (!isset($memberList[$item['user_id']])) {
                        throw new \Exception("{$item['user_id']}对应的会员信息不存在");
                    }
                    $memberInfo = $memberList[$item['user_id']];

                    $datas[] = array(
                        'replyUserName' => $this->modelMember->getRegisterName($memberInfo, true),
                        'replyUserWeb' => $memberInfo['_id'],
                        'replyContent' => $item['user_content'],
                        'replyTime' => date('Y-m-d H:i:s', $item['reply_time']->sec),
                        'replyID' => $item['_id'],
                        'replyCount' => $item['num'] - $item['del_num'],
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $memberInfo['avatar']),
                        'userID' => $memberInfo['_id']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取某个回复的回复列表接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getpostreplyinfoAction()
    {
        // http://post.1yyg.com/JPData?action=GetPostReplyInfo&replyId=1655353&fun=jsonp1450921251276&_=1450921759981
        // jsonp1450693242938({"code":0,"data":[{"floorID":1,"replyID":1649117,"replyContent":"让人羡慕啊","replyUserID":9563477,"userPhoto":"20151106195125381.jpg","replyUserName":"郭永荣","replyUserWeb":"1010381532","replyRefFloor":0,"replyTime":"1分钟前","isDel":1}]})
        // http://www.myapplicationmodule.com.com/post/service/getpostreplyinfo?replyId=1655353
        try {
            $replyId = ($this->get('replyId', ''));
            $list = $this->modelReply->getListByReplyId($replyId);

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    $user_ids[] = $item['user_id'];
                }
                $memberList = $this->modelMember->getListByIds($user_ids);

                foreach ($list['datas'] as $item) {

                    // floorID: 1
                    // isDel: 1
                    // replyContent: "看来我也要去买一个"
                    // replyID: 1654704
                    // replyRefFloor: 0
                    // replyTime: "1分钟前"
                    // replyUserID: 9563477
                    // replyUserName: "郭永荣"
                    // replyUserWeb: "1010381532"
                    // userPhoto: "20151106195125381.jpg"
                    if (!isset($memberList[$item['user_id']])) {
                        throw new \Exception("{$item['user_id']}对应的会员信息不存在");
                    }
                    $memberInfo = $memberList[$item['user_id']];

                    $datas[] = array(
                        'userID' => $memberInfo['_id'],
                        'replyUserName' => $this->modelMember->getRegisterName($memberInfo, true),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $memberInfo['avatar']),
                        'replyUserWeb' => $memberInfo['_id'],
                        'replyID' => $item['_id'],
                        'floorID' => $item['floor'],
                        'replyTime' => date('Y-m-d H:i:s', $item['reply_time']->sec),
                        'replyContent' => $item['user_content'],
                        'isDel' => (!empty($_SESSION['member_id']) && $item['user_id'] == $_SESSION['member_id']), // 可以删除
                        'replyRefFloor' => $item['ref_floor']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 加羡慕的接口
     */
    public function insertposthitsAction()
    {
        // http://post.1yyg.com/JPData?action=InsertPostHits&postid=125584&fun=jsonp1450961627171&_=1450966353810
        // jsonp1451811093382({"code":0})
        // http://www.myapplicationmodule.com.com/post/service/insertposthits?postid=125584
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $postid = ($this->get('postid', ''));
            if (empty($postid)) {
                echo ($this->error('-1', '帖子ID为空'));
                return false;
            }
            $voteInfo = $this->modelVote->getInfoByPostIdAndUserId($postid, $_SESSION['member_id']);
            if (!empty($voteInfo)) {
                echo ($this->error('-2', '已经投票过了'));
                return false;
            } else {
                $info = $this->modelPost->getInfoById($postid);
                if (empty($info)) {
                    echo ($this->error('-3', '回复ID不正确'));
                    return false;
                }
                $list = $this->modelPost->incVoteNum($postid);
                $this->modelVote->log($postid, $_SESSION['member_id']);
            }
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 分页获取最新的晒单记录
     *
     * @return boolean
     */
    public function getgoodspostlistAction()
    {
        // http://post.1yyg.com/JPData?action=getGoodsPostList&goodsID=22288&FIdx=1&EIdx=10&isCount=1&fun=jsonp1451399131727&_=1451399757535
        // jsonp1451821170043({'code':0,'CountEx':6643,'Count':1240,'Data':[{"userWeb":"1011714446","userPhoto":"20151231205835150.jpg","userName":"A6还给我啊","codePeriod":"18669","postID":"132547","postTitle":"感悟 心态跟奖品一样重要","postContent":"中奖了，有点点欣喜，让我欣喜的是原来真的可以中奖。但是带给我更多的是悲伤，云购 总共花了几千也算是拿到大奖了运气应该也算不错 物流配送还可以接受 我想云购内部应该没有作假 但是我觉得这个东西只能适度玩玩，千万别沉迷其中，多做点实际的工作，这才是出路。 最后 还是要感谢云购 谢谢","postAllPic":"20160102223608260.jpg,20160102223618279.jpg,20160102223634470.jpg,20160102224740721.jpg","postHits":"5","postReplyCount":"0","grade":"03","gradeName":"云购中将","postTimeEx":"昨天 23:08"},{"userWeb":"1004490565","userPhoto":"20140326161102983.jpg","userName":"最后10块再不中就不玩","codePeriod":"24097","postID":"132475","postTitle":"真的6S噢","postContent":"无意间在手机软件商城里发现1元云购，还以为是忽悠人的，就抱着试试玩的?？玩了几天，投了豆奶，中奖了，填了收货地址后，结果收到产品啦。再投了几次苹果手机6s,结果中了一部。收到产品用了几天。是真货。希望云购越做越好！希望下次再中辆汽车就更完美了。？？？？？？？？","postAllPic":"20160102195743560.jpg,20160102195811628.jpg,20160102200207810.jpg,20160102200237763.jpg","postHits":"2","postReplyCount":"2","grade":"03","gradeName":"云购中将","postTimeEx":"昨天 20:11"},{"userWeb":"1002220241","userPhoto":"20151228083251771.jpg","userName":"金牌凯子","codePeriod":"25401","postID":"132348","postTitle":"喜中苹果！","postContent":"这个苹果来的不易，你们不要看人家一块就中，挺不现实的，投了好几次才中，功夫不负有心人'还要感谢云购这么好的平台，在这里我表示衷心祝福大家中的更多的奖品1元买宝马。2元种手机，3快中话费。大家都中，都来小汽车","postAllPic":"20160102160129670.jpg,20160102160741687.jpg,20160102160820921.jpg","postHits":"17","postReplyCount":"12","grade":"03","gradeName":"云购中将","postTimeEx":"昨天 16:08"},{"userWeb":"1011940404","userPhoto":"00000000000000000.jpg","userName":"我为云购已卖肾","codePeriod":"15276","postID":"132195","postTitle":"我的6S","postContent":"中奖了，真的中奖了，一块真的买到了6S，感谢云购，虽然东西不算很贵，但还是享受了那会买彩票中奖的感觉，希望再给我来辆奥迪，开回家过年。哈哈，祝愿所有朋友在新的一年里都能有收获！云购越办越好！好东西越来越多！","postAllPic":"20160102110851149.jpg,20160102110918311.jpg,20160102111014453.jpg","postHits":"20","postReplyCount":"12","grade":"01","gradeName":"云购小将","postTimeEx":"昨天 11:13"},{"userWeb":"1011649707","userPhoto":"20151202121315507.jpg","userName":"一手群6969693v","codePeriod":"20294","postID":"132161","postTitle":"我是每天都zai不同的云","postContent":"云购现在是我生活的一部分，每天基本撒不少元，自去年到现在陆续云到有手机，我云购金额并不少，基本都是挺多才云到的。说道这里没中过手机的云友又该吐槽了，说我是云购的托，说自己投了了几千块都没有中什么的。我想告诉云友，我是每天都再不同的云","postAllPic":"20160102100902162.jpg,20160102100910463.jpg,20160102100915003.jpg","postHits":"0","postReplyCount":"1","grade":"06","gradeName":"云购将军","postTimeEx":"昨天 10:09"},{"userWeb":"1011649707","userPhoto":"20151202121315507.jpg","userName":"一手群6969693v","codePeriod":"20533","postID":"132118","postTitle":"...........期待下一次的晒单哦！","postContent":"刚刚接触时是嗤之以鼻，不相信一元真能买手机。后来仔细接触了一下，看见身边的朋友确实收到货了。正好赶上咱也要还手机，赶紧加入浩浩荡荡的云购团队。第一次进入的不是这个云购网，那个网人气不足，什么都没有。后来经朋友发链接来进入这个云购网，好运直来，就中了苹果一部了。并且经专业做手机生意的朋友检验，此手机为大陆行货。哈哈，云购给我的好运...........期待下一次的晒单哦！","postAllPic":"20160102093942735.jpg,20160102093948422.jpg,20160102093957604.jpg","postHits":"10","postReplyCount":"4","grade":"06","gradeName":"云购将军","postTimeEx":"昨天 09:40"},{"userWeb":"1011649707","userPhoto":"20151202121315507.jpg","userName":"一手群6969693v","codePeriod":"18569","postID":"132102","postTitle":"希望在中个宝马车，呵呵","postContent":"前听朋友一直说1元云购买车买手机和其他物品等，我以为是开玩笑的没有当真，过了一段时间越来越多人在谈论云购，我就开始了解云购加入云购，选了些物品1元1元的云购，随便到处买了。其中有1元购买 等到揭晓时1元云购公众号发来喜中消息，我就填写信息和地址，一个星期后就收到由顺丰快递来苹果6手机，再接再厉希望在中个宝马车，呵呵","postAllPic":"20160102093134917.jpg,20160102093139543.jpg,20160102093144624.jpg","postHits":"5","postReplyCount":"10","grade":"06","gradeName":"云购将军","postTimeEx":"昨天 09:32"},{"userWeb":"1011649707","userPhoto":"20151202121315507.jpg","userName":"一手群6969693v","codePeriod":"22117","postID":"132087","postTitle":"运气不会太差","postContent":"关注一元云购完全是因为大学同学中了一台苹果电脑，眼红的不行，当时还在上班的时候一冲动就买了电脑，随便买块手机，万万没想到居然中了，人品爆发，爱笑的人运气不会太差。等了1个多星期，，顺丰还挺快，拿了立刻拆了，全新机，国行就是屌。迫不及待换卡用上，以后也会持续关注的","postAllPic":"20160102092221783.jpg,20160102092226348.jpg,20160102092230873.jpg","postHits":"1","postReplyCount":"2","grade":"06","gradeName":"云购将军","postTimeEx":"昨天 09:22"},{"userWeb":"1011298802","userPhoto":"20151212015811281.jpg","userName":"泥马限购为什么中不了","codePeriod":"25797","postID":"132072","postTitle":"30块钱中的第二部手机到货","postContent":"今天早上快递打电话说手机到了，心里很高兴，第二部手机在上两天晚上花了30块钱中的6S到了，但是也投资了不少，相对的回报我的还可以，感谢一元云购，很多人不相信这是真的，看到我中的奖品之后也带动不少人跟我一起玩，希望云购越来越好，也希望自己下次中个更好的奖品，小汽车等着我","postAllPic":"20160102090912330.jpg,20160102085531757.jpg,20160102085618757.jpg,20160102085645411.jpg,20160102090919552.jpg","postHits":"6","postReplyCount":"9","grade":"03","gradeName":"云购中将","postTimeEx":"昨天 09:09"},{"userWeb":"1005196071","userPhoto":"20151230075050660.jpg","userName":"扌喿亻尔女马屄","codePeriod":"24411","postID":"132035","postTitle":"是的，真的中了个iPhone6s，新年礼物!向云购致敬!","postContent":"感谢一元云购给我这个机会，在新的一年里，收到一个这么好的礼物，发货很快，我是27号凌晨26分的时候中的奖，那个激动啊，掐自己问:是不是在做梦，结果不是梦，到今天31号下午收到快递，我高兴坏了，在新的一年里收到这么好的礼物，在此预祝云购能够越做越好，生意新隆!新年快乐!望云购多多照顾，最后我想说一句:云购，幸好有你，谢谢你!","postAllPic":"20160102013714604.jpg,20160102013919874.jpg,20160102014041660.jpg","postHits":"6","postReplyCount":"0","grade":"01","gradeName":"云购小将","postTimeEx":"昨天 01:40"}]})
        // http://www.myapplicationmodule.com.com/post/service/getgoodspostlist?goodsID=22288&page=1&limit=10
        try {
            $goodsID = ($this->get('goodsID', ''));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));

            $ret = array();
            $ret['total'] = 0;
            $ret['CountEx'] = 6643;
            $ret['Count'] = 1240;

            $ret['datas'] = array();
            $otherConditions = array();
            $otherConditions['state'] = \App\Post\Models\Post::STATE2;
            $list = $this->modelPost->getPageList($page, $limit, $otherConditions);
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {

                foreach ($list['datas'] as $item) {
                    $user_ids[] = $item['buyer_id'];
                }
                $pointUserList = $this->modelPointsUser->getListByUserIds($user_ids, POINTS_CATEGORY2);

                foreach ($list['datas'] as $item) {

                    // "userWeb":"1011714446",
                    // "userPhoto":"20151231205835150.jpg",
                    // "userName":"A6还给我啊",
                    // "codePeriod":"18669",
                    // "postID":"132547",
                    // "postTitle":"感悟 心态跟奖品一样重要",
                    // "postContent":"中奖了，有点点欣喜，让我欣喜的是原来真的可以中奖。但是带给我更多的是悲伤，云购 总共花了几千也算是拿到大奖了运气应该也算不错 物流配送还可以接受 我想云购内部应该没有作假 但是我觉得这个东西只能适度玩玩，千万别沉迷其中，多做点实际的工作，这才是出路。 最后 还是要感谢云购 谢谢",
                    // "postAllPic":"20160102223608260.jpg,20160102223618279.jpg,20160102223634470.jpg,20160102224740721.jpg",
                    // "postHits":"5",
                    // "postReplyCount":"0",
                    // "grade":"03",
                    // "gradeName":"云购中将",
                    // "postTimeEx":"昨天 23:08"

                    if (!isset($pointUserList[$item['buyer_id']])) {
                        throw new \Exception("{$item['buyer_id']}对应的积分账户不存在");
                    }
                    $exp = $pointUserList[$item['buyer_id']]['current'];
                    $gradeInfo = $this->modelMemberGrade->getGradeInfo($exp);

                    $picArr = explode(',', $item['pic']);
                    foreach ($picArr as &$pic) {
                        $pic = $this->modelPost->getImagePath($this->baseUrl, $pic);
                    }
                    $item['pic'] = implode(',', $picArr);
                    $datas[] = array(
                        'userWeb' => $item['goods_info']['prize_buyer_id'],
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['goods_info']['prize_buyer_avatar']),
                        'userName' => getBuyerName($item['goods_info']['prize_buyer_name'], $item['goods_info']['prize_buyer_register_by']),
                        'codePeriod' => $item['goods_info']['period'],
                        'postID' => $item['_id'],
                        'postTitle' => $item['title'],
                        'postContent' => $item['content'],
                        'postAllPic' => $item['pic'],
                        'postTimeEx' => date('Y-m-d H:i:s', $item['post_time']->sec),
                        'postHits' => $item['vote_num'],
                        'postReplyCount' => $item['reply_num'],
                        'grade' => str_pad($gradeInfo['current']['level'], 2, '0', STR_PAD_LEFT),
                        'gradeName' => $gradeInfo['current']['name']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 审核晒单通过的接口
     */
    public function passpostAction()
    {
        // http://www.myapplicationmodule.com.com/order/service/passpost?post_id=xxx&point=800&is_recommend=1&user_id=xx&user_name=xx
        try {
            $post_id = ($this->get('post_id', ''));
            if (empty($post_id)) {
                echo ($this->error(-1, '晒单ID为空'));
                return false;
            }
            $user_id = ($this->get('user_id', ''));
            if (empty($user_id)) {
                echo ($this->error(-2, '操作者ID为空'));
                return false;
            }
            $user_name = ($this->get('user_name', ''));
            $point = intval($this->get('point', '0'));
            if (empty($point)) {
                echo ($this->error(-3, '福分为空'));
                return false;
            }
            $is_recommend = intval($this->get('is_recommend', '0'));

            $postInfo = $this->modelPost->getInfoById($post_id);
            if (empty($postInfo)) {
                echo ($this->error(-4, '晒单信息不存在'));
                return false;
            }
            if ($postInfo['state'] == \App\Post\Models\Post::STATE2) {
                echo ($this->error(-5, '晒单已审核通过'));
                return false;
            }
            // 审核通过
            $this->modelPost->pass($post_id, $point, $is_recommend, $user_id, $user_name);

            // 记录会员动态
            $this->modelMemberNews->log($postInfo['goods_info']['prize_buyer_id'], $postInfo['goods_info']['prize_buyer_name'], $postInfo['goods_info']['prize_buyer_avatar'], $postInfo['goods_info']['prize_buyer_register_by'], \App\Member\Models\News::ACTION2, $postInfo['_id'], $postInfo);

            // 订单的状态改成已完成
            $this->modelOrderGoods->finishOrder($postInfo['order_no']);

            // 增加积分
            // 成功晒单 400-1500 500
            $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'suc_list');
            if ($pointsRuleInfo['points'] > $point) {
                $point = $pointsRuleInfo['points'];
            }
            if ($point > 1500) {
                $point = 1500;
            }
            $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $postInfo['buyer_id'], $postInfo['goods_info']['prize_buyer_name'], $postInfo['goods_info']['prize_buyer_avatar'], $postInfo['_id'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
            $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY2, 'suc_list');
            $this->modelPointsService->addOrReduce(POINTS_CATEGORY2, $postInfo['buyer_id'], $postInfo['goods_info']['prize_buyer_name'], $postInfo['goods_info']['prize_buyer_avatar'], $postInfo['_id'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 审核晒单未通过的接口
     */
    public function unpasspostAction()
    {
        // http://www.myapplicationmodule.com.com/order/service/unpasspost?post_id=xxx&fail_reason=xxxx&user_id=xx&user_name=xx
        try {
            $post_id = ($this->get('post_id', ''));
            if (empty($post_id)) {
                echo ($this->error(-1, '晒单ID为空'));
                return false;
            }
            $user_id = ($this->get('user_id', ''));
            if (empty($user_id)) {
                echo ($this->error(-2, '操作者ID为空'));
                return false;
            }
            $user_name = ($this->get('user_name', ''));
            $fail_reason = ($this->get('fail_reason', ''));
            if (empty($fail_reason)) {
                echo ($this->error(-3, '不通过的原因为空'));
                return false;
            }

            $postInfo = $this->modelPost->getInfoById($post_id);
            if (empty($postInfo)) {
                echo ($this->error(-4, '晒单信息不存在'));
                return false;
            }
            if ($postInfo['state'] == \App\Post\Models\Post::STATE2) {
                echo ($this->error(-5, '晒单已审核通过'));
                return false;
            }
            // 审核未通过
            $this->modelPost->unpass($post_id, $fail_reason, $user_id, $user_name);

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}

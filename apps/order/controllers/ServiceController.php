<?php
namespace App\Order\Controllers;

/**
 * 关于订单的各种其他服务
 *
 * @author Admin
 *        
 */
class ServiceController extends ControllerBase
{

    private $modelPost = null;

    private $modelMember = null;

    private $modelMemberFriend = null;

    private $modelMemberConsignee = null;

    private $modelGoods = null;

    private $modelOrderLog = null;

    private $modelOrderGoods = null;

    private $modelOrderStatistics = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelPost = new \App\Post\Models\Post();
        $this->modelMember = new \App\Member\Models\Member();
        $this->modelMemberFriend = new \App\Member\Models\Friend();
        $this->modelMemberConsignee = new \App\Member\Models\Consignee();
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelOrderLog = new \App\Order\Models\Log();
        $this->modelOrderGoods = new \App\Order\Models\Goods();
        $this->modelOrderStatistics = new \App\Order\Models\Statistics();
    }

    /**
     * 总参与次数的接口
     *
     * @return boolean
     */
    public function totalbuycountAction()
    {
        try {
            // http://www.myapplicationmodule.com/order/service/totalbuycount
            $info = $this->modelOrderStatistics->getInfoById(YUNGOU_ORDER_STATISTICS_ID);
            $ret = array();
            $ret['state'] = 0;
            $ret['count'] = $info['success_count'];
            $ret['fundTotal'] = $info['success_count'] * 1.00;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取最新的购买记录的接口
     *
     * @return boolean
     */
    public function getuserbuynewlistAction()
    {
        // http://api.1yyg.com/JPData?action=GetUserBuyNewList&buyID=320256317&fun=jQuery18103058221349492669_1451305421624&_=1451305815047
        // jQuery18103058221349492669_1451305421624({'code':0,'listItems':[{'userName':'扯淡的一元购','goodsID':22612,'goodsName':'苹果（Apple）iPhone 6s Plus 128G版 4G手机','goodsPic':'20151105111557474.jpg','buyID':320256614,'userWeb':'1008152190','userPhoto':'20151227091236615.jpg'},{'userName':'好久没有给我来短信了','goodsID':22803,'goodsName':'十月流年 绅士系列 18K奢华男士钻石戒指','goodsPic':'20151224151349149.jpg','buyID':320256613,'userWeb':'1009043110','userPhoto':'20150909160833474.jpg'},{'userName':'15080****79','goodsID':22622,'goodsName':'费列罗（Ferrero Rocher）榛果威化巧克力 T30粒钻石装 375g 意大利进口','goodsPic':'20151109150043345.jpg','buyID':320256612,'userWeb':'1011510363','userPhoto':'20151214032623160.jpg'},{'userName':'麒麟夺宝一定会中中中','goodsID':22790,'goodsName':'倍轻松（breo）迷你按摩器（2件套）M319','goodsPic':'20151223105047650.jpg','buyID':320256611,'userWeb':'1011328561','userPhoto':'00000000000000000.jpg'},{'userName':'13618****73','goodsID':22504,'goodsName':'苹果（Apple）iPhone 6s 16G版 4G手机','goodsPic':'20150910150825965.jpg','buyID':320256610,'userWeb':'1011494044','userPhoto':'00000000000000000.jpg'},{'userName':'13828****08','goodsID':22552,'goodsName':'苹果（Apple）iPhone 6s Plus 128G版 4G手机','goodsPic':'20150930180313416.jpg','buyID':320256609,'userWeb':'1010674274','userPhoto':'00000000000000000.jpg'},{'userName':'13828****08','goodsID':22612,'goodsName':'苹果（Apple）iPhone 6s Plus 128G版 4G手机','goodsPic':'20151105111557474.jpg','buyID':320256608,'userWeb':'1010674274','userPhoto':'00000000000000000.jpg'},{'userName':'蓝宇航','goodsID':22504,'goodsName':'苹果（Apple）iPhone 6s 16G版 4G手机','goodsPic':'20150910150825965.jpg','buyID':320256607,'userWeb':'1011951588','userPhoto':'20151228192622709.jpg'},{'userName':'中宝马送云购','goodsID':22803,'goodsName':'十月流年 绅士系列 18K奢华男士钻石戒指','goodsPic':'20151224151349149.jpg','buyID':320256606,'userWeb':'1009933805','userPhoto':'00000000000000000.jpg'}]})
        // http://www.myapplicationmodule.com/order/service/getuserbuynewlist?id=xxx&page=1&limit=9
        try {
            $id = ($this->get('id', ''));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '9'));
            $otherConditions = array();
            if (! empty($id)) {
                $otherConditions['_id'] = array(
                    '$gt' => $id
                );
            }
            $list = $this->modelOrderGoods->getUserBuyList('', $page, $limit, 0, 0, 0, $otherConditions);
            $ret['total'] = $list['total'];
            $maxId = '';
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // 'userName':'扯淡的一元购',
                    // 'goodsID':22612,
                    // 'goodsName':'苹果（Apple）iPhone 6s Plus 128G版 4G手机',
                    // 'goodsPic':'20151105111557474.jpg',
                    // 'buyID':320256614,
                    // 'userWeb':'1008152190',
                    // 'userPhoto':'20151227091236615.jpg'
                    $datas[] = array(
                        'userName' => getBuyerName($item['buyer_name'], $item['buyer_register_by']),
                        'codeID' => $item['goods_id'],
                        'goodsID' => $item['goods_commonid'],
                        'goodsName' => $item['goods_name'],
                        'goodsPic' => $this->modelGoods->getImagePath($this->baseUrl, $item['goods_image']),
                        'buyID' => $item['_id'],
                        'userWeb' => $item['buyer_id'],
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['buyer_avatar'])
                    );
                    if (empty($maxId)) {
                        $maxId = $item['_id'];
                    }
                }
            }
            if (empty($maxId)) {
                $maxId = $id;
            }
            $ret['maxId'] = $maxId;
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 商品别,获取最新的购买记录的接口
     *
     * @return boolean
     */
    public function getuserbuylistbycodeAction()
    {
        // http://api.1yyg.com/JPData?action=GetUserBuyListByCode&codeID=2575695&FIdx=1&EIdx=10&isCount=1&fun=jsonp1451400741661&_=1451400800966
        // jsonp1451400741661({"Code":0,"Count":27,"Data":{"Tables":{"BuyList":{"Rows":[{"userName":"13921****67","userPhoto":"00000000000000000.jpg","userWeb":"1010300968","buyNum":"3","buyIP":"124.113.229.91","buyIPAddr":"安徽省滁州市","buyTime":"2015-12-29 22:54:01.402","buyDevice":"3","buyID":"322265804"},{"userName":"给我中大奖撒","userPhoto":"00000000000000000.jpg","userWeb":"1010859990","buyNum":"5","buyIP":"114.95.212.155","buyIPAddr":"上海市","buyTime":"2015-12-29 22:53:50.866","buyDevice":"0","buyID":"322265427"},{"userName":"15632****53","userPhoto":"20151002124554548.jpg","userWeb":"1010395689","buyNum":"4","buyIP":"60.1.244.93","buyIPAddr":"河北省石家庄市","buyTime":"2015-12-29 22:53:47.903","buyDevice":"2","buyID":"322265309"},{"userName":"13901****71","userPhoto":"00000000000000000.jpg","userWeb":"1010870531","buyNum":"5","buyIP":"114.241.0.38","buyIPAddr":"北京市","buyTime":"2015-12-29 22:53:35.852","buyDevice":"3","buyID":"322264872"},{"userName":"云购要倒闭不发货","userPhoto":"00000000000000000.jpg","userWeb":"1011415509","buyNum":"55","buyIP":"223.104.19.13","buyIPAddr":"河南省郑州市","buyTime":"2015-12-29 22:53:32.889","buyDevice":"4","buyID":"322264745"},{"userName":"云购_草你大爷的","userPhoto":"00000000000000000.jpg","userWeb":"1011677996","buyNum":"13","buyIP":"163.177.69.59","buyIPAddr":"广东省肇庆市","buyTime":"2015-12-29 22:53:27.930","buyDevice":"3","buyID":"322264562"},{"userName":"云购_草你大爷的","userPhoto":"00000000000000000.jpg","userWeb":"1011677996","buyNum":"10","buyIP":"163.177.69.59","buyIPAddr":"广东省肇庆市","buyTime":"2015-12-29 22:52:36.899","buyDevice":"3","buyID":"322262725"},{"userName":"云购在不中炸你中部了","userPhoto":"20150927235323564.jpg","userWeb":"1009551685","buyNum":"19","buyIP":"122.13.2.168","buyIPAddr":"广东省","buyTime":"2015-12-29 22:52:36.605","buyDevice":"0","buyID":"322262712"},{"userName":"13859****05","userPhoto":"20140713235009230.jpg","userWeb":"1006569952","buyNum":"1","buyIP":"58.23.91.29","buyIPAddr":"福建省泉州市","buyTime":"2015-12-29 22:52:35.516","buyDevice":"3","buyID":"322262679"},{"userName":"我的单车该换了","userPhoto":"00000000000000000.jpg","userWeb":"1008520381","buyNum":"4","buyIP":"119.142.219.169","buyIPAddr":"广东省潮州市","buyTime":"2015-12-29 22:51:53.637","buyDevice":"3","buyID":"322261145"}]}}}})
        // http://www.myapplicationmodule.com/order/service/getuserbuylistbycode?codeID=xxxx&page=1&limit=9
        try {
            $goods_id = $this->get('codeID', '');
            if (empty($goods_id)) {
                echo ($this->error(- 1, '商品ID为空'));
                return false;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));
            
            $otherConditions = array();
            $otherConditions['goods_id'] = $goods_id;
            $list = $this->modelOrderGoods->getUserBuyList('', $page, $limit, 0, 0, 0, $otherConditions);
            
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "userName":"13921****67",
                    // "userPhoto":"00000000000000000.jpg",
                    // "userWeb":"1010300968",
                    // "buyNum":"3",
                    // "buyIP":"124.113.229.91",
                    // "buyIPAddr":"安徽省滁州市",
                    // "buyTime":"2015-12-29 22:54:01.402",
                    // "buyDevice":"3",
                    // "buyID":"322265804"
                    $datas[] = array(
                        'userName' => getBuyerName($item['buyer_name'], $item['buyer_register_by']),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['buyer_avatar']),
                        'userWeb' => $item['buyer_id'],
                        'buyNum' => $item['purchase_num'],
                        'buyIP' => $item['buyer_ip'],
                        'buyIPAddr' => convertIp($item['buyer_ip']),
                        'buyTime' => getMilliTime4Show($item['purchase_time']),
                        'buyDevice' => 0,
                        'buyID' => $item['_id']
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
     * 商品别,获取参与记录的接口
     * 这个用在已揭晓的商品页面
     * 但是和接口getuserbuylistbycode好像一模一样的
     *
     * @return boolean
     */
    public function getuserbuylistbycodeendAction()
    {
        // http://api.1yyg.com/JPData?action=GetUserBuyListByCodeEnd&codeID=2575690&FIdx=1&EIdx=10&isCount=1&fun=jsonp1451400368062&_=1451400431509
        // jsonp1451400368062({"Code":0,"Count":37,"Data":{"Tables":{"BuyList":{"Rows":[{"userName":"13774****76","userPhoto":"00000000000000000.jpg","userWeb":"1011530880","buyNum":"4","buyIP":"220.249.163.61","buyIPAddr":"福建省福州市连江县","buyTime":"2015-12-29 22:13:18.849","buyDevice":"4","buyID":"322168118"},{"userName":"灵宝人爱云购","userPhoto":"00000000000000000.jpg","userWeb":"1010453808","buyNum":"2","buyIP":"123.183.47.209","buyIPAddr":"河北省保定市","buyTime":"2015-12-29 22:13:17.899","buyDevice":"2","buyID":"322168092"},{"userName":"云购大骗子不爱你了哼","userPhoto":"20151229182331833.jpg","userWeb":"1011353982","buyNum":"1","buyIP":"120.33.178.105","buyIPAddr":"福建省泉州市","buyTime":"2015-12-29 22:13:15.768","buyDevice":"4","buyID":"322168029"},{"userName":"短信响起来大奖是我个","userPhoto":"00000000000000000.jpg","userWeb":"1009068170","buyNum":"3","buyIP":"113.110.63.233","buyIPAddr":"广东省揭阳市","buyTime":"2015-12-29 22:13:15.745","buyDevice":"3","buyID":"322168028"},{"userName":"给老婆的手机","userPhoto":"20151027155023250.jpg","userWeb":"1008627117","buyNum":"4","buyIP":"113.85.55.71","buyIPAddr":"广东省潮州市","buyTime":"2015-12-29 22:13:11.175","buyDevice":"3","buyID":"322167800"},{"userName":"财神保佑求中汽车","userPhoto":"00000000000000000.jpg","userWeb":"1011508443","buyNum":"5","buyIP":"119.125.101.77","buyIPAddr":"广东省梅州市平远县","buyTime":"2015-12-29 22:13:10.579","buyDevice":"0","buyID":"322167774"},{"userName":"5288云狗鸡巴毛炒久菜","userPhoto":"20151229091029745.jpg","userWeb":"1010736799","buyNum":"20","buyIP":"14.220.178.159","buyIPAddr":"广东省东莞市","buyTime":"2015-12-29 22:13:10.489","buyDevice":"4","buyID":"322167769"},{"userName":"希望能中个奖过年","userPhoto":"20151116014152909.jpg","userWeb":"1010546286","buyNum":"12","buyIP":"117.136.78.86","buyIPAddr":"中国","buyTime":"2015-12-29 22:13:08.007","buyDevice":"4","buyID":"322167702"},{"userName":"我日你妹的云狗","userPhoto":"20151205122034256.jpg","userWeb":"1010626927","buyNum":"3","buyIP":"117.136.40.57","buyIPAddr":"广东省广州市","buyTime":"2015-12-29 22:13:07.970","buyDevice":"4","buyID":"322167698"},{"userName":"楼兰臻品","userPhoto":"20150620154934693.jpg","userWeb":"1008466950","buyNum":"3","buyIP":"114.111.166.138","buyIPAddr":"北京市","buyTime":"2015-12-29 22:12:58.214","buyDevice":"0","buyID":"322167381"}]}}}})
        // http://www.myapplicationmodule.com/order/service/getuserbuylistbycodeend?codeID=xxxx&page=1&limit=10
        try {
            $goods_id = $this->get('codeID', '');
            if (empty($goods_id)) {
                echo ($this->error(- 1, '商品ID为空'));
                return false;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));
            
            $otherConditions = array();
            $otherConditions['goods_id'] = $goods_id;
            $list = $this->modelOrderGoods->getUserBuyList('', $page, $limit, 0, 0, 0, $otherConditions);
            
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "userName":"13921****67",
                    // "userPhoto":"00000000000000000.jpg",
                    // "userWeb":"1010300968",
                    // "buyNum":"3",
                    // "buyIP":"124.113.229.91",
                    // "buyIPAddr":"安徽省滁州市",
                    // "buyTime":"2015-12-29 22:54:01.402",
                    // "buyDevice":"3",
                    // "buyID":"322265804"
                    $datas[] = array(
                        'userName' => getBuyerName($item['buyer_name'], $item['buyer_register_by']),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['buyer_avatar']),
                        'userWeb' => $item['buyer_id'],
                        'buyNum' => $item['purchase_num'],
                        'buyIP' => $item['buyer_ip'],
                        'buyIPAddr' => convertIp($item['buyer_ip']),
                        'buyTime' => getMilliTime4Show($item['purchase_time']),
                        'buyDevice' => 0,
                        'buyID' => $item['_id']
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
     * 历史云购记录的接口
     *
     * @return boolean
     */
    public function gethistorybuyrecordAction()
    {
        // http://api.1yyg.com/JPData?action=getHistoryBuyRecord&FIdx=1&EIdx=20&BTime=2016-01-01%2019:16:00&ETime=2016-01-01%2020:16:59.999&isCount=1&fun=jsonp1451650529375&_=1451650635447
        // jsonp1451650529375({'code':0,'count':107282,'data':[{"buyID":327888632,"buyTime":"2016-01-01 20:16:58.999","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2461850,"buyNum":1,"goodsPeriod":150,"goodsName":"苹果（Apple）MacBook Air MJVG2CH/A 13.3英寸笔记本电脑"},{"buyID":327888631,"buyTime":"2016-01-01 20:16:58.997","timeCodeVal":"","buyName":"15106****87","userWeb":"1010816804","buyCode":2699913,"buyNum":1,"goodsPeriod":377,"goodsName":"亚马逊 Kindle Paperwhite 3 全新升级版电子书阅读器 黑色"},{"buyID":327888630,"buyTime":"2016-01-01 20:16:58.996","timeCodeVal":"","buyName":"施成翰D5555","userWeb":"1009569726","buyCode":2613274,"buyNum":140,"goodsPeriod":5425,"goodsName":"苹果（Apple）iPhone 6s Plus 16G版 4G手机"},{"buyID":327888629,"buyTime":"2016-01-01 20:16:58.980","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2213326,"buyNum":1,"goodsPeriod":4388,"goodsName":"苹果（Apple）iPhone 6 Plus A1524 128G版 4G手机"},{"buyID":327888628,"buyTime":"2016-01-01 20:16:58.942","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2705472,"buyNum":1,"goodsPeriod":85,"goodsName":"十月流年 绅士系列 18K奢华男士钻石戒指"},{"buyID":327888627,"buyTime":"2016-01-01 20:16:58.927","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2647772,"buyNum":1,"goodsPeriod":969,"goodsName":"苹果（Apple）MacBook Pro MF840CH/A 13.3英寸宽屏笔记本电脑"},{"buyID":327888626,"buyTime":"2016-01-01 20:16:58.907","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2665380,"buyNum":1,"goodsPeriod":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyID":327888625,"buyTime":"2016-01-01 20:16:58.892","timeCodeVal":"","buyName":"2016喜中大奖","userWeb":"1011889694","buyCode":2568770,"buyNum":5,"goodsPeriod":5098,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyID":327888624,"buyTime":"2016-01-01 20:16:58.887","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2666349,"buyNum":1,"goodsPeriod":467,"goodsName":"平安银行 平安梯形金条 Au9999 200g"},{"buyID":327888623,"buyTime":"2016-01-01 20:16:58.865","timeCodeVal":"","buyName":"2016喜中大奖","userWeb":"1011889694","buyCode":2680348,"buyNum":5,"goodsPeriod":3029,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyID":327888622,"buyTime":"2016-01-01 20:16:58.864","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2608285,"buyNum":1,"goodsPeriod":602,"goodsName":"苹果（Apple）Apple Watch 38毫米不锈钢表壳搭配米兰尼斯表带 MJ322CH/A"},{"buyID":327888621,"buyTime":"2016-01-01 20:16:58.849","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2679258,"buyNum":1,"goodsPeriod":22,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyID":327888620,"buyTime":"2016-01-01 20:16:58.843","timeCodeVal":"","buyName":"2016喜中大奖","userWeb":"1011889694","buyCode":2601733,"buyNum":5,"goodsPeriod":1569,"goodsName":"苹果（Apple）iPhone 6s Plus 16G版 4G手机"},{"buyID":327888619,"buyTime":"2016-01-01 20:16:58.831","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2711351,"buyNum":1,"goodsPeriod":21138,"goodsName":"苹果（Apple）iPhone 6s 64G版 4G手机"},{"buyID":327888618,"buyTime":"2016-01-01 20:16:58.816","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2669490,"buyNum":1,"goodsPeriod":11888,"goodsName":"苹果（Apple）iPhone 6s 128G版 4G手机"},{"buyID":327888617,"buyTime":"2016-01-01 20:16:58.810","timeCodeVal":"","buyName":"2016喜中大奖","userWeb":"1011889694","buyCode":2626030,"buyNum":5,"goodsPeriod":39,"goodsName":"微软（Microsoft）Surface Pro 4专业版 128G 平板电脑 i5 4G内存"},{"buyID":327888616,"buyTime":"2016-01-01 20:16:58.797","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2748251,"buyNum":1,"goodsPeriod":1732,"goodsName":"中国农业银行传世之宝 \"金元宝\" Au9999 200g"},{"buyID":327888615,"buyTime":"2016-01-01 20:16:58.789","timeCodeVal":"","buyName":"买云狗输没钱","userWeb":"1009574011","buyCode":2665380,"buyNum":5,"goodsPeriod":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyID":327888614,"buyTime":"2016-01-01 20:16:58.783","timeCodeVal":"","buyName":"我要宝马与美","userWeb":"1011283379","buyCode":2748013,"buyNum":1,"goodsPeriod":440,"goodsName":"平安银行 平安传家如意算盘 Au9999 100g"},{"buyID":327888613,"buyTime":"2016-01-01 20:16:58.773","timeCodeVal":"","buyName":"13828****41","userWeb":"1012693115","buyCode":2725044,"buyNum":1,"goodsPeriod":58858,"goodsName":"苹果（Apple）iPhone 6 A1586 16G版 4G手机"}]})
        // http://www.myapplicationmodule.com/order/service/gethistorybuyrecord?page=1&limit=20&BTime=2016-01-01%2019:16:00&ETime=2016-01-01%2020:16:59.999
        try {
            $beginTime = $this->get('BTime', '');
            if (empty($beginTime)) {
                echo ($this->error(- 1, '开始时间为空'));
                return false;
            }
            $endTime = $this->get('ETime', '');
            if (empty($endTime)) {
                echo ($this->error(- 2, '结束时间为空'));
                return false;
            }
            
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '20'));
            
            $otherConditions = array();
            if (! empty($beginTime)) {
                $beginTime = (strtotime($beginTime));
            }
            if (! empty($endTime)) {
                $endTime = (strtotime($endTime));
            }
            $list = $this->modelOrderGoods->getUserBuyList('', $page, $limit, 0, $beginTime, $endTime, $otherConditions);
            
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "buyID":327888632,
                    // "buyTime":"2016-01-01 20:16:58.999",
                    // "timeCodeVal":"",
                    // "buyName":"我要宝马与美",
                    // "userWeb":"1011283379",
                    // "buyCode":2461850,
                    // "buyNum":1,
                    // "goodsPeriod":150,
                    // "goodsName":"苹果（Apple）MacBook Air MJVG2CH/A 13.3英寸笔记本电脑"
                    $datas[] = array(
                        'buyID' => $item['_id'],
                        'buyTime' => getMilliTime4Show($item['purchase_time']),
                        'timeCodeVal' => '',
                        'buyName' => getBuyerName($item['buyer_name'], $item['buyer_register_by']),
                        'userWeb' => $item['buyer_id'],
                        'buyCode' => $item['goods_id'],
                        'buyNum' => $item['purchase_num'],
                        'buyIP' => $item['buyer_ip'],
                        'goodsPeriod' => $item['goods_period'],
                        'goodsName' => $item['goods_name']
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
     * 个人主页上的
     * 获取云购记录和获得的商品和晒单的接口
     *
     * @throws \Exception
     * @throws Exception
     * @return boolean
     */
    public function getuserpagebuyrafpostAction()
    {
        // http://u.1yyg.com/JPData?action=getUserPageBuyRafPost&&type=0&userID=9563477&FIdx=1&EIdx=10&isCount=1&fun=jsonp1452090079362&_=1452090079728
        // jsonp1452090079362({"code":0,"num":8,"str":{"listItems":[{"codeID":2542873,"goodsSName":"苹果（Apple）iPhone 6s 16G版 4G手机","goodsPic":"20150910150825965.jpg","codeState":"3","codePeriod":"21158","codePrice":"5188.00","codeQuantity":"5188","codeSales":"5188","codeRNO":"10001930","codeRTime":"2015-12-20 21:37:19","userName":"几十万该中宝马了","userPhoto":"20151213160405492.jpg","userWeb":"1011010841","codeType":"0","limitBuy":"0","buyNum":"1","buyTime":"2015-12-20 21:36"},{"codeID":2509590,"goodsSName":"苹果（Apple）iPad Pro 12.9 英寸平板电脑 32G WiFi版","goodsPic":"20151023164405174.jpg","codeState":"3","codePeriod":"1270","codePrice":"6088.00","codeQuantity":"6088","codeSales":"6088","codeRNO":"10001796","codeRTime":"2015-12-20 22:06:34","userName":"轰284629214","userPhoto":"20160104214704859.jpg","userWeb":"1010037107","codeType":"0","limitBuy":"0","buyNum":"2","buyTime":"2015-12-20 21:36"},{"codeID":2450928,"goodsSName":"苹果（Apple）iMac MK462CH/A 27英寸一体电脑","goodsPic":"20151026092646574.jpg","codeState":"3","codePeriod":"438","codePrice":"13488.00","codeQuantity":"13488","codeSales":"13488","codeRNO":"10007248","codeRTime":"2015-12-20 21:45:20","userName":"一今一日一必一中一","userPhoto":"20151214001336668.jpg","userWeb":"1008906059","codeType":"0","limitBuy":"0","buyNum":"1","buyTime":"2015-12-20 21:32"},{"codeID":2542665,"goodsSName":"苹果（Apple）iPhone 6s 16G版 4G手机","goodsPic":"20150910150825965.jpg","codeState":"3","codePeriod":"21146","codePrice":"5188.00","codeQuantity":"5188","codeSales":"5188","codeRNO":"10004588","codeRTime":"2015-12-20 21:19:00","userName":"集中力","userPhoto":"20151224064042329.jpg","userWeb":"1011741168","codeType":"0","limitBuy":"0","buyNum":"1","buyTime":"2015-12-20 21:19"},{"codeID":2509588,"goodsSName":"苹果（Apple）iPad Pro 12.9 英寸平板电脑 32G WiFi版","goodsPic":"20151023164405174.jpg","codeState":"3","codePeriod":"1269","codePrice":"6088.00","codeQuantity":"6088","codeSales":"6088","codeRNO":"10002598","codeRTime":"2015-12-20 21:21:44","userName":"这样不中奖毫无意思","userPhoto":"00000000000000000.jpg","userWeb":"1011117589","codeType":"0","limitBuy":"0","buyNum":"2","buyTime":"2015-12-20 21:19"},{"codeID":2162400,"goodsSName":"苹果（Apple）iPad Air 2 9.7英寸平板电脑 16G WiFi版","goodsPic":"20141024155736176.jpg","codeState":"3","codePeriod":"7275","codePrice":"3488.00","codeQuantity":"3488","codeSales":"3488","codeRNO":"10001349","codeRTime":"2015-11-27 23:51:21","userName":"开启无敌模式大杀","userPhoto":"00000000000000000.jpg","userWeb":"1008784398","codeType":"0","limitBuy":"0","buyNum":"1","buyTime":"2015-11-27 23:50"},{"codeID":1992345,"goodsSName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机","goodsPic":"20151105111557474.jpg","codeState":"3","codePeriod":"709","codePrice":"8488.00","codeQuantity":"8488","codeSales":"8488","codeRNO":"10005690","codeRTime":"2015-11-19 10:41:30","userName":"中部宝马娶某","userPhoto":"00000000000000000.jpg","userWeb":"1010143659","codeType":"3","limitBuy":"5","buyNum":"1","buyTime":"2015-11-19 10:30"},{"codeID":1856479,"goodsSName":"蒙牛 特仑苏 纯牛奶 250mlx12盒 礼盒装","goodsPic":"20140813160215287.jpg","codeState":"3","codePeriod":"40428","codePrice":"65.00","codeQuantity":"65","codeSales":"65","codeRNO":"10000031","codeRTime":"2015-11-06 20:42:24","userName":"老不中买最后一次","userPhoto":"00000000000000000.jpg","userWeb":"1008660584","codeType":"0","limitBuy":"0","buyNum":"1","buyTime":"2015-11-06 20:41"}]}})
        // http://www.myapplicationmodule.com/order/service/getuserpagebuyrafpost?type=0&userID=9563477&limit=5&page=1
        try {
            // 0 云购记录 1 获得的商品 2 晒单
            $type = intval($this->get('type', '0'));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));
            $userID = $this->get('userID', '');
            if (empty($userID)) {
                echo ($this->error(- 1, 'userID为空'));
                return false;
            }
            $memberInfo = $this->modelMember->getInfoById($userID);
            if (empty($memberInfo)) {
                echo ($this->error(- 2, 'userID不正确'));
                return false;
            }
            $isCan = false;
            if (empty($type)) {
                // 0 云购记录
                // 个人主页-云购记录 0:所有人可见 1:好友可见 2:仅自己可见
                $isCan = true;
                $limit = empty($memberInfo['privacy']['buyShowNum']) ? $limit : $memberInfo['privacy']['buyShowNum'];
                if (! empty($memberInfo['privacy']['buySet'])) {
                    if (empty($_SESSION['member_id'])) {
                        $isCan = false;
                    } else {
                        $isCan = $this->modelMember->isCanBuySee($memberInfo, $_SESSION['member_id']);
                    }
                }
            } elseif ($type == 1) {
                // 1 获得的商品
                // 个人主页-获得的商品 0:所有人可见 1:好友可见 2:仅自己可见
                $isCan = true;
                $limit = empty($memberInfo['privacy']['rafShowNum']) ? $limit : $memberInfo['privacy']['rafShowNum'];
                if (! empty($memberInfo['privacy']['rafSet'])) {
                    if (empty($_SESSION['member_id'])) {
                        $isCan = false;
                    } else {
                        $isCan = $this->modelMember->isCanRafSee($memberInfo, $_SESSION['member_id']);
                    }
                }
            } elseif ($type == 2) {
                // 2 晒单
                // 个人主页-晒单 0:所有人可见 1:好友可见 2:仅自己可见
                $isCan = true;
                $limit = empty($memberInfo['privacy']['postShowNum']) ? $limit : $memberInfo['privacy']['postShowNum'];
                if (! empty($memberInfo['privacy']['postSet'])) {
                    if (empty($_SESSION['member_id'])) {
                        $isCan = false;
                    } else {
                        $isCan = $this->modelMember->isCanPostSee($memberInfo, $_SESSION['member_id']);
                    }
                }
            }
            $ret = array();
            if ($isCan) {
                if (empty($type)) {
                    // 0 云购记录
                    $list = $this->modelOrderGoods->getUserBuyList($userID, $page, $limit);
                } elseif ($type == 1) {
                    // 1 获得的商品
                    $list = $this->modelOrderGoods->getUserWinList($userID, $page, $limit);
                } elseif ($type == 2) {
                    // 2 晒单
                    // 已晒单
                    $otherConditions = array();
                    $otherConditions['state'] = \App\Post\Models\Post::STATE2;
                    $list = $this->modelPost->getPageListByBuyerId($userID, $page, $limit, $otherConditions);
                }
                
                $ret['total'] = $list['total'];
                $datas = array();
                if (! empty($list['datas'])) {
                    foreach ($list['datas'] as $item) {
                        if ($type == 2) {
                            // 2 晒单
                            // "postID":"165660",
                            // "postPic":"20160304195137145.jpg",
                            // "postTitle":"特别实用的奖品",
                            // "postContent":"最近开始玩云购。妈妈看我中了旺仔牛奶和矿泉水就说让我给她中点实用的厨房用品。那天看…",
                            // "postTime":"昨天 19:55",
                            // "postHits":"0",
                            // "postReplyCount":"0",
                            // "replyUserPhoto":"",
                            // "replyUserName":"",
                            // "replyContent":"",
                            // "replyUserWeb":"",
                            // "postAllPic":"20160304195137145.jpg,20160304195147784.jpg,20160304195155912.jpg,20160304195203728.jpg"
                            $picArr = explode(',', $item['pic']);
                            foreach ($picArr as &$pic) {
                                $pic = $this->modelPost->getImagePath($this->baseUrl, $pic);
                            }
                            $item['pic'] = implode(',', $picArr);
                            $datas[] = array(
                                'postId' => $item['_id'],
                                'postPic' => $picArr[0],
                                'postTitle' => $item['title'],
                                'postContent' => $item['content'],
                                'postTime' => date('Y-m-d H:i:s', $item['post_time']->sec),
                                'postHits' => $item['vote_num'],
                                'postReplyCount' => $item['reply_num'],
                                
                                'replyUserPhoto' => '',
                                'replyUserName' => '',
                                'replyUserWeb' => '',
                                'replyContent' => '',
                                'postAllPic' => $item['pic']
                            );
                        } else {
                            // "codeID":2542873,
                            // "goodsSName":"苹果（Apple）iPhone 6s 16G版 4G手机",
                            // "goodsPic":"20150910150825965.jpg",
                            // "codeState":"3",
                            // "codePeriod":"21158",
                            // "codePrice":"5188.00",
                            // "codeQuantity":"5188",
                            // "codeSales":"5188",
                            // "codeRNO":"10001930",
                            // "codeRTime":"2015-12-20 21:37:19",
                            // "userName":"几十万该中宝马了",
                            // "userPhoto":"20151213160405492.jpg",
                            // "userWeb":"1011010841",
                            // "codeType":"0",
                            // "limitBuy":"0",
                            // "buyNum":"1",
                            // "buyTime":"2015-12-20 21:36"
                            // "buyIP":"124.113.229.91",
                            // "buyIPAddr":"安徽省滁州市",
                            // "buyDevice":"3",
                            // "buyID":"322265804"
                            
                            $codeSales = $item['goods_total_person_time'];
                            if ($item['state'] == \App\Order\Models\Goods::STATE1) {
                                $goodsInfo = $this->modelGoods->getInfoById($item['goods_id']);
                                $codeSales = $goodsInfo['purchase_person_time'];
                            }
                            $datas[] = array(
                                'codeID' => $item['goods_id'],
                                'goodsSName' => $item['goods_name'],
                                'goodsPic' => $this->modelGoods->getImagePath($this->baseUrl, $item['goods_image']),
                                'codeState' => $item['state'],
                                'codePeriod' => $item['goods_period'],
                                'codePrice' => showPrice($item['goods_price'], 2),
                                'codeQuantity' => $item['goods_total_person_time'],
                                'codeSales' => $codeSales,
                                'codeRNO' => empty($item['prize_code']) ? '' : $item['prize_code'],
                                'codeRTime' => empty($item['prize_time']) ? '' : getMilliTime4Show($item['prize_time']),
                                'userName' => getBuyerName($item['buyer_name'], $item['buyer_register_by']),
                                'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['buyer_avatar']),
                                'userWeb' => $item['buyer_id'],
                                'buyNum' => $item['purchase_num'],
                                'buyIP' => $item['buyer_ip'],
                                'buyIPAddr' => convertIp($item['buyer_ip']),
                                'buyTime' => getMilliTime4Show($item['purchase_time']),
                                'buyDevice' => 0,
                                'buyID' => $item['buyer_id'],
                                'codeType' => 0,
                                'limitBuy' => 0
                            );
                        }
                    }
                }
                $ret['datas'] = $datas;
            } else {
                $ret['total'] = 0;
                $ret['datas'] = array();
            }
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 最新云购记录的接口
     *
     * @return boolean
     */
    public function getlastestrecordAction()
    {
        // http://api.1yyg.com/JPData?action=getLastestRecord&fun=jsonp1451650692128&_=1451650742442
        // jsonp1451650692128({"code":0,"data":[{"buyTime":"2016-01-01 20:20:11.376","buyName":"13690****36","userWeb":"1008738425","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:11.333","buyName":"18621****20","userWeb":"1012379409","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:11.308","buyName":"给我中个奖苹","userWeb":"1011622852","buyCode":2691225,"buyNum":2,"period":32938,"goodsName":"维他 柠檬茶 250mlx16盒 整箱装"},{"buyTime":"2016-01-01 20:20:11.301","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2761288,"buyNum":1,"period":47308,"goodsName":"伊利 安慕希常温酸牛奶 205mlx12盒 礼盒装"},{"buyTime":"2016-01-01 20:20:11.289","buyName":"给我中个奖苹","userWeb":"1011622852","buyCode":2665380,"buyNum":4,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:11.284","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2613275,"buyNum":1,"period":5426,"goodsName":"苹果（Apple）iPhone 6s Plus 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:11.268","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2639764,"buyNum":1,"period":2152,"goodsName":"悦胜 波士顿大龙虾 1000-1200g/只 加拿大进口"},{"buyTime":"2016-01-01 20:20:11.245","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2705472,"buyNum":2,"period":85,"goodsName":"十月流年 绅士系列 18K奢华男士钻石戒指"},{"buyTime":"2016-01-01 20:20:11.230","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2543688,"buyNum":1,"period":4470,"goodsName":"美的（Midea） W12PCS505E 电压力锅 5L 多功能电脑版"},{"buyTime":"2016-01-01 20:20:11.216","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2679258,"buyNum":2,"period":22,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2016-01-01 20:20:11.200","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2748013,"buyNum":2,"period":440,"goodsName":"平安银行 平安传家如意算盘 Au9999 100g"},{"buyTime":"2016-01-01 20:20:11.186","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2780305,"buyNum":2,"period":2604,"goodsName":"费列罗（Ferrero Rocher）榛果威化巧克力 T30粒钻石装 375g 意大利进口"},{"buyTime":"2016-01-01 20:20:11.177","buyName":"小东西你快来","userWeb":"1011525527","buyCode":2680348,"buyNum":1,"period":3029,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:11.167","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2568770,"buyNum":1,"period":5098,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:11.146","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2778848,"buyNum":2,"period":8867,"goodsName":"美的（Midea）MB-WFS4017TM 4L智能不锈钢电饭煲"},{"buyTime":"2016-01-01 20:20:11.138","buyName":"汽车对我来说","userWeb":"1012851029","buyCode":2763772,"buyNum":5,"period":7694,"goodsName":"福临门 优质 东北大米 4kgx2包"},{"buyTime":"2016-01-01 20:20:11.137","buyName":"云购哥哥我想","userWeb":"1010263051","buyCode":2665380,"buyNum":2,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:11.131","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2725044,"buyNum":1,"period":58858,"goodsName":"苹果（Apple）iPhone 6 A1586 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:11.106","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2778038,"buyNum":1,"period":2208,"goodsName":"苹果（Apple）iPhone 5S 16G版 3G手机 电信版"},{"buyTime":"2016-01-01 20:20:11.106","buyName":"13533****99","userWeb":"1011994567","buyCode":2666349,"buyNum":1,"period":467,"goodsName":"平安银行 平安梯形金条 Au9999 200g"},{"buyTime":"2016-01-01 20:20:11.096","buyName":"13433****35","userWeb":"1010632432","buyCode":2665380,"buyNum":5,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:11.091","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2710008,"buyNum":1,"period":12710,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:11.091","buyName":"粤de等待","userWeb":"1009351162","buyCode":2725044,"buyNum":5,"period":58858,"goodsName":"苹果（Apple）iPhone 6 A1586 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:11.074","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2659366,"buyNum":1,"period":12513,"goodsName":"陶华碧老干妈 风味豆豉油制辣椒 280g/瓶x3瓶"},{"buyTime":"2016-01-01 20:20:11.069","buyName":"粤de等待","userWeb":"1009351162","buyCode":2673486,"buyNum":12,"period":1215,"goodsName":"金士顿（Kingston）DT SE9H 32GB 金属U盘 银色亮薄"},{"buyTime":"2016-01-01 20:20:11.064","buyName":"最后一次说拜","userWeb":"1011927916","buyCode":2665380,"buyNum":2,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:11.058","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2717214,"buyNum":1,"period":54455,"goodsName":"蒙牛 特仑苏 纯牛奶 250mlx12盒 礼盒装"},{"buyTime":"2016-01-01 20:20:11.050","buyName":"粤de等待","userWeb":"1009351162","buyCode":2613851,"buyNum":5,"period":29768,"goodsName":"苹果（Apple）iPhone 5S 16G版 4G手机 开放版"},{"buyTime":"2016-01-01 20:20:11.040","buyName":"云购给点面子","userWeb":"1012637335","buyCode":2680348,"buyNum":2,"period":3029,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.987","buyName":"生活没激情云","userWeb":"1008254622","buyCode":2665380,"buyNum":5,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:10.965","buyName":"没运气就只是","userWeb":"1004257180","buyCode":2575386,"buyNum":1,"period":2685,"goodsName":"桂格 高纤燕麦乳饮品 麦香原味 250mlx12瓶 礼盒装"},{"buyTime":"2016-01-01 20:20:10.923","buyName":"至少让我中个","userWeb":"1010825588","buyCode":2648477,"buyNum":1,"period":1518,"goodsName":"苹果（Apple）MacBook Pro MF839CH/A 13.3英寸宽屏笔记本电脑"},{"buyTime":"2016-01-01 20:20:10.906","buyName":"至少让我中个","userWeb":"1010825588","buyCode":2647814,"buyNum":1,"period":1075,"goodsName":"苹果（Apple）MacBook 12英寸笔记本电脑 256GB 闪存"},{"buyTime":"2016-01-01 20:20:10.884","buyName":"13682****12","userWeb":"1003944614","buyCode":2251861,"buyNum":2,"period":805,"goodsName":"美图（Meitu）美图M4 32GB 移动联通4G手机 薄荷绿"},{"buyTime":"2016-01-01 20:20:10.879","buyName":"实在太难中了","userWeb":"1011647927","buyCode":2747050,"buyNum":5,"period":42,"goodsName":"外星人（Alienware）ALW17ER-3728 17.3英寸游戏笔记本电脑"},{"buyTime":"2016-01-01 20:20:10.865","buyName":"吕思乔","userWeb":"1011462146","buyCode":2691915,"buyNum":10,"period":10259,"goodsName":"苹果（Apple）iPad Air 2 9.7英寸平板电脑 16G WiFi版"},{"buyTime":"2016-01-01 20:20:10.789","buyName":"13500****29","userWeb":"1012546401","buyCode":2754125,"buyNum":1,"period":27347,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.610","buyName":"都他妈骗人的","userWeb":"1010009618","buyCode":2679258,"buyNum":1,"period":22,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2016-01-01 20:20:10.607","buyName":"H-相信云购8相","userWeb":"1012494175","buyCode":2702544,"buyNum":1,"period":1043,"goodsName":"苹果（Apple）Apple Watch 42毫米不锈钢表壳搭配米兰尼斯表带 MJ3Y2CH/A"},{"buyTime":"2016-01-01 20:20:10.572","buyName":"再見删除了-8","userWeb":"1008804023","buyCode":2568770,"buyNum":5,"period":5098,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.571","buyName":"云梦想何时成","userWeb":"1011092548","buyCode":2748013,"buyNum":1,"period":440,"goodsName":"平安银行 平安传家如意算盘 Au9999 100g"},{"buyTime":"2016-01-01 20:20:10.545","buyName":"云梦想何时成","userWeb":"1011092548","buyCode":2648559,"buyNum":1,"period":706,"goodsName":"平安银行 平安金标准金条 Au9999 100g"},{"buyTime":"2016-01-01 20:20:10.520","buyName":"云梦想何时成","userWeb":"1011092548","buyCode":2748251,"buyNum":1,"period":1732,"goodsName":"中国农业银行传世之宝 〃金元宝〃 Au9999 200g"},{"buyTime":"2016-01-01 20:20:10.484","buyName":"换了这么多名","userWeb":"1011532436","buyCode":2680348,"buyNum":5,"period":3029,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.475","buyName":"云梦想何时成","userWeb":"1011092548","buyCode":2666349,"buyNum":1,"period":467,"goodsName":"平安银行 平安梯形金条 Au9999 200g"},{"buyTime":"2016-01-01 20:20:10.438","buyName":"云梦想何时成","userWeb":"1011092548","buyCode":2568770,"buyNum":1,"period":5098,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.437","buyName":"17750****05","userWeb":"1011567799","buyCode":2624024,"buyNum":3,"period":1739,"goodsName":"阳光少女（Sunmaid）加州葡萄干（罐装）500g 美国进口"},{"buyTime":"2016-01-01 20:20:10.433","buyName":"没中个奖","userWeb":"1011743927","buyCode":2748393,"buyNum":1,"period":16204,"goodsName":"飞科（FLYCO）FS357 旋转式三刀头全身水洗电动剃须刀"},{"buyTime":"2016-01-01 20:20:10.397","buyName":"15058****20","userWeb":"1012531463","buyCode":2626376,"buyNum":1,"period":5096,"goodsName":"斯伯丁（SPALDING）74-221/74-604Y PU材质 室内外兼用 比赛用篮球"},{"buyTime":"2016-01-01 20:20:10.348","buyName":"15916****06","userWeb":"1008494303","buyCode":2568770,"buyNum":3,"period":5098,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.282","buyName":"让我中宝马吧","userWeb":"1011788770","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:10.264","buyName":"13072****37","userWeb":"1012804290","buyCode":2710008,"buyNum":1,"period":12710,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.260","buyName":"明天会更好嗒","userWeb":"1011509645","buyCode":2679258,"buyNum":5,"period":22,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2016-01-01 20:20:10.234","buyName":"明天会更好嗒","userWeb":"1011509645","buyCode":2710008,"buyNum":5,"period":12710,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.204","buyName":"靠中一次可以","userWeb":"1011365367","buyCode":2710008,"buyNum":5,"period":12710,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.203","buyName":"明天会更好嗒","userWeb":"1011509645","buyCode":2665380,"buyNum":5,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:10.186","buyName":"15176****68","userWeb":"1012908874","buyCode":2691225,"buyNum":1,"period":32938,"goodsName":"维他 柠檬茶 250mlx16盒 整箱装"},{"buyTime":"2016-01-01 20:20:10.170","buyName":"明天会更好嗒","userWeb":"1011509645","buyCode":2680348,"buyNum":5,"period":3029,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.128","buyName":"终于等到你你","userWeb":"1009953298","buyCode":2710008,"buyNum":2,"period":12710,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.103","buyName":"亏了以后不玩","userWeb":"1011529150","buyCode":2704303,"buyNum":3,"period":1757,"goodsName":"苹果（Apple）MacBook Pro MF840CH/A 13.3英寸宽屏笔记本电脑"},{"buyTime":"2016-01-01 20:20:10.050","buyName":"2016财运亨通","userWeb":"1011523384","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:10.039","buyName":"没钱给老婆买","userWeb":"1011587801","buyCode":2711354,"buyNum":5,"period":21139,"goodsName":"苹果（Apple）iPhone 6s 64G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.031","buyName":"最后一次让我","userWeb":"1011048539","buyCode":2680348,"buyNum":2,"period":3029,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:10.015","buyName":"中一个啊LLF","userWeb":"1008207546","buyCode":2717214,"buyNum":1,"period":54455,"goodsName":"蒙牛 特仑苏 纯牛奶 250mlx12盒 礼盒装"},{"buyTime":"2016-01-01 20:20:09.952","buyName":"SB星球云购","userWeb":"1010298688","buyCode":2751183,"buyNum":12,"period":12992,"goodsName":"小米（MIUI）红米2A 增强版 移动4G手机"},{"buyTime":"2016-01-01 20:20:09.739","buyName":"13822****61","userWeb":"1011254097","buyCode":2634958,"buyNum":20,"period":2537,"goodsName":"途锐达（TOPRIGHT）蜘蛛侠儿童自行车 16寸"},{"buyTime":"2016-01-01 20:20:09.714","buyName":"老婆我中手机","userWeb":"1011404738","buyCode":2710008,"buyNum":1,"period":12710,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:09.710","buyName":"奥迪沃饿沃来","userWeb":"1009472356","buyCode":2665380,"buyNum":4,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:09.641","buyName":"看来是没这命","userWeb":"1011437711","buyCode":2633680,"buyNum":1,"period":239,"goodsName":"乐视 超级电视 第3代X55（X3-55）55英寸 4K智能网络液晶平板电视（标配挂架）"},{"buyTime":"2016-01-01 20:20:09.608","buyName":"中了宝马就买","userWeb":"1010476994","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:09.579","buyName":"术死你云购","userWeb":"1008746756","buyCode":2711354,"buyNum":4,"period":21139,"goodsName":"苹果（Apple）iPhone 6s 64G版 4G手机"},{"buyTime":"2016-01-01 20:20:09.573","buyName":"给次机会5","userWeb":"1008097840","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:09.523","buyName":"一生只为奥迪","userWeb":"1010697563","buyCode":2754125,"buyNum":1,"period":27347,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:09.522","buyName":"云购尼玛让我","userWeb":"1001174717","buyCode":2697060,"buyNum":1,"period":6908,"goodsName":"好丽友 巧克力派 20枚680g"},{"buyTime":"2016-01-01 20:20:09.495","buyName":"中了投资云购","userWeb":"1012856973","buyCode":2452847,"buyNum":1,"period":2079,"goodsName":"美奈丝 全棉活性印染春夏季床品四件套 200cm*230cm"},{"buyTime":"2016-01-01 20:20:09.455","buyName":"15057****11","userWeb":"1009057479","buyCode":2626104,"buyNum":5,"period":1924,"goodsName":"SKG MY-610 榨汁机"},{"buyTime":"2016-01-01 20:20:09.445","buyName":"13539****33","userWeb":"1011180991","buyCode":2751183,"buyNum":1,"period":12992,"goodsName":"小米（MIUI）红米2A 增强版 移动4G手机"},{"buyTime":"2016-01-01 20:20:09.423","buyName":"六六輑101101","userWeb":"1011414601","buyCode":2583299,"buyNum":30,"period":36609,"goodsName":"小米（MIUI）16000mAh 移动电源"},{"buyTime":"2016-01-01 20:20:09.386","buyName":"在不中卸载云","userWeb":"1011869957","buyCode":2474546,"buyNum":5,"period":784,"goodsName":"苹果（Apple）iPad mini 4 7.9英寸平板电脑 64G WiFi版"},{"buyTime":"2016-01-01 20:20:09.379","buyName":"恭喜您中了奥","userWeb":"1010559308","buyCode":2705472,"buyNum":50,"period":85,"goodsName":"十月流年 绅士系列 18K奢华男士钻石戒指"},{"buyTime":"2016-01-01 20:20:09.372","buyName":"15976****58","userWeb":"1009355582","buyCode":2738060,"buyNum":1,"period":106828,"goodsName":"金龙鱼 优质东北大米 5kg"},{"buyTime":"2016-01-01 20:20:09.299","buyName":"什么都不中云","userWeb":"1010939330","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:09.221","buyName":"我来也哈哈","userWeb":"1010574226","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:09.184","buyName":"云是偶然999","userWeb":"1011720527","buyCode":2450362,"buyNum":6,"period":444,"goodsName":"大疆（DJI）精灵3 Phantom 3 Advanced 高端专业HD航拍飞行器无人机"},{"buyTime":"2016-01-01 20:20:09.171","buyName":"云购真害死人","userWeb":"1010377069","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:09.154","buyName":"六六大顺中中","userWeb":"1009757792","buyCode":2680348,"buyNum":4,"period":3029,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2016-01-01 20:20:09.136","buyName":"13312****95","userWeb":"1011590121","buyCode":2665380,"buyNum":5,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:09.084","buyName":"再不中辆车就","userWeb":"1011097239","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:09.046","buyName":"不中6s不买了","userWeb":"1010126129","buyCode":2577719,"buyNum":1,"period":11236,"goodsName":"利葡（Lipo）面包干 300gx2袋 越南进口"},{"buyTime":"2016-01-01 20:20:08.995","buyName":"梁总大发慈悲","userWeb":"1011850886","buyCode":2754125,"buyNum":1000,"period":27347,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2016-01-01 20:20:08.906","buyName":"13580****72","userWeb":"1012002498","buyCode":2667598,"buyNum":3,"period":581,"goodsName":"创维（Skyworth) 55G7200 55英寸 酷开4K12核智能网络液晶电视"},{"buyTime":"2016-01-01 20:20:08.853","buyName":"LU_XIAO","userWeb":"1010793668","buyCode":2751183,"buyNum":100,"period":12992,"goodsName":"小米（MIUI）红米2A 增强版 移动4G手机"},{"buyTime":"2016-01-01 20:20:08.845","buyName":"啊啊中了真中","userWeb":"1009109170","buyCode":2665380,"buyNum":5,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:08.698","buyName":"13960****11","userWeb":"1012168655","buyCode":2451377,"buyNum":3,"period":509,"goodsName":"苹果（Apple）iMac MK462CH/A 27英寸一体电脑"},{"buyTime":"2016-01-01 20:20:08.631","buyName":"18705****54","userWeb":"1011409270","buyCode":2665380,"buyNum":1,"period":32,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2016-01-01 20:20:08.622","buyName":"不骂你云让我","userWeb":"1012576754","buyCode":2716140,"buyNum":1,"period":2061,"goodsName":"捷波朗（Jabra）MINI 迷你 蓝牙耳机"},{"buyTime":"2016-01-01 20:20:08.601","buyName":"不骂你云让我","userWeb":"1012576754","buyCode":2705472,"buyNum":1,"period":85,"goodsName":"十月流年 绅士系列 18K奢华男士钻石戒指"},{"buyTime":"2016-01-01 20:20:08.583","buyName":"不骂你云让我","userWeb":"1012576754","buyCode":2688064,"buyNum":1,"period":860,"goodsName":"三星（Samsung）Galaxy S6 Edge+（G9280）32G版 4G手机 全网通"},{"buyTime":"2016-01-01 20:20:08.564","buyName":"不骂你云让我","userWeb":"1012576754","buyCode":2590512,"buyNum":1,"period":22,"goodsName":"IPS 自平衡电动车 Lhotz 340WH 智能代步独轮体感车"},{"buyTime":"2016-01-01 20:20:08.547","buyName":"不骂你云让我","userWeb":"1012576754","buyCode":2545438,"buyNum":1,"period":665,"goodsName":"福玛特（FMART）E-R620C 智能家用扫地机器人吸尘器"}]})
        // http://www.myapplicationmodule.com/order/service/getlastestrecord&id=
        try {
            $id = ($this->get('id', ''));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '100'));
            $otherConditions = array();
            if (! empty($id)) {
                $otherConditions['_id'] = array(
                    '$gt' => $id
                );
            }
            $endTime = getMilliTime();
            $list = $this->modelOrderGoods->getUserBuyList('', $page, $limit, 0, 0, $endTime, $otherConditions);
            
            $ret = array();
            $ret['total'] = $list['total'];
            $maxId = '';
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "buyTime":"2016-01-01 20:20:11.376",
                    // "buyName":"13690****36",
                    // "userWeb":"1008738425",
                    // "buyCode":2665380,
                    // "buyNum":1,
                    // "period":32,
                    // "goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"
                    $datas[] = array(
                        'buyID' => $item['_id'],
                        'buyTime' => getMilliTime4Show($item['purchase_time']),
                        'timeCodeVal' => '',
                        'buyName' => getBuyerName($item['buyer_name'], $item['buyer_register_by']),
                        'userWeb' => $item['buyer_id'],
                        'buyCode' => $item['goods_id'],
                        'buyNum' => $item['purchase_num'],
                        'buyIP' => $item['buyer_ip'],
                        'period' => $item['goods_period'],
                        'goodsName' => $item['goods_name']
                    );
                    if (empty($maxId)) {
                        $maxId = $item['_id'];
                    }
                }
            }
            if (empty($maxId)) {
                $maxId = $id;
            }
            $ret['maxId'] = $maxId;
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取云购记录
     * 会员-首页
     * 会员-云购记录
     */
    public function getmembercenterbuymessageAction()
    {
        // http://member.1yyg.com/JPData?action=getMemberCenterBuyMessage&FIdx=1&EIdx=5&state=-1&region=4&beginTime=&endTime=&keyWords=&isCount=1&fun=jsonp1451572613311&_=1451572613810
        // jsonp1451572613311({"code":0,"str":{"totalCount":8,"listItems":[{"codeState":"3","codeID":"2542873","goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机","goodsPic":"20150910150825965.jpg","buyNum":"1","refundNum":"0","codePeriod":"21158","userName":"yz*@sina.com","codeSales":"5188","codeQuantity":"5188","codePrice":"5188.00","userWeb":"1011010841","userName":"几十万该中宝马了","codeRTime":"2015-12-20 21:37:19.245","codeType":"0","goodsID":"22504"},{"codeState":"3","codeID":"2509590","goodsName":"苹果（Apple）iPad Pro 12.9 英寸平板电脑 32G WiFi版","goodsPic":"20151023164405174.jpg","buyNum":"2","refundNum":"0","codePeriod":"1270","userName":"13580****90","codeSales":"6088","codeQuantity":"6088","codePrice":"6088.00","userWeb":"1010037107","userName":"抠脚大叔前来领奖","codeRTime":"2015-12-20 22:06:34.205","codeType":"0","goodsID":"22591"},{"codeState":"3","codeID":"2450928","goodsName":"苹果（Apple）iMac MK462CH/A 27英寸一体电脑","goodsPic":"20151026092646574.jpg","buyNum":"1","refundNum":"0","codePeriod":"438","userName":"15662****77","codeSales":"13488","codeQuantity":"13488","codePrice":"13488.00","userWeb":"1008906059","userName":"者得获品商为成您喜恭","codeRTime":"2015-12-20 21:45:20.430","codeType":"0","goodsID":"22592"},{"codeState":"3","codeID":"2542665","goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机","goodsPic":"20150910150825965.jpg","buyNum":"1","refundNum":"0","codePeriod":"21146","userName":"13926****95","codeSales":"5188","codeQuantity":"5188","codePrice":"5188.00","userWeb":"1011741168","userName":"集中力","codeRTime":"2015-12-20 21:19:00.141","codeType":"0","goodsID":"22504"},{"codeState":"3","codeID":"2509588","goodsName":"苹果（Apple）iPad Pro 12.9 英寸平板电脑 32G WiFi版","goodsPic":"20151023164405174.jpg","buyNum":"2","refundNum":"0","codePeriod":"1269","userName":"bo*@sina.com","codeSales":"6088","codeQuantity":"6088","codePrice":"6088.00","userWeb":"1011117589","userName":"这样不中奖毫无意思","codeRTime":"2015-12-20 21:21:44.949","codeType":"0","goodsID":"22591"}]}})
        // http://www.myapplicationmodule.com/order/service/getmembercenterbuymessage?page=1&limit=5&state=0&beginTime=2015-12-15&endTime=2015-12-15&keyWords=xxx
        try {
            // 0全部 1 进行中 3 已揭晓 4已退购
            $state = intval($this->get('state', '0'));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '9'));
            $beginTime = $this->get('beginTime', '');
            $endTime = $this->get('endTime', '');
            
            $ret = array();
            $ret['total'] = 0;
            $ret['datas'] = array();
            
            if (! empty($_SESSION['member_id'])) {
                
                if (! empty($beginTime)) {
                    $beginTime = strtotime($beginTime . " 00:00:00");
                }
                if (! empty($endTime)) {
                    $endTime = strtotime($endTime . " 23:59:59");
                }
                $list = $this->modelOrderGoods->getUserBuyList($_SESSION['member_id'], $page, $limit, $state, $beginTime, $endTime);
                $ret['total'] = $list['total'];
                $datas = array();
                if (! empty($list['datas'])) {
                    $goodsIds = array();
                    foreach ($list['datas'] as $item) {
                        $goodsIds[] = $item['goods_id'];
                    }
                    $goodsList = $this->modelGoods->getListByIds($goodsIds);
                    foreach ($list['datas'] as $item) {
                        if (! isset($goodsList[$item['goods_id']])) {
                            echo ($this->error(- 2, "商品号为{$item['goods_id']}的商品不存在"));
                            return false;
                        }
                        $goodsInfo = $goodsList[$item['goods_id']];
                        // "codeState":"3",
                        // "codeID":"2542873",
                        // "goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机",
                        // "goodsPic":"20150910150825965.jpg",
                        // "buyNum":"1",
                        // "refundNum":"0",
                        // "codePeriod":"21158",
                        // "userName":"yz*@sina.com",
                        // "codeSales":"5188",
                        // "codeQuantity":"5188",
                        // "codePrice":"5188.00",
                        // "userWeb":"1011010841",
                        // "userName":"几十万该中宝马了",
                        // "codeRTime":"2015-12-20 21:37:19.245",
                        // "codeType":"0",
                        // "goodsID":"22504"
                        
                        $datas[] = array(
                            'codeState' => $goodsInfo['sale_state'],
                            'codeID' => $goodsInfo['_id'],
                            'goodsName' => $goodsInfo['name'],
                            'goodsPic' => $this->modelGoods->getImagePath($this->baseUrl, $goodsInfo['image']),
                            'buyNum' => $item['purchase_num'],
                            'refundNum' => $item['refund_num'],
                            'codePeriod' => $goodsInfo['period'],
                            'userName' => getBuyerName($goodsInfo['prize_buyer_name'], $goodsInfo['prize_buyer_register_by']),
                            'codeSales' => intval($goodsInfo['purchase_person_time']),
                            'codeQuantity' => intval($goodsInfo['total_person_time']),
                            'codePrice' => showPrice($goodsInfo['price'], 2),
                            'userWeb' => $goodsInfo['prize_buyer_id'],
                            'userId' => $goodsInfo['prize_buyer_id'],
                            'codeRTime' => getMilliTime4Show($goodsInfo['prize_time']),
                            'codeType' => '0',
                            'goodsID' => $goodsInfo['goods_commonid'],
                            'purchase_time' => getMilliTime4Show($item['purchase_time'])
                        );
                    }
                }
                $ret['datas'] = $datas;
            }
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取获得的商品的接口
     *
     * @return boolean
     */
    public function getmembercenteruserwinlistAction()
    {
        // http://member.1yyg.com/JPData?action=getMemberCenterUserWinList&FIdx=1&EIdx=5&orderState=0&region=4&beginTime=&endTime=&OrderType=-1&isStat=0&isCount=1&fun=jsonp1451609661016&_=1451609661476
        // jsonp1451701004523({"code":0,"totalCount":1,"listItems":[{"codeID":"2489811","goodsPic":"20151215155940353.jpg","goodsName":"川宇（Kawau）Micro SD/T-Flash TF读卡器 C289","codeRNO":"10000007","codeRTime":"2015-12-18 10:39:22.280","orderState":"10","orderNo":"2261112","codePeriod":"74","IsPostSingle":"-1","codePrice":"20.00","codeType":"0","orderActDesc":"","orderAddTime":"2015-12-18 10:39","actAddTime":"20151218","goodsID":"22746","buyNum":"20","orderType":"0","ordersaleprice":"20.00"}]})
        // http://www.myapplicationmodule.com/order/service/getmembercenteruserwinlist?page=1&limit=5&beginTime=2015-12-15&endTime=2015-12-15
        try {
            // 0全部 1待确认地址 2待发货 3待收货 4待晒单
            $orderState = intval($this->get('orderState', '0'));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));
            $beginTime = $this->get('beginTime', '');
            $endTime = $this->get('endTime', '');
            
            $ret = array();
            $ret['total'] = 0;
            $ret['datas'] = array();
            
            if (! empty($_SESSION['member_id'])) {
                
                if (! empty($beginTime)) {
                    $beginTime = strtotime($beginTime . " 00:00:00");
                }
                if (! empty($endTime)) {
                    $endTime = strtotime($endTime . " 23:59:59");
                }
                
                $list = $this->modelOrderGoods->getUserWinList($_SESSION['member_id'], $page, $limit, $orderState, $beginTime, $endTime);
                
                $ret['total'] = $list['total'];
                $datas = array();
                if (! empty($list['datas'])) {
                    foreach ($list['datas'] as $item) {
                        
                        // "codeID":"2489811",
                        // "goodsPic":"20151215155940353.jpg",
                        // "goodsName":"川宇（Kawau）Micro SD/T-Flash TF读卡器 C289",
                        // "codeRNO":"10000007",
                        // "codeRTime":"2015-12-18 10:39:22.280",
                        // "orderState":"10",
                        // "orderNo":"2261112",
                        // "codePeriod":"74",
                        // "IsPostSingle":"-1",
                        // "codePrice":"20.00",
                        // "codeType":"0",
                        // "orderActDesc":"",
                        // "orderAddTime":"2015-12-18 10:39",
                        // "actAddTime":"20151218",
                        // "goodsID":"22746",
                        // "buyNum":"20",
                        // "orderType":"0",
                        // "ordersaleprice":"20.00"
                        $datas[] = array(
                            'orderState' => $item['state'],
                            'codeID' => $item['goods_id'],
                            'goodsPic' => $this->modelGoods->getImagePath($this->baseUrl, $item['goods_image']),
                            'goodsName' => $item['goods_name'],
                            'codeRNO' => $item['prize_code'],
                            'codeRTime' => getMilliTime4Show($item['prize_time']),
                            'orderState' => $item['order_state'],
                            'orderNo' => $item['order_no'],
                            'codePeriod' => $item['goods_period'],
                            'IsPostSingle' => $item['is_post_single'],
                            'codePrice' => showPrice($item['goods_price'], 2),
                            'codeType' => 0,
                            'orderActDesc' => $item['orderActDesc'],
                            'orderAddTime' => date('Y-m-d H:i:s', $item['prize_time']),
                            'actAddTime' => date('Ymd', $item['prize_time']),
                            'goodsID' => $item['goods_commonid'],
                            'buyNum' => $item['purchase_num'],
                            'orderType' => 0,
                            'ordersaleprice' => showPrice($item['goods_price'], 2)
                        );
                    }
                }
                $ret['datas'] = $datas;
            }
            
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 确认收货地址的接口
     */
    public function confirmorderconsigneeAction()
    {
        // http://www.myapplicationmodule.com/order/service/confirmorderconsignee?order_no=xxx&consignee_id=xxx&message=xxx
        try {
            $order_no = ($this->get('order_no', ''));
            if (empty($order_no)) {
                echo ($this->error(- 2, '订单NO为空'));
                return false;
            }
            $consignee_id = ($this->get('consignee_id', ''));
            if (empty($consignee_id)) {
                echo ($this->error(- 3, '收货地址为空'));
                return false;
            }
            $message = urldecode($this->get('message', ''));
            if (empty($message)) {
                echo ($this->error(- 4, '备注信息为空'));
                return false;
            }
            if (empty($_SESSION['member_id'])) {
                echo ($this->error(- 1, '非法访问'));
                return false;
            }
            $orderInfo = $this->modelOrderGoods->getInfoByOrderNo($order_no);
            if (empty($orderInfo)) {
                echo ($this->error(- 6, '订单信息不存在'));
                return false;
            }
            
            if ($orderInfo['buyer_id'] != $_SESSION['member_id']) {
                echo ($this->error(- 7, '订单信息不存在'));
                return false;
            }
            
            if ($orderInfo['order_state'] != \App\Order\Models\Goods::ORDER_STATE1) {
                echo ($this->error(- 5, '订单已完善了收货地址'));
                return false;
            }
            
            $consigneeInfo = $this->modelMemberConsignee->getInfoById($consignee_id);
            if (empty($consigneeInfo)) {
                echo ($this->error(- 8, '收货信息不存在'));
                return false;
            }
            // 确认收货地址
            $this->modelOrderGoods->confirmOrderConsignee($order_no, $message, $consigneeInfo);
            
            // 记录订单日志记录
            $this->modelOrderLog->log($order_no, \App\Order\Models\Goods::ORDER_STATE2, "会员已填写配送地址信息，等待商城发货！", \App\Order\Models\Log::ROLE_BUYER, $_SESSION['member_id'], $_SESSION['member_name']);
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 发货的接口
     */
    public function deliveryorderAction()
    {
        // http://www.myapplicationmodule.com/order/service/deliveryorder?order_no=xxx&express_no=xxx&user_id=xx&delivery_sn=227187853706&user_name=xx
        try {
            $order_no = ($this->get('order_no', ''));
            if (empty($order_no)) {
                echo ($this->error(- 1, '订单NO为空'));
                return false;
            }
            $user_id = ($this->get('user_id', ''));
            $user_name = ($this->get('user_name', ''));
            if (empty($user_id)) {
                echo ($this->error(- 2, '操作者ID为空'));
                return false;
            }
            $delivery_sn = ($this->get('delivery_sn', ''));
            if (empty($delivery_sn)) {
                echo ($this->error(- 3, '快递单号为空'));
                return false;
            }
            $orderInfo = $this->modelOrderGoods->getInfoByOrderNo($order_no);
            if (empty($orderInfo)) {
                echo ($this->error(- 4, '订单信息不存在'));
                return false;
            }
            if ($orderInfo['order_state'] != \App\Order\Models\Goods::ORDER_STATE2) {
                echo ($this->error(- 5, '订单已发货'));
                return false;
            }
            
            $deliveryInfo = array(
                'delivery_sn' => $delivery_sn,
                'name' => '申通速递'
            );
            // 发货
            $this->modelOrderGoods->deliveryOrder($order_no, $deliveryInfo);
            
            // 记录订单日志记录
            $this->modelOrderLog->log($order_no, \App\Order\Models\Goods::ORDER_STATE2, "您的配送信息已确认。将由【{$deliveryInfo['name']}】配送,快递单号【{$deliveryInfo['delivery_sn']}】", \App\Order\Models\Log::ROLE_ADMIN, $user_id, $user_name);
            
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 确认收货的接口
     */
    public function confirmorderreceiveAction()
    {
        // http://www.myapplicationmodule.com/order/service/confirmorderreceive?order_no=xxx
        try {
            $order_no = ($this->get('order_no', ''));
            if (empty($order_no)) {
                echo ($this->error(- 2, '订单NO为空'));
                return false;
            }
            if (empty($_SESSION['member_id'])) {
                echo ($this->error(- 1, '非法访问'));
                return false;
            }
            $orderInfo = $this->modelOrderGoods->getInfoByOrderNo($order_no);
            if (empty($orderInfo)) {
                echo ($this->error(- 6, '订单信息不存在'));
                return false;
            }
            if ($orderInfo['buyer_id'] != $_SESSION['member_id']) {
                echo ($this->error(- 7, '订单信息不存在'));
                return false;
            }
            if ($orderInfo['order_state'] != \App\Order\Models\Goods::ORDER_STATE3) {
                echo ($this->error(- 5, '订单还没有发货'));
                return false;
            }
            
            // 确认收货
            $this->modelOrderGoods->confirmOrderReceive($order_no);
            // 记录订单日志记录
            $this->modelOrderLog->log($order_no, \App\Order\Models\Goods::ORDER_STATE3, "已成功提交确认收货！", \App\Order\Models\Log::ROLE_BUYER, $_SESSION['member_id'], $_SESSION['member_name']);
            // 生成一个晒单记录
            $goodsInfo = $this->modelGoods->getInfoById($orderInfo['goods_id']);
            $postInfo = $this->modelPost->create($_SESSION['member_id'], $goodsInfo);
            if (empty($postInfo)) {
                echo ($this->error(- 3, '晒单数据生成失败'));
                return false;
            }
            // 记录晒单ID
            $this->modelOrderGoods->recordPostId($order_no, $postInfo['_id']);
            
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}


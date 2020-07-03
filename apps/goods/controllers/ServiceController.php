<?php

namespace App\Goods\Controllers;

class ServiceController extends ControllerBase
{

    private $modelPost = null;

    private $modelMember = null;

    private $modelGoods = null;

    private $modelGoodsCommon = null;

    private $modelPrize = null;

    private $modelPrizeCode = null;

    private $modelLotteryRule = null;

    private $modelOrder = null;

    private $modelOrderLog = null;

    private $modelOrderGoods = null;

    private $serviceGoods = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelPost = new \App\Post\Models\Post();
        $this->modelMember = new \App\Member\Models\Member();
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelGoodsCommon = new \App\Goods\Models\GoodsCommon();
        $this->modelPrize = new \App\Prize\Models\Prize();
        $this->modelPrizeCode = new \App\Prize\Models\Code();
        $this->modelLotteryRule = new \App\Lottery\Models\Rule();
        $this->modelOrder = new \App\Order\Models\Order();
        $this->modelOrderLog = new \App\Order\Models\Log();
        $this->modelOrderGoods = new \App\Order\Models\Goods();
        $this->modelGoodsCollect = new \App\Goods\Models\Collect();
        $this->serviceGoods = new \App\Goods\Services\Goods();
    }

    /**
     * 生成新一期的商品的接口
     * 生成新一期的商品和云购码
     *
     * @throws \Exception
     * @throws Exception
     * @return boolean
     */
    public function createnewperiodAction()
    {
        // http://www.applicationmodule.com/goods/service/createnewperiod?goods_commonid=563728bf7f50eab0040003e3
        try {
            $goods_commonid = $this->get('goods_commonid', '');
            if (empty($goods_commonid)) {
                echo ($this->error('-1', 'goods_commonid为空'));
                return false;
            }
            $goodsCommonInfo = $this->modelGoodsCommon->getInfoById($goods_commonid);
            if (empty($goodsCommonInfo)) {
                echo ($this->error('-2', 'goods_commonid不正确'));
                return false;
            }
            // 生成新的一期
            $ret = $this->serviceGoods->createNewPeriodGoods($goods_commonid);
            if (!empty($ret['error_code'])) {
                echo ($this->error('-3', $ret['error_msg']));
                return false;
            }
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 满员商品抽奖
     *
     * @throws \Exception
     * @throws Exception
     * @return boolean
     */
    public function lotteryAction()
    {
        // http://www.applicationmodule.com/goods/service/lottery?goods_id=563728be7f50eab0040003b1
        try {
            $goods_id = $this->get('goods_id', '');
            if (empty($goods_id)) {
                echo ($this->error('-1', 'goods_id为空'));
                return false;
            }
            $goodsInfo = $this->modelGoods->getInfoById($goods_id);
            if (empty($goodsInfo)) {
                echo ($this->error('-2', 'goods_id不正确'));
                return false;
            }
            // 满员商品抽奖
            $ret = $this->serviceGoods->lottery($goods_id);
            if (!empty($ret['error_code'])) {
                echo ($this->error('-3', $ret['error_msg']));
                return false;
            }
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 分页获取即将揭晓的商品列表的接口
     *
     * @return boolean
     */
    public function getgoodslistAction()
    {

        // http://api.1yyg.com/JPData?action=getGoodsList&sortID=0&brandID=0&orderFlag=10&FIdx=1&EIdx=24&isCount=0&fun=jQuery18106648092379327863_1451569831574&_=1451569832167
        // jQuery18106648092379327863_1451569831574({"Code":0,"Count":0,"Data":{"Tables":{"Table1":{"Rows":[{"rowID":0,"goodsID":22797,"goodsSName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车","goodsPic":"20151223154720771.jpg","codeID":2665183,"codePrice":"458888.00","codeQuantity":458888,"codeSales":441973,"codePeriod":28,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":1,"goodsID":22719,"goodsSName":"小米（MIUI）红米Note 3 16GB 4G手机 标准版","goodsPic":"20151202152244904.jpg","codeID":2682716,"codePrice":"1058.00","codeQuantity":1058,"codeSales":991,"codePeriod":1034,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":2,"goodsID":22732,"goodsSName":"金士顿（Kingston）DT SE9H 32GB 金属U盘 银色亮薄","goodsPic":"20151208162616625.jpg","codeID":2536404,"codePrice":"75.00","codeQuantity":75,"codeSales":69,"codePeriod":1164,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":3,"goodsID":22798,"goodsSName":"微软（Microsoft）Surface Pro 4专业版 128G 平板电脑 i5 4G内存","goodsPic":"20151223163837660.jpg","codeID":2626023,"codePrice":"7488.00","codeQuantity":7488,"codeSales":6880,"codePeriod":34,"codeType":3,"goodsTag":"10","codeLimitBuy":"5"},{"rowID":4,"goodsID":22634,"goodsSName":"美的（Midea）MG38CB-AA 38L 家用容量多功能电烤箱","goodsPic":"20151112102523330.jpg","codeID":2656075,"codePrice":"329.00","codeQuantity":329,"codeSales":284,"codePeriod":3022,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":5,"goodsID":22704,"goodsSName":"小米（MIUI）红米Note 3 32GB 4G手机 高配版","goodsPic":"20151130164943898.jpg","codeID":2719858,"codePrice":"1299.00","codeQuantity":1299,"codeSales":1068,"codePeriod":4489,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":6,"goodsID":22620,"goodsSName":"360 儿童卫士智能手表3 通话版 W461B 王子蓝","goodsPic":"20151106171829548.jpg","codeID":2548807,"codePrice":"399.00","codeQuantity":399,"codeSales":315,"codePeriod":2959,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":7,"goodsID":22523,"goodsSName":"苹果（Apple）iPad mini 4 7.9英寸平板电脑 16G WiFi版","goodsPic":"20150918113524362.jpg","codeID":2614310,"codePrice":"2899.00","codeQuantity":2899,"codeSales":2269,"codePeriod":6093,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":8,"goodsID":22806,"goodsSName":"佳能（Canon）EOS原装单反相机包 蓝色","goodsPic":"20151228163121605.jpg","codeID":2702107,"codePrice":"78.00","codeQuantity":78,"codeSales":58,"codePeriod":63,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":9,"goodsID":22612,"goodsSName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机","goodsPic":"20151105111557474.jpg","codeID":2637611,"codePrice":"8488.00","codeQuantity":8488,"codeSales":6234,"codePeriod":2971,"codeType":3,"goodsTag":"10","codeLimitBuy":"5"},{"rowID":10,"goodsID":22670,"goodsSName":"七匹狼 男士纯棉秋冬透气短袜 6双礼盒装 均码","goodsPic":"20151123110546874.jpg","codeID":2512916,"codePrice":"49.00","codeQuantity":49,"codeSales":35,"codePeriod":3321,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":11,"goodsID":21762,"goodsSName":"美的（Midea）WH517E2b 外塑内钢双层防烫 电水壶","goodsPic":"20140507095331971.jpg","codeID":2577809,"codePrice":"119.00","codeQuantity":119,"codeSales":85,"codePeriod":4026,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":12,"goodsID":22370,"goodsSName":"斯伯丁（SPALDING）74-221/74-604Y PU材质 室内外兼用 比赛用篮球","goodsPic":"20150629153939560.jpg","codeID":2626331,"codePrice":"168.00","codeQuantity":168,"codeSales":114,"codePeriod":5051,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":13,"goodsID":22317,"goodsSName":"麦斯威尔（Maxwell House）三合一特浓咖啡 13gx60条 盒装","goodsPic":"20150606143113848.jpg","codeID":2627285,"codePrice":"55.00","codeQuantity":55,"codeSales":34,"codePeriod":2004,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":14,"goodsID":22160,"goodsSName":"统一 来一桶 老坛酸菜牛肉面（辣味）120g*12桶 整箱装","goodsPic":"20150313162131353.jpg","codeID":2663538,"codePrice":"49.00","codeQuantity":49,"codeSales":30,"codePeriod":25020,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":15,"goodsID":22282,"goodsSName":"飞利浦 (Philips) M2BTBK/00 Fidelio旗舰系列 NFC蓝牙无线头戴式耳机","goodsPic":"20150521153407811.jpg","codeID":2584294,"codePrice":"1899.00","codeQuantity":1899,"codeSales":1143,"codePeriod":228,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":16,"goodsID":22262,"goodsSName":"锐澳（RIO）鸡尾酒 预调酒 KT新春幻彩装 275ml x 6瓶","goodsPic":"20150513142117466.jpg","codeID":2680607,"codePrice":"95.00","codeQuantity":95,"codeSales":57,"codePeriod":15857,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":17,"goodsID":22245,"goodsSName":"威凯（Viki）婴儿推车 超轻便可折叠经典夏季伞车 彩色网布坐兜 S1180","goodsPic":"20150429145520103.jpg","codeID":2559225,"codePrice":"299.00","codeQuantity":299,"codeSales":179,"codePeriod":2740,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":18,"goodsID":21473,"goodsSName":"曼秀雷敦（Mentholatum）薄荷润唇膏3.5g+润唇啫喱8g","goodsPic":"20131219181640458.jpg","codeID":2686681,"codePrice":"34.00","codeQuantity":34,"codeSales":20,"codePeriod":850,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":19,"goodsID":22709,"goodsSName":"乐视 超级电视 第3代X55（X3-55）55英寸 4K智能网络液晶平板电视（标配挂架）","goodsPic":"20151203145651154.jpg","codeID":2633779,"codePrice":"4899.00","codeQuantity":4899,"codeSales":2856,"codePeriod":310,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":20,"goodsID":22468,"goodsSName":"飞利浦（Philips）S5079/04 全身水洗电动剃须刀","goodsPic":"20150827170344769.jpg","codeID":2702221,"codePrice":"799.00","codeQuantity":799,"codeSales":465,"codePeriod":1106,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":21,"goodsID":22613,"goodsSName":"苹果（Apple）iPad mini 4 7.9英寸平板电脑 64G WiFi版","goodsPic":"20151105112154971.jpg","codeID":2474533,"codePrice":"3688.00","codeQuantity":3688,"codeSales":2143,"codePeriod":771,"codeType":3,"goodsTag":"10","codeLimitBuy":"5"},{"rowID":22,"goodsID":21958,"goodsSName":"苏泊尔（Supor）30cm精铸富铁炒锅 FC30E","goodsPic":"20151214100728129.jpg","codeID":2501882,"codePrice":"109.00","codeQuantity":109,"codeSales":60,"codePeriod":4726,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"},{"rowID":23,"goodsID":22658,"goodsSName":"闪迪（SanDisk）至尊高速 MicroSDXC UHS-I 存储卡 64GB-Class10-48Mb/s","goodsPic":"20151117180200543.jpg","codeID":2679301,"codePrice":"109.00","codeQuantity":109,"codeSales":60,"codePeriod":611,"codeType":0,"goodsTag":"0","codeLimitBuy":"0"}]}}}})
        // http://www.applicationmodule.com/goods/service/getgoodslist?sortID=0&brandID=0&orderFlag=10&limit=24&page=1
        try {
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '24'));

            // 即将揭晓
            $list = $this->modelGoods->getAnnouncedSoonList($page, $limit);

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $goodsInfo) {
                    // "rowID":0,
                    // "goodsID":22797,
                    // "goodsSName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车",
                    // "goodsPic":"20151223154720771.jpg",
                    // "codeID":2665183,
                    // "codePrice":"458888.00",
                    // "codeQuantity":458888,
                    // "codeSales":441973,
                    // "codePeriod":28,
                    // "codeType":0,
                    // "goodsTag":"0",
                    // "codeLimitBuy":"0"
                    $data = array();
                    $data['goodsID'] = $goodsInfo['goods_commonid'];
                    $data['goodsSName'] = $goodsInfo['name'];
                    $data['goodsPic'] = $this->modelGoods->getImagePath($this->baseUrl, $goodsInfo['image']);
                    $data['codeID'] = $goodsInfo['_id'];
                    $data['codePrice'] = showPrice($goodsInfo['price'], 2);
                    $data['codeQuantity'] = $goodsInfo['total_person_time'];
                    $data['codeSales'] = $goodsInfo['purchase_person_time'];
                    $data['codePeriod'] = $goodsInfo['period'];
                    $data['codeType'] = 0;
                    $data['codeLimitBuy'] = $goodsInfo['restrict_person_time'];
                    $datas[] = $data;
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
     * 获取热门推荐或新品上架的商品列表的接口
     *
     * @return boolean
     */
    public function getrecgoodslistAction()
    {
        // http://api.1yyg.com/JPData?action=getRecGoodsList&goodsLabel=12&quantity=8&fun=jQuery18106648092379327863_1451569831578&_=1451569832166
        // jQuery18106648092379327863_1451569831578({'code':0,'listItems':[{'goodsID':22504,'goodsName':'苹果（Apple）iPhone 6s 16G版 4G手机','goodsPic':'20150910150825965.jpg','goodsTag':'0','goodsRecDesc':'','codeID':2724506,'codePrice':'5188.00','codeQuantity':5188,'codeSales':16,'goodsNameEx':'苹果（Apple）iPhone 6s 16G版 4','codePeriod':26840},{'goodsID':21976,'goodsName':'苹果（Apple）iPad Air 2 9.7英寸平板电脑 16G WiFi版','goodsPic':'20141024155736176.jpg','goodsTag':'0','goodsRecDesc':'性能极强的一款 iPad','codeID':2674724,'codePrice':'3488.00','codeQuantity':3488,'codeSales':67,'goodsNameEx':'苹果（Apple）iPad Air 2 9.7英寸','codePeriod':10102},{'goodsID':21915,'goodsName':'苹果（Apple）iPhone 6 A1586 16G版 4G手机','goodsPic':'20140910144439506.jpg','goodsTag':'0','goodsRecDesc':'iPhone6 比更大还更大','codeID':2725008,'codePrice':'4488.00','codeQuantity':4488,'codeSales':259,'goodsNameEx':'苹果（Apple）iPhone 6 A1586 16','codePeriod':58822},{'goodsID':22592,'goodsName':'苹果（Apple）iMac MK462CH/A 27英寸一体电脑','goodsPic':'20151026092646574.jpg','goodsTag':'0','goodsRecDesc':'','codeID':2451371,'codePrice':'13488.00','codeQuantity':13488,'codeSales':710,'goodsNameEx':'苹果（Apple）iMac MK462CH/A 27','codePeriod':503},{'goodsID':21948,'goodsName':'佳能（Canon）EOS 6D 单反套机（EF 24-105mm f/4L IS USM 镜头）','goodsPic':'20140930185320485.jpg','goodsTag':'0','goodsRecDesc':'专业级数码单反相机','codeID':2758173,'codePrice':'13699.00','codeQuantity':13699,'codeSales':3237,'goodsNameEx':'佳能（Canon）EOS 6D 单反套机（','codePeriod':1003},{'goodsID':22629,'goodsName':'小米（MIUI）小米电视 2S 48英寸 网络液晶平板电视机单品','goodsPic':'20151110145954350.jpg','goodsTag':'0','goodsRecDesc':'','codeID':2564935,'codePrice':'3688.00','codeQuantity':3688,'codeSales':417,'goodsNameEx':'小米（MIUI）小米电视 2S 48英寸','codePeriod':328},{'goodsID':22591,'goodsName':'苹果（Apple）iPad Pro 12.9 英寸平板电脑 32G WiFi版','goodsPic':'20151023164405174.jpg','goodsTag':'0','goodsRecDesc':'','codeID':2685617,'codePrice':'5999.00','codeQuantity':5999,'codeSales':95,'goodsNameEx':'苹果（Apple）iPad Pro 12.9 英寸','codePeriod':1508},{'goodsID':22634,'goodsName':'美的（Midea）MG38CB-AA 38L 家用容量多功能电烤箱','goodsPic':'20151112102523330.jpg','goodsTag':'0','goodsRecDesc':'','codeID':2656076,'codePrice':'329.00','codeQuantity':329,'codeSales':0,'goodsNameEx':'美的（Midea）MG38CB-AA 38L 家用','codePeriod':3023}]})
        // http://www.applicationmodule.com/goods/service/getrecgoodslist?goodsLabel=12&quantity=8
        try {
            $goodsLabel = intval($this->get('goodsLabel', '12'));
            $limit = intval($this->get('quantity', '8'));

            if ($goodsLabel == 12) {
                // 热门推荐
                $list = $this->modelGoods->getHotList(1, $limit);
            } elseif ($goodsLabel == 13) {
                // 新品上架
                $list = $this->modelGoods->getNewList(1, $limit);
            }

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $goodsInfo) {
                    // 'goodsID':22504,
                    // 'goodsName':'苹果（Apple）iPhone 6s 16G版 4G手机',
                    // 'goodsPic':'20150910150825965.jpg',
                    // 'goodsTag':'0',
                    // 'goodsRecDesc':'',
                    // 'codeID':2724506,
                    // 'codePrice':'5188.00',
                    // 'codeQuantity':5188,
                    // 'codeSales':16,
                    // 'goodsNameEx':'苹果（Apple）iPhone 6s 16G版 4',
                    // 'codePeriod':26840
                    $data = array();
                    $data['goodsID'] = $goodsInfo['goods_commonid'];
                    $data['goodsName'] = $goodsInfo['name'];
                    $data['goodsPic'] = $this->modelGoods->getImagePath($this->baseUrl, $goodsInfo['image']);
                    $data['goodsTag'] = 0;
                    $data['goodsRecDesc'] = "";
                    $data['codeID'] = $goodsInfo['_id'];
                    $data['codePrice'] = showPrice($goodsInfo['price'], 2);
                    $data['codeQuantity'] = $goodsInfo['total_person_time'];
                    $data['codeSales'] = $goodsInfo['purchase_person_time'];
                    $data['codePeriod'] = $goodsInfo['period'];
                    $data['goodsNameEx'] = $goodsInfo['name'];
                    $datas[] = $data;
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
     * 获取最新的商品信息的接口
     *
     * @return boolean
     */
    public function getbarcodernoinfoAction()
    {
        // http://api.1yyg.com/JPData?action=GetBarcodernoInfo&codeID=2688851&fun=jQuery18103058221349492669_1451305421621&_=1451305807877
        // jQuery18103058221349492669_1451305421621({"code":1})
        // jQuery18103058221349492669_1451305421624({"code":0,"codePeriod":3147,"codeRNO":10000019,"codeRTime":"2015-12-28 20:28:28.463","buyTime":"2015-12-28 20:28:18.400","price":"69.00","buyCount":30,"userName":"再不中删删删删删掉","userNC":"再不中删删删删删掉","ipAddr":"广东省汕头市","goodsName":"爱利嗒 柴鸡蛋 40枚装","goodsPic":"20150113113355323.jpg","userPhoto":"00000000000000000.jpg","userWeb":"1010817316","codeType":0})
        // http://www.applicationmodule.com/goods/service/getbarcodernoinfo?codeID=2688851
        try {
            $goods_id = ($this->get('codeID', ''));
            if (empty($goods_id)) {
                echo ($this->error(-1, '商品ID为空'));
                return true;
            }
            $goodsInfo = $this->modelGoods->getInfoById($goods_id);
            // if (empty($goodsInfo)) {
            // echo ($this->error(- 2, '商品ID不存在'));
            // return true;
            // }
            $ret = array();
            if (!empty($goodsInfo)) {
                // "codePeriod":3147,
                // "codeRNO":10000019,
                // "codeRTime":"2015-12-28 20:28:28.463",
                // "buyTime":"2015-12-28 20:28:18.400",
                // "price":"69.00",
                // "buyCount":30,
                // "userName":"再不中删删删删删掉",
                // "userNC":"再不中删删删删删掉",
                // "ipAddr":"广东省汕头市",
                // "goodsName":"爱利嗒 柴鸡蛋 40枚装",
                // "goodsPic":"20150113113355323.jpg",
                // "userPhoto":"00000000000000000.jpg",
                // "userWeb":"1010817316",
                // "codeType":0
                $ret['codePeriod'] = $goodsInfo['period'];
                $ret['codeRNO'] = $goodsInfo['prize_code'];
                $ret['codeRTime'] = getMilliTime4Show($goodsInfo['prize_time']);
                $ret['buyTime'] = getMilliTime4Show($goodsInfo['prize_buyer_purchase_time']);
                $ret['price'] = showPrice($goodsInfo['price'], 2);
                $ret['buyCount'] = $goodsInfo['purchase_person_time'];
                $ret['userName'] = getBuyerName($goodsInfo['prize_buyer_name'], $goodsInfo['prize_buyer_register_by']);
                $ret['userNC'] = getBuyerName($goodsInfo['prize_buyer_name'], $goodsInfo['prize_buyer_register_by']);
                $ret['ipAddr'] = convertIp($goodsInfo['prize_buyer_ip']);
                $ret['goodsName'] = $goodsInfo['name'];
                $ret['goodsPic'] = $this->modelGoods->getImagePath($this->baseUrl, $goodsInfo['image']);
                $ret['userPhoto'] = $this->modelMember->getImagePath($this->baseUrl, $goodsInfo['prize_buyer_avatar']);
                $ret['userWeb'] = $goodsInfo['prize_buyer_id'];
                $ret['codeType'] = 0;
            }
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取最新揭晓的商品的接口
     *
     * @throws \Exception
     * @throws Exception
     * @return boolean
     */
    public function getstartrafflealllistAction()
    {
        // http://api.1yyg.com/JPData?action=GetStartRaffleAllList&time=8299096279.239&fun=jQuery18103058221349492669_1451305421621&_=1451305808648
        // jQuery18103058221349492669_1451305421621({"errorCode":0,"maxSeconds":8299096288.331,"listItems":[{"goodsPic":"20150727183000536.jpg","goodsSName":"统一 来一桶 红烧牛肉面 12桶 整箱装","seconds":"178","codeID":2670323,"price":"49.00","period":16307,"codeQuantity":49,"codeSales":49,"codeType":0}]})
        // http://www.applicationmodule.com/goods/service/getstartrafflealllist?time=xxxx&limit=5&page=1
        try {
            $time = $this->get('time', null);
            if (empty($time)) {
                $time = getMilliTime();
            }
            // die('time:' . $time);
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));

            $otherConditions = array();
            $otherConditions['prize_time'] = array(
                '$gt' => $time
            );
            $maxSeconds = $time;
            $list = $this->modelGoods->getStartRaffleList($page, $limit, $otherConditions);
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $goodsInfo) {
                    // "goodsPic":"20150727183000536.jpg",
                    // "goodsSName":"统一 来一桶 红烧牛肉面 12桶 整箱装",
                    // "seconds":"178",
                    // "codeID":2670323,
                    // "price":"49.00",
                    // "period":16307,
                    // "codeQuantity":49,
                    // "codeSales":49,
                    // "codeType":0
                    $data = array();
                    $data['goodsPic'] = $this->modelGoods->getImagePath($this->baseUrl, $goodsInfo['image']);
                    $data['goodsSName'] = $goodsInfo['name'];
                    // die('xxx' . $goodsInfo['prize_time'] . 'time:' . $time);
                    $data['seconds'] = round($goodsInfo['prize_time'] - $time, 0);
                    $data['codeID'] = $goodsInfo['_id'];
                    $data['price'] = showPrice($goodsInfo['price'], 2);
                    $data['period'] = $goodsInfo['period'];
                    $data['codeQuantity'] = $goodsInfo['total_person_time'];
                    $data['codeSales'] = $goodsInfo['purchase_person_time'];
                    $data['codeType'] = 0;
                    $datas[] = $data;
                    $maxSeconds = max($maxSeconds, $goodsInfo['prize_time']);
                }
            }
            $ret['datas'] = $datas;
            $ret['maxSeconds'] = $maxSeconds + 0.001;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取揭晓商品的总数的接口
     *
     * @throws \Exception
     * @throws Exception
     * @return boolean
     */
    public function getrafflecountAction()
    {
        // http://www.applicationmodule.com/goods/service/getrafflecount
        try {
            $num = $this->modelGoods->getRaffleCount();
            echo ($this->result("OK", $num));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取计算详情的接口
     *
     * @return boolean
     */
    public function getlotteryrecordsAction()
    {
        // http://api.1yyg.com/JPData?action=getLotteryRecords&codeId=2575690&fun=jsonp1451400368049&_=1451400368853
        // jsonp1451400368049({'code':0,'recordEnd1':[{"buyTime":"2015-12-29 22:13:19.408","buyName":"13430****90","userWeb":"1011061184","buyID":322168123,"buyCode":2601807,"buyNum":4,"goodsPeriod":2870,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:19.320","buyName":"什么都没中过","userWeb":"1010515589","buyID":322168122,"buyCode":2703039,"buyNum":3,"goodsPeriod":25704,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2015-12-29 22:13:19.065","buyName":"13480****31","userWeb":"1011176689","buyID":322168121,"buyCode":2611643,"buyNum":15,"goodsPeriod":5970,"goodsName":"苹果（Apple）iPad mini 4 7.9英寸平板电脑 16G WiFi版"},{"buyTime":"2015-12-29 22:13:19.031","buyName":"艹_没中过云购","userWeb":"1008126341","buyID":322168120,"buyCode":2683227,"buyNum":1,"goodsPeriod":12101,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:18.901","buyName":"骗子云狗明天","userWeb":"1010840250","buyID":322168119,"buyCode":2664768,"buyNum":10,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"}],'recordEnd2':[{"buyTime":"2015-12-29 22:13:18.849","timeCodeVal":"221318849","buyName":"13774****76","userWeb":"1011530880","buyID":322168118,"buyCode":2575690,"buyNum":4,"goodsPeriod":18642,"goodsName":"加多宝 凉茶（310ml x 24罐）/箱 x 2箱"},{"buyTime":"2015-12-29 22:13:18.757","timeCodeVal":"221318757","buyName":"13413****79","userWeb":"1010839223","buyID":322168117,"buyCode":2683227,"buyNum":1,"goodsPeriod":12101,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:18.745","timeCodeVal":"221318745","buyName":"13413****79","userWeb":"1010839223","buyID":322168116,"buyCode":2665120,"buyNum":2,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:18.727","timeCodeVal":"221318727","buyName":"13413****79","userWeb":"1010839223","buyID":322168115,"buyCode":2664768,"buyNum":2,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:18.720","timeCodeVal":"221318720","buyName":"限够王","userWeb":"1011788201","buyID":322168114,"buyCode":2655024,"buyNum":10,"goodsPeriod":20260,"goodsName":"平安银行 招财进宝金章 Au9999 5g"},{"buyTime":"2015-12-29 22:13:18.714","timeCodeVal":"221318714","buyName":"云购真的好夺","userWeb":"1010405519","buyID":322168113,"buyCode":2683227,"buyNum":3,"goodsPeriod":12101,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:18.707","timeCodeVal":"221318707","buyName":"13413****79","userWeb":"1010839223","buyID":322168112,"buyCode":2663657,"buyNum":1,"goodsPeriod":589,"goodsName":"苹果（Apple）iPad Pro 12.9 英寸平板电脑 32G WiFi版"},{"buyTime":"2015-12-29 22:13:18.641","timeCodeVal":"221318641","buyName":"给老婆中一辆","userWeb":"1010303074","buyID":322168111,"buyCode":2557958,"buyNum":15,"goodsPeriod":332,"goodsName":"十月流年 铂朗系列 PT950铂金男士骑士项链 约38g"},{"buyTime":"2015-12-29 22:13:18.584","timeCodeVal":"221318584","buyName":"麦当劳甜筒好","userWeb":"1011479291","buyID":322168110,"buyCode":2664768,"buyNum":11,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:18.570","timeCodeVal":"221318570","buyName":"13964****55","userWeb":"1011479797","buyID":322168109,"buyCode":2665120,"buyNum":1,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:18.512","timeCodeVal":"221318512","buyName":"13959****85","userWeb":"1011555847","buyID":322168108,"buyCode":2705232,"buyNum":3,"goodsPeriod":138,"goodsName":"美的（Midea）NDK22-16FW 防水欧式快热炉取暖器/电暖器"},{"buyTime":"2015-12-29 22:13:18.503","timeCodeVal":"221318503","buyName":"可遇不可求-","userWeb":"1011217438","buyID":322168107,"buyCode":2647907,"buyNum":8,"goodsPeriod":1756,"goodsName":"平安银行 平安金标准金条 Au9999 50g"},{"buyTime":"2015-12-29 22:13:18.464","timeCodeVal":"221318464","buyName":"29*@qq.com","userWeb":"1011162148","buyID":322168106,"buyCode":2601807,"buyNum":5,"goodsPeriod":2870,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:18.455","timeCodeVal":"221318455","buyName":"不再信云购-","userWeb":"1010749370","buyID":322168105,"buyCode":2533944,"buyNum":4,"goodsPeriod":342,"goodsName":"vivo X6 移动联通双4G手机 4GB+32GB"},{"buyTime":"2015-12-29 22:13:18.425","timeCodeVal":"221318425","buyName":"买几千了破产","userWeb":"1011127726","buyID":322168104,"buyCode":2533391,"buyNum":28,"goodsPeriod":522,"goodsName":"秘鲁桑娜（PERU SANA）玛卡片 100粒/瓶 原装进口"},{"buyTime":"2015-12-29 22:13:18.402","timeCodeVal":"221318402","buyName":"丫的都是广东","userWeb":"1011705338","buyID":322168103,"buyCode":2664768,"buyNum":1,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:18.400","timeCodeVal":"221318400","buyName":"一心一意得奥","userWeb":"1011596752","buyID":322168102,"buyCode":2611643,"buyNum":2,"goodsPeriod":5970,"goodsName":"苹果（Apple）iPad mini 4 7.9英寸平板电脑 16G WiFi版"},{"buyTime":"2015-12-29 22:13:18.378","timeCodeVal":"221318378","buyName":"13510****54","userWeb":"1011632363","buyID":322168101,"buyCode":2664768,"buyNum":1,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:18.355","timeCodeVal":"221318355","buyName":"奥巴马8888","userWeb":"1008162726","buyID":322168100,"buyCode":2664768,"buyNum":18,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:18.284","timeCodeVal":"221318284","buyName":"13411****06","userWeb":"1011921503","buyID":322168099,"buyCode":2704796,"buyNum":1,"goodsPeriod":45902,"goodsName":"伊利 安慕希常温酸牛奶 205mlx12盒 礼盒装"},{"buyTime":"2015-12-29 22:13:18.271","timeCodeVal":"221318271","buyName":"13788****70","userWeb":"1010445043","buyID":322168098,"buyCode":2601807,"buyNum":1,"goodsPeriod":2870,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:18.204","timeCodeVal":"221318204","buyName":"来吧我都要","userWeb":"1011882107","buyID":322168097,"buyCode":2688329,"buyNum":10,"goodsPeriod":1697,"goodsName":"中国农业银行传世之宝 \"金元宝\" Au9999 200g"},{"buyTime":"2015-12-29 22:13:18.193","timeCodeVal":"221318193","buyName":"我姓赖","userWeb":"1010178857","buyID":322168096,"buyCode":2601493,"buyNum":1,"goodsPeriod":1462,"goodsName":"苹果（Apple）iPhone 6s Plus 16G版 4G手机"},{"buyTime":"2015-12-29 22:13:18.096","timeCodeVal":"221318096","buyName":"带我走上征途","userWeb":"1009136470","buyID":322168095,"buyCode":2543040,"buyNum":9,"goodsPeriod":5230,"goodsName":"苹果（Apple）iPhone 6s Plus 16G版 4G手机"},{"buyTime":"2015-12-29 22:13:18.064","timeCodeVal":"221318064","buyName":"可遇不可求啊","userWeb":"1010160001","buyID":322168094,"buyCode":2582739,"buyNum":30,"goodsPeriod":36049,"goodsName":"小米（MIUI）16000mAh 移动电源"},{"buyTime":"2015-12-29 22:13:17.953","timeCodeVal":"221317953","buyName":"运气要很好宝","userWeb":"1008938627","buyID":322168093,"buyCode":2611643,"buyNum":25,"goodsPeriod":5970,"goodsName":"苹果（Apple）iPad mini 4 7.9英寸平板电脑 16G WiFi版"},{"buyTime":"2015-12-29 22:13:17.899","timeCodeVal":"221317899","buyName":"灵宝人爱云购","userWeb":"1010453808","buyID":322168092,"buyCode":2575690,"buyNum":2,"goodsPeriod":18642,"goodsName":"加多宝 凉茶（310ml x 24罐）/箱 x 2箱"},{"buyTime":"2015-12-29 22:13:17.884","timeCodeVal":"221317884","buyName":"福州玛莎莉酒","userWeb":"1011198345","buyID":322168091,"buyCode":2647907,"buyNum":19,"goodsPeriod":1756,"goodsName":"平安银行 平安金标准金条 Au9999 50g"},{"buyTime":"2015-12-29 22:13:17.882","timeCodeVal":"221317882","buyName":"奥迪奥迪我是","userWeb":"1009325352","buyID":322168090,"buyCode":2663739,"buyNum":5,"goodsPeriod":1468,"goodsName":"苹果（Apple）iPad Pro 12.9 英寸平板电脑 32G WiFi版"},{"buyTime":"2015-12-29 22:13:17.851","timeCodeVal":"221317851","buyName":"奥迪奥迪我是","userWeb":"1009325352","buyID":322168089,"buyCode":2647907,"buyNum":3,"goodsPeriod":1756,"goodsName":"平安银行 平安金标准金条 Au9999 50g"},{"buyTime":"2015-12-29 22:13:17.725","timeCodeVal":"221317725","buyName":"云购很好玩a-","userWeb":"1009864333","buyID":322168088,"buyCode":2582739,"buyNum":1,"goodsPeriod":36049,"goodsName":"小米（MIUI）16000mAh 移动电源"},{"buyTime":"2015-12-29 22:13:17.714","timeCodeVal":"221317714","buyName":"心灰意冷102","userWeb":"1010555498","buyID":322168087,"buyCode":2665120,"buyNum":1,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:17.646","timeCodeVal":"221317646","buyName":"13570****67","userWeb":"1011209990","buyID":322168086,"buyCode":2663268,"buyNum":1,"goodsPeriod":52835,"goodsName":"金龙鱼 黄金比例 食用调和油 5L"},{"buyTime":"2015-12-29 22:13:17.643","timeCodeVal":"221317643","buyName":"13380****95","userWeb":"1010668450","buyID":322168085,"buyCode":2543040,"buyNum":1,"goodsPeriod":5230,"goodsName":"苹果（Apple）iPhone 6s Plus 16G版 4G手机"},{"buyTime":"2015-12-29 22:13:17.615","timeCodeVal":"221317615","buyName":"中国出彩人好","userWeb":"1010993332","buyID":322168084,"buyCode":2647907,"buyNum":2,"goodsPeriod":1756,"goodsName":"平安银行 平安金标准金条 Au9999 50g"},{"buyTime":"2015-12-29 22:13:17.605","timeCodeVal":"221317605","buyName":"一定要中一定","userWeb":"1011422830","buyID":322168083,"buyCode":2664768,"buyNum":3,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:17.587","timeCodeVal":"221317587","buyName":"中国出彩人好","userWeb":"1010993332","buyID":322168082,"buyCode":2545061,"buyNum":2,"goodsPeriod":953,"goodsName":"Mama&Bebe 汽车儿童安全座椅 9个月-4周岁暴风豪华舒适型 荷兰品牌"},{"buyTime":"2015-12-29 22:13:17.534","timeCodeVal":"221317534","buyName":"食父云购甜骗","userWeb":"1010938544","buyID":322168081,"buyCode":2504722,"buyNum":1,"goodsPeriod":1879,"goodsName":"苏泊尔（Supor）SZ26B5 双层加厚复底不锈钢蒸锅"},{"buyTime":"2015-12-29 22:13:17.407","timeCodeVal":"221317407","buyName":"半月半月传传","userWeb":"1009822936","buyID":322168080,"buyCode":2683227,"buyNum":1,"goodsPeriod":12101,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:17.402","timeCodeVal":"221317402","buyName":"王老先生","userWeb":"1011817460","buyID":322168079,"buyCode":2655024,"buyNum":150,"goodsPeriod":20260,"goodsName":"平安银行 招财进宝金章 Au9999 5g"},{"buyTime":"2015-12-29 22:13:17.385","timeCodeVal":"221317385","buyName":"半月半月传传","userWeb":"1009822936","buyID":322168078,"buyCode":2651033,"buyNum":2,"goodsPeriod":18857,"goodsName":"苹果（Apple）iPhone 6s Plus 64G版 4G手机"},{"buyTime":"2015-12-29 22:13:17.371","timeCodeVal":"221317371","buyName":"喜中云购宝马","userWeb":"1009895596","buyID":322168077,"buyCode":2665120,"buyNum":5,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:17.367","timeCodeVal":"221317367","buyName":"半月半月传传","userWeb":"1009822936","buyID":322168076,"buyCode":2595917,"buyNum":1,"goodsPeriod":1251,"goodsName":"华为（HUAWEI）荣耀7 64GB 4G手机 全网通 荣耀金"},{"buyTime":"2015-12-29 22:13:17.348","timeCodeVal":"221317348","buyName":"半月半月传传","userWeb":"1009822936","buyID":322168075,"buyCode":2546689,"buyNum":2,"goodsPeriod":11495,"goodsName":"苹果（Apple）iPhone 6s 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:17.337","timeCodeVal":"221317337","buyName":"喜中云购宝马","userWeb":"1009895596","buyID":322168074,"buyCode":2664768,"buyNum":4,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:17.330","timeCodeVal":"221317330","buyName":"半月半月传传","userWeb":"1009822936","buyID":322168073,"buyCode":2462551,"buyNum":1,"goodsPeriod":1816,"goodsName":"华为（HUAWEI）Ascend Mate7 标配版 4G手机 移动版"},{"buyTime":"2015-12-29 22:13:17.314","timeCodeVal":"221317314","buyName":"半月半月传传","userWeb":"1009822936","buyID":322168072,"buyCode":2212988,"buyNum":2,"goodsPeriod":4347,"goodsName":"苹果（Apple）iPhone 6 Plus A1524 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:17.253","timeCodeVal":"221317253","buyName":"15815****41","userWeb":"1011134851","buyID":322168071,"buyCode":2626012,"buyNum":3,"goodsPeriod":26,"goodsName":"微软（Microsoft）Surface Pro 4专业版 128G 平板电脑 i5 4G内存"},{"buyTime":"2015-12-29 22:13:17.234","timeCodeVal":"221317234","buyName":"15818****80","userWeb":"1011723006","buyID":322168070,"buyCode":2546689,"buyNum":1,"goodsPeriod":11495,"goodsName":"苹果（Apple）iPhone 6s 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:17.221","timeCodeVal":"221317221","buyName":"给不给中云购","userWeb":"1011631619","buyID":322168069,"buyCode":2715938,"buyNum":3,"goodsPeriod":59,"goodsName":"丹尼尔惠灵顿（Daniel Wellington）DW时尚男士手表 0103DW"},{"buyTime":"2015-12-29 22:13:17.186","timeCodeVal":"221317186","buyName":"云狗没有中过","userWeb":"1011998819","buyID":322168068,"buyCode":2651033,"buyNum":1,"goodsPeriod":18857,"goodsName":"苹果（Apple）iPhone 6s Plus 64G版 4G手机"},{"buyTime":"2015-12-29 22:13:17.159","timeCodeVal":"221317159","buyName":"云狗没有中过","userWeb":"1011998819","buyID":322168067,"buyCode":2647706,"buyNum":5,"goodsPeriod":15,"goodsName":"苹果（Apple）iMac MF886CH/A 27英寸一体电脑"},{"buyTime":"2015-12-29 22:13:17.151","timeCodeVal":"221317151","buyName":"13531****99","userWeb":"1010717928","buyID":322168066,"buyCode":2665120,"buyNum":6,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:17.035","timeCodeVal":"221317035","buyName":"我的我尔我","userWeb":"1007585508","buyID":322168065,"buyCode":2647907,"buyNum":11,"goodsPeriod":1756,"goodsName":"平安银行 平安金标准金条 Au9999 50g"},{"buyTime":"2015-12-29 22:13:16.990","timeCodeVal":"221316990","buyName":"耶稣耶稣保佑","userWeb":"1008125996","buyID":322168063,"buyCode":2647907,"buyNum":5,"goodsPeriod":1756,"goodsName":"平安银行 平安金标准金条 Au9999 50g"},{"buyTime":"2015-12-29 22:13:16.990","timeCodeVal":"221316990","buyName":"15915****36","userWeb":"1009260237","buyID":322168064,"buyCode":2669774,"buyNum":1,"goodsPeriod":20386,"goodsName":"苹果（Apple）iPhone 6s 64G版 4G手机"},{"buyTime":"2015-12-29 22:13:16.963","timeCodeVal":"221316963","buyName":"投最后一次了","userWeb":"1011101556","buyID":322168062,"buyCode":2665120,"buyNum":56,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:16.940","timeCodeVal":"221316940","buyName":"耶稣耶稣保佑","userWeb":"1008125996","buyID":322168061,"buyCode":2545061,"buyNum":3,"goodsPeriod":953,"goodsName":"Mama&Bebe 汽车儿童安全座椅 9个月-4周岁暴风豪华舒适型 荷兰品牌"},{"buyTime":"2015-12-29 22:13:16.939","timeCodeVal":"221316939","buyName":"最后一块了试","userWeb":"1011545916","buyID":322168060,"buyCode":2601493,"buyNum":1,"goodsPeriod":1462,"goodsName":"苹果（Apple）iPhone 6s Plus 16G版 4G手机"},{"buyTime":"2015-12-29 22:13:16.883","timeCodeVal":"221316883","buyName":"18898****77","userWeb":"1011675781","buyID":322168059,"buyCode":2686582,"buyNum":1,"goodsPeriod":751,"goodsName":"曼秀雷敦（Mentholatum）薄荷润唇膏3.5g+润唇啫喱8g"},{"buyTime":"2015-12-29 22:13:16.839","timeCodeVal":"221316839","buyName":"13710****75","userWeb":"1011783570","buyID":322168058,"buyCode":2665120,"buyNum":2,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:16.804","timeCodeVal":"221316804","buyName":"13878****99","userWeb":"1008458182","buyID":322168057,"buyCode":2665120,"buyNum":4,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:16.779","timeCodeVal":"221316779","buyName":"干林老木云狗","userWeb":"1009600934","buyID":322168056,"buyCode":2613176,"buyNum":5,"goodsPeriod":971,"goodsName":"屈臣氏（Watsons）苏打汽水（香草味）330mlx24罐 整箱装"},{"buyTime":"2015-12-29 22:13:16.728","timeCodeVal":"221316728","buyName":"13413****61","userWeb":"1008925272","buyID":322168055,"buyCode":2294112,"buyNum":2,"goodsPeriod":2474,"goodsName":"苹果（Apple）iPad Air 2 9.7英寸平板电脑 16G WiFi版"},{"buyTime":"2015-12-29 22:13:16.724","timeCodeVal":"221316724","buyName":"快过年了给一","userWeb":"1011909222","buyID":322168054,"buyCode":2716102,"buyNum":1,"goodsPeriod":992,"goodsName":"佳能（Canon）EOS 6D 单反套机（EF 24-105mm f/4L IS USM 镜头）"},{"buyTime":"2015-12-29 22:13:16.663","timeCodeVal":"221316663","buyName":"我要艹云购别","userWeb":"1011566649","buyID":322168053,"buyCode":2664768,"buyNum":2,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:16.629","timeCodeVal":"221316629","buyName":"13711****18","userWeb":"1010297963","buyID":322168052,"buyCode":2664768,"buyNum":13,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:16.589","timeCodeVal":"221316589","buyName":"13662****28","userWeb":"1011725750","buyID":322168051,"buyCode":2664768,"buyNum":9,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:16.564","timeCodeVal":"221316564","buyName":"好人定会有好","userWeb":"1011134304","buyID":322168050,"buyCode":2688329,"buyNum":3,"goodsPeriod":1697,"goodsName":"中国农业银行传世之宝 \"金元宝\" Au9999 200g"},{"buyTime":"2015-12-29 22:13:16.539","timeCodeVal":"221316539","buyName":"为了家庭请删","userWeb":"1008181157","buyID":322168049,"buyCode":2664768,"buyNum":1,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:16.523","timeCodeVal":"221316523","buyName":"一觉睡醒中了","userWeb":"1011404750","buyID":322168048,"buyCode":2665120,"buyNum":1,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:16.511","timeCodeVal":"221316511","buyName":"好人定会有好","userWeb":"1011134304","buyID":322168047,"buyCode":2665792,"buyNum":3,"goodsPeriod":434,"goodsName":"平安银行 平安梯形金条 Au9999 200g"},{"buyTime":"2015-12-29 22:13:16.472","timeCodeVal":"221316472","buyName":"一觉睡醒中了","userWeb":"1011404750","buyID":322168046,"buyCode":2664768,"buyNum":1,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:16.468","timeCodeVal":"221316468","buyName":"好人定会有好","userWeb":"1011134304","buyID":322168045,"buyCode":2665120,"buyNum":2,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:16.451","timeCodeVal":"221316451","buyName":"1购做梦想来辆","userWeb":"1010802184","buyID":322168044,"buyCode":2651033,"buyNum":4,"goodsPeriod":18857,"goodsName":"苹果（Apple）iPhone 6s Plus 64G版 4G手机"},{"buyTime":"2015-12-29 22:13:16.449","timeCodeVal":"221316449","buyName":"13790****83","userWeb":"1010657115","buyID":322168043,"buyCode":2706087,"buyNum":2,"goodsPeriod":958,"goodsName":"荟智（Huizhi）儿童电动车 HW888-G 四轮可坐玩具汽车"},{"buyTime":"2015-12-29 22:13:16.421","timeCodeVal":"221316421","buyName":"好人定会有好","userWeb":"1011134304","buyID":322168042,"buyCode":2664768,"buyNum":2,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:16.323","timeCodeVal":"221316323","buyName":"该中一次了吧","userWeb":"1010947715","buyID":322168041,"buyCode":2608253,"buyNum":1,"goodsPeriod":570,"goodsName":"苹果（Apple）Apple Watch 38毫米不锈钢表壳搭配米兰尼斯表带 MJ322CH/A"},{"buyTime":"2015-12-29 22:13:16.293","timeCodeVal":"221316293","buyName":"宝马奥迪沃尔","userWeb":"1011433514","buyID":322168040,"buyCode":2647907,"buyNum":1,"goodsPeriod":1756,"goodsName":"平安银行 平安金标准金条 Au9999 50g"},{"buyTime":"2015-12-29 22:13:16.288","timeCodeVal":"221316288","buyName":"易宝云阁","userWeb":"1011868627","buyID":322168039,"buyCode":2664768,"buyNum":2,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:16.243","timeCodeVal":"221316243","buyName":"车车车啊啊啊","userWeb":"1007572042","buyID":322168038,"buyCode":2461827,"buyNum":170,"goodsPeriod":127,"goodsName":"苹果（Apple）MacBook Air MJVG2CH/A 13.3英寸笔记本电脑"},{"buyTime":"2015-12-29 22:13:16.238","timeCodeVal":"221316238","buyName":"15711****60","userWeb":"1011720347","buyID":322168037,"buyCode":2601807,"buyNum":5,"goodsPeriod":2870,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:16.237","timeCodeVal":"221316237","buyName":"再不中真要去","userWeb":"1011748768","buyID":322168036,"buyCode":2611643,"buyNum":4,"goodsPeriod":5970,"goodsName":"苹果（Apple）iPad mini 4 7.9英寸平板电脑 16G WiFi版"},{"buyTime":"2015-12-29 22:13:16.188","timeCodeVal":"221316188","buyName":"艹云购给个元","userWeb":"1008273793","buyID":322168035,"buyCode":2611643,"buyNum":2,"goodsPeriod":5970,"goodsName":"苹果（Apple）iPad mini 4 7.9英寸平板电脑 16G WiFi版"},{"buyTime":"2015-12-29 22:13:16.166","timeCodeVal":"221316166","buyName":"土豪为我填坑","userWeb":"1011968630","buyID":322168034,"buyCode":2425054,"buyNum":17,"goodsPeriod":1851,"goodsName":"SKG MY-610 榨汁机"},{"buyTime":"2015-12-29 22:13:16.120","timeCodeVal":"221316120","buyName":"山炮啥样你啥","userWeb":"1011361302","buyID":322168033,"buyCode":2665120,"buyNum":10,"goodsPeriod":14,"goodsName":"沃尔沃（Volvo）XC60 2016款 2.0T T5 智远版轿车"},{"buyTime":"2015-12-29 22:13:16.071","timeCodeVal":"221316071","buyName":"云购我中宝马","userWeb":"1011523717","buyID":322168032,"buyCode":2544995,"buyNum":5,"goodsPeriod":607,"goodsName":"福玛特（FMART）E-R620C 智能家用扫地机器人吸尘器"},{"buyTime":"2015-12-29 22:13:15.985","timeCodeVal":"221315985","buyName":"云购绝对骗子","userWeb":"1011621364","buyID":322168031,"buyCode":2601807,"buyNum":1,"goodsPeriod":2870,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:15.831","timeCodeVal":"221315831","buyName":"兴兴来来中宝","userWeb":"1010573431","buyID":322168030,"buyCode":2664768,"buyNum":4,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:15.768","timeCodeVal":"221315768","buyName":"云购大骗子不","userWeb":"1011353982","buyID":322168029,"buyCode":2575690,"buyNum":1,"goodsPeriod":18642,"goodsName":"加多宝 凉茶（310ml x 24罐）/箱 x 2箱"},{"buyTime":"2015-12-29 22:13:15.745","timeCodeVal":"221315745","buyName":"短信响起来大","userWeb":"1009068170","buyID":322168028,"buyCode":2575690,"buyNum":3,"goodsPeriod":18642,"goodsName":"加多宝 凉茶（310ml x 24罐）/箱 x 2箱"},{"buyTime":"2015-12-29 22:13:15.731","timeCodeVal":"221315731","buyName":"云购大骗子不","userWeb":"1011353982","buyID":322168027,"buyCode":2472571,"buyNum":1,"goodsPeriod":1439,"goodsName":"小米（MIUI）蓝牙耳机 黑色"},{"buyTime":"2015-12-29 22:13:15.670","timeCodeVal":"221315670","buyName":"飞机中云购","userWeb":"1008479451","buyID":322168026,"buyCode":2647754,"buyNum":5,"goodsPeriod":953,"goodsName":"苹果（Apple）MacBook Pro MF840CH/A 13.3英寸宽屏笔记本电脑"},{"buyTime":"2015-12-29 22:13:15.644","timeCodeVal":"221315644","buyName":"飞机中云购","userWeb":"1008479451","buyID":322168025,"buyCode":2638836,"buyNum":5,"goodsPeriod":455,"goodsName":"苹果（Apple）MacBook 12英寸笔记本电脑 256GB 闪存"},{"buyTime":"2015-12-29 22:13:15.621","timeCodeVal":"221315621","buyName":"飞机中云购","userWeb":"1008479451","buyID":322168024,"buyCode":2601807,"buyNum":5,"goodsPeriod":2870,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:15.597","timeCodeVal":"221315597","buyName":"飞机中云购","userWeb":"1008479451","buyID":322168023,"buyCode":2601493,"buyNum":5,"goodsPeriod":1462,"goodsName":"苹果（Apple）iPhone 6s Plus 16G版 4G手机"},{"buyTime":"2015-12-29 22:13:15.569","timeCodeVal":"221315569","buyName":"飞机中云购","userWeb":"1008479451","buyID":322168022,"buyCode":2549921,"buyNum":5,"goodsPeriod":4992,"goodsName":"苹果（Apple）iPhone 6s 16G版 4G手机"},{"buyTime":"2015-12-29 22:13:15.459","timeCodeVal":"221315459","buyName":"18607****73","userWeb":"1009133817","buyID":322168021,"buyCode":2596087,"buyNum":3,"goodsPeriod":939,"goodsName":"飞利浦（Philips）HTB3550/93 5.1声道音响卡拉OK 蓝光家庭影院音箱"},{"buyTime":"2015-12-29 22:13:15.442","timeCodeVal":"221315442","buyName":"想要车回家过","userWeb":"1010724131","buyID":322168020,"buyCode":2664768,"buyNum":2,"goodsPeriod":22,"goodsName":"奥迪（Audi）A6L 2014款 TFSI 标准型轿车"},{"buyTime":"2015-12-29 22:13:15.433","timeCodeVal":"221315433","buyName":"15106****85","userWeb":"1011094519","buyID":322168019,"buyCode":2642784,"buyNum":1,"goodsPeriod":27,"goodsName":"佳能（Canon）EOS 70D 单反套机（EF-S 18-135mm f/3.5-5.6 IS STM镜头）"}],'recordEnd3':[{"buyTime":"2015-12-29 22:13:15.415","buyName":"云购肯定有内","userWeb":"1008832507","buyID":322168018,"buyCode":2647907,"buyNum":3,"goodsPeriod":1756,"goodsName":"平安银行 平安金标准金条 Au9999 50g"},{"buyTime":"2015-12-29 22:13:15.395","buyName":"伟爷一次都没","userWeb":"1009210035","buyID":322168017,"buyCode":2683227,"buyNum":5,"goodsPeriod":12101,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:15.372","buyName":"云购云购坑我","userWeb":"1011312816","buyID":322168016,"buyCode":2601807,"buyNum":2,"goodsPeriod":2870,"goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"},{"buyTime":"2015-12-29 22:13:15.331","buyName":"13765****67","userWeb":"1011825792","buyID":322168015,"buyCode":2472571,"buyNum":1,"goodsPeriod":1439,"goodsName":"小米（MIUI）蓝牙耳机 黑色"},{"buyTime":"2015-12-29 22:13:15.262","buyName":"18962****61","userWeb":"1011795478","buyID":322168014,"buyCode":2689573,"buyNum":15,"goodsPeriod":1886,"goodsName":"金龙鱼 玉米油 4L 非转基因压榨食用油"}]})
        // http://www.applicationmodule.com/goods/service/getlotteryrecords?codeId=2688851
        try {
            $goods_id = ($this->get('codeId', ''));
            if (empty($goods_id)) {
                echo ($this->error(-1, '商品ID为空'));
                return true;
            }
            $goodsInfo = $this->modelGoods->getInfoById($goods_id);
            if (empty($goodsInfo)) {
                echo ($this->error(-2, '商品ID不存在'));
                return true;
            }
            $last_purchase_time = empty($goodsInfo['last_purchase_time']) ? 0 : $goodsInfo['last_purchase_time'];
            $ret = array();
            $orderGoodsList = $goodsInfo['order_goods_list'];
            $recordEnd2 = array();
            if (!empty($orderGoodsList)) {
                foreach ($orderGoodsList as $orderGoodsInfo) {
                    // "timeCodeVal":"221318849"
                    // "buyTime":"2015-12-29 22:13:19.408",
                    // "buyName":"13430****90",
                    // "userWeb":"1011061184",
                    // "buyID":322168123,
                    // "buyCode":2601807,
                    // "buyNum":4,
                    // "goodsPeriod":2870,
                    // "goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"
                    $data = array();
                    list($sec, $msec) = explode(".", $orderGoodsInfo['purchase_time']);
                    $mill = date('His', $sec) . $msec;
                    $data['timeCodeVal'] = $mill;
                    $data['buyTime'] = getMilliTime4Show($orderGoodsInfo['purchase_time']);
                    $data['userName'] = getBuyerName($orderGoodsInfo['buyer_name'], $orderGoodsInfo['buyer_register_by']);
                    $data['userWeb'] = $orderGoodsInfo['buyer_id'];
                    $data['buyID'] = $orderGoodsInfo['_id'];
                    $data['buyCode'] = $orderGoodsInfo['goods_id'];
                    $data['buyNum'] = $orderGoodsInfo['purchase_num'];
                    $data['goodsPeriod'] = $orderGoodsInfo['goods_period'];
                    $data['goodsName'] = $orderGoodsInfo['goods_name'];
                    $recordEnd2[] = $data;
                    // $last_purchase_time = $orderGoodsInfo['purchase_time'];
                }
            }
            $ret['recordEnd2'] = $recordEnd2;
            // 获取最前一条之前的5条记录
            $recordEnd3 = array();
            if (!empty($last_purchase_time)) {
                $orderGoodsList = $this->modelOrderGoods->getLastPurchaseList($last_purchase_time, 5);
            }
            if (!empty($orderGoodsList)) {
                foreach ($orderGoodsList as $orderGoodsInfo) {
                    // "timeCodeVal":"221318849"
                    // "buyTime":"2015-12-29 22:13:19.408",
                    // "buyName":"13430****90",
                    // "userWeb":"1011061184",
                    // "buyID":322168123,
                    // "buyCode":2601807,
                    // "buyNum":4,
                    // "goodsPeriod":2870,
                    // "goodsName":"苹果（Apple）iPhone 6s Plus 128G版 4G手机"
                    $data = array();
                    $data['buyTime'] = getMilliTime4Show($orderGoodsInfo['purchase_time']);
                    $data['userName'] = getBuyerName($orderGoodsInfo['buyer_name'], $orderGoodsInfo['buyer_register_by']);
                    $data['userWeb'] = $orderGoodsInfo['buyer_id'];
                    $data['buyID'] = $orderGoodsInfo['_id'];
                    $data['buyCode'] = $orderGoodsInfo['goods_id'];
                    $data['buyNum'] = $orderGoodsInfo['purchase_num'];
                    $data['goodsPeriod'] = $orderGoodsInfo['goods_period'];
                    $data['goodsName'] = $orderGoodsInfo['goods_name'];
                    $recordEnd3[] = $data;
                }
            }
            $ret['recordEnd3'] = $recordEnd3;
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取已经揭晓的商品的接口
     *
     * @throws \Exception
     * @throws Exception
     * @return boolean
     */
    public function getlotterylistAction()
    {
        // http://api.1yyg.com/JPData?action=getLotteryList&FIdx=1&EIdx=72&SortID=0&IsCount=1&fun=jsonp1451640189454&_=1451640189908
        // jsonp1451640189454({'code':0,'totalCount':2418769,'listItems':[{'codeID':2781107,'goodsPic':'20151223134549532.jpg','goodsName':"魅族（MEIZU）PRO5 32GB 联通定制版 4G手机",'codePeriod':80,'codePrice':'2588.00','raffTime':'3分钟前','userWeb':'1008590853','userName':'云购想对你说相见恨晚','userPhoto':'20150723025836157.jpg','userAddr':'广东省潮州市','userRNO':'10002029','userBuyNum':'88','postID':'0','codeType':0},{'codeID':2717165,'goodsPic':'20140813160215287.jpg','goodsName':"蒙牛 特仑苏 纯牛奶 250mlx12盒 礼盒装",'codePeriod':54406,'codePrice':'65.00','raffTime':'3分钟前','userWeb':'1008156505','userName':'再书住无爱买阿','userPhoto':'20150910084405530.jpg','userAddr':'广东省肇庆市','userRNO':'10000028','userBuyNum':'11','postID':'0','codeType':0},{'codeID':2664838,'goodsPic':'20150908180118824.jpg','goodsName':"小米（MIUI）10000mAh 移动电源",'codePeriod':18446,'codePrice':'89.00','raffTime':'3分钟前','userWeb':'1008249661','userName':'2016我要中手机','userPhoto':'20150930062131712.jpg','userAddr':'广东省东莞市','userRNO':'10000039','userBuyNum':'40','postID':'0','codeType':0},{'codeID':2737919,'goodsPic':'20150708112557251.jpg','goodsName':"金龙鱼 优质东北大米 5kg",'codePeriod':106703,'codePrice':'40.00','raffTime':'3分钟前','userWeb':'1011059554','userName':'18931****21','userPhoto':'00000000000000000.jpg','userAddr':'河北省','userRNO':'10000025','userBuyNum':'3','postID':'0','codeType':0},{'codeID':2756202,'goodsPic':'20150703172947616.jpg','goodsName':"蓝月亮 亮白增艳洗衣液（自然清香）3kg/瓶",'codePeriod':7184,'codePrice':'62.00','raffTime':'3分钟前','userWeb':'1010386541','userName':'13502****89','userPhoto':'00000000000000000.jpg','userAddr':'上海市','userRNO':'10000060','userBuyNum':'1','postID':'0','codeType':0},{'codeID':2731786,'goodsPic':'20151117145015560.jpg','goodsName':"维达（Vinda）卫生纸 蓝色经典3层140g卷纸*27卷（整箱销售）",'codePeriod':12705,'codePrice':'50.00','raffTime':'3分钟前','userWeb':'1008134197','userName':'牛逼大王干死你','userPhoto':'20160101084118678.jpg','userAddr':'中国','userRNO':'10000044','userBuyNum':'2','postID':'0','codeType':0},{'codeID':2711900,'goodsPic':'20151126163415411.jpg','goodsName':"张君雅小妹妹 甜甜圈（巧克力味）45g/包x3包 中国台湾进口",'codePeriod':1973,'codePrice':'27.00','raffTime':'3分钟前','userWeb':'1011916767','userName':'云购能让我中次不我靠','userPhoto':'20151211124823282.jpg','userAddr':'日本','userRNO':'10000010','userBuyNum':'10','postID':'0','codeType':0},{'codeID':2761206,'goodsPic':'20150605165935473.jpg','goodsName':"伊利 安慕希常温酸牛奶 205mlx12盒 礼盒装",'codePeriod':47226,'codePrice':'59.00','raffTime':'3分钟前','userWeb':'1010030483','userName':'限购一次都没中过','userPhoto':'20151130223446600.jpg','userAddr':'广东省广州市','userRNO':'10000041','userBuyNum':'20','postID':'0','codeType':0},{'codeID':2691792,'goodsPic':'20141024155736176.jpg','goodsName':"苹果（Apple）iPad Air 2 9.7英寸平板电脑 16G WiFi版",'codePeriod':10231,'codePrice':'3488.00','raffTime':'3分钟前','userWeb':'1012780464','userName':'2016终结者','userPhoto':'20160101144329338.jpg','userAddr':'河北省保定市','userRNO':'10002893','userBuyNum':'1650','postID':'0','codeType':0},{'codeID':2659351,'goodsPic':'20150615112540839.jpg','goodsName':"陶华碧老干妈 风味豆豉油制辣椒 280g/瓶x3瓶",'codePeriod':12498,'codePrice':'29.00','raffTime':'3分钟前','userWeb':'1006890225','userName':'能让我中一次吗醉了','userPhoto':'20151231084106356.jpg','userAddr':'广东省梅州市','userRNO':'10000003','userBuyNum':'2','postID':'0','codeType':0},{'codeID':2753617,'goodsPic':'20150910150825965.jpg','goodsName':"苹果（Apple）iPhone 6s 16G版 4G手机",'codePeriod':27267,'codePrice':'5188.00','raffTime':'3分钟前','userWeb':'1010167788','userName':'必胜合资裙140990','userPhoto':'20151231215909790.jpg','userAddr':'山东省潍坊市','userRNO':'10002684','userBuyNum':'2000','postID':'0','codeType':0},{'codeID':2707546,'goodsPic':'20150113113355323.jpg','goodsName':"爱利嗒 柴鸡蛋 40枚装",'codePeriod':3490,'codePrice':'69.00','raffTime':'4分钟前','userWeb':'1011273801','userName':'中中中大奖奖奖','userPhoto':'00000000000000000.jpg','userAddr':'上海市卢湾区','userRNO':'10000031','userBuyNum':'15','postID':'0','codeType':0},{'codeID':2750530,'goodsPic':'20151019112525255.jpg','goodsName':"小米（MIUI）红米2A 增强版 移动4G手机",'codePeriod':12903,'codePrice':'589.00','raffTime':'4分钟前','userWeb':'1008236364','userName':'2927','userPhoto':'00000000000000000.jpg','userAddr':'辽宁省葫芦岛市','userRNO':'10000298','userBuyNum':'150','postID':'0','codeType':0},{'codeID':2691338,'goodsPic':'20150529154529591.jpg','goodsName':"维达（Vinda）手帕纸 超韧系列4层手帕纸 18包/条x2条",'codePeriod':3451,'codePrice':'38.00','raffTime':'4分钟前','userWeb':'1001713185','userName':'太悲催了吧','userPhoto':'20140109160637918.jpg','userAddr':'甘肃省兰州市','userRNO':'10000034','userBuyNum':'10','postID':'0','codeType':0},{'codeID':2719044,'goodsPic':'20151026181548493.jpg','goodsName':"平安银行 招财进宝金章 Au9999 5g",'codePeriod':21372,'codePrice':'1388.00','raffTime':'4分钟前','userWeb':'1011096291','userName':'云购沃尔沃送老婆','userPhoto':'00000000000000000.jpg','userAddr':'广西','userRNO':'10001223','userBuyNum':'3','postID':'0','codeType':0},{'codeID':2697031,'goodsPic':'20150706114446137.jpg','goodsName':"好丽友 巧克力派 20枚680g",'codePeriod':6879,'codePrice':'29.00','raffTime':'4分钟前','userWeb':'1009794726','userName':'传说中的男人','userPhoto':'20151006214810102.jpg','userAddr':'上海市','userRNO':'10000015','userBuyNum':'13','postID':'0','codeType':0},{'codeID':2647506,'goodsPic':'20151223144758509.jpg','goodsName':"新加坡惠氏 Wyeth s-26金装幼儿配方奶粉4段 900g/罐x2（3岁以上）",'codePeriod':52,'codePrice':'288.00','raffTime':'4分钟前','userWeb':'1009445703','userName':'八万中个苹果6s都难','userPhoto':'20151219213720125.jpg','userAddr':'广东省潮州市','userRNO':'10000264','userBuyNum':'110','postID':'0','codeType':0},{'codeID':2748376,'goodsPic':'20140311155420151.jpg','goodsName':"飞科（FLYCO）FS357 旋转式三刀头全身水洗电动剃须刀",'codePeriod':16187,'codePrice':'139.00','raffTime':'4分钟前','userWeb':'1008613679','userName':'云购让我中五次吧','userPhoto':'20150715194657679.jpg','userAddr':'广西柳州市柳北区','userRNO':'10000074','userBuyNum':'1','postID':'0','codeType':0},{'codeID':2619481,'goodsPic':'20140918153038578.jpg','goodsName':"杜蕾斯（Durex）LOVE装安全套 10片装",'codePeriod':46133,'codePrice':'25.00','raffTime':'4分钟前','userWeb':'1011827533','userName':'85*@qq.com','userPhoto':'00000000000000000.jpg','userAddr':'广东省揭阳市','userRNO':'10000004','userBuyNum':'1','postID':'0','codeType':0},{'codeID':2645009,'goodsPic':'20151222173617454.jpg','goodsName':"德国莎露斯 Salus Floradix Iron 铁元红版果蔬补铁补血口服液 500ml/瓶x2",'codePeriod':76,'codePrice':'269.00','raffTime':'4分钟前','userWeb':'1010850958','userName':'不中这样会破产的','userPhoto':'20151108173324666.jpg','userAddr':'中国','userRNO':'10000033','userBuyNum':'93','postID':'0','codeType':0},{'codeID':2763724,'goodsPic':'20151214171921789.jpg','goodsName':"福临门 优质 东北大米 4kgx2包",'codePeriod':7646,'codePrice':'55.00','raffTime':'4分钟前','userWeb':'1010979858','userName':'屌丝逆袭中奥迪','userPhoto':'20151225152039341.jpg','userAddr':'广东省深圳市','userRNO':'10000029','userBuyNum':'15','postID':'0','codeType':0},{'codeID':2542060,'goodsPic':'20150708175657511.jpg','goodsName':"统一 海之言 柠檬口味饮料 500mlx15瓶 整箱装",'codePeriod':8198,'codePrice':'65.00','raffTime':'4分钟前','userWeb':'1011906872','userName':'涡阳人也来玩云购666','userPhoto':'20151223131253473.jpg','userAddr':'湖北省宜昌市','userRNO':'10000018','userBuyNum':'21','postID':'0','codeType':0},{'codeID':2648871,'goodsPic':'20150606171603360.jpg','goodsName':"爱士堡（Eichbaum）小麦啤酒 500mlx24听 德国进口",'codePeriod':4848,'codePrice':'178.00','raffTime':'4分钟前','userWeb':'1008529557','userName':'我要中小小马','userPhoto':'20150711172744962.jpg','userAddr':'广东省肇庆市','userRNO':'10000095','userBuyNum':'20','postID':'0','codeType':0},{'codeID':2707052,'goodsPic':'20151120154029227.jpg','goodsName':"荟智（Huizhi）儿童电动车 HW888-G 四轮可坐玩具汽车",'codePeriod':1103,'codePrice':'819.00','raffTime':'4分钟前','userWeb':'1011256971','userName':'15920****39','userPhoto':'00000000000000000.jpg','userAddr':'广东省深圳市','userRNO':'10000285','userBuyNum':'10','postID':'0','codeType':0},{'codeID':2741487,'goodsPic':'20140919110724510.jpg','goodsName':"金龙鱼 黄金比例 食用调和油 5L",'codePeriod':53967,'codePrice':'65.00','raffTime':'4分钟前','userWeb':'1011569457','userName':'中辆幸运宝马320i汽车','userPhoto':'20151229235257786.jpg','userAddr':'广东省广州市','userRNO':'10000062','userBuyNum':'30','postID':'0','codeType':0},{'codeID':2737918,'goodsPic':'20150708112557251.jpg','goodsName':"金龙鱼 优质东北大米 5kg",'codePeriod':106702,'codePrice':'40.00','raffTime':'4分钟前','userWeb':'1008588353','userName':'习大大要苹果6S','userPhoto':'20151224025755582.jpg','userAddr':'广东省','userRNO':'10000016','userBuyNum':'8','postID':'0','codeType':0},{'codeID':2613268,'goodsPic':'20150910150902360.jpg','goodsName':"苹果（Apple）iPhone 6s Plus 16G版 4G手机",'codePeriod':5419,'codePrice':'6088.00','raffTime':'5分钟前','userWeb':'1008430253','userName':'短信不来云购拜拜','userPhoto':'20150830214148869.jpg','userAddr':'福建省泉州市安溪县','userRNO':'10003979','userBuyNum':'179','postID':'0','codeType':0},{'codeID':2491797,'goodsPic':'20150731180339729.jpg','goodsName':"华为（HUAWEI）小天鹅蓝牙免提音箱 AM08（白色）",'codePeriod':6820,'codePrice':'139.00','raffTime':'5分钟前','userWeb':'1011098222','userName':'13055****96','userPhoto':'00000000000000000.jpg','userAddr':'福建省福州市','userRNO':'10000083','userBuyNum':'5','postID':'0','codeType':0},{'codeID':2577711,'goodsPic':'20150605155130831.jpg','goodsName':"利葡（Lipo）面包干 300gx2袋 越南进口",'codePeriod':11228,'codePrice':'32.00','raffTime':'5分钟前','userWeb':'1008070872','userName':'筱明晨曦80112050','userPhoto':'20151011021052537.jpg','userAddr':'广东省佛山市南海区','userRNO':'10000013','userBuyNum':'2','postID':'0','codeType':0},{'codeID':2568759,'goodsPic':'20150910154327547.jpg','goodsName':"苹果（Apple）iPhone 6s 16G版 4G手机",'codePeriod':5092,'codePrice':'5188.00','raffTime':'5分钟前','userWeb':'1011733879','userName':'让我中一把可以不','userPhoto':'20151217160103918.jpg','userAddr':'湖北省黄冈市麻城市','userRNO':'10000338','userBuyNum':'5','postID':'0','codeType':3},{'codeID':2680825,'goodsPic':'20150513142117466.jpg','goodsName':"锐澳（RIO）鸡尾酒 预调酒 KT新春幻彩装 275ml x 6瓶",'codePeriod':15904,'codePrice':'95.00','raffTime':'5分钟前','userWeb':'1008126833','userName':'新年快乐快乐','userPhoto':'20150718202939679.jpg','userAddr':'广东省肇庆市','userRNO':'10000073','userBuyNum':'1','postID':'0','codeType':0},{'codeID':2693900,'goodsPic':'20150727183000536.jpg','goodsName':"统一 来一桶 红烧牛肉面 12桶 整箱装",'codePeriod':16949,'codePrice':'49.00','raffTime':'5分钟前','userWeb':'1011441322','userName':'杀阡刀','userPhoto':'20151218153653279.jpg','userAddr':'福建省泉州市','userRNO':'10000021','userBuyNum':'25','postID':'0','codeType':0},{'codeID':2697030,'goodsPic':'20150706114446137.jpg','goodsName':"好丽友 巧克力派 20枚680g",'codePeriod':6878,'codePrice':'29.00','raffTime':'5分钟前','userWeb':'1011402728','userName':'中车去接老婆回家','userPhoto':'20151222171150632.jpg','userAddr':'安徽省铜陵市','userRNO':'10000023','userBuyNum':'4','postID':'0','codeType':0},{'codeID':2402045,'goodsPic':'20150423163327670.jpg','goodsName':"悠骑（JQ）悠美款 迷你锂电折叠电动车自行车 中国红",'codePeriod':979,'codePrice':'2680.00','raffTime':'5分钟前','userWeb':'1011747831','userName':'再不中车到坂田去','userPhoto':'20151201143332344.jpg','userAddr':'广东省深圳市','userRNO':'10002436','userBuyNum':'500','postID':'0','codeType':0},{'codeID':2700643,'goodsPic':'20151021091802148.jpg','goodsName':"康夫（Kangfu）KF-T86 充电式电动理发器 成人儿童电推子剃头刀",'codePeriod':777,'codePrice':'139.00','raffTime':'5分钟前','userWeb':'1011296836','userName':'云购我死给你看','userPhoto':'20151219014717597.jpg','userAddr':'广东省广州市','userRNO':'10000023','userBuyNum':'15','postID':'0','codeType':0},{'codeID':2691196,'goodsPic':'20150709160852552.jpg','goodsName':"维他 柠檬茶 250mlx16盒 整箱装",'codePeriod':32909,'codePrice':'46.00','raffTime':'5分钟前','userWeb':'1012116790','userName':'中中中一次就好啊','userPhoto':'00000000000000000.jpg','userAddr':'四川省成都市','userRNO':'10000038','userBuyNum':'3','postID':'0','codeType':0},{'codeID':2583274,'goodsPic':'20141120165235285.jpg','goodsName':"小米（MIUI）16000mAh 移动电源",'codePeriod':36584,'codePrice':'129.00','raffTime':'5分钟前','userWeb':'1012165024','userName':'222-中中中中中中中中','userPhoto':'20151231020508544.jpg','userAddr':'广东省深圳市','userRNO':'10000089','userBuyNum':'25','postID':'0','codeType':0},{'codeID':2739830,'goodsPic':'20141210160450495.jpg','goodsName':"金龙鱼 玉米油 4L 非转基因压榨食用油",'codePeriod':2810,'codePrice':'50.00','raffTime':'6分钟前','userWeb':'1008868279','userName':'云购老总点名此号中','userPhoto':'20151229191806410.jpg','userAddr':'广东省潮州市','userRNO':'10000014','userBuyNum':'15','postID':'0','codeType':0},{'codeID':2716618,'goodsPic':'20150709173327779.jpg','goodsName':"康师傅 冰红茶 550mlx15瓶 整箱装",'codePeriod':16788,'codePrice':'42.00','raffTime':'6分钟前','userWeb':'1012971395','userName':'悲伤零食王','userPhoto':'20160101171307568.jpg','userAddr':'湖南省','userRNO':'10000006','userBuyNum':'19','postID':'0','codeType':0},{'codeID':2730700,'goodsPic':'20150108184857458.jpg','goodsName':"友臣 肉松饼 2500g 整箱装",'codePeriod':25119,'codePrice':'82.00','raffTime':'6分钟前','userWeb':'1008195633','userName':'云购中奖是什么味道','userPhoto':'20151109001107737.jpg','userAddr':'广东省广州市','userRNO':'10000031','userBuyNum':'5','postID':'0','codeType':0},{'codeID':2753611,'goodsPic':'20150910150825965.jpg','goodsName':"苹果（Apple）iPhone 6s 16G版 4G手机",'codePeriod':27266,'codePrice':'5188.00','raffTime':'6分钟前','userWeb':'1009343358','userName':'秒款公司','userPhoto':'20160101101010582.jpg','userAddr':'广东省江门市','userRNO':'10001412','userBuyNum':'2500','postID':'0','codeType':0},{'codeID':2703182,'goodsPic':'20150930180313416.jpg','goodsName':"苹果（Apple）iPhone 6s Plus 128G版 4G手机",'codePeriod':12676,'codePrice':'8488.00','raffTime':'6分钟前','userWeb':'1008935678','userName':'此号已20天没中过奖','userPhoto':'00000000000000000.jpg','userAddr':'四川省','userRNO':'10002882','userBuyNum':'1438','postID':'0','codeType':0},{'codeID':2717164,'goodsPic':'20140813160215287.jpg','goodsName':"蒙牛 特仑苏 纯牛奶 250mlx12盒 礼盒装",'codePeriod':54405,'codePrice':'65.00','raffTime':'6分钟前','userWeb':'1012446134','userName':'祝新年中','userPhoto':'00000000000000000.jpg','userAddr':'广东省深圳市','userRNO':'10000025','userBuyNum':'2','postID':'0','codeType':0},{'codeID':2711259,'goodsPic':'20150911093936846.jpg','goodsName':"苹果（Apple）iPhone 6s 64G版 4G手机",'codePeriod':21094,'codePrice':'6088.00','raffTime':'6分钟前','userWeb':'1010471055','userName':'快快中奖88','userPhoto':'20151224153425854.jpg','userAddr':'广东省','userRNO':'10000728','userBuyNum':'3500','postID':'0','codeType':0},{'codeID':2761205,'goodsPic':'20150605165935473.jpg','goodsName':"伊利 安慕希常温酸牛奶 205mlx12盒 礼盒装",'codePeriod':47225,'codePrice':'59.00','raffTime':'6分钟前','userWeb':'1009407138','userName':'我心中想要个车','userPhoto':'20151220105917423.jpg','userAddr':'北京市','userRNO':'10000010','userBuyNum':'15','postID':'0','codeType':0},{'codeID':2543678,'goodsPic':'20140507172830535.jpg','goodsName':"美的（Midea） W12PCS505E 电压力锅 5L 多功能电脑版",'codePeriod':4465,'codePrice':'319.00','raffTime':'6分钟前','userWeb':'1011336659','userName':'798艺术区演唱会','userPhoto':'20160101102946567.jpg','userAddr':'北京市丰台区','userRNO':'10000024','userBuyNum':'10','postID':'0','codeType':0},{'codeID':2588968,'goodsPic':'20151130172551170.jpg','goodsName':"曼秀雷敦（Mentholatum）天然植物润唇膏 4g",'codePeriod':2840,'codePrice':'35.00','raffTime':'6分钟前','userWeb':'1011178379','userName':'扑领母云狗狗','userPhoto':'20151223052727298.jpg','userAddr':'广东省深圳市','userRNO':'10000012','userBuyNum':'22','postID':'0','codeType':0},{'codeID':2619480,'goodsPic':'20140918153038578.jpg','goodsName':"杜蕾斯（Durex）LOVE装安全套 10片装",'codePeriod':46132,'codePrice':'25.00','raffTime':'6分钟前','userWeb':'1000006016','userName':'云淡风轻257','userPhoto':'20151126171958149.jpg','userAddr':'福建省厦门市','userRNO':'10000011','userBuyNum':'5','postID':'0','codeType':0},{'codeID':2643637,'goodsPic':'20151222173453179.jpg','goodsName':"澳大利亚 Swisse 奶蓟草 护肝片 120粒/瓶x2",'codePeriod':133,'codePrice':'249.00','raffTime':'6分钟前','userWeb':'1009834203','userName':'13531****33','userPhoto':'00000000000000000.jpg','userAddr':'广东省深圳市','userRNO':'10000184','userBuyNum':'1','postID':'0','codeType':0},{'codeID':1582418,'goodsPic':'20140910165751727.jpg','goodsName':"苹果（Apple）iPhone 6 Plus A1524 16G版 4G手机",'codePeriod':16132,'codePrice':'5388.00','raffTime':'6分钟前','userWeb':'1010280131','userName':'柚子群385359913','userPhoto':'00000000000000000.jpg','userAddr':'福建省三明市沙县','userRNO':'10000032','userBuyNum':'2020','postID':'0','codeType':0},{'codeID':2656671,'goodsPic':'20150525141334296.jpg','goodsName':"加多宝 凉茶（310ml x 24罐）/箱 x 2箱",'codePeriod':18918,'codePrice':'168.00','raffTime':'6分钟前','userWeb':'1011513839','userName':'13480****22','userPhoto':'20151209135027600.jpg','userAddr':'广东省中山市','userRNO':'10000023','userBuyNum':'2','postID':'0','codeType':0},{'codeID':2741486,'goodsPic':'20140919110724510.jpg','goodsName':"金龙鱼 黄金比例 食用调和油 5L",'codePeriod':53966,'codePrice':'65.00','raffTime':'6分钟前','userWeb':'1011205678','userName':'喝水吃馒头','userPhoto':'20151229094536785.jpg','userAddr':'福建省泉州市','userRNO':'10000008','userBuyNum':'20','postID':'0','codeType':0},{'codeID':2699734,'goodsPic':'20151218165444696.jpg','goodsName':"乐心（LIFESENSE）Mambo 运动手环（升级版 ）",'codePeriod':5054,'codePrice':'129.00','raffTime':'6分钟前','userWeb':'1011762743','userName':'上云云云购中大大大奖','userPhoto':'20160101124742102.jpg','userAddr':'江苏省','userRNO':'10000096','userBuyNum':'10','postID':'0','codeType':0},{'codeID':2746482,'goodsPic':'20150605151219903.jpg','goodsName':"旺旺 旺仔牛奶 原味 125mlx20盒 礼盒装",'codePeriod':62195,'codePrice':'53.00','raffTime':'7分钟前','userWeb':'1012611029','userName':'15084****45','userPhoto':'00000000000000000.jpg','userAddr':'湖南省长沙市','userRNO':'10000006','userBuyNum':'10','postID':'0','codeType':0},{'codeID':2750529,'goodsPic':'20151019112525255.jpg','goodsName':"小米（MIUI）红米2A 增强版 移动4G手机",'codePeriod':12902,'codePrice':'589.00','raffTime':'7分钟前','userWeb':'1011871812','userName':'群982987新年快乐','userPhoto':'20151231175743470.jpg','userAddr':'广东省河源市','userRNO':'10000210','userBuyNum':'67','postID':'0','codeType':0},{'codeID':2646542,'goodsPic':'20151223105317462.jpg','goodsName':"新加坡惠氏 Wyeth s-26金装婴儿配方奶粉1段 900g/罐x2（0-12个月）",'codePeriod':44,'codePrice':'399.00','raffTime':'7分钟前','userWeb':'1010500569','userName':'Tmd云购穷三代','userPhoto':'20151106193813261.jpg','userAddr':'福建省泉州市','userRNO':'10000243','userBuyNum':'1','postID':'0','codeType':0},{'codeID':2720617,'goodsPic':'20150313173432499.jpg','goodsName':"双汇 王中王火腿肠 40g x 10支装 x 3包",'codePeriod':4519,'codePrice':'48.00','raffTime':'7分钟前','userWeb':'1011707274','userName':'哎又没中不买了','userPhoto':'00000000000000000.jpg','userAddr':'中国','userRNO':'10000004','userBuyNum':'5','postID':'0','codeType':0},{'codeID':2690425,'goodsPic':'20151010150835695.jpg','goodsName':"苏泊尔（Supor）CYSB50YCW21QJ-100 电压力锅 5L 球釜内胆智能精控",'codePeriod':4338,'codePrice':'469.00','raffTime':'7分钟前','userWeb':'1010896182','userName':'无缘苹果6s','userPhoto':'00000000000000000.jpg','userAddr':'广东省揭阳市','userRNO':'10000417','userBuyNum':'85','postID':'0','codeType':0},{'codeID':2695497,'goodsPic':'20150710142351823.jpg','goodsName':"伊利 原味优酸乳 250mlx24盒 整箱装",'codePeriod':15559,'codePrice':'52.00','raffTime':'7分钟前','userWeb':'1012776959','userName':'云购身家性命全压你了','userPhoto':'20160101165945734.jpg','userAddr':'广东省广州市','userRNO':'10000038','userBuyNum':'15','postID':'0','codeType':0},{'codeID':2645138,'goodsPic':'20151217154356416.jpg','goodsName':"施巴（Sebamed）婴儿沐浴乳 德国进口 200ml",'codePeriod':208,'codePrice':'69.00','raffTime':'7分钟前','userWeb':'1009807109','userName':'15002****23','userPhoto':'00000000000000000.jpg','userAddr':'陕西省西安市','userRNO':'10000014','userBuyNum':'23','postID':'0','codeType':0},{'codeID':2737917,'goodsPic':'20150708112557251.jpg','goodsName':"金龙鱼 优质东北大米 5kg",'codePeriod':106701,'codePrice':'40.00','raffTime':'7分钟前','userWeb':'1011108069','userName':'云购来点副业中一回','userPhoto':'00000000000000000.jpg','userAddr':'陕西省西安市','userRNO':'10000026','userBuyNum':'10','postID':'0','codeType':0},{'codeID':2578857,'goodsPic':'20150327165257584.jpg','goodsName':"360 随身WiFi 3",'codePeriod':9341,'codePrice':'39.00','raffTime':'7分钟前','userWeb':'1011770042','userName':'神手中奖','userPhoto':'20151231042121429.jpg','userAddr':'广东省广州市','userRNO':'10000009','userBuyNum':'6','postID':'0','codeType':0},{'codeID':2706869,'goodsPic':'20151030103220602.jpg','goodsName':"美奈丝 全棉超柔软羽丝绒可水洗枕头芯 一对装 48cm*74cm",'codePeriod':1706,'codePrice':'168.00','raffTime':'7分钟前','userWeb':'1011886036','userName':'18023****28','userPhoto':'00000000000000000.jpg','userAddr':'广东省广州市','userRNO':'10000115','userBuyNum':'33','postID':'0','codeType':0},{'codeID':2741485,'goodsPic':'20140919110724510.jpg','goodsName':"金龙鱼 黄金比例 食用调和油 5L",'codePeriod':53965,'codePrice':'65.00','raffTime':'8分钟前','userWeb':'1009381626','userName':'恭喜阿鬼您又中之再中','userPhoto':'20151112225309701.jpg','userAddr':'广东省深圳市','userRNO':'10000009','userBuyNum':'5','postID':'0','codeType':0},{'codeID':2624014,'goodsPic':'20151130113336154.jpg','goodsName':"阳光少女（Sunmaid）加州葡萄干（罐装）500g 美国进口",'codePeriod':1729,'codePrice':'35.00','raffTime':'8分钟前','userWeb':'1009381626','userName':'恭喜阿鬼您又中之再中','userPhoto':'20151112225309701.jpg','userAddr':'广东省深圳市','userRNO':'10000023','userBuyNum':'11','postID':'0','codeType':0},{'codeID':2518428,'goodsPic':'20151116173631677.jpg','goodsName':"红双喜 DHS E-MX202-2 羽毛球拍 2支装 已穿线",'codePeriod':712,'codePrice':'128.00','raffTime':'8分钟前','userWeb':'1010381255','userName':'13711****85','userPhoto':'00000000000000000.jpg','userAddr':'广东省广州市','userRNO':'10000093','userBuyNum':'88','postID':'0','codeType':0},{'codeID':2504534,'goodsPic':'20151111172609238.jpg','goodsName':"美旅箱包（AmericanTourister）炫彩ABS+PC万向轮拉杆箱 25寸",'codePeriod':1736,'codePrice':'399.00','raffTime':'8分钟前','userWeb':'1011648301','userName':'真他他的惨淡','userPhoto':'00000000000000000.jpg','userAddr':'福建省厦门市','userRNO':'10000304','userBuyNum':'100','postID':'0','codeType':0},{'codeID':2607744,'goodsPic':'20151126152706564.jpg','goodsName':"养元 六个核桃 精品型 核桃露饮料 240mlx12罐 整箱装",'codePeriod':3634,'codePrice':'69.00','raffTime':'8分钟前','userWeb':'1012270717','userName':'今天不中不玩的','userPhoto':'20160101002724588.jpg','userAddr':'内蒙古包头市','userRNO':'10000069','userBuyNum':'1','postID':'0','codeType':0},{'codeID':2691337,'goodsPic':'20150529154529591.jpg','goodsName':"维达（Vinda）手帕纸 超韧系列4层手帕纸 18包/条x2条",'codePeriod':3450,'codePrice':'38.00','raffTime':'8分钟前','userWeb':'1005335303','userName':'新年新開始別再虧本了','userPhoto':'20160101093210201.jpg','userAddr':'江苏省','userRNO':'10000033','userBuyNum':'12','postID':'0','codeType':0},{'codeID':2761204,'goodsPic':'20150605165935473.jpg','goodsName':"伊利 安慕希常温酸牛奶 205mlx12盒 礼盒装",'codePeriod':47224,'codePrice':'59.00','raffTime':'8分钟前','userWeb':'1008713566','userName':'一元晕购毛都没有','userPhoto':'20150911110211307.jpg','userAddr':'河北省石家庄市','userRNO':'10000058','userBuyNum':'5','postID':'0','codeType':0},{'codeID':2559431,'goodsPic':'20151201175925141.jpg','goodsName':"悦胜 澳洲进口雪花牛仔骨 1000g 无腌制",'codePeriod':634,'codePrice':'188.00','raffTime':'8分钟前','userWeb':'1010508580','userName':'15959****10','userPhoto':'00000000000000000.jpg','userAddr':'福建省泉州市','userRNO':'10000066','userBuyNum':'60','postID':'0','codeType':0},{'codeID':2657558,'goodsPic':'20140912171929163.jpg','goodsName':"飘柔（Rejoice）家庭护理兰花长效洁顺水润洗发露 750ml",'codePeriod':3705,'codePrice':'45.00','raffTime':'8分钟前','userWeb':'1008904585','userName':'15913****88','userPhoto':'00000000000000000.jpg','userAddr':'广东省广州市','userRNO':'10000016','userBuyNum':'28','postID':'0','codeType':0}]})
        // http://www.applicationmodule.com/goods/service/getlotterylist?limit=72&page=1&SortID=106
        try {
            $SortID = ($this->get('SortID', ''));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '72'));
            $time = getMilliTime();

            $otherConditions = array();
            if (!empty($SortID)) {
                $otherConditions['gc_id_1'] = $SortID;
            }
            $otherConditions['prize_time'] = array(
                '$lte' => $time
            );
            $list = $this->modelGoods->getStartRaffleList($page, $limit, $otherConditions);
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $goodsInfo) {

                    // 'codeID':2781107,
                    // 'goodsPic':'20151223134549532.jpg',
                    // 'goodsName':"魅族（MEIZU）PRO5 32GB 联通定制版 4G手机",
                    // 'codePeriod':80,
                    // 'codePrice':'2588.00',
                    // 'raffTime':'3分钟前',
                    // 'userWeb':'1008590853',
                    // 'userName':'云购想对你说相见恨晚',
                    // 'userPhoto':'20150723025836157.jpg',
                    // 'userAddr':'广东省潮州市',
                    // 'userRNO':'10002029',
                    // 'userBuyNum':'88',
                    // 'postID':'0',
                    // 'codeType':0

                    $data = array();
                    $data['codeID'] = $goodsInfo['_id'];
                    $data['goodsPic'] = $this->modelGoods->getImagePath($this->baseUrl, $goodsInfo['image']);
                    $data['goodsName'] = $goodsInfo['name'];
                    $data['codePeriod'] = $goodsInfo['period'];
                    $data['codePrice'] = showPrice($goodsInfo['price'], 2);
                    $data['raffTime'] = getMilliTime4Show($goodsInfo['prize_time']);
                    $data['seconds'] = round($time - $goodsInfo['prize_time'], 0);

                    $data['userWeb'] = $goodsInfo['prize_buyer_id'];
                    $data['userName'] = getBuyerName($goodsInfo['prize_buyer_name'], $goodsInfo['prize_buyer_register_by']);
                    $data['userPhoto'] = $this->modelMember->getImagePath($this->baseUrl, $goodsInfo['prize_buyer_avatar']);
                    $data['userAddr'] = convertIp($goodsInfo['prize_buyer_ip']);
                    $data['userRNO'] = $goodsInfo['prize_code'];
                    $data['userBuyNum'] = $goodsInfo['prize_buyer_purchase_num'];
                    $data['postID'] = 0;
                    $data['codeType'] = 0;
                    $datas[] = $data;
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
     * 获取商品所有期列表的接口
     *
     * @return boolean
     */
    public function getgoodsperiodpageAction()
    {
        // http://api.1yyg.com/JPData?action=getGoodsPeriodPage&goodsID=22642&codeID=2575388&FIdx=1&EIdx=100&IsCount=1&fun=jsonp1451652867339&_=1451652870832
        // jsonp1451652867339({"code":0,"totalCount":2688,"listItems":[{"codePeriod":2688,"codeID":2575389,"codeState":1},{"codePeriod":2687,"codeID":2575388,"codeState":3},{"codePeriod":2686,"codeID":2575387,"codeState":3},{"codePeriod":2685,"codeID":2575386,"codeState":3},{"codePeriod":2684,"codeID":2575385,"codeState":3},{"codePeriod":2683,"codeID":2575384,"codeState":3},{"codePeriod":2682,"codeID":2575383,"codeState":3},{"codePeriod":2681,"codeID":2575382,"codeState":3},{"codePeriod":2680,"codeID":2575381,"codeState":3},{"codePeriod":2679,"codeID":2575380,"codeState":3},{"codePeriod":2678,"codeID":2575379,"codeState":3},{"codePeriod":2677,"codeID":2575378,"codeState":3},{"codePeriod":2676,"codeID":2575377,"codeState":3},{"codePeriod":2675,"codeID":2575376,"codeState":3},{"codePeriod":2674,"codeID":2575375,"codeState":3},{"codePeriod":2673,"codeID":2575374,"codeState":3},{"codePeriod":2672,"codeID":2575373,"codeState":3},{"codePeriod":2671,"codeID":2575372,"codeState":3},{"codePeriod":2670,"codeID":2575371,"codeState":3},{"codePeriod":2669,"codeID":2575370,"codeState":3},{"codePeriod":2668,"codeID":2575369,"codeState":3},{"codePeriod":2667,"codeID":2575368,"codeState":3},{"codePeriod":2666,"codeID":2575367,"codeState":3},{"codePeriod":2665,"codeID":2575366,"codeState":3},{"codePeriod":2664,"codeID":2575365,"codeState":3},{"codePeriod":2663,"codeID":2575364,"codeState":3},{"codePeriod":2662,"codeID":2575363,"codeState":3},{"codePeriod":2661,"codeID":2575362,"codeState":3},{"codePeriod":2660,"codeID":2575361,"codeState":3},{"codePeriod":2659,"codeID":2575360,"codeState":3},{"codePeriod":2658,"codeID":2575359,"codeState":3},{"codePeriod":2657,"codeID":2575358,"codeState":3},{"codePeriod":2656,"codeID":2575357,"codeState":3},{"codePeriod":2655,"codeID":2575356,"codeState":3},{"codePeriod":2654,"codeID":2575355,"codeState":3},{"codePeriod":2653,"codeID":2575354,"codeState":3},{"codePeriod":2652,"codeID":2575353,"codeState":3},{"codePeriod":2651,"codeID":2575352,"codeState":3},{"codePeriod":2650,"codeID":2575351,"codeState":3},{"codePeriod":2649,"codeID":2575350,"codeState":3},{"codePeriod":2648,"codeID":2575349,"codeState":3},{"codePeriod":2647,"codeID":2575348,"codeState":3},{"codePeriod":2646,"codeID":2575347,"codeState":3},{"codePeriod":2645,"codeID":2575346,"codeState":3},{"codePeriod":2644,"codeID":2575345,"codeState":3},{"codePeriod":2643,"codeID":2575344,"codeState":3},{"codePeriod":2642,"codeID":2575343,"codeState":3},{"codePeriod":2641,"codeID":2575342,"codeState":3},{"codePeriod":2640,"codeID":2575341,"codeState":3},{"codePeriod":2639,"codeID":2575340,"codeState":3},{"codePeriod":2638,"codeID":2575339,"codeState":3},{"codePeriod":2637,"codeID":2575338,"codeState":3},{"codePeriod":2636,"codeID":2575337,"codeState":3},{"codePeriod":2635,"codeID":2575336,"codeState":3},{"codePeriod":2634,"codeID":2575335,"codeState":3},{"codePeriod":2633,"codeID":2575334,"codeState":3},{"codePeriod":2632,"codeID":2575333,"codeState":3},{"codePeriod":2631,"codeID":2575332,"codeState":3},{"codePeriod":2630,"codeID":2575331,"codeState":3},{"codePeriod":2629,"codeID":2575330,"codeState":3},{"codePeriod":2628,"codeID":2575329,"codeState":3},{"codePeriod":2627,"codeID":2575328,"codeState":3},{"codePeriod":2626,"codeID":2575327,"codeState":3},{"codePeriod":2625,"codeID":2575326,"codeState":3},{"codePeriod":2624,"codeID":2575325,"codeState":3},{"codePeriod":2623,"codeID":2575324,"codeState":3},{"codePeriod":2622,"codeID":2575323,"codeState":3},{"codePeriod":2621,"codeID":2575322,"codeState":3},{"codePeriod":2620,"codeID":2575321,"codeState":3},{"codePeriod":2619,"codeID":2575320,"codeState":3},{"codePeriod":2618,"codeID":2575319,"codeState":3},{"codePeriod":2617,"codeID":2575318,"codeState":3},{"codePeriod":2616,"codeID":2575317,"codeState":3},{"codePeriod":2615,"codeID":2575316,"codeState":3},{"codePeriod":2614,"codeID":2575315,"codeState":3},{"codePeriod":2613,"codeID":2575314,"codeState":3},{"codePeriod":2612,"codeID":2575313,"codeState":3},{"codePeriod":2611,"codeID":2575312,"codeState":3},{"codePeriod":2610,"codeID":2575311,"codeState":3},{"codePeriod":2609,"codeID":2575310,"codeState":3},{"codePeriod":2608,"codeID":2575309,"codeState":3},{"codePeriod":2607,"codeID":2575308,"codeState":3},{"codePeriod":2606,"codeID":2575307,"codeState":3},{"codePeriod":2605,"codeID":2575306,"codeState":3},{"codePeriod":2604,"codeID":2575305,"codeState":3},{"codePeriod":2603,"codeID":2575304,"codeState":3},{"codePeriod":2602,"codeID":2575303,"codeState":3},{"codePeriod":2601,"codeID":2575302,"codeState":3},{"codePeriod":2600,"codeID":2575301,"codeState":3},{"codePeriod":2599,"codeID":2575300,"codeState":3},{"codePeriod":2598,"codeID":2575299,"codeState":3},{"codePeriod":2597,"codeID":2575298,"codeState":3},{"codePeriod":2596,"codeID":2575297,"codeState":3},{"codePeriod":2595,"codeID":2575296,"codeState":3},{"codePeriod":2594,"codeID":2575295,"codeState":3},{"codePeriod":2593,"codeID":2575294,"codeState":3},{"codePeriod":2592,"codeID":2575293,"codeState":3},{"codePeriod":2591,"codeID":2575292,"codeState":3},{"codePeriod":2590,"codeID":2575291,"codeState":3},{"codePeriod":2589,"codeID":2575290,"codeState":3}]})
        // http://www.applicationmodule.com/goods/service/getgoodsperiodpage?goodsID=22642&codeID=2575388&page=1&limit=100
        try {
            $goods_id = $this->get('goodsID', '');
            if (empty($goods_id)) {
                echo ($this->error(-1, '商品ID为空'));
                return true;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '100'));
            $list = $this->modelGoods->getPeriodList($goods_id, $page, $limit);
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $goodsInfo) {
                    // "codePeriod":2688,
                    // "codeID":2575389,
                    // "codeState":1
                    $data = array();
                    $data['codeID'] = $goodsInfo['_id'];
                    $data['codePeriod'] = $goodsInfo['period'];
                    $data['codeState'] = $goodsInfo['sale_state'];
                    $datas[] = $data;
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
     * 获取该期商品信息的接口
     *
     * @return boolean
     */
    public function getgoodsperiodinfoAction()
    {
        // http://api.1yyg.com/JPData?action=getGoodsPeriodInfo&goodsID=22642&period=100&fun=jsonp1451652867395&_=1451653126485
        // http://www.applicationmodule.com/goods/service/getgoodsperiodinfo?goodsID=22642&period=100
        try {
            $goods_id = $this->get('goodsID', '');
            if (empty($goods_id)) {
                echo ($this->error(-1, '商品ID为空'));
                return true;
            }
            $period = intval($this->get('period', '0'));
            if (empty($period)) {
                echo ($this->error(-2, '期数为空'));
                return true;
            }
            $goodsInfo = $this->modelGoods->getPeriodInfo($goods_id, $period);
            if (empty($goodsInfo)) {
                echo ($this->error(-3, '商品不存在'));
                return true;
            }

            $ret = array();
            // "codePeriod":2688,
            // "codeState":1
            $ret = array();
            $ret['codeID'] = $goodsInfo['_id'];
            $ret['codeState'] = $goodsInfo['sale_state'];
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取我的关注的接口
     * 会员-我的关注
     */
    public function getcollectgoodslistAction()
    {
        // http://member.1yyg.com/JPData?action=getCollectGoodsList&FIdx=1&EIdx=15&isCount=1&fun=jsonp1451609871046&_=1451609871386
        // jsonp1451609871046({"code":0,"totalCount":2,"isExistsNotSale":1,"listItems":[{"goodsID":21876,"codeID":2716987,"goodsName":"蒙牛 特仑苏 纯牛奶 250mlx12盒 礼盒装","goodsPic":"20140813160215287.jpg","codePeriod":54297,"quantity":65,"sales":8,"isSale":1,"limitBuy":0,"codeType":0},{"goodsID":22597,"codeID":2552252,"goodsName":"2016款 宝马（BMW）3系 320i 时尚型 四门轿车","goodsPic":"20151102140442446.jpg","codePeriod":271,"quantity":389999,"sales":389999,"isSale":0,"limitBuy":0,"codeType":0}]})
        // http://www.applicationmodule.com/goods/service/getcollectgoodslist?page=1&limit=15
        try {
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '15'));

            $ret = array();
            $ret['total'] = 0;
            $ret['datas'] = array();

            if (!empty($_SESSION['member_id'])) {

                $otherConditions = array();
                $list = $this->modelGoodsCollect->getUserCollectlist($_SESSION['member_id'], $page, $limit, $otherConditions);
                $ret['total'] = $list['total'];
                $datas = array();
                if (!empty($list['datas'])) {
                    $goods_commonids = array_keys($list['datas']);
                    $period_goods_ids = $this->modelGoodsCommon->getPeriodGoodsIdListByIds($goods_commonids);
                    $periodGoodsList = $this->modelGoods->getListByIds($period_goods_ids, 'goods_commonid');

                    foreach ($list['datas'] as $value) {
                        // "goodsID": 21876,
                        // "codeID": 2561952,
                        // "goodsName": "蒙牛 特仑苏 纯牛奶 250mlx12盒 礼盒装",
                        // "goodsPic": "20140813160215287.jpg",
                        // "codePeriod": 51932,
                        // "quantity": 65,
                        // "sales": 3,
                        // "isSale": 1,
                        // "limitBuy": 0,
                        // "codeType": 0
                        if (!isset($periodGoodsList[$value['goods_id']])) {
                            throw new \Exception("{$value['goods_id']}不存在");
                        }
                        $item = $periodGoodsList[$value['goods_id']];

                        $datas[] = array(
                            'goodsID' => $item['goods_commonid'],
                            'codeID' => $item['_id'],
                            'goodsName' => $item['name'],
                            'goodsPic' => $this->modelGoods->getImagePath($this->baseUrl, $item['image']),
                            'codePeriod' => $item['period'],
                            'quantity' => intval($item['total_person_time']),
                            'sales' => intval($item['purchase_person_time']),
                            'isSale' => $item['sale_state'],
                            'limitBuy' => intval($item['restrict_person_time']),
                            'codeType' => 0
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
     * 关注商品
     * 商品详情页面
     */
    public function addcollectgoodsAction()
    {
        try {
            // http://www.applicationmodule.com/goods/service/addcollectgoods?goods_id=xxx
            $goods_id = ($this->get('goods_id', ''));
            if (empty($goods_id)) {
                echo ($this->error(-2, '商品ID为空'));
                return false;
            }
            if (empty($_SESSION['member_id'])) {
                echo ($this->error(-1, '非法访问'));
                return false;
            }
            // 检查商品是否存在
            $goodsInfo = $this->modelGoodsCommon->getInfoById($goods_id);
            if (empty($goodsInfo)) {
                echo ($this->error(-3, '商品ID不存在'));
                return false;
            }
            // 检查是否已关注了
            $goodsCollectInfo = $this->modelGoodsCollect->getInfoByUserIdAndGoodsId($_SESSION['member_id'], $goodsInfo['_id']);
            if (empty($goodsCollectInfo)) {
                $this->modelGoodsCollect->log($_SESSION['member_id'], $goodsInfo['_id']);
                $this->modelGoodsCommon->incCollectNum($goodsInfo['_id']);
                $this->modelGoods->incCollectNum($goodsInfo['_id']);
            }
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 取消关注
     * 商品详情页面
     */
    public function delcollectgoodsAction()
    {
        try {
            // http://member.1yyg.com/JPData?action=delCollectGoods&goodsID=22746&fun=jsonp1453015089178&_=1453015991779
            // http://www.applicationmodule.com/goods/service/delcollectgoods?goodsID=xxx
            $goods_id = ($this->get('goodsID', ''));
            if (empty($goods_id)) {
                echo ($this->error(-2, '商品ID为空'));
                return false;
            }
            if (empty($_SESSION['member_id'])) {
                echo ($this->error(-1, '非法访问'));
                return false;
            }
            $this->modelGoodsCollect->del($_SESSION['member_id'], $goods_id);
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 检查关注
     * 商品详情页面
     */
    public function checkcollectgoodsAction()
    {
        try {
            // http://www.applicationmodule.com/goods/service/checkcollectgoods?goods_id=xxx
            $goods_id = ($this->get('goods_id', ''));
            if (empty($goods_id)) {
                echo ($this->error(-2, '商品ID为空'));
                return false;
            }
            if (empty($_SESSION['member_id'])) {
                echo ($this->error(-1, '非法访问'));
                return false;
            }
            // 检查是否已关注了
            $goodsCollectInfo = $this->modelGoodsCollect->getInfoByUserIdAndGoodsId($_SESSION['member_id'], $goods_id);
            if (empty($goodsCollectInfo)) {
                $is_collected = false;
            } else {
                $is_collected = true;
            }
            echo ($this->result("OK", $is_collected));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 有关注
     */
    public function hascollectgoodsAction()
    {
        try {
            // http://www.applicationmodule.com/goods/service/hascollectgoods
            if (empty($_SESSION['member_id'])) {
                echo ($this->error(-1, '非法访问'));
                return false;
            }
            // 检查是否已有关注
            $hasGoods = $this->modelGoodsCollect->hasGoods($_SESSION['member_id']);

            echo ($this->result("OK", $hasGoods));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}

<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\Type;
use App\Backend\Submodules\Goods\Models\Category;
use App\Backend\Submodules\Goods\Models\Brand;
use App\Backend\Submodules\Goods\Models\GoodsCommon;
use App\Backend\Submodules\Goods\Models\Goods;
use App\Backend\Submodules\Store\Models\Store;

/**
 * @title({name="商品SKU管理"})
 *
 * @name 商品SKU管理
 */
class GoodsController extends \App\Backend\Controllers\FormController
{

    private $stateDatas = \App\Goods\Models\Goods::STATEDATAS;

    private $verifyDatas = \App\Goods\Models\Goods::VERIFYDATAS;

    private $saleStateDatas = \App\Goods\Models\Goods::SALESTATEDATAS;

    private $modelType;

    private $modelCategory;

    private $modelBrand;

    private $modelGoodsCommon;

    private $modelGoods;

    public function initialize()
    {
        $this->modelType = new Type();
        $this->modelCategory = new Category();
        $this->modelBrand = new Brand();
        $this->modelGoodsCommon = new GoodsCommon();
        $this->modelGoods = new Goods();
        $this->modelStore = new Store();
        
        parent::initialize();
    }

    /**
     * @title({name="抽奖"})
     *
     * @name 抽奖
     */
    public function lotteryAction()
    {
        try {
            
            $input = $this->getFilterInput();
            
            if ($input->isValid("id")) {} else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            $this->doLottery($input->id);
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {        $schemas['goods_commonid'] = array(
            'name' => '所属商品',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select2',
                'is_show' => true,
                'items' => function ($id)
                {
                    return $this->modelGoodsCommon->getAll(array(
                        $id
                    ));
                },
                'select' => array(
                    'is_remote_load' => true,
                    'apiUrl' => "admin/goods/goodscommon/getgoodslist"
                )
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['name'] = array(
            'name' => '商品名称',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'width' => "10%"
            ),
            'search' => array(
                'is_show' => true
            )
        );
        $schemas['jingle'] = array(
            'name' => '广告词',
            'data' => array(
                'type' => 'string',
                'length' => 150
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['price'] = array(
            'name' => '价格',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['image'] = array(
            'name' => '商品主图',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelGoods->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['state'] = array(
            'name' => '商品状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->stateDatas
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 商品审核 1通过，0未通过，10审核中
        $schemas['verify'] = array(
            'name' => '审核状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->verifyDatas
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 是否是否热门
        $schemas['is_hot'] = array(
            'name' => '是否热门',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 是否是否最新
        $schemas['is_new'] = array(
            'name' => '是否最新',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['period'] = array(
            'name' => '期数',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'width' => "20%"
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['lottery_code'] = array(
            'name' => '云购码基数',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['lottery_prize_id'] = array(
            'name' => '云购奖品',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['total_person_time'] = array(
            'name' => '总需人次',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'width' => "20%"
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['purchase_person_time'] = array(
            'name' => '参与人次',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['remain_person_time'] = array(
            'name' => '剩余人次',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['restrict_person_time'] = array(
            'name' => '限购人次',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 销售状态 1 进行中 2 揭晓中 3 已揭晓
        $schemas['sale_state'] = array(
            'name' => '销售状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->saleStateDatas
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['prize_code'] = array(
            'name' => '中奖码',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['prize_time'] = array(
            'name' => '中奖时间',
            'data' => array(
                'type' => 'string',
                'length' => '19'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['prize_order_goods_id'] = array(
            'name' => '中奖订单商品ID',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['gc_id'] = array(
            'name' => '所属分类',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    // die('sdfsdfsdf');
                    return $this->modelCategory->getList4Tree('');
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id_1'] = array(
            'name' => '多级分类',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'is_show' => true,
                'partial' => '../submodules/goods/views/partials/categorycascade'
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id_2'] = array(
            'name' => '二级分类',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'is_show' => true,
                'partial' => 'partials/empty'
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id_3'] = array(
            'name' => '三级分类',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'partial' => 'partials/empty',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['brand_id'] = array(
            'name' => '所属品牌',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelBrand->getAll();
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['store_id'] = array(
            'name' => '所属店铺',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelStore->getAll();
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['store_name'] = array(
            'name' => '店铺名',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['promotion_price'] = array(
            'name' => '促销价格',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 促销类型 0无促销，1团购，2限时折扣
        $schemas['promotion_type'] = array(
            'name' => '促销类型',
            'data' => array(
                'type' => 'integer',
                'length' => 3
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['marketprice'] = array(
            'name' => '市场价',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 商家编号
        $schemas['serial'] = array(
            'name' => 'serial',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['storage_alarm'] = array(
            'name' => '库存报警值',
            'data' => array(
                'type' => 'integer',
                'length' => 3
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['click'] = array(
            'name' => '商品点击数量',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['salenum'] = array(
            'name' => '销售数量',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['collect'] = array(
            'name' => '收藏数量',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['spec'] = array(
            'name' => '商品规格序列化',
            'data' => array(
                'type' => 'string',
                'length' => 0
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['storage'] = array(
            'name' => '商品库存',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        // $schemas['addtime'] = array(
        // 'name' => '添加时间',
        // 'data' => array(
        // 'type' => 'datetime',
        // 'length' => '19',
        // 'defaultValue' => getCurrentTime($now)
        // ),
        // 'validation' => array(
        // 'required' => 1
        // ),
        // 'form' => array(
        // 'input_type' => 'datetimepicker',
        // 'is_show' => true
        // ),
        // 'list' => array(
        // 'is_show' => false
        // ),
        // 'search' => array(
        // 'is_show' => false
        // )
        // );
        // $schemas['edittime'] = array(
        // 'name' => '编辑时间',
        // 'data' => array(
        // 'type' => 'datetime',
        // 'length' => '19',
        // 'defaultValue' => getCurrentTime($now)
        // ),
        // 'validation' => array(
        // 'required' => 1
        // ),
        // 'form' => array(
        // 'input_type' => 'datetimepicker',
        // 'is_show' => true
        // ),
        // 'list' => array(
        // 'is_show' => false
        // ),
        // 'search' => array(
        // 'is_show' => false
        // )
        // );
        $schemas['areaid_1'] = array(
            'name' => '一级地区',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['areaid_2'] = array(
            'name' => '二级地区',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['color_id'] = array(
            'name' => '颜色规格',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['transport_id'] = array(
            'name' => '运费模板',
            'data' => array(
                'type' => 'integer',
                'length' => 8
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 运费 0为免运费
        $schemas['freight'] = array(
            'name' => '运费',
            'data' => array(
                'type' => 'decimal',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'currency',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 是否开具增值税发票 1是，0否
        $schemas['vat'] = array(
            'name' => '是否开具增值税发票',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 商品推荐 1是，0否 默认0
        $schemas['commend'] = array(
            'name' => '商品推荐',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['stcids'] = array(
            'name' => '店铺分类',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['evaluation_good_star'] = array(
            'name' => '好评星级',
            'data' => array(
                'type' => 'integer',
                'length' => 3
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['evaluation_count'] = array(
            'name' => '评价数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 是否为虚拟商品 1是，0否
        $schemas['is_virtual'] = array(
            'name' => '是否为虚拟商品',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => false,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 虚拟商品有效期
        $schemas['virtual_indate'] = array(
            'name' => '虚拟商品有效期',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['virtual_limit'] = array(
            'name' => '虚拟商品购买上限',
            'data' => array(
                'type' => 'integer',
                'length' => 3
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 是否允许过期退款， 1是，0否
        $schemas['virtual_invalid_refund'] = array(
            'name' => '是否允许过期退款',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => false,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 是否为F码商品 1是，0否
        $schemas['is_fcode'] = array(
            'name' => '是否为F码商品',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => false,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 是否是预约商品 1是，0否
        $schemas['is_appoint'] = array(
            'name' => '是否是预约商品',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => false,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 是否是预售商品 1是，0否
        $schemas['is_presell'] = array(
            'name' => '是否是预售商品',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => false,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 是否拥有赠品
        $schemas['have_gift'] = array(
            'name' => '是否拥有赠品',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => false,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 是否为平台自营
        $schemas['is_own_shop'] = array(
            'name' => '平台自营',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '商品SKU';
    }

    protected function getModel()
    {
        return $this->modelGoods;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        // $goodsList = $this->modelGoodsCommon->getAll();
        foreach ($list['data'] as &$item) {
            // $item['goods_commonid'] = isset($goodsList[$item['goods_commonid']]) ? $goodsList[$item['goods_commonid']] : "--";
            $item['period'] = "云:{$item['period']}<br/>云购买基数:{$item['lottery_code']}<br/>云购奖品:{$item['lottery_prize_id']}";
            $item['total_person_time'] = "总需人次:{$item['total_person_time']}<br/>参与人次:{$item['purchase_person_time']}<br/>剩余人次:{$item['remain_person_time']}<br/>限购次数:{$item['restrict_person_time']}<br/>完成度:{$item['complete_percent']}%";
            $item['prize_code'] = "中奖码:{$item['prize_code']}<br/>中奖时间:{$item['prize_time']}<br/>中奖用户:{$item['prize_buyer_id']}-{$item['prize_buyer_name']}<br/>中奖订单商品ID:{$item['prize_order_goods_id']}"; // <br/>订单商品列表:{$item['order_goods_list']}
            $item['price'] = showPrice($item['price'], 2);
            $item['state'] = $this->stateDatas[$item['state']]['name'];
            $item['verify'] = $this->verifyDatas[$item['verify']]['name'];
            if ($item['sale_state'] == \App\Common\Models\Goods\Goods::SALE_STATE2) {
                $item['sale_state'] = $this->saleStateDatas[$item['sale_state']]['name'];
                $item['sale_state'] = $item['sale_state'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要进行抽奖吗？\', \'lottery\')" class="halflings-icon user white"><i></i> 抽奖</a>';
            } else {
                $item['sale_state'] = $this->saleStateDatas[$item['sale_state']]['name'];
            }
        }
        return $list;
    }

    function doLottery($goods_id)
    {
        $req_time = time();
        $param = array(
            'goods_id' => $goods_id
        );
        $ret = doPost("http://www.applicationmodule.com/goods/service/lottery", $param);
        $ret = (string) $ret;
        if (! empty($ret)) {
            if (isJson($ret)) {
                $ret = json_decode($ret, true);
                if ($ret["success"]) {
                    $info = $ret['result'];
                    return $info;
                } else {
                    throw new \Exception("满员商品抽奖信息为空");
                }
            } else {
                throw new \Exception("满员商品抽奖信息不是有效的json格式");
            }
        } else {
            throw new \Exception("可能网络繁忙,满员商品抽奖的请求失败");
        }
    }
}
<?php

namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\Type;
use App\Backend\Submodules\Goods\Models\Category;
use App\Backend\Submodules\Goods\Models\Brand;
use App\Backend\Submodules\Goods\Models\GoodsCommon;

/**
 * @title({name="商品公共内容管理"})
 *
 * @name 商品公共内容管理
 */
class GoodscommonController extends \App\Backend\Controllers\FormController
{
    // 表单页的模板是form还是tabbedform
    protected $form_template = 'tabbedform';

    // tabbed表单结构设置
    protected $tabbedform_settings = array(
        // tab
        'tabs' => array(
            0 => array('name' => '基本信息', 'fields' => array('name', 'jingle', 'price', 'image', 'body', 'state', 'verify', 'is_lock', 'is_hot', 'is_new', 'commend')),
            1 => array('name' => '开奖信息', 'fields' => array('restrict_person_time', 'current_period', 'max_period', 'lottery_code', 'period_goods_id')),
            2 => array('name' => '分类&品牌', 'fields' => array('gc_id', 'gc_id_1', 'gc_id_2', 'gc_id_3', 'gc_name', 'store_id', 'store_name', 'spec_name', 'spec_value', 'brand_id', 'brand_name', 'type_id', 'attr')),
            3 => array('name' => '促销信息', 'fields' => array('mobile_body', 'stateremark', 'verifyremark', 'addtime', 'selltime', 'specname', 'marketprice', 'costprice', 'discount', 'serial', 'storage_alarm', 'transport_id', 'transport_title', 'freight')),
            4 => array('name' => '其他信息', 'fields' => array('vat', 'areaid_1', 'areaid_2', 'goods_stcids', 'plateid_top', 'is_virtual', 'virtual_indate', 'virtual_invalid_refund', 'is_fcode', 'is_appoint', 'appoint_satedate', 'is_presell', 'presell_deliverdate', 'is_own_shop', 'collect')),
        )
    );

    private $stateDatas = \App\Goods\Models\GoodsCommon::STATEDATAS;

    private $verifyDatas = \App\Goods\Models\GoodsCommon::VERIFYDATAS;

    private $modelType;

    private $modelCategory;

    private $modelBrand;

    private $modelGoodsCommon;

    public function initialize()
    {
        $this->modelType = new Type();
        $this->modelCategory = new Category();
        $this->modelBrand = new Brand();
        $this->modelGoodsCommon = new GoodsCommon();
        parent::initialize();
    }

    /**
     * @title({name="获取商品列表"})
     *
     * @name 获取商品列表
     */
    public function getgoodslistAction()
    {
        try {
            $this->response->setHeader("Content-Type", "application/json; charset=utf-8");

            $q = urldecode($this->get('q', ''));
            $page = intval($this->get('page', '1'));
            $query = array(
                'name' => array(
                    '$like' => '%' . $q . '%'
                )
            );
            $sort = array(
                '_id' => -1
            );
            $ret = array();
            $ret['total_count'] = 0;
            $ret['items'] = array();
            $ret['incomplete_results'] = false;

            if (!empty($q)) {
                $list = $this->modelGoodsCommon->find($query, $sort, ($page - 1), 10);
                $ret['total_count'] = $list['total'];
                if (!empty($list['datas'])) {
                    foreach ($list['datas'] as &$value) {
                        $data = array();
                        $data["id"] = $value['_id'];
                        $data['text'] = $value['name'];
                        $data['image'] = $this->modelGoodsCommon->getImagePath($this->baseUrl, $value['image'], 50, 50);
                        $ret['items'][] = $data;
                    }
                }
            }

            // $ret = array();
            // $ret['total_count'] = 1;
            // $data = array();
            // $data["id"] = "12345";
            // $data["full_name"] = "ryanmjacobs/c";
            // $data["owner"]["avatar_url"] = ("https://avatars.githubusercontent.com/u/4251257?v=3");
            // $data["description"] = "Compile and execute C \"scripts\" in one go!";
            // $data["forks_count"] = 88;
            // $data["stargazers_count"] = 1313;
            // $data["watchers_count"] = 1313;
            // $ret['items'][0] = $data;
            echo \App\Common\Utils\Helper::myJsonEncode($ret);
            // $this->response->setJsonContent($ret)->send();
            return true;
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="生成新期商品"})
     *
     * @name 生成新期商品
     */
    public function newperiodAction()
    {
        try {

            $input = $this->getFilterInput();

            if ($input->isValid("id")) {
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            $this->createNewPeriodGoods($input->id);
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['name'] = array(
            'name' => '商品名',
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
                'length' => 190
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false
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
                'required' => 1
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
                'length' => 255,
                'file' => array(
                    'path' => $this->modelGoodsCommon->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
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

        $schemas['body'] = array(
            'name' => '商品内容',
            'data' => array(
                'type' => 'html',
                'length' => 1024
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'ueditor',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
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
                'required' => 1
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

        $schemas['verify'] = array(
            'name' => '审核状态',
            'data' => array(
                'type' => 'integer',
                'length' => 3
            ),
            'validation' => array(
                'required' => 1
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

        // 商品锁定 0未锁，1已锁
        $schemas['is_lock'] = array(
            'name' => '锁定状态',
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
                'is_show' => true,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => true
            )
        );

        // 是否是否新品
        $schemas['is_new'] = array(
            'name' => '是否新品',
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
                'is_show' => true,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => true
            )
        );

        // 商品推荐 1是，0否，默认为0
        $schemas['commend'] = array(
            'name' => '商品推荐',
            'data' => array(
                'type' => 'boolean',
                'length' => 1
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
                'is_show' => true,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => true
            )
        );

        $schemas['restrict_person_time'] = array(
            'name' => '限购人次',
            'data' => array(
                'type' => 'integer',
                'length' => '11'
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

        $schemas['current_period'] = array(
            'name' => '当期数',
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
                'is_show' => true,
                'width' => "20%"
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['max_period'] = array(
            'name' => '最大期数',
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

        $schemas['period_goods_id'] = array(
            'name' => '当期商品',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
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
                'items' => function () {
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
            'name' => '一级分类',
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
                'items' => function () {
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
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
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
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
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
        $schemas['gc_name'] = array(
            'name' => '分类名',
            'data' => array(
                'type' => 'string',
                'length' => 200
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
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
        $schemas['spec_name'] = array(
            'name' => '规格名',
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
        $schemas['spec_value'] = array(
            'name' => '规格值',
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
                'items' => function () {
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
        $schemas['brand_name'] = array(
            'name' => '品牌名',
            'data' => array(
                'type' => 'string',
                'length' => 100
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['type_id'] = array(
            'name' => '所属类型',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => false,
                'items' => function () {
                    return $this->modelType->getAll();
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['attr'] = array(
            'name' => '商品属性',
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

        $schemas['mobile_body'] = array(
            'name' => '手机端商品描述',
            'data' => array(
                'type' => 'html',
                'length' => 1024
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'ueditor',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['stateremark'] = array(
            'name' => '违规原因',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['verifyremark'] = array(
            'name' => '审核失败原因',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false
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
        $schemas['addtime'] = array(
            'name' => '添加时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['selltime'] = array(
            'name' => '上架时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['specname'] = array(
            'name' => '规格名称序列化',
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
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['costprice'] = array(
            'name' => '成本价',
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
        $schemas['discount'] = array(
            'name' => '折扣',
            'data' => array(
                'type' => 'decimal',
                'length' => 0
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'decimal',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['serial'] = array(
            'name' => '商家编号',
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
        $schemas['transport_id'] = array(
            'name' => '运费模板',
            'data' => array(
                'type' => 'integer',
                'length' => 11
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
        $schemas['transport_title'] = array(
            'name' => '运费模板名称',
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
                'is_show' => true
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
        $schemas['goods_stcids'] = array(
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
        $schemas['plateid_top'] = array(
            'name' => '顶部关联板式',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 0
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
        $schemas['plateid_bottom'] = array(
            'name' => '底部关联板式',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 0
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
        $schemas['virtual_indate'] = array(
            'name' => '虚拟商品有效期',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => 0
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
                'required' => 0
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
        // 是否为F码商品 1是，0否
        $schemas['is_fcode'] = array(
            'name' => '是否为F码商品',
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
        // 是否是预约商品 1是，0否
        $schemas['is_appoint'] = array(
            'name' => '是否是预约商品',
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
        // 预约商品出售时间
        $schemas['appoint_satedate'] = array(
            'name' => '预约商品出售时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => 1
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
        // 是否是预售商品 1是，0否
        $schemas['is_presell'] = array(
            'name' => '是否是预售商品',
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
        // 预售商品发货时间
        $schemas['presell_deliverdate'] = array(
            'name' => '预售商品发货时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => 1
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

        return $schemas;
    }

    protected function getName()
    {
        return '商品公共内容';
    }

    protected function getModel()
    {
        return $this->modelGoodsCommon;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        // $typeList = $this->modelType->getAll();
        // $brandList = $this->modelBrand->getAll();
        // $categoryList = $this->modelCategory->getAll();
        foreach ($list['data'] as &$item) {
            // $item['type_id'] = isset($typeList[$item['type_id']]) ? $typeList[$item['type_id']] : "--";
            $item['current_period'] = "云:{$item['current_period']}<br/>云购买基数:{$item['lottery_code']}<br/>当期商品:{$item['period_goods_id']}<br/>限购人次:{$item['restrict_person_time']}";
            $item['price'] = showPrice($item['price'], 2);
            $item['state'] = $this->stateDatas[$item['state']]['name'];
            $item['verify'] = $this->verifyDatas[$item['verify']]['name'];
            $item['max_period'] = $item['max_period'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要生成新一期的商品记录吗？\', \'newperiod\')" class="halflings-icon user white"><i></i> 新期商品生成</a>';
        }

        return $list;
    }

    function createNewPeriodGoods($goods_commonid)
    {
        $req_time = time();
        $param = array(
            'goods_commonid' => $goods_commonid
        );
        $ret = doPost("http://www.myapplicationmodule.com/goods/service/createnewperiod", $param);
        $ret = (string) $ret;
        if (!empty($ret)) {
            if (isJson($ret)) {
                $ret = json_decode($ret, true);
                if ($ret["success"]) {
                    $info = $ret['result'];
                    return $info;
                } else {
                    throw new \Exception("生成新期商品信息为空");
                }
            } else {
                throw new \Exception("生成新期商品信息不是有效的json格式");
            }
        } else {
            throw new \Exception("可能网络繁忙,生成新期商品的请求失败");
        }
    }
}

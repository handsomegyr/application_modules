<?php

namespace App\Backend\Submodules\Member\Controllers;

use App\Backend\Submodules\Member\Models\Consignee;
use App\System\Models\Area;

/**
 * @title({name="收货人管理"})
 *
 * @name 收货人管理
 */
class ConsigneeController extends \App\Backend\Controllers\FormController
{

    private $modelConsignee;

    private $modelArea;

    public function initialize()
    {
        $this->modelConsignee = new Consignee();

        $this->modelArea = new Area();
        parent::initialize();
    }

    /**
     * @title({name="获取城市列表"})
     *
     * @name 获取城市列表
     */
    public function getcitysAction()
    {
        try {
            $this->response->setHeader("Content-Type", "application/json; charset=utf-8");
            $province = urldecode($this->get('province', ''));
            $province = trim($province);
            $ret = $this->modelArea->getCitys($province);
            $data = array();
            if (!empty($ret)) {
                foreach ($ret as $key => $value) {
                    $data[] = array('id' => strval($key), 'text' => strval($value));
                }
            }
            return $this->makeJsonResult($data, '获取成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取区县列表"})
     *
     * @name 获取区县列表
     */
    public function getdistrictsAction()
    {
        try {
            $this->response->setHeader("Content-Type", "application/json; charset=utf-8");
            $city = urldecode($this->get('city', ''));
            $city = trim($city);
            $ret = $this->modelArea->getDistricts($city);
            $data = array();
            if (!empty($ret)) {
                foreach ($ret as $key => $value) {
                    $data[] = array('id' => strval($key), 'text' => strval($value));
                }
            }
            return $this->makeJsonResult($data, '获取成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['member_id'] = array(
            'name' => '会员',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['name'] = array(
            'name' => '收货人',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        $schemas['province'] = array(
            'name' => '省份',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelArea->getProvinces();
                }
            ),
            'list' => array(
                'is_show' => true,
                'items' => function () {
                    return $this->modelArea->getProvinces();
                }
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['city'] = array(
            'name' => '城市',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'cascade' => 'province',
                'cascadeAjax' => array(
                    'type' => 'POST',
                    'url' => "admin/member/consignee/getcitys"
                ),
                'items' => function ($province) {
                    return $this->modelArea->getCitys($province);
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['district'] = array(
            'name' => '区/县',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'cascade' => 'city',
                'cascadeAjax' => array(
                    'type' => 'POST',
                    'url' => "admin/member/consignee/getdistricts"
                ),
                'items' => function ($city) {
                    return $this->modelArea->getDistricts($city);
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['address'] = array(
            'name' => '详细地址',
            'data' => array(
                'type' => 'string',
                'length' => 255
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
        $schemas['zipcode'] = array(
            'name' => '邮政编码',
            'data' => array(
                'type' => 'integer',
                'length' => 6
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
        $schemas['telephone'] = array(
            'name' => '电话',
            'data' => array(
                'type' => 'string',
                'length' => 20
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
        $schemas['mobile'] = array(
            'name' => '手机',
            'data' => array(
                'type' => 'string',
                'length' => 20
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
        $schemas['is_default'] = array(
            'name' => '是否默认',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '收货人';
    }

    protected function getModel()
    {
        return $this->modelConsignee;
    }
}

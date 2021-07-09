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

    protected function getRowTools2($tools)
    {
        $tools['exchangeaddress'] = array(
            'title' => '修改省市区地址',
            'action' => 'exchangeaddress',
            // 'is_show' => true,
            'is_show' => function ($row) {
                return true;
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['exchangeaddress'] = array(
            'title' => '修改省市区地址',
            'action' => 'exchangeaddress',
            // 'is_show' => true,
            'is_show' => function ($row) {
                return true;
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="修改省市区地址"})
     * 修改省市区地址
     *
     * @name 修改省市区地址
     */
    public function exchangeaddressAction()
    {
        // http://www.myapplicationmodule.com/admin/member/consignee/exchangeaddress?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelConsignee->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = array();
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );

                $fields['name'] = array(
                    'name' => '收货人',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $fields['province'] = array(
                    'name' => '省份',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => function () {
                            return $this->modelArea->getProvinces();
                        }
                    ),
                );
                $fields['city'] = array(
                    'name' => '城市',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'cascadeSettings' => array(
                            'source' => 'province',
                            'type' => 'POST',
                            'url' => "admin/member/consignee/getcitys"
                        ),
                        'items' => function () {
                            return $this->modelArea->getListByLevel(2);
                        }
                    ),
                );
                $fields['district'] = array(
                    'name' => '区/县',
                    'validation' => array(
                        'required' => 1
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'cascadeSettings' => array(
                            'source' => 'city',
                            'type' => 'POST',
                            'url' => "admin/member/consignee/getdistricts"
                        ),
                        'items' => function () {
                            return $this->modelArea->getListByLevel(3);
                        }
                    ),
                );

                $title = "修改省市区地址";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理  
                $province = trim($this->request->get('province'));
                $city = trim($this->request->get('city'));
                $district = trim($this->request->get('district'));

                if (empty($province) || empty($city) || empty($district)) {
                    return $this->makeJsonError("省市区未指定");
                }
                // 更新
                $updateDataInfo = array(
                    'province' => $province,
                    'city' => $city,
                    'district' => $district
                );
                $this->modelConsignee->update(array('_id' => $id), array('$set' => $updateDataInfo));
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功修改');
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取城市列表"})
     *
     * @name 获取城市列表
     */
    public function getcitysAction()
    {
        // http://www.myapplicationmodule.com/admin/member/consignee/getcitys?province=xxx
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
        // http://www.myapplicationmodule.com/admin/member/consignee/getdistricts?city=xxx
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
                'is_show' => true
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
                'is_show' => true
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
                'is_show' => true
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
                'cascadeSettings' => array(
                    'source' => 'province',
                    'type' => 'POST',
                    'url' => "admin/member/consignee/getcitys"
                ),
                'items' => function () {
                    return $this->modelArea->getListByLevel(2);
                }
            ),
            'list' => array(
                'is_show' => true,
                'items' => function () {
                    return $this->modelArea->getListByLevel(2);
                }
            ),
            'search' => array(
                'is_show' => true
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
                'cascadeSettings' => array(
                    'source' => 'city',
                    'type' => 'POST',
                    'url' => "admin/member/consignee/getdistricts"
                ),
                'items' => function () {
                    return $this->modelArea->getListByLevel(3);
                }
            ),
            'list' => array(
                'is_show' => true,
                'items' => function () {
                    return $this->modelArea->getListByLevel(3);
                }
            ),
            'search' => array(
                'is_show' => true
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
                'is_show' => true
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
                'is_show' => true
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
                'content_type' => 'phone',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
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
                'content_type' => 'phone',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
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
                'is_show' => true
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

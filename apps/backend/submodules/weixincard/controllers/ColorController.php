<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Color;

/**
 * @title({name="颜色"})
 *
 * @name 颜色
 */
class ColorController extends \App\Backend\Controllers\FormController
{

    private $modelColor;

    public function initialize()
    {
        $this->modelColor = new Color();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['getcolors'] = array(
            'title' => '获取颜色列表',
            'action' => 'getcolors',
            'is_show' => function () {
                return true;
            },
            'icon' => '',
        );
        return $tools;
    }

    protected function getSchemas2($schemas)
    {
        $schemas['value'] = array(
            'name' => '颜色值',
            'data' => array(
                'type' => 'string',
                'length' => 20,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'color',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['name'] = array(
            'name' => '颜色名称',
            'data' => array(
                'type' => 'string',
                'length' => 20,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '颜色';
    }

    protected function getModel()
    {
        return $this->modelColor;
    }

    /**
     * @title({name="获取颜色列表"})
     * 从微信公众平台上获取颜色列表到本地的Hook
     *
     * @name 获取颜色列表
     */
    public function getcolorsAction()
    {
        // http://www.myapplicationmodule.com/admin/weixincard/color/getcolors
        try {
            $weixin = $this->getWeixin();
            $ret = $weixin->getCardManager()->getcolors();
            // print_r($ret);
            // die('xxx');
            // Array ( [errcode] => 0 [errmsg] => ok [colors] => Array ( [0] => Array ( [name] => Color010 [value] => #55bd47 ) [1] => Array ( [name] => Color020 [value] => #10ad61 ) [2] => Array ( [name] => Color030 [value] => #35a4de ) [3] => Array ( [name] => Color040 [value] => #3d78da ) [4] => Array ( [name] => Color050 [value] => #9058cb ) [5] => Array ( [name] => Color060 [value] => #de9c33 ) [6] => Array ( [name] => Color070 [value] => #ebac16 ) [7] => Array ( [name] => Color080 [value] => #f9861f ) [8] => Array ( [name] => Color090 [value] => #e75735 ) [9] => Array ( [name] => Color100 [value] => #d54036 ) [10] => Array ( [name] => Color101 [value] => #cf3e36 ) ) )
            if (!empty($ret['errcode'])) {
                throw new \Exception($ret['errmsg'], $ret['errcode']);
            }

            if (!empty($ret['colors'])) {
                $this->modelColor->remove(array());
                foreach ($ret['colors'] as $color) {
                    $this->modelColor->record($color['value'], $color['name']);
                }
            }
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功获取颜色列表');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}

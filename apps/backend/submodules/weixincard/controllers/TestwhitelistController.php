<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Testwhitelist;

/**
 * @title({name="测试白名单"})
 *
 * @name 测试白名单
 */
class TestwhitelistController extends \App\Backend\Controllers\FormController
{

    private $modelTestwhitelist;

    public function initialize()
    {
        $this->modelTestwhitelist = new Testwhitelist();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['set'] = array(
            'title' => '设置测试用户白名单',
            'action' => 'set',
            'is_show' => function () {
                return true;
            },
            'icon' => '',
        );
        return $tools;
    }

    protected function getSchemas2($schemas)
    {
        $schemas['openid'] = array(
            'name' => '测试的openid',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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
        $schemas['username'] = array(
            'name' => '测试的微信昵称',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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
        $schemas['is_set'] = array(
            'name' => '是否已设置',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
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
                'list_type' => '1',
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
        return '测试白名单';
    }

    protected function getModel()
    {
        return $this->modelTestwhitelist;
    }

    /**
     * @title({name="设置测试用户白名单"})
     * 设置测试用户白名单的Hook
     *
     * @name 设置测试用户白名单
     */
    public function setAction()
    {
        // http://www.myapplicationmodule.com.com/admin/weixincard/testwhitelist/set
        try {
            $weixin = $this->getWeixin();

            $whiteList = $this->modelTestwhitelist->getAll();
            $openids = array();
            $usernames = array();
            $ids = array();
            foreach ($whiteList as $item) {
                $ids[] = $item['_id'];
                if (!empty($item['openid'])) {
                    $openids[] = $item['openid'];
                }
                if (!empty($item['username'])) {
                    $usernames[] = $item['username'];
                }
            }
            if (!empty($ids)) {
                if (!empty($openids) || !empty($usernames)) {
                    $ret = $weixin->getCardManager()->testwhitelistSet($openids, $usernames);
                    if (!empty($ret['errcode'])) {
                        throw new \Exception($ret['errmsg'], $ret['errcode']);
                    }
                }
                $this->modelTestwhitelist->updateIsset($ids, true);
            }

            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功设置测试用户白名单');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}

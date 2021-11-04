<?php

namespace App\Backend\Submodules\Exchange\Controllers;

use App\Backend\Submodules\Exchange\Models\Rule;
use App\Backend\Submodules\Prize\Models\Prize;
use App\Backend\Submodules\Points\Models\Category;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="兑换规则"})
 *
 * @name 兑换规则
 */
class RuleController extends \App\Backend\Controllers\FormController
{

    private $modelRule;

    private $modelPrize;

    private $modelCategory;

    private $modelActivity;

    public function initialize()
    {
        $this->modelRule = new Rule();
        $this->modelPrize = new Prize();
        $this->modelCategory = new Category();
        $this->modelActivity = new Activity();

        $this->categoryList = $this->modelCategory->getAll();
        $this->prizeList = $this->modelPrize->getAll();
        $this->activityList = $this->modelActivity->getAll();

        parent::initialize();
    }

    protected function getRowTools2($tools)
    {
        $tools['setcondition'] = array(
            'title' => '设置兑换资格',
            'action' => 'setcondition',
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
        $tools['setcondition'] = array(
            'title' => '设置兑换资格',
            'action' => 'setcondition',
            // 'is_show' => true,
            'is_show' => function ($row) {
                return true;
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="设置兑换资格"})
     * 设置兑换资格
     *
     * @name 设置兑换资格
     */
    public function setconditionAction()
    {
        // http://www.myapplicationmodule.com/admin/exchange/rule/setcondition?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelRule->getInfoById($id);
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

                $other_conditions = empty($row['other_conditions']) ? array() : $row['other_conditions'];
                if (empty($other_conditions)) {
                    $other_conditions = array();
                }
                $medalCodeList = empty($other_conditions['medalCodeList']) ? array() : $other_conditions['medalCodeList'];
                $row['medalCodeList'] = $medalCodeList;

                $fields['medalCodeList'] = array(
                    'name' => '徽章列表',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'checkbox',
                        'is_show' => true,
                        'items' => $this->modelCategory->getAllExcludeParent()
                    ),
                );

                $title = "设置兑换资格";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理
                $medalchkbox = ($this->request->get('medalCodeList'));
                if (empty($medalchkbox)) {
                    return $this->makeJsonError("徽章未设定");
                }

                $categoryList = $this->modelCategory->getAllExcludeParent();
                $medalCodeList = array();
                foreach ($medalchkbox as $medalcode) {
                    if (!empty($medalcode) && array_key_exists($medalcode, $categoryList)) {
                        $medalCodeList[$medalcode] = $categoryList[$medalcode];
                    }
                }
                if (empty($medalCodeList)) {
                    return $this->makeJsonError("徽章未设定");
                }

                $other_conditions = empty($row['other_conditions']) ? array() : $row['other_conditions'];
                if (empty($other_conditions)) {
                    $other_conditions = array();
                }
                $other_conditions['medalCodeList'] = array_keys($medalCodeList);
                $other_conditions['medalNameList'] = array_values($medalCodeList);
                $updateData = array();
                $updateData['other_conditions'] = \App\Common\Utils\Helper::myJsonEncode($other_conditions);

                $this->modelRule->update(array('_id' => $id), array('$set' => $updateData));
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功修改');
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    private $activityList = null;
    private $categoryList = null;
    private $prizeList = null;

    protected function getSchemas2($schemas)
    {
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);

        $schemas['activity_id'] = array(
            'name' => '活动名称',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->activityList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->activityList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->activityList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['prize_id'] = array(
            'name' => '奖品ID',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->prizeList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->prizeList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->prizeList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['allow_start_time'] = array(
            'name' => '开始时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['allow_end_time'] = array(
            'name' => '结束时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 2 - 1)
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['allow_number'] = array(
            'name' => '奖品数量',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['score_category'] = array(
            'name' => '积分分类',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['score'] = array(
            'name' => '积分',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        $categoryList = $this->modelCategory->getAllExcludeParent();
        $schemas['other_conditions'] = array(
            'name' => '兑换资格',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) use ($categoryList) {
                    $row = $column->getRow();
                    //display()方法来通过传入的回调函数来处理当前列的值：
                    return $column->display(function () use ($categoryList, $row) {
                        $other_conditions = empty($row->other_conditions) ? array() : \json_decode($row->other_conditions, true);
                        if (empty($other_conditions)) {
                            $other_conditions = array();
                        }
                        $medalCodeList = empty($other_conditions['medalCodeList']) ? array() : $other_conditions['medalCodeList'];
                        $medalNameList = empty($other_conditions['medalNameList']) ? array() : $other_conditions['medalNameList'];
                        $nameList = array();
                        if (!empty($medalCodeList)) {
                            foreach ($medalCodeList as $code) {
                                if (array_key_exists($code, $categoryList)) {
                                    $nameList[] = $categoryList[$code];
                                } else {
                                    $nameList[] = "code:{$code}的徽章未找到";
                                }
                            }
                        }
                        // die(implode(",", $nameList));
                        return implode(",", $nameList);
                    });
                }
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        $schemas['exchange_quantity'] = array(
            'name' => '已兑换数量',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $settings['validation'] = array(
                        'required' => false
                    );
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    // print_r($settings);
                    // die('xxxxxxxx');
                    return $settings;
                }
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
        $schemas['sort'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        return '兑换规则';
    }

    protected function getModel()
    {
        return $this->modelRule;
    }
}

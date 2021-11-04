<?php

namespace App\Backend\Submodules\Task\Controllers;

use App\Backend\Submodules\Task\Models\Task;
use App\Backend\Submodules\Points\Models\Rule;

/**
 * @title({name="任务"})
 *
 * @name 任务
 */
class TaskController extends \App\Backend\Controllers\FormController
{
    private $modelTask;
    private $modelPointRule;

    protected $actTypeOptions = array();
    public function initialize()
    {
        $this->modelTask = new Task();
        $this->modelPointRule = new Rule();

        $this->actTypeOptions[strval(\App\Member\Services\MemberService::ACT_TYPE_BM)] = '报名';
        $this->actTypeOptions[strval(\App\Member\Services\MemberService::ACT_TYPE_YJ)] = '有介';
        $this->actTypeOptions[strval(\App\Member\Services\MemberService::ACT_TYPE_QD)] = '签到';
        $this->actTypeOptions[strval(\App\Member\Services\MemberService::ACT_TYPE_YDWZ)] = '阅读文章';
        $this->actTypeOptions[strval(\App\Member\Services\MemberService::ACT_TYPE_GKSP)] = '观看视频';

        parent::initialize();
    }

    protected function getFormTools2($tools)
    {
        $tools['setgifts'] = array(
            'title' => '设置任务达成后的奖励',
            'action' => 'setgifts',
            // 'is_show' => true,
            'is_show' => function ($row) {
                return true;
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="设置任务达成后的奖励"})
     * 设置任务达成后的奖励
     *
     * @name 设置任务达成后的奖励
     */
    public function setgiftsAction()
    {
        // http://www.myapplicationmodule.com/admin/task/task/setgifts?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelTask->getInfoById($id);
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
                    'name' => '任务名',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $gifts = empty($row['gifts']) ? array() : (is_array($row['gifts']) ? $row['gifts'] : \json_decode($row['gifts'], true));
                if (empty($gifts)) {
                    $gifts = array();
                }
                $pointRuleList = empty($gifts['pointRuleList']) ? array() : $gifts['pointRuleList'];
                $row['pointRuleList'] = $pointRuleList;

                $fields['pointRuleList'] = array(
                    'name' => '奖励列表',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'checkbox',
                        'is_show' => true,
                        'items' => $this->modelPointRule->getAll(),
                        'readonly' => false,
                    ),
                );

                // $ruleList = RuleModel::getAll();
                // $this->checkbox('pointrulechkbox', '奖励列表')->options($ruleList)->rules('required')->value($pointRuleList)->stacked();

                $title = "设置任务达成后的奖励";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理  
                $pointrulechkbox = ($this->request->get('pointRuleList'));
                if (empty($pointrulechkbox)) {
                    return $this->makeJsonError("奖励未设定");
                }
                $ruleList = $this->modelPointRule->getAll();
                $pointRuleList = array();
                foreach ($pointrulechkbox as $pointruleId) {
                    if (!empty($pointruleId) && array_key_exists($pointruleId, $ruleList)) {
                        $pointRuleList[$pointruleId] = $ruleList[$pointruleId];
                    }
                }
                if (empty($pointRuleList)) {
                    return $this->makeJsonError("奖励未设定");
                }

                $gifts = empty($row['gifts']) ? array() : (is_array($row['gifts']) ? $row['gifts'] : \json_decode($row['gifts'], true));
                if (empty($gifts)) {
                    $gifts = array();
                }
                $gifts['pointRuleList'] = array_keys($pointRuleList);
                $this->modelTask->update(array('_id' => $id), array('$set' => array('gifts' => \App\Common\Utils\Helper::myJsonEncode($gifts))));
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功修改');
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['name'] = array(
            'name' => '任务名称',
            'data' => array(
                'type' => 'string',
                'length' => 50,
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
        $schemas['desc'] = array(
            'name' => '任务简介',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
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
        $schemas['image_url1'] = array(
            'name' => '任务图片1',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['image_url2'] = array(
            'name' => '任务图片2',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['image_url3'] = array(
            'name' => '任务图片3',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['start_time'] = array(
            'name' => '任务开始时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
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
        $schemas['end_time'] = array(
            'name' => '任务结束时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
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
        $schemas['is_actived'] = array(
            'name' => '是否激活',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => true
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
        $schemas['show_order'] = array(
            'name' => '显示顺序',
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
        $schemas['act_type'] = array(
            'name' => '行为类型',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->actTypeOptions
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->actTypeOptions
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'select',
                'items' => $this->actTypeOptions
            ),
            'export' => array(
                'is_show' => true,
                'items' => $this->actTypeOptions
            )
        );
        $schemas['act_num'] = array(
            'name' => '任务达成所需行为数量',
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
        $schemas['gifts'] = array(
            'name' => '任务达成后的奖励',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
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
        $schemas['complete_num'] = array(
            'name' => '完成数量',
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
                'is_show' => false,
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
        return '任务';
    }

    protected function getModel()
    {
        return $this->modelTask;
    }
}

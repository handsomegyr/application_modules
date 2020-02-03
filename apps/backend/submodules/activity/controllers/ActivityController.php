<?php

namespace App\Backend\Submodules\Activity\Controllers;

use App\Backend\Submodules\Activity\Models\Activity;
use App\Backend\Submodules\Activity\Models\Category;

/**
 * @title({name="活动管理"})
 *
 * @name 活动管理
 */
class ActivityController extends \App\Backend\Controllers\FormController
{

    private $modelActivity;

    private $modelCategory;

    public function initialize()
    {
        $this->modelActivity = new Activity();
        $this->modelCategory = new Category();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['exportcsv'] = array(
            'title' => 'Csv导出',
            'is_show' => true,
            'action' => 'exportcsv',
        );
        return $tools;
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();

        $schemas['category'] = array(
            'name' => '所属分类',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelCategory->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'category_name'
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['name'] = array(
            'name' => '活动名称',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => true
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
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);

        $schemas['start_time'] = array(
            'name' => '开始时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['end_time'] = array(
            'name' => '截止时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 2 - 1)
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
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
                'length' => '1',
                'defaultValue' => true
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['is_paused'] = array(
            'name' => '是否暂停',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['config'] = array(
            'name' => '活动配置',
            'data' => array(
                'type' => 'json',
                'length' => '1000'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
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
        return '活动';
    }

    protected function getModel()
    {
        return $this->modelActivity;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $categoryList = $this->modelCategory->getAll();
        foreach ($list['data'] as &$item) {
            $item['category_name'] = isset($categoryList[$item['category']]) ? $categoryList[$item['category']] : '';
            $item['start_time'] = date("Y-m-d H:i:s", $item['start_time']->sec);
            $item['end_time'] = date("Y-m-d H:i:s", $item['end_time']->sec);
        }
        return $list;
    }

    /**
     * @title({name="csv导出"})
     * csv导出
     *
     * @name csv导出
     */
    public function exportcsvAction()
    {
        // http://www.applicationmodule.com/admin/activity/activity/exportcsv
        try {
            $this->view->disable();
            // if ($this->request->isGet()) {
            // die(__DIR__ . '/../../../views/');
            $this->view->setViewsDir(__DIR__ . '/../../../views/');
            $this->view->setVar('title', 'CSV导出');
            $this->view->setVar('modal_id', 'xxx');
            $this->view->setVar('fields', array());
            //ob_start();
            //$this->view->start();
            //$this->view->partial("partials/actions/modal");
            //$this->view->finish();            
            //$content = ob_get_content();
            //ob_end_clean();

            // $this->view->start();
            // //Shows recent posts view (app/views/posts/recent.phtml)
            // $this->view->render('partials', 'modal');
            // $this->view->finish();

            // // Printing views output
            // $content = $this->view->getContent();

            $data = array('title' => 'CSV导出', 'modal_id' => 'xxx', 'fields' => array());
            $data['content'] = $this->view->getRender('partials', 'modal', $data);
            $this->makeJsonResult($data);
            // } else {
            //     $this->view->disable();
            //     $this->makeJsonResult();
            // }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}

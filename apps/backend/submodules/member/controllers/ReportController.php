<?php
namespace Webcms\Backend\Controllers\Member;

use Webcms\Backend\Models\Member\Report;

/**
 * @title({name="举报管理"})
 *
 * @name 举报管理
 */
class ReportController extends \Webcms\Backend\Controllers\FormController
{

    private $typeDatas = \Webcms\Member\Models\Report::TYPEDATAS;

    private $modelReport;

    public function initialize()
    {
        $this->modelReport = new Report();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['from_user_id'] = array(
            'name' => '举报用户ID',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['to_user_id'] = array(
            'name' => '被举报用户ID',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['type'] = array(
            'name' => '举报类型',
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
                'items' => $this->typeDatas
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['content'] = array(
            'name' => '举报内容',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['report_time'] = array(
            'name' => '举报时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '举报';
    }

    protected function getModel()
    {
        return $this->modelReport;
    }

    protected function getList4Show(\Webcms\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            $item['type'] = $this->typeDatas[$item['type']]['name'];
            $item['report_time'] = date('Y-m-d H:i:s', $item['report_time']->sec);
        }
        
        return $list;
    }
}
<?php
namespace App\Backend\Submodules\Order\Controllers;

use App\Backend\Models\Order\Statistics;

/**
 * @title({name="订单统计管理"})
 *
 * @name 订单统计管理
 */
class StatisticsController extends \App\Backend\Controllers\FormController
{

    private $modelStatistics = NULL;

    public function initialize()
    {
        $this->modelStatistics = new Statistics();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['order_amount'] = array(
            'name' => '订单总金额',
            'data' => array(
                'type' => 'decimal',
                'length' => 10
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'currency',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_amount'] = array(
            'name' => '商品总金额',
            'data' => array(
                'type' => 'decimal',
                'length' => 10
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'currency',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['rcb_amount'] = array(
            'name' => '充值卡总金额',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['pd_amount'] = array(
            'name' => '预存款总金额',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['points_amount'] = array(
            'name' => '福分总金额',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['shipping_fee'] = array(
            'name' => '运费总金额',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['refund_amount'] = array(
            'name' => '退款金额',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['pay_amount'] = array(
            'name' => '支付总金额',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['success_count'] = array(
            'name' => '支付数量',
            'data' => array(
                'type' => 'integer',
                'length' => 11
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
        
        $schemas['failure_count'] = array(
            'name' => '失败数量',
            'data' => array(
                'type' => 'integer',
                'length' => 11
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '订单统计';
    }

    protected function getModel()
    {
        return $this->modelStatistics;
    }
}
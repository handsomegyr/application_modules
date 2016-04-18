<?php
namespace App\Backend\Submodules\Prize\Controllers;

use App\Backend\Submodules\Prize\Models\Prize;
use App\Backend\Submodules\Prize\Models\Category;

/**
 * @title({name="奖品管理"})
 *
 * @name 奖品管理
 */
class PrizeController extends \App\Backend\Controllers\FormController
{

    private $modelPrize;

    private $modelCategory;

    public function initialize()
    {
        $this->modelPrize = new Prize();
        $this->modelCategory = new Category();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['prize_code'] = array(
            'name' => '奖品编码',
            'data' => array(
                'type' => 'string',
                'length' => '24'
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
        
        $schemas['prize_name'] = array(
            'name' => '奖品名称',
            'data' => array(
                'type' => 'string',
                'length' => '30'
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
        
        $schemas['category'] = array(
            'name' => '奖品类别',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelCategory->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'category_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // $schemas['desc'] = array(
        // 'name' => '奖品描述',
        // 'data' => array(
        // 'type' => 'string',
        // 'length' => '100'
        // ),
        // 'validation' => array(
        // 'required' => false
        // ),
        // 'form' => array(
        // 'input_type' => 'textarea',
        // 'is_show' => true
        // ),
        // 'list' => array(
        // 'is_show' => true
        // ),
        // 'search' => array(
        // 'is_show' => false
        // )
        // );
        
        $schemas['is_virtual'] = array(
            'name' => '是否虚拟奖品',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
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
                'is_show' => false
            )
        );
        
        $schemas['virtual_currency'] = array(
            'name' => '虚拟货币',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['is_need_virtual_code'] = array(
            'name' => '是否发放虚拟奖品',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
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
                'is_show' => false
            )
        );
                
        $schemas['is_valid'] = array(
            'name' => '是否立即生效',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
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
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '奖品';
    }

    protected function getModel()
    {
        return $this->modelPrize;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $categoryList = $this->modelCategory->getAll();
        foreach ($list['data'] as &$item) {
            $item['category_name'] = isset($categoryList[$item['category']]) ? $categoryList[$item['category']] : "--";
        }
        return $list;
    }
}
<?php
namespace App\Backend\Submodules\System\Models;

use App\Backend\Models\Input;

class Enum extends \App\Common\Models\System\Enum
{
    use \App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'show_order' => 1,
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array(
            'is_show' => true
        );
        return $query;
    }

    /**
     * 获取商品菜单列表信息
     *
     * @param Input $input            
     * @return array
     */
    public function getList(Input $input)
    {
        // 分页查询
        $list = $this->findAll($input->getQuery(), $input->getSort());
        
        $enumList = array();
        if (! empty($list)) {
            foreach ($list as $item) {
                $pkey = "p:" . (empty($item['pid']) ? "0" : $item['pid']);
                $key = ($item['_id']);
                $enumList[$pkey][$key] = $item;
            }
        }
        if (! empty($enumList["p:0"])) {
            $input->setRecordCount(count($enumList["p:0"]));
            $filter = $input->getFilter();
            $enumList["p:0"] = array_slice($enumList["p:0"], $filter['start'], min($filter['record_count'], $filter['page_size']));
            $datas = $this->recursiveGet($enumList, "0", 0);
        } else {
            $input->setRecordCount(0);
            $filter = $input->getFilter();
            $datas = array();
        }
        
        return array(
            'data' => $datas,
            'filter' => $filter,
            'page_count' => $filter['page_count'],
            'record_count' => $filter['record_count']
        );
    }

    /**
     * 递归方式获取信息
     *
     * @param array $enumList            
     * @param string $pkey            
     * @param number $level            
     * @return Ambigous <multitype:, multitype:number >
     */
    private function recursiveGet($enumList, $pkey, $level = 0)
    {
        $list = array();
        $pkey = "p:" . $pkey;
        if (! empty($enumList[$pkey])) {
            foreach ($enumList[$pkey] as $key => $item) {
                $item['level'] = $level;
                $item['enum_id'] = $item['_id'];
                $item['has_children'] = empty($enumList["p:" . $item['enum_id']]);
                
                $list[] = $item;
                $list2 = $this->recursiveGet($enumList, $key, $level + 1);
                if (! empty($list2)) {
                    $list = array_merge($list, $list2);
                }
            }
        }
        return $list;
    }

    public function getList4Tree($enum_id = "")
    {
        $input = new Input();
        if (! empty($enum_id)) {
            $input->id = $enum_id;
        }
        $input->sort_by = "show_order";
        $input->sort_order = "DESC";
        $input->page_size = 3000;
        
        $ret = $this->getList($input);
        $datas = array();
        
        foreach ($ret["data"] as $var) {
            $text = "";
            if ($var['level'] > 0) {
                $text .= str_repeat('&nbsp;', $var['level'] * 4);
            }
            $text .= $var['name'];
            $datas[$var['enum_id']] = $text;
        }
        return $datas;
    }
}
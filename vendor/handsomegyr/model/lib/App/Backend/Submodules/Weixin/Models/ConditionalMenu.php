<?php
namespace App\Backend\Submodules\Weixin\Models;

use App\Backend\Models\Input;

class ConditionalMenu extends \App\Common\Models\Weixin\ConditionalMenu
{
    
    use \App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'priority' => 1,
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 获取菜单列表信息
     *
     * @param Input $input            
     * @return array
     */
    public function getList(Input $input)
    {
        // 分页查询
        $list = $this->findAll($input->getQuery(), $input->getSort());
        $menuList = array();
        if (! empty($list)) {
            foreach ($list as $item) {
                $pkey = "p:" . (empty($item['parent']) ? "0" : $item['parent']);
                $key = ($item['_id']);
                $menuList[$pkey][$key] = $item;
            }
        }
        if (! empty($menuList["p:0"])) {
            $input->setRecordCount(count($menuList["p:0"]));
            $filter = $input->getFilter();
            $menuList["p:0"] = array_slice($menuList["p:0"], $filter['start'], min($filter['record_count'], $filter['page_size']));
            $datas = $this->recursiveGet($menuList, "0", 0);
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
     * @param array $menuList            
     * @param string $pkey            
     * @param number $level            
     * @return Ambigous <multitype:, multitype:number >
     */
    private function recursiveGet($menuList, $pkey, $level = 0)
    {
        $list = array();
        $pkey = "p:" . $pkey;
        if (! empty($menuList[$pkey])) {
            foreach ($menuList[$pkey] as $key => $item) {
                $item['level'] = $level;
                $item['menu_id'] = $item['_id'];
                $item['has_children'] = empty($menuList["p:" . $item['menu_id']]);
                
                $list[] = $item;
                $list2 = $this->recursiveGet($menuList, $key, $level + 1);
                if (! empty($list2)) {
                    $list = array_merge($list, $list2);
                }
            }
        }
        return $list;
    }

    public function getList4Tree($menu_id = "")
    {
        $input = new Input();
        if (! empty($menu_id)) {
            $input->id = $menu_id;
        }
        $input->sort_by = "priority";
        $input->sort_order = "DESC";
        $input->page_size = 1000;
        
        $ret = $this->getList($input);
        $datas = array();
        
        foreach ($ret["data"] as $var) {
            $text = "";
            if ($var['level'] > 0) {
                $text .= str_repeat('&nbsp;', $var['level'] * 4);
            }
            $text .= $var['name'];
            $datas[$var['menu_id']] = $text;
        }
        return $datas;
    }
}

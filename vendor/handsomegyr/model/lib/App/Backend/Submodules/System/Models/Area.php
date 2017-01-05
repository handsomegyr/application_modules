<?php
namespace App\Backend\Submodules\System\Models;

use App\Backend\Models\Input;

class Area extends \App\Common\Models\System\Area
{
    use \App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'level' => 1,
            'parent_code' => 1,
            'name' => 1,
            'code' => 1
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
     * 获取商品菜单列表信息
     *
     * @param Input $input            
     * @return array
     */
    public function getList(Input $input)
    {
        // 分页查询
        $list = $this->findAll($input->getQuery(), $input->getSort());
        $areaList = array();
        if (! empty($list)) {
            foreach ($list as $item) {
                $pkey = "p:" . (empty($item['parent_code']) ? "0" : $item['parent_code']);
                $key = "cur:" . ($item['code']);
                $areaList[$pkey][$key] = $item;
            }
        }
        
        if (! empty($areaList["p:0"])) {
            $input->setRecordCount(count($areaList["p:0"]));
            $filter = $input->getFilter();
            $areaList["p:0"] = array_slice($areaList["p:0"], $filter['start'], min($filter['record_count'], $filter['page_size']));
            $datas = $this->recursiveGet($areaList, "0", 1);
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
     * @param array $areaList            
     * @param string $pkey            
     * @param number $level            
     * @return Ambigous <multitype:, multitype:number >
     */
    private function recursiveGet($areaList, $pkey, $level = 1)
    {
        $list = array();
        $pkey = "p:" . $pkey;
        if (! empty($areaList[$pkey])) {
            foreach ($areaList[$pkey] as $key => $item) {
                // $item['level'] = $level;
                $item['area_code'] = $item['code'];
                $item['has_children'] = empty($areaList["p:" . $item['area_code']]);
                
                $list[] = $item;
                $list2 = $this->recursiveGet($areaList, ltrim($key, 'cur:'), $level + 1);
                if (! empty($list2)) {
                    $list = array_merge($list, $list2);
                }
            }
        }
        return $list;
    }

    public function getList4Tree($area_code = 0)
    {
        $input = new Input();
        if (! empty($area_code)) {
            $input->code = $area_code;
        }
        $input->sort_by = "parent_code";
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
            $datas[$var['area_code']] = $text;
        }
        return $datas;
    }
}
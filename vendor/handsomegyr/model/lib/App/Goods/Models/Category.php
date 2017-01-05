<?php
namespace App\Goods\Models;

class Category extends \App\Common\Models\Goods\Category
{

    /**
     * 默认排序方式
     *
     * @param number $dir            
     * @return array
     */
    public function getDefaultSort($dir = 1)
    {
        $sort = array(
            'parent_id' => $dir,
            'sort' => $dir,
            '_id' => $dir
        );
        
        return $sort;
    }

    /**
     * 默认查询条件
     *
     * @return array
     */
    public function getDefaultQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 根据某种条件获取列表
     *
     * @param array $query            
     * @param array $sort            
     * @param array $fields            
     * @return array
     */
    public function getList(array $query = array(), array $sort = array(), array $fields = array())
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $list = $this->findAll($query, $sort, $fields);
            if (empty($list)) {
                $cache->save($key, $list, 60 * 60 * 24); // 24小时
            }
        }
        return $list;
    }

    /**
     * 根据某种条件获取分类扩展列表
     *
     * @param array $query            
     * @param array $fields            
     * @return array
     */
    public function getExtendList(array $query = array(), array $sort = array(), array $fields = array())
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort);
        $cache = $this->getDI()->get("cache");
        $list = false; // $cache->get($key);
        if (empty($list)) {
            $list = $this->getList($query, $sort, $fields);
            if (! empty($list)) {
                $parents = array();
                $childrens = array();
                // 获取父分类列表和子分类列表
                foreach ($list as $v) {
                    $id = $v['_id'];
                    // 父分类
                    $parents[$id] = $v['parent_id'];
                    // 子分类列表
                    $pid = empty($v['parent_id']) ? '--' : $v['parent_id'];
                    $childrens[$pid][] = $id;
                }
                
                // 构造gc_id,gc_name,level,pid,ppid,cids,ccids等字段信息
                foreach ($list as &$v) {
                    $v['gc_id'] = $v['_id']; // 商品分类ID
                    $v['gc_name'] = $v['name']; // 商品分类名
                    $v['pid'] = $v['parent_id']; // 父分类
                    $v['level'] = empty($v['pid']) ? 1 : 2; // 层级
                    if (empty($v['pid'])) {
                        $v['ppid'] = ''; // 祖父分类
                    } else {
                        $v['ppid'] = $parents[$v['pid']]; // 祖父分类
                    }
                    if (! empty($v['ppid'])) {
                        $v['level'] ++;
                    }
                    $v['cids'] = empty($childrens[$v['gc_id']]) ? array() : array_unique($childrens[$v['gc_id']]); // 子分类列表
                    $v['ccids'] = array(); // 子子分类列表
                    foreach ($v['cids'] as $cid) {
                        $ccids = empty($childrens[$cid]) ? array() : array_unique($childrens[$cid]); // 子子分类列表
                        $v['ccids'] = array_merge($v['ccids'], $ccids);
                    }
                }
                
                $cache->save($key, $list, 60 * 60 * 24); // 24小时
            }
        }
        return $list;
    }

    /**
     * 获取顶级的分类列表信息
     *
     * @return array
     */
    public function getTopLevelExtendList()
    {
        $categoryList = $this->getExtendList();
        $list = array();
        if (! empty($categoryList)) {
            foreach ($categoryList as $category) {
                if ($category['level'] == 1) {
                    $list[$category['gc_id']] = $category;
                }
            }
        }
        return $list;
    }

    /**
     * 根据某种条件，获取分类树
     *
     * @param array $query            
     * @param array $fields            
     * @return array
     */
    public function getTree(array $query = array(), array $sort = array(), array $fields = array())
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort);
        $cache = $this->getDI()->get("cache");
        $tree = $cache->get($key);
        if (empty($tree)) {
            $categoryList = $this->getExtendList($query, $sort, $fields);
            $tree = array();
            if (! empty($categoryList)) {
                foreach ($categoryList as $category) {
                    if ($category['level'] == 1) {
                        $tree[$category['gc_id']] = $category;
                    } elseif ($category['level'] == 2) {
                        if (array_key_exists($category['pid'], $tree)) {
                            $tree[$category['pid']]['level2'][$category['gc_id']] = $category;
                        }
                    } elseif ($category['level'] == 3) {
                        if (array_key_exists($category['ppid'], $tree)) {
                            $tree[$category['ppid']]['level2'][$category['pid']]['level3'][$category['gc_id']] = $category;
                        }
                    }
                }
                $cache->save($key, $tree, 60 * 60 * 24); // 24小时
            }
        }
        return $tree;
    }

    /**
     * 根据给定分类id列表,获取他们的祖父分类对应的分类树
     *
     * @param array $ids            
     * @return array
     */
    public function getTreeByIds(array $ids = array())
    {
        $tree = array();
        if (! empty($ids)) {
            // 获取所有的分类列表
            $categoryList = $this->getExtendList();
            $ppids = array();
            if (! empty($categoryList)) {
                foreach ($ids as $id) {
                    if (isset($categoryList[$id])) {
                        $ppids[] = $categoryList[$id]['ppid'];
                    }
                }
            }
        }
        if (! empty($ppids)) {
            // 获取分类树
            $categoryTree = $this->getTree();
            if (! empty($categoryTree)) {
                foreach ($ppids as $id) {
                    if (isset($categoryTree[$id])) {
                        $tree[] = $categoryTree[$id];
                    }
                }
            }
        }
        return tree;
    }
}
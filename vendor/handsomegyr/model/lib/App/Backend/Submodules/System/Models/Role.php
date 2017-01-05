<?php
namespace App\Backend\Submodules\System\Models;

class Role extends \App\Common\Models\System\Role
{
    use \App\Backend\Models\Base;

    public function checkName($id, $name)
    {
        /* 判断是否已经存在 */
        $query = array();
        $query['name'] = urldecode($name);
        if (! empty($id)) {
            $query['_id'] = array(
                '$ne' => $id
            );
        }
        $num = $this->count($query);
        if ($num > 0) {
            throw new \Exception(sprintf("角色名已存在", stripslashes($name)), 1);
        }
    }

    public function checkAlias($id, $alias)
    {
        /* 判断是否已经存在 */
        $query = array();
        $query['alias'] = $alias;
        if (! empty($id)) {
            $query['_id'] = array(
                '$ne' => $id
            );
        }
        $num = $this->count($query);
        if ($num > 0) {
            throw new \Exception(sprintf("角色别名已存在", stripslashes($alias)), 1);
        }
    }

    /**
     * 获取所有活动列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['_id']] = $item['name'];
        }
        return $list;
    }
}
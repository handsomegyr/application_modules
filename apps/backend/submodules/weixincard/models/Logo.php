<?php
namespace App\Backend\Submodules\Weixincard\Models;

class Logo extends \App\Common\Models\Weixincard\Logo
{
    
    use \App\Backend\Models\Base;

    /**
     * 获取列表信息
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        return $ret;
    }

    /**
     * 更新是否上传
     *
     * @param string $id            
     * @param boolean $is_uploaded            
     */
    public function updateIsUploaded($id, $logo_url, $is_uploaded = true)
    {
        $query = array();
        $query['_id'] = myMongoId($id);
        $data = array();
        $data['logo_url'] = $logo_url;
        $data['is_uploaded'] = $is_uploaded;
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 获取最新的商户LOGO信息
     *
     * @return array
     */
    public function getLatestLogo()
    {
        $query = $this->getQuery();
        $query['is_uploaded'] = true;
        $sort = $this->getDefaultSort();
        $list = $this->find($query, $sort, 0, 1);
        if (! empty($list['datas'])) {
            return $list['datas'][0];
        }
        return array();
    }
}
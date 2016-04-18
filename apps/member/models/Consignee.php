<?php
namespace App\Member\Models;

class Consignee extends \App\Common\Models\Member\Consignee
{

    public function getDefaultSort()
    {
        $sort = array();
        $sort['is_default'] = - 1;
        return $sort;
    }

    /**
     * 根据会员ID列表获取列表信息
     *
     * @param string $member_id            
     * @param boolean $is_default            
     * @return array
     */
    public function getListByMemberId($member_id, $is_default = false)
    {
        $query = array(
            'member_id' => $member_id
        );
        $sort = $this->getDefaultSort();
        if (! empty($is_default)) {
            $query['is_default'] = $is_default;
        }
        $list = $this->findAll($query, $sort);
        $ret = array();
        if (! empty($list)) {
            foreach ($list as $item) {
                $ret[$item['_id']] = $item;
            }
        }
        return $ret;
    }

    /**
     * 新增和修改处理
     *
     * @param string $member_id            
     * @param string $member_id            
     * @param string $name            
     * @param number $province            
     * @param number $city            
     * @param number $district            
     * @param string $address            
     * @param string $zipcode            
     * @param string $telephone            
     * @param string $mobile            
     * @param boolean $is_default            
     * @return array
     */
    public function insertOrUpdate($id, $member_id, $name, $province, $city, $district, $address, $zipcode, $telephone, $mobile, $is_default)
    {
        $data = array();
        $data['name'] = $name;
        $data['member_id'] = $member_id;
        $data['province'] = $province;
        $data['city'] = $city;
        $data['district'] = $district;
        $data['address'] = $address;
        $data['zipcode'] = $zipcode;
        $data['telephone'] = $telephone;
        $data['mobile'] = $mobile;
        $data['is_default'] = $is_default;
        if (empty($id)) {
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $id,
                'member_id' => $member_id
            );
            $this->update($query, array(
                '$set' => $data
            ));
        }
    }

    /**
     * 设置默认地址
     *
     * @param string $id            
     */
    public function setDefault($id)
    {
        $query = array(
            '_id' => array(
                '$ne' => $id
            )
        );
        $data = array();
        $data['is_default'] = false;
        $this->update($query, array(
            '$set' => $data
        ));
        
        $query = array(
            '_id' => $id
        );
        $data = array();
        $data['is_default'] = true;
        $this->update($query, array(
            '$set' => $data
        ));
    }
}
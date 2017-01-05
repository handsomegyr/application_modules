<?php
namespace App\Prize\Models;

class Prize extends \App\Common\Models\Prize\Prize
{

    public function getInfoByCode($prizeCode)
    {
        $arrayPrize = $this->findOne(array(
            'prize_code' => $prizeCode
        ));
        return $arrayPrize;
    }

    public function getAll()
    {
        $arrayTmp = $this->findAll(array());
        $arrayReturn = array();
        foreach ($arrayTmp as $key => $val) {
            $arrayReturn[$val['prize_code']] = $val;
        }
        return $arrayReturn;
    }

    public function getPrizeInfo($prize_id)
    {
        return $this->findOne(array(
            '_id' => $prize_id
        ));
    }
    
    /**
     * 创建一个奖品
     *
     * @param string $prize_code
     * @param string $prize_name
     * @param boolean $is_need_virtual_code
     * @param boolean $is_valid
     * @param number $category
     * @param boolean $is_virtual
     * @param number $virtual_currency
     */
    public function create($prize_code, $prize_name, $is_need_virtual_code = false, $is_valid = true, $category = 0, $is_virtual = false, $virtual_currency = 0)
    {
        $data = array();
        $data['prize_code'] = $prize_code;
        $data['prize_name'] = $prize_name;
        $data['is_need_virtual_code'] = $is_need_virtual_code;
        $data['is_valid'] = $is_valid;
        $data['category'] = $category;
        $data['is_virtual'] = $is_virtual;
        $data['virtual_currency'] = $virtual_currency;
        return $this->insert($data);
    }
}
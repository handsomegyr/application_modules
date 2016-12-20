<?php
namespace App\Backend\Submodules\Weixincard\Models;

class CodeDeposit extends \App\Common\Models\Weixincard\CodeDeposit
{
    
    use \App\Backend\Models\Base;

    private $_weixin;

    /**
     * 设置微信对象
     */
    public function setWeixin(\Weixin\Client $weixin)
    {
        $this->_weixin = $weixin;
    }

    public function depositeCode($card_id)
    {
        $query = array();
        $query['card_id'] = $card_id;
        $query['is_deposited'] = false;
        $defaultQuery = $this->getQuery();
        $query = array_merge($query, $defaultQuery);
        
        $limit = 100;
        $sort = $this->getDefaultSort();
        $list = $this->find($query, $sort, 0, $limit);
        if (! empty($list['datas'])) {
            $cardManager = $this->_weixin->getCardManager();
            $codes = array();
            $ids = array();
            foreach ($list['datas'] as $value) {
                $codes[] = $value['card_code'];
                $ids[] = $value['_id'];
            }
            $rst = $cardManager->codeDeposit($card_id, $codes);
            // $rst = $cardManager->codeCheck($card_id, $codes);
            if (! empty($rst['errcode'])) {
                $this->updateIsDeposited($ids, false, $rst);
                // 如果有异常，会在errcode 和errmsg 描述出来。
                throw new \Exception($rst['errmsg'], $rst['errcode']);
            } else {
                $this->updateIsDeposited($ids, true);
            }
        }
    }

    /**
     * 更新导入信息
     *
     * @param array $id            
     * @param number $is_deposited            
     */
    public function updateIsDeposited(array $ids, $is_deposited = true, array $demo = array('memo'=>''))
    {
        $query = array();
        $query['_id'] = array(
            '$in' => $ids
        );
        $data = array();
        $data['is_deposited'] = ($is_deposited);
        $data['memo'] = $demo;
        $this->update($query, array(
            '$set' => $data
        ));
    }
}
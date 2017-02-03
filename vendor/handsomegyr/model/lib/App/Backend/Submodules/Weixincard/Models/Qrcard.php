<?php
namespace App\Backend\Submodules\Weixincard\Models;

class Qrcard extends \App\Common\Models\Weixincard\Qrcard
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

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
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
     * 根据ID获取信息
     *
     * @param string $id            
     * @return array
     */
    public function getInfoById($id)
    {
        $query = array(
            '_id' => myMongoId($id)
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据CardId获取信息
     *
     * @param string $code            
     * @return array
     */
    public function getInfoByCardId($card_id)
    {
        $query = array(
            'card_id' => $card_id
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据CardId和Code获取信息
     *
     * @param string $code            
     * @return array
     */
    public function getInfoByCardIdWithCode($card_id, $code)
    {
        $query = array(
            'card_id' => $card_id
        );
        if (! empty($code)) {
            $query['code'] = $code;
        }
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据Code获取信息
     *
     * @param string $code            
     * @return array
     */
    public function getInfoByCode($code)
    {
        $query = array(
            'code' => $code
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 获取列表信息
     *
     * @param string $pid            
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $list = $this->findAll($query, $sort);
        return $list;
    }

    /**
     * 获取2维码图片
     *
     * @param array $card            
     * @param string $ticket            
     * @param string $url            
     * @param string $qrcodeUrl            
     * @param string $show_qrcode_url            
     * @param array $memo            
     */
    public function recordTicket(array $card, $ticket, $url, $qrcodeUrl, $show_qrcode_url, array $memo = array('memo'=>''))
    {
        $query = array();
        $query['_id'] = $card['_id'];
        
        $data = array();
        $data['ticket'] = $ticket;
        $data['url'] = $url;
        $data['qrcodeUrl'] = $qrcodeUrl;
        $data['show_qrcode_url'] = $show_qrcode_url;
        
        $data['ticket_time'] = new \MongoDate();
        $data['is_created'] = true;
        $data['memo'] = $memo;
        $this->update($query, array(
            '$set' => $data
        ));
    }

    public function create4Weixin(array $card)
    {
        $card_id = empty($card['card_id']) ? "" : trim($card['card_id']);
        $code = empty($card['code']) ? "" : trim($card['code']);
        $openid = empty($card['openid']) ? "" : trim($card['openid']);
        $expire_seconds = empty($card['expire_seconds']) ? 0 : intval($card['expire_seconds']);
        $is_unique_code = empty($card['is_unique_code']) ? false : true;
        $balance = empty($card['balance']) ? 0 : $card['balance'];
        $outer_id = empty($card['outer_id']) ? 0 : $card['outer_id'];
        
        $ticketInfo = $this->_weixin->getCardManager()->qrcodeCreate($card_id, $code, $openid, $expire_seconds, $is_unique_code, $balance, $outer_id);
        if (! empty($ticketInfo['errcode'])) {
            throw new \Exception($ticketInfo['errmsg'], $ticketInfo['errcode']);
        }
        // Array ( [errcode] => 0 [errmsg] => ok [ticket] => gQEj8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL3drd1VOUVBtcUt6UkxGajlVR2RGAAIEHWuBVAMEAAAAAA== [url] => http://weixin.qq.com/q/wkwUNQPmqKzRLFj9UGdF [show_qrcode_url] => http://weixin.qq.com/q/wkwUNQPmqKzRLFj9UGdF )
        $ticket = ($ticketInfo['ticket']);
        $url = ($ticketInfo['url']);
        // $qrcodeUrl = $this->_weixin->getQrcodeManager()->getQrcodeUrl(urlencode($ticket));
        $qrcodeUrl = $show_qrcode_url = ($ticketInfo['show_qrcode_url']);
        
        // 记录
        $memo = array(
            'weixinQrcodeCreate' => $ticketInfo
        );
        $this->recordTicket($card, $ticket, $url, $qrcodeUrl, $show_qrcode_url, $memo);
    }
}
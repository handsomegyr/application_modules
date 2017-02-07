<?php
namespace App\Weixin\Models;

class Reply extends \App\Common\Models\Weixin\Reply
{

    public $HOST_URL = '/';

    private $_weixin;

    const MULTI = 1;

    const MUSIC = 2;

    const TEXT = 3;

    const VOICE = 4;

    const VIDEO = 5;

    const IMAGE = 6;

    public function setWeixinInstance(\Weixin\Client $weixin)
    {
        $this->_weixin = $weixin;
    }

    public function answer($match)
    {
        $replys = $this->getReplyDetail($match);
        if (empty($replys)) {
            return false;
        }
        
        switch ($match['reply_type']) {
            case self::MULTI:
                $articles = array();
                foreach ($replys as $index => $reply) {
                    array_push($articles, array(
                        'title' => $reply['title'],
                        'description' => $reply['description'],
                        'picurl' => $index == 0 ? $this->getImagePath($this->HOST_URL, $reply['picture']) : $this->getImagePath($this->HOST_URL, $reply['icon']),
                        'url' => ! empty($reply['url']) ? $reply['url'] : (isset($reply['page']) ? $this->HOST_URL . 'weixin/page/index/id/' . $reply['page'] : '')
                    ));
                }
                
                return $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyGraphText($articles);
                break;
            case self::MUSIC:
                return $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyMusic($replys[0]['title'], $replys[0]['description'], $replys[0]['music']);
                break;
            case self::TEXT:
                return $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyText($replys[0]['description']);
                break;
            case self::VOICE:
                $media_id = $this->getMediaId('voice', $replys[0]);
                return $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyVoice($media_id);
                break;
            case self::VIDEO:
                $media_id = $this->getMediaId('video', $replys[0]);
                return $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyVideo($replys[0]['title'], $replys[0]['description'], $media_id);
                break;
            case self::IMAGE:
                $media_id = $this->getMediaId('image', $replys[0]);
                return $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyImage($media_id);
                break;
        }
    }

    private function getMediaId($type, $reply)
    {
        $created_at = 0;
        if (isset($reply[$type . '_media_result']['created_at'])) {
            $created_at = $reply[$type . '_media_result']['created_at'];
            $media_result = $reply[$type . '_media_result'];
        }
        
        if ($created_at + 24 * 3600 * 3 < time()) {
            $file = $this->getImagePath($this->HOST_URL, $reply[$type]);
            $media_result = $this->_weixin->getMediaManager()->upload($type, $file);
            $this->update(array(
                '_id' => $reply['_id']
            ), array(
                '$set' => array(
                    $type . '_media_result' => json_encode($media_result)
                )
            ));
        }
        return $media_result['media_id'];
    }

    /**
     * 获取指定回复内容的回复内容
     *
     * @param array $match            
     * @return array
     */
    public function getReplyDetail($match)
    {
        if (isset($match['reply_ids'])) {
            $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__, $match['reply_ids']);
            $cache = $this->getDI()->get('cache'); // Zend_Registry::get('cache');
            $rst = $cache->get($cacheKey);
            if (empty($rst)) {
                $rst = $this->findAll(array(
                    'reply_type' => $match['reply_type'],
                    '_id' => array(
                        '$in' => $match['reply_ids']
                    )
                ), array(
                    'priority' => - 1,
                    '_id' => - 1
                ));
                $expire_time = 300; // 5分钟
                $cache->save($cacheKey, $rst, $expire_time);
            }
            return $rst;
        }
        return false;
    }
}
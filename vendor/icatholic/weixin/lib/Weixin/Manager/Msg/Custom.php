<?php
namespace Weixin\Manager\Msg;

use Weixin\Client;

/**
 * 发送消息-----发送客服消息接口
 * 当用户主动发消息给公众号的时候，
 * 微信将会把消息数据推送给开发者，
 * 开发者在一段时间内（目前为48小时）可以调用客服消息接口，
 * 通过POST一个JSON数据包来发送消息给普通用户，
 * 在48小时内不限制发送次数。
 * 此接口主要用于客服等有人工消息处理环节的功能，
 * 方便开发者为用户提供更加优质的服务。
 *
 * @author guoyongrong <handsomegyr@gmail.com>
 * @author young <youngyang@icatholic.net.cn>
 */
class Custom
{

    private $_client;

    private $_length = 140;

    private $_kf_account = "";

    public function __construct(Client $client)
    {
        $this->_client = $client;
    }

    /**
     * 获取文字长度
     *
     * @return number
     */
    public function getLength()
    {
        return $this->_length;
    }

    /**
     * 设定图文消息的最大显示文字长度，超过省略
     *
     * @return number
     */
    public function setLength()
    {
        return $this->_length;
    }

    /**
     * 设定客服帐号
     *
     * @return string
     */
    public function setKfAccount($kf_account)
    {
        return $this->_kf_account;
    }

    /**
     *
     * @param array $params            
     * @throws Exception
     * @return array
     */
    public function send(array $params)
    {
        if (! empty($this->_kf_account)) {
            // 如果需要以某个客服帐号来发消息（在微信6.0.2及以上版本中显示自定义头像），则需在JSON数据包的后半部分加入customservice参数
            $params['customservice'] = array(
                "kf_account" => $this->_kf_account
            );
        }
        $rst = $this->_client->getRequest()->post('message/custom/send', $params);
        return $this->_client->rst($rst);
    }

    /**
     * 发送文本消息
     *
     * @param string $toUser            
     * @param string $content            
     * @return string
     */
    public function sendText($toUser, $content)
    {
        $ret = array();
        $ret['touser'] = $toUser;
        $ret['msgtype'] = 'text';
        $ret['text']['content'] = $content;
        return $this->send($ret);
    }

    /**
     * 发送图片消息
     *
     * @param string $toUser            
     * @param string $media_id            
     * @return string
     */
    public function sendImage($toUser, $media_id)
    {
        $ret = array();
        $ret['touser'] = $toUser;
        $ret['msgtype'] = 'image';
        $ret['image']['media_id'] = $media_id;
        return $this->send($ret);
    }

    /**
     * 发送语音消息
     *
     * @param string $toUser            
     * @param string $media_id            
     * @return string
     */
    public function sendVoice($toUser, $media_id)
    {
        $ret = array();
        $ret['touser'] = $toUser;
        $ret['msgtype'] = 'voice';
        $ret['voice']['media_id'] = $media_id;
        return $this->send($ret);
    }

    /**
     * 发送视频消息
     *
     * @param string $toUser            
     * @param string $media_id            
     * @param string $thumb_media_id            
     * @param string $title            
     * @param string $description            
     * @return string
     */
    public function sendVideo($toUser, $media_id, $thumb_media_id, $title, $description)
    {
        $ret = array();
        $ret['touser'] = $toUser;
        $ret['msgtype'] = 'video';
        $ret['video']['media_id'] = $media_id;
        $ret['video']['thumb_media_id'] = $thumb_media_id;
        $ret['video']['title'] = $title;
        $ret['video']['description'] = $description;
        return $this->send($ret);
    }

    /**
     * 发送音乐消息
     *
     * @param string $toUser            
     * @param string $title            
     * @param string $description            
     * @param string $musicurl            
     * @param string $hqmusicurl            
     * @param string $thumb_media_id            
     * @return string
     */
    public function sendMusic($toUser, $title, $description, $musicurl, $hqmusicurl, $thumb_media_id)
    {
        $hqmusicurl = $hqmusicurl == '' ? $musicurl : $hqmusicurl;
        $ret = array();
        $ret['touser'] = $toUser;
        $ret['msgtype'] = 'video';
        $ret['music']['title'] = $title;
        $ret['music']['description'] = $description;
        $ret['music']['musicurl'] = $musicurl;
        $ret['music']['hqmusicurl'] = $hqmusicurl;
        $ret['music']['thumb_media_id'] = $thumb_media_id;
        return $this->send($ret);
    }

    /**
     * 发送图文消息
     *
     * @param string $toUser            
     * @param string $articles            
     * @return string
     */
    public function sendGraphText($toUser, Array $articles)
    {
        if (! is_array($articles) || count($articles) == 0)
            return '';
        $items = array();
        $articles = array_slice($articles, 0, 10); // 图文消息条数限制在10条以内。
        $articleCount = count($articles);
        foreach ($articles as $article) {
            if (mb_strlen($article['description'], 'utf-8') > $this->_length) {
                $article['description'] = mb_substr($article['description'], 0, $this->getLength(), 'utf-8') . '……';
            }
            $items[] = array(
                'title' => $article['title'],
                'description' => $article['description'],
                'url' => $article['url'],
                'picurl' => $article['picurl']
            );
        }
        $ret = array();
        $ret['touser'] = $toUser;
        $ret['msgtype'] = 'news';
        $ret['news']['articles'] = $items;
        return $this->send($ret);
    }
}

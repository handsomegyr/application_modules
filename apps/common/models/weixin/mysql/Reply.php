<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Reply extends Base
{

    /**
     * 微信自动回复设定
     * This model is mapped to the table iweixin_reply
     */
    public function getSource()
    {
        return 'iweixin_reply';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        
        $data['image_media_result'] = $this->changeToArray($data['image_media_result']);
        $data['voice_media_result'] = $this->changeToArray($data['voice_media_result']);
        $data['video_media_result'] = $this->changeToArray($data['video_media_result']);
        
        return $data;
    }
}
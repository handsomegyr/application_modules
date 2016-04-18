<?php
namespace App\Weixin\Models;

/**
 * 记录微信二维码扫描状况
 */
class Qrcode extends \App\Common\Models\Weixin\Qrcode
{

    public function record($openid, $event, $eventKey, $ticket)
    {
        if ($event === 'subscribe') {
            $scene_id = str_ireplace('qrscene_', '', $eventKey);
        } else 
            if ($event === 'SCAN') {
                $scene_id = $eventKey;
            } else {
                throw new \Exception("无效的事件类型");
            }
        
        if (! empty($scene_id)) {
            $datas = array(
                'scene_id' => $scene_id,
                'openid' => $openid,
                'Event' => $event,
                'EventKey' => $eventKey,
                'Ticket' => $ticket
            );
            return $this->insert($datas);
        } else {
            return array();
        }
    }
}
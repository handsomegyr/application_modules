<?php
namespace App\Weixin\Models;

/**
 * 微信二维码场景管理
 *
 * @author young
 *        
 */
class Scene extends \App\Common\Models\Weixin\Scene
{

    public function getAll()
    {
        $query = array();
        $list = $this->findAll($query);
        return $list;
    }

    public function recordTicket(array $scene, $ticket, $url)
    {
        $query = array();
        $query['_id'] = $scene['_id'];
        
        $data['ticket'] = $ticket;
		$data['url'] = $url;
        $data['ticket_time'] = getCurrentTime();
        $data['is_created'] = true;
        $this->update($query, array(
            '$set' => $data
        ));
    }
}
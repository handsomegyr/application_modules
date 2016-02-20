<?php
namespace Webcms\Common\Models\Mysql\Weixin;

use Webcms\Common\Models\Mysql\Base;

class Application extends Base
{

    /**
     * 微信应用管理
     * This model is mapped to the table iweixin_application
     */
    public function getSource()
    {
        return 'iweixin_application';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        
        $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        $data['jsapi_ticket_expire'] = $this->changeToMongoDate($data['jsapi_ticket_expire']);
        $data['wx_card_api_ticket_expire'] = $this->changeToMongoDate($data['wx_card_api_ticket_expire']);
        
        $data['is_advanced'] = $this->changeToBoolean($data['is_advanced']);
        $data['is_product'] = $this->changeToBoolean($data['is_product']);
        $data['is_weixin_card'] = $this->changeToBoolean($data['is_weixin_card']);
        
        return $data;
    }
}
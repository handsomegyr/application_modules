<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ComponentApplication extends Base
{

    /**
     * 第三方平台应用管理
     * This model is mapped to the table iweixin_component_application
     */
    public function getSource()
    {
        return 'iweixin_component_application';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        
        $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        $data['jsapi_ticket_expire'] = $this->changeToMongoDate($data['jsapi_ticket_expire']);
        $data['wx_card_api_ticket_expire'] = $this->changeToMongoDate($data['wx_card_api_ticket_expire']);
        $data['component_access_token_expire'] = $this->changeToMongoDate($data['component_access_token_expire']);
        
        $data['access_token'] = trim($data['access_token']);
        $data['is_advanced'] = $this->changeToBoolean($data['is_advanced']);
        $data['is_product'] = $this->changeToBoolean($data['is_product']);
        $data['is_weixin_card'] = $this->changeToBoolean($data['is_weixin_card']);
        
        return $data;
    }
}
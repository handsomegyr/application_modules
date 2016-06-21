<?php
namespace App\Tencent\Models;

class OauthInfo extends \App\Common\Models\Tencent\OauthInfo
{

    public function record($applicationId, $token)
    {
        $check = $this->findOne(array(
            'access_token' => $token['access_token'],
            'applicationId' => $applicationId
        ));
        if ($check == NULL) {
            $token['applicationId'] = $applicationId;
            $check = $this->insert($token);
        } else {
            $this->update(array(
                '_id' => $check['_id']
            ), array(
                '$set' => $token
            ));
        }
        
        return ($check['_id']);
    }
}

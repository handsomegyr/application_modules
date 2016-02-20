<?php
namespace Webcms\Tencent\Models;

class Application extends \Webcms\Common\Models\Tencent\Application
{

    public function getSignKey($user_id, $secretKey, $timestamp = 0)
    {
        return sha1($user_id . "|" . $secretKey . "|" . $timestamp);
    }
}

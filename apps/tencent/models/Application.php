<?php
namespace App\Tencent\Models;

class Application extends \App\Common\Models\Tencent\Application
{

    public function getSignKey($user_id, $secretKey, $timestamp = 0)
    {
        return sha1($user_id . "|" . $secretKey . "|" . $timestamp);
    }
}

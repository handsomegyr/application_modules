<?php
namespace App\Alipay\Models;

class Callbackurls extends \App\Common\Models\Alipay\Callbackurls
{

    public function getValidCallbackUrlList($appid)
    {
        $cache = $this->getDI()->get('cache');
        $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__);
        $list = $cache->get($cacheKey);
        // $list = array();
        if (empty($list)) {
            $ret = $this->findAll(array(
                'app_id' => $appid,
                'is_valid' => true
            ));
            $list = array();
            if (! empty($ret)) {
                foreach ($ret as $item) {
                    $list[] = $item['url'];
                }
            }
            if (! empty($list)) {
                $cache->save($cacheKey, $list);
            }
        }
        return $list;
    }

    public function isValid($appid, $url)
    {
        $callbackUrls = $this->getValidCallbackUrlList($appid);
        if (empty($callbackUrls)) {
            return false;
        }
        $hostname = $this->getHost($url);
        if (in_array($hostname, $callbackUrls)) {
            return true;
        }
        $pos = strpos($hostname, '.');
        if ($pos === false) {} else {
            $hostname = substr($hostname, $pos + 1);
            if (in_array($hostname, $callbackUrls)) {
                return true;
            }
        }
        
        return false;
    }

    private function getHost($Address)
    {
        $parseUrl = parse_url(trim($Address));
        return trim(isset($parseUrl['host']) ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2)));
    }
}
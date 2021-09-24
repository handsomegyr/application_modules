<?php

namespace App\Weixin2\Controllers;

class ControllerBase extends \App\Common\Controllers\ControllerBase
{

    protected function initialize()
    {
        parent::initialize();
    }


    protected function debugVar()
    {
        ob_start();
        print_r(func_get_args());
        $info = ob_get_contents();
        ob_get_clean();
        return $info;
    }

    /**
     * 获取信息接收信息
     *
     * @return array
     */
    protected function revieve($postStr = "")
    {
        if (empty($postStr)) {
            $postStr = file_get_contents('php://input');
        }
        $datas = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $datas = $this->object2array($datas);

        if (isset($datas['Event']) && $datas['Event'] === 'LOCATION') {
            $Latitude = isset($datas['Latitude']) ? floatval($datas['Latitude']) : 0;
            $Longitude = isset($datas['Longitude']) ? floatval($datas['Longitude']) : 0;
            $datas['coordinate'] = array(
                $Latitude,
                $Longitude
            );
        }

        if (isset($datas['MsgType']) && $datas['MsgType'] === 'location') {
            $Location_X = isset($datas['Location_X']) ? floatval($datas['Location_X']) : 0;
            $Location_Y = isset($datas['Location_Y']) ? floatval($datas['Location_Y']) : 0;
            $datas['coordinate'] = array(
                $Location_X,
                $Location_Y
            );
        }

        return $datas;
    }

    /**
     * 百度地图API URI标示服务
     *
     * @param float $lat
     *            lat<纬度>,lng<经度>
     * @param float $lng
     *            lat<纬度>,lng<经度>
     * @param string $title
     *            标注点显示标题
     * @param string $content
     *            标注点显示内容
     * @param int $zoom
     *            展现地图的级别，默认为视觉最优级别。
     * @param string $output
     *            表示输出类型，web上必须指定为html才能展现地图产品结果
     * @return string
     */
    protected function mapUrl($lat, $lng, $title = '', $content = '', $zoom = '', $output = 'html')
    {
        $title = rawurlencode($title);
        $content = rawurlencode($content);
        return "http://api.map.baidu.com/marker?location={$lat},{$lng}&title={$title}&content={$content}&zoom={$zoom}&output={$output}&referer=catholic";
    }

    /**
     * 生成某个坐标的静态定位图片
     *
     * @param float $lat
     *            lat<纬度>,lng<经度>
     * @param float $lng
     *            lat<纬度>,lng<经度>
     * @param int $width
     *            图片宽度。取值范围：(0, 1024]。默认400
     * @param int $height
     *            图片高度。取值范围：(0, 1024]。 默认300
     * @param int $zoom
     *            地图级别。取值范围：[1, 18]。 默认11
     * @return string
     */
    protected function mapImage($lat, $lng, $width = 400, $height = 300, $zoom = 11)
    {
        return "http://api.map.baidu.com/staticimage?center={$lng},{$lat}&markers={$lng},{$lat}&width={$width}&height={$height}&zoom={$zoom}";
    }

    protected function shopLocation($Location_X, $Location_Y)
    {
        return array();
        // $modelShop = new Cronjob_Model_Shop();
        // $shopList = $modelShop->getNearby($Location_Y, $Location_X, 2000, 1, 10);
        // $shopList = $shopList['list'];
        $shopList = array();
        $articles = array();
        if (count($shopList) > 0) {
            $count = 0;
            foreach ($shopList as $item) {
                $name = (string) $item['name'];
                $address = (string) $item['address'];
                $longitude = (string) $item['location'][0];
                $latitude = (string) $item['location'][1];

                $article = array();
                $article['title'] = $name;
                $article['description'] = $address;
                if ($count == 0) {
                    $article['picurl'] = $this->mapImage($latitude, $longitude, 640, 320);
                } else {
                    $article['picurl'] = '';
                }
                $article['url'] = $this->mapUrl($latitude, $longitude, $name, $address);

                array_push($articles, $article);
                $count++;
                // 只要推送5条地理位置信息
                if ($count >= 5) {
                    break;
                }
            }
        }
        return $articles;
    }

    /**
     * 转化方法 很重要
     *
     * @param object $object            
     */
    protected function object2array($object)
    {
        // return @json_decode(@\App\Common\Utils\Helper::myJsonEncode($object), 1);
        return @json_decode(preg_replace('/{}/', '""', @\App\Common\Utils\Helper::myJsonEncode($object)), 1);
    }
}

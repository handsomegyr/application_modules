<?php
namespace Webcms\System\Controllers;

use Webcms\System\Models\Area;

/**
 * 地区服务
 *
 * @author Admin
 *        
 */
class AreaController extends ControllerBase
{

    private $modelArea = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelArea = new Area();
    }

    /**
     * 获取省列表的接口
     */
    public function getprovincesAction()
    {
        // http://webcms.didv.cn/service/area/getprovinces
        try {
            // 获取省列表
            $list = $this->modelArea->getProvinces();
            
            // 返回结果
            echo ($this->result("获取成功", $list));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取城市列表的接口
     */
    public function getcitysAction()
    {
        // http://webcms.didv.cn/service/area/getcitys?province=110000
        try {
            $province = $this->get('province', '');
            // 获取省列表
            $list = $this->modelArea->getCitys($province);
            
            // 返回结果
            echo ($this->result("获取成功", $list));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取区县列表的接口
     */
    public function getDistrictsAction()
    {
        // http://webcms.didv.cn/service/area/getdistricts?city=130900
        try {
            $city = $this->get('city', '');
            // 获取省列表
            $list = $this->modelArea->getDistricts($city);
            
            // 返回结果
            echo ($this->result("获取成功", $list));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}


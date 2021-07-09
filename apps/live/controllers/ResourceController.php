<?php
namespace App\Live\Controllers;

class ResourceController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        $this->modelResource = new \App\Live\Models\Resource();
    }

    /**
     * 随机获取一个资源的接口
     */
    public function getrandomAction()
    {
        // http://www.myapplicationmodule.com/live/resource/getrandom?type=1
        try {
            $type = $this->get('type', '');
            if (empty($type)) {
                echo $this->error(- 1, 'type is empty');
                return false;
            }
            $resource = $this->modelResource->getRandom($type);
            echo $this->result('OK', $resource);
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取昵称和头像,话语资源
     *
     * @return boolean
     */
    public function getlistAction()
    {
        // http://www.myapplicationmodule.com/live/resource/getlist?next_id=xxxx&type=1
        try {
            $next_id = $this->get('next_id', '');
            $type = $this->get('type', '');
            
            $sort = array(
                '_id' => 1
            );
            $query = array();
            if (! empty($next_id)) {
                $query['_id'] = array(
                    '$gt' => $next_id
                );
            }
            if (! empty($type)) {
                $query['type'] = intval($type);
            }
            
            $list = $this->modelResource->find($query, $sort, 0, 1000);
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    $this->modelResource->saveInfoToRedis($item);
                    $next_id = ($item['_id']);
                }
            } else {
                $next_id = '';
            }
            echo $this->result('OK', array(
                'next_id' => $next_id
            ));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 删除某个资源的接口
     */
    public function removefromredisAction()
    {
        // http://www.myapplicationmodule.com/live/resource/removefromredis?id=xxxx
        try {
            $id = $this->get('id', '');
            if (empty($id)) {
                echo $this->error(- 1, 'id为空');
                return false;
            }
            $info = $this->modelResource->getInfoById($id);
            if (empty($info)) {
                echo $this->error(- 2, 'id不存在');
                return false;
            }
            
            $this->modelResource->removeFromRedis($info);
            
            echo $this->result('remove OK');
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 清空机器人资源
     *
     * @return boolean
     */
    public function removeallfromredisAction()
    {
        // http://www.myapplicationmodule.com/live/resource/removeallfromredis&type=1
        try {
            $type = $this->get('type', '');
            if (empty($type)) {
                $this->modelResource->removeAllFromRedis();
                echo $this->result('remove all OK');
            } else {
                $this->modelResource->removeOneTypeFromRedis($type);
                echo $this->result("remove {$type}的记录 OK");
            }
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}
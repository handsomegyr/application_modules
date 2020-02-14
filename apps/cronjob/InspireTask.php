<?php
class InspireTask extends \Phalcon\CLI\Task
{

    /**
     * 处理
     * @param array $params            
     */
    public function handleAction(array $params)
    {
        $modelInspire = new \App\Cronjob\Models\Inspire();
        $modelInspire->remove(array());
        $info = array();
        $info['content'] = \uniqid();
        $modelInspire->insert($info);
    }
}

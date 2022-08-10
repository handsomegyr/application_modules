<?php
class InspireTask extends \Phalcon\CLI\Task
{

    /**
     * 处理
     * @param array $params
     */
    public function handleAction(array $params)
    {
        $content = \uniqid();
        echo sprintf('%s', $content) . PHP_EOL;

        $modelInspire = new \App\Cronjob\Models\Inspire();
        $modelInspire->remove(array());
        $info = array();
        $info['content'] = $content;
        $modelInspire->insert($info);
    }
}

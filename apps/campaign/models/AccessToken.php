<?php
namespace App\Campaign\Models;

class AccessToken
{

    private $table_name = 'weixin_access_token';

    public function getConnection()
    {
        $di = \Phalcon\DI::getDefault();
        if (! $di->has('dbfrom')) {
            throw new \Exception('dbfrom未正确初始化');
        }
        return $di['dbfrom'];
    }

    public function getLastestInfo()
    {
        $connection = $this->getConnection();
        $info = $connection->fetchOne("SELECT * FROM {$this->table_name} Order by id desc", \Phalcon\Db::FETCH_ASSOC);
        return $info;
    }
}
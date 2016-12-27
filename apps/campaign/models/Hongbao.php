<?php
namespace App\Campaign\Models;

class Hongbao
{

    private $table_name = 'compaign20160425_qrcode';

    public function getConnection()
    {
        $di = \Phalcon\DI::getDefault();
        if (! $di->has('dbfrom')) {
            throw new \Exception('dbfrom未正确初始化');
        }
        return $di['dbfrom'];
    }

    public function getInfoByFromUserName($FromUserName)
    {
        $connection = $this->getConnection();
        $info = $connection->fetchOne("SELECT * FROM {$this->table_name} Where FromUserName = :FromUserName", \Phalcon\Db::FETCH_ASSOC, array(
            'FromUserName' => $FromUserName
        ));
        return $info;
    }

    /**
     * 更新使用金额
     *
     * @param string $customer_id            
     * @param number $total_amount            
     */
    public function incAmount($FromUserName, $get_money)
    {
        $connection = $this->getConnection();
        $success = $connection->execute("update {$this->table_name} set get_money=get_money+:get_money where FromUserName=:FromUserName And money>=get_money+:get_money", array(
            'get_money' => $get_money,
            'FromUserName' => $FromUserName
        ));
    }
}
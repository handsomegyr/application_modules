<?php
class SyncthirdpartyorderTask extends \Phalcon\CLI\Task
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pointshop:sync_thirdparty_order {cronjob_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步第三方订单数据';

    private $time = 0;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php syncthirdpartyorder handle xxxxx
     * @param array $params            
     */
    public function handleAction(array $params)
    {
        $modelLog = new \App\Cronjob\Models\DataImport\Log();
        $modelFile = new \App\Cronjob\Models\DataImport\File();
        $modelFileContent = new \App\Cronjob\Models\DataImport\FileContent();
        // $modelOrder = new \App\ThirdParty\Models\Order();

        $this->time = time();

        $cronjob_id = empty($params[0]) ? '' : $params[0];
        if (empty($cronjob_id)) {
            // 取最前的那条数据
            $info = $modelFileContent->getFirstInfo4UnProccessByType("order");
            if (empty($info)) {
                $cronjob_id = '';
            } else {
                $cronjob_id = $info['cronjob_id'];
            }
        }
        $fileContentList = $modelFileContent->getContentList4UnProccess($cronjob_id, "order");

        $params  = array(
            'cronjob_id' => $cronjob_id,
            'time' => date('Y-m-d H:i:s', $this->time),
            'name' => 'pointshop:sync_thirdparty_order',
            'fileContentList' => $fileContentList
        );
        print_r($params);
        // die('xxx');

        // 处理数据的次数
        $processcount = 0;

        if (!empty($fileContentList)) {
            foreach ($fileContentList as $item) {

                $itemId = ($item['_id']);
                $current_cronjob_id = $item['cronjob_id'];
                $returnback_cronjob_id = '';

                // $content = \json_decode($item['content'], true);
                $processcount++;

                $status = \App\Cronjob\Models\DataImport\FileContent::PROCESS_STATUS_FINISH;
                $process_desc = "";

                try {
                    // DB::beginTransaction();
                    $modelFileContent->begin();
                    $now = time();
                    // 将数据同步到商城订单表中
                    $process_desc =  "已成功创建订单";

                    // //客户编号	订单号	订单总金额	商品编码	商品名称	价格	数量	订单日期
                    // $ucode = $content[0];
                    // // 订单编号
                    // $order_id = $content[1];
                    // $order_amount = $content[2] * 100;
                    // $sku_no = $content[3];
                    // $sku_name = $content[4];
                    // $sku_price = $content[5] * 100;
                    // $sku_num = intval($content[6]);
                    // $content[7] = str_replace('/', '-', $content[7]);
                    // $order_time = strtotime($content[7]);

                    // $sku = array();
                    // // no 商品SKU no
                    // $sku['no'] = $sku_no;
                    // // sku_name 商品SKU名称
                    // $sku['name'] = $sku_name;
                    // // sku_price 商品SKU价格(单位分)
                    // $sku['price'] = $sku_price;
                    // // sku_num 商品SKU数量
                    // $sku['num'] = $sku_num;
                    // // sku_image 商品SKU图片
                    // $sku['img1'] = '';

                    // // 检查是否已经导入到订单表了
                    // $orderInfo = $modelOrder->lockOrderByOrderId($order_id);

                    // // 如果没有
                    // if (empty($orderInfo)) {
                    //     // 创建一个订单
                    //     // 商品
                    //     $goods_amount = 0;
                    //     $goods_num = 0;
                    //     $goodsList = array();
                    //     // 商品金额
                    //     $goods_amount += $sku['price'] * $sku['num'];
                    //     // 商品数量
                    //     $goods_num += $sku['num'];
                    //     // 商品列表
                    //     $goodsList[] = $sku;

                    //     $memo = array('cronjob_ids' => array(array('cronjob_id' => $current_cronjob_id, 'file_content_item_id' => $itemId)));
                    //     $memo['belong_to_cronjob_id'] = $current_cronjob_id;
                    //     $orderInfo = $modelOrder->createOrder($ucode, $order_id, $goodsList, $order_amount, $goods_amount, $goods_num, $order_time, $memo);

                    //     $process_desc =  "已成功创建订单ID:{$orderInfo['_id']}的订单";
                    // } else {
                    //     // 更新订单
                    //     // 商品
                    //     $goods_amount = $orderInfo['goods_amount'];
                    //     $goods_num = $orderInfo['goods_num'];
                    //     $goodsList = $orderInfo['goods_details'];
                    //     // 商品金额
                    //     $goods_amount += $sku['price'] * $sku['num'];
                    //     // 商品数量
                    //     $goods_num += $sku['num'];
                    //     // 商品列表
                    //     $goodsList[] = $sku;

                    //     $updateData = array();
                    //     // goods_amount 商品总金额(单位分)
                    //     $updateData['goods_amount'] = $goods_amount;

                    //     // goods_num 商品总数量
                    //     $updateData['goods_num'] = $goods_num;

                    //     // order_amount 订单总金额(单位分)
                    //     $updateData['order_amount'] = $order_amount;

                    //     // goods_details 商品信息
                    //     $updateData['goods_details'] = \\App\Common\Utils\Helper::myJsonEncode($goodsList);

                    //     $memo = $orderInfo['memo'];

                    //     if ($memo['belong_to_cronjob_id'] == $current_cronjob_id) {
                    //         $memo['cronjob_ids'][] = array('cronjob_id' => $current_cronjob_id, 'file_content_item_id' => $itemId);
                    //         $updateData['memo'] = $memo;
                    //         $orderInfo = $modelOrder->updateById($orderInfo['_id'], $updateData);
                    //         $process_desc =  "已成功更新订单ID:{$orderInfo['_id']}的订单，增加了商品信息:{$sku['name']}|{$sku['num']}";
                    //     } else {
                    //         $status = \App\Cronjob\Models\DataImport\FileContent::PROCESS_STATUS_FAIL;
                    //         $process_desc =  "current_cronjob_id:{$current_cronjob_id}和订单ID:{$order_id}所对应的订单所记录的belong_to_cronjob_id:{$memo['belong_to_cronjob_id']}不一致";
                    //         //throw new \Exception($process_desc);
                    //     }
                    // }

                    // 更新该条数据
                    $updateData4FileContent = array();
                    $updateData4FileContent['process_desc'] = $process_desc;
                    $modelFileContent->updateProcessStatus($itemId, $status, $now, array(), $updateData4FileContent);

                    // 更新
                    $incData4File = array('process_total' => 1);
                    $updataData4File = array('desc' => '上传的订单数据同步到商城订单表的处理');
                    $modelFile->updateStatus($current_cronjob_id, null, $now, $incData4File, $updataData4File);

                    // DB::commit();
                    $modelFileContent->commit();
                } catch (\Exception $e) {
                    // DB::rollback();
                    $modelFileContent->rollback();
                    $errorMsg = $e->getCode() . ":" . $e->getMessage();
                    $modelLog->log($current_cronjob_id, $returnback_cronjob_id, "上传的订单数据同步到商城订单表的处理,处理{$itemId}对应的的数据失败", $itemId, time(), "失败原因：[{$errorMsg}]");
                }
            }
        }
        echo "OK";
    }
}

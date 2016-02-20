<?php
namespace Weixin\Manager\Msg;

use Weixin\Client;

/**
 * 模板消息接口
 *
 * @author Ben
 *        
 */
class Template
{

    private $_client;

    public function __construct(Client $client)
    {
        $this->_client = $client;
    }

    /**
     * 发送模板消息
     *
     * @param string $touser            
     * @param string $template_id            
     * @param string $url            
     * @param string $topcolor            
     * @param array $data            
     *
     * @throws Exception
     * @return array
     */
    public function send($touser, $template_id, $url, $topcolor, array $data)
    {
        /**
         * {
         * "touser":"OPENID",
         * "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
         * "url":"http://weixin.qq.com/download",
         * "topcolor":"#FF0000",
         * "data":{
         * "first": {
         * "value":"您好，您已成功消费。",
         * "color":"#0A0A0A"
         * },
         * "keynote1":{
         * "value":"海记汕头牛肉",
         * "color":"#CCCCCC"
         * },
         * "keynote2": {
         * "value":"8703514836",
         * "color":"#CCCCCC"
         * },
         * "keynote3":{
         * "value":"2014-08-03 19:35",
         * "color":"#CCCCCC"
         * },
         * "remark":{
         * "value":"欢迎再次购买。",
         * "color":"#173177"
         * }
         * }
         */
        $params = array();
        $params['touser'] = $touser;
        $params['template_id'] = $template_id;
        $params['url'] = $url;
        $params['topcolor'] = $topcolor;
        $params['data'] = $data;
        $rst = $this->_client->getRequest()->post('message/template/send', $params);
        return $this->_client->rst($rst);
    }

    /**
     * 设置所属行业
     *
     * 设置行业可在MP中完成，每月可修改行业1次，账号仅可使用所属行业中相关的模板，为方便第三方开发者，提供通过接口调用的方式来修改账号所属行业，具体如下：
     *
     * 接口调用请求说明
     *
     * http请求方式: POST
     * https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=ACCESS_TOKEN
     * POST数据说明
     *
     * POST数据示例如下：
     *
     * {
     * "industry_id1":"1",
     * "industry_id2":"4"
     * }
     * 参数说明
     *
     * 参数	是否必须	说明
     * industry_id1 是 公众号模板消息所属行业编号
     * industry_id2 是 公众号模板消息所属行业编号
     * 行业代码查询
     *
     * 主行业	副行业	代码
     * IT科技 互联网/电子商务 1
     * IT科技 IT软件与服务 2
     * IT科技 IT硬件与设备 3
     * IT科技 电子技术 4
     * IT科技 通信与运营商 5
     * IT科技 网络游戏 6
     * 金融业 银行 7
     * 金融业	基金|理财|信托 8
     * 金融业 保险 9
     * 餐饮 餐饮 10
     * 酒店旅游 酒店 11
     * 酒店旅游 旅游 12
     * 运输与仓储 快递 13
     * 运输与仓储 物流 14
     * 运输与仓储 仓储 15
     * 教育 培训 16
     * 教育 院校 17
     * 政府与公共事业 学术科研 18
     * 政府与公共事业 交警 19
     * 政府与公共事业 博物馆 20
     * 政府与公共事业	公共事业|非盈利机构 21
     * 医药护理 医药医疗 22
     * 医药护理 护理美容 23
     * 医药护理 保健与卫生 24
     * 交通工具 汽车相关 25
     * 交通工具 摩托车相关 26
     * 交通工具 火车相关 27
     * 交通工具 飞机相关 28
     * 房地产	建筑 29
     * 房地产 物业 30
     * 消费品 消费品 31
     * 商业服务 法律 32
     * 商业服务	会展 33
     * 商业服务 中介服务 34
     * 商业服务	认证 35
     * 商业服务	审计 36
     * 文体娱乐	传媒 37
     * 文体娱乐 体育 38
     * 文体娱乐 娱乐休闲 39
     * 印刷	印刷 40
     * 其它 其它 41
     *
     *
     * @param string $industry_id1            
     * @param string $industry_id2            
     *
     * @throws Exception
     * @return array
     */
    public function setIndustry($industry_id1, $industry_id2)
    {
        $params = array();
        $params['industry_id1'] = $industry_id1;
        $params['industry_id2'] = $industry_id2;
        $rst = $this->_client->getRequest()->post('template/api_set_industry', $params);
        return $this->_client->rst($rst);
    }

    /**
     * 获得模板ID
     *
     * 从行业模板库选择模板到账号后台，获得模板ID的过程可在MP中完成。为方便第三方开发者，提供通过接口调用的方式来修改账号所属行业，具体如下：
     *
     * 接口调用请求说明
     *
     * http请求方式: POST
     * https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=ACCESS_TOKEN
     * POST数据说明
     *
     * POST数据示例如下：
     *
     * {
     * "template_id_short":"TM00015"
     * }
     * 参数说明
     *
     * 参数	是否必须	说明
     * template_id_short 是 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * 返回码说明
     *
     * 在调用模板消息接口后，会返回JSON数据包。正常时的返回JSON数据包示例：
     *
     * {
     * "errcode":0,
     * "errmsg":"ok",
     * "template_id":"Doclyl5uP7Aciu-qZ7mJNPtWkbkYnWBWVja26EGbNyk"
     * }
     */
    public function addTemplate($template_id_short)
    {
        $params = array();
        $params['template_id_short'] = $template_id_short;
        $rst = $this->_client->getRequest()->post('template/api_add_template', $params);
        return $this->_client->rst($rst);
    }
}

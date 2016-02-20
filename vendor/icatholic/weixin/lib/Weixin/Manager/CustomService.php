<?php
namespace Weixin\Manager;

use Weixin\Client;

/**
 * 获取客服聊天记录接口
 * 在需要时，开发者可以通过获取客服聊天记录接口，获取多客服的会话记录，
 * 包括客服和用户会话的所有消息记录和会话的创建、关闭等操作记录。
 * 利用此接口可以开发如“消息记录”、“工作监控”、“客服绩效考核”等功能。
 *
 * @author guoyongrong <handsomegyr@gmail.com>
 * @author young <youngyang@icatholic.net.cn>
 */
class CustomService
{

    private $_client;

    private $_request;

    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->_request = $client->getRequest();
    }

    /**
     * 获取客服聊天记录接口
     * 接口调用请求说明
     *
     * http请求方式: POST
     * https://api.weixin.qq.com/cgi-bin/customservice/getrecord?access_token=ACCESS_TOKEN
     * POST数据示例如下：
     * {
     * "starttime" : 123456789,
     * "endtime" : 987654321,
     * "openid" : "OPENID",
     * "pagesize" : 10,
     * "pageindex" : 1,
     * }
     *
     * @return mixed
     */
    public function getRecord($openid, $starttime, $endtime, $pageindex = 1, $pagesize = 1000)
    {
        $params = array();
        /**
         * openid 否 普通用户的标识，对当前公众号唯一
         * starttime 是 查询开始时间，UNIX时间戳
         * endtime 是 查询结束时间，UNIX时间戳，每次查询不能跨日查询
         * pagesize 是 每页大小，每页最多拉取1000条
         * pageindex 是 查询第几页，从1开始
         */
        if ($openid) {
            $params['openid'] = $openid;
        }
        $params['starttime'] = $starttime;
        $params['endtime'] = $endtime;
        $params['pageindex'] = $pageindex;
        $params['pagesize'] = $pagesize;
        
        $rst = $this->_request->post('customservice/getrecord', $params);
        return $this->_client->rst($rst);
    }

    /**
     * 获取客服基本信息
     *
     * 开发者通过本接口，根据AppID获取公众号中所设置的客服基本信息，包括客服工号、客服昵称、客服登录账号。
     * 开发者利用客服基本信息，结合客服接待情况，可以开发例如“指定客服接待”等功能。
     *
     * 接口调用说明
     *
     * http请求方式: GET
     * https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=ACCESS_TOKEN
     * 返回说明
     *
     * 返回数据示例（正确时的JSON返回结果）：
     * {
     * "kf_list": [
     * {
     * "kf_account": "test1@test",
     * "kf_nick": "ntest1",
     * "kf_id": "1001"
     * "kf_headimg": "http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw/0"
     * },
     * {
     * "kf_account": "test2@test",
     * "kf_nick": "ntest2",
     * "kf_id": "1002"
     * "kf_headimg": "http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw/0"
     * },
     * {
     * "kf_account": "test3@test",
     * "kf_nick": "ntest3",
     * "kf_id": "1003"
     * "kf_headimg": "http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw/0"
     * }
     * ]
     * }
     * 参数	说明
     * kf_account 完整客服账号，格式为：账号前缀@公众号微信号
     * kf_nick 客服昵称
     * kf_id 客服工号
     * 错误时微信会返回错误码等信息，请根据错误码查询错误信息:全局返回码说明
     */
    public function getkflist()
    {
        $params = array();
        $rst = $this->_request->get('customservice/getkflist', $params);
        return $this->_client->rst($rst);
    }

    /**
     * 获取在线客服接待信息
     *
     * 开发者通过本接口，根据AppID获取公众号中当前在线的客服的接待信息，
     * 包括客服工号、客服登录账号、客服在线状态（手机在线、PC客户端在线、手机和PC客户端全都在线）、
     * 客服自动接入最大值、客服当前接待客户数。
     * 开发者利用本接口提供的信息，结合客服基本信息，
     * 可以开发例如“指定客服接待”等功能；结合会话记录，
     * 可以开发”在线客服实时服务质量监控“等功能。
     *
     * 接口调用请求说明
     *
     * http请求方式: GET
     * https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token=ACCESS_TOKEN
     * 返回说明
     *
     * 返回数据示例（正确时的JSON返回结果）：
     * {
     * "kf_online_list": [
     * {
     * "kf_account": "test1@test",
     * "status": 1,
     * "kf_id": "1001",
     * "auto_accept": 0,
     * "accepted_case": 1
     * },
     * {
     * "kf_account": "test2@test",
     * "status": 1,
     * "kf_id": "1002",
     * "auto_accept": 0,
     * "accepted_case": 2
     * }
     * ]
     * }
     * 参数	说明
     * kf_account 完整客服账号，格式为：账号前缀@公众号微信号
     * status 客服在线状态 1：pc在线，2：手机在线。若pc和手机同时在线则为 1+2=3
     * kf_id 客服工号
     * auto_accept 客服设置的最大自动接入数
     * accepted_case 客服当前正在接待的会话数
     * 错误时微信会返回错误码等信息，请根据错误码查询错误信息:全局返回码说明
     */
    public function getonlinekflist()
    {
        $params = array();
        $rst = $this->_request->get('customservice/getonlinekflist', $params);
        return $this->_client->rst($rst);
    }

    /**
     * 添加客服账号
     *
     * 开发者通过本接口可以为公众号添加客服账号，每个公众号最多添加10个客服账号。
     *
     * 接口调用请求说明
     *
     * http请求方式: POST
     * https://api.weixin.qq.com/customservice/kfaccount/add?access_token=ACCESS_TOKEN
     * POST数据说明
     *
     * POST数据示例如下：
     * {
     * "kf_account" : test1@test,
     * "nickname" : “客服1”,
     * "password" : "pswmd5",
     * }
     * 参数	是否必须	说明
     * kf_account 是 完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符。如果没有公众号微信号，请前往微信公众平台设置。
     * nickname 是 客服昵称，最长6个汉字或12个英文字符
     * password 是 客服账号登录密码，格式为密码明文的32位加密MD5值
     * 返回说明
     *
     * 返回数据示例（正确时的JSON返回结果）：
     * {
     * "errcode" : 0,
     * "errmsg" : "ok",
     * }
     * 错误时微信会返回错误码等信息，请根据错误码查询错误信息:全局返回码说明
     *
     * @param string $kf_account            
     * @param string $nickname            
     * @param string $password            
     */
    public function kfaccountAdd($kf_account, $nickname, $password)
    {
        $params = array();
        $params['kf_account'] = $kf_account;
        $params['nickname'] = $nickname;
        $params['password'] = $password;
        
        $rst = $this->_request->payPost('customservice/kfaccount/add', $params);
        return $this->_client->rst($rst);
    }

    /**
     * 设置客服信息
     *
     * 接口调用请求说明
     *
     * http请求方式: POST
     * https://api.weixin.qq.com/customservice/kfaccount/update?access_token=ACCESS_TOKEN
     * POST数据说明
     *
     * POST数据示例如下：
     * {
     * "kf_account" : test1@test,
     * "nickname" : “客服1”,
     * "password" : "pswmd5",
     * }
     * 参数	是否必须	说明
     * kf_account 是 完整客服账号，格式为：账号前缀@公众号微信号
     * nickname 是 客服昵称，最长6个汉字或12个英文字符
     * password 是 客服账号登录密码，格式为密码明文的32位加密MD5值
     * 返回说明
     *
     * 返回数据示例（正确时的JSON返回结果）：
     * {
     * "errcode" : 0,
     * "errmsg" : "ok",
     * }
     * 错误时微信会返回错误码等信息，请根据错误码查询错误信息:全局返回码说明
     *
     * @param string $kf_account            
     * @param string $nickname            
     * @param string $password            
     */
    public function kfaccountUpdate($kf_account, $nickname, $password)
    {
        $params = array();
        $params['kf_account'] = $kf_account;
        $params['nickname'] = $nickname;
        $params['password'] = $password;
        
        $rst = $this->_request->payPost('customservice/kfaccount/update', $params);
        return $this->_client->rst($rst);
    }

    /**
     * 上传客服头像
     *
     * 开发者可调用本接口来上传图片作为客服人员的头像，头像图片文件必须是jpg格式，推荐使用640*640大小的图片以达到最佳效果。
     *
     * 接口调用请求说明
     *
     * http请求方式: POST/FORM
     * http://api.weixin.qq.com/customservice/kfacount/uploadheadimg?access_token=ACCESS_TOKEN&kf_account=KFACCOUNT
     *
     * 调用示例（使用curl命令，用FORM表单方式上传一个多媒体文件）：
     * curl -F media=@test.jpg "https://api.weixin.qq.com/customservice/kfacount/uploadheadimg?access_token=ACCESS_TOKEN&kf_account=KFACCOUNT"
     * 参数说明
     *
     * 参数	是否必须	说明
     * kf_account 是 完整客服账号，格式为：账号前缀@公众号微信号
     * media 是 form-data中媒体文件标识，有filename、filelength、content-type等信息
     * 返回说明
     *
     * 返回数据示例（正确时的JSON返回结果）：
     * {
     * "errcode" : 0,
     * "errmsg" : "ok",
     * }
     * 错误时微信会返回错误码等信息，请根据错误码查询错误信息:全局返回码说明
     *
     * @param string $kf_account            
     * @param string $media            
     */
    public function kfacountUploadheadimg($kf_account, $media)
    {
        $rst = $this->_request->uploadheadimg4KfAcount($kf_account, $media);
        return $this->_client->rst($rst);
    }

    /**
     * 删除客服账号
     *
     * 接口调用请求说明
     *
     * http请求方式: GET
     * https://api.weixin.qq.com/customservice/kfaccount/del?access_token=ACCESS_TOKEN&kf_account=KFACCOUNT
     * 参数说明
     *
     * 参数	是否必须	说明
     * kf_account 是 完整客服账号，格式为：账号前缀@公众号微信号
     * 返回说明
     *
     * 返回数据示例（正确时的JSON返回结果）：
     * {
     * "errcode" : 0,
     * "errmsg" : "ok",
     * }
     * 错误时微信会返回错误码等信息，请根据错误码查询错误信息:全局返回码说明
     *
     * @param string $kf_account            
     */
    public function kfaccountDel($kf_account)
    {
        $params = array();
        $params['kf_account'] = $kf_account;
        
        $rst = $this->_request->payPost('customservice/kfaccount/del', $params);
        return $this->_client->rst($rst);
    }
}

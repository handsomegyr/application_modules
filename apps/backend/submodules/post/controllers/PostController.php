<?php
namespace App\Backend\Submodules\Post\Controllers;

use App\Backend\Submodules\Post\Models\Post;

/**
 * @title({name="晒单管理"})
 *
 * @name 晒单管理
 */
class PostController extends \App\Backend\Controllers\FormController
{

    private $stateDatas = \App\Common\Models\Post\Post::STATEDATAS;

    private $modelPost;

    public function initialize()
    {
        $this->modelPost = new Post();
        parent::initialize();
    }

    /**
     * @title({name="审核通过"})
     *
     * @name 审核通过
     */
    public function passAction()
    {
        try {
            $this->view->disable();
            
            $input = $this->getFilterInput();
            
            if ($input->isValid("id")) {} else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            $point = intval($this->get('point', '0'));
            if (empty($point)) {
                throw new \Exception('积分未填写');
            }
            $is_recommend = intval($this->get('is_recommend', '0'));
            $this->doPassPost($input->id, $point, $is_recommend);
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="审核不通过"})
     *
     * @name 审核不通过
     */
    public function unpassAction()
    {
        try {
            $this->view->disable();
            
            $input = $this->getFilterInput();
            
            if ($input->isValid("id")) {} else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            $fail_reason = intval($this->get('fail_reason', '0'));
            if (empty($fail_reason)) {
                throw new \Exception('失败原因未填写');
            }
            $this->doUnPassPost($input->id, $fail_reason);
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['title'] = array(
            'name' => '主题',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['content'] = array(
            'name' => '内容',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['pic'] = array(
            'name' => '晒图',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelPost->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['post_time'] = array(
            'name' => '晒单时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['state'] = array(
            'name' => '状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->stateDatas
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'defaultValues' => array(
                    \App\Common\Models\Post\Post::STATE0
                ),
                'items' => function ()
                {
                    return array_column($this->stateDatas, 'name', 'value');
                }
            )
        );
        $schemas['fail_reason'] = array(
            'name' => '未通过原因',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['point'] = array(
            'name' => '福分',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_commonid'] = array(
            'name' => 'goods_commonid',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_id'] = array(
            'name' => 'goods_id',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id_1'] = array(
            'name' => 'gc_id_1',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id_2'] = array(
            'name' => 'gc_id_2',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id_3'] = array(
            'name' => 'gc_id_3',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['brand_id'] = array(
            'name' => 'brand_id',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['order_no'] = array(
            'name' => 'order_no',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['buyer_id'] = array(
            'name' => 'buyer_id',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_info'] = array(
            'name' => '商品信息',
            'data' => array(
                'type' => 'json',
                'length' => 1000
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['vote_num'] = array(
            'name' => '羡慕数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['reply_num'] = array(
            'name' => '评论数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['read_num'] = array(
            'name' => '阅读数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_recommend'] = array(
            'name' => '是否推荐',
            'data' => array(
                'type' => 'boolean',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '晒单';
    }

    protected function getPartials4List()
    {
        return array(
            '../submodules/post/views/partials/postverify'
        );
    }

    protected function getModel()
    {
        return $this->modelPost;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            if ($item['state'] != \App\Common\Models\Post\Post::STATE_NONE) {
                $item['post_time'] = date('Y-m-d H:i:s', $item['post_time']->sec);
            } else {
                $item['post_time'] = "";
            }
            $picArr = explode(',', $item['pic']);
            $item['pic'] = $picArr[0];
            
            if ($item['state'] > \App\Common\Models\Post\Post::STATE0) {
                $item['verify_time'] = date('Y-m-d H:i:s', $item['verify_time']->sec);
            } else {
                $item['verify_time'] = "";
            }
            $goods_info = $item['goods_info'];
            $item['goods_info'] = "";
            $item['goods_info'] .= "云购ID:{$goods_info['_id']}<br/>商品名称:(第{$goods_info['period']}云){$goods_info['name']}<br/>单价:{$goods_info['price']}<br/>商品ID:{$goods_info['goods_commonid']}<br/>";
            $item['goods_info'] .= "买家ID:{$goods_info['prize_buyer_id']}<br/>买家名称:{$goods_info['prize_buyer_name']}<br/>IP:{$goods_info['prize_buyer_ip']}<br/>";
            $purchase_time = getMilliTime4Show($goods_info['prize_buyer_purchase_time']);
            $prize_time = getMilliTime4Show($goods_info['prize_time']);
            $item['goods_info'] = "云购码:{$goods_info['lottery_code']}<br/>幸运码:{$goods_info['prize_code']}<br/>揭晓时间:{$prize_time}<br/>云购次数:{$goods_info['prize_buyer_purchase_num']}<br/>购买时间:{$purchase_time}";
            
            if ($item['state'] != \App\Common\Models\Post\Post::STATE2) { // 已通过
                $item['state'] = $this->stateDatas[strval($item['state'])]['name'];
                // $item['state'] = $item['state'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要审核通过吗？\', \'pass\')" class="halflings-icon user white"><i></i> 通过</a>';
                // $item['state'] = $item['state'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要审核未通过吗？\', \'unpass\')" class="halflings-icon user white"><i></i> 不通过</a>';
                $item['state'] = $item['state'] . '<br/><button type="button" onclick="Post.showPass(\'' . $item['_id'] . '\')" class="btn blue">通过</button>';
                $item['state'] = $item['state'] . '<br/><button type="button" onclick="Post.showUnPass(\'' . $item['_id'] . '\')" class="btn blue">不通过</button>';
            } else {
                $item['state'] = $this->stateDatas[strval($item['state'])]['name'];
            }
        }
        return $list;
    }

    function doPassPost($post_id, $point, $is_recommend)
    {
        $param = array(
            'post_id' => $post_id,
            'point' => $point,
            'is_recommend' => $is_recommend,
            'user_id' => $_SESSION['admin_id'],
            'user_name' => $_SESSION['admin_name']
        );
        // http://www.jizigou.com/order/service/passpost?post_id=xxx&point=800&is_recommend=1
        $ret = $this->verify("http://www.jizigou.com/post/service/passpost", $param);
    }

    function doUnPassPost($post_id, $fail_reason)
    {
        $param = array(
            'post_id' => $post_id,
            'fail_reason' => $fail_reason,
            'user_id' => $_SESSION['admin_id'],
            'user_name' => $_SESSION['admin_name']
        );
        // http://www.jizigou.com/order/service/unpasspost?post_id=xxx&fail_reason=xxxx&user_id=xx&user_name=xx
        $ret = $this->verify("http://www.jizigou.com/post/service/unpasspost", $param);
    }

    function verify($url, $param)
    {
        $ret = doPost("http://www.jizigou.com/post/service/passpost", $param);
        $ret = (string) $ret;
        if (! empty($ret)) {
            if (isJson($ret)) {
                $ret = json_decode($ret, true);
                if ($ret["success"]) {
                    $info = $ret['result'];
                    return $info;
                } else {
                    throw new \Exception($ret['error_msg'], $ret['error_code']);
                }
            } else {
                throw new \Exception("审核晒单不是有效的json格式");
            }
        } else {
            throw new \Exception("可能网络繁忙,审核晒单的请求失败");
        }
    }
}
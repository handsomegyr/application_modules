<?php
namespace App\Backend\Submodules\Weixincard\Models;

use Weixin\Model\BaseInfo;
use Weixin\Model\DateInfo;
use Weixin\Model\Sku;
use Weixin\Model\CardBase;
use Weixin\Model\GeneralCoupon;
use Weixin\Model\Cash;
use Weixin\Model\Gift;
use Weixin\Model\Groupon;
use Weixin\Model\Discount;
use Weixin\Model\BoardingPass;
use Weixin\Model\LuckyMoney;
use Weixin\Model\MemberCard;
use Weixin\Model\MovieTicket;
use Weixin\Model\ScenicTicket;
use Weixin\Model\CustomField;
use Weixin\Model\CustomCell;

class Card extends \App\Common\Models\Weixincard\Card
{
    
    use \App\Backend\Models\Base;

    private $_weixin;

    /**
     * 设置微信对象
     */
    public function setWeixin(\Weixin\Client $weixin)
    {
        $this->_weixin = $weixin;
    }

    /**
     * 根据card_id获取信息
     *
     * @param string $card_id            
     * @return array
     */
    public function getInfoByCardId($card_id)
    {
        $query = array(
            'card_id' => $card_id
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 获取所有带有cardid的列表信息
     *
     * @param string $pid            
     * @return array
     */
    public function getAllWithCardId()
    {
        $query = $this->getQuery();
        $query['card_id'] = array(
            '$ne' => ''
        );
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['card_id']] = $item['title'];
        }
        return $list;
    }

    /**
     * 获取列表信息
     *
     * @param string $pid            
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $list = $this->findAll($query, $sort);
        return $list;
    }

    /**
     * 记录
     *
     * @param BoardingPass $objCard            
     * @param array $memo            
     */
    public function recordBoardingPass(BoardingPass $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $from = $objCard->from;
        $to = $objCard->to;
        $flight = $objCard->flight;
        $departure_time = $objCard->departure_time;
        $landing_time = $objCard->landing_time;
        $check_in_url = $objCard->check_in_url;
        $gate = $objCard->gate;
        $boarding_time = $objCard->boarding_time;
        $air_model = $objCard->air_model;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        $data[$card_type_low]['from'] = (string) ($from);
        $data[$card_type_low]['to'] = (string) ($to);
        $data[$card_type_low]['flight'] = (string) ($flight);
        $data[$card_type_low]['departure_time'] = (string) ($departure_time);
        $data[$card_type_low]['landing_time'] = (string) ($landing_time);
        $data[$card_type_low]['check_in_url'] = (string) ($check_in_url);
        $data[$card_type_low]['gate'] = (string) ($gate);
        $data[$card_type_low]['boarding_time'] = (string) ($boarding_time);
        $data[$card_type_low]['air_model'] = (string) ($air_model);
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param Cash $objCard            
     * @param array $memo            
     */
    public function recordCash(Cash $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $least_cost = $objCard->least_cost;
        $reduce_cost = $objCard->reduce_cost;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        $data[$card_type_low]['least_cost'] = intval($least_cost);
        $data[$card_type_low]['reduce_cost'] = intval($reduce_cost);
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param Discount $objCard            
     * @param array $memo            
     */
    public function recordDiscount(Discount $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $discount = $objCard->discount;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        $data[$card_type_low]['discount'] = intval($discount);
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param GeneralCoupon $objCard            
     * @param array $memo            
     */
    public function recordGeneralCoupon(GeneralCoupon $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $default_detail = $objCard->default_detail;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        $data[$card_type_low]['default_detail'] = (string) ($default_detail);
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param Gift $objCard            
     * @param array $memo            
     */
    public function recordGift(Gift $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $gift = $objCard->gift;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        $data[$card_type_low]['gift'] = (string) ($gift);
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param Groupon $objCard            
     * @param array $memo            
     */
    public function recordGroupon(Groupon $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $deal_detail = $objCard->deal_detail;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        $data[$card_type_low]['deal_detail'] = (string) ($deal_detail);
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param LuckyMoney $objCard            
     * @param array $memo            
     */
    public function recordLuckyMoney(LuckyMoney $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        $data[$card_type_low]['memo'] = "";
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param MemberCard $objCard            
     * @param array $memo            
     */
    public function recordMemberCard(MemberCard $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $supply_bonus = $objCard->supply_bonus;
        $supply_balance = $objCard->supply_balance;
        $bonus_cleared = $objCard->bonus_cleared;
        $bonus_rules = $objCard->bonus_rules;
        $balance_rules = $objCard->balance_rules;
        $prerogative = $objCard->prerogative;
        $bind_old_card_url = $objCard->bind_old_card_url;
        $activate_url = $objCard->activate_url;
        $need_push_on_view = $objCard->need_push_on_view;
        
        $custom_field1 = $objCard->custom_field1;
        $custom_field2 = $objCard->custom_field2;
        $custom_field3 = $objCard->custom_field3;
        $custom_cell1 = $objCard->custom_cell1;
        $custom_cell2 = $objCard->custom_cell2;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        
        $data[$card_type_low]['supply_bonus'] = $supply_bonus;
        $data[$card_type_low]['supply_balance'] = $supply_balance;
        $data[$card_type_low]['bonus_cleared'] = (string) ($bonus_cleared);
        $data[$card_type_low]['bonus_rules'] = (string) ($bonus_rules);
        $data[$card_type_low]['balance_rules'] = (string) ($balance_rules);
        $data[$card_type_low]['prerogative'] = (string) ($prerogative);
        $data[$card_type_low]['bind_old_card_url'] = (string) ($bind_old_card_url);
        $data[$card_type_low]['activate_url'] = (string) ($activate_url);
        $data[$card_type_low]['custom_field1'] = $custom_field1;
        $data[$card_type_low]['custom_field2'] = $custom_field2;
        $data[$card_type_low]['custom_field3'] = $custom_field3;
        $data[$card_type_low]['custom_cell1'] = $custom_cell1;
        $data[$card_type_low]['custom_cell2'] = $custom_cell2;
        $data[$card_type_low]['need_push_on_view'] = $need_push_on_view;
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param MovieTicket $objCard            
     * @param array $memo            
     */
    public function recordMovieTicket(MovieTicket $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $detail = $objCard->detail;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        
        $data[$card_type_low]['detail'] = (string) ($detail);
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 记录
     *
     * @param ScenicTicket $objCard            
     * @param array $memo            
     */
    public function recordScenicTicket(ScenicTicket $objCard, array $memo = array('orignal_card_id'=>''))
    {
        $card_id = $objCard->base_info->card_id;
        $card_type = $objCard->card_type;
        $base_info = $this->formatBaseInfoData($objCard);
        
        $ticket_class = $objCard->ticket_class;
        $guide_url = $objCard->guide_url;
        
        $info = array();
        if (! empty($card_id)) {
            $info = $this->getInfoByCardId($card_id);
        } else {
            if (! empty($objCard->_id)) {
                $info = $this->getInfoById($objCard->_id);
            }
        }
        
        $data = array();
        $data['card_id'] = (string) $card_id;
        $data['card_type'] = $card_type;
        $data['base_info'] = $base_info;
        
        $card_type_low = strtolower($card_type);
        
        $data[$card_type_low]['ticket_class'] = (string) ($ticket_class);
        $data[$card_type_low]['guide_url'] = (string) ($guide_url);
        
        if (empty($info)) {
            $data['memo'] = $memo;
            return $this->insert($data);
        } else {
            $query = array(
                '_id' => $info['_id']
            );
            $data['memo'] = array_merge($info['memo'], $memo);
            $this->update($query, array(
                '$set' => $data
            ));
            $info = array_merge($info, $data);
            return $info;
        }
    }

    /**
     * 更新卡券ID信息
     *
     * @param string $id            
     * @param string $card_id            
     */
    public function updateCardId($id, $card_id)
    {
        $query = array();
        $query['_id'] = $id;
        $data = array();
        $data['card_id'] = $card_id;
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 生成微信卡券
     *
     * @param array $card            
     * @param array $colors            
     * @throws \Exception
     * @return array
     */
    public function create(array $card, array $colors)
    {
        $type = ($card['date_info_type']);
        $begin_timestamp = ! empty($card['date_info_begin_timestamp']) ? intval($card['date_info_begin_timestamp']->sec) : 0;
        if ($type == 'DATE_TYPE_FIX_TIME_RANGE') {
            $end_timestamp = ! empty($card['date_info_end_timestamp']) ? intval($card['date_info_end_timestamp']->sec) : 0;
        } else {
            $end_timestamp = ! empty($card['date_info_fixed_end_timestamp']) ? intval($card['date_info_fixed_end_timestamp']->sec) : 0;
        }
        $fixed_term = ! empty($card['date_info_fixed_term']) ? intval($card['date_info_fixed_term']) : 0;
        $fixed_begin_term = ! empty($card['date_info_fixed_begin_term']) ? intval($card['date_info_fixed_begin_term']) : 0;
        $date_info = new DateInfo($type, $begin_timestamp, $end_timestamp, $fixed_term, $fixed_begin_term);
        
        $quantity = intval($card['sku_quantity']);
        $sku = new Sku($quantity);
        
        $logo_url = trim($card['logo_url']);
        $brand_name = trim($card['brand_name']);
        $code_type = trim($card['code_type']);
        $title = trim($card['title']);
        $color = trim($card['color']);
        
        if (key_exists(strtoupper($color), $colors)) {
            $color = $colors[strtoupper($color)];
        } else {
            throw new \Exception("{$color}所指定的颜色记录不存在");
        }
        
        $notice = trim($card['notice']);
        $description = trim($card['description']);
        $baseInfo = new BaseInfo($logo_url, $brand_name, $code_type, $title, $color, $notice, $description, $date_info, $sku);
        
        $sub_title = empty($card['sub_title']) ? NULL : trim($card['sub_title']);
        $baseInfo->set_sub_title($sub_title);
        
        $service_phone = empty($card['service_phone']) ? NULL : trim($card['service_phone']);
        $baseInfo->set_service_phone($service_phone);
        
        $use_limit = empty($card['use_limit']) ? 0 : intval($card['use_limit']);
        $baseInfo->set_use_limit($use_limit);
        
        $get_limit = empty($card['get_limit']) ? 0 : intval($card['get_limit']);
        $baseInfo->set_get_limit($get_limit);
        
        $use_custom_code = empty($card['use_custom_code']) ? false : true;
        $baseInfo->set_use_custom_code($use_custom_code);
        
        $bind_openid = empty($card['bind_openid']) ? false : true;
        $baseInfo->set_bind_openid($bind_openid);
        
        $can_share = empty($card['can_share']) ? false : true;
        $baseInfo->set_can_share($can_share);
        
        $can_give_friend = empty($card['can_give_friend']) ? false : true;
        $baseInfo->set_can_give_friend($can_give_friend);
        
        $location_id_list = empty($card['location_id_list']) ? NULL : ($card['location_id_list']);
        $baseInfo->set_location_id_list($location_id_list);
        
        $url_name_type = empty($card['url_name_type']) ? NULL : trim($card['url_name_type']);
        $baseInfo->set_url_name_type($url_name_type); // v2.0废弃
        
        $custom_url_name = empty($card['custom_url_name']) ? NULL : trim($card['custom_url_name']);
        $baseInfo->set_custom_url_name($custom_url_name);
        
        $custom_url = empty($card['custom_url']) ? NULL : trim($card['custom_url']);
        $baseInfo->set_custom_url($custom_url);
        
        $custom_url_sub_title = empty($card['custom_url_sub_title']) ? NULL : trim($card['custom_url_sub_title']);
        $baseInfo->set_custom_url_sub_title($custom_url_sub_title);
        
        $promotion_url_name_type = empty($card['promotion_url_name_type']) ? NULL : trim($card['promotion_url_name_type']);
        $baseInfo->set_promotion_url_name_type($promotion_url_name_type); // v2.0废弃
        
        $promotion_url_name = empty($card['promotion_url_name']) ? NULL : trim($card['promotion_url_name']);
        $baseInfo->set_promotion_url_name($promotion_url_name);
        
        $promotion_url = empty($card['promotion_url']) ? NULL : trim($card['promotion_url']);
        $baseInfo->set_promotion_url($promotion_url);
        
        $promotion_url_sub_title = empty($card['promotion_url_sub_title']) ? NULL : trim($card['promotion_url_sub_title']);
        $baseInfo->set_promotion_url_sub_title($promotion_url_sub_title);
        
        $source = empty($card['source']) ? NULL : trim($card['source']);
        $baseInfo->set_source($source);
        
        $get_custom_code_mode = empty($card['get_custom_code_mode']) ? NULL : trim($card['get_custom_code_mode']);
        $baseInfo->set_get_custom_code_mode($get_custom_code_mode);
        
        // 微信摇一摇
        $can_shake = empty($card['can_shake']) ? false : true;
        $baseInfo->set_can_shake($can_shake);
        
        $shake_slogan_title = empty($card['shake_slogan_title']) ? NULL : trim($card['shake_slogan_title']);
        $baseInfo->set_shake_slogan_title($shake_slogan_title);
        
        $shake_slogan_sub_title = empty($card['shake_slogan_sub_title']) ? NULL : trim($card['shake_slogan_sub_title']);
        $baseInfo->set_shake_slogan_sub_title($shake_slogan_sub_title);
        
        $card_type = strtoupper($card['card_type']);
        switch ($card_type) {
            case 'GENERAL_COUPON':
                // 通用券
                $default_detail = empty($card['general_coupon_default_detail']) ? NULL : trim($card['general_coupon_default_detail']);
                $objCard = new GeneralCoupon($baseInfo, $default_detail);
                break;
            case 'GROUPON':
                // 团购券
                $deal_detail = empty($card['groupon_deal_detail']) ? NULL : trim($card['groupon_deal_detail']);
                $objCard = new Groupon($baseInfo, $deal_detail);
                break;
            case 'DISCOUNT':
                // 折扣券
                $discount = empty($card['discount_discount']) ? 0 : intval($card['discount_discount']);
                $objCard = new Discount($baseInfo, $discount);
                break;
            case 'GIFT':
                // 礼品券
                $gift = empty($card['gift_gift']) ? '' : trim($card['gift_gift']);
                $objCard = new Gift($baseInfo, $gift);
                break;
            case 'CASH':
                // 代金券
                $reduce_cost = empty($card['cash_reduce_cost']) ? 0 : intval($card['cash_reduce_cost']);
                $least_cost = empty($card['cash_least_cost']) ? 0 : intval($card['cash_least_cost']);
                $objCard = new Cash($baseInfo, $reduce_cost);
                $objCard->set_least_cost($least_cost);
                
                break;
            case 'MEMBER_CARD':
                // 会员卡
                $supply_bonus = empty($card['member_card_supply_bonus']) ? false : true;
                $supply_balance = empty($card['member_card_supply_balance']) ? false : true;
                $prerogative = empty($card['member_card_prerogative']) ? NULL : trim($card['member_card_prerogative']);
                
                $bonus_cleared = empty($card['member_card_bonus_cleared']) ? NULL : trim($card['member_card_bonus_cleared']);
                $bonus_rules = empty($card['member_card_bonus_rules']) ? NULL : trim($card['member_card_bonus_rules']);
                $balance_rules = empty($card['member_card_balance_rules']) ? NULL : trim($card['member_card_balance_rules']);
                $bind_old_card_url = empty($card['member_card_bind_old_card_url']) ? NULL : trim($card['member_card_bind_old_card_url']);
                $activate_url = empty($card['member_card_activate_url']) ? NULL : trim($card['member_card_activate_url']);
                
                $objCard = new MemberCard($baseInfo, $supply_bonus, $supply_balance, $prerogative);
                if (! empty($card['member_card_custom_field1_name_type']) && ! empty($card['member_card_custom_field1_url'])) {
                    $custom_field1 = new CustomField($card['member_card_custom_field1_name_type'], $card['member_card_custom_field1_url']);
                    $objCard->set_custom_field1($custom_field1);
                }
                if (! empty($card['member_card_custom_field2_name_type']) && ! empty($card['member_card_custom_field2_url'])) {
                    $custom_field2 = new CustomField($card['member_card_custom_field2_name_type'], $card['member_card_custom_field2_url']);
                    $objCard->set_custom_field2($custom_field2);
                }
                if (! empty($card['member_card_custom_field3_name_type']) && ! empty($card['member_card_custom_field3_url'])) {
                    $custom_field3 = new CustomField($card['member_card_custom_field3_name_type'], $card['member_card_custom_field3_url']);
                    $objCard->set_custom_field3($custom_field3);
                }
                $need_push_on_view = empty($card['member_card_need_push_on_view']) ? NULL : trim($card['member_card_need_push_on_view']);
                if (! empty($card['member_card_custom_cell1_name']) && ! empty($card['member_card_custom_cell1_url'])) {
                    $custom_cell1 = new CustomCell($card['member_card_custom_cell1_name'], $card['member_card_custom_cell1_url']);
                    if (! empty($card['member_card_custom_cell1_tips'])) {
                        $custom_cell1->set_tips($card['member_card_custom_cell1_tips']);
                    }
                    $objCard->set_custom_cell1($custom_cell1);
                }
                if (! empty($card['member_card_custom_cell2_name']) && ! empty($card['member_card_custom_cell2_url'])) {
                    $custom_cell2 = new CustomCell($card['member_card_custom_cell2_name'], $card['member_card_custom_cell2_url']);
                    if (! empty($card['member_card_custom_cell2_tips'])) {
                        $custom_cell2->set_tips($card['member_card_custom_cell2_tips']);
                    }
                    $objCard->set_custom_cell2($custom_cell2);
                }
                $objCard->set_bonus_cleared($bonus_cleared);
                $objCard->set_bonus_rules($bonus_rules);
                $objCard->set_balance_rules($balance_rules);
                $objCard->set_bind_old_card_url($bind_old_card_url);
                $objCard->set_activate_url($activate_url);
                break;
            case 'SCENIC_TICKET':
                // 门票
                $ticket_class = empty($card['scenic_ticket_ticket_class']) ? NULL : trim($card['scenic_ticket_ticket_class']);
                $guide_url = empty($card['scenic_ticket_guide_url']) ? NULL : trim($card['scenic_ticket_guide_url']);
                $objCard = new ScenicTicket($baseInfo);
                $objCard->set_ticket_class($ticket_class);
                $objCard->set_guide_url($guide_url);
                
                break;
            case 'MOVIE_TICKET':
                // 电影票
                $detail = empty($card['movie_ticket_detail']) ? NULL : trim($card['movie_ticket_detail']);
                $objCard = new MovieTicket($baseInfo);
                $objCard->set_detail($detail);
                break;
            case 'BOARDING_PASS':
                // 飞机票
                $from = empty($card['boarding_pass_from']) ? NULL : trim($card['boarding_pass_from']);
                $to = empty($card['boarding_pass_to']) ? NULL : trim($card['boarding_pass_to']);
                $flight = empty($card['boarding_pass_flight']) ? NULL : trim($card['boarding_pass_flight']);
                
                $departure_time = empty($card['boarding_pass_departure_time']) ? NULL : trim($card['boarding_pass_departure_time']);
                $landing_time = empty($card['boarding_pass_landing_time']) ? NULL : trim($card['boarding_pass_landing_time']);
                $check_in_url = empty($card['boarding_pass_check_in_url']) ? NULL : trim($card['boarding_pass_check_in_url']);
                $gate = empty($card['boarding_pass_gate']) ? NULL : trim($card['boarding_pass_gate']);
                $boarding_time = empty($card['boarding_pass_boarding_time']) ? NULL : trim($card['boarding_pass_boarding_time']);
                $air_model = empty($card['boarding_pass_air_model']) ? NULL : trim($card['boarding_pass_air_model']);
                
                $objCard = new BoardingPass($baseInfo, $from, $to, $flight);
                $objCard->set_departure_time($departure_time);
                $objCard->set_landing_time($landing_time);
                $objCard->set_check_in_url($check_in_url);
                $objCard->set_gate($gate);
                $objCard->set_boarding_time($boarding_time);
                $objCard->set_air_model($air_model);
                
                break;
            case 'LUCKY_MONEY':
                // 红包
                $objCard = new LuckyMoney($baseInfo);
                break;
            default:
                throw new \Exception("卡券类别不存在");
                break;
        }
        
        if (empty($this->_weixin)) {
            throw new \Exception("微信接口对象没有设置");
        }
        // var_dump($objCard);
        // die('xxx');
        $ret = $this->_weixin->getCardManager()->create($objCard);
        // Array ( [errcode] => 0 [errmsg] => ok [card_id] => pgW8rt02smNifj51uqt1J3jNQXQY )
        if (! empty($ret['errcode'])) {
            throw new \Exception($ret['errmsg'], $ret['errcode']);
        }
        
        // 更新card_id
        $this->updateCardId(($card['_id']), $ret['card_id']);
        // 将card_id信息返回出去
        $card['card_id'] = $ret['card_id'];
        return $card;
    }

    /**
     * 获取baseInfo信息
     *
     * @param array $base_info            
     * @return \Weixin\Model\BaseInfo
     */
    private function getBaseInfo(array $base_info)
    {
        $card_id = isset($base_info['id']) ? $base_info['id'] : '';
        $logo_url = isset($base_info['logo_url']) ? $base_info['logo_url'] : '';
        $code_type = isset($base_info['code_type']) ? $base_info['code_type'] : '';
        $brand_name = isset($base_info['brand_name']) ? $base_info['brand_name'] : '';
        $title = isset($base_info['title']) ? $base_info['title'] : '';
        $sub_title = isset($base_info['sub_title']) ? $base_info['sub_title'] : '';
        $color = isset($base_info['color']) ? $base_info['color'] : '';
        $notice = isset($base_info['notice']) ? $base_info['notice'] : '';
        $service_phone = isset($base_info['service_phone']) ? $base_info['service_phone'] : '';
        $source = isset($base_info['source']) ? $base_info['source'] : '';
        $description = isset($base_info['description']) ? $base_info['description'] : '';
        $use_limit = isset($base_info['use_limit']) ? intval($base_info['use_limit']) : 0;
        $get_limit = isset($base_info['get_limit']) ? intval($base_info['get_limit']) : 0;
        
        $use_custom_code = (empty($base_info['use_custom_code']) || ($base_info['use_custom_code'] == 'false')) ? false : true;
        $bind_openid = (empty($base_info['bind_openid']) || ($base_info['bind_openid'] == 'false')) ? false : true;
        $can_share = (empty($base_info['can_share']) || ($base_info['can_share'] == 'false')) ? false : true;
        $can_give_friend = (empty($base_info['can_give_friend']) || ($base_info['can_give_friend'] == 'false')) ? false : true;
        
        $location_id_list = empty($base_info['location_id_list']) ? array() : $base_info['location_id_list'];
        $date_info_type = intval($base_info['date_info']['type']);
        $date_info_begin_timestamp = isset($base_info['date_info']['begin_timestamp']) ? intval($base_info['date_info']['begin_timestamp']) : 0;
        $date_info_end_timestamp = isset($base_info['date_info']['end_timestamp']) ? intval($base_info['date_info']['end_timestamp']) : 0;
        $date_info_fixed_term = isset($base_info['date_info']['fixed_term']) ? intval($base_info['date_info']['fixed_term']) : 0;
        $date_info_fixed_begin_term = isset($base_info['date_info']['fixed_begin_term']) ? intval($base_info['date_info']['fixed_begin_term']) : 0;
        $sku_quantity = isset($base_info['sku']['quantity']) ? intval($base_info['sku']['quantity']) : 0;
        $url_name_type = isset($base_info['url_name_type']) ? $base_info['url_name_type'] : ''; // v2.0废弃
        $custom_url_name = isset($base_info['custom_url_name']) ? $base_info['custom_url_name'] : '';
        $custom_url = isset($base_info['custom_url']) ? $base_info['custom_url'] : '';
        $custom_url_sub_title = isset($base_info['custom_url_sub_title']) ? $base_info['custom_url_sub_title'] : '';
        $promotion_url_name_type = isset($base_info['promotion_url_name_type']) ? $base_info['promotion_url_name_type'] : ''; // v2.0废弃
        $promotion_url_name = isset($base_info['promotion_url_name']) ? $base_info['promotion_url_name'] : '';
        $promotion_url = isset($base_info['promotion_url']) ? $base_info['promotion_url'] : '';
        $promotion_url_sub_title = isset($base_info['promotion_url_sub_title']) ? $base_info['promotion_url_sub_title'] : '';
        $status = isset($base_info['status']) ? ($base_info['status']) : "CARD_STATUS_NOT_VERIFY"; // 待审核
        
        $get_custom_code_mode = isset($base_info['get_custom_code_mode']) ? $base_info['get_custom_code_mode'] : '';
        $can_shake = (empty($base_info['can_shake']) || ($base_info['can_shake'] == 'false')) ? false : true;
        
        $shake_slogan_title = isset($base_info['shake_slogan_title']) ? $base_info['shake_slogan_title'] : '';
        $shake_slogan_sub_title = isset($base_info['shake_slogan_sub_title']) ? $base_info['shake_slogan_sub_title'] : '';
        
        $date_info = new DateInfo($date_info_type, $date_info_begin_timestamp, $date_info_end_timestamp, $date_info_fixed_term, $date_info_fixed_begin_term);
        $sku = new Sku($sku_quantity);
        $objBaseInfo = new BaseInfo($logo_url, $brand_name, $code_type, $title, $color, $notice, $description, $date_info, $sku);
        $objBaseInfo->set_sub_title($sub_title);
        $objBaseInfo->set_service_phone($service_phone);
        $objBaseInfo->set_source($source);
        $objBaseInfo->set_use_limit($use_limit);
        $objBaseInfo->set_get_limit($get_limit);
        $objBaseInfo->set_use_custom_code($use_custom_code);
        $objBaseInfo->set_bind_openid($bind_openid);
        $objBaseInfo->set_can_share($can_share);
        $objBaseInfo->set_can_give_friend($can_give_friend);
        $objBaseInfo->set_location_id_list($location_id_list);
        $objBaseInfo->set_url_name_type($url_name_type); // v2.0废弃
        $objBaseInfo->set_custom_url_name($custom_url_name);
        $objBaseInfo->set_custom_url($custom_url);
        $objBaseInfo->set_custom_url_sub_title($custom_url_sub_title);
        
        $objBaseInfo->set_promotion_url_name_type($promotion_url_name_type); // v2.0废弃
        $objBaseInfo->set_promotion_url_name($promotion_url_name);
        $objBaseInfo->set_promotion_url($promotion_url);
        $objBaseInfo->set_promotion_url_sub_title($promotion_url_sub_title);
        
        $objBaseInfo->set_status($status);
        
        $objBaseInfo->set_card_id($card_id);
        $objBaseInfo->set_get_custom_code_mode($get_custom_code_mode);
        $objBaseInfo->set_can_shake($can_shake);
        $objBaseInfo->set_shake_slogan_title($shake_slogan_title);
        $objBaseInfo->set_shake_slogan_sub_title($shake_slogan_sub_title);
        
        return $objBaseInfo;
    }

    /**
     * 记录数据
     *
     * @param array $cardInfo
     *            微信返回的信息
     * @param array $memo            
     * @throws \Exception
     * @return array
     */
    public function record(array $cardInfo, array $memo = array('orignal_card_id'=>''))
    {
        $card_type = strtoupper($cardInfo['card_type']);
        $ret = array();
        switch ($card_type) {
            case 'GENERAL_COUPON':
                // 通用券
                $base_info = $cardInfo['general_coupon']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $default_detail = isset($cardInfo['general_coupon']['default_detail']) ? trim($cardInfo['general_coupon']['default_detail']) : "";
                $objCard = new GeneralCoupon($objBaseInfo, $default_detail);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordGeneralCoupon($objCard, $memo);
                break;
            case 'GROUPON':
                // 团购券
                $base_info = $cardInfo['groupon']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $deal_detail = isset($cardInfo['groupon']['deal_detail']) ? trim($cardInfo['groupon']['deal_detail']) : "";
                $objCard = new Groupon($objBaseInfo, $deal_detail);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordGroupon($objCard, $memo);
                break;
            case 'DISCOUNT':
                // 折扣券
                $base_info = $cardInfo['discount']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $discount = isset($cardInfo['discount']['discount']) ? intval($cardInfo['discount']['discount']) : 0;
                $objCard = new Discount($objBaseInfo, $discount);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordDiscount($objCard, $memo);
                
                break;
            case 'GIFT':
                // 礼品券
                $base_info = $cardInfo['gift']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $gift = isset($cardInfo['gift']['gift']) ? trim($cardInfo['gift']['gift']) : "";
                $objCard = new Gift($objBaseInfo, $gift);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordGift($objCard, $memo);
                
                break;
            case 'CASH':
                // 代金券
                $base_info = $cardInfo['cash']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $least_cost = isset($cardInfo['cash']['least_cost']) ? intval($cardInfo['cash']['least_cost']) : 0;
                $reduce_cost = isset($cardInfo['cash']['reduce_cost']) ? intval($cardInfo['cash']['reduce_cost']) : 0;
                $objCard = new Cash($objBaseInfo, $reduce_cost);
                $objCard->set_least_cost($least_cost);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordCash($objCard, $memo);
                
                break;
            case 'MEMBER_CARD':
                // 会员卡
                $base_info = $cardInfo['member_card']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $supply_bonus = isset($cardInfo['member_card']['supply_bonus']) ? trim($cardInfo['member_card']['supply_bonus']) : false;
                $supply_balance = isset($cardInfo['member_card']['supply_balance']) ? trim($cardInfo['member_card']['supply_balance']) : false;
                $prerogative = isset($cardInfo['member_card']['prerogative']) ? trim($cardInfo['member_card']['prerogative']) : "";
                
                $bonus_cleared = isset($cardInfo['member_card']['bonus_cleared']) ? trim($cardInfo['member_card']['bonus_cleared']) : "";
                $bonus_rules = isset($cardInfo['member_card']['bonus_rules']) ? trim($cardInfo['member_card']['bonus_rules']) : "";
                $balance_rules = isset($cardInfo['member_card']['balance_rules']) ? trim($cardInfo['member_card']['balance_rules']) : "";
                $bind_old_card_url = isset($cardInfo['member_card']['bind_old_card_url']) ? trim($cardInfo['member_card']['bind_old_card_url']) : "";
                $activate_url = isset($cardInfo['member_card']['activate_url']) ? trim($cardInfo['member_card']['activate_url']) : "";
                
                $objCard = new MemberCard($objBaseInfo, $supply_bonus, $supply_balance, $prerogative);
                
                if (! empty($cardInfo['member_card']['custom_field1']['name_type']) && ! empty($cardInfo['member_card']['custom_field1']['url'])) {
                    $custom_field1 = new CustomField($cardInfo['member_card']['custom_field1']['name_type'], $cardInfo['member_card']['custom_field1']['url']);
                    $objCard->set_custom_field1($custom_field1);
                }
                if (! empty($cardInfo['member_card']['custom_field2']['name_type']) && ! empty($cardInfo['member_card']['custom_field2']['url'])) {
                    $custom_field2 = new CustomField($cardInfo['member_card']['custom_field2']['name_type'], $cardInfo['member_card']['custom_field2']['url']);
                    $objCard->set_custom_field2($custom_field2);
                }
                if (! empty($cardInfo['member_card']['custom_field3']['name_type']) && ! empty($cardInfo['member_card']['custom_field3']['url'])) {
                    $custom_field3 = new CustomField($cardInfo['member_card']['custom_field3']['name_type'], $cardInfo['member_card']['custom_field3']['url']);
                    $objCard->set_custom_field3($custom_field3);
                }
                
                $need_push_on_view = empty($cardInfo['member_card']['need_push_on_view']) ? NULL : trim($cardInfo['member_card']['need_push_on_view']);
                if (! empty($cardInfo['member_card']['custom_cell1']['name']) && ! empty($cardInfo['member_card']['custom_cell1']['url'])) {
                    $custom_cell1 = new CustomCell($cardInfo['member_card']['custom_cell1']['name'], $cardInfo['member_card']['custom_cell1']['url']);
                    if (! empty($cardInfo['member_card']['custom_cell1']['tips'])) {
                        $custom_cell1->set_tips($cardInfo['member_card']['custom_cell1']['tips']);
                    }
                    $objCard->set_custom_cell1($custom_cell1);
                }
                if (! empty($cardInfo['member_card']['custom_cell2']['name']) && ! empty($cardInfo['member_card']['custom_cell2']['url'])) {
                    $custom_cell2 = new CustomCell($cardInfo['member_card']['custom_cell2']['name'], $cardInfo['member_card']['custom_cell2']['url']);
                    if (! empty($cardInfo['member_card']['custom_cell2']['tips'])) {
                        $custom_cell2->set_tips($cardInfo['member_card']['custom_cell2']['tips']);
                    }
                    $objCard->set_custom_cell2($custom_cell2);
                }
                $objCard->set_bonus_cleared($bonus_cleared);
                $objCard->set_bonus_rules($bonus_rules);
                $objCard->set_balance_rules($balance_rules);
                $objCard->set_bind_old_card_url($bind_old_card_url);
                $objCard->set_activate_url($activate_url);
                $objCard->set_need_push_on_view($need_push_on_view);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordMemberCard($objCard, $memo);
                
                break;
            case 'SCENIC_TICKET':
                // 门票
                $base_info = $cardInfo['scenic_ticket']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $ticket_class = isset($cardInfo['scenic_ticket']['ticket_class']) ? trim($cardInfo['scenic_ticket']['ticket_class']) : "";
                $guide_url = isset($cardInfo['scenic_ticket']['guide_url']) ? trim($cardInfo['scenic_ticket']['guide_url']) : "";
                $objCard = new ScenicTicket($objBaseInfo);
                $objCard->set_ticket_class($ticket_class);
                $objCard->set_guide_url($guide_url);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordScenicTicket($objCard, $memo);
                
                break;
            case 'MOVIE_TICKET':
                // 电影票
                $base_info = $cardInfo['movie_ticket']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $detail = isset($cardInfo['movie_ticket']['detail']) ? trim($cardInfo['movie_ticket']['detail']) : "";
                $objCard = new MovieTicket($objBaseInfo, $detail);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordMovieTicket($objCard, $memo);
                
                break;
            case 'BOARDING_PASS':
                // 飞机票
                $base_info = $cardInfo['boarding_pass']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $from = isset($cardInfo['boarding_pass']['from']) ? trim($cardInfo['boarding_pass']['from']) : "";
                $to = isset($cardInfo['boarding_pass']['to']) ? trim($cardInfo['boarding_pass']['to']) : "";
                $flight = isset($cardInfo['boarding_pass']['flight']) ? trim($cardInfo['boarding_pass']['flight']) : "";
                
                $departure_time = isset($cardInfo['boarding_pass']['departure_time']) ? trim($cardInfo['boarding_pass']['departure_time']) : "";
                $landing_time = isset($cardInfo['boarding_pass']['landing_time']) ? trim($cardInfo['boarding_pass']['landing_time']) : "";
                $check_in_url = isset($cardInfo['boarding_pass']['check_in_url']) ? trim($cardInfo['boarding_pass']['check_in_url']) : "";
                $gate = isset($cardInfo['boarding_pass']['gate']) ? trim($cardInfo['boarding_pass']['gate']) : "";
                $boarding_time = isset($cardInfo['boarding_pass']['boarding_time']) ? trim($cardInfo['boarding_pass']['boarding_time']) : "";
                $air_model = isset($cardInfo['boarding_pass']['air_model']) ? trim($cardInfo['boarding_pass']['air_model']) : "";
                $objCard = new BoardingPass($objBaseInfo, $from, $to, $flight);
                $objCard->set_departure_time($departure_time);
                $objCard->set_landing_time($landing_time);
                $objCard->set_check_in_url($check_in_url);
                $objCard->set_gate($gate);
                $objCard->set_boarding_time($boarding_time);
                $objCard->set_air_model($air_model);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordBoardingPass($objCard, $memo);
                
                break;
            case 'LUCKY_MONEY':
                // 红包
                $base_info = $cardInfo['lucky_money']['base_info'];
                $objBaseInfo = $this->getBaseInfo($base_info);
                $objCard = new LuckyMoney($objBaseInfo);
                
                $objCard->_id = isset($cardInfo['_id']) ? $cardInfo['_id'] : "";
                $ret = $this->recordLuckyMoney($objCard, $memo);
                break;
            default:
                throw new \Exception("卡券类别不存在");
                break;
        }
        return $ret;
    }

    /**
     * 删除数据
     *
     * @param string $id            
     * @throws \Exception
     * @return array
     */
    public function delCard($id)
    {
        $this->remove(array(
            '_id' => myMongoId($id)
        ));
    }

    protected function formatBaseInfoData(CardBase $objCard)
    {
        $logo_url = $objCard->base_info->logo_url;
        $code_type = $objCard->base_info->code_type;
        $brand_name = $objCard->base_info->brand_name;
        $title = $objCard->base_info->title;
        $sub_title = $objCard->base_info->sub_title;
        $color = $objCard->base_info->color;
        $notice = $objCard->base_info->notice;
        $service_phone = $objCard->base_info->service_phone;
        $source = $objCard->base_info->source;
        $description = $objCard->base_info->description;
        $use_limit = $objCard->base_info->use_limit;
        $get_limit = $objCard->base_info->get_limit;
        $use_custom_code = $objCard->base_info->use_custom_code;
        $bind_openid = $objCard->base_info->bind_openid;
        $can_share = $objCard->base_info->can_share;
        $can_give_friend = $objCard->base_info->can_give_friend;
        $location_id_list = $objCard->base_info->location_id_list;
        $date_info_type = $objCard->base_info->date_info->type;
        $date_info_begin_timestamp = $objCard->base_info->date_info->begin_timestamp;
        $date_info_end_timestamp = $objCard->base_info->date_info->end_timestamp;
        $date_info_fixed_term = $objCard->base_info->date_info->fixed_term;
        $date_info_fixed_begin_term = $objCard->base_info->date_info->fixed_begin_term;
        $sku_quantity = $objCard->base_info->sku->quantity;
        $url_name_type = $objCard->base_info->url_name_type; // v2.0時废弃
        $custom_url_name = $objCard->base_info->custom_url_name;
        $custom_url = $objCard->base_info->custom_url;
        $custom_url_sub_title = $objCard->base_info->custom_url_sub_title;
        
        $promotion_url_name_type = $objCard->base_info->promotion_url_name_type; // v2.0時废弃
        $promotion_url_name = $objCard->base_info->promotion_url_name;
        $promotion_url = $objCard->base_info->promotion_url;
        $promotion_url_sub_title = $objCard->base_info->promotion_url_sub_title;
        
        $status = $objCard->base_info->status;
        
        // 微信摇一摇相关字段
        $get_custom_code_mode = $objCard->base_info->get_custom_code_mode;
        $can_shake = $objCard->base_info->can_shake;
        $shake_slogan_title = $objCard->base_info->shake_slogan_title;
        $shake_slogan_sub_title = $objCard->base_info->shake_slogan_sub_title;
        
        $base_info = array();
        $base_info['logo_url'] = $logo_url;
        $base_info['code_type'] = $code_type;
        $base_info['brand_name'] = $brand_name;
        $base_info['title'] = $title;
        $base_info['sub_title'] = $sub_title;
        $base_info['color'] = strtoupper($color);
        $base_info['notice'] = $notice;
        $base_info['service_phone'] = $service_phone;
        $base_info['source'] = $source;
        $base_info['description'] = $description;
        $base_info['use_limit'] = intval($use_limit);
        $base_info['get_limit'] = intval($get_limit);
        $base_info['use_custom_code'] = $use_custom_code;
        $base_info['bind_openid'] = $bind_openid;
        $base_info['can_share'] = $can_share;
        $base_info['can_give_friend'] = $can_give_friend;
        $base_info['location_id_list'] = $location_id_list;
        $base_info['date_info_type'] = intval($date_info_type);
        $base_info['date_info_begin_timestamp'] = (intval($date_info_begin_timestamp));
        $base_info['date_info_end_timestamp'] = (intval($date_info_end_timestamp));
        $base_info['date_info_fixed_term'] = intval($date_info_fixed_term);
        $base_info['date_info_fixed_begin_term'] = intval($date_info_fixed_begin_term);
        $base_info['sku_quantity'] = intval($sku_quantity);
        $base_info['url_name_type'] = $url_name_type; // v2.0废弃
        $base_info['custom_url_name'] = $custom_url_name;
        $base_info['custom_url'] = $custom_url;
        $base_info['custom_url_sub_title'] = $custom_url_sub_title;
        $base_info['promotion_url_name_type'] = $promotion_url_name_type; // v2.0废弃
        $base_info['promotion_url_name'] = $promotion_url_name;
        $base_info['promotion_url'] = $promotion_url;
        $base_info['promotion_url_sub_title'] = $promotion_url_sub_title;
        $base_info['status'] = ($status);
        
        // 微信摇一摇相关字段
        $base_info['get_custom_code_mode'] = $get_custom_code_mode;
        $base_info['can_shake'] = $can_shake;
        $base_info['shake_slogan_title'] = $shake_slogan_title;
        $base_info['shake_slogan_sub_title'] = $shake_slogan_sub_title;
        
        return $base_info;
    }

    public function getAndUpdateCardInfo($card_id)
    {
        $ret = $this->_weixin->getCardManager()->get($card_id);
        /**
         * Array ( [errcode] => 0 [errmsg] => ok
         * [card] => Array (
         * [card_type] => CASH
         * [cash] => Array (
         * [base_info] => Array (
         * [id] => pgW8rt5vzjJ7nFLYxskYGBtxZP3k
         * [logo_url] => http://mmbiz.qpic.cn/mmbiz/iaAQwicknkictRV7yN2GWSlOz9MynPSR643sqDf2bZQxiaKAwNia8Rjyy83OU4m1Cia6obkjpq2CPDymzMOo2AJ5vT4w/0
         * [code_type] => CODE_TYPE_TEXT
         * [brand_name] => 来伊份
         * [title] => 功能测试
         * [sub_title] => 功能测试
         * [date_info] => Array ( [type] => 1 [begin_timestamp] => 1417708800 [end_timestamp] => 1420041599 )
         * [color] => #10AD61
         * [notice] => 功能测试
         * [service_phone] => 400111111
         * [description] => 功能测试
         * [location_id_list] => Array ( )
         * [get_limit] => 1
         * [can_share] =>
         * [can_give_friend] =>
         * [status] => CARD_STATUS_VERIFY_OK
         * [sku] => Array ( [quantity] => 99 )
         * [create_time] => 1417766130
         * [update_time] => 1417766130
         * [js_oauth_uin_list] => Array ( )
         * )
         * [least_cost] => 100000
         * [reduce_cost] => 1 )
         * )
         * )
         */
        if (! empty($ret['errcode'])) {
            throw new \Exception($ret['errmsg'], $ret['errcode']);
        }
        $cardInfo = $ret['card'];
        $rst = $this->record($cardInfo);
        return $rst;
    }
}
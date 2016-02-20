<?php
namespace Weixin\Model;

/**
 * 飞机票
 */
class BoardingPass extends CardBase
{

    /**
     * from
     * 起点，上限为18 个汉字。
     * 是
     */
    public $from = NULL;

    /**
     * to
     * 终点，上限为18 个汉字。
     * 是
     */
    public $to = NULL;

    /**
     * flight
     * 航班
     * 是
     */
    public $flight = NULL;

    /**
     * departure_time
     * 起飞时间。Unix 时间戳格式。
     * 否
     */
    public $departure_time = NULL;

    /**
     * landing_time
     * 降落时间。Unix 时间戳格式。
     * 否
     */
    public $landing_time = NULL;

    /**
     * check_in_url
     * 在线值机的链接
     * 否
     */
    public $check_in_url = NULL;

    /**
     * gate
     * 登机口。
     * 如发生登机口变更，建议商家实时调用该接口变更。
     * 否
     */
    public $gate = NULL;

    /**
     * boarding_time
     * 登机时间，只显示“时分”不显示日期，按时间戳格式填写。
     * 如发生登机时间变更，建议商家实时调用该接口变更。
     * 否
     */
    public $boarding_time = NULL;

    /**
     * air_model
     * 机型，上限为8 个汉字
     * 否
     */
    public $air_model = NULL;

    public function __construct(BaseInfo $base_info, $from, $to, $flight)
    {
        parent::__construct($base_info);
        $this->card_type = self::$CARD_TYPE["BOARDING_PASS"];
        $this->create_key = 'boarding_pass';
        $this->from = $from;
        $this->to = $to;
        $this->flight = $flight;
    }

    public function set_departure_time($departure_time)
    {
        $this->departure_time = $departure_time;
    }

    public function set_landing_time($landing_time)
    {
        $this->landing_time = $landing_time;
    }

    public function set_check_in_url($check_in_url)
    {
        $this->check_in_url = $check_in_url;
    }

    public function set_gate($gate)
    {
        $this->gate = $gate;
    }

    public function set_boarding_time($boarding_time)
    {
        $this->boarding_time = $boarding_time;
    }

    public function set_air_model($air_model)
    {
        $this->air_model = $air_model;
    }

    protected function getParams()
    {
        $params = array();
        
        if ($this->isNotNull($this->from)) {
            $params['from'] = $this->from;
        }
        if ($this->isNotNull($this->to)) {
            $params['to'] = $this->to;
        }
        if ($this->isNotNull($this->flight)) {
            $params['flight'] = $this->flight;
        }
        if ($this->isNotNull($this->departure_time)) {
            $params['departure_time'] = $this->departure_time;
        }
        if ($this->isNotNull($this->landing_time)) {
            $params['landing_time'] = $this->landing_time;
        }
        if ($this->isNotNull($this->check_in_url)) {
            $params['check_in_url'] = $this->check_in_url;
        }
        if ($this->isNotNull($this->gate)) {
            $params['gate'] = $this->gate;
        }
        if ($this->isNotNull($this->boarding_time)) {
            $params['boarding_time'] = $this->boarding_time;
        }
        if ($this->isNotNull($this->air_model)) {
            $params['air_model'] = $this->air_model;
        }
        return $params;
    }
}

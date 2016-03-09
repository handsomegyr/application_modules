<?php
namespace Webcms\Vote\Models;

class Period extends \Webcms\Common\Models\Vote\Period
{

    /**
     * 根据主题ID获取信息
     *
     * @param string $subject_id            
     * @return array
     */
    public function getInfoBySubject($subject_id)
    {
        $query = array(
            'subject_id' => $subject_id
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据主题ID获取最新的排行期数
     *
     * @param string $subject_id            
     * @return number
     */
    public function getLatestPeriod($subject_id)
    {
        $info = $this->getInfoBySubject($subject_id);
        if (empty($info)) {
            $data = array();
            $data['subject_id'] = $subject_id;
            $data['period'] = 1;
            $info = $this->insert($data);
            return $info['period'];
        } else {
            $options = array(
                "query" => array(
                    "_id" => $info['_id']
                ),
                "update" => array(
                    '$inc' => array(
                        'period' => 1
                    )
                ),
                "new" => true
            );
            $return_result = $this->findAndModify($options);
            return $return_result["value"]['period'];
        }
    }
}
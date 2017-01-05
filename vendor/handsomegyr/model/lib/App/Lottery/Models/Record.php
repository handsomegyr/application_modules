<?php
namespace App\Lottery\Models;

class Record extends \App\Common\Models\Lottery\Record
{

    public function record($activity_id, $user_id, $source, $result_id, $result_msg)
    {
        $datas = array(
            'activity_id' => $activity_id,
            'user_id' => $user_id,
            'source' => $source,
            'result_id' => $result_id,
            'result_msg' => $result_msg
        );
        return $this->insert($datas);
    }

    public function getTotal($activity_id, $user_id, $success = false)
    {
        if ($success == true) {
            return $this->count(array(
                'activity_id' => $activity_id,
                'user_id' => $user_id,
                'result_id' => 1
            ));
        } else {
            return $this->count(array(
                'activity_id' => $activity_id,
                'user_id' => $user_id
            ));
        }
    }
}
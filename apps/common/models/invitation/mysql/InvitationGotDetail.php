<?php
namespace App\Common\Models\Mysql\Invitation;

use App\Common\Models\Mysql\Base;

class InvitationGotDetail extends Base
{

    /**
     * 微信邀请-邀请函领取明细表管理
     * This model is mapped to the table iinvitation_invitationgotdetail
     */
    public function getSource()
    {
        return 'iinvitation_invitationgotdetail';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['got_time'] = $this->changeToMongoDate($data['got_time']);
        return $data;
    }
}
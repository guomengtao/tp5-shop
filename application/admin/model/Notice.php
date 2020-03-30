<?php

namespace app\admin\model;

use think\Cookie;
use think\Model;

class Notice extends model
{

    public function noticed()
    {
        $user_id = Cookie::get("user_id");
        return $this->hasOne('Noticed', 'notice_id', 'id');
    }

}
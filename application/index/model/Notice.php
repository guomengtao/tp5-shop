<?php

namespace app\index\model;

use think\Cookie;
use think\Model;

class Notice extends model
{

    public function noticed()
    {
        $user_id = Cookie::get("user_id");
        return $this->hasOne('Noticed', 'notice_id', 'id')->where('user_id',$user_id);
    }




}
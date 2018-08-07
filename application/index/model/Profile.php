<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Profile extends model
{
    
    public function apple()
    {
        return $this->hasOne('User','id','user_id');
    }


}
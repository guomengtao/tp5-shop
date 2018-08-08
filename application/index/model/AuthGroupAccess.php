<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class AuthGroupAccess extends model
{
 
     public function thec()
    {
        return $this->hasOne('AuthGroup','id','group_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    
    }
 
}
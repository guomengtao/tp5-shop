<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use think\Cookie;

class Wechat extends model
{
    public function setIPAttr($value)
    {
        return request()->ip();
    }


    public function footprint()
    {
        return $this->hasOne('Footprint', 'ip', 'ip');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


}
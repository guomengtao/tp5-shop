<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use think\Cookie;

class Human extends model
{

    public function footprint()
    {
        return $this->hasOne('Footprint', 'ip', 'ip');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


    public function user()
    {
        return $this->hasOne('User', 'ip', 'ip');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }
    public function ipInfo()
    {
        return $this->hasOne('Ipinfo', 'ip', 'ip');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }





}
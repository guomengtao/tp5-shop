<?php

namespace app\admin\model;

use think\Model;

class Message extends model
{

    public function footprint()
    {
        return $this->hasOne('Footprint', 'ip', 'ip');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


}
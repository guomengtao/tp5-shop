<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Order extends model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = ['name' => "游客", 'age' => 17, 'ip' => 17];
    protected $update = ['name'];



    public function setIpAttr($value)
    {
        return request()->ip();
    }

    public function  user(){
        $this->hasOne('user','id','user_id');
    }



    public function setTitleAttr($value)
    {
        return strtolower($value);
    }

    public function setContentAttr($value)
    {
        return strtolower($value);
    }

    public function setShopAttr($value)
    {
        return $value;
    }



    public static function registration_today()
    {
        // 查询今天签到
        $registration = Order::where('body', '=', 135)
            ->whereTime('create_time', 'today')
            ->count();
        return $registration;
    }



    public function getContentAttr($value)
    {
        if ($value) {
            return "<>" . $value;

        }
        return $value;
    }

}
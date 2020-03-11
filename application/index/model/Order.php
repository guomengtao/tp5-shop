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

    public function setNameAttr($value)
    {
        return request()->ip();
        return "30";
    }

    public function setIpAttr($value)
    {
        return request()->ip();
        return "30";
    }

    public function setAgeAttr($value)
    {
        return "20";
    }

    public function setTitleAttr($value)
    {
        // return request()->ip();
        return strtolower($value);
    }

    public function setContentAttr($value)
    {
        return strtolower($value);
    }

    public function setShopAttr($value)
    {
//        dump($value);die();
        return $value;
    }

    public function getBuyAttr($value)
    {
        // 查询播放记录条数
        $play_count = Video::where('shop', '=', Shop)
            ->where('id', '<>', 5)
            ->count();
        $play_count = 999;
        dump($play_count);
        die();
        return 999;

    }

    public static function registration_today()
    {
        // 查询今天签到
        $registration = Order::where('body', '=', 135)
            ->whereTime('create_time', 'today')
            ->count();
        return $registration;
    }

    public function getTitleAttr($value)
    {


        if ($value == 1) {

            return "男";
        } elseif ($value == 2) {

            return "女";
        } else {

            return $value . "";

        }


    }

    public function getContentAttr($value)
    {
        // $title = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        if ($value) {
            # code...
            return "<>" . $value;

        }
        return $value;
    }

}
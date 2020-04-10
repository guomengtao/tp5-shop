<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use think\Cookie;


class Fans extends model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = [];
    protected $update = [];


    public function setIpAttr($value)
    {
        return request()->ip();
    }

    // 粉丝
    public function fansuser()
    {
        return $this->hasOne('User', 'aaaauser_id', 'id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    // 关注
    public function followUser()
    {
        return $this->hasOne('User', 'id', 'follow_id');
    }
    // 关的用户所在地
    public function follow()
    {
        return $this->hasOne('Ipinfo', 'user_id', 'follow_id');
    }


    public function qq()
    {
        // return $this->hasOne('User');
        // return $this->belongsTo('User','user_id','id');
        return $this->hasOne('UserQq', 'user_id', 'user_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }

    // 测试关联点赞表
    public function likes()
    {
        // return $this->hasOne('User');
        // return $this->belongsTo('User','user_id','id');
        return $this->hasOne('Likes', 'user_id', 'user_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function getCheckAttr($value,$data)
    {

        // 查询当前用户有没有关注

        $user_id = $data['user_id'];
        $follow_id = $data['follow_id'];

        // 反向查询是否相互关注了
        $check = Fans::where('user_id',$follow_id)
            ->where('follow_id',$user_id)
            ->count();

        return  $check;
    }


}
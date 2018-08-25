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
    public function fansuser(){

        return $this->hasOne('User','aaaauser_id','id');
         //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }
    // 关注
    public function followuser(){

        return $this->hasOne('UserQq','user_id','follow_id');
    }


   public function userqq(){
        // return $this->hasOne('User');
        // return $this->belongsTo('User','user_id','id');
        return $this->hasOne('UserQq','user_id','user_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


    public function user(){
        // return $this->hasOne('User');
        // return $this->belongsTo('User','aaaauser_id','id');
        return $this->hasOne('User','id','user_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }
    // 测试关联点赞表
    public function likes(){
        // return $this->hasOne('User');
        // return $this->belongsTo('User','user_id','id');
        return $this->hasOne('Likes','user_id','user_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


 
 

}
<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use think\Cookie;


class Data extends model 
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
 	protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = ['name','ip'];
    protected $update = ['name'];


    public function getOnAttr($value,$data)
    {
        // 查询当前用户有没有点赞
        $on = likes::where('data_id','=',$data['id'])
                    ->where('user_id','=',Cookie::get('user_id'))
                    ->count();
       return $on;
    }


    public static function jack(){


       // 查询最新的聊天信息

      $talk_new = Data::with('watermelon,user,dataSelf,likesList')
                    ->withCount('likeslist')
                    ->order('id', 'desc')
                    ->limit(5)
                    ->select();

        return $talk_new;
        return "1008611";
    }

    public static function tom(){

        // 查询最新的聊天信息

      $talk_new = Data::with('watermelon,user,dataSelf,likesList')
                    ->withCount('likeslist')
                    ->order('id', 'desc')
                    ->limit(3)
                    ->select();

     return $talk_new;

         return "10086";

         $this->likes = 10086;
         return $this;
    }

    public static function footStatic(){

        
        return "10086";
       
    }


 
    public function watermelon(){
        return $this->hasOne('Shop','id','shop');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

 
    // 测试 查询关联的多条点赞
    public function likesList()
    {
        return $this->hasMany('likes','data_id');
    }

     public function comments()
    {
        return $this->hasMany('Comment','commentable_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function shop_title(){
        return $this->hasOne('Shop','id','shop');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }
    public function user(){
        // return $this->belongsTo('User');
        // return $this->belongsTo('User','user_id','id');
        return $this->hasOne('User','id','user_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }
    // 关联自己 - 查询回复的哪条留言
    public function dataself(){
        return $this->hasOne('Data','id','age');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }




    public function ipinfo()
    {
        return $this->hasone('Ipinfo','data_id');
    }


    public function userinfo()
    {
        return $this->hasOne('Userinfo','id','id');
    }

    public function getCreate_timeAttr($value)
    {
        return "123";
    }
    public function setNameAttr($value)
    {
        // return request()->ip();
        return "30";
    }
    public function setIpAttr($value)
    {
        // return request()->ip();
        return "30";
    }

 

    public function setTitleAttr($value)
    {
        // return request()->ip();
//        return strtolower($value);
        return $value;
    }

    public function setContentAttr($value)
    {
//        return strtolower($value);
        return $value;
    }

	public function getTitleAttr($value,$data)
    {


        // 查询当前用户有没有点赞
        $on = likes::where('data_id','=',$data['id'])
                    ->where('user_id','=',Cookie::get('user_id'))
                    ->count();
       return $on;
        
 
         $user_id             = Cookie::get('user_id');
         return $value;

    	if ($value==1) {

    		return "男d";
    	}elseif ($value==2) {

    		return "女";
    	} else{

    		return '666123456';

    	}



    }
    public function getContentAttr($value)
    {
        // $title = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
    	if ($value) {
    		# code...
    		return "<>" .$value ;

    	}
        return $value ;
    }

}
<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use think\Cookie;
class User extends model 
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
 	protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = [];
    protected $update = ['name'];




     // 测试 查询单个用户信息模型

    public static function userselfinfo($id){
            // 查询当前用户信息
            
            $user  = User::with('ipinfo','userqq','fans')
                     ->withCount('myblog')
                     ->withCount('fans')
                     ->where('id',$id)
                     ->find();

            return $user;
    }

    public function getFollowedAttr($value,$data)
    {
        
        // 添加关注
        if (input('editfollow') == 1) {


                    $fanscount = Fans::where('user_id',Cookie::get('user_id'))
                                    ->where('follow_id',input('user_id'))
                                    ->count();


                    if (!$fanscount) {

                            // 添加关注
                            $user               = new Fans;
                            $user->user_id      = Cookie::get('user_id');
                            $user->follow_id    = input('user_id');
                            $user->save();
                    }
 
        }
        // 取消关注
        if (input('editfollow') == 2) {


                    // 举例 这样数据库方式不支持软删除
                    // Fans::where('user_id',Cookie::get('user_id'))
                    //                 ->where('follow_id',input('user_id'))
                    //                 ->delete();


                    // 删除状态为0的数据
                    Fans::destroy(['user_id' => Cookie::get('user_id'),'follow_id' => input('user_id')]);
             
        }



        // 查询当前用户有没有 关注
        $on = Fans::where('follow_id','=',input('user_id'))
                    ->where('user_id','=',Cookie::get('user_id'))
                    ->count();
       






       return $on;

    }

     // 测试 查询关联的多条点赞
    public function myblog()
    {
        
        return $this->hasMany('data');
    }

    // 查询关联的多个关注
    public function follow()
    {
        // 查询他关注了用户
        return $this->hasMany('fans','user_id','id');
    }

    // 查询关联的多个粉丝fnsa
    public function fans()
    {
        // 查询那些用户关注了他
        return $this->hasMany('fans','follow_id','id');
    }

    protected function scopeAgetom($query)
    {
        $query->where('age','>',11)->limit(10);
    }

    protected function scopeAgego($query)
    {
        $query->where('age','>',50)->limit(10);
    } 

    public function userqq(){
        return $this->hasOne('userQq');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


    protected function scopeAgeAbove($query, $lowest_age)
    {
        $query->where('age','>',$lowest_age)->limit(10);
        // $query->where('age','>',$lowest_age)->whereTime('update_time','-1 hours')
                // ->order('update_time', 'desc')->where('id','>',394)->limit(10);
    }  

    public function ipinfo(){
        return $this->hasOne('Ipinfo','ip','ip');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function profile()
    {
            // 设置预载入查询方式为JOIN方式
        return $this->hasOne('Profile');
    }

    public function AuthGroupAccess()
    {
        return $this->hasOne('AuthGroupAccess','uid');
    }






    public function apple()
    {
        return $this->hasOne('UserProfile');
    }

    public function userinfo()
    {
        return $this->hasOne('Userinfo','phone','phone');
    }
    public function user_qq()
    {
        return $this->hasOne('UserQq','user_id','id');
    }
    public function money()
    {
        return $this->hasMany('Money');
    }

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

    public function sort(){
        return $this->hasOne('User','invite','id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
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

	public function getTitleAttr($value)
    {
        
 
 

    	if ($value==1) {
    		 
    		return "男";
    	}elseif ($value==2) {
    		 
    		return "女";
    	} else{
    		 
    		return $value ."";

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
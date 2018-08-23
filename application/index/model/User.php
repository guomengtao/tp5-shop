<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
class User extends model 
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
 	protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = [];
    protected $update = ['name'];


     // 测试 查询关联的多条点赞
    public function myblog()
    {
        
        return $this->hasMany('data');
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
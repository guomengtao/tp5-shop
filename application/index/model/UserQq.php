<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
class UserQq extends model 
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
 	protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = [];
    protected $update = [];

    public function ipinfo(){
        return $this->hasOne('Ipinfo','ip','ip');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function profile()
    {
        return $this->hasOne('Profile','phone','phone');
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
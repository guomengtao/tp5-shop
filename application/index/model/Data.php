<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
class Data extends model 
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
 	protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = ['name','ip'];
    protected $update = ['name'];




    public function sort(){
        return $this->hasOne('Ipinfo','phone','phone');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function shop_title(){
        return $this->hasOne('Shop','id','shop');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function foot(){
        return $this->hasOne('Footprint','phone','phone');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


    public function ipinfo()
    {
        return $this->hasone('Ipinfo','data_id');
    }


    public function userinfo()
    {
        return $this->hasOne('Userinfo','phone','phone');
    }

    public function getCreate_timeAttr($value)
    {
        return "123";
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

	public function getTitleAttr($value)
    {
        
 


    	if ($value==1) {

    		return "男d";
    	}elseif ($value==2) {

    		return "女";
    	} else{

    		return $value;

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
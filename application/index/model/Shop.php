<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
class Shop extends model 
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = ['name' => "游客",'age' => 17];  
    protected $update = ['name'];

    public static function course(){

         // 查询最新的聊天信息
        $talk_new = Shop::order('sort', 'asc')
                    ->where('sort','<', '10086')
                    ->paginate(15); 

        return $talk_new;

        return 1;
    }
    public function cha()
    {
        // 这是模型里面
        $data =  Shop::where('id','<>',1)
            ->order('id', 'desc')
            ->paginate(20);
        return $data;
    }

    public function sort(){
        return $this->hasOne('Footprint','url','id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }
    public function footprint(){
        return $this->hasMany('Footprint','goods_id','id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function abc()
    {
        // 这是模型里面
        $data =  Shop::where('id','<>',1)
            ->order('id', 'desc')
            ->paginate(20);
        return $data;
    }

    
    public function setNameAttr($value)
    {
        return request()->ip();
        return "30";
    }

    public function setAgeAttr($value)
    {
        return "30";
    }



    public function setContentAttr($value)
    {
        return $value;
//        return strtolower($value);
    }

    public function getTitleAttr($value)
    {
        
       
        return $value ;

        
    }
    public function getLabelAttr($value)
    {

        // 解析出标签内容为数组
        // $list->label = "w d  d   d        的  的 李四 战三";
        $value = explode(" ", $value);// 空格拆分
        $value = array_filter($value);// 删除空元素

//        dump($value);die();
        return $value ;


    }

    public function getContentAttr($value)
    {
        // $title = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
    	if ($value) {
    		# code...
    		return $value ;

    	}
        return $value ;
    }
    public function getCreate_TimeAttr($value)
    {
        return "33333333";
    }

}
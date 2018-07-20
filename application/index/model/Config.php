<?php
namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Config extends model
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = [];
    protected $update = [];






    public function setLessonAttr($value)
    {
        return request()->ip();
        return "30";
    }
    

    public function setAgeAttr($value)
    {
        return "30";
    }

    public function setTitleAttr($value)
    {
        // return request()->ip();
        return $value;
//        return strtolower($value);
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
    public function getContentAttr($value)
    {
        // $title = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
    	if ($value) {
    		# code...
    		return $value ;

    	}
        return $value ;
    }

}
<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Ipinfo extends model
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
    }
    

    public function setAgeAttr($value)
    {
        return "30";
    }

    public function setTitleAttr($value)
    {
        return $value;
    }

    public function setContentAttr($value)
    {
        return $value;
    }

    public function getTitleAttr($value)
    {
        
       
        return $value ;

        
    }
    public function getContentAttr($value)
    {

        return $value ;
    }
    public function human()
    {
        return $this->hasOne('Human', 'ip', 'ip');
    }
}
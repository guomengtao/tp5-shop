<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Data extends model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];


    public function test()
    {
        return 123;
    }



    public function setAgeAttr($value)
    {
        return "30";
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

    public function getContentAttr($value)
    {
        // $title = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        if ($value) {
            # code...
            return $value;
        }
        return $value;
    }


}
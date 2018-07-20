<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
class Video extends model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = ['name' => "游客",'age' => 17,'ip' => 17];
    protected $update = ['name'];


    public function setNameAttr($value)
    {
        return request()->ip();
        return "30";
    }
    public function setIPAttr($value)
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

    public function getAgeAttr($value)
    {
        // $title = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        if ($value) {
            # code...
            return $value ;

        }else{
            return 40 ;
        }

        return $value ;
    }


}
<?php

namespace app\index\model;

use think\Model;
use think\Cookie;
use traits\model\SoftDelete;
use think\Request;
class Agent extends model
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = ['ip'];
    protected $update = [];



    protected function setIpAttr()
    {
        return request()->ip();
    }





}
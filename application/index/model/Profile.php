<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
class Profile extends model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = [];
    protected $update = ['name'];



}
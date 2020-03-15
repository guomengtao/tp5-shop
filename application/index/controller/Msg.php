<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\index\model\Footprint;
use app\index\model\Human;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;
use QL\QueryList;
use think\Request;

/**
 * 会员中心
 */
class Msg extends Frontend
{

    public function _initialize()
    {
        $user_id = Cookie::get('user_id');
        if($user_id){
            return $this->error('请登录','index/index/login');
        }
    }

    /**
     * 空的请求
     * @param $name
     * @return mixed
     */
    public function _empty($name)
    {
        echo "empty";
    }

    public function index()
    {
        return $this->fetch();
    }
}

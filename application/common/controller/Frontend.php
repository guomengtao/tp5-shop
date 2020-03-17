<?php

namespace app\common\controller;

use think\Controller;
use think\Cookie;
use app\index\model\Footprint;
/**
 * 前台控制器基类
 */
class Frontend extends Controller
{


    public function _initialize()
    {
        $user_id = Cookie::get('user_id');
        // if (!$user_id) {
        //     return $this->error('请登录', 'index/index/login');
        // }
        $home = 'u/'.Cookie::get('user_id');

        $visit      = Footprint::where('path', $home)
            ->where('user_id', '<>', $user_id)
            ->where('msg', '<>', 1)
            ->limit(3)
            ->order('id', 'desc')
            ->select();
        $visitCount = Footprint::where('path', $home)
            ->where('msg', '<>', 1)
            ->where('user_id', '<>', $user_id)
            ->count();
        $this->assign("visit", $visit);
        $this->assign("visitCount", $visitCount);
    }


}

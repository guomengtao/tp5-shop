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


    /**
     * 空的请求
     * @param $name
     * @return mixed
     */
    public function _empty($name)
    {
        echo "empty";
    }

    public function sidebar()
    {
        $sidebar = Cookie::get('sidebar');
        // sidebar-mini sidebar-collapse
        if ($sidebar=='sidebar-mini'){
            Cookie::set('sidebar','sidebar-mini sidebar-collapse','360000');
        }else{
            Cookie::set('sidebar','sidebar-mini','360000');
        }

    }
    public function index()
    {
        $this->assign("title", "首页");
        return $this->fetch();
    }

    public function visit()
    {
        $home    = 'u/'.Cookie::get('user_id');
        $user_id = Cookie::get('user_id');


        // 设置为已读
        Footprint::where('path', $home)
            ->where('msg', '<>', 1)
            ->where('user_id', '<>', $user_id)
            ->update(['msg' => '1']);

        $visits = Footprint::where('path', $home)
            ->where('user_id', '<>', $user_id)
            ->order('id', 'desc')
            ->paginate(5,false,['type'=>'page\Page']);


        $this->assign("visits", $visits);
        $this->assign("visitCount", 0);
        $this->assign("title", "来访会员");


        return $this->fetch();
    }


}

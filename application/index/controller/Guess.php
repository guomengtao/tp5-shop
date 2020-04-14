<?php

namespace app\index\controller;

use app\admin\model\Message;
use app\index\model\Data;
use app\index\model\Fans;
use app\index\model\Mail;
use app\index\model\Notice;
use app\index\model\Noticed;
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
 * 游客
 */
class Guess extends Frontend
{

    public function _initialize()
    {
        parent::_initialize();
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
        $this->assign("title", "首页");
        return $this->fetch();
    }

    public function sidebarShow()
    {
        $sidebar = Cookie::get('sidebar');
        // sidebar-mini sidebar-collapse
        if ($sidebar == 'sidebar-mini') {
            Cookie::set('sidebar', 'sidebar-mini sidebar-collapse', '31536000');
        } else {
            Cookie::set('sidebar', 'sidebar-mini', '31536000');
        }
    }


}

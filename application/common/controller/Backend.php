<?php

namespace app\common\controller;

use app\admin\library\Auth;
use think\Controller;
use think\Cookie;
use app\index\model\Footprint;
/**
 * 后台控制器基类
 */
class Backend extends Controller
{

    public $admin_id = '';

    public function _initialize()
    {
        $this->admin_id = Cookie::get('admin_id');
        if (!$this->admin_id) {
            return $this->error('请登录', 'admin/login/index');
        }
        $visit      = Footprint::where('msg', '<>', 1)
            ->limit(3)
            ->order('id', 'desc')
            ->select();
        $visitCount = Footprint::where('msg', '<>', 1)
            ->count();
        $this->assign("visit", $visit);
        $this->assign("visitCount", $visitCount);
    }


}

<?php

namespace app\common\controller;

use app\admin\model\Message;
use app\index\controller\Member;
use app\index\model\Agent;
use app\index\model\Notice;
use app\index\model\Noticed;
use think\Controller;
use think\Cookie;
use app\index\model\Footprint;

/**
 * 前台控制器基类
 */
class Frontend extends Controller
{
    // 初始化数据

    // 用户ID
    public $user_id = '';

    public function _initialize()
    {
        $user_id = Cookie::get('user_id');

       $this->user_id = $user_id;
        // if (!$user_id) {
        //     return $this->error('请登录', 'index/index/login');
        // }
        $home = 'u/'.Cookie::get('user_id');

        $visit = Footprint::where('path', $home)
            ->where('user_id', '<>', $user_id)
            ->where('msg', '<>', 1)
            ->limit(3)
            ->order('id', 'desc')
            ->select();

        $visitCount = Footprint::where('path', $home)
            ->where('msg', '<>', 1)
            ->where('user_id', '<>', $user_id)
            ->count();

        $messageCount = Footprint::where('module', 'index')
            ->where('controller', 'Msg')
            ->where('action', 'view')
            ->where('msg', null)
            ->count();


        $noticeCount = Notice::count();

        $user_id      = Cookie::get('user_id');
        $noticedCount = Noticed::where('user_id', $user_id)->count();

        $messageCount = $noticeCount - $noticedCount;



        $this->assign("visit", $visit);
        $this->assign("visitCount", $visitCount);
        $this->assign("messageCount", $messageCount);


    }


}

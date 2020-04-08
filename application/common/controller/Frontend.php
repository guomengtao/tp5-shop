<?php

namespace app\common\controller;

use app\admin\model\Message;
use app\index\controller\Member;
use app\index\model\Agent;
use app\index\model\Fans;
use app\index\model\Notice;
use app\index\model\Noticed;
use app\index\model\User;
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

        // 验证token
        if ($user_id) {
            $token       = Cookie::get('token');
            $check_token = User::where('id', $user_id)
                ->where('token', $token)
                ->count();
            if (!$check_token) {
                // 设置Cookie 有效期为 秒
                Cookie::set('phone', '', 1);
                Cookie::set('user_id', '', 1);
                Cookie::set('vip', '', 1);
                Cookie::set('token', '', 1);
                Cookie::set('admin', '', 1);
                Cookie::set('photo', '', 1);
                Cookie::set('nickname', '', 1);
            }
        }

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

         $follow = Fans::where('follow_id',$this->user_id)
            ->where('msg',NULL)
            ->count();






        $noticeCount = Notice::count();

        $noticedCount = Noticed::where('user_id', $user_id)->count();

        $messageCount = $noticeCount - $noticedCount;

        $msg_total =  $messageCount + $follow;
        $this->assign("visit", $visit);
        $this->assign("visitCount", $visitCount);
        $this->assign("messageCount", $messageCount);
        $this->assign("followCount", $follow);
        $this->assign("msg_total", $msg_total);
    }


}

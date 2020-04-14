<?php

namespace app\common\controller;

use app\index\model\Data;
use app\admin\model\Message;
use app\index\controller\Member;
use app\index\model\Agent;
use app\index\model\Fans;
use app\index\model\Mail;
use app\index\model\Notice;
use app\index\model\Noticed;
use app\index\model\User;
use think\Controller;
use think\Cookie;
use app\index\model\Footprint;
use think\Request;

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
        $this->msgCount();
        $this->sidebar();
    }

    public function msgCount()
    {
        $msg     = [];
        $user_id = $this->user_id;
        // if (!$user_id){
        //         //     return false;
        //         // }

        $home = 'index/member/home/user_id/'.$user_id;

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

        $follow = Fans::where('follow_id', $this->user_id)
            ->where('msg', null)
            ->count();


        // 查询评论状态正常的文章
        $commentCount = Data::where('reply_user_id', $user_id)
            ->where('user_id', '<>', $user_id)
            ->where('msg', null)
            ->count();

        $mailCount = Mail::where('to', $user_id)
            ->where('msg', null)
            ->count();

        $noticeCount = Notice::count();

        $noticedCount = Noticed::where('user_id', $user_id)->count();

        $messageCount = $noticeCount - $noticedCount;

        $msg_total = $messageCount + $follow + $commentCount + $mailCount;

        $this->assign("visit", $visit);
        $this->assign("visitCount", $visitCount);
        $this->assign("messageCount", $messageCount);
        $this->assign("followCount", $follow);
        $this->assign("commentCount", $commentCount);
        $this->assign("mailCount", $mailCount);
        $this->assign("msg_total", $msg_total);

        $msg = [
            'visit'        => $visit,
            'visitCount'   => $visitCount,
            'messageCount' => $messageCount,
            'followCount'  => $follow,
            'commentCount' => $commentCount,
            'mailCount"'   => $mailCount,
            'msg_total'    => $msg_total,
        ];
        $this->assign("msg", $msg);
    }

    /**
     * 登录验证
     *
     * 需要的登录的位置用$this->must_log_in()跳转
     */
    public function must_log_in()
    {
        if (!$this->user_id) {
            $this->redirect('index/index/login');
        }

    }


    public function sidebar()
    {
        $arr = [
            '会员中心' => [
                '会员中心' => 'index/user/index',
                '账号设置' => 'index/user/profile',
            ],
            '消息管理' => [
                '通知' => 'index/msg/notice',
                '评论' => 'index/msg/comment',
            ],
            'on'   => [
                '通知' => 'index/msg/notice',
                '评论' => 'index/msg/comment',
            ],
        ];

        $arr = [
            [
                'title'  => '会员中心',
                'url'    => 'User',
                'icon'   => 'tachometer-alt',
                'active' => '',
                'son'    => [
                    ['title' => "会员设置", 'method' => 'profile', 'url' => 'index/user/profile', 'active' => '',],
                    ['title' => "我的关注", 'method' => 'follow', 'url' => 'index/user/follow', 'active' => '',],
                    ['title' => "密码修改", 'method' => 'password', 'url' => 'index/user/password', 'active' => '',],
                ]
            ],
            [
                'title'  => '消息',
                'url'    => 'Msg',
                'icon'   => 'comments',
                'active' => '',
                'son'    => [
                    ['title' => "通知", 'method' => 'notice', 'url' => 'index/msg/notice', 'active' => '',],
                    ['title' => "评论", 'method' => 'comment', 'url' => 'index/msg/comment', 'active' => '',],
                    ['title' => "关注", 'method' => 'follow', 'url' => 'index/msg/follow', 'active' => '',],
                    ['title' => "私信", 'method' => 'mail', 'url' => 'index/msg/mail', 'active' => '',],
                ]
            ],
            [
                'title'  => '推荐功能',
                'url'    => 'Member',
                'icon'   => 'users',
                'active' => '',
                'son'    => [
                     ['title' => "我的积分", 'method' => 'money', 'url' => 'index/member/money', 'active' => '',],
                ],
            ],
        ];

        // 获取当前的控制器 和方法
        $controller = Request::instance()->controller();
        $method     = Request::instance()->action();


        foreach ($arr as $k => $vo) {
            if ($controller == $vo['url']) {
                $arr[$k]['active'] = 'active';
                $father            = $k;
                foreach ($arr[$k]['son'] as $k2 => $vo) {
                    if ($vo['method'] == $method) {
                        $arr[$father]['son'][$k2]['active'] = 'active';
                        // return;
                    }
                }
            }
        }

        $this->assign("sidebar", $arr);
    }


}

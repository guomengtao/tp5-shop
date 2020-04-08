<?php

namespace app\index\controller;

use app\admin\model\Message;
use app\index\model\Fans;
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
        if ($sidebar == 'sidebar-mini') {
            Cookie::set('sidebar', 'sidebar-mini sidebar-collapse', '31536000');
        } else {
            Cookie::set('sidebar', 'sidebar-mini', '31536000');
        }
    }

    public function index()
    {
        $this->assign("title", "首页");
        return $this->fetch();
    }

    public function view()
    {
        $id      = input('id') ?: 1;
        $message = Notice::where('id', $id)->find();

        // 查询是否已读这条通知
        $noticeCount = Noticed::where('notice_id', $id)
            ->where('user_id', $this->user_id)
            ->count();

        if (!$noticeCount) {
            $info['notice_id'] = $id;
            $info['user_id']   = $this->user_id;

            $user = new Noticed();
            $user->data($info);
            $user->save();
        }


        $this->assign("title", '通知:'.$message['title']);
        $this->assign("message", $message);
        return $this->fetch();
    }

    public function message()
    {
        $this->assign("title", "最新消息");
        return $this->fetch();
    }

    public function follow()
    {
        // 全部设置为已读
        Fans::where('follow_id', $this->user_id)
         ->update(['msg' => '1']);




        $follow = Fans::where('follow_id', $this->user_id)
            ->order('id desc')
            ->paginate(9);


        $this->assign("follow", $follow);
        $this->assign("title", "我的粉丝");
        return $this->fetch();
    }

    public function notice()
    {
        $message = Notice::order('id', 'desc')
            ->paginate(5);


        $this->assign("message", $message);
        $this->assign("title", "通知");
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
            ->paginate(5, false, ['type' => 'page\Page']);


        $this->assign("visits", $visits);
        $this->assign("visitCount", 0);
        $this->assign("title", "来访会员");


        return $this->fetch();
    }


}

<?php

namespace app\admin\controller;

use app\admin\model\Notice;
use app\common\controller\Backend;
use app\index\model\Footprint;
use app\admin\model\Message;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;

/**
 * 会员中心
 */
class Msg extends Backend
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
        return $sidebar;
    }

    public function index()
    {
        $this->assign("title", "首页");
        return $this->fetch();
    }

    public function send()
    {
        // 是否为 POST 请求
        if (request()->isPost()) {
            $title   = input("title");
            $content = input("content");

            if (!trim($title)) {
                return;
            }

            $info['title']= $title;
            $info['content']= $content;

            $message = new Notice();
            $message->data($info);
            $message->save();

        }

        $this->assign("title", "发布通知/公告");
        return $this->fetch();
    }

    public function visit()
    {
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

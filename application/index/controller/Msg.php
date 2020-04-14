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
 * 会员中心
 */
class Msg extends Frontend
{

    public function _initialize()
    {
        parent::_initialize();

        $this->must_log_in();
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

    public function mail_view()
    {
        $group_id = input('group_id');
        $from     = input('from');

        // 是否为 POST 请求
        if (request()->isPost()) {
            $to    = input('from');
            $title = trim(input('title'));

            // 发私信功能
            if ($to and $title) {
                $data = Mail::send();


                if ($data) {
                    // $this->redirect();
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('发布成功');
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('请填写内容');
                }
            }
        }


        $data = Mail::where('group_id', $group_id)
            ->paginate(100);


        // 全部设置为已读
        Mail::where('group_id', $group_id)
            ->where('to', $this->user_id)
            ->update(['msg' => '1']);


        $this->assign("from", $from);
        $this->assign("msg_null_all", input('msg_null_all'));
        $this->assign("data", $data);
        $this->assign("title", "私信详情");
        return $this->fetch();
    }


    public function mail()
    {


        $data = Mail::order('id', 'desc')
            ->where('to|from', $this->user_id)
            ->group('group_id')
            ->paginate(5);


        $this->assign("data", $data);
        $this->assign("title", "我的私信");
        return $this->fetch();
    }

    public function comment()
    {
        // 是否为 POST 请求
        if (request()->isPost()) {
            $add = Data::add();

            if ($add) {
                // $this->redirect();
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                $this->success('发布成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
                $this->error('请填写内容');
            }
        }

        // 全部设置为已读
        Data::where('reply_user_id', $this->user_id)
            ->update(['msg' => '1']);


        $comment = Data::where('reply_user_id', $this->user_id)
            ->where('user_id', '<>', $this->user_id)
            ->order('id desc')
            ->paginate(5);


        $this->assign("data", $comment);
        $this->assign("title", "收到的评论");
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

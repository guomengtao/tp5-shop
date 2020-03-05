<?php

namespace app\index\controller;


use think\Db;
use think\Request;
use app\index\model\Data;
use app\index\model\User;
use app\index\model\Footprint;
use app\index\model\Ipinfo;
use think\Cookie;
use think\Session;

class Bbs extends \think\Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        ob_clean();
    }

    public function admin()
    {

        //      调用浏览记录和来路统计功能
        footprint();

        return $this->fetch();


    }

    public function show()
    {

        //      调用浏览记录和来路统计功能
        footprint();


        // DB写法
        // $show = Db::name('data')->where('id','>',0)->order('id', 'desc')->paginate(10,80);

        // dump($show);
        // 模型写法
        $show = Data::with('user')->where('id', '>', 0)->order('id', 'desc')->paginate(10);

        // $show = $show->toArray();


        $captcha = input("captcha");
        if ($captcha=="cancel") {

            if (!captcha_check($captcha)) {
                //验证失败
                $this->error('验证码错误');

            } else {
                // $this->success('验证码正确');

            };


        }


        // dump($show);
        // exit();
        $this->assign('show', $show);
        // 渲染模板输出

        return $this->fetch();


    }

    public function view()
    {

        //      调用浏览记录和来路统计功能
        footprint();

        //echo input('param.id');

        $id = input('id');

        if ($id <> '') {


            // 查询数据 - 查询留言详情内容
            $list = Db::name('data')
                ->where('id', '=', $id)
                ->select();
            //dump($list);

            // 查询数据 - 上一页
            //echo $id;
            $up = Db::name('data')
                ->where('id', '>', $id)
                ->order('id', '')
                ->limit(1)
                ->value('id');
            //dump($up);

            // 查询数据 - 下一页
            $next = Db::name('data')
                ->where('id', '<', $id)
                ->order('id', 'desc')
                ->limit(1)
                ->value('id');

            //dump($next);

            $this->assign('up', $up);
            $this->assign('next', $next);
            $this->assign('list', $list);


            // 渲染模板输出
            return $this->fetch();


        }
        return $this->fetch('no');
        return "留言不存在";

    }

    public function add()
    {


        $title   = trim(input('title'));
        $phone   = Cookie::get('phone');
        $user_id = Cookie::get('user_id');
        $user_id = $user_id?$user_id:1;
        $captcha = input("captcha");


        if (!$title) {
            # code...
            $this->error('留言内容不能为空');

        }


        if ($captcha=="cancel") {

            $this->error('验证码不能为空');
        }


        if (!$phone) {
            $phone = "15966982315";
        }


        //限制一下最大长度，预防来发个非常长的。
        //mb_substr 针对中文的解决
        //mb_substr要开启php.ini里面extension=php_mbstring.dll扩展 一般默认偶开启了
        $title = mb_substr($title, 0, 100, "UTF-8");


        if (captcha_check($captcha)== "cancel") {
            //验证失败
            $this->error('验证码错误');

        }


        // 模型的 静态方法
        $user = Data::create([
            'title'   => $title,
            'user_id' => $user_id,
            'shop' => 0,
        ]);


        return $this->success('恭喜您留言成功^_^', 'bbs/show');


    }


    public function ajax()
    {

        //      调用浏览记录和来路统计功能
        footprint();
        return $this->fetch();


    }
}
 
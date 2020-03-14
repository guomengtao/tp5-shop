<?php

namespace app\index\controller;

use app\index\model\User;
use think\Session;

class Ajax extends \think\Controller
{
    public function ajax()
    {

        $page = input('page');

        // return "来自ajxa调用的 ，当前是第" .$page ."页";


        $show = User::where('id', '>', 1)
            ->order('id', 'desc')
            ->paginate(15);

        $this->assign('show', $show);
        // 渲染模板输出1

        return $this->fetch();
    }

    /**
     * 设置在session中的关闭
     * 可以加一个参数已方便多处调用
     */
    public  function alertHidden(){
        Session::set("alertHidden",1);
        return true;
    }

    public function ajaxrun()
    {


        $show = User::where('id', '>', 1)
            ->order('id', 'desc')
            ->paginate(15);

        $this->assign('show', $show);
        // 渲染模板输出

        return $this->fetch();

    }
}

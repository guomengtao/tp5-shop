<?php

namespace app\index\controller;

use app\index\model\User;

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

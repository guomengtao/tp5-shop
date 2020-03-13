<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Rq extends Controller
{

    public function index(Request $request)
    {

        $request = Request::instance();
        echo '请求方法：' . $request->method() . '<br/>';
        echo '资源类型：' . $request->type() . '<br/>';
        echo '访问ip地址：' . $request->ip() . '<br/>';
        echo '是否AJax请求：' . var_export($request->isAjax(), true) . '<br/>';
        echo '请求参数：';
        dump($request->param());
        echo '请求参数：仅包含name';
        dump($request->only(['name']));
        echo '请求参数：排除name';
        dump($request->except(['name']));
// 获取当前请求的name变量
        echo Request::instance()->param('name');

        echo "---++++-";
        echo input('post.name');
        echo "-------------";
        echo input('get.name');

        echo "-------------";
        echo input('post.name/d');


        return $this->fetch('rq');
    }
}
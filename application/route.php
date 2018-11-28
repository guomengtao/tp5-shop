<?php
use think\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 注册路由到index模块的News控制器的read操作


// Route::rule('demo','index/member/tip');

// 注册路由到index模块的News控制器的read操作
// Route::rule('tom','index/index/index');
Route::get('u/:user_id','index/member/home',['ext'=>'']);


 

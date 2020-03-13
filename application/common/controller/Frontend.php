<?php

namespace app\common\controller;

use think\Config;
use think\Controller;

/**
 * 前台控制器基类
 */
class Frontend extends Controller
{


    public function _initialize()
    {
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');

    }


}

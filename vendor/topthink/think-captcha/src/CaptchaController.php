<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\captcha;

use think\Config;

class CaptchaController
{
    public function index($id = "")
    {
        // 修改thinkphp核心文件，解决验证码不显示问题
        // 修改核心文件，更新composer的时候会覆盖回去的
        ob_clean();

        $captcha = new Captcha((array)Config::get('captcha'));
        return $captcha->entry($id);
    }
}
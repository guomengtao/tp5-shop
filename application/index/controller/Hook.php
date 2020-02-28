<?php


namespace app\index\controller;

/**
 * Class webHook 自动pull，替换FTP方式
 * @package app\index\controller
 */
class Hook
{
    public function run()
    {
        echo "开始拉取 ";

        echo exec("git pull");

        echo "拉取成功";
        // echo exec("git clone https://gitee.com/rinuo/phpmanual.git");

    }
}
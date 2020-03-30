<?php

namespace app\admin\controller;



use app\admin\model\Admin;
use think\Controller;
use think\Cookie;
use think\Request;
/**
 * 登录后台
 */
class Login extends Controller
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

    public function index()
    {
        $warning      = "";
        $phone        = "";
        $get_password = "";

        // 登录功能
        if (Request::instance()->isPost()) {
            $username = input('username');
            $password = input('password');

            if (!trim($username)) {
                $warning = "账号密码不能为空";

            }


            $get_token = Admin::where('username', $username)->value('token');

            // 先判断用户是否存在
            if (!$get_token) {
                $warning = "管理账号不存在";
            }

            if ($get_token) {
                // 查询密码和账号是否正确
                $get_password = Admin::where('password', '=', md5(trim($password)))
                    ->where('username', $username)
                    ->count();

                if (!$get_password) {
                    // 此处可以加一个Session或者数据库加一个记录，记录密码错误次数
                    $warning = "账号或密码不正确";
                }
            }

            // 确认账号密码一致开始登录操作

            if ($get_password) {
                $admin_id = Admin::where('username', '=', $username)->value('id');

                Cookie::set('admin_username', $username, 3600000);
                Cookie::set('admin_token', $get_token, 3600000);
                Cookie::set('admin_id', $admin_id, 3600000);

                // 跳转之前再加一个验证是否是管理员身份  此处略
                return $this->success('管理员您好^_^', 'admin/index/index');
            }
        }

        $this->assign('warning', $warning);
        $this->assign('title', '后台登录');

        return view('login');
    }



}

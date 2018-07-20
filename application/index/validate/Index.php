<?php
namespace app\index\validate;

use think\Validate;

class Index extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:25|unique:admin',
        'email'  =>  'require|min:5',
    ];

    protected $message  =   [
        'username.require' => '用户名不能为空！',  
        'username.unique' => '用户名不能重复！',  
        'username.max' => '用户名不能大于25位！', 
        'password.require' => '密码不能为空！', 
        'password.min' => '密码不能少于5位！', 
        // 'password.number' => '密码必须是数字类型！', 

    ];

    protected $scene = [
        'edit'  =>  ['username'],
    ];


}
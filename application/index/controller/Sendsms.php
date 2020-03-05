<?php

namespace app\index\controller;

use think\Db;
use think\Request;


class Api extends \think\Controller
{
    /*--------------------------------
    '说明:	https://api.chanyoo.net/sendsms?username=demo&password=demo&mobile=13333333333&content=content
    '状态:
    '>=0	OK					短信提交成功
    '-100	系统内部错误		系统内部错误
    '-101	系统维护请稍后再试	系统维护请稍后再试
    '-102	API接口帐号不存在	API接口帐号不存在
    '-103	API接口调用密码错误	API接口调用密码错误
    '-110	参数错误请注意核实	参数错误请注意核实
    '-118	内容超过最大长度	内容超过最大长度
    '-119	手机号不符合规则	手机号不符合规则
    '-777	短信签名不正确		短信签名不正确
    '-888	短信模板未报备		短信模板未报备
    '-999	请求频繁请稍后再试	连续提交相同的手机号和短信内容
    --------------------------------*/
                                                                                         //输出返回结果

    /*
    $ret = sendSMS($username,$password,$mobile,$content);
    echo $ret;
    */
    function send(){
        $username = 'guomengtao1';                //请修改成你的平台帐号
        $password = 'KEYxtybMMdN';                //请修改成你的调用密码
        $mobile   = '13333333333';        //你接收短信的手机号码
        $content  = '您的手机号：13012345678，验证码：110426，请及时完成验证，如不是本人操作请忽略。';        //发送短信内容
        $ret      = $this->sendSMS($username, $password, $mobile, $content);                                                //调用接口发送
        echo $ret;
    }
    function sendSMS($usename, $password, $mobile, $content)
    {
        $content = urlencode($content);
        $url     = 'http://api.chanyoo.net/sendsms?username=' . $usename . '&password=' . $password . '&mobile=' . $mobile . '&content=' . $content . '';
        if (function_exists('file_get_contents')) {
            $result = file_get_contents($url);
        } else {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        $result = json_decode($result, true);
        return $result['result'];
    }
}
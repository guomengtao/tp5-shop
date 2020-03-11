<?php

namespace app\index\controller;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


use app\index\model\Money;
use think\Db;
use think\Request;
use app\index\model\Shop;
use app\index\model\Video;
use app\index\model\User;
use app\index\model\Sms;
use app\index\model\Order;
use app\index\model\Data;
use app\index\model\Footprint;
use app\index\model\Ipinfo;
use think\Cookie;
use think\Session;
use think\Validate;
use Ip2Region;


function ip1region()
{
    $request = Request::instance();

    $ip = $request->ip();

    if (!$ip or $ip == '127.0.0.1') {
        return "ok";
    }

    $ip2region = new Ip2Region();


    $info = $ip2region->btreeSearch($ip);

    $check   = Ipinfo::where('ip', $ip)->count();
    $user_id = Cookie::get('user_id');

    if ($check) {

        $user = new Ipinfo;
        // save方法第二个参数为更新条件
        $user->save([
            'user_id' => $user_id,
        ], ['ip' => $ip]);
        return '';
    }


    $isp = $info['city_id'];
    $pos = $info['region'];

    if ($pos) {
        Ipinfo::create([
            'isp'     => $isp,
            'region'  => $pos,
            'ip'      => $ip,
            'user_id' => $user_id,
        ]);
    }


}

function quickLogon()
{
    // $openid = "1011";

    $user = new User_qq();
    // 查询单个数据
    $user = $user->where('openid', $openid)
        ->find();


    // 没登记openID的先登记
    if (!$user) {
        # code..

        $user                 = new User_qq;
        $user->openid         = $openid;
        $user->nickname       = $user_from_qq->nickname;
        $user->figureurl_qq_1 = $user_from_qq->figureurl_qq_1;
        $user->figureurl_qq_2 = $user_from_qq->figureurl_qq_2;
        $user->gender         = $user_from_qq->gender;
        $user->year           = $user_from_qq->year;
        $user->save();

        // return "创建成功！";

        // 查询单个数据
        $user = $user->where('openid', $openid)
            ->find();

    } else {

        // 如果已经存在就更新，保持数据最新  暂时不更新
    }


    // 记录昵称和头像，页面展示
    cookie('nickname', $user_from_qq->nickname, 3600000);
    cookie('figureurl_qq_2', $user_from_qq->figureurl_qq_2, 3600000);
    return "ok123456";
}

function get_user_id($phone)
{
    $user_id = User::where('phone', '=', $phone)->value('id');
    return $user_id;
}

function arraySequence($array, $field, $sort = 'SORT_DESC')
{
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort['view_count'], constant($sort), $array);
    return $array;
}


// 验证课程播放少于10次
function play_count($shop)
{

    // 查询播放记录条数
    $play_count = Video::where('shop', '=', $shop)
        ->count();

    return $play_count;

}


// 验证是否达到所有课程免费的功能
function all_lesson_free()
{


//    说明：
//    有多种情况可以设置为课程免费
//
//
//    例如 每日签到满10人
    $all_lesson_free = 0;


    //        查询今天有多少人签到了
    $registration_count = Order::whereTime('create_time', 'today')
        ->where('body', '=', 135)
        ->where('phone', '<>', '15966982315')
        ->count();


    if ($registration_count >= 10) {
        return 1;
    }


    // 简易全民学习功能  每晚8点-10点免费开放
    // date_default_timezone_set("Asia/Shanghai");

    // 判断当前是几点几分 915是9点15
    $secondkill = intval(date("Hi"));

    if ($secondkill > "2000" && $secondkill < "2200") {
        // code
        return 0;
    }


    return 0;

}

// 增加会员vip天数功能

function add_vip_days($add_vip_days, $out_trade_no)
{


    $phone = Cookie::get('phone');

    // 首先查询他的到期日期大于现在

    // 处理没有登录先付款的情况
    if (!$phone) {
        $phone = '15966982315';
    }


    // 把时间增加上 ，忽略开始日期字段
    // 日期转秒数 60*60*24=1天的秒数


    $expiration_time = User::where('phone', $phone)
        ->whereTime('expiration_time', '>=', '-1 minute')
        ->value('expiration_time');

    $add_vip_days_time = $add_vip_days * 3600 * 24;


    // 判断是否过期
    if ($expiration_time) {

        $expiration_time = $expiration_time + $add_vip_days_time;

        User::where('phone', $phone)
            ->update(['expiration_time' => $expiration_time, 'rand' => 1]);
    } else {
        $expiration_time = time() + $add_vip_days_time;

        User::where('phone', $phone)
            ->update(['expiration_time' => $expiration_time, 'start_time' => time(), 'rand' => 1]);
    }


    // 写入订单
    $order = Order::create([
        'phone'        => $phone,
        'body'         => 105,
        'subject'      => "增加VIP会员：" . $add_vip_days . "天",
        'total_fee'    => 0,
        'buyer_id'     => $phone,
        'buyer_email'  => $phone,
        'out_trade_no' => $out_trade_no,
    ]);


}

//功能浏览次数和来路
function footprint()
{

    $info = [];

    $info['user_id'] = Cookie::get('user_id');

    $request = Request::instance();


    $ip   = $request->ip();
    $view = $request->module() . $request->controller() . $request->action();

    // 如果是产品详情页，记录一下访问的产品id，$goods_id 
    if ($view == 'indexIndexview') {
        $info['goods_id'] = input('id');
    }

    //  模块控制器和方法
    $info['path'] = $request->path();
    //  获取ip地址
    $info['ip'] = $request->ip();
    Session::get('start_session_working');
    $info['session_id'] = session_id();

    $user = new Footprint;
    $user->data($info);
    $user->save();

    ip1region();
}


// 验证用户是否token功能
function token()
{
    $user  = Cookie::get('phone');
    $token = Cookie::get('token');
    // dump($token);
    if ($user) {

        // 判断是否其他浏览器或者设备登录（设置每次登录修改token时有效）
        // 判断Cookie里的token的否正确
        $token_count = User::where('phone', '=', $user)
            ->where('token', '=', $token)
            ->count();
        if ($token_count <= 0) {

            Cookie::set('phone', '', 36000000);
            Cookie::set('token', '', 36000000);
            $user  = "";
            $token = "";

        }
    }
}

//人性化时间显示
function formatTime($time)
{
    return $time;
    $rtime = date("m月d日 H:i", $time);
    $htime = date("H:i", $time);
    $year  = date("Y") - date("Y", $time);
    $time  = time() - $time;

    if ($time < 60) {
        $str = '刚刚';
    } elseif ($time < 60 * 60) {
        $min = floor($time / 60);
        $str = $min . '分钟前';
    } elseif ($time < 60 * 60 * 24) {
        $h   = floor($time / (60 * 60));
        $str = $h . '小时前 ';
    } elseif ($time < 60 * 60 * 24 * 3) {
        $d = floor($time / (60 * 60 * 24));
        if ($d == 1) {
            $str = '昨天 ' . $rtime;
        } else {
            $str = '前天 ' . $rtime;
        }
    } elseif ($year > 0) {
        $str = $rtime;
    } else {
        $str = date("Y年m月d日 H:i", $time);
    }
    return $str;
}

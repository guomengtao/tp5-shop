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
use think\Validate;

/**
 * 二维数组根据字段进行排序
 * @params array $array 需要排序的数组
 * @params string $field 排序的字段
 * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 */
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
            $user->nickname       = $user_from_qq->nickname ;
            $user->figureurl_qq_1 = $user_from_qq->figureurl_qq_1 ;
            $user->figureurl_qq_2 = $user_from_qq->figureurl_qq_2 ;
            $user->gender         = $user_from_qq->gender ;
            $user->year           = $user_from_qq->year ;
            $user->save();

            // return "创建成功！";

            // 查询单个数据
            $user = $user->where('openid', $openid)
            ->find();

        }else{

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
function play_count($shop){

    // 查询播放记录条数
    $play_count =  Video::where('shop','=',$shop)
        ->count();

    return $play_count;

}


// 查询页面浏览次数
// function view_count($shop){

//     // 查询播放记录条数
//     $url = "/index/index/view/id/". $shop;
//     // $view_count =  1;
//     $view_count =  Footprint::where('url','=',$url)
//         ->count();

//     return $view_count;

// }


// 验证是否达到所有课程免费的功能
function all_lesson_free(){


//    说明：
//    有多种情况可以设置为课程免费
//
//
//    例如 每日签到满10人
    $all_lesson_free    =  0;


        //        查询今天有多少人签到了
        $registration_count  = Order::whereTime('create_time', 'today')
        ->where('body','=',135)
        ->where('phone','<>','15966982315')
        ->count();


        if ( $registration_count >=10) {
           return 1;
        }


        // 简易全民学习功能  每晚8点-10点免费开放
        // date_default_timezone_set("Asia/Shanghai");

        // 判断当前是几点几分 915是9点15
        $secondkill = intval (date("Hi"));
         
        if ($secondkill > "2000" && $secondkill < "2200") {
          // code
          return 0;
        }




        return 0;

}

// 增加会员vip天数功能

function add_vip_days($add_vip_days,$out_trade_no){


    $phone      = Cookie::get('phone');

    // 首先查询他的到期日期大于现在

    // 处理没有登录先付款的情况
    if (!$phone){
        $phone='15966982315';
    }



    // 把时间增加上 ，忽略开始日期字段
    // 日期转秒数 60*60*24=1天的秒数


    $expiration_time = User::where('phone', $phone)
        ->whereTime('expiration_time', '>=', '-1 minute')
        ->value('expiration_time');

        $add_vip_days_time = $add_vip_days *3600 *24;



    // 判断是否过期
    if ($expiration_time) {

        $expiration_time = $expiration_time + $add_vip_days_time;

        User::where('phone', $phone)
            ->update(['expiration_time' => $expiration_time, 'rand' => 1]);
    } else {
        $expiration_time = time() + $add_vip_days_time ;

        User::where('phone', $phone)
            ->update(['expiration_time' => $expiration_time, 'start_time' => time(), 'rand' => 1]);
    }


    // 写入订单
    $order = Order::create([
    'phone'                    =>  $phone,
    'body'                     =>  105,
    'subject'                  =>  "增加VIP会员：".$add_vip_days."天",
    'total_fee'                =>  0,
    'buyer_id'                 =>  $phone,
    'buyer_email'              =>  $phone,
    'out_trade_no'             =>  $out_trade_no,
    ]);





}

//功能浏览次数和来路
function footprint(){

    debug('begin');

    $referer    = '';
    $domain     = '';
    $pathinfo   = '';
    $mobile     = '';
    $address    = '';
    $browser    = '';
    $user_id      = Cookie::get('user_id');


 $request = Request::instance();

 

    $goods_id = '';

    $view = $request->module().$request->controller().$request->action();
 
    // 如果是产品详情页，记录一下访问的产品id，$goods_id 

    if ($view == 'indexIndexview') {
        # code...
        $goods_id = input('id');
    }



    if (isset($_SERVER["HTTP_REFERER"])) {

//        统计来路功能
        $se = 0;
        $url = $_SERVER["HTTP_REFERER"]; //获取完整的来路URL


        $str = str_replace("http://","",$url); //去掉http://
        $strdomain = explode("/",$str); // 以“/”分开成数组
        $domain = $strdomain[0]; //取第一个“/”以前的字符
        if(strstr($domain,'baidu.com')){
            $se = 1;
        }
        else if(strstr($domain,'google.cn')){
            $se = 1;
        }

        $referer  = $str;


    }

    $request = Request::instance();

//  去掉http://后的所有url
    $url = $request->url();
//  模块控制器和方法
    $pathinfo = $request->path();
//  获取ip地址
    $ip = $request->ip();

// 判断是否是手机
    if(Request::instance()->isMobile()){
        $mobile    = 1;
    }else{
        $mobile    = 0;
    }




//    获取是什么浏览器和浏览器的版本号
    $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
    if (stripos($sys, "Firefox/") > 0) {
        preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
        $exp[0] = "Firefox";
        $exp[1] = $b[1];  //获取火狐浏览器的版本号
    } elseif (stripos($sys, "Maxthon") > 0) {
        preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
        $exp[0] = "傲游";
        $exp[1] = $aoyou[1];
    } elseif (stripos($sys, "MSIE") > 0) {
        preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
        $exp[0] = "IE";
        $exp[1] = $ie[1];  //获取IE的版本号
    } elseif (stripos($sys, "OPR") > 0) {
        preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
        $exp[0] = "Opera";
        $exp[1] = $opera[1];
    } elseif(stripos($sys, "Edge") > 0) {
        //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
        preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
        $exp[0] = "Edge";
        $exp[1] = $Edge[1];
    } elseif (stripos($sys, "Chrome") > 0) {
        preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
        $exp[0] = "Chrome";
        $exp[1] = $google[1];  //获取google chrome的版本号
    } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
        preg_match("/rv:([\d\.]+)/", $sys, $IE);
        $exp[0] = "IE";
        $exp[1] = $IE[1];
    }else {
        $exp[0] = "未知浏览器";
        $exp[1] = "";
    }
    $browser =  $exp[0].'('.$exp[1].')';



    $agent = $_SERVER['HTTP_USER_AGENT'];
    $os = false;

    if (preg_match('/win/i', $agent) && strpos($agent, '95'))
    {
        $os = 'Windows 95';
    }
    else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))
    {
        $os = 'Windows ME';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent))
    {
        $os = 'Windows 98';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))
    {
        $os = 'Windows Vista';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))
    {
        $os = 'Windows 7';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))
    {
        $os = 'Windows 8';
    }else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent))
    {
        $os = 'Windows 10';#添加win10判断
    }else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))
    {
        $os = 'Windows XP';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))
    {
        $os = 'Windows 2000';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))
    {
        $os = 'Windows NT';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent))
    {
        $os = 'Windows 32';
    }
    else if (preg_match('/linux/i', $agent))
    {
        $os = 'Linux';
    }
    else if (preg_match('/unix/i', $agent))
    {
        $os = 'Unix';
    }
    else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))
    {
        $os = 'SunOS';
    }
    else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))
    {
        $os = 'IBM OS/2';
    }
    else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))
    {
        $os = 'Macintosh';
    }
    else if (preg_match('/PowerPC/i', $agent))
    {
        $os = 'PowerPC';
    }
    else if (preg_match('/AIX/i', $agent))
    {
        $os = 'AIX';
    }
    else if (preg_match('/HPUX/i', $agent))
    {
        $os = 'HPUX';
    }
    else if (preg_match('/NetBSD/i', $agent))
    {
        $os = 'NetBSD';
    }
    else if (preg_match('/BSD/i', $agent))
    {
        $os = 'BSD';
    }
    else if (preg_match('/OSF1/i', $agent))
    {
        $os = 'OSF1';
    }
    else if (preg_match('/IRIX/i', $agent))
    {
        $os = 'IRIX';
    }
    else if (preg_match('/FreeBSD/i', $agent))
    {
        $os = 'FreeBSD';
    }
    else if (preg_match('/teleport/i', $agent))
    {
        $os = 'teleport';
    }
    else if (preg_match('/flashget/i', $agent))
    {
        $os = 'flashget';
    }
    else if (preg_match('/webzip/i', $agent))
    {
        $os = 'webzip';
    }
    else if (preg_match('/offline/i', $agent))
    {
        $os = 'offline';
    }
    else
    {
        $os = '0';
    }

    if($user_id==''){

        $user_id = '0';

    }



    // $user             = new Footprint;
    // $user->domain     = $domain;
    // $user->mobile     = $mobile;
    // $user->pathinfo   = $pathinfo;
    // $user->url        = $url;
    // $user->os         = $os;
    // $user->phone      = $phone;
    // $user->referer    = $referer;
    // $user->address    = $address;
    // $user->browser    = $browser;
    // $user->save();

$data = ['domain' => $domain, 
        'mobile' => $mobile,
        'pathinfo' => $pathinfo,
        'url' => $url,
        'os' => $os,
        'ip' => $ip,
        'user_id' => $user_id,
        'goods_id' => $goods_id,
        'referer' => $referer,
        'address' => $address,
        'browser' => $browser,
        'create_time' => time(),
        'update_time' => time()
        ];

Cookie::set('footprint',$data,20); 
return "";


    // 添加单条数据
// db('footprint')->insert($data);

// 使用model助手函数实例化User模型
// $user = model('Footprint');
// 模型对象赋值
// $user->data([
//     'domain'  =>  'thinkphp',
//     'mobile' =>  'thinkphp@qq.com'
// ]);
// $user->save();


    // 获取自增ID



//    以下是ip地址单独写入一个库，方便后期对此ip信息的记录
//    获取上面刚插入的ip地址
    // $ip = $user->ip;


//    判断这个ip地址是否已经存在

    // $user_ip = Ipinfo::where('ip', $ip)->count();



    // if($user_ip){

    //     return '';
    // }

//    如果没有入库继续存入


    // $user         = new Ipinfo;
    // $user->ip     = $ip;
    // $user->phone  = $phone;
    // $user->save();

//        dump(Request::instance()->isMobile());
//        $data = file_get_contents('http://ip.ws.126.net/ipquery?ip='.);
//    return json_decode($data,$assoc=true);
//        $address =  json_decode($data,$assoc=true);

}


//功能浏览次数和来路 - 在聊天窗口返回时用
function footprinttalk(){

    $referer    = '';
    $domain     = '';
    $pathinfo   = '';
    $mobile     = '';
    $address    = '';
    $browser    = '';
    $phone      = Cookie::get('phone');


    $request = Request::instance();

// 判断是否是手机
    if(Request::instance()->isMobile()){
        $mobile    = "来自手机";
    }




//    获取是什么浏览器和浏览器的版本号
    $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
    if (stripos($sys, "Firefox/") > 0) {
        preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
        $exp[0] = "Firefox";
        $exp[1] = $b[1];  //获取火狐浏览器的版本号
    } elseif (stripos($sys, "Maxthon") > 0) {
        preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
        $exp[0] = "傲游";
        $exp[1] = $aoyou[1];
    } elseif (stripos($sys, "MSIE") > 0) {
        preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
        $exp[0] = "IE";
        $exp[1] = $ie[1];  //获取IE的版本号
    } elseif (stripos($sys, "OPR") > 0) {
        preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
        $exp[0] = "Opera";
        $exp[1] = $opera[1];
    } elseif(stripos($sys, "Edge") > 0) {
        //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
        preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
        $exp[0] = "Edge";
        $exp[1] = $Edge[1];
    } elseif (stripos($sys, "Chrome") > 0) {
        preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
        $exp[0] = "Chrome";
        $exp[1] = $google[1];  //获取google chrome的版本号
    } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
        preg_match("/rv:([\d\.]+)/", $sys, $IE);
        $exp[0] = "IE";
        $exp[1] = $IE[1];
    }else {
        $exp[0] = "未知浏览器";
        $exp[1] = "";
    }
    $browser =  $exp[0].'('.$exp[1].')';



    $agent = $_SERVER['HTTP_USER_AGENT'];
    $os = false;

    if (preg_match('/win/i', $agent) && strpos($agent, '95'))
    {
        $os = 'Windows 95';
    }
    else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))
    {
        $os = 'Windows ME';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent))
    {
        $os = 'Windows 98';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))
    {
        $os = 'Windows Vista';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))
    {
        $os = 'Windows 7';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))
    {
        $os = 'Windows 8';
    }else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent))
    {
        $os = 'Windows 10';#添加win10判断
    }else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))
    {
        $os = 'Windows XP';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))
    {
        $os = 'Windows 2000';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))
    {
        $os = 'Windows NT';
    }
    else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent))
    {
        $os = 'Windows 32';
    }
    else if (preg_match('/linux/i', $agent))
    {
        $os = 'Linux';
    }
    else if (preg_match('/unix/i', $agent))
    {
        $os = 'Unix';
    }
    else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))
    {
        $os = 'SunOS';
    }
    else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))
    {
        $os = 'IBM OS/2';
    }
    else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))
    {
        $os = 'Macintosh';
    }
    else if (preg_match('/PowerPC/i', $agent))
    {
        $os = 'PowerPC';
    }
    else if (preg_match('/AIX/i', $agent))
    {
        $os = 'AIX';
    }
    else if (preg_match('/HPUX/i', $agent))
    {
        $os = 'HPUX';
    }
    else if (preg_match('/NetBSD/i', $agent))
    {
        $os = 'NetBSD';
    }
    else if (preg_match('/BSD/i', $agent))
    {
        $os = 'BSD';
    }
    else if (preg_match('/OSF1/i', $agent))
    {
        $os = 'OSF1';
    }
    else if (preg_match('/IRIX/i', $agent))
    {
        $os = 'IRIX';
    }
    else if (preg_match('/FreeBSD/i', $agent))
    {
        $os = 'FreeBSD';
    }
    else if (preg_match('/teleport/i', $agent))
    {
        $os = 'teleport';
    }
    else if (preg_match('/flashget/i', $agent))
    {
        $os = 'flashget';
    }
    else if (preg_match('/webzip/i', $agent))
    {
        $os = 'webzip';
    }
    else if (preg_match('/offline/i', $agent))
    {
        $os = 'offline';
    }
    else
    {
        $os = '未知操作系统';
    }

    if($phone==''){

        $phone = '15966982315';

    }


//    $fp = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$request->ip());
//
//    $fp = json_decode($fp,true);

//    echo $fp['data']['city'];
//    echo $fp['data']['region'];

//    $address = $fp['data']['region'].$fp['data']['city'];


//echo "okl";

    return $request->ip().' '.$address.' '.$mobile.' '.$os.' '.$browser;


}

// 验证用户是否token功能
function token(){
        $user            =  Cookie::get('phone');
        $token           =  Cookie::get('token');
        // dump($token);
        if ($user) {
            
            // 判断是否其他浏览器或者设备登录（设置每次登录修改token时有效）
            // 判断Cookie里的token的否正确
            $token_count = User::where('phone','=',$user )
                                ->where('token','=',$token )
                                ->count();
            if ( $token_count<=0) {

                Cookie::set('phone','',36000000);
                Cookie::set('token','',36000000);
                $user    =  "";
                $token =  "";

            }
        }
}

//人性化时间显示
function formatTime($time){
    return $time;
    $rtime = date("m月d日 H:i",$time);
    $htime = date("H:i",$time);
    $year  = date("Y")-date("Y",$time);
    $time  = time() - $time;

    if ($time < 60){
        $str = '刚刚';
    }elseif($time < 60 * 60){
        $min = floor($time/60);
        $str = $min.'分钟前';
    }elseif($time < 60 * 60 * 24){
        $h = floor($time/(60*60));
        $str = $h.'小时前 ';
    }elseif($time < 60 * 60 * 24 * 3){
        $d = floor($time/(60*60*24));
        if($d==1){
            $str = '昨天 '.$rtime;
        }else{
            $str = '前天 '.$rtime;
        }
    }elseif($year >0){
        $str = $rtime;
    }
    else{
        $str = date("Y年m月d日 H:i",$time);
    }
    return $str;
}

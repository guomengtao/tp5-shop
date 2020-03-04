<?php

namespace app\index\controller;

use think\Db;
use think\Request;
use app\index\model\Sms;
use app\index\model\User;
use app\index\model\Config;
use app\index\model\UserQq;
use app\index\model\Order;
use app\index\model\Shop;
use alipay\alipaynotify;
use think\Cookie;
use think\Session;

// use QL\QueryList;

class Api extends \think\Controller
{
    // public function cai()
    // {
    //     // 设置采集规则
    //     // 采集规则
    //     $rules = array(
    //         // 'catalog' => array('.breadcrumbs-container>li:nth-child(2)>a','href'),
    //         'word' => array('.inner', 'text'),
    //         // 'url'  => array('.panel-body>.yd-tags>a', 'href'),
    //
    //     );
    //
    //     $ql = QueryList::rules($rules);
    //
    //
    //     $this->qlWeb(0, $ql);
    // }
    //
    // /**
    //  * 采集外部的网址
    //  * @param $id
    //  * @param $ql
    //  *
    //  */
    // public function qlWeb($page, $ql)
    // {
    //     // 采集quWord示例
    //     $ip = "117.89.35.58";
    //     $page = $ip;
    //     // 从本地目录读取方式，解决文件名带.php无法正确访问问题
    //     $url = "https://www.ipip.net/ip/" . $page;
    //     // $url = get($url);
    //
    //
    //     // 开始采集
    //     $data    = $ql->get($url)->query()->getData();
    //     $dataall = $data->all();
    //
    //     dump($dataall);
    //     // echo $page . '-' . count($dataall) . "\n";
    //
    //
    //
    //
    //
    //
    //
    //
    //
    //     // 释放Document内存占用
    //     $ql->destruct();
    //
    //     // $page = $page + 1;
    //
    //     // 继续递归查询
    //
    //     // if ($page < 489) {
    //     //
    //     //     $this->qlWeb($page, $ql);
    //     // }
    //
    // }

    public function login()
    {

        // 指定json数据输出
        // return json(['data'=>6,'code'=>1,'message'=>'操作完成']);


        $username = input('username');
        $password = input('password');

        if (!$username) {
            # code...
            return 3;
        }
        if (!$password) {
            # code...
            return json(['data' => 6, 'code' => 404, 'message' => '密码不能为空']);
            return 4;
        }

        if ($username == '13034892752' and $password == '123456') {
            # code...
            return 200;
        } else {
            return 2;

        }


    }

    public function qq()
    {


        // 改为从数据获取以上三个敏感信息

        $config = new Config();
        // 查询单个数据
        $config = $config->where('name', 'qq')
            ->find();


        //应用的APPID
        $app_id = $config->appid;
        //应用的APPKEY
        $app_secret = $config->appkey;
        //成功授权后的回调地址
        $my_url = $config->my_url;


        //Step1：获取Authorization Code


        // 查询数据库有没这个用户的openid，没有创建。      
        $code = input("code");

        if ($code) {
            # code...

            $code = Cookie::set('code', $code, 200000);
        }

        $code = Cookie::get('code');

        // echo  "qq登录的code第二步骤：" . $code . "<br/>";


        // echo "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=100556678&client_secret=ac07166c50a0361b7beec4e423a6bc46&code=" . $code. "&state=1&redirect_uri=http://open.gaoxueya.com/index/api/qq";

        // echo "<br/>";

        //Step2：通过Authorization Code获取Access Token

        // $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=" . $app_id."&client_secret=". $app_secret."&code=" . $code. "&state=1&redirect_uri=".$my_url;

        //拼接URL
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
            . "&client_secret=" . $app_secret . "&code=" . $code;

        $response = file_get_contents($token_url);


        if (strpos($response, "callback") !== false) {
            $lpos     = strpos($response, "(");
            $rpos     = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);


            $msg = json_decode($response);

            // dump($msg);die();
            if (isset($msg->error)) {
                // echo "<h3>error:</h3>" . $msg->error;
                // echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }


        //Step3：使用Access Token来获取用户的OpenID
        $params = array();
        parse_str($response, $params);
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $params['access_token'];

        $str = file_get_contents($graph_url);

        // dump($str);

        if (strpos($str, "callback") !== false) {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos - 1);
        }

        $user = json_decode($str);

        // dump($user);

        if (isset($user->error)) {
            // echo "<h3>error:</h3>" . $user->error;
            // echo "<h3>msg  :</h3>" . $user->error_description;
            exit;
        }
        // echo("Helloni " . $user->openid);

        $openid = $user->openid;

        // Step4：使用Access Token来获取用户的OpenID


        $graph_url = "https://graph.qq.com/user/get_user_info?access_token=" . $params['access_token'] . "&oauth_consumer_key=" . $app_id . "&openid=" . $user->openid;


        $str          = file_get_contents($graph_url);
        $user_from_qq = json_decode($str);
        // dump($str);
        // dump($user);

        // echo "欢迎您" . $user->nickname;
        // echo "你的头像是：<img src=" . $user->figureurl_qq_1 . ">";
        // echo "性别：" . $user->gender . "";


        // $data = json_decode(json_encode($fp),TRUE);


        // die();
        // echo $tom;

        // echo $data['message'];

        // dump($fp);
        //Step3：使用Access Token来获取用户的OpenID


        // Step5：数据库存储

        if (!$user_from_qq->nickname) {
            // 如果没获取到昵称终止操作
            return "没有昵称";
        }


        // $openid = "1011";

        $user = new UserQq();
        // 查询单个数据
        $user = $user->where('openid', $openid)
            ->where('type', 0)
            ->find();


        // 没登记openID的先登记
        if (!$user) {
            # code.. 

            $user                 = new UserQq();
            $user->openid         = $openid;
            $user->nickname       = $user_from_qq->nickname;
            $user->figureurl_qq_1 = $user_from_qq->figureurl_qq_1;
            $user->figureurl_qq_2 = $user_from_qq->figureurl_qq_2;
            $user->gender         = $user_from_qq->gender;
            $user->year           = $user_from_qq->year;
            $user->type           = 0;
            $user->save();

            // return "创建成功！";

            // 查询单个数据
            $user = $user->where('openid', $openid)
                ->where('type', 0)
                ->find();

        } else {

            // 如果已经存在就更新，保持数据最新  暂时不更新
        }


        // 记录昵称和头像，页面展示
        cookie('openid', $user_from_qq->nickname, 3600000);
        cookie('nickname', $user_from_qq->nickname, 3600000);
        cookie('figureurl_qq_2', $user_from_qq->figureurl_qq_2, 3600000);


        // dump($user);
        // dump($user->user_id);

        // 没绑定的跳转到绑定页面
        if (!$user->user_id) {
            # code.. 

            // 赋值（当前作用域）记录需要绑定openid
            session('openid_id', $user->id);


            // 重定向到News模块的Category操作
            $this->redirect('index/index/register', ['cate_id' => 2]);


            return $this->success('登录成功，绑定账号', 'index/index/register');

        }

        // 获取用户账号和 token秘钥

        // dump($openid);

        // 取出主键为 $user->user_id 的数据
        // 提示从user表里查询主键id是$user->user_id ，这个$user->user_id是用户id从已经绑定的UserQq里面获取
        // 这个页面大量的用$user 造成了很多混淆，简易改成独立的
        $user = User::get($user->user_id);

        // dump($user);
        // echo $user->phone . "999+++++";
        // echo $user->token;


        // 删除Cookie
        cookie('phone', null);
        cookie('token', null);

        // 设置Cookie 有效期为 秒
        Cookie('phone', $user->phone, 3600000);
        Cookie('token', $user->token, 3600000);
        Cookie('user_id', $user->id, 3600000);


        // 判断是否是先支付了，再来注册/登录的用户
        if (Session::get('total_fee') > 0) {
            // 更新用户的用户名
            Session::set('phone', $user->phone);
            //  重定向到收款页面，加入订单
            $this->redirect('member/payReturn');
        }


        // 设置管理方便区分管理员
        if ($user->phone == "18210787405") {
            Cookie::set('admin', 1, 3600000);
        }

        return $this->success('登录成功^_^', 'index/index/index');


        // 查询有没绑定账号，没绑定跳转绑定页面。有绑定 自动设置登录


        // https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=[YOUR_APP_ID]&client_secret=[YOUR_APP_Key]&code=[The_AUTHORIZATION_CODE]&state=[The_CLIENT_STATE]&redirect_uri=[YOUR_REDIRECT_URI]

        // https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=[YOUR_APP_ID]&client_secret=[YOUR_APP_Key]&code=[The_AUTHORIZATION_CODE]&state=[The_CLIENT_STATE]&redirect_uri=[YOUR_REDIRECT_URI]

        // https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=100556678&client_secret=ac07166c50a0361b7beec4e423a6bc46&code=C043B984F85C1B301C4295878CE7E921&state=1&redirect_uri=http://open.gaoxueya.com/index/api/qq


        // access_token=F2CE42D4E114610204B329ADDDDF90ED&expires_in=7776000&refresh_token=E973B7747526347BC63962122A2ABB5C

        // access_token=F2CE42D4E114610204B329ADDDDF90ED&expires_in=7776000&refresh_token=E973B7747526347BC63962122A2ABB5C

        // https://graph.qq.com/oauth2.0/me?access_token=F2CE42D4E114610204B329ADDDDF90ED


        // https://graph.qq.com/oauth2.0/me?access_token=F2CE42D4E114610204B329ADDDDF90ED

// callback( {"client_id":"100556678","openid":"537770203713756DC8D596C2E97BC5B8"} );

        // https://graph.qq.com/user/get_user_info?access_token=YOUR_ACCESS_TOKEN&oauth_consumer_key=YOUR_APP_ID&openid=YOUR_OPENID


        // callback( {"client_id":"100556678","openid":"537770203713756DC8D596C2E97BC5B8"} );


        // https://graph.qq.com/user/get_user_info?access_token=YOUR_ACCESS_TOKEN&oauth_consumer_key=YOUR_APP_ID&openid=YOUR_OPENID

        // https://graph.qq.com/user/get_user_info?access_token=F2CE42D4E114610204B329ADDDDF90ED&oauth_consumer_key=100556678&openid=537770203713756DC8D596C2E97BC5B8


        // { "ret": 0, "msg": "", "is_lost":0, "nickname": "Speech", "gender": "男", "province": "", "city": "阿纳", "year": "1982", "figureurl": "http:\/\/qzapp.qlogo.cn\/qzapp\/100556678\/537770203713756DC8D596C2E97BC5B8\/30", "figureurl_1": "http:\/\/qzapp.qlogo.cn\/qzapp\/100556678\/537770203713756DC8D596C2E97BC5B8\/50", "figureurl_2": "http:\/\/qzapp.qlogo.cn\/qzapp\/100556678\/537770203713756DC8D596C2E97BC5B8\/100", "figureurl_qq_1": "http:\/\/thirdqq.qlogo.cn\/qqapp\/100556678\/537770203713756DC8D596C2E97BC5B8\/40", "figureurl_qq_2": "http:\/\/thirdqq.qlogo.cn\/qqapp\/100556678\/537770203713756DC8D596C2E97BC5B8\/100", "is_yellow_vip": "0", "vip": "0", "yellow_vip_level": "0", "level": "0", "is_yellow_year_vip": "0" }

        return "qq登录";
    }

    public function json()
    {
        echo '{"code":0,"msg":"","count":1000,"data":[{"id":10000,"username":"user-0","sex":"女","city":"城市-0","sign":"签名-0","experience":255,"logins":24,"wealth":82830700,"classify":"作家","score":57},{"id":10001,"username":"user-1","sex":"男","city":"城市-1","sign":"签名-1","experience":884,"logins":58,"wealth":64928690,"classify":"词人","score":27},{"id":10002,"username":"user-2","sex":"女","city":"城市-2","sign":"签名-2","experience":650,"logins":77,"wealth":6298078,"classify":"酱油","score":31},{"id":10003,"username":"user-3","sex":"女","city":"城市-3","sign":"签名-3","experience":362,"logins":157,"wealth":37117017,"classify":"诗人","score":68},{"id":10004,"username":"user-4","sex":"男","city":"城市-4","sign":"签名-4","experience":807,"logins":51,"wealth":76263262,"classify":"作家","score":6},{"id":10005,"username":"user-5","sex":"女","city":"城市-5","sign":"签名-5","experience":173,"logins":68,"wealth":60344147,"classify":"作家","score":87},{"id":10006,"username":"user-6","sex":"女","city":"城市-6","sign":"签名-6","experience":982,"logins":37,"wealth":57768166,"classify":"作家","score":34},{"id":10007,"username":"user-7","sex":"男","city":"城市-7","sign":"签名-7","experience":727,"logins":150,"wealth":82030578,"classify":"作家","score":28},{"id":10008,"username":"user-8","sex":"男","city":"城市-8","sign":"签名-8","experience":951,"logins":133,"wealth":16503371,"classify":"词人","score":14},{"id":10009,"username":"user-9","sex":"女","city":"城市-9","sign":"签名-9","experience":484,"logins":25,"wealth":86801934,"classify":"词人","score":75}]}';
    }

    public function jack()
    {
        // 获取当前请求的所有变量（经过过滤）
        // dump(input('')); 
        // exit();

        // 改为从数据获取以上三个敏感信息

        $config = new Config();
        // 查询单个数据
        $config = $config->where('name', 'alipay')
            ->find();


        //应用的APPID
        $app_id = $config->appid;
        //应用的APPKEY
        $appkey = $config->appkey;
        //成功授权后的回调地址
        $email = $config->email;


        $alipay_config['partner'] = $config->appid;


        //收款支付宝账号，一般情况下收款账号就是签约账号
        $alipay_config['seller_email'] = $config->email;


        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key'] = $config->appkey;


        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


        //签名方式 不需修改
        $alipay_config['sign_type'] = strtoupper('MD5');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset'] = strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert'] = getcwd() . '\\cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = 'http';
        // $b = new return_url();


        // $b = new alipaynotify($alipay_config);
        // $foo = new \first\second\Foo();
        //计算得出通知验证结果
        $alipayNotify  = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();

        $result = '';

        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号

            $out_trade_no = $_GET['out_trade_no'];

            //支付宝交易号

            $trade_no = $_GET['trade_no'];

            // echo "您的订单号是：" .$trade_no;


            //交易状态
            $trade_status = $_GET['trade_status'];


            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                $result = "支付成功";

                // 登记订单信息
// http://open.gaoxueya.com/index/api/jack?body=137&buyer_email=18311182167&buyer_id=2088822675183141&exterface=create_direct_pay_by_user&is_success=T&notify_id=RqPnCoPT3K9%252Fvwbh3Ih30dwL%252Fplw7rcKM3w%252FRgCozrpyxNlkpDUnPLASoamX%252FTY6ZINy&notify_time=2017-12-04+16%3A57%3A03&notify_type=trade_status_sync&out_trade_no=1371891043418620171204045626&payment_type=1&seller_email=rinuo%40vip.qq.com&seller_id=2088002229990889&subject=92课+购买后显示信息的调整+作业题.&total_fee=0.10&trade_no=2017120421001004140522784159&trade_status=TRADE_SUCCESS&sign=095d7a9e2670a9e3f54accb96e907f1f&sign_type=MD5


                $phone        = Cookie::get('phone');
                $body         = input('param.body');
                $rand         = '';
                $subject      = input('param.subject');
                $total_fee    = input('param.WIDtotal_fee');
                $buyer_id     = input('param.buyer_id');
                $buyer_email  = input('param.buyer_email');
                $total_fee    = input('param.total_fee');
                $out_trade_no = input('param.out_trade_no');

//                注册 登录 找回密码时先清空当前默认登录数值
                if ($body == 1008611 or $body == 1008612) {
                    $phone = '';
                }


//                把所有订单成功数据加入Session，解决支付宝支付成功页面不能刷新的问题
//                通知Session方便其他页面灵活处理返回的结果
//                同时注意Session可能带来的安全隐患

                Session::set('phone', $phone);
                Session::set('body', $body);
                Session::set('rand', $rand);
                Session::set('subject', $subject);
                Session::set('total_fee', $total_fee);
                Session::set('buyer_id', $buyer_id);
                Session::set('buyer_email', $buyer_email);
                Session::set('total_fee', $total_fee);
                Session::set('out_trade_no', $out_trade_no);

//              重定向到指定页面处理相关业务逻辑
                $this->redirect('member/payReturn', 302);


                $phone        = Session::get('phone');
                $body         = Session::get('body');
                $rand         = Session::get('rand');
                $subject      = Session::get('subject');
                $total_fee    = Session::get('total_fee');
                $buyer_id     = Session::get('buyer_id');
                $buyer_email  = Session::get('buyer_email');
                $total_fee    = Session::get('total_fee');
                $out_trade_no = Session::get('out_trade_no');


                // 查询价格是否篡改,大于等于的情况可以通过

                $map['id'] = $body;
//                $map['price'] = $total_fee;

                $price = Shop::where($map)
                    ->value('price');
                if ($total_fee >= $price) {
//                     return "ok";
                } else {
                    $body = 40;
//                     return "价格不一致";
                }


                // 如果是签到，查询昨天累加的签到天数
                if ($body == 135) {

                    $rand = Order::where('body', $body)
                        ->where('phone', $phone)
                        ->whereTime('create_time', 'yesterday')
                        ->value('rand');

                }


                $order = Order::create([
                    'phone'        => $phone,
                    'body'         => $body,
                    'rand'         => $rand + 1,
                    'subject'      => $subject,
                    'total_fee'    => $total_fee,
                    'buyer_id'     => $buyer_id,
                    'buyer_email'  => $buyer_email,
                    'out_trade_no' => $out_trade_no,
                ]);


                // 如果是vip用户，设置vip字段
                if ($body == 105) {


                    User::where('body', $body)
                        ->update(['rand' => 105]);

                    //设置增加vip天数,先查询vip到期日期
                    $expiration_time = User::where('phone', $phone)
                        ->whereTime('create_time', '>=', 'today')
                        ->value('expiration_time');

//                    设置开始日期，过期的从今天开始算。
                    if ($expiration_time < time()) {
                        $expiration_time = time();
                        $start_time      = time();

                        User::where('phone', $phone)
                            ->update(['start_time' => $start_time, 'rand' => 1]);

                    }
                    // 根据支付价格设置对应vip有效期
                    if ($total_fee = 33) {
                        $expiration_time = $expiration_time + (3600 * 24 * 99);
                    } elseif ($total_fee = 188) {
                        $expiration_time = $expiration_time + (3600 * 24 * 188);
                    } elseif ($total_fee >= 320) {
                        $expiration_time = $expiration_time + (3600 * 24 * 366);
                    } else {
                        $expiration_time = $expiration_time + (3600 * 24 * 32);
                    }

                    User::where('phone', $phone)
                        ->update(['expiration_time' => $expiration_time]);


                }


//                处理没有登录先付款的情况
                if ($phone = '15966982315') {

//                    设置session记录订单号，跳转到登录页面，补充订单流程
                    Session::set('name', 'thinkphp');
//                    重定向方式
                    $this->redirect('index/login', ['id' => $body]);

                }
                //重定向到用户购买的商品结果页面
                // $this->redirect('index/view', ['id' => $body]);
                exit('<script>top.location.href="../index/view/id/' . $body . '"</script>');

            } else {
                echo "trade_status=" . $_GET['trade_status'];
            }

            // echo "购买成功了...<br />";


            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            // echo "付款失败";
            $result = "支付失败";


        }

// 模板变量赋值
        $this->assign('result', $result);
// 渲染模板输出
        return $this->fetch();
    }

    public function sms()
    {


        $config = new Config();
        // 查询单个数据
        $config = $config->where('name', 'sms')
            ->find();


        //应用的APPID
        $username = $config->username;
        //应用的APPKEY
        $password = $config->password;
        //成功授权后的回调地址
        $my_url = $config->my_url;

        header("content-type:text/html; charset=utf-8");
        $mobile  = input('s');
        $rand    = rand(1000, 9999); //取随机四位数字
        $content = '验证码：' . $rand . '【高血压】';
        // $cha  = $my_url . '?username=' . $username . '&password=' . $password . '&mobile=' . $tom . '&content=验证码：' . $rand . '【高血压】';
        $cha = $my_url . '?username=' . $username . '&password=' . $password . '&mobile=' . $mobile . '&content=' . $content;


        // $url = 'http://api.chanyoo.net/sendsms?username=' . $username . '&password=' . $password . '&mobile=' . $mobile . '&content=' . $content . '';
        // dump($url);
        // $this->sendSMS($url);
        // $fp = file_get_contents($cha);

        $fp = file_get_contents("http://api.chanyoo.net/sendsms?username=$username&password=$password&mobile=$mobile&content=$content");

        dump($fp);


        die();

        //转xml为数组形式
        $xml  = simplexml_load_string($fp);
        $data = json_decode(json_encode($xml), TRUE);


        echo $data['message'];


        if ($data['message'] == '非法请求，一个号码一天内提交超过了五次') {
            return " 【请使用你今天收到的上条短信验证码登录即可 ！ 】";
        }

        // die();

        if ($data['message'] == '短信提交成功') {


            echo "(";

            // 模型的 静态方法 
            // 存入短信发送日志表
            $user = Sms::create([
                'phone' => $tom,
                'rand'  => $rand
            ]);

            echo ":";
        }
        // return $this->fetch();

        // 短信入库完成ccc
    }

    public function sendSMS($url)
    {
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
        dump($result);
        $result = json_decode($result, true);
        return $result['result'];
    }

    public function alipay()
    {

        return $this->fetch();
    }

    public function alipayReturnUrl()
    {
        import('alipay/tom.php');
        require("alipay/tom.php");
        require("alipay/tom.php");
        require("alipay/lib/alipay_notify.class.php");


        exit();


        require("alipay/alipay.config.php");
        require("alipay/lib/alipay_core.function.php");
        require("alipay/lib/alipay_md5.function.php");
        require("alipay/lib/alipay_notify.class.php");


        // 计算得出通知验证结果
        $alipayNotify  = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号

            $out_trade_no = $_GET['out_trade_no'];

            //支付宝交易号

            $trade_no = $_GET['trade_no'];

            echo "您的订单号是：" . $trade_no;


            //交易状态
            $trade_status = $_GET['trade_status'];


            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            } else {
                echo "trade_status=" . $_GET['trade_status'];
            }

            echo "购买成功了...<br />";

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "付款失败";
        }


        // return $this->fetch();
    }

    public function demo()
    {

        dump("演示一下 api跨域访问");

        $url = "http://open.gaoxueya.com/tp5/public/index.php/index/bbs/add";
        // $url = file_get_contents($url);

        // echo $url ;

        // exit();

        $data = array(
            "title"   => "用机器人来发帖了,我来采集你的内容了",
            "content" => "加个验证码吧，不然被攻击了"
        );

        // 1. 初始化
        $ch = curl_init();

        // 2. 设置选项，包括URL

        // 指定请求的URL；
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        // 返回字符串
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);

        // 4. 释放curl句柄
        curl_close($ch);


        //echo $output;


    }

    public function weibo()
    {


// 微博登录官方开发步骤
// http://open.weibo.com/wiki/Connect/login

// 微博登录官方获取信息接口
// http://open.weibo.com/wiki/2/users/show

// 老师的临时演示地址：
// http://open.gaoxueya.com/tp5/public/index/api/weibo?code=6555d1d93a8b9630c1fc2302bdc9e34f 


        // 2018年5月31日 重新梳理 总结
        //         第一个 搞不清楚 client_id  对应1460932055 App Key
        //   client_secret 对应 0f1f4d4480b6ba59c3bb46a5238b41a0 App Secret


        // 尽管这个可以猜测，时间已久还是要再猜一遍

        // 第二个问题 curl方式失效
        // 测试几遍 自己又莫名的好了

        // 第三个问题 直接copy到地址栏访问无效，这里重点是要post请求猜可以

        // 以上三个问题，重点整理一下，然后准备开发入库对接 流程


        // 改为从数据获取以上三个敏感信息

        $config = new Config();
        // 查询单个数据
        $config = $config->where('name', 'weibo')
            ->find();


        //应用的APPID
        $app_id = $config->appid;
        //应用的APPKEY
        $app_secret = $config->appkey;
        //成功授权后的回调地址
        $my_url = $config->my_url;


        $code = input('code');

        // echo $code;

//         $tom =   "https://api.weibo.com/oauth2/access_token?client_id=1460932055
// &client_secret=0f1f4d4480b6ba59c3bb46a5238b41a0&grant_type=authorization_code&redirect_uri=http://open.gaoxueya.com/index/api/weibo&code=" .$code ;

        $url = "https://api.weibo.com/oauth2/access_token?client_id=" . $app_id . "&client_secret=" . $app_secret . "&grant_type=authorization_code&redirect_uri=" . $my_url . "&code=" . $code;

// dump($url);


// $curl = curl_init();
// curl_setopt($curl, CURLOPT_URL, $url);
// curl_setopt($curl, CURLOPT_HEADER, 1);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);// 这个是主要参数
// $data = curl_exec($curl); 
// curl_close($curl);
// var_dump($data); 

        // $tom =   "https://api.weibo.com/oauth2/access_token?client_id=2078783153&client_secret=249bc84acffd4d59335021f1e4865707&grant_type=authorization_code&redirect_uri=http://open.gaoxueya.com/tp5/public/index/api/weibo&code=" .$code ;


        // post获取开始 获取重要的唯一ID钥匙-访问令牌
        header("Content-Type:text/html;charset=utf-8");
        // $url = $tom;
        //echo $url.'<br />';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");
        $data = curl_exec($curl);
        curl_close($curl);

        // dump($data);
        $result = json_decode($data, true);

        // echo '<pre>';
        // dump($result);
        // echo '</pre> 1233';

        // exit();


        $access_token = $result['access_token'];
        $uid          = $result['uid'];
        //echo $access_token;

        // 继续获得用户信息 - 开始 方法一
        $tom = "https://api.weibo.com/2/users/show.json?access_token=" . $access_token . "&uid=" . $uid;

        // 使用file方法
        // $domain = 'Rinuo.com'; 
        // $cha = 'http://panda.www.net.cn/cgi-bin/check.cgi?area_domain='.$domain ; 
        $data = file_get_contents($tom, 'rb');
        //dump($data);

        $data = json_decode($data, true);

        //$xml = simplexml_load_string($fp);
        //$data = json_decode(json_encode($xml),TRUE);

        // dump($data);


        //exit();

        // 共用快速登录网站处理接口
        // dump(quickLogon());
        // dump("-98");


        // 模板变量赋值
        // $this->assign('data',$data);


        // 渲染模板输出
        // return $this->fetch();

        // 以下部分为copy的qq登录后的储存代码对接

        // type =1 设置为微博登录


        $openid = $uid;

        $user = new UserQq();
        // 查询单个数据
        $user = $user->where('openid', $openid)->where('type', 1)
            ->find();


        // 没登记openID的先登记
        if (!$user) {
            # code.. 

            $user                 = new UserQq;
            $user->openid         = $openid;
            $user->nickname       = $data['name'];
            $user->figureurl_qq_1 = $data['avatar_large'];
            $user->figureurl_qq_2 = $data['avatar_hd'];
            $user->gender         = $data['gender'];
            $user->year           = $data['province'];
            $user->type           = 1;
            $user->save();

            // return "创建成功！";

            // 查询单个数据
            $user = $user->where('openid', $openid)->where('type', 1)
                ->find();


        } else {

            // 如果已经存在就更新，保持数据最新  暂时不更新

        }


        // 记录昵称和头像，页面展示
        cookie('openid', $openid, 3600000);
        // cookie('nickname', 6, 3600000);
        cookie('nickname', $data['name'], 3600000);
        cookie('figureurl_qq_2', $data['avatar_hd'], 3600000);


        // dump("123996");die();

        // 没绑定的跳转到绑定页面
        if (!$user->user_id) {
            # code.. 

            // 赋值（当前作用域）记录需要绑定openid
            session('openid_id', $user->id);


            // 重定向到News模块的Category操作
            $this->redirect('index/index/register', ['cate_id' => 2]);


            return $this->success('登录成功，绑定账号', 'index/index/register');

        }


        // 获取用户账号和 token秘钥
        // 取出主键为 $user->user_id 的数据
        // 提示从user表里查询主键id是$user->user_id
        // 这个$user->user_id是用户id从已经绑定的UserQq里面获取
        // 这个页面大量的用$user 造成了很多混淆，简易改成独立的
        $user = User::get($user->user_id);


        // 删除Cookie
        cookie('phone', null);
        cookie('token', null);

        // 设置Cookie 有效期为 秒
        Cookie('phone', $user->phone, 3600000);
        Cookie('token', $user->token, 3600000);
        Cookie('user_id', $user->id, 3600000);

        // 判断是否是先支付了，再来注册/登录的用户
        if (Session::get('total_fee') > 0) {
            // 更新用户的用户名
            Session::set('phone', $user->phone);
            //  重定向到收款页面，加入订单
            $this->redirect('member/payReturn');
        }


        // 设置管理方便区分管理员
        if ($user->phone == "18210787405") {
            Cookie::set('admin', 1, 3600000);
        }

        return $this->success('登录成功^_^', 'index/index/index');


    }

    public function domain()
    {


        $domain = 'Rinuo.com';
        $cha    = 'http://panda.www.net.cn/cgi-bin/check.cgi?area_domain=' . $domain;
        $fp     = file_get_contents($cha, 'rb');
        //dump($fp);


        $xml  = simplexml_load_string($fp);
        $data = json_decode(json_encode($xml), TRUE);

        //dump($data);
        //exit();
        // 模板变量赋值
        $this->assign('data', $data);

        // 渲染模板输出
        return $this->fetch();

    }

}
 
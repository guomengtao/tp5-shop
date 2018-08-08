<?php
namespace app\index\controller;


use think\Db;
use think\Request;
use app\index\model\Data;
use app\index\model\User;
use app\index\model\Footprint;
use app\index\model\Ipinfo;
use think\Cookie;
use think\Session;

class Bbs extends \think\Controller
{
    public function admin()
    {

 //      调用浏览记录和来路统计功能
        footprint();

        return $this->fetch();


    }
    public function show(){

 //      调用浏览记录和来路统计功能
        footprint();

        
        // DB写法
        // $show = Db::name('data')->where('id','>',0)->order('id', 'desc')->paginate(10,80);

        // dump($show);
        // 模型写法
        $show = Data::where('id','>',0)->order('id', 'desc')->paginate(10);

        // $show = $show->toArray();

    

        $captcha = input("captcha");
        if ($captcha) {

                if(!captcha_check($captcha)){
                 //验证失败
                    $this->error('验证码错误');
                     
                }else{
                    // $this->success('验证码正确');
                     
                };

      

        }




        // dump($show);
        // exit();
        $this->assign('show', $show);
        // 渲染模板输出

        return $this->fetch();


    }
    public function view(){

 //      调用浏览记录和来路统计功能
        footprint();

        //echo input('param.id');

        $id  = input('id');

        if ($id<>'') {


            // 查询数据 - 查询留言详情内容
            $list = Db::name('data')
                ->where('id','=', $id )
                ->select();
            //dump($list);

            // 查询数据 - 上一页
            //echo $id;
            $up = Db::name('data')
                ->where('id','>', $id )
                ->order('id', '')
                ->limit(1)
                ->value('id');
            //dump($up);

            // 查询数据 - 下一页
            $next = Db::name('data')
                ->where('id','<', $id )
                ->order('id', 'desc')
                ->limit(1)
                ->value('id');

            //dump($next);

            $this->assign('up',$up);
            $this->assign('next',$next);
            $this->assign('list',$list);


            // 渲染模板输出
            return $this->fetch();


        }
        return $this->fetch('no');
        return "留言不存在";

    }
    public function add(){
          


        $title           = trim(input('title'));
        $content         = input('param.content');
        $shop            = input('param.shop');
        $age             = input('age');
        $phone           = Cookie::get('phone');
        $user_id         = Cookie::get('user_id');
        $online_time     = Cookie::get('online_time');
        $new_data_title  = "";
        $captcha         = input("captcha");

            // dump($status);die();
        if($shop==2){

            if (!$title) {
                # code...
                $this->error('留言内容不能为空');

            }


            if (!$captcha) {
                     
                $this->error('验证码不能为空');
            }
        }
 

//            生成一条Sesion产生session_id
//            收发聊天信息时用session_id作为判断唯一用户

        Session::set('s','1');
//        dump(session_id());



        if (!$phone){
            $phone ="15966982315";
        }





//        记录在线时间的功能，3分钟记录一次
        if (time()-$online_time>=300){







            Cookie::set('online_time',time(),3600);

            $user = User::where('phone','=',$phone)
                ->find();

//            echo '<h5 class="text-center"><small>您已累计在线'.$user['online_time'].'分钟</small></h5>';

            $user->online_time     = $user['online_time']+3;

            $user->save();


            //每3分钟更新一条ip地址的api接口地址
//            此接口返回数据较慢，所以放在一个不需要快速反馈消息的位置

            // 从浏览记录数据库拿出一条最新的地址为空的ip
            $user = Ipinfo::where('status', '')->order('id', 'desc')->value('ip');
//            echo $user;

            $ip = $user;



            if($ip<>''){

            }else{

                return '';
            }

//            通过接口获得这个ip的地址
            $fp = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);

            $fp = json_decode($fp,true);

//            echo $fp['data']['city'];
//            echo $fp['data']['region'];

//            dump($fp);

            $address = $fp['data']['region'].$fp['data']['city'];


//            die();{"code":0,"data":{"ip":"58.246.221.73333",
//"country":"","area":"","region":"","city":"","county":"","isp
//":"","country_id":"","area_id":"",
//"region_id":"","city_id":"","county_id":"","isp_id":""}}

//            把查询结果存进数据库，批量把同一ip的都更新

            Ipinfo::where('ip',$ip)
                ->update([
                    'city'           => $fp['data']['city'],
                    'region'         => $fp['data']['region'],
                    'country'        => $fp['data']['country'],
                    'region'         => $fp['data']['region'],
                    'isp'            => $fp['data']['isp'],
                    'country_id'     => $fp['data']['country_id'],
                    'area_id'        => $fp['data']['area_id'],
                    'region_id'      => $fp['data']['region_id'],
                    'city_id'        => $fp['data']['city_id'],
                    'isp_id'         => $fp['data']['isp_id'],
                    'status'         => 1]);


        }



        $song = '<div style="display:none " >
    <audio controls="controls" autoplay="autoplay">
        <source src="http://data.huiyi8.com/2014/lxy/05/14/48.mp3" type="audio/mpeg" />
        Your browser does not support the audio element.
    </audio></div>';

//        发帖成功的时候直接返回提示音
        if ($title=="ask_song"){
            return $song;
        }
//        检测到新回帖的时候的提示音
        if ($title=="ask_song_ajax"){


            // 只查询10秒以内的数据,如果遇到临时断网可能会有遗漏
            // 管理员收到全部的评论通知

            $new_data  = Data::where('session_id','<>',session_id())
                ->whereTime('create_time', '>=', time()-10)
                ->count();


            if ($new_data){
                return $song;
            }
            return "";


        }
        $new_data_title = "";

        //如果是第一次进入本节的欢迎词

        if ($title=="ask_ajax_play_welcome"){

            $welcome = Cookie::get('t'.$shop.'welcome');
//            加入一个停顿，防止多条并发
            sleep(2);
            if ($welcome<>2){
                Cookie::set('t'.$shop.'welcome',2,600);
                $new_data_title = "欢迎开始学习本节课程！课程中哪里听了不明白可以来这里沟通~";
                $phone_short = "7405";
            }else{
                return '';
            }

        }



        if ($title=="ask_ajax"){

// 只查询10秒以内的数据,如果遇到临时断网可能会有遗漏
//            管理员收到全部回帖功能
//            游客和收不到游客的信息 如果解决
//            排除自己发的信息，但是获取别人发的。
//

//            不排除任何人的发布，包括自己发的。






            $new_data  = Data::with('sort','foot')
                        ->where('session_id','<>',session_id())
                        ->whereTime('create_time', '>=', time()-10)
                        ->find();


            if ($phone=="182107874051"){
                $new_data  = Data::where('phone','<>',$phone)
                    ->whereTime('create_time', '>=', time()-10)
                    ->find();
            }






            if ($new_data){
                $phone    =  $new_data['phone'];
                $city     =  $new_data['sort']['city'];
                $browser  =  $new_data['foot']['browser'];
//                echo $new_data['sort']['city'];
                $phone =  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
                $phone_short =  substr_replace($phone,'****',3,4);


                $request = Request::instance();

                $ipAdd = $request->ip();



                $new_data_title = $new_data['title'];


//                return '<p style="padding: 6px;"><span class="label label-default" style="padding: 0.6em 1.6em 0.6em;    font-size: 100%; ">'.$phone.':'.$new_data['title'].'</span></p>';
//                return '<table class="table table-hover " style="word-break:break-all; word-wrap:break-all;"><tr  ><td  class="type-info" style="border-top: 0px;border-bottom: 1px solid #ddd;" ><h6><small>'.$phone.'</h6></small><strong>'.$new_data['title'].'</strong><h6><small>'.$new_data['create_time'].'</h6></small></td></tr></tbody></table>';
            }else{
                return "";
            }

        }
        if ($title=="ask_ajax_welcome"){
//            echo 99977766;
            $new_data_title = "欢迎来到本节课程！~";
            $phone_short = "7405";
        }

        if($new_data_title){

            //  这里设置统一的返回模板

            $title = $new_data_title ;

            // 三个表情的统一转换
            if($title == "[thumb]"){
                $title = '<img    src="/static/images/[thumb].png" data-holder-rendered="true" style="width: 28px; height: 28px;">';
            }

            if($title == "[rose]"){
                $title = '<img    src="/static/images/[rose].png" data-holder-rendered="true" style="width: 28px; height: 28px;">';
            }
            if($title == "[bq]"){
                $title = '<img    src="/static/images/[bq].png" data-holder-rendered="true" style="width: 28px; height: 28px;">';
            }

            $new_data_title =  $title;

            $gethtml = '
                <div class="row" style="margin-top: 10px;">
                        <div class="col-xs-2 col-md-2 text-center "><img class="media-object img-circle"  style="width: 34px"  src="http://open.gaoxueya.com/static/images/user.jpg" /><h5><small>'.$phone_short.'</small></h5></div>
                        <div class="col-xs-8 col-md-8">
                            <div style=" float: left;border-radius:5px ;border: 1px solid transparent;border-color: #ddd;padding: 10px 15px;word-wrap: break-word;max-width: 250px;">'.$new_data_title.'</div>
                        </div>
                    </div>         
                              ';


            $gethtml = '
            <div   class=" col-xs-12 col-sm-12 col-md-12 " style="padding: 0px;    border-bottom: 1px solid #eee;    margin-bottom: 10px; ">
                        <div class="media" >
                            <div class="media-left" >
                                <a href="#">
                                    <img class="media-object img-circle"    src="http://open.gaoxueya.com/static/images/user/user (10).jpg" data-holder-rendered="true" style="width: 34px; height: 34px;">
                                </a>
                            </div>
                            <div class="media-body">
                                <small><a href="#">'.$phone_short.'</a>
                                   :'.$new_data_title.'</small>
                                <h5> <small> 刚刚 '.$city.' '.$browser.'</small></h5>
                            </div>
                        </div>
                    </div>
            ';



            return $gethtml;
        }





        if ($title) {

            if($title == "[thumb]"){
                $title =$title;
            }

            // 插入记录 - 去掉表前缀
            // $result = Db::name('data')
            // ->insert(['title' => $title, 'content' => $content, 'create_time' => time()]);
            //dump($result);

            //限制一下最大长度，预防来发个非常长的。
            //mb_substr 针对中文的解决
            //mb_substr要开启php.ini里面extension=php_mbstring.dll扩展 一般默认偶开启了
            $title = mb_substr($title,0,100,"UTF-8");

         

             
              if ($captcha) {



                    if(!captcha_check($captcha)){
                     //验证失败
                        $this->error('验证码错误');
                         
                    }else{
                        // $this->success('验证码正确');
                         
                    };

      

                }



             // 模型的 静态方法
            $user = Data::create([
                'title'      =>  $title,
                'shop'       =>  $shop,
                'user_id'    =>  $user_id,
                'age'        =>  $age,
                'session_id' =>  session_id()
            ]);



        if ($captcha) {

                 
                    $this->success('留言成功！');
                     
        }


     





            if($title == "[thumb]"){
                $title = '<img    src="/static/images/[thumb].png" data-holder-rendered="true" style="width: 28px; height: 28px;">';
            }

            if($title == "[rose]"){
                $title = '<img    src="/static/images/[rose].png" data-holder-rendered="true" style="width: 28px; height: 28px;">';
            }
            if($title == "[bq]"){
                $title = '<img    src="/static/images/[bq].png" data-holder-rendered="true" style="width: 28px; height: 28px;">';
            }


            $gethtml = '
            
            
            <div   class=" col-xs-12 col-sm-12 col-md-12 " style="padding: 0px;    border-bottom: 1px solid #eee;    margin-bottom: 10px; ">


                        <div class="media" >
                            <div class="media-left" >
                                <a href="#">
                                    <img class="media-object img-circle"    src="http://open.gaoxueya.com/static/images/user/user (16).jpg" data-holder-rendered="true" style="width: 34px; height: 34px;">
                                </a>
                            </div>
                            <div class="media-body">
                                <small><a href="#">我</a>
                                   :'.$title.'</small>
                                <h5> <small> 刚刚</small></h5>
                            </div>
                        </div>



                    </div>
                    
                    
                    
                    
            ';


            echo $gethtml;





            if ($title=="tom几点了"){
//            echo 99977766;
                $new_data_title = "现在时间是：" .date('Y-m-d h:i:s',time());
                $phone_short = "7405";
            }
            if ($title=="tom你好"){
                $new_data_title = $phone." 您好！~ ";
                $phone_short = "7405";
            }
            if ($title=="tom几点了"){
                $new_data_title = "现在时间是：" .date('Y-m-d h:i:s',time());
                $phone_short = "7405";
            }
            if ($title=="查询积分1"){
                $new_data_title = "你的积分是***分";
                $phone_short = "7405";

            }
            if ($title=="查询积分"){

                $user  = User::where('phone',$phone)
                    ->find();
                dump($user);
//            dump($user->profile->email);

                $new_data_title = "你的积分是***分";
                $phone_short = "7405";
            }
            if(strpos($title,'今天日期') !== false){
                $new_data_title = "今天的日期：" .date('Y-m-d h:i:s',time());
                $phone_short = "7405";
            }

            if(strpos($title,'你好') !== false){
                $new_data_title = "你也好奥！~~我是机器人TOM，我还在成长中，谢谢你的关注";
                $phone_short = "7405";

            }

            if(strpos($title,'吗') !== false){
                $new_data_title = "我是机器人TOM，发现你问了一个问题 [".$title."]尽管我现在能回答的问题还很少，已帮你发给其他在线同学来解答你的问题";
                $phone_short = "7405";
            }

            if(strpos($title,'在') !== false){
                $new_data_title = "在的哦！我是Tom，有问题可以可以随时问奥";
                $phone_short = "7405";
            }




            $gethtml = '
            
            
            <div   class=" col-xs-12 col-sm-12 col-md-12 " style="padding: 0px;    border-bottom: 1px solid #eee;    margin-bottom: 10px; ">


                        <div class="media" >
                            <div class="media-left" >
                                <a href="#">
                                    <img class="media-object img-thumbnail"    src="http://open.gaoxueya.com/static/images/user/user (16).jpg" data-holder-rendered="true" style="width: 48px; height: 48px;">
                                </a>
                            </div>
                            <div class="media-body">
                                <small><a href="#">Tom</a>
                                   :'.$new_data_title.'</small>
                                <h5> <small> 刚刚 '.footprinttalk().'</small></h5>
                            </div>
                        </div>



                    </div>
                    
                    
                    
                    
            ';





            if ($new_data_title){
                echo $gethtml;
            }


            return "";
            return '<table class="table table-hover " style="word-break:break-all; word-wrap:break-all;"><tbody><tr  ><td  class="type-info" style="border-top: 0px;border-bottom: 1px solid #ddd;" ><h6><small>'.$phone.'</h6></small><strong>'.$title.'</strong><h6><small>'.'刚刚</h6></small></td></tr></tbody></table>';



//        return $this->success('恭喜您留言成功^_^','bbs/show');

        }

        return "请填写内容";


//       return $this->fetch();
    }


    public function ajax(){

 //      调用浏览记录和来路统计功能
        footprint();
        return $this->fetch();


    }
}
 
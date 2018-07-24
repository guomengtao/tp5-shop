<?php
namespace app\index\controller;

use app\index\model\Money;
use think\Db;
use think\Request;
use app\index\model\Shop;
use app\index\model\Video;
use app\index\model\Likes;
use app\index\model\User;
use app\index\model\User_qq;
use app\index\model\Sms;
use app\index\model\Order;
use app\index\model\Footprint;
use app\index\model\Data;
use think\Debug;
use think\Lang;
use think\Cookie;
use think\Session;
use think\Validate;
use think\captcha\Captcha;

class Index extends \think\Controller
{
 

        public function install(){

          set_time_limit(0);
          

          $hostname          = input('hostname');
          $database          = input('database');
          $username          = input('username');
          $password          = input('password');
          $step              = input('step');
           

           
          // 是否存在安装锁文件
          $install_lock = ROOT_PATH . 'application' . DS .'install.lock';
          if (file_exists($install_lock)) {
            $install_lock = 1;
            
          }

          // 增加一个第二步的已安装判断，方便查看
          if (Request::instance()->isPost() and  $install_lock == 1) {
            header('Content-Type:text/plain;charset=utf-8');
            echo "已安装installed! delllet application/install.lock file " ;
            die();
          }

         
        if (Request::instance()->isPost() and  $install_lock <> 1) {

             // 测试填写的数据库信息是否正确，不正确 thinkphp5的调试模式会报错
             $conn = mysqli_connect( 
               $hostname, /* The host to connect to 连接MySQL地址 */  
               $username, /* The user to connect as 连接MySQL用户名 */
               $password, /* The password to use 连接MySQL密码 */  
               $database);/* The default database to query 连接数据库名称*/ 

              // 设置读取数据库配置文件路径
              $database_file = ROOT_PATH . 'application' . DS . 'database.php';
              // 通过覆盖方式，直接重新复制配置文件里的内容
              $myfile = fopen($database_file, "w") or die("Unable to open file!");
              $txt = "<?php
              // +----------------------------------------------------------------------
              // | ThinkPHP [ WE CAN DO IT JUST THINK ]
              // +----------------------------------------------------------------------
              // | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
              // +----------------------------------------------------------------------
              // | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
              // +----------------------------------------------------------------------
              // | Author: liu21st <liu21st@gmail.com>
              // +----------------------------------------------------------------------

              return [
                  // 数据库类型
                  'type'            => 'mysql',
                  // 服务器地址
                  'hostname'        => '".$hostname."',
                  // 数据库名
                  'database'        => '".$database."',
                  // 用户名
                  'username'        => '".$username."',
                  // 密码
                  'password'        => '".$password."',
                  // 端口
                  'hostport'        => '',
                  // 连接dsn
                  'dsn'             => '',
                  // 数据库连接参数
                  'params'          => [],
                  // 数据库编码默认采用utf8
                  'charset'         => 'utf8',
                  // 数据库表前缀
                  'prefix'          => 'think_',
                  // 数据库调试模式
                  'debug'           => true,
                  // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
                  'deploy'          => 0,
                  // 数据库读写是否分离 主从式有效
                  'rw_separate'     => false,
                  // 读写分离后 主服务器数量
                  'master_num'      => 1,
                  // 指定从服务器序号
                  'slave_no'        => '',
                  // 是否严格检查字段是否存在
                  'fields_strict'   => true,
                  // 数据集返回类型
                  'resultset_type'  => 'array',
                  // 自动写入时间戳字段
                  'auto_timestamp'  => false,
                  // 时间字段取出后的默认时间格式
                  'datetime_format' => 'Y-m-d H:i:s',
                  // 是否需要进行SQL性能分析
                  'sql_explain'     => false,
              ];
              ";

              fwrite($myfile, $txt);
              // $txt = "Minnie Mouse\n";
              // fwrite($myfile, $txt);
              // $txt = "Minnie Mouse\n";
              // fwrite($myfile, $txt);
              fclose($myfile); 

        

        // ob_end_clean();  

        //读取文件内容
        $sql_file = ROOT_PATH . 't966.sql';
        $_sql = file_get_contents($sql_file);
         
        $_arr = explode(';', $_sql);
        // $conn=mysqli_connect($hostname, /* The host to connect to 连接MySQL地址 */  
        //        $username,  The user to connect as 连接MySQL用户名 
        //        $password, /* The password to use 连接MySQL密码 */  
        //        $database) or die('打开失败');  

        // 过滤掉数组里的空值
        $_arr = array_filter($_arr);

        header("content-Type: text/html; charset=Utf-8");

 
         
         
        //执行sql语句
        foreach ($_arr as $_value) {
            // $conn->query($_value.';');

            
            if ($conn->query($_value.';') === TRUE) {
               
                echo $_value . "    --- > ok  ";
                ob_flush();
                flush();  
                sleep(1);   
            } else {
                echo  $_value  ." Error: " .$conn->error . "  ";
                ob_flush();
                flush();  
                sleep(1);
                echo "<script>alert('导入sql，注意先清空数据库。防止冲突')</script>";
                die();

                   
   
            }
        }



         
        $conn->close();
        $conn = null;

        // 创建安装锁文件
        $myfile = fopen($install_lock, "w") or die("Unable to open file!");
        $txt = "install_lock\n";
        fwrite($myfile, $txt);
        $txt = "提示：安装成功，如需再次安装，请删除此文件\n";
        fwrite($myfile, $txt);
        fclose($myfile);


        $this->success('安装成功', url('/index/index/install/step/12'));


        //重定向到安装成功结果页
        $this->redirect('index/install', ['step' => 10]);



        }

          
          $this->assign('install_lock', $install_lock);
          return view();

        }


        public function cha123(){


          $image = \think\Image::open('./image.png');
          // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
          $image->thumb(150, 150)->save('./thumb.png');

          die();


         // 这个是实现自动添加跟进记录的功能，十分重要，这个是开始

                 //使用上面保存的cookies再次访问
          $url = 'https://weibo.com/p/aj/v6/mblog/add?ajwvr=6&domain=100505&__rnd=1523085138515';
  


        // 请求数据
        $data = [
                 
            "title" => "有什么新鲜事想告诉大家?",
            "location" => "page_100505_info",
            "text" => "123456",
            "appkey" => "",
            "style_type" => 1,
            "pic_id" => "",
            "tid" => "",
            "pdetail" => "1005051174602572",
            "mid" => "",
            "isReEdit" => "false",
            "rank" => 0,
            "rankid" => " ",
            "pub_source" => "page_2",
            "longtext" => 1,
            "topic_id" => "1022:",
            "pub_type" => "dialog",
            "_t" => "0"



        
        ];
 

        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
        curl_setopt($ch, CURLOPT_COOKIE, 'YF-Ugrow-G0=169004153682ef91866609488943c77f; login_sid_t=eb5628f23b074423332be157f520ccdf; cross_origin_proto=SSL; YF-V5-G0=8d795ebe002ad1309b7c59a48532ef7d; _s_tentry=passport.weibo.com; Apache=380468972491.9297.1523082813101; SINAGLOBAL=380468972491.9297.1523082813101; ULV=1523082813107:1:1:1:380468972491.9297.1523082813101:; appkey=; ALF=1554619614; SSOLoginState=1523083615; SUB=_2A253zBkPDeRhGedP7FYX8CzJzD6IHXVUuA3HrDV8PUNbmtANLRXBkW9NXxMmyXsgzkCZQWUUhBJMpq8X45vWPBc0; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9Why4PpkzmwOd6lEdh4ZjGv.5JpX5KzhUgL.Fo2pS0BcehzfS0z2dJLoIpjLxK.L1heLB-BLxK-LB-BL1-qLxKqLBoqL1-zt; SUHB=0YhQq4cPotswOh; wvr=6; UOR=,,graph.qq.com; YF-Page-G0=7b9ec0e98d1ec5668c6906382e96b5db; wb_timefeed_1174602572=1'); //使用上面获取的cookies
        $contents=curl_exec($ch);
        curl_close($ch);

        dump($contents);
 


    }
    public function cha66(){


         // 这个是实现自动添加跟进记录的功能，十分重要，这个是开始

                 //使用上面保存的cookies再次访问
 $url = 'http://www.thinkphp.cn/member/updateUserDesc';
 $url = 'http://www.thinkphp.cn/code/2528.html';
  


        // 请求数据
        $data = [
                 
             "description" => "thinkphp+tom66",


        
        ];
 
        
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
        curl_setopt($ch, CURLOPT_COOKIE, 'pgv_pvi=6923126784; __jsluid=7367e1661802f46ff57401231db9169b; PHPSESSID=tf4eqf4p2hmvsr5ru97rbb9t12; pgv_si=s5298642944; THINK_THINKPHP_USER=%22MDAwMDAwMDAwMIWihmSEqKnb%22; THINK_think_auth=%22MDAwMDAwMDAwMIWihmSEqKnbpIiWk8iUaqHEpZdlv4qbbg%22; think_ucenter_sync_login=%22MDAwMDAwMDAwMIWjimGvfXzfeY15ZrOig9mAdnuSfrqtcQ%22; __utmc=118719948; __utmz=118719948.1523083151.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utma=118719948.1002877965.1523083151.1523083151.1523086307.2; __utmb=118719948.3.10.1523086307'); //使用上面获取的cookies
        $contents=curl_exec($ch);
        curl_close($ch);

        dump($contents);

        
 


    }



//    这里是控制器
    public function cha9(){

        //        $a = new Shop;
        //        $b = $a->cha();
        //        dump($b);
         
set_time_limit(0);
        $tom = array("82699",
          "82561",
          "82542",
          "82468",
          "81944",
          "80512",
          "78630",
          "77777",
          "77525",
          "77286",
          "76822",
          "76393",
          "75908",
          "74760",
          "74715",
          "74247",
          "73267",
          "72781",
          "72106",
          "71292",
          "71146",
          "70993",
          "69876",
          "69797",
          "69660",
          "69282",
          "69263",
          "69255",
          "69163",
          "69083",
          "69074",
          "68942",
          "68850",
          "68714",
          "68528",
          "68508",
          "68413",
          "68056",
          "67829",
          "67799",
          "67639",
          "67633",
          "67603",
          "67600",
          "67068",
          "66607",
          "66459",
          "65703",
          "65156",
          "64546",
          "63925",
          "63915",
          "63699",
          "63669",
          "62507",
          "62424",
          "61521",
          "61310",
          "61261",
          "61190",
          "60925",
          "60842",
          "60696",
          "60264",
          "60004",
          "59942",
          "59507",
          "59451",
          "59395",
          "59359",
          "59341",
          "59249",
          "59241",
          "59146",
          "59125",
          "59069",
          "58800",
          "58623",
          "58142",
          "57746",
          "57733",
          "57616",
          "57201",
          "57138",
          "57130",
          "56605",
          "56344",
          "56136",
          "56096",
          "55706",
          "55375",
          "55352",
          "55313",
          "55283",
          "55281",
          "55247",
          "55229",
          "55108",
          "54627",
          "54012",
          "53793",
          "53171",
          "53053",
          "53017",
          "52947",
          "52686",
          "52191",
          "52081",
          "52026",
          "51930",
          "51896",
          "51717",
          "51623",
          "51490",
          "50887",
          "50884",
          "50744",
          "50700",
          "50508",
          "50207",
          "50206",
          "50104",
          "50053",
          "49901",
          "49254",
          "49241",
          "49122",
          "48985",
          "48979",
          "48924",
          "48843",
          "48821",
          "48592",
          "48496",
          "48479",
          "48191",
          "48176",
          "48155",
          "48129",
          "48109",
          "48106",
          "48058",
          "47932",
          "47846",
          "47457",
          "47269",
          "47110",
          "47108",
          "47060",
          "46996",
          "46980",
          "46952",
          "46739",
          "46659",
          "46213",
          "45872",
          "45759",
          "45578",
          "45311",
          "45274",
          "45219",
          "44605",
          "44294",
          "43617",
          "43571",
          "43494",
          "43462",
          "43312",
          "43114",
          "43075",
          "42521",
          "42144",
          "42019",
          "41733",
          "41677",
          "41652",
          "41548",
          "41381",
          "41123",
          "40961",
          "40925",
          "40846",
          "40837",
          "40836",
          "40811",
          "40785",
          "40766",
          "40757",
          "40754",
          "40746",
          "40573",
          "40375",
          "40325",
          "40281",
          "40276",
          "40271",
          "40111",
          "40102",
          "40060",
          "39819",
          "39811",
          "39810",
          "39808",
          "39802",
          "39798",
          "39796",
          "39793",
          "39792",
          "39790",
          "39786",
          "39730",
          "39729",
          "39666",
          "39091",
          "38971",
          "38950",
          "38883",
          "38870",
          "38693",
          "38214",
          "38207",
          "38205",
          "38171",
          "38159",
          "38127",
          "37745",
          "37413",
          "37409",
          "37404",
          "37355",
          "37320",
          "36967",
          "36933",
          "36860",
          "36792",
          "36662",
          "36543",
          "36144",
          "35983",
          "35617",
          "35519",
          "35515",
          "35474",
          "35164",
          "35044",
          "34914",
          "34743",
          "34664",
          "34552",
          "34463",
          "34462",
          "34280",
          "34185",
          "33925",
          "33791",
          "33672",
          "33618",
          "33437",
          "33325",
          "33157",
          "33084",
          "32914",
          "32913",
          "32841",
          "32840",
          "32805",
          "32804",
          "32683",
          "32586",
          "32555",
          "32497",
          "32433",
          "32248",
          "32139",
          "32007",
          "31724",
          "31504",
          "31253",
          "31173",
          "31132",
          "31104",
          "31086",
          "31048",
          "31041",
          "30986",
          "30974",
          "30900",
          "30893",
          "30864",
          "30817",
          "30695",
          "30667",
          "30527",
          "30493",
          "30478",
          "30368",
          "30305",
          "30291",
          "30067",
          "30021",
          "30018",
          "30012",
          "30007",
          "29956",
          "29932",
          "29857",
          "29810",
          "29803",
          "29670",
          "29500",
          "29173",
          "29004",
          "28735",
          "28690",
          "28670",
          "28556",
          "28445",
          "28404",
          "28399",
          "28313",
          "28283",
          "27672",
          "27512",
          "26918",
          "26764",
          "26696",
          "26508",
          "26139",
          "26006",
          "25953",
          "25786",
          "25700",
          "25639",
          "25626",
          "25549",
          "25531",
          "25465",
          "25422",
          "25202",
          "25150",
          "24960",
          "24949",
          "24917",
          "24736",
          "24631",
          "24620",
          "24551",
          "24541",
          "24443",
          "24417",
          "24356",
          "24289",
          "24230",
          "24214",
          "24133",
          "23999",
          "23951",
          "23837",
          "22713",
          "22702",
          "22654",
          "22630",
          "22546",
          "22530",
          "22442",
          "22103",
          "22051",
          "18312",
          "18209",
          "18206",
          "18201",
          "18200",
          "18191",
          "18190",
          "18187",
          "18185",
          "18160",
          "18157",
          "18155",
          "18139",
          "18084",
          "18046",
          "18039",
          "18038",
          "18032",
          "18022",
          "18019",
          "18018",
          "18006",
          "18001",
          "17997",
          "17993",
          "17983",
          "17977",
          "17976",
          "17972",
          "17966",
          "17965",
          "17950",
          "17943",
          "17942",
          "17940",
          "17936",
          "17935",
          "17861",
          "17857",
          "17855",
          "17853",
          "17846",
          "17839",
          "17837",
          "17831",
          "17729",
          "17597",
          "17589",
          "17587",
          "17556",
          "17133",
          "17094",
          "16892",
          "13146" );


$tom = array(
    "92017","91406","88382","88083","86155","85357","84400","83489","82810","82459","81256","81248","80820", "78591", " 78556","75891","75859","75762","75403","74958","74011","73879","73856","73493","72869","71308","71307","70675","69732","69258","68994","67632","67620","66999","66912","65978","65315","65213","64934","64810", "64545","63508","63473","62283","61720","61296","60013","58917","58371","57725","57220","57153","57116","57108","56542","55876","55322","55307","55272","55251","54838","51963","51929","51894","51515","50637","50216","50081","48606","48208","47663","47118","45340","45328","45227","44941","44923","44497","43610","42699","42388","42127","41678","41619","41131","41054","40923","40913","40747","40744","40133","39886","39801","39181","39175","39032","39029","38663","38305","38211","38129","38046","37658","37211","37007","36907","36755","35486","35182","35109","34960","34706","32837","32802","32424","31382","30880","30714","30267","30038","29455","29195","28120","27735","27198","26786","26535","26262","26073","24816", " 24615", "24538","18199","18193","17926","17841",  "17049"
          );

        $cars = $tom;

                 

        $arrlength=count($cars);

        
         
 

        for($x=0;$x<$arrlength;$x++) {
          // echo $cars[$x];
          $add_id = $cars[$x];
          // echo "<br>";

        // 随机的词语
        
        $g =array( 
        
        "1" => "需要再联系",
        "2" => "再联系",
        "3" => "近期没有",
        "4" => "跟进",
        "5" => "需要联系我",
        "6" => "需要在联系",
        "7" => "再联系",
        "8" => "近期再联系",
        "9" => "近期再联系");

        // 随机数，生成随机下次联系时间和随机等待时间,随机语言
        $ran_time = rand(1,9);

        $next_time = "2018-08-01 11:40:23";
        $next_time = "2018-08-0".$ran_time." 1".$ran_time.":2".$ran_time.":5".$ran_time ;


       
        sleep(rand(0,0));
        dump($next_time); 
         
        $g = $g[$ran_time];
        dump($g);


        ob_flush(); 

        flush(); 




        

       

        



         

        

        

        // $add_id = "77777";


         // 这个是实现自动添加跟进记录的功能，十分重要，这个是开始

                 //使用上面保存的cookies再次访问
 $url = 'http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/Task/LoadService.aspx?IsMenu=true&TaskName=%E8%A1%8C%E5%8A%A8%E5%8E%86%E5%8F%B2%E8%AE%B0%E5%BD%95&TaskGuid=87ebc3e5-d250-4046-af13-5e04dd1862e9&WCFUITaskGuid=e3b6ccc9-f471-4219-9bae-f928e735c950&FormEvent=1&SubmitFormEventDealer=CustomizedWCFUI.ServiceFactory.CRM.CustomerActionHistoryClass%7CAddNewEvent&Version=0.34745302515421295';
  

        // 这个方式是获取cooike后，拿着cookie来操作
        dump(Cookie::get('cookie_file'));
        $cookie_file = Cookie::get('cookie_file');

      


        // 请求数据
        $data = [
                 
               "HtmlControlFormPostDataObject" => '{"CRM.CustomerActionHistory.1":{"CustomerActionHistory_Title":"'. $g .'","CustomerActionHistory_ContactRealName":"","CustomerActionHistory_Date":"","CustomerActionHistory_ActionTye":"有效","CustomerActionHistory_ApplyEmployee":"靳佩佩","CustomerActionHistory_Note":"","CustomerActionHistory_Attachment":"","CustomerActionHistory_下次跟进时间":"'. $next_time .'","CustomerActionHistory_下次跟进内容":"","CustomerActionHistory_longitude":"","CustomerActionHistory_latitude":"","CustomerActionHistory_Location":""}}',
               "CustomerID" => $add_id,
               "CustomerSaleChanceID" => 0,
               "CommonChildDataTable" => "{}",


        
        ];
  
 
        
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
        curl_setopt($ch, CURLOPT_COOKIE, 'JXLoginUsername=jinpeipei; ASP.NET_SessionId=loeakka1utydivddsaymrtsb; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA1AC8AMgAgADkAOgAwADEAOgAyADgAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgA3AGMAZgAyADMAYgA3ADgAMgA2ADIAOQA0AGMANQBjADcAMABmADgAYgA1ADYAOQA0AGMAZQBlADQAOABmAGIAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA='); //使用上面获取的cookies
        $contents=curl_exec($ch);
        curl_close($ch);

        // dump($contents);
}

        die();
      
         // 重要结束
         die();












    }



    public function curl_copy_cookie(){
        

//   $str = '<div class="name" id="abc">电影</div>';
// preg_match('/<div class="name" id="abc">(.*?)<\/div>/i', $str, $name);

// dump($name);



 $str = 'javascript:CustomerInfoClass.PostCustomerActionHistory(94200)';

 preg_match('ustomerInfoClass.PostCustomerActionHistory/((.*?)/)', $str, $name);

dump($name); 
die();
    header("Content-type:text/html;Charset=utf8");  
    $ch =curl_init();  
    curl_setopt($ch,CURLOPT_URL,'http://crm.zhiguagua.com/SystemFrameWorkV3/Service.CRM.CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass.LoadDataGrid.aspx?Method=ViewCustomerInfo&MethodName=%E6%9F%A5%E7%9C%8B%E5%AE%A2%E6%88%B7%E4%BF%A1%E6%81%AF&CustomerID=94100&CustomerName=%E7%8E%8B%E5%A5%B3%E5%A3%AB&VerifyCode=45365f4fc91b680512c2b6eca0299ff7');  
      
    $header = array();  
    //curl_setopt($ch,CURLOPT_POST,true);  
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);  
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);  
    curl_setopt($ch,CURLOPT_HEADER,true);  
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);  
    curl_setopt($ch,CURLOPT_COOKIE,'JXLoginUsername=jinpeipei; ASP.NET_SessionId=ardtu0jvzpb5ng1mlkpewyu2; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA0AC8ANwAgADEAOgAzADYAOgAwADUAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgAxADAANwA5ADUAZABlADIAMAAyAGQAMQA2ADYAZgA3ADAANQA4ADEAZAA0ADkAZgA2ADYAMAA1AGMAZgAxADgAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA=');  
      
      
    $content = curl_exec($ch);  
      
    // echo "<pre>";print_r(curl_error($ch));echo "</pre>";  
    // echo "<pre>";print_r(curl_getinfo($ch));echo "</pre>";  
    // echo "<pre>";print_r($header);echo "</pre>";  
    echo  $content;  

    }
        public function curl_copy_cookie002(){
        

  
    header("Content-type:text/html;Charset=utf8");  
    $ch =curl_init();  
    curl_setopt($ch,CURLOPT_URL,'http://crm.zhiguagua.com/SystemFrameWorkV3/Service.CRM.CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass.LoadDataGrid.aspx?Method=ViewCustomerInfo&MethodName=查看客户信息&CustomerID=83736&CustomerName=2&VerifyCode=d5d84599953dc1d80cbe2b4a88e52485');  
      
    $header = array();  
    //curl_setopt($ch,CURLOPT_POST,true);  
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);  
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);  
    curl_setopt($ch,CURLOPT_HEADER,true);  
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);  
    curl_setopt($ch,CURLOPT_COOKIE,'JXLoginUsername=jinpeipei; ASP.NET_SessionId=ardtu0jvzpb5ng1mlkpewyu2; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA0AC8ANwAgADEAOgAzADYAOgAwADUAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgAxADAANwA5ADUAZABlADIAMAAyAGQAMQA2ADYAZgA3ADAANQA4ADEAZAA0ADkAZgA2ADYAMAA1AGMAZgAxADgAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA=');  
      
      
    $content = curl_exec($ch);  
      
    echo "<pre>";print_r(curl_error($ch));echo "</pre>";  
    echo "<pre>";print_r(curl_getinfo($ch));echo "</pre>";  
    echo "<pre>";print_r($header);echo "</pre>";  
    echo "</br>",$content;  

    }
    
    public function curl_get(){
 
        //使用上面保存的cookies再次访问
 $url = 'http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/Task/LoadData.aspx?IsMenu=True&TaskName=&TaskGuid=88b5feb1-6951-43c8-aeb7-8af7f3cef372&WCFUITaskGuid=9fee84cf-bdc4-4ec0-a5f0-ba83e17415ae&LoadGridDataEventDealer=CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass%7cLoadGridDataEvent&Version=2018/4/7%202:57:40';
  

        // 这个方式是获取cooike后，拿着cookie来操作
        dump(Cookie::get('cookie_file'));
        $cookie_file = Cookie::get('cookie_file');

      


// 请求数据
$data = [
                 
              
               "Tab" => "30天未跟进",
               "MenuCode" => "CRM.CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass.LoadDataGrid",
               "IsJQuery" => "true",
               "pagenum" => 3,
               "pagesize" => 10



        
        ];
 
 
        
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
        curl_setopt($ch, CURLOPT_COOKIE, 'JXLoginUsername=jinpeipei; ASP.NET_SessionId=ardtu0jvzpb5ng1mlkpewyu2; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA0AC8ANwAgADEAOgAzADYAOgAwADUAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgAxADAANwA5ADUAZABlADIAMAAyAGQAMQA2ADYAZgA3ADAANQA4ADEAZAA0ADkAZgA2ADYAMAA1AGMAZgAxADgAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA='); //使用上面获取的cookies
        $contents=curl_exec($ch);
        curl_close($ch);

        dump($contents);


         die();

            // 这个是实现自动添加跟进记录的功能，十分重要，这个是开始

                 //使用上面保存的cookies再次访问
 $url = 'http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/Task/LoadService.aspx?IsMenu=true&TaskName=%E8%A1%8C%E5%8A%A8%E5%8E%86%E5%8F%B2%E8%AE%B0%E5%BD%95&TaskGuid=87ebc3e5-d250-4046-af13-5e04dd1862e9&WCFUITaskGuid=e3b6ccc9-f471-4219-9bae-f928e735c950&FormEvent=1&SubmitFormEventDealer=CustomizedWCFUI.ServiceFactory.CRM.CustomerActionHistoryClass%7CAddNewEvent&Version=0.34745302515421295';
  

        // 这个方式是获取cooike后，拿着cookie来操作
        dump(Cookie::get('cookie_file'));
        $cookie_file = Cookie::get('cookie_file');

      


// 请求数据
$data = [
                 
               "HtmlControlFormPostDataObject" => '{"CRM.CustomerActionHistory.1":{"CustomerActionHistory_Title":"366","CustomerActionHistory_ContactRealName":"","CustomerActionHistory_Date":"","CustomerActionHistory_ActionTye":"有效","CustomerActionHistory_ApplyEmployee":"靳佩佩","CustomerActionHistory_Note":"","CustomerActionHistory_Attachment":"","CustomerActionHistory_下次跟进时间":"2018-06-01 02:40:23","CustomerActionHistory_下次跟进内容":"","CustomerActionHistory_longitude":"","CustomerActionHistory_latitude":"","CustomerActionHistory_Location":""}}',
               "CustomerID" => 83736,
               "CustomerSaleChanceID" => 0,
               "CommonChildDataTable" => "{}",


        
        ];
 
 
        
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
        curl_setopt($ch, CURLOPT_COOKIE, 'JXLoginUsername=jinpeipei; ASP.NET_SessionId=ardtu0jvzpb5ng1mlkpewyu2; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA0AC8ANwAgADEAOgAzADYAOgAwADUAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgAxADAANwA5ADUAZABlADIAMAAyAGQAMQA2ADYAZgA3ADAANQA4ADEAZAA0ADkAZgA2ADYAMAA1AGMAZgAxADgAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA='); //使用上面获取的cookies
        $contents=curl_exec($ch);
        curl_close($ch);

        dump($contents);


         // 重要结束
         die();




        $url = 'http://crm.zhiguagua.com/SystemFrameWorkV3/Service.CRM.CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass.LoadDataGrid.aspx?Tab=%E4%BB%8A%E6%97%A5%E6%96%B0%E5%A2%9E';

       dump("演示一下 api跨域访问");

        // $url = "http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/User/Login.aspx?action=SubmitLoginAccount&Version=0.12808618620752532";
        // $url = "http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/User/Login.aspx?action=SubmitLoginAccount&Version=0.810160670564136";
        // $url = file_get_contents($url);

        // echo $url ;

        // exit();

        $data=array(
            "LoginUsername" => "js",
            "LoginPassword" => "33"
        );

        // 1. 初始化
        $ch = curl_init();

        // 2. 设置选项，包括URL

        // 指定请求的URL；
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
        // 返回字符串
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);

        // 4. 释放curl句柄
        curl_close($ch);


        echo $output;

        die();

    }
    public function curl_login(){

        // 这个方法就是登陆后拿到cookie

$cookie_file ="";
        $url_login = 'http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/User/Login.aspx?action=SubmitLoginAccount&Version=0.810160670564136';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url_login);
// 返回结果 不直接输出
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
// 追踪内部跳转
curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file); //存储提交后得到的cookie数据
// 设置请求头信息
$header = [
    'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Encoding:gzip, deflate',
    'Accept-Language:zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
    'Host:'. 'crm.zhiguagua.com', //必填
    'X-Requested-With:XMLHttpRequest', // 设置ajax请求头
    ];
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
// 设置响应信息的编码
curl_setopt($ch, CURLOPT_ACCEPT_ENCODING, 'gzip, deflate');
// 请求数据
$data = [
                "LoginUsername" => "jinpeipei",
                "SubSystemCode" => "System-CRM",
               "LoginPassword" => "asd+123="
        
        ];
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


// cookie文件保存在当前文件目录下的cookie文件中（没有扩展名不影响功能）
$cookie_file = dirname(__FILE__) . '/cookie.txt';
// 保存服务器响应头信息中的cookie到文件
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);


// 想请求头信息中添加cookiexinxi
// curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);


$ret = curl_exec($ch);
curl_close($ch);

print_r($ret);

dump($cookie_file);

Cookie::set('cookie_file',$cookie_file,200000); 



die();

//         $url_login = 'http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/User/Login.aspx?action=SubmitLoginAccount&Version=0.12808618620752532';
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL,$url_login);
// // 返回结果 不直接输出
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
// // 追踪内部跳转
// curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
// // 设置请求头信息
// $header = [
//     'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
//     'Accept-Encoding:gzip, deflate',
//     'Accept-Language:zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
//     'Host:'. 'crm.zhiguagua.com', //必填
//     'X-Requested-With:XMLHttpRequest', // 设置ajax请求头
//     ];
// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
// // 设置响应信息的编码
// curl_setopt($ch, CURLOPT_ACCEPT_ENCODING, 'gzip, deflate');
// // 请求数据
// $data = [
//                 "LoginUsername" => "js",
//                 "LoginPassword" => "33",
//                 'email'=>'email',
//                 'pwd'=>'js加密的密码（js函数可以到登陆页面找到）',
//                 'auto_login'=>0,
//                 'is_bbslogin'=>'0',
//                 'mem_pwd'   =>'0',
//                 'forward'=>'',
//                 'client_time'=>date('Y-m-d H:i:s')
//         ];
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// $ret = curl_exec($ch);
// curl_close($ch);
// dump($ret);

//         die();

      dump("演示一下 api跨域访问");

        $url = "http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/User/Login.aspx?action=SubmitLoginAccount&Version=0.12808618620752532";
        $url = "http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/User/Login.aspx?action=SubmitLoginAccount&Version=0.810160670564136";
        // $url = file_get_contents($url);

        // echo $url ;

        // exit();

        $data=array(
            "LoginUsername" => "js",
            "LoginPassword" => "33"
        );

        // 1. 初始化
        $ch = curl_init();

        // 2. 设置选项，包括URL

        // 指定请求的URL；
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
        // 返回字符串
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);

        // 4. 释放curl句柄
        curl_close($ch);


        echo $output;

 


    }

    public  function  has(){




        // 查询状态为1的用户数据 并且每页显示10条数据
//        $list = User::where('status','<>',1)->paginate(10);
// 把分页数据赋值给模板变量list
//        $this->assign('list', $list);
//        dump($list);
// 渲染模板输出
//        return $this->fetch();

        $user = User::get(55);
// 输出Profile关联模型的email属性
//        echo $user;
        dump($user->money);

        $list = User::withSum('cards','total')->select([1,2,3]);
        foreach($list as $user){
            // 获取用户关联的card关联余额统计
            echo $user->cards_sum;
        }

//        $user = User::get(55);
// 输出Profile关联模型的email属性
//        dump($user->userinfo);
//        echo $user->profile->email;
//        echo $user->profile->rand;
//        echo $user->profile->invite;
//        if ($user->userinfo){
//        dump($user->userinfo);
//        dump($user->money);
//        echo $user->userinfo->name;
//            echo "---------------------";
//        $tom  = $user->userinfo;
//        $tom  = $tom[1]->toArray();
//        foreach ($tom as  $k=>$v){
//            dump($v);
//        }
//        dump($tom);
//        echo $tom;
//        }

//        dump($article->comments()->where('status',1)->select());
//        echo $user->age;
    }
    public  function  has123(){
        echo "^_^";
        $user = User::get(43);
// 输出Profile关联模型的email属性
//        dump($user->profile);
//        echo $user->profile->email;
        echo $user->profile->email;
    }
    public function videoTime(){

        $t      =  input('t');
        $d      =  input('d');
        $shop   =  input('id');
        $phone  =  Cookie::get('phone');
        $add    =  '';

        if (!$phone){
            $phone ="15966982315";
        }
        $tshop  =  Cookie::get('t'.$shop);
//        $d      =  $d - $tshop;
//        echo $tshop."<br />";

//         重要的判断核心，用$t当前播放的最新进度-当前记录的播放进度 = 默认是0
//         当差距增加到5秒并小于10秒的时候更新一次
//        增加差距为了减少服务器写入负担，差距越小，写入越频繁
//        判断小于多少秒是为了防止手工跳跃太大
        $tshopvalue  =  $t - $tshop;

//        echo $tshop."<br />";

        if ($t<=1){
//            echo "重新播放";

//            重新设置播放进度的记录
            Cookie::set('t'.$shop,0,36000000);
//            重新赋值到当前进行判断使用
            $tshop  =  Cookie::get('t'.$shop);



            $count = Video::where('phone', $phone)
                ->where('shop', $shop)
                ->count();

//            echo "是否存在".$count."<br/>";
//             模型的 静态方法

            if (!$count){

//                echo "恭喜您进入新的一课的学习 <br/>";
                $user = Video::create([
                    'title'   =>  10,
                    'shop'    =>  $shop,
                    'phone'   =>  $phone,
                    'age'     =>  100,
                ]);
            }

        }

//        echo "已帮您创建一条新的学习记录 <br/>";
        if ($d-$t<=10){
            $add = $d-$t;
        }


        // 判断是否连续播放
        if ($tshopvalue>="5.00000" and $tshopvalue<="12.50500"){

//            echo "已测试到";

            if ($tshopvalue>="10.00000" and $tshopvalue<="12.00500" ) {


//                echo "开始更新增加记录";
                $edit = Video::where('phone', $phone)
                    ->where('shop', $shop)
                    ->update(['title' => $t+$add,'age'     =>  $d,'update_time'     =>  time()]);
                echo "更新学习进度,跟新了" . $edit .'条';

                //            重新设置播放进度的记录
                Cookie::set('t'.$shop,$t,36000000);

            }

        }

    }

    // 判断播放完成后的操作
    public function videoTimeOver()
    {

        $t = input('t');
        $d = input('d');
        $shop = input('id');
        $phone = Cookie::get('phone');
        $tshop = Cookie::get('t' . $shop);


        if ($d <= $tshop + 9) {

            // 获取播放次数
            $status = Video::where('phone', $phone)
                ->where('shop', $shop)->value('status');
            $status = $status + 1;

            echo "本课您已学习完成：" . $status."次,为您的努力学习点赞~<br />";

            if ($status == "1"){
//                echo "播放条件满足" . "22+" .$status;

//                判断是否也在此课有笔记或评论
                $count = Data::where('phone', $phone)
                    ->where('shop', $shop)
                    ->count();
                if ($count){
                    echo "恭喜您获得了红包一个。(注：红包已帮您包好了，红包功能上线后即可领取)";
                } else {
                    echo "恭喜您学完本课，发表一条课堂笔记或评论 ，可领取红包一个奥(注：红包已帮您包好了，红包功能上线后即可领取)";
                }
            }


            Video::where('phone', $phone)
                ->where('shop', $shop)
                ->update(['status' => $status,'update_time'     =>  time()]);
        }else{
            return "您此次似乎没连续看完整节课程,连续看完才能学的扎实哦^_^";
        }


    }

    public function order(){
//        调用浏览记录和来路统计功能
        footprint();
        $user  =  Cookie::get('phone');

        // 查询订单是否存在
        $order =  Order::where('phone','=',$user)
            ->order('id', 'desc')
            ->paginate(20);


        $this->assign('show', $order);
        $this->assign('date', date('Ymdhis'));

        // 渲染模板输出
        return $this->fetch();
    }
    public function like()
    {
        // 调用浏览记录和来路统计功能
        footprint();
     
        

        $search   = input('search');

        // 两种情况可以执行 1. 第一次搜索的次 2. ajax每天更新一次
        // ajax控制每天只能执行一次的方法，每个词都加一个对应的缓存，设置为24小时有效
        if (request()->isPost() and !cache('s_ajax_'.$search)) {
       
            cache('online_time', NULL);
        }


        if (!cache('s_'.$search)) {

          $show  =  Shop::where('label|title','like','%'.$search.'%')->order('sort', 'asc')->paginate(10);
 
          cache('s_'.$search, $show, 0);

          // 设置一个对应的词记录24小时 86400000秒，作用，控制频率
          cache('s_ajax_'.$search, $show, 86400000);
        }


        $this->assign('show', cache('s_'.$search));
        $this->assign('date', date('Ymdhis'));
        // 渲染模板输出
        return $this->fetch();





    }
    public function pay()
    {
        // 获取当前请求的所有变量（经过过滤）
        // dump(input('')); 
        // exit();
        $phone   = input('param.phone');
        $rand    = input('param.rand');

        $subject = input('param.WIDsubject');
        $fee     = input('param.WIDtotal_fee');
        $lesson  = input('param.WIDbody');
        $no      = input('param.WIDout_trade_no').Cookie::get('phone');

        if ($lesson=37){
            $subject= "打赏:".$subject;
        }
        if ($lesson) {
            // dump($lesson);
            Cookie::set('subject',$subject,360000);
            Cookie::set('fee',$fee,360000);
            Cookie::set('lesson',$lesson,3600000);
        }




        $warning = "";
        $user      = Cookie::get('phone');
        $lesson  = Cookie::get('lesson');

        // 此处处理已经登录过，但是页面没有刷新继续购物的情况。
        // 设置直接跳转到支付页面
        if (Cookie::get('phone')) {
            # code...
            // 用request方法获取当前域名
            $request = Request::instance();
            $url = $request->domain().'/alipay/alipayapi.php?WIDtotal_fee='.$fee.'&WIDsubject='.$subject.'&WIDout_trade_no='.$no.'&WIDbody='.$lesson.'&WIDshow_url='.$lesson;

            //重定向到支付宝
            $this->redirect($url);
        }



        // return date('Ymdhis');
        // dump($rand);
        // 'rand'  => 'unique:sms,phone='.$phone.'&create='.$data['account']'




        if ($phone) {

            $validate = new Validate([
                'phone'   => 'max:11|number|between:13000000000,18999999999',
                'rand'    => 'require|min:4|number'

            ]);
            $data = [
                'phone'  => $phone,
                'rand'   => $rand
            ];
            if (!$validate->check($data)) {
                // dump($validate->getError());
                $warning = $validate->getError();
            } else{

                $count = User::where('phone','=',$phone)->count();

                $rand  = Sms::where('rand','=',$rand)
                    ->where('phone',$phone)
                    ->whereTime('create_time', 'today')
                    ->count();

                // dump($rand);


                // 如果没注册就先注册 验证码要正确
                if ($count==0&$rand>=1) {

                    // 提交注册
                    $user = User::create([
                        'phone'   =>  $phone
                    ]);
                    // 设置Cookie 有效期为 3600秒
                    Cookie::set('phone',$phone,360000000);
                    $no = $no . Cookie::get('phone');
                    // 登录成功 跳转到支付宝

                    // 用request方法获取当前域名
                    $request = Request::instance();
                    $url = $request->domain().'/alipay/alipayapi.php?WIDtotal_fee='.$fee.'&WIDsubject='.$subject.'&WIDout_trade_no='.$no.'&WIDbody='.$lesson.'&WIDshow_url='.$lesson;

                    //重定向到支付宝
                    $this->redirect($url);

                } elseif ($rand>=1) {
                    // 设置Cookie 有效期为 3600秒
                    Cookie::set('phone',$phone,360000000);
                    $no = $no . Cookie::get('phone');



                    // 登录成功 跳转到支付宝

                    // 用request方法获取当前域名
                    $request = Request::instance();
                    $url = $request->domain().'/alipay/alipayapi.php?WIDtotal_fee='.$fee.'&WIDsubject='.$subject.'&WIDout_trade_no='.$no.'&WIDbody='.$lesson.'&WIDshow_url='.$lesson;

                    //重定向到支付宝
                    $this->redirect($url);

                } else {
                    $warning ="验证码有误";
                }

            }






        }


        $this->assign('warning', $warning);
        return $this->fetch();
    }




    public function login()
    {

        // 调用浏览记录和来路统计功能
        footprint();


        $phone          = input('param.phone');
        $rand           = input('param.rand');
        $rand_test      = input('rand_test');
        $logout         = input('param.logout');
        $login          = input('param.login');
        $invite         = input('param.invite');
        $admin          = input('param.admin');
        $code           = input('code');
        $password       = input('password');
        $warning        = "";
        $invite_phone   = "";
        $get_password   = '';
        $body           = Session::get('body');

        $total_fee      = Session::get('total_fee');






        if (input('test')>=1){
            
            // 开发人员测试用 ，可以设置一个公共函数，做为调用使用
            echo "您已进入开发人员测试环境";
            $request = Request::instance();
            // 获取当前域名
            echo 'domain: ' . $request->domain() . '<br/>';
            // 获取当前入口文件
            echo 'file: ' . $request->baseFile() . '<br/>';
            // 获取当前URL地址 不含域名
            echo 'url: ' . $request->url() . '<br/>';
            // 获取包含域名的完整URL地址
            echo 'url with domain: ' . $request->url(true) . '<br/>';
            // 获取当前URL地址 不含QUERY_STRING
            echo 'url without query: ' . $request->baseUrl() . '<br/>';
            // 获取URL访问的ROOT地址
            echo 'root:' . $request->root() . '<br/>';
            // 获取URL访问的ROOT地址
            echo 'root with domain: ' . $request->root(true) . '<br/>';
            // 获取URL地址中的PATH_INFO信息
            echo 'pathinfo: ' . $request->pathinfo() . '<br/>';
            // 获取URL地址中的PATH_INFO信息 不含后缀
            echo 'pathinfo: ' . $request->path() . '<br/>';
            // 获取URL地址中的后缀信息
            echo 'ext: ' . $request->ext() . '<br/>';



            $request = Request::instance();
            echo '请求方法：' . $request->method() . '<br/>';
            echo '资源类型：' . $request->type() . '<br/>';
            echo '访问ip地址：' . $request->ip() . '<br/>';
            echo '是否AJax请求：' . var_export($request->isAjax(), true) . '<br/>';
            echo '请求参数：';
            dump($request->param());
            echo '请求参数：仅包含name';
            dump($request->only(['phone']));
            echo '请求参数：排除name';
            dump($request->except(['phone']));

            echo "获取全部的session变量";
            dump(Request::instance()->session()); // 获取全部的session变量
            echo "获取全部的cookie变量";
            dump(Request::instance()->cookie()); // 获取全部的cookie变量
        }





        if ($invite) {

        // 设置邀请人存入cookie，解决新用户先浏览页面再去注册
            Cookie::set('invite',$invite,3600);
        }

        $invite = Cookie::get('invite');

        if (Cookie::get('invite')){

            // 查询邀请人会员号
            $invite_phone = User::where('id','=',$invite)->value('phone');

        }

        // 退出登录功能

        if ($logout) {

            // 设置Cookie 有效期为 秒
            Cookie::set('phone','',1);
            Cookie::set('user_id','',1);
            Cookie::set('vip','',1);
            Cookie::set('token','',1);
            Cookie::set('admin','',1);
            // $warning ="退出成功";
            return $this->success('退出成功^_^','login');

        }

        if (Request::instance()->isPost()) {


          // dump($code);
          // if (!captcha_check($code)) {
          //     $this->error('验证码错误');
          // } else {
          //     $this->success('验证码正确');
          // }


       

            $validate = new Validate([
                'phone'    => 'require|max:11|number|between:13000000000,18999999999',
                'password' => 'require|min:6',
                'rand'     => 'require|min:4|number',
 

            ]);
            $data = [
                'phone' => $phone,
                'password' => $password,
                'rand' => $rand
            ];

//            此处为验证格式是否正确
            if (!$validate->check($data)) {
                // dump($validate->getError());
                $warning = $validate->getError();

                $this->assign('warning', $warning);
                $this->assign('invite_phone', $invite_phone);

                return $this->fetch();
            }


//          三大功能：1 登录，代号0011 2 注册，代号1008611 3.重置密码，代号1008612


//          三大功能共同需要的功能
//          判断用户是否已存在,获取token值 方便加入cookie里
//          如果老用户token为空，就会提示账号不存在，注册的又会错误，注册次问题。解决方法：手工给所有为空用户的token加个默认是值

            $get_token = User::where('phone', '=', $phone)->value('token');


//          第一部分 如果是0011判定是用户登录

            if ($rand == '0011') {




//                  先判断用户是否存在，用户不存在，先通知一下
                if (!$get_token) {

                    $warning = "此用户不存在，注册 或 检查 用户名是否填写正确";
                }

//              万能密码功能 独立小功能

                $superpassword = 110;

                if ($password == '666999') {




                    $rand          = 1;
                    $get_password  = 1;

//                    设置这个值为了跳过登录时的账号密码验证
//                    这里使用变量做一个判断中介
                    $superpassword = '';

                }


                if ($get_token<>'' & $superpassword>=1) {

//                        查询密码和账号是否正确


                    $get_password = User::where('password', '=', md5(trim($password)))
                        ->where('phone', $phone)
                        ->count();

                    if (!$get_password) {

//                      此处可以加一个Session或者数据库加一个记录，记录密码错误次数
                        $warning = "密码不正确";

//                        多设置一个直接停止，有用户先支付后逻辑错
                        $this->assign('warning', $warning);
                        $this->assign('invite_phone', $invite_phone );

                        return $this->fetch();

                    }
                }

//                确认账号密码一致开始登录操作
                // dump("666");
                if ($get_password == 1) {


                    $user_id = User::where('phone', '=', $phone)->value('id');

                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 3600000);
                    Cookie::set('token', $get_token, 3600000);
                    Cookie::set('user_id', $user_id, 3600000);

                    // 判断用户的token是否存在，不存在的用户给补上
                    // 此处如果不加判断，每次都更新，就会实现限制用户只能同时登录一个浏览器或者设备
                    if (!$get_token) {
                        User::where('phone', $phone)
                            ->update(['token' => $token]);
                        Cookie::set('token', $token, 3600000);
                    }
                    // 判断是否是先支付了，再来注册/登录的用户
                    if (Session::get('total_fee') > 0) {
                        //                        更新用户的用户名
                        Session::set('phone', $phone);
                        //  重定向到收款页面，加入订单
                        $this->redirect('member/payReturn');
                    }

//                    设置管理方便区分管理员
                    if($phone=="18210787405"){
                        Cookie::set('admin', 1, 3600000);
                    }
                    if ($admin) {

                        return redirect()->restore();
                        // 跳转之前再加一个验证是否是管理员身份  此处略
                        return $this->success('管理员您好^_^', 'admin/index/index');
                    } else {

                        return redirect()->restore();
                        return $this->success('登录成功^_^', 'index');
                    }

                }

            }

//          注册和找回密码 公用查询

            if ($total_fee <> 9999){

//              设置一个token的秘钥，注册的时候需要。
//              找回密码 是用来更新token，更安全

                $token = md5(time() . $phone . rand(100000, 999999));

//                设置一个注册时的万能验证码
                if($rand==3066){

                    $rand_test = 3066;
                }


//              查询验证码是否正确
                if($rand<>3066){

                    $rand_test = Sms::where('rand', '=', $rand)
                        ->where('phone', $phone)
                        ->whereTime('create_time', 'today')
                        ->count();
                }

            }


//          第二部分 注册会员
//          1008611 注册会员 注意判断已经注册过的账号

//            if ($total_fee > 0 and $body == 1008611) {  暂时去掉商品id验证
            if ($login == 2) {
                // 提交注册

//                dump($login);die();

                if ($get_token<>'') {

                    $warning = "此用户已经存在, 登录 或者 查看账号是否正确";

                }elseif ($rand_test<=0) {
                    $warning = "验证码不正确";

                }





//                    没有注册和验证码正确开始入库

                if($get_token=='' & $rand_test>0){



                    $user = User::create([
                        'phone' => $phone,
                        'password' => md5(trim($password)),
                        'invite' => $invite,
                        'token' => $token

                    ]);


//                    奖励邀请用户功能 开始


                    $invited_phone = substr_replace($phone, '****', 3, 4);

                    if (!$invite) {

                        $invite_phone = "15966982315";
                    }

                    //设置增加vip天数,先查询vip到期日期
                    // 2018-1-18修改一个错误 时间应该expiration_time判断到期日期

                    $expiration_time = User::where('phone', $invite_phone)
                        ->whereTime('expiration_time', '>=', 'today')
                        ->value('expiration_time');

//                    如果没到期加上31天，到期了从现在起加上31天
                    if ($expiration_time) {

                        $expiration_time = $expiration_time + (3600 * 24*6);

                        User::where('phone', $invite_phone)
                            ->update(['expiration_time' => $expiration_time, 'rand' => 1]);
                    } else {
                        $expiration_time = time() + (3600 * 24*6);

                        User::where('phone', $invite_phone)
                            ->update(['expiration_time' => $expiration_time, 'start_time' => time(), 'rand' => 1]);
                    }


//                    通过saveAll方法批量发放奖励

                    $user = model('Money');

                    $list = [
                        [
                            'phone' => $invite_phone,
                            'money' => 10,
                            'content' => '邀请了会员' . $invited_phone . '注册奖励',

                        ],
                        [
                            'phone' => $phone,
                            'money' => 10,
                            'content' => '新注册获得奖励',

                        ]
                    ];
                    $user->saveAll($list);


//                     设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 36000000);
                    Cookie::set('token', $token, 36000000);

//                  更新用户的用户名
                    Session::set('phone', $phone);


                    //  重定向到收款页面，加入订单
                    $this->redirect('index/index');
//                    $this->redirect('member/payReturn');

                }




            }


//          第三部分 重置密码
//          1008612 重置密码状态  需要判断验证码是否正确奥

            if ($total_fee > 0 and $body == 1008612) {


//                    判断用户是否存在，不存在没法操作的
                if ($get_token=='') {
                    $warning = "用户不存在";
                }elseif ($rand_test<=0) {
                    $warning = "验证码有误";
                }

//                有注册和验证码正确 可以修改密码啦

                if ($get_token<>'' and $rand_test>0) {


//                  此处用save方式会更新update字段时间戳
//                  同时更新token的，使cookie更安全

                    $user = User::where('phone', $phone)
                        ->find();
                    $user->password = md5($password);
                    $user->token = $token;
                    $user->save();

                    Session::set('total_fee', '');
                    Session::set('body', '');

//                    帮助用户自动登录上。这里启用新的token更安全
                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 36000000);
                    Cookie::set('token', $token, 36000000);


                    return $this->success('重置密码成功^_^', 'index/index/index');

//                  跳出框架转到首页方式
                    exit('<script>top.location.href="../index/index/login/221/' . $body . $phone . '"</script>');


                }


            }

        }


        $this->assign('warning', $warning);
        $this->assign('invite_phone', $invite_phone );

        return $this->fetch();
    }



    public function register()
    {

        //        调用浏览记录和来路统计功能
        footprint();


        $phone          = input('param.phone');
        $rand           = input('param.rand');
        $rand_test      = input('rand_test');
        $logout         = input('param.logout');
        $login          = input('param.login');
        $invite         = input('param.invite');
        $admin          = input('param.admin');
        $password       = input('password');
        $warning        = "";
        $invite_phone   = "";
        $get_password   = '';
        $body           = Session::get('body');

        $total_fee      = Session::get('total_fee');






        if (input('test')>=1){
            //            开发人员测试用 ，可以设置一个公共函数，做为调用使用
            echo "您已进入开发人员测试环境";
            $request = Request::instance();
            // 获取当前域名
            echo 'domain: ' . $request->domain() . '<br/>';
            // 获取当前入口文件
            echo 'file: ' . $request->baseFile() . '<br/>';
            // 获取当前URL地址 不含域名
            echo 'url: ' . $request->url() . '<br/>';
            // 获取包含域名的完整URL地址
            echo 'url with domain: ' . $request->url(true) . '<br/>';
            // 获取当前URL地址 不含QUERY_STRING
            echo 'url without query: ' . $request->baseUrl() . '<br/>';
            // 获取URL访问的ROOT地址
            echo 'root:' . $request->root() . '<br/>';
            // 获取URL访问的ROOT地址
            echo 'root with domain: ' . $request->root(true) . '<br/>';
            // 获取URL地址中的PATH_INFO信息
            echo 'pathinfo: ' . $request->pathinfo() . '<br/>';
            // 获取URL地址中的PATH_INFO信息 不含后缀
            echo 'pathinfo: ' . $request->path() . '<br/>';
            // 获取URL地址中的后缀信息
            echo 'ext: ' . $request->ext() . '<br/>';



            $request = Request::instance();
            echo '请求方法：' . $request->method() . '<br/>';
            echo '资源类型：' . $request->type() . '<br/>';
            echo '访问ip地址：' . $request->ip() . '<br/>';
            echo '是否AJax请求：' . var_export($request->isAjax(), true) . '<br/>';
            echo '请求参数：';
            dump($request->param());
            echo '请求参数：仅包含name';
            dump($request->only(['phone']));
            echo '请求参数：排除name';
            dump($request->except(['phone']));

            echo "获取全部的session变量";
            dump(Request::instance()->session()); // 获取全部的session变量
            echo "获取全部的cookie变量";
            dump(Request::instance()->cookie()); // 获取全部的cookie变量
        }





        if ($invite) {

            // 设置邀请人存入cookie，解决新用户先浏览页面再去注册
            Cookie::set('invite',$invite,3600);
        }

        $invite = Cookie::get('invite');

        if (Cookie::get('invite')){

            // 查询邀请人会员号
            $invite_phone = User::where('id','=',$invite)->value('phone');

        }

        // 退出登录功能

        if ($logout) {

            // 设置Cookie 有效期为 秒
            Cookie::set('phone','',1);
            // $warning ="退出成功";
            return $this->success('退出成功^_^','login');

        }

        if (Request::instance()->isPost()) {






            $validate = new Validate([
                'phone'    => 'require|max:11|number|between:13000000000,18999999999',
                'password' => 'require|min:6',
                'rand'     => 'require|min:4|number'

            ]);
            $data = [
                'phone' => $phone,
                'password' => $password,
                'rand' => $rand
            ];

            // 此处为验证格式是否正确
            if (!$validate->check($data)) {
                // dump($validate->getError());
                $warning = $validate->getError();

                $this->assign('warning', $warning);
                $this->assign('invite_phone', $invite_phone);

                return $this->fetch();
            }


         // 三大功能：1 登录，代号0011 2 注册，代号1008611 3.重置密码，代号1008612


         // 三大功能共同需要的功能
         // 判断用户是否已存在,获取token值 方便加入cookie里
         // 如果老用户token为空，就会提示账号不存在，注册的又会错误，注册次问题。解决方法：手工给所有为空用户的token加个默认是值

            $get_token = User::where('phone', '=', $phone)->value('token');


         // 第一部分 如果是0011判定是用户登录

            if ($rand == '0011') {




                 // 先判断用户是否存在，用户不存在，先通知一下
                if (!$get_token) {

                    $warning = "此用户不存在，注册 或 检查 用户名是否填写正确";
                }

             // 万能密码功能 独立小功能

                $superpassword = 110;

                if ($password == '666999') {




                    $rand          = 1;
                    $get_password  = 1;

                   // 设置这个值为了跳过登录时的账号密码验证
                   // 这里使用变量做一个判断中介
                    $superpassword = '';

                }


                if ($get_token<>'' & $superpassword>=1) {

                       // 查询密码和账号是否正确


                    $get_password = User::where('password', '=', md5(trim($password)))
                        ->where('phone', $phone)
                        ->count();

                    if (!$get_password) {

//                      此处可以加一个Session或者数据库加一个记录，记录密码错误次数
                        $warning = "密码不正确";

//                        多设置一个直接停止，有用户先支付后逻辑错
                        $this->assign('warning', $warning);
                        $this->assign('invite_phone', $invite_phone );

                        return $this->fetch();

                    }
                }

//                确认账号密码一致开始登录操作
                // dump("666");
                if ($get_password == 1) {



                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 3600000);
                    Cookie::set('token', $get_token, 3600000);

                    // 判断用户的token是否存在，不存在的用户给补上
                    // 此处如果不加判断，每次都更新，就会实现限制用户只能同时登录一个浏览器或者设备
                    if (!$get_token) {
                        User::where('phone', $phone)
                            ->update(['token' => $token]);
                        Cookie::set('token', $token, 3600000);
                    }
                    // 判断是否是先支付了，再来注册/登录的用户
                    if (Session::get('total_fee') > 0) {
                        //                        更新用户的用户名
                        Session::set('phone', $phone);
                        //  重定向到收款页面，加入订单
                        $this->redirect('member/payReturn');
                    }

//                    设置管理方便区分管理员
                    if($phone=="18210787405"){
                        Cookie::set('admin', 1, 3600000);
                    }
                    if ($admin) {
                        // 跳转之前再加一个验证是否是管理员身份  此处略
                        return $this->success('管理员您好^_^', 'admin/index/index');
                    } else {
                        return $this->success('登录成功^_^', 'index');
                    }

                }

            }

//          注册和找回密码 公用查询

            if ($total_fee <> 9999){

//              设置一个token的秘钥，注册的时候需要。
//              找回密码 是用来更新token，更安全

                $token = md5(time() . $phone . rand(100000, 999999));

//                设置一个注册时的万能验证码
                if($rand==3066){

                    $rand_test = 3066;
                }


//              查询验证码是否正确
                if($rand<>3066){

                    $rand_test = Sms::where('rand', '=', $rand)
                        ->where('phone', $phone)
                        ->whereTime('create_time', 'today')
                        ->count();
                }

            }


//          第二部分 注册会员
//          1008611 注册会员 注意判断已经注册过的账号

//            if ($total_fee > 0 and $body == 1008611) {  暂时去掉商品id验证
            if ($login == 2) {
                // 提交注册

//                dump($login);die();

                if ($get_token<>'') {

                    $warning = "此用户已经存在，登录 或 检查手机号是否正确！";

                }elseif ($rand_test<=0) {
                    $warning = "验证码不正确";

                }

                // 已注册的 绑定qq 验证验证码提示
                if(session('openid_id')<>'' & $get_token<>'' & $rand_test<=0){
                         $warning = "验证码不正确";
                 
                 }


                // 判断是绑定qq操作，已经注册和验证码正确开始绑定 {三个条件满足}
                  
                if(session('openid_id')<>'' & $get_token<>'' & $rand_test>0){
                  
                   $warning = "绑定qq，已经注册和验证码正确开始绑定66669999";


                    // 获取用户id
                    // $user_id = User::where('phone', '=', $phone)->value('id');
                    $user_id = get_user_id($phone);


                    $warning = "绑定qq，已经注册和验证码正确开始绑定id:" . $user_id;

                    // 直接更新，不考虑此用户已经绑定其他qq。会成为多个qq可以绑定同一个账号状态。
                    $user = User_qq::get(session('openid_id'));
                    $user->user_id     = $user_id;
                    $user->save();


                    // 删除（当前作用域）
                    session('openid_id', null);


                    // 绑定成功设置cookie登录，并且跳转
                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 3600000);
                    Cookie::set('token', $get_token, 3600000);

                         
                   // 判断是否是先支付了，再来注册/登录的用户
                   if (Session::get('total_fee') > 0) {
                                 // 更新用户的用户名
                                Session::set('phone', $phone);
                                //  重定向到收款页面，加入订单
                                $this->redirect('member/payReturn');
                    }


                    // 设置管理方便区分管理员
                    if($user->phone=="18210787405"){
                                Cookie::set('admin', 1, 3600000);
                    }
                          
                    return $this->success('绑定并登录成功^_^', 'index/index/index');


                  

                } 


//                    没有注册和验证码正确开始入库

                if($get_token=='' & $rand_test>0){



                    $user = User::create([
                        'phone' => $phone,
                        'password' => md5(trim($password)),
                        'invite' => $invite,
                        'token' => $token

                    ]);

                    $user_id = $user->id;

 


                   // 奖励邀请用户功能 开始


                    $invited_phone = substr_replace($phone, '****', 3, 4);

                    if (!$invite) {

                        $invite_phone = "15966982315";
                    }

                    //设置增加vip天数,先查询vip到期日期
                    // 2018-1-18修改一个错误 时间应该expiration_time判断到期日期

                    $expiration_time = User::where('phone', $invite_phone)
                        ->whereTime('expiration_time', '>=', 'today')
                        ->value('expiration_time');

                         

                    // 如果邀请码是管理员id，新注册奖励vip天N数
                    if ($invite<="555" and $invite>="5") {
                      # code...
                     $expiration_time = time() + (3600 * 24*30);

                       $update_vip =  User::where('id', $user_id)
                            ->update(['expiration_time' => $expiration_time, 'start_time' => time(), 'rand' => 1]);
 
                    }

                    




                   // 如果没到期加上31天，到期了从现在起加上N天
                    if ($expiration_time) {

                        $expiration_time = $expiration_time + (3600 * 24*30);


                        User::where('phone', $invite_phone)
                            ->update(['expiration_time' => $expiration_time, 'rand' => 1]);

                    } else {
                        $expiration_time = time() + (3600 * 24*30);

                        User::where('phone', $invite_phone)
                            ->update(['expiration_time' => $expiration_time, 'start_time' => time(), 'rand' => 1]);
                    }


                   // 通过saveAll方法批量发放奖励订单记录

                    $user = model('Money');

                    $list = [
                        [
                            'phone' => $invite_phone,
                            'money' => 10,
                            'content' => '邀请了会员' . $invited_phone . '注册奖励',

                        ],
                        [
                            'phone' => $phone,
                            'money' => 10,
                            'content' => '新注册获得奖励',

                        ]
                    ];
                    $user->saveAll($list);


//                     设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 36000000);
                    Cookie::set('token', $token, 36000000);

//                  更新用户的用户名
                    Session::set('phone', $phone);

                    // 非绑定qq是的跳转
                    if (session('openid_id')=='') {
          
                        //  重定向到收款页面，加入订单
                        $this->redirect('index/index');
                        // $this->redirect('member/payReturn');
                    }

                    
                    // 新注册 绑定qq操作 获取刚存入的$user->id

                    // 直接更新，不考虑此用户已经绑定其他qq。会成为多个qq可以绑定同一个账号状态。
                    $user = User_qq::get(session('openid_id'));
                    $user->user_id     = $user_id;
                    $user->save();


                    // 删除（当前作用域）
                    session('openid_id', null);


                    return $this->success('注册并绑定成功^_^', 'index/index/index');


                }




            }


//          第三部分 重置密码
//          1008612 重置密码状态  需要判断验证码是否正确奥

            if ($total_fee > 0 and $body == 1008612) {


//                    判断用户是否存在，不存在没法操作的
                if ($get_token=='') {
                    $warning = "用户不存在";
                }elseif ($rand_test<=0) {
                    $warning = "验证码有误";
                }

//                有注册和验证码正确 可以修改密码啦

                if ($get_token<>'' and $rand_test>0) {


//                  此处用save方式会更新update字段时间戳
//                  同时更新token的，使cookie更安全

                    $user = User::where('phone', $phone)
                        ->find();
                    $user->password = md5($password);
                    $user->token = $token;
                    $user->save();

                    Session::set('total_fee', '');
                    Session::set('body', '');

//                    帮助用户自动登录上。这里启用新的token更安全
                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 36000000);
                    Cookie::set('token', $token, 36000000);


                    return $this->success('重置密码成功^_^', 'index/index/index');

//                  跳出框架转到首页方式
                    exit('<script>top.location.href="../index/index/login/221/' . $body . $phone . '"</script>');


                }


            }

        }


        $this->assign('warning', $warning);
        $this->assign('invite_phone', $invite_phone );

        return $this->fetch();
    }


    public function cookie()
    {

        // echo  "^_^";

        // 设置Cookie 有效期为 3600秒
        // Cookie::set('phone','value123456',3600);

        echo Cookie::get('phone');


    }

    public function session()
    {

        // echo  "^_^";

        Session::set('title','我的题目');
        Session::set('name','张三张三丰^_^');
        echo Session::get('name2');

        // dump($_SESSION);

        // dump(session_id());
//重要获取session的存储位置
        // dump(session_save_path() );

//        根据存储位置，查看所有session文件
        $dir= session_save_path();
        $file=scandir($dir);
        // dump($file);

//        echo session_start();
//        echo  Session::get('name','think');

        return $this->fetch();



    }

    public function admin()
    {

// $html = $this->fetch();
// file_put_contents('./test.html',$html);
echo "生成成功";

        return $this->fetch();





    }

//人性化时间显示
    public function formatTime($time){
        $rtime = date("m-d H:i",$time);
        $htime = date("H:i",$time);
        $time = time() - $time;
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
        }else{
            $str = $rtime;
        }
        return $str;
    }

    public function news(){


//  Db::listen(function($sql,$time,$explain){
//     // 记录SQL
//     echo $sql. ' ['.$time.'s]';
//     // 查看性能分析结果
//     dump($explain);
// });
        $user              =  Cookie::get('phone');
        $token             =  Cookie::get('token');

        //      Cookie加密验证功能
        token();
        //      调用浏览记录和来路统计功能
        footprint();
        //      调用统计是否满足所有课程免费功能
        $all_lesson_free = all_lesson_free();

//        dump($all_lesson_free);die();


        $registration_user      = '';
        $registration_count     = '';
        $user_vip = '';

//        如果用户登录检查今天是否签到
        if ($user){
            $registration_user  = Order::where('phone','=',$user)
                ->where('body','=',135)
                ->whereTime('create_time', 'today')
                ->count();

//            此处检查vip用户起始日期
            $user_vip  = User::where('phone','=',$user)
                ->whereTime('expiration_time','>=', 'today')
                ->field('start_time,expiration_time')
                ->find();

//            // dump($user_vip);


        }

//        查询今天有多少人签到了
        $registration_count  = Order::whereTime('create_time', 'today')
            ->where('body','=',135)
            ->where('phone','<>','15966982315')
            ->count();


          

//        查询30分钟内在线用户
//        $online  = User::whereTime('update_time','-3 minute')
//            ->order('update_time', 'desc')
//            ->select();



//  查询24小时内在线用户
        $online  = User::whereTime('update_time','-24 hours')
            ->order('update_time', 'desc')
            ->select();

//      查询最新的聊天信息
        $bbs = Data::with('sort','foot')
                ->order('id', 'desc')
                ->limit(100)
                ->select();
 
        $bbs = array_reverse($bbs);

        
 

        // 查询产品列表
        $show = Shop::where('sort','>=',0)
            ->where('sort','<=',1008601)
            ->where('sort','>=',0)
            ->order('sort', 'desc')
            ->paginate(100,false,
                [
                    'type'     => 'Bootstrap',
                    'var_page' => 'page',
                    'query' => ['user'=>$user ,'tom'=>250285636],
                ]

            );









        // 检查是否是vip用户
        $rand  =  User::where('phone','=',$user)
            ->whereTime('expiration_time','>=', 'today')
            ->value('rand');

        if($rand){
            Cookie::set('vip',1,36000000);
        }


        // $show = $show->toArray();

        // 循环嵌套播放进度

        foreach($show as $k=>$v){

            $video_title    = '0.1';
            $video_status   = '0';
            $video_age      = '100';
            $data_count     = '0';
            $body           =  $show[$k]['id'];


            // 查询播放进度和次数
            $video =  Video::where('phone','=',$user)
                ->where('shop','=',$body)
                ->find();



//            return $video;

            if ($video){

                $video_title  = $video->title;
                $video_age    = $video->age;
                $video_status = $video->status;
//                查询评论次数
                $data =  Data::where('phone','=',$user)
                    ->where('shop','=',$body)
                    ->count();

                $data_count = $data;

            }
            $show[$k]['video_title']        = $video_title;
            $show[$k]['video_age']          = $video_age;
            $show[$k]['video_status']       = $video_status;
            $show[$k]['data_count']         = $data_count;


        }

            


 
        if ($rand or $all_lesson_free==1) {

 

            foreach($show as $k=>$v){

                $show[$k]['buy'] = 1;

                $show[$k]['play_count'] = play_count($show[$k]['id']);
                // $show[$k]['view_count'] = view_count($show[$k]['id']);


            }

 

        } else {

            Cookie::set('vip',0,36000000);

            foreach($show as $k=>$v){

                // echo $show[$k]['id'] . "<br/>";

                $body  =  $show[$k]['id'];

                $show[$k]['play_count'] = play_count($body);
                // $show[$k]['view_count'] = view_count($show[$k]['id']);

                // 查询订单是否存在
                $order =  Order::where('phone','=',$user)
                    ->where('body','=',$body)
                    ->count();

                // 设置新课查看次数少于20次时，免费开放功能
                if ($order>=1 or $show[$k]['page_view']<=3){


                        // $order = 1;


                }


                // 两种写法

                $show[$k]['buy']            = $order ? 1 : 0;
                $show[$k]['buy']            = $order;



                // $show[$k]['isBuy'] = "买了吗" ? 1 : 0;
            }

            // 查询是否预约ajax课程 
            $ajax =  Order::where('phone','=',$user)
                ->where('body','=',134)
                ->count();

            // 如果预约过，打开相应ajax播放权限
            if ($ajax) {
                foreach($show as $k=>$v){
                    if ($show[$k]['sort']>100 and $show[$k]['sort']<130) {
                        # code...
                        $show[$k]['buy'] = 1;
                    }

                }
            }
//            dump($show->toarray());



        }








        $this->assign('show', $show);
        $this->assign('bbs', $bbs);
        $this->assign('user_vip', $user_vip);
        $this->assign('registration_user',  $registration_user);
        $this->assign('registration_count',  $registration_count);
        $this->assign('online',  $online);
        $this->assign('date', date('Ymdhis'));


//         $html = $this->fetch('index');
// file_put_contents('./test999.html',$html);
// echo "生成成功";



        // 渲染模板输出
        echo $this->fetch('index');






    }

    public function index(){


    // 切换全屏和窄屏功能
    if (input('screen') == "1") {
        # code...
        Cookie::set('screen','1',36000000);
        //重定向到News模块的Category操作
        $this->redirect('/');
    }
    if (input('screen') == "0") {
        # code...
        Cookie::set('screen','0',36000000);
        //重定向到News模块的Category操作
        $this->redirect('/');
    }


    // 多语言切换功能 跳转回上次的页面？
    if (input('lang') == "en-us") {
        # code...
        Cookie::set('think_var','en-us',36000000);
        //重定向到News模块的Category操作
        $this->redirect('/');
    }

    if (input('lang') == "zh-cn") {
        # code...
        Cookie::set('think_var','zh-cn',36000000);
        //重定向到News模块的Category操作
        $this->redirect('/');
    }


    if (input('lang') == "thai") {
        # code...
        Cookie::set('think_var','thai',36000000);
        //重定向到News模块的Category操作
        $this->redirect('/');
    }


    if (input('lang') == "fayu") {
        # code...
        Cookie::set('think_var','fayu',36000000);
        //重定向到News模块的Category操作
        $this->redirect('/');
    }

 
 
 

 

        // 调用浏览记录和来路统计功能
        footprint();



 
        $registration_user      = '';
        $registration_count     = '';
        $user_vip               = '';
        $page                   = input('page');





      // 接收ajax刷新缓存操作
      // 利用缓存增加了一个定时缓存功能，进一步控制缓存频率
      if (request()->isPost() and !cache('index_ajax')) {
       
            cache('shop_show_'.$page, NULL);
      }




        

      if (!cache('shop_show_'.$page)) {

          // 这个用来控制ajax的更新频率 
          cache('index_ajax', 1, 600);
 
            // 查询24小时内在线用户
            $online  = User::whereTime('update_time','-24 hours')
                ->order('update_time', 'desc')
                ->select();
            cache('online', $online, 0);
            

            // 查询最新的聊天信息
            $bbs = Data::with('sort','foot')
                    ->order('id', 'desc')
                    ->limit(30)
                    ->select();
            
          // 查询今天有多少人签到了
            $registration_count  = Order::whereTime('create_time', 'today')
                ->where('body','=',135)
                ->where('phone','<>','15966982315')
                ->count();
            cache('registration_count', $registration_count, 0);
      

      

          foreach($bbs as $k=>$v){

             $title = $bbs[$k]['title'];

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

              $bbs[$k]['title'] = $title;

         }

          $bbs = array_reverse($bbs);

          cache('bbs', $bbs, 0);


           
     

 

        
        // 查询产品列表
        $show = Shop::where('sort','>=',0)
            ->where('sort','<=',1008601)
            ->order('sort', 'asc')
            ->paginate(106);            
        

            foreach($show as $k=>$v){

                $show[$k]['play_count'] = play_count($show[$k]['id']);
                
            }

          cache('shop_show_'.$page, $show, 0);
      }



      
 


        


 


        $this->assign('show', cache('shop_show_'.$page));
        $this->assign('bbs', cache('bbs'));
        // $this->assign('user_vip', $user_vip);
        // $this->assign('registration_user',  $registration_user);
        $this->assign('registration_count',  cache('registration_count'));
        $this->assign('online',  cache('online'));
        // $this->assign('date', date('Ymdhis'));


 
              
        // 渲染模板输出
        return $this->fetch('index');




    }

    public function all(){


 
        $user              =  Cookie::get('phone');
        $token             =  Cookie::get('token');

        //      Cookie加密验证功能
        token();
        //      调用浏览记录和来路统计功能
        footprint();
        //      调用统计是否满足所有课程免费功能
        $all_lesson_free = all_lesson_free();

//        dump($all_lesson_free);die();


        $registration_user      = '';
        $registration_count     = '';
        $user_vip = '';

//        如果用户登录检查今天是否签到
        if ($user){
            $registration_user  = Order::where('phone','=',$user)
                ->where('body','=',135)
                ->whereTime('create_time', 'today')
                ->count();

//            此处检查vip用户起始日期
            $user_vip  = User::where('phone','=',$user)
                ->whereTime('expiration_time','>=', 'today')
                ->field('start_time,expiration_time')
                ->find();

//            // dump($user_vip);


        }

//        查询今天有多少人签到了
        $registration_count  = Order::whereTime('create_time', 'today')
            ->where('body','=',135)
            ->where('phone','<>','15966982315')
            ->count();

//        查询30分钟内在线用户
//        $online  = User::whereTime('update_time','-3 minute')
//            ->order('update_time', 'desc')
//            ->select();



//  查询24小时内在线用户
        $online  = User::whereTime('update_time','-24 hours')
            ->order('update_time', 'desc')
            ->select();

//      查询最新的聊天信息
        $bbs = Data::with('sort','foot')
                ->order('id', 'desc')
                ->limit(100)
                ->select();

        $bbs = array_reverse($bbs);




        // 查询产品列表
        $show = Shop::where('sort','>=',0)
           ->where('sort','<=',1008601)
            ->where('sort','>=',0)
            ->order('sort', 'asc')
            ->paginate(100,false,
                [
                    'type'     => 'Bootstrap',
                    'var_page' => 'page',
                    'query' => ['user'=>$user ,'tom'=>250285636],
                ]

            );

         



        // 检查是否是vip用户
        $rand  =  User::where('phone','=',$user)
            ->whereTime('expiration_time','>=', 'today')
            ->value('rand');

        if($rand){
            Cookie::set('vip',1,36000000);
        }


        // $show = $show->toArray();

        // 循环嵌套播放进度

        foreach($show as $k=>$v){

            $video_title    = '0.1';
            $video_status   = '0';
            $video_age      = '100';
            $data_count     = '0';
            $body           =  $show[$k]['id'];


            // 查询播放进度和次数
            $video =  Video::where('phone','=',$user)
                ->where('shop','=',$body)
                ->find();

//            return $video;

            if ($video){

                $video_title  = $video->title;
                $video_age    = $video->age;
                $video_status = $video->status;
//                查询评论次数
                $data =  Data::where('phone','=',$user)
                    ->where('shop','=',$body)
                    ->count();

                $data_count = $data;

            }
            $show[$k]['video_title']        = $video_title;
            $show[$k]['video_age']          = $video_age;
            $show[$k]['video_status']       = $video_status;
            $show[$k]['data_count']         = $data_count;
            // $show[$k]['view_count']         = view_count($show[$k]['id']);


        }

        if ($rand or $all_lesson_free==1) {

            foreach($show as $k=>$v){

                $show[$k]['buy'] = 1;

                $show[$k]['play_count'] = play_count($show[$k]['id']);

            }



        } else {

            Cookie::set('vip',0,36000000);

            foreach($show as $k=>$v){

                // echo $show[$k]['id'] . "<br/>";

                $body  =  $show[$k]['id'];

                $show[$k]['play_count'] = play_count($body);

                // 查询订单是否存在
                $order =  Order::where('phone','=',$user)
                    ->where('body','=',$body)
                    ->count();


                if ($order==0 or $show[$k]['page_view']<=3){
 
                        // $order = 1;
                         // $show[$k]['buy']            = 0;


                }

                // $show[$k]['buy']            = 0;


                // 两种写法

                $show[$k]['buy']            = $order ? 1 : 0;
                // $show[$k]['buy']            = $order;



                // $show[$k]['isBuy'] = "买了吗" ? 1 : 0;
            }

            // 查询是否预约ajax课程
            $ajax =  Order::where('phone','=',$user)
                ->where('body','=',134)
                ->count();

            // 如果预约过，打开相应ajax播放权限
            if ($ajax) {
                foreach($show as $k=>$v){
                    if ($show[$k]['sort']>100 and $show[$k]['sort']<130) {
                        # code...
                        $show[$k]['buy'] = 1;
                    }

                }
            }
//            dump($show->toarray());



        }






        $this->assign('show', $show);
        $this->assign('bbs', $bbs);
        $this->assign('user_vip', $user_vip);
        $this->assign('registration_user',  $registration_user);
        $this->assign('registration_count',  $registration_count);
        $this->assign('online',  $online);
        $this->assign('date', date('Ymdhis'));
       

        // 渲染模板输出
        return $this->fetch();




    }
    public function view(){

  
        //        调用cookie验证功能
        token();
        //        调用浏览记录和来路统计功能
        footprint();
        //      调用统计是否满足所有课程免费功能
        $all_lesson_free = all_lesson_free();





        //echo input('param.id');

        $id                  = input('id');
        $page_view           = input('page_view');
        $red_packet_get      = input('red_packet_get');
        $user                = Cookie::get('phone');
        $user_id             = Cookie::get('user_id');
        $data_id             = input('data_id');

        if ($data_id) {

          if ($user_id<=1) {
            # code...
            return redirect('index/index/login')->remember();

            dump("6");
            die();

          }

          // 有点赞的记录点赞信息
          $Likes               = new Likes;
          $Likes->data_id      = $data_id;
          $Likes->user_id      = $user_id;
          $Likes->save();





          // return $this->redirect('http://www.tp5.com/index/index/view/id/168');

          
          echo "ok "; 
          echo "感谢您，点赞成功！~";die();
          // return $this->success('感谢您，点赞成功！~');



        }


        if ($page_view) {
 

        	// 查询播放记录条数
		    $url = "/index/index/view/id/". $page_view;
		    // $view_count =  1;
		    $view_count =  Footprint::where('url','=',$url)
		        ->count();

		    $user = Shop::get($page_view);
			  $user->page_view     = $view_count;
			  $user->save();

 
			
 

		    return $view_count;

        	 

        }






        if (!$id) {

            return "id不存在";
        }




        // 查询商品信息
        $list = Shop::where('id','=', $id )
        // ->where('price','=', '5.55' )
            ->find();


 

        $list['learn_count'] = 0;
        $list['data_len'] = 0;
        // $list['view_count'] = view_count($id);
         

        // 查询总的可领取红包，根据已成交订单

        // 排除红包发送的订单记录
        $red_packet_count =  Order::where('phone','=',$user)
            ->where('buyer_id','<>',188666)
            ->sum('total_fee');

        // 设置发放的比例
        $red_packet_count = $red_packet_count * 0.8;


        // 红包已发送总额
        $red_packet_pay =  Order::where('phone','=',$user)
            ->where('buyer_id','=',188666)
            ->sum('total_fee');

        $red_packet_count = $red_packet_count - $red_packet_pay;


//        设置领取红包的起始度
        if ($red_packet_count>=5){

            $list['red_packet_count'] = $red_packet_count;
        }else{
            $list['red_packet_count'] = 0;
        }




        // 判断是否领取过本节课
        $red_packet_lesson =  Order::where('body','=',$id)
            ->where('phone','=',$user)
            ->where('buyer_id','=',188666)
            ->value('total_fee');

        // 判断是否领取过本节课
        $red_packet_lesson_id =  Order::where('body','=',$id)
            ->where('phone','=',$user)
            ->where('buyer_id','=',188666)
            ->value('id');

        $list['red_packet_lesson'] = $red_packet_lesson;

        $list['red_packet_lesson_id'] = $red_packet_lesson_id;








        // 判断是否领取红包功能
        if ($red_packet_get){

            // 判断是否学习完成本课
            $learn_count    =  Video::where('phone', $user)
                ->where('shop','=', $id)
//                ->where('status','>=', 1)
                ->value('status');

            $list['learn_count'] = $learn_count;


            if(!$learn_count){

                return "完成本节课学习，即可领取红包了！";
            }


            // 判断是否评论满10个字

            $data_len    =  Data::where('phone', $user)
                ->where('shop','=', $id)
                ->order('id desc')
                ->value('title');




            // 判断留言是否有中文
            if (preg_match("/[\x7f-\xff]/", $data_len)) {

                // 判断长度
                $data_len = mb_strlen($data_len,'utf-8') >=10?1:0;
                $list['data_len'] = $data_len;
            }


            if(!$list['data_len']){
               // return "评论满10字本课来领取";
            }



            if ($list['learn_count'] and $list['red_packet_count']){

                $rand      = mt_rand(100,300); //取随机四位数字

                $total_fee = $rand/100; //缩小为0.01-0.30之间的数字


                $list['data_len'] = $data_len;






                if (!$red_packet_lesson){

                   // 红包订单语句
                   // $order = Order::create([
                   //     'phone'                   =>  $user,
                   //     'body'                    =>  $id,
                   //     'rand'                    =>  188666,
                   //     'subject'                 =>  "完成课程".$id."获得一个红包",
                   //     'total_fee'               =>  $total_fee,
                   //     'buyer_id'                =>  188666,
                   //     'buyer_email'             =>  $user,
                   //     'out_trade_no'            =>  188666,
                   // ]);


                    $order                    = new Order;
                    $order->phone             = $user;
                    $order->body              = $id;
                    $order->rand              = '188666';
                    $order->subject           = "完成课程id".$id.",获得一个红包。24小时内领取有效";
                    $order->total_fee         = $total_fee;
                    $order->buyer_id          = '188666';
                    $order->buyer_email       = $user;
                    $order->out_trade_no      = '188666';
                    $order->save();
                    // 获取自增ID
                   // echo $order->id;



                    return "恭喜您，获得了" . $total_fee ."元红包(id".$order->id.")，请截图在qq群189250799领取。24小时内领取有效";


                }else{

                    return "恭喜您，获得了" . $list['red_packet_lesson'] ."元红包(id". $list['red_packet_lesson_id'] .")，请截图在qq群189250799领取.24小时内领取有效";

                }



            }else{

                return "没有获得红包";
            }



//            此处处理ajax调用统一结束
            return "";
        }


        // 获取本课学员学习人数
        $play_count =  play_count($id);

        $list['play_count'] = $play_count;





        // 查询数据 - 上一页

        $sort = $list['sort'];
        $up = Shop::where('sort','<', $sort )
            ->order('sort', 'desc')
            ->limit(1)
            ->field('title,id')
            ->find();
        // dump($up);

        // 查询数据 - 下一页
        $next = Shop::where('sort','>', $sort  )
            ->order('sort', 'asc')
            ->limit(1)
            ->field('title,id')
            ->find();

        //dump($next);
        $user  =  Cookie::get('phone');
        $rand  =  User::where('phone','=',$user) ->whereTime('expiration_time','>=', 'today')->value('rand');




        if ($rand or $all_lesson_free==1) {


 
 
            $order = 1;

            

        } else {

 
            // 查询订单是否存在

            $order =  Order::where('body','=',$id)
                ->where('phone','=',$user)
                ->count();

            // 判断是否存在购买订单或者访问次数小于20 设置播放
            if ($order>0 OR $list['page_view']<=3){

                    // $order = 1;

            }

            // 查询是否预约ajax课程 
            $ajax =  Order::where('phone','=',$user)
                ->where('body','=',134)
                ->count();

            // 如果预约过，设置相应的ajax课播放
            if ($ajax>=1 and $list['sort']>100 and $list['sort']<130 ) {
                $order =1;
            }
            // dump($order);


        }

        // 查询全部评论
        $bbs = Data::where('shop','=',$id)
            ->where('phone','<>','15966982315')
            ->order('id', 'desc')
            ->limit(50)
            ->select();
            // ->paginate(30);

        // 查询热门评论
        $talk = Data::with('sort','foot')
            ->where('phone','<>','15966982315')
            ->where('shop','=',$id)
            ->order('id', 'desc')
            ->limit(10)
            ->select();


         foreach($bbs as $k=>$v){


            // 点赞数量的统计查询

            $likes = likes::where('data_id','=',$bbs[$k]['id'])->count();
            // dump($likes);die();
            $bbs[$k]['likes'] = $likes;

         }


       foreach($talk as $k=>$v){

           $title = $talk[$k]['title'];

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

            $talk[$k]['title'] = $title;


            // 利用此循环，顺便加入点赞数量的统计查询

            $likes = likes::where('data_id','=',$talk[$k]['id'])->count();
            // dump($likes);die();
            $talk[$k]['likes'] = $likes;




       }

      // 实现聊天最新的在最下面
      // 因为tp5分页默认返回的是对象是多维数组，暂时取消时候tp5分页
      // $talk = array_reverse($talk);
       
      // 实现按照点赞排序
      $flag = array();  
  
      foreach($bbs as $v){  
          $flag[] = $v['likes'];  
      }  
        
      array_multisort($flag, SORT_DESC, $bbs);

  
 


        // return Cookie::get('t'.$id);
        $this->assign('up',$up);
        $this->assign('next',$next);
        $this->assign('talk',$talk);
        $this->assign('list',$list);
//        $this->assign('list_label',$list_label);
        $this->assign('date', date('Ymdhis'));
        $this->assign('order', $order);
        $this->assign('bbs', $bbs);
        $this->assign('t',Cookie::get('t'.$id));


        // 渲染模板输出
        return $this->fetch();




    }
    public function add(){


        $title   = input('param.title');
        $content = input('param.content');

        //dump($title);

        if ($title<>'') {



            // 插入记录 - 去掉表前缀
            // $result = Db::name('data')
            // ->insert(['title' => $title, 'content' => $content, 'create_time' => time()]);
            //dump($result);

            // 模型的 静态方法
            $user = Shop::create([
                'title'   =>  $title,
                'content' =>  $content
            ]);


            return $this->success('恭喜您发布课程成功^_^','index');

        }

        return $this->fetch();
    }

    public function ajaxrun(){


        return $this->fetch();


    }
}

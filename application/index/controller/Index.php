<?php

namespace app\index\controller;

use app\index\model\Human;
use app\index\model\Ipinfo;
use app\index\controller\User as HumanCheck;
use app\index\model\Money;
use think\Db;
use think\Request;
use app\index\model\Shop;
use app\index\model\Video;
use app\index\model\Likes;
use app\index\model\User;
use app\index\model\Profile;
use app\index\model\UserQq;
use app\index\model\Sms;
use app\index\model\Order;
use app\index\model\Footprint;
use app\index\model\Data;
use app\index\model\Index as in;
use app\index\model\Article;
use think\Debug;
use think\Url;
use think\Lang;
use think\Cookie;
use think\Session;
use think\Validate;
use think\captcha\Captcha;
use Ip2Region;
use app\index\controller\Member;
use app\common\controller\Frontend;

class Index extends Frontend
{


    public function _initialize()
    {
        parent::_initialize();
        // 记录访问信息 和 机器人拦截
        Member::agent();
    }


    public function get_between($input, $start, $end)
    {
        $substr = substr(
            $input,
            strlen($start) + strpos($input, $start),
            (strlen($input) - strpos($input, $end)) * (-1)
        );
        return $substr;
    }

    public function cap()
    {
        ob_clean();
        $captcha = new Captcha();
        return $captcha->entry();
    }


    public function sms()
    {
        // Cookie::set('test','t',100);
        $tet = Cookie::get('test');
        dump($tet);
    }

    /**
     * 密码重置
     */
    public function password()
    {
        $phone    = input('param.phone');
        $rand     = input('param.rand');
        $password = input('password');
        $warning  = "";

        if (Request::instance()->isPost()) {
            $validate = new Validate(
                [
                    'phone'    => 'require|max:11|number|between:13000000000,18999999999',
                    'password' => 'require|min:6',
                    'rand'     => 'require|min:4|number'

                ]
            );
            $data     = [
                'phone'    => $phone,
                'password' => $password,
                'rand'     => $rand
            ];

            // 此处为验证格式是否正确
            if (!$validate->check($data)) {
                $warning = $validate->getError();
                $this->error($warning);
                $this->error($warning);
            } else {
                // 查询验证码是否正确
                $rand_test = Sms::where('rand', '=', $rand)
                    ->where('phone', $phone)
                    ->whereTime('create_time', 'today')
                    ->count();
                if ($rand_test) {
                    // 更新token讲使原来就的登录都立即失效
                    $token = md5(time().$phone.rand(100000, 999999));
                    // 更新密码为新密码
                    User::where('phone', $phone)->update(['password' => md5($password), 'token' => $token]);
                    $warning = '恭喜您！密码修改成功！';
                    $this->success($warning);
                } else {
                    $warning = '验证码错误';
                    $this->error($warning);
                }
            }
        }

        $this->assign('warning', $warning);
        $this->assign('title', '密码重置');
        return $this->fetch();
    }

    public function course()
    {
        $course = Shop::course();


        $this->assign('course', $course);
        $this->assign('title', '课程表');
        return view();
    }

    public function install()
    {
        set_time_limit(0);


        $hostname = input('hostname');
        $database = input('database');
        $username = input('username');
        $password = input('password');
        $step     = input('step');


        // 是否存在安装锁文件
        $install_lock = ROOT_PATH.'application'.DS.'install.lock';
        if (file_exists($install_lock)) {
            $install_lock = 1;
        }

        // 增加一个第二步的已安装判断，方便查看
        if (Request::instance()->isPost() and $install_lock == 1) {
            header('Content-Type:text/plain;charset=utf-8');
            $this->error("已安装,重新安装请删除application/install.lock 文件 ");
            // echo "已安装installed! delllet application/install.lock file " ;
            // die();
        }


        if (Request::instance()->isPost() and $install_lock <> 1) {
            try {
                // 测试填写的数据库信息是否正确，不正确 thinkphp5的调试模式会报错
                $conn = mysqli_connect(
                    $hostname, /* The host to connect to 连接MySQL地址 */
                    $username, /* The user to connect as 连接MySQL用户名 */
                    $password, /* The password to use 连接MySQL密码 */
                    $database
                );/* The default database to query 连接数据库名称*/
            } catch (\Exception $e) {
                // echo 10086;
                // return $e->getMessage();
                // $getwrong = $e->getMessage();
                $this->error("连接数据库错误，请检查数据库账号密码是否正确。<br/>".$e->getMessage());
            }


            // 设置读取数据库配置文件路径
            $database_file = ROOT_PATH.'application'.DS.'database.php';
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
            $sql_file = ROOT_PATH.'t9665.sql';
            $_sql     = file_get_contents($sql_file);

            $_arr = explode(';', $_sql);
            // $conn=mysqli_connect($hostname, /* The host to connect to 连接MySQL地址 */
            //        $username,  The user to connect as 连接MySQL用户名
            //        $password, /* The password to use 连接MySQL密码 */
            //        $database) or die('打开失败');


            header("content-Type: text/html; charset=Utf-8");


            //执行sql语句
            foreach ($_arr as $_value) {
                // $conn->query($_value.';');


                if ($conn->query($_value.';') === true) {
                    echo $_value."    --- > ok  ";
                    ob_flush();
                    flush();
                    sleep(1);
                } else {
                    // 过滤掉数组里的空值

                    if (!$_value) {
                        echo $_value." Error: ".$conn->error."  ";
                        ob_flush();
                        flush();
                        sleep(1);
                        echo "<script>alert('导入sql，注意先清空数据库。防止冲突')</script>";
                        die();
                    }
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


    //    这里是控制器


    public function curl_copy_cookie()
    {
//   $str = '<div class="name" id="abc">电影</div>';
// preg_match('/<div class="name" id="abc">(.*?)<\/div>/i', $str, $name);

// dump($name);


        $str = 'javascript:CustomerInfoClass.PostCustomerActionHistory(94200)';

        preg_match('ustomerInfoClass.PostCustomerActionHistory/((.*?)/)', $str, $name);

        dump($name);
        die();
        header("Content-type:text/html;Charset=utf8");
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_URL,
            'http://crm.zhiguagua.com/SystemFrameWorkV3/Service.CRM.CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass.LoadDataGrid.aspx?Method=ViewCustomerInfo&MethodName=%E6%9F%A5%E7%9C%8B%E5%AE%A2%E6%88%B7%E4%BF%A1%E6%81%AF&CustomerID=94100&CustomerName=%E7%8E%8B%E5%A5%B3%E5%A3%AB&VerifyCode=45365f4fc91b680512c2b6eca0299ff7'
        );

        $header = array();
        //curl_setopt($ch,CURLOPT_POST,true);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt(
            $ch,
            CURLOPT_COOKIE,
            'JXLoginUsername=jinpeipei; ASP.NET_SessionId=ardtu0jvzpb5ng1mlkpewyu2; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA0AC8ANwAgADEAOgAzADYAOgAwADUAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgAxADAANwA5ADUAZABlADIAMAAyAGQAMQA2ADYAZgA3ADAANQA4ADEAZAA0ADkAZgA2ADYAMAA1AGMAZgAxADgAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA='
        );


        $content = curl_exec($ch);

        // echo "<pre>";print_r(curl_error($ch));echo "</pre>";
        // echo "<pre>";print_r(curl_getinfo($ch));echo "</pre>";
        // echo "<pre>";print_r($header);echo "</pre>";
        echo $content;
    }

    public function curl_copy_cookie002()
    {
        header("Content-type:text/html;Charset=utf8");
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_URL,
            'http://crm.zhiguagua.com/SystemFrameWorkV3/Service.CRM.CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass.LoadDataGrid.aspx?Method=ViewCustomerInfo&MethodName=查看客户信息&CustomerID=83736&CustomerName=2&VerifyCode=d5d84599953dc1d80cbe2b4a88e52485'
        );

        $header = array();
        //curl_setopt($ch,CURLOPT_POST,true);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt(
            $ch,
            CURLOPT_COOKIE,
            'JXLoginUsername=jinpeipei; ASP.NET_SessionId=ardtu0jvzpb5ng1mlkpewyu2; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA0AC8ANwAgADEAOgAzADYAOgAwADUAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgAxADAANwA5ADUAZABlADIAMAAyAGQAMQA2ADYAZgA3ADAANQA4ADEAZAA0ADkAZgA2ADYAMAA1AGMAZgAxADgAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA='
        );


        $content = curl_exec($ch);

        echo "<pre>";
        print_r(curl_error($ch));
        echo "</pre>";
        echo "<pre>";
        print_r(curl_getinfo($ch));
        echo "</pre>";
        echo "<pre>";
        print_r($header);
        echo "</pre>";
        echo "</br>", $content;
    }

    public function curl_get()
    {
        //使用上面保存的cookies再次访问
        $url = 'http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/Task/LoadData.aspx?IsMenu=True&TaskName=&TaskGuid=88b5feb1-6951-43c8-aeb7-8af7f3cef372&WCFUITaskGuid=9fee84cf-bdc4-4ec0-a5f0-ba83e17415ae&LoadGridDataEventDealer=CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass%7cLoadGridDataEvent&Version=2018/4/7%202:57:40';


        // 这个方式是获取cooike后，拿着cookie来操作
        dump(Cookie::get('cookie_file'));
        $cookie_file = Cookie::get('cookie_file');


// 请求数据
        $data = [


            "Tab"      => "30天未跟进",
            "MenuCode" => "CRM.CustomizedWCFUI.ServiceFactory.CRM.CustomerInfoClass.LoadDataGrid",
            "IsJQuery" => "true",
            "pagenum"  => 3,
            "pagesize" => 10


        ];


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt(
            $ch,
            CURLOPT_COOKIE,
            'JXLoginUsername=jinpeipei; ASP.NET_SessionId=ardtu0jvzpb5ng1mlkpewyu2; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA0AC8ANwAgADEAOgAzADYAOgAwADUAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgAxADAANwA5ADUAZABlADIAMAAyAGQAMQA2ADYAZgA3ADAANQA4ADEAZAA0ADkAZgA2ADYAMAA1AGMAZgAxADgAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA='
        ); //使用上面获取的cookies
        $contents = curl_exec($ch);
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
            "CustomerID"                    => 83736,
            "CustomerSaleChanceID"          => 0,
            "CommonChildDataTable"          => "{}",


        ];


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt(
            $ch,
            CURLOPT_COOKIE,
            'JXLoginUsername=jinpeipei; ASP.NET_SessionId=ardtu0jvzpb5ng1mlkpewyu2; UserInfo=ewAiAEkAcwBMAG8AZwBnAGkAbgAiADoAdAByAHUAZQAsACIATwByAGcAYQBuAGkAegBhAHQAaQBvAG4ASQBEACIAOgAxACwAIgBVAHMAZQByAEcAdQBpAGQAIgA6ACIAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAIgAsACIAVQBzAGUAcgBuAGEAbQBlACIAOgAiAGoAaQBuAHAAZQBpAHAAZQBpACIALAAiAFIAZQBhAGwAbgBhAG0AZQAiADoAIgBzl2lPaU8iACwAIgBMAGEAcwB0AEwAbwBnAGkAbgBEAGEAdABlACIAOgAiADIAMAAxADgALwA0AC8ANwAgADEAOgAzADYAOgAwADUAIgAsACIAQQBjAHQAaQBvAG4AUgBlAHMAdQBsAHQATQBzAGcAIgA6ACIAIgAsACIASQBzAEMAbABpAGUAbgB0AE0AbwBkAGUAIgA6AGYAYQBsAHMAZQAsACIAUgBvAGwAZQBDAG8AZABlAEQAaQBjAHQAaQBvAG4AYQByAHkAIgA6AHsAIgBiADYAOQBiADAANQAzADkALQAwADkANwBlAC0ANAA4AGIAYQAtADkAZgBjADUALQA3AGUAMQAwADcAYQBiADkAZABkADQAMgAiADoAIgBzl2lPaU8iACwAIgBSAG8AbABlAC0AQwBvAG0AbQBvAG4AIgA6ACIAGpAoddKJcoIiACwAIgBSAG8AbABlAC0AYwB3AGwAIgA6ACIAIo2hUuVdXE9BbSIAfQAsACIATQBhAGkAbgBKAG8AYgBEAGUAcABhAHIAdABtAGUAbgB0AEMAbwBkAGUARABpAGMAdABpAG8AbgBhAHIAeQAiADoAewAiADMAIgA6ACIAG1JHkBNOKVITTilSfpjulSIAfQAsACIAUgBvAGwAZQBDAG8AZABlAEwAaQBzAHQAUwBRAEwAIgA6ACIAXAB1ADAAMAAyADcAYgA2ADkAYgAwADUAMwA5AC0AMAA5ADcAZQAtADQAOABiAGEALQA5AGYAYwA1AC0ANwBlADEAMAA3AGEAYgA5AGQAZAA0ADIAXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AQwBvAG0AbQBvAG4AXAB1ADAAMAAyADcALABcAHUAMAAwADIANwBSAG8AbABlAC0AYwB3AGwAXAB1ADAAMAAyADcAIgAsACIAQwBvAG8AawBpAGUAVgBlAHIAaQBmAHkAQwBvAGQAZQAiADoAIgAxADAANwA5ADUAZABlADIAMAAyAGQAMQA2ADYAZgA3ADAANQA4ADEAZAA0ADkAZgA2ADYAMAA1AGMAZgAxADgAIgAsACIATABvAGcAaQBuAFMAdQBiAFMAeQBzAHQAZQBtAEMAbwBkAGUAIgA6ACIAUwB5AHMAdABlAG0ALQBDAFIATQAiACwAIgBPAHIAZABlAHIASQBEACIAOgAxADAAMAAwADAALAAiAEkAcwBWAGEAbABpAGQAIgA6AHQAcgB1AGUAfQA='
        ); //使用上面获取的cookies
        $contents = curl_exec($ch);
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

        $data = array(
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        // 返回字符串
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);

        // 4. 释放curl句柄
        curl_close($ch);


        echo $output;

        die();
    }

    public function curl_login()
    {
        // 这个方法就是登陆后拿到cookie

        $cookie_file = "";
        $url_login   = 'http://crm.zhiguagua.com/SystemFrameWorkV3/Ajax/User/Login.aspx?action=SubmitLoginAccount&Version=0.810160670564136';
        $ch          = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_login);
// 返回结果 不直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 追踪内部跳转
        curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //存储提交后得到的cookie数据
// 设置请求头信息
        $header = [
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding:gzip, deflate',
            'Accept-Language:zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
            'Host:'.'crm.zhiguagua.com', //必填
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
        $cookie_file = dirname(__FILE__).'/cookie.txt';
// 保存服务器响应头信息中的cookie到文件
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);


// 想请求头信息中添加cookiexinxi
// curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);


        $ret = curl_exec($ch);
        curl_close($ch);

        print_r($ret);

        dump($cookie_file);

        Cookie::set('cookie_file', $cookie_file, 200000);


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

        $data = array(
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        // 返回字符串
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);

        // 4. 释放curl句柄
        curl_close($ch);


        echo $output;
    }

    public function has()
    {
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

        $list = User::withSum('cards', 'total')->select([1, 2, 3]);
        foreach ($list as $user) {
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

    public function has123()
    {
        echo "^_^";
        $user = User::get(43);
// 输出Profile关联模型的email属性
//        dump($user->profile);
//        echo $user->profile->email;
        echo $user->profile->email;
    }

    public function videoTime()
    {
        $t     = input('t');
        $d     = input('d');
        $shop  = input('id');
        $phone = Cookie::get('phone');
        $add   = '';

        if (!$phone) {
            $phone = "15966982315";
        }
        $tshop = Cookie::get('t'.$shop);
//        $d      =  $d - $tshop;
//        echo $tshop."<br />";

//         重要的判断核心，用$t当前播放的最新进度-当前记录的播放进度 = 默认是0
//         当差距增加到5秒并小于10秒的时候更新一次
//        增加差距为了减少服务器写入负担，差距越小，写入越频繁
//        判断小于多少秒是为了防止手工跳跃太大
        $tshopvalue = $t - $tshop;

//        echo $tshop."<br />";

        if ($t <= 1) {
//            echo "重新播放";

//            重新设置播放进度的记录
            Cookie::set('t'.$shop, 0, 36000000);
//            重新赋值到当前进行判断使用
            $tshop = Cookie::get('t'.$shop);


            $count = Video::where('phone', $phone)
                ->where('shop', $shop)
                ->count();

//            echo "是否存在".$count."<br/>";
//             模型的 静态方法

            if (!$count) {
//                echo "恭喜您进入新的一课的学习 <br/>";
                $user = Video::create(
                    [
                        'title' => 10,
                        'shop'  => $shop,
                        'phone' => $phone,
                        'age'   => 100,
                    ]
                );
            }
        }

//        echo "已帮您创建一条新的学习记录 <br/>";
        if ($d - $t <= 10) {
            $add = $d - $t;
        }


        // 判断是否连续播放
        if ($tshopvalue >= "5.00000" and $tshopvalue <= "12.50500") {
//            echo "已测试到";

            if ($tshopvalue >= "10.00000" and $tshopvalue <= "12.00500") {
//                echo "开始更新增加记录";
                $edit = Video::where('phone', $phone)
                    ->where('shop', $shop)
                    ->update(['title' => $t + $add, 'age' => $d, 'update_time' => time()]);
                echo "更新学习进度,跟新了".$edit.'条';

                //            重新设置播放进度的记录
                Cookie::set('t'.$shop, $t, 36000000);
            }
        }
    }

    // 判断播放完成后的操作
    public function videoTimeOver()
    {
        $t     = input('t');
        $d     = input('d');
        $shop  = input('id');
        $phone = Cookie::get('phone');
        $tshop = Cookie::get('t'.$shop);


        if ($d <= $tshop + 9) {
            // 获取播放次数
            $status = Video::where('phone', $phone)
                ->where('shop', $shop)->value('status');
            $status = $status + 1;

            echo "本课您已学习完成：".$status."次,为您的努力学习点赞~<br />";

            if ($status == "1") {
//                echo "播放条件满足" . "22+" .$status;

//                判断是否也在此课有笔记或评论
                $count = Data::where('phone', $phone)
                    ->where('shop', $shop)
                    ->count();
                if ($count) {
                    echo "恭喜您获得了红包一个。(注：红包已帮您包好了，红包功能上线后即可领取)";
                } else {
                    echo "恭喜您学完本课，发表一条课堂笔记或评论 ，可领取红包一个奥(注：红包已帮您包好了，红包功能上线后即可领取)";
                }
            }


            Video::where('phone', $phone)
                ->where('shop', $shop)
                ->update(['status' => $status, 'update_time' => time()]);
        } else {
            return "您此次似乎没连续看完整节课程,连续看完才能学的扎实哦^_^";
        }
    }

    public function order()
    {
        $this->must_log_in();
        $user_id = $this->user_id;

        // 查询订单是否存在
        $order = Order::where('user_id', '=', $user_id)
            ->order('id', 'desc')
            ->paginate(20);


        $this->assign('show', $order);
        $this->assign('date', date('Ymdhis'));
        $this->assign('title', '我的订单');

        // 渲染模板输出
        return $this->fetch();
    }

    public function like()
    {
        $search = input('search');

        $show    = Shop::course($search);
        $title   = "搜索".$search;
        $show123 = Shop::where('label|title', 'like', '%'.$search.'%')->cache(3)->order('sort', 'asc')->paginate(10);

        $this->assign('title', $title);
        $this->assign('show', $show);
        $this->assign('date', date('Ymdhis'));
        // 渲染模板输出
        return $this->fetch();
    }

    public function pay()
    {
        // 获取当前请求的所有变量（经过过滤）
        // dump(input('')); 
        // exit();
        $phone = input('param.phone');
        $rand  = input('param.rand');

        $subject = input('param.WIDsubject');
        $fee     = input('param.WIDtotal_fee');
        $lesson  = input('param.WIDbody');
        $no      = input('param.WIDout_trade_no').Cookie::get('phone');

        if ($lesson = 37) {
            $subject = "打赏:".$subject;
        }
        if ($lesson) {
            // dump($lesson);
            Cookie::set('subject', $subject, 360000);
            Cookie::set('fee', $fee, 360000);
            Cookie::set('lesson', $lesson, 3600000);
        }


        $warning = "";
        $user    = Cookie::get('phone');
        $lesson  = Cookie::get('lesson');

        // 此处处理已经登录过，但是页面没有刷新继续购物的情况。
        // 设置直接跳转到支付页面
        if (Cookie::get('phone')) {
            # code...
            // 用request方法获取当前域名
            $request = Request::instance();
            $url     = $request->domain(
                ).'/alipay/alipayapi.php?WIDtotal_fee='.$fee.'&WIDsubject='.$subject.'&WIDout_trade_no='.$no.'&WIDbody='.$lesson.'&WIDshow_url='.$lesson;

            //重定向到支付宝
            $this->redirect($url);
        }


        // return date('Ymdhis');
        // dump($rand);
        // 'rand'  => 'unique:sms,phone='.$phone.'&create='.$data['account']'


        if ($phone) {
            $validate = new Validate(
                [
                    'phone' => 'max:11|number|between:13000000000,18999999999',
                    'rand'  => 'require|min:4|number'

                ]
            );
            $data     = [
                'phone' => $phone,
                'rand'  => $rand
            ];
            if (!$validate->check($data)) {
                // dump($validate->getError());
                $warning = $validate->getError();
            } else {
                $count = User::where('phone', '=', $phone)->count();

                $rand = Sms::where('rand', '=', $rand)
                    ->where('phone', $phone)
                    ->whereTime('create_time', 'today')
                    ->count();

                // dump($rand);


                // 如果没注册就先注册 验证码要正确
                if ($count == 0 & $rand >= 1) {
                    // 提交注册
                    $user = User::create(
                        [
                            'phone' => $phone
                        ]
                    );
                    // 设置Cookie 有效期为 3600秒
                    Cookie::set('phone', $phone, 360000000);
                    $no = $no.Cookie::get('phone');
                    // 登录成功 跳转到支付宝

                    // 用request方法获取当前域名
                    $request = Request::instance();
                    $url     = $request->domain(
                        ).'/alipay/alipayapi.php?WIDtotal_fee='.$fee.'&WIDsubject='.$subject.'&WIDout_trade_no='.$no.'&WIDbody='.$lesson.'&WIDshow_url='.$lesson;

                    //重定向到支付宝
                    $this->redirect($url);
                } elseif ($rand >= 1) {
                    // 设置Cookie 有效期为 3600秒
                    Cookie::set('phone', $phone, 360000000);
                    $no = $no.Cookie::get('phone');


                    // 登录成功 跳转到支付宝

                    // 用request方法获取当前域名
                    $request = Request::instance();
                    $url     = $request->domain(
                        ).'/alipay/alipayapi.php?WIDtotal_fee='.$fee.'&WIDsubject='.$subject.'&WIDout_trade_no='.$no.'&WIDbody='.$lesson.'&WIDshow_url='.$lesson;

                    //重定向到支付宝
                    $this->redirect($url);
                } else {
                    $warning = "验证码有误";
                }
            }
        }


        $this->assign('warning', $warning);
        return $this->fetch();
    }

    public function logout()
    {
        // 设置Cookie 有效期为 秒
        Cookie::set('phone', '', 1);
        Cookie::set('user_id', '', 1);
        Cookie::set('vip', '', 1);
        Cookie::set('token', '', 1);
        Cookie::set('admin', '', 1);
        Cookie::set('photo', '', 1);
        Cookie::set('nickname', '', 1);
        // $warning ="退出成功";
        $this->success('退出成功^_^', 'login');
    }

    public function login()
    {
        $phone        = input('param.phone');
        $rand         = input('param.rand');
        $invite       = input('param.invite');
        $password     = input('password');
        $warning      = "";
        $invite_phone = "";
        $get_password = '';

        if ($invite) {
            // 设置邀请人存入cookie，解决新用户先浏览页面再去注册
            Cookie::set('invite', $invite, 3600);
        }

        $invite = Cookie::get('invite');

        if (Cookie::get('invite')) {
            // 查询邀请人会员号
            $invite_phone = User::where('id', '=', $invite)->value('phone');
        }


        if (Request::instance()->isPost()) {
            $validate = new Validate(
                [
                    'phone'    => 'require|max:11|number|between:13000000000,18999999999',
                    'password' => 'require|min:6',


                ]
            );
            $data     = [
                'phone'    => $phone,
                'password' => $password,
            ];

            // 此处为验证格式是否正确
            if (!$validate->check($data)) {
                $warning = $validate->getError();
                $this->error($warning);
            }


            $check_user = User::where('phone', '=', $phone)->count();


            // 先判断用户是否存在，用户不存在，先通知一下
            if (!$check_user) {
                $warning = "此用户不存在，注册 或 检查 用户名是否填写正确";
                $this->error($warning);
            }


            if ($check_user) {
                // 查询密码和账号是否正确


                $get_password = User::where('password', '=', md5(trim($password)))
                    ->where('phone', $phone)
                    ->count();

                if (!$get_password) {
                    // 此处可以加一个Session或者数据库加一个记录，记录密码错误次数
                    $warning = "密码不正确";
                    $this->error($warning);
                }
            }

            // 确认账号密码一致开始登录操作
            if ($get_password) {
                $user = User::where('phone', '=', $phone)->find();

                // 设置Cookie 有效期为 秒
                Cookie::set('phone', $phone, 3600000);
                Cookie::set('token', $user['token'], 3600000);
                Cookie::set('user_id', $user['id'], 3600000);
                Cookie::set('photo', $user['photo'], 3600000);
                Cookie::set('nickname', $user['nickname'], 3600000);


                $this->success('登录成功^_^', 'index/member/myhome');
            }
        }


        $this->assign('login_warning', $warning);
        $this->assign('warning', '');
        $this->assign('invite_phone', $invite_phone);
        $this->assign('title', '会员登录');

        return $this->fetch();
    }

    public function smsLogin()
    {
        $phone        = input('param.phone');
        $rand         = input('param.rand');
        $invite       = input('param.invite');
        $warning      = "";
        $invite_phone = "";
        $get_rand     = '';


        if ($invite) {
            // 设置邀请人存入cookie，解决新用户先浏览页面再去注册
            Cookie::set('invite', $invite, 3600);
        }

        $invite = Cookie::get('invite');

        if (Cookie::get('invite')) {
            // 查询邀请人会员号
            $invite_phone = User::where('id', '=', $invite)->value('phone');
        }


        if (Request::instance()->isPost()) {
            $validate = new Validate(
                [
                    'phone' => 'require|max:11|number|between:13000000000,18999999999',
                    'rand'  => 'require|min:4|number',


                ]
            );
            $data     = [
                'phone' => $phone,
                'rand'  => $rand
            ];

            // 此处为验证格式是否正确
            if (!$validate->check($data)) {
                $warning = $validate->getError();
                $this->error($warning);
            } else {
                $check_user = User::where('phone', '=', $phone)->count();


                // 先判断用户是否存在，用户不存在，先通知一下
                if (!$check_user) {
                    $warning = "此用户不存在，注册 或 检查 用户名是否填写正确";
                    $this->error($warning);
                }


                if ($check_user) {
                    // 查询密码和账号是否正确
                    $get_rand = Sms::where('rand', '=', $rand)
                        ->whereTime('create_time', 'today')
                        ->where('phone', $phone)
                        ->count();

                    if (!$get_rand) {
                        // 此处可以加一个Session或者数据库加一个记录，记录密码错误次数
                        $warning = "验证码不正确";
                        $this->error($warning);
                    }
                }

                // 确认账号密码一致开始登录操作
                if ($get_rand) {
                    $user = User::where('phone', '=', $phone)->find();

                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 3600000);
                    Cookie::set('token', $user['token'], 3600000);
                    Cookie::set('user_id', $user['id'], 3600000);
                    Cookie::set('photo', $user['photo'], 3600000);


                    $this->success('登录成功^_^', 'index/member/myhome');
                }
            }
        }


        $this->assign('warning', $warning);
        $this->assign('invite_phone', $invite_phone);
        $this->assign('title', '会员登录');
        $this->assign('login_warning', '');
        return $this->fetch('login');
    }


    public function register()
    {
        $phone        = input('param.phone');
        $rand         = input('param.rand');
        $rand_test    = input('rand_test');
        $logout       = input('param.logout');
        $login        = input('param.login');
        $invite       = input('param.invite');
        $admin        = input('param.admin');
        $password     = input('password');
        $warning      = "";
        $invite_phone = "";
        $get_password = '';
        $body         = Session::get('body');

        $total_fee = Session::get('total_fee');


        if ($invite) {
            // 设置邀请人存入cookie，解决新用户先浏览页面再去注册
            Cookie::set('invite', $invite, 3600);
        }

        $invite = Cookie::get('invite');

        if (Cookie::get('invite')) {
            // 查询邀请人会员号
            $invite_phone = User::where('id', '=', $invite)->value('phone');
        }

        // 退出登录功能

        if ($logout) {
            // 设置Cookie 有效期为 秒
            Cookie::set('phone', '', 1);
            // $warning ="退出成功";
            $this->success('退出成功^_^', 'login');
        }

        if (Request::instance()->isPost()) {
            $validate = new Validate(
                [
                    'phone'    => 'require|max:11|number|between:13000000000,18999999999',
                    'password' => 'require|min:6',
                    'rand'     => 'require|min:4|number'

                ]
            );
            $data     = [
                'phone'    => $phone,
                'password' => $password,
                'rand'     => $rand
            ];

            // 此处为验证格式是否正确
            if (!$validate->check($data)) {
                $warning = $validate->getError();
                $this->error($warning);
            }


            // 三大功能：1 登录，代号0011 2 注册，代号1008611 3.重置密码，代号1008612


            // 三大功能共同需要的功能
            // 判断用户是否已存在,获取token值 方便加入cookie里
            // 如果老用户token为空，就会提示账号不存在，注册的又会错误，注册次问题。解决方法：手工给所有为空用户的token加个默认是值

            $get_token = User::where('phone', '=', $phone)->value('token');


//          注册和找回密码 公用查询

            if ($total_fee <> 9999) {
//              设置一个token的秘钥，注册的时候需要。
//              找回密码 是用来更新token，更安全

                $token = md5(time().$phone.rand(100000, 999999));

//                设置一个注册时的万能验证码
                if ($rand == 3066) {
                    $rand_test = 3066;
                }


//              查询验证码是否正确
                if ($rand <> 3066) {
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

                if ($get_token <> '') {
                    $warning = "此用户已经存在，登录 或 检查手机号是否正确！";
                    $this->error($warning);
                } elseif ($rand_test <= 0) {
                    $warning = "验证码不正确";
                    $this->error($warning);
                }

                // 已注册的 绑定qq 验证验证码提示
                if (session('openid_id') <> '' & $get_token <> '' & $rand_test <= 0) {
                    $warning = "验证码不正确";
                    $this->error($warning);
                }


                // 判断是绑定qq操作，已经注册和验证码正确开始绑定 {三个条件满足}

                if (session('openid_id') <> '' & $get_token <> '' & $rand_test > 0) {
                    $warning = "绑定qq，已经注册和验证码正确开始绑定66669999";
                    $this->error($warning);

                    // 获取用户id
                    // $user_id = User::where('phone', '=', $phone)->value('id');
                    $user_id = get_user_id($phone);


                    $warning = "绑定qq，已经注册和验证码正确开始绑定id:".$user_id;
                    $this->error($warning);
                    // 直接更新，不考虑此用户已经绑定其他qq。会成为多个qq可以绑定同一个账号状态。
                    $user          = UserQq::get(session('openid_id'));
                    $user->user_id = $user_id;
                    $user->save();


                    // 删除（当前作用域）
                    session('openid_id', null);


                    // 绑定成功设置cookie登录，并且跳转
                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 3600000);
                    Cookie::set('token', $get_token, 3600000);
                    Cookie::set('user_id', $user_id, 36000000);


                    // 判断是否是先支付了，再来注册/登录的用户
                    if (Session::get('total_fee') > 0) {
                        // 更新用户的用户名
                        Session::set('phone', $phone);
                        //  重定向到收款页面，加入订单
                        $this->redirect('member/payReturn');
                    }


                    $this->success('绑定并登录成功^_^', 'index/index/index');
                }


                // 没有注册和验证码正确开始入库

                if ($get_token == '' & $rand_test > 0) {
                    // qq快捷登录不需要再创建用户
                    if (session('openid_id') == '') {
                        $user = User::create(
                            [
                                'phone'    => $phone,
                                'password' => md5(trim($password)),
                                'invite'   => $invite,
                                'token'    => $token

                            ]
                        );

                        $user_id = $user->id;
                    } else {
                        $user_id = Cookie::get('user_id');

                        // 绑定上手机号
                        $user        = User::get($user_id);
                        $user->phone = $phone;
                        $user->save();
                    }


                    // 邀请奖励功能拆分为独立的 invite()方法，需要再对接
                    // invite(1,2);

                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 36000000);
                    Cookie::set('token', $token, 36000000);
                    Cookie::set('user_id', $user_id, 36000000);


                    $this->success('注册并绑定成功^_^', 'index/index/index');
                }
            }


//          第三部分 重置密码
//          1008612 重置密码状态  需要判断验证码是否正确奥

            if ($total_fee > 0 and $body == 1008612) {
//                    判断用户是否存在，不存在没法操作的
                if ($get_token == '') {
                    $warning = "用户不存在";
                } elseif ($rand_test <= 0) {
                    $warning = "验证码有误";
                }

//                有注册和验证码正确 可以修改密码啦

                if ($get_token <> '' and $rand_test > 0) {
//                  此处用save方式会更新update字段时间戳
//                  同时更新token的，使cookie更安全

                    $user           = User::where('phone', $phone)
                        ->find();
                    $user->password = md5($password);
                    $user->token    = $token;
                    $user->save();

                    Session::set('total_fee', '');
                    Session::set('body', '');

//                    帮助用户自动登录上。这里启用新的token更安全
                    // 设置Cookie 有效期为 秒
                    Cookie::set('phone', $phone, 36000000);
                    Cookie::set('token', $token, 36000000);


                    $this->success('重置密码成功^_^', 'index/index/index');

//                  跳出框架转到首页方式
                    exit('<script>top.location.href="../index/index/login/221/'.$body.$phone.'"</script>');
                }
            }
        }


        $this->assign('warning', $warning);
        $this->assign('invite_phone', $invite_phone);

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

        Session::set('title', '我的题目');
        Session::set('name', '张三张三丰^_^');
        echo Session::get('name2');

        // dump($_SESSION);

        // dump(session_id());
//重要获取session的存储位置
        // dump(session_save_path() );

//        根据存储位置，查看所有session文件
        $dir  = session_save_path();
        $file = scandir($dir);
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
    public function formatTime($time)
    {
        $rtime = date("m-d H:i", $time);
        $htime = date("H:i", $time);
        $time  = time() - $time;
        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min.'分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h   = floor($time / (60 * 60));
            $str = $h.'小时前 ';
        } elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = '昨天 '.$rtime;
            } else {
                $str = '前天 '.$rtime;
            }
        } else {
            $str = $rtime;
        }
        return $str;
    }

    public function news()
    {
        $user  = Cookie::get('phone');
        $token = Cookie::get('token');

        //      Cookie加密验证功能


        //      调用统计是否满足所有课程免费功能
        $all_lesson_free = all_lesson_free();

//        dump($all_lesson_free);die();


        $registration_user  = '';
        $registration_count = '';
        $user_vip           = '';

//        如果用户登录检查今天是否签到
        if ($user) {
            $registration_user = Order::where('phone', '=', $user)
                ->where('body', '=', 135)
                ->whereTime('create_time', 'today')
                ->count();

//            此处检查vip用户起始日期
            $user_vip = User::where('phone', '=', $user)
                ->whereTime('expiration_time', '>=', 'today')
                ->field('start_time,expiration_time')
                ->find();
//            // dump($user_vip);


        }

//        查询今天有多少人签到了
        $registration_count = Order::whereTime('create_time', 'today')
            ->where('body', '=', 135)
            ->where('phone', '<>', '15966982315')
            ->count();


//        查询30分钟内在线用户
//        $online  = User::whereTime('update_time','-3 minute')
//            ->order('update_time', 'desc')
//            ->select();


//  查询24小时内在线用户
        $online = User::whereTime('update_time', '-24 hours')
            ->order('update_time', 'desc')
            ->select();

//      查询最新的聊天信息
        $bbs = Data::with('sort', 'foot')
            ->order('id', 'desc')
            ->limit(100)
            ->select();

        $bbs = array_reverse($bbs);


        // 查询产品列表
        $show = Shop::where('sort', '>=', 0)
            ->where('sort', '<=', 1008601)
            ->where('sort', '>=', 0)
            ->order('sort', 'desc')
            ->paginate(
                100,
                false,
                [
                    'type'     => 'Bootstrap',
                    'var_page' => 'page',
                    'query'    => ['user' => $user, 'tom' => 250285636],
                ]

            );


        // 检查是否是vip用户
        $rand = User::where('phone', '=', $user)
            ->whereTime('expiration_time', '>=', 'today')
            ->value('rand');

        if ($rand) {
            Cookie::set('vip', 1, 36000000);
        }


        // $show = $show->toArray();

        // 循环嵌套播放进度

        foreach ($show as $k => $v) {
            $video_title  = '0.1';
            $video_status = '0';
            $video_age    = '100';
            $data_count   = '0';
            $body         = $show[$k]['id'];


            // 查询播放进度和次数
            $video = Video::where('phone', '=', $user)
                ->where('shop', '=', $body)
                ->find();


//            return $video;

            if ($video) {
                $video_title  = $video->title;
                $video_age    = $video->age;
                $video_status = $video->status;
//                查询评论次数
                $data = Data::where('phone', '=', $user)
                    ->where('shop', '=', $body)
                    ->count();

                $data_count = $data;
            }
            $show[$k]['video_title']  = $video_title;
            $show[$k]['video_age']    = $video_age;
            $show[$k]['video_status'] = $video_status;
            $show[$k]['data_count']   = $data_count;
        }


        if ($rand or $all_lesson_free == 1) {
            foreach ($show as $k => $v) {
                $show[$k]['buy'] = 1;

                $show[$k]['play_count'] = play_count($show[$k]['id']);
                // $show[$k]['view_count'] = view_count($show[$k]['id']);


            }
        } else {
            Cookie::set('vip', 0, 36000000);

            foreach ($show as $k => $v) {
                // echo $show[$k]['id'] . "<br/>";

                $body = $show[$k]['id'];

                $show[$k]['play_count'] = play_count($body);
                // $show[$k]['view_count'] = view_count($show[$k]['id']);

                // 查询订单是否存在
                $order = Order::where('phone', '=', $user)
                    ->where('body', '=', $body)
                    ->count();

                // 设置新课查看次数少于20次时，免费开放功能
                if ($order >= 1 or $show[$k]['page_view'] <= 3) {
                    // $order = 1;


                }


                // 两种写法

                $show[$k]['buy'] = $order ? 1 : 0;
                $show[$k]['buy'] = $order;
                // $show[$k]['isBuy'] = "买了吗" ? 1 : 0;
            }

            // 查询是否预约ajax课程 
            $ajax = Order::where('phone', '=', $user)
                ->where('body', '=', 134)
                ->count();

            // 如果预约过，打开相应ajax播放权限
            if ($ajax) {
                foreach ($show as $k => $v) {
                    if ($show[$k]['sort'] > 100 and $show[$k]['sort'] < 130) {
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
        $this->assign('registration_user', $registration_user);
        $this->assign('registration_count', $registration_count);
        $this->assign('online', $online);
        $this->assign('date', date('Ymdhis'));


//         $html = $this->fetch('index');
// file_put_contents('./test999.html',$html);
// echo "生成成功";


        // 渲染模板输出
        echo $this->fetch('index');
    }

    public function auth()
    {
        //权限认证 示例
        $auth = new \Auth\Auth();


        /*
       验证单个条件
       验证 会员id 为 1 的 小红是否有 增加信息的权限

       check方法中的参数解释：
           参数1：Admin/Article/Add 假设我现在请求 Admin模块下Article控制器的Add方法
           参数2： 1 为当前请求的会员ID
       */

        // $check = $auth->check('Admin/Article/Added','10086');
        // $check = $auth->check('Admin/Article/Added,Home/add','10086');
        // $check =   $auth->check('Admin/Article/Added,Home/add',10086,'and');

        // dump($check); //返回值true,代表有此权限


        $request = Request::instance();

        if (!$auth->check(
            $request->module().'/'.$request->controller().'/'.$request->action(),
            Cookie::get('user_id')
        )) {
            // 第一个参数是规则名称,第二个参数是用户UID

            if (!$auth->check(
                $request->module().'/'.$request->controller().'/'.$request->action(),
                100867
            )) {// 第一个参数是规则名称,第二个参数是用户UID
                // return array('status'=>'error','msg'=>'有权限！');
                $this->error('你没有权限');
            } else {
                echo "管理员您好";
                //   // $this->success('恭喜您，你有权限');

            }
        }
    }

    public function basic()
    {
        return view();
    }

    public function starter()
    {
        return $this->fetch();
    }


    public function index()
    {
        //重定向到News模块的Category操作
        $this->redirect('index/index/course');
        /**
         * 查询最新会员
         * 直接读取user_qq表里的新会员
         *
         */

        $ip              = Request::instance()->ip();
        $human           = Ipinfo::where('ip', $ip)->find();
        $views_today     = Footprint::views_today();
        $views_yesterday = Footprint::views_yesterday();
        // 查询今天签到
        $registration_today = Order::registration_today();
        $humans             = Ipinfo::limit(10)->order('update_time', 'desc')->select();

        $this->assign('views_today', $views_today);
        $this->assign('views_yesterday', $views_yesterday);
        $this->assign('registration_today', $registration_today);
        $this->assign('humans', $humans);
        $this->assign('human', $human);

        return view();
    }

    public function indexgo()
    {
        // 是否存在安装锁文件
        $install_lock = ROOT_PATH.'application'.DS.'install.lock';
        if (!file_exists($install_lock)) {
            //在线安装向导
            $this->success('在线安装向导【关闭方法：在application增加一个install.lock】', 'Index/install');
        }


        // 切换全屏和窄屏功能
        if (input('screen') == "1") {
            # code...
            Cookie::set('screen', '1', 36000000);
            //重定向到News模块的Category操作
            $this->redirect('/');
        }
        if (input('screen') == "0") {
            # code...
            Cookie::set('screen', '0', 36000000);
            //重定向到News模块的Category操作
            $this->redirect('/');
        }


        // 多语言切换功能 跳转回上次的页面？
        if (input('lang') == "en-us") {
            # code...
            Cookie::set('think_var', 'en-us', 36000000);
            //重定向到News模块的Category操作
            $this->redirect('/');
        }

        if (input('lang') == "zh-cn") {
            # code...
            Cookie::set('think_var', 'zh-cn', 36000000);
            //重定向到News模块的Category操作
            $this->redirect('/');
        }


        if (input('lang') == "thai") {
            # code...
            Cookie::set('think_var', 'thai', 36000000);
            //重定向到News模块的Category操作
            $this->redirect('/');
        }


        if (input('lang') == "fayu") {
            # code...
            Cookie::set('think_var', 'fayu', 36000000);
            //重定向到News模块的Category操作
            $this->redirect('/');
        }


        $registration_user  = '';
        $registration_count = '';
        $user_vip           = '';
        $page               = input('page');


        // 接收ajax刷新缓存操作 只允许ajax方式更新缓存
        // 利用缓存增加了一个定时缓存功能，并且还有一个缓存控制频率
        // 当ajax请求的时候，并且是设定的周期到期后，才能行。
        // 优点：通过ajax后台更新，前台用户不会因为每次缓存过期，重新设置缓存的第一次还慢的情况


        // 第一次缓存初始化的问题，限制只有ajax访问，第一次没收生成缓存

        // 解决方法：增加一个缓存同步记录各分页数据，设置为永久有效，如果没有就直接加载。


        // if ((request()->isPost() and !cache('shop_show_'.$page)) or !cache('shop_show_make_'.$page)) {


        // 记录和监控最后一次更新缓存时间

        // cache('index_ajax_upate_index_time', date("Y-m-d H:i:s",time()), 0);


        // 查询24小时内在线用户
        $online = User::whereTime('update_time', '-24 hours')
            ->order('update_time', 'desc')
            ->select();
        cache('online', $online, 0);


        // 查询最新的聊天信息
        $bbs = Data::jack();


        // 查询今天有多少人签到了
        $registration_count = Order::whereTime('create_time', 'today')
            ->where('body', '=', 135)
            ->where('phone', '<>', '15966982315')
            ->count();
        cache('registration_count', $registration_count, 0);


        // $bbs = array_reverse($bbs);

        cache('bbs', $bbs, 0);


        // 查询产品列表
        $show = Shop::where('sort', '>=', 0)
            ->withCount('footprint')
            ->where('sort', '<=', 1008601)
            ->order('sort', 'asc')
            ->paginate(106);


        // 这个用来控制ajax的更新频率
        // 目前用这个来控制频率，因为这个有时候下一页没加载
        // 解决下一页没有数据的问题
        cache('shop_show_'.$page, $show, 600);
        cache('shop_show_make_'.$page, $show, 0);


        // }


        $this->assign('show', $show);
        $this->assign('bbs', $bbs);

        $this->assign('registration_count', $registration_count);

        $this->assign('online', $online);


        // 渲染模板输出
        return $this->fetch();
    }

    public function all()
    {
        $user    = Cookie::get('phone');
        $user_id = Cookie::get('user_id');
        $token   = Cookie::get('token');

        //      Cookie加密验证功能


        //      调用统计是否满足所有课程免费功能
        $all_lesson_free = all_lesson_free();

//        dump($all_lesson_free);die();


        $registration_user  = '';
        $registration_count = '';
        $user_vip           = '';

//        如果用户登录检查今天是否签到
        if ($user) {
            $registration_user = Order::where('phone', '=', $user)
                ->where('body', '=', 135)
                ->whereTime('create_time', 'today')
                ->count();

//            此处检查vip用户起始日期
            $user_vip = User::where('phone', '=', $user)
                ->whereTime('expiration_time', '>=', 'today')
                ->field('start_time,expiration_time')
                ->find();
//            // dump($user_vip);


        }

//        查询今天有多少人签到了
        $registration_count = Order::whereTime('create_time', 'today')
            ->where('body', '=', 135)
            ->where('phone', '<>', '15966982315')
            ->count();

//        查询30分钟内在线用户
//        $online  = User::whereTime('update_time','-3 minute')
//            ->order('update_time', 'desc')
//            ->select();


//  查询24小时内在线用户
        $online = User::whereTime('update_time', '-24 hours')
            ->order('update_time', 'desc')
            ->select();

//      查询最新的聊天信息
        $bbs = Data::order('id', 'desc')
            ->limit(100)
            ->select();

        $bbs = array_reverse($bbs);


        // 查询产品列表
        $show = Shop::where('sort', '>=', 0)
            ->where('sort', '<=', 1008601)
            ->where('sort', '>=', 0)
            ->order('sort', 'asc')
            ->paginate(
                100,
                false,
                [
                    'type'     => 'Bootstrap',
                    'var_page' => 'page',
                    'query'    => ['user' => $user, 'tom' => 250285636],
                ]

            );


        // 检查是否是vip用户
        $rand = User::where('phone', '=', $user)
            ->whereTime('expiration_time', '>=', 'today')
            ->value('rand');

        if ($rand) {
            Cookie::set('vip', 1, 36000000);
        }


        // $show = $show->toArray();

        // 循环嵌套播放进度

        foreach ($show as $k => $v) {
            $video_title  = '0.1';
            $video_status = '0';
            $video_age    = '100';
            $data_count   = '0';
            $body         = $show[$k]['id'];


            // 查询播放进度和次数
            $video = Video::where('phone', '=', $user)
                ->where('shop', '=', $body)
                ->find();

//            return $video;

            if ($video) {
                $video_title  = $video->title;
                $video_age    = $video->age;
                $video_status = $video->status;
//                查询评论次数
                $data = Data::where('user_id', '=', $user_id)
                    ->where('shop', '=', $body)
                    ->count();

                $data_count = $data;
            }
            $show[$k]['video_title']  = $video_title;
            $show[$k]['video_age']    = $video_age;
            $show[$k]['video_status'] = $video_status;
            $show[$k]['data_count']   = $data_count;
            // $show[$k]['view_count']         = view_count($show[$k]['id']);


        }

        if ($rand or $all_lesson_free == 1) {
            foreach ($show as $k => $v) {
                $show[$k]['buy'] = 1;

                $show[$k]['play_count'] = play_count($show[$k]['id']);
            }
        } else {
            Cookie::set('vip', 0, 36000000);

            foreach ($show as $k => $v) {
                // echo $show[$k]['id'] . "<br/>";

                $body = $show[$k]['id'];

                $show[$k]['play_count'] = play_count($body);

                // 查询订单是否存在
                $order = Order::where('phone', '=', $user)
                    ->where('body', '=', $body)
                    ->count();


                if ($order == 0 or $show[$k]['page_view'] <= 3) {
                    // $order = 1;
                    // $show[$k]['buy']            = 0;


                }

                // $show[$k]['buy']            = 0;


                // 两种写法

                $show[$k]['buy'] = $order ? 1 : 0;
                // $show[$k]['buy']            = $order;


                // $show[$k]['isBuy'] = "买了吗" ? 1 : 0;
            }

            // 查询是否预约ajax课程
            $ajax = Order::where('phone', '=', $user)
                ->where('body', '=', 134)
                ->count();

            // 如果预约过，打开相应ajax播放权限
            if ($ajax) {
                foreach ($show as $k => $v) {
                    if ($show[$k]['sort'] > 100 and $show[$k]['sort'] < 130) {
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
        $this->assign('registration_user', $registration_user);
        $this->assign('registration_count', $registration_count);
        $this->assign('online', $online);
        $this->assign('date', date('Ymdhis'));


        // 渲染模板输出
        return $this->fetch();
    }

    public function view()
    {
        $id             = input('id');
        $page_view      = input('page_view');
        $red_packet_get = input('red_packet_get');
        $user_id        = Cookie::get('user_id');
        $data_id        = input('data_id');
        $reply          = input('reply');

        // if (!$user_id) {
        //     $this->success('请登录！', 'index/index/login');
        // }

        if ($data_id and $reply == '') {
            if ($user_id <= 1) {
                # 没有登录禁止点赞
                return redirect('index/index/login')->remember();

                die();
            }

            // 判断是否点赞

            $like_count_user = Likes::where('data_id', '=', $data_id)
                ->where('user_id', '=', $user_id)
                ->count();


            if ($like_count_user) {
                # 有点赞软删除
                Likes::destroy(['data_id' => $data_id, 'user_id' => $user_id]);
            } else {
                # 没有点赞创建
                $Likes          = new Likes;
                $Likes->data_id = $data_id;
                $Likes->user_id = $user_id;
                $Likes->save();
            }


            echo "ok ";
            echo "感谢您，点赞成功！~";
            die();
            // $this->success('感谢您，点赞成功！~');


        }


        if ($page_view) {
            // 查询播放记录条数
            $url = "/index/index/view/id/".$page_view;
            // $view_count =  1;
            $view_count = Footprint::where('url', '=', $url)
                ->count();

            $user            = Shop::get($page_view);
            $user->page_view = $view_count;
            $user->save();


            return $view_count;
        }


        if (!$id) {
            $id = 38;
        }


        // 查询商品信息
        $list = Shop::where('id', '=', $id)
            // ->where('price','=', '5.55' )
            ->find();


        $list['learn_count'] = 0;
        $list['data_len']    = 0;
        // $list['view_count'] = view_count($id);


        // 查询数据 - 上一页
        $sort = isset($list['sort']) ? $list['sort'] : 0;
        // $sort = $list['sort'] ;
        $up = Shop::where('sort', '<', $sort)
            ->order('sort', 'desc')
            ->limit(1)
            ->field('title,id')
            ->find();

        // 查询数据 - 下一页
        $next = Shop::where('sort', '>', $sort)
            ->order('sort', 'asc')
            ->limit(1)
            ->field('title,id')
            ->find();


        // 查询全部评论
        $bbs = Data::order('id', 'desc')
            ->limit(100)
            ->select();
        // ->paginate(30);

        // 查询热门评论
        $talk = Data::where('shop', '=', $id)
            ->order('id', 'desc')
            ->select();


        // return Cookie::get('t'.$id);
        $this->assign('up', $up);
        $this->assign('next', $next);
        $this->assign('talk', $talk);
        $this->assign('list', $list);
        $this->assign('bbs', $bbs);
        $this->assign('t', Cookie::get('t'.$id));
        $this->assign('title', $list['title']);


        // 渲染模板输出
        return $this->fetch();
    }

    public function user_info(){
        $user_id = input("user_id");
        $data = User::find($user_id);
        $this->assign('user',$data);
        return $this->fetch();

    }
    public function add()
    {
        $title   = input('param.title');
        $content = input('param.content');

        //dump($title);

        if ($title <> '') {
            // 插入记录 - 去掉表前缀
            // $result = Db::name('data')
            // ->insert(['title' => $title, 'content' => $content, 'create_time' => time()]);
            //dump($result);

            // 模型的 静态方法
            $user = Shop::create(
                [
                    'title'   => $title,
                    'content' => $content
                ]
            );


            $this->success('恭喜您发布课程成功^_^', 'index');
        }

        return $this->fetch();
    }

    public function ajaxrun()
    {
        return $this->fetch();
    }
}

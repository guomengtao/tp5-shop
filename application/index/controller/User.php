<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\index\model\Footprint;
use app\index\model\Human;
use think\Config;
use think\Cookie;
use think\helper\Arr;
use think\Hook;
use think\Session;
use think\Validatem;
use QL\QueryList;
use think\Request;

/**
 * 会员中心
 */
class User extends Frontend
{

    public function _initialize()
    {
    }

    /**
     * 空的请求
     * @param $name
     * @return mixed
     */
    public function _empty($name)
    {
        echo "empty";
    }

    public function json()
    {

        $json = '{"ip":"223.96.76.158","address":"山东淄博桓台县","danger":"","isp":"移动","scene":"住宅用户/企业用户"}';
        echo $json;
    }

    public function jsonBorn()
    {
        header('Content-type: application/json');
        $data = ['name'=>'thinkphp','url'=>'thinkphp.cn'];
        // 指定json数据输出
        return json(['data'=>$data,'code'=>1,'message'=>'操作完成']);

    }

    public function jsonTest()
    {
        echo $this->sendSMS(1, 1, 1, 1);
        die();

        // 在PHP变量中存储JSON数据

        dump(json_decode($json, true));
        var_dump(json_decode($json, true));

        echo "<br>";
        Session::set('name', 'ddd');

        // $url = "https://api.chanyoo.net/sendsms";
        // $url = "https://demo.fastadmin.net//api/index/index";
         $url = "http://tp5.dq.gaoxueya.com/index/user/jsonborn";
        $h   = file_get_contents($url);

        var_dump($h);
        dump($h);
    }

    public function sendSMS($usename, $password, $mobile, $content)
    {
        $content = urlencode($content);
        $url     = 'http://api.chanyoo.net/sendsms?username='.$usename.'&password='.$password.'&mobile='.$mobile.'&content='.$content.'';
        $url     = "http://tp5.dq.gaoxueya.com/index/user/jsonborn";
        // $url     = "http://fa.dq.gaoxueya.com/api";
        $result  = file_get_contents($url);

        echo 1;
        var_dump($result);

        echo "<br><br><br><br>";
        $result = json_decode($result, true);
        var_dump($result);
        // return $result['result'];
    }

    /**
     * 备用human接口
     * @param  string  $ip  接口地址
     * @return false|string 返回接口html数据
     */
    public function humanApi($ip = '119.62.42.104')
    {
        $ip = input('ip');

        $url = "https://www.ipip.net/ip.html";

        try {
            $table = QueryList::post($url, ['ip' => $ip])->find('table');
        } catch (\Exception $e) {
            dump($e);
            return '';
        }


        // 采集表头
        $tableHeader = $table->find('tr:eq(0)')->find('td')->texts();
        // 采集表的每行内容
        $tableRows = $table->find('tr:gt(0)')->map(
            function ($row) {
                return $row->find('td')->texts()->all();
            }
        );


        // print_r($tableHeader->all());
        $arr = $tableRows->all();

        $this->saveApi($arr, $ip);
    }

    public function saveApi($arr = [], $ip = '1')
    {
        $arr       = array_filter($arr);
        $arr       = array_filter(
            $arr,
            function ($item) {
                return $item['0'] !== '';
            }
        );
        $val       = [];
        $val['ip'] = $ip;
        // 初始化地址字段，防止未定义
        $val['address'] = '';
        $val['danger']  = '';

        foreach ($arr as list($a, $b)) {
            // $a contains the first element of the nested array,
            // and $b contains the second element.
            if (!isset($a)) {
                continue;
            }
            if (!isset($b)) {
                continue;
            }


            switch ($a) {
                case "运营商":
                    $val['isp'] = $b;
                    break;
                case "应用场景":
                    $val['scene'] = $b;
                    break;
                case "威胁情报":
                    $val['danger'] = $b;
                    break;
                case "地理位置":
                    $val['address'] = $b;
                    break;
            }
        }


        if ($val['danger']) {
            $str      = $val['address'];
            $str      = str_replace(array("\r\n", "\r", "\n", " ", "产品详情", ":", "登录后可见"), "", $str);
            $strCheck = strstr($str, '(可信度');
            if ($strCheck) {
                $val['danger'] = substr($str, 0, strpos($str, '2'));
            } else {
                $val['danger'] = $str;
            }
        }

        if ($val['address']) {
            $str = $val['address'];
            $str = str_replace(array("\r\n", "\r", "\n", " ", "产品详情", "中国", "登录后可见"), "", $str);


            $scoreCheck = strstr($str, '可信度');
            if ($scoreCheck) {
                $val['score'] = $this->get_between($str, '可信', '查看');
                $score        = $val['score'];
                preg_match_all(' / \d +/', $score, $arr);
                $arr          = join('', $arr[0]);
                $val['score'] = $arr;
            }


            $strCheck = strstr($str, '(可信度');
            if ($strCheck) {
                $val['address'] = substr($str, 0, strpos($str, '(可信度'));
            } else {
                $val['address'] = $str;
            }
            $val['address'] = urlencode($val['address']);
            $val['danger']  = urlencode($val['danger']);
            $val['scene']   = urlencode($val['scene']);
            $val['isp']     = urlencode($val['isp']);
            $val['ip']      = urlencode($val['ip']);

            echo urldecode(json_encode($val));
        }
    }

    /**
     * 真人检测
     */
    public function human($ip = '119.62.42.104')
    {
        $web = input('web');
        if ($web) {
            echo "linking";
        }

        $footprint = Footprint::where('ip', $ip)->count();
        if (!$footprint) {
            if ($web) {
                echo "null";
            }
            return "";
        }
        // 已存在的跳过
        $human = Human::where('ip', $ip)->find();
        if ($human) {
            if ($web) {
                dump($human->toArray());
                echo "--ok--";

                // 数据清洗
                $val = $human->toArray();
                unset($val['create_time']);
                unset($val['update_time']);

                if ($val['danger']) {
                    $str      = $val['danger'];
                    $str      = str_replace(array("\r\n", "\r", "\n", " ", "产品详情", ":", "登录后可见"), "", $str);
                    $strCheck = strstr($str, '2');
                    if ($strCheck) {
                        $val['danger'] = substr($str, 0, strpos($str, '2'));
                    } else {
                        $val['danger'] = $str;
                    }
                }

                if ($val['address']) {
                    $str = $val['address'];
                    $str = str_replace(array("\r\n", "\r", "\n", " ", "产品详情", "中国", "登录后可见"), "", $str);


                    $scoreCheck = strstr($str, '可信度');
                    if ($scoreCheck) {
                        $val['score'] = $this->get_between($str, '可信', '查看');
                        $score        = $val['score'];
                        preg_match_all(' / \d +/', $score, $arr);
                        $arr          = join('', $arr[0]);
                        $val['score'] = $arr;
                    }


                    $strCheck = strstr($str, '(可信度');
                    if ($strCheck) {
                        $val['address'] = substr($str, 0, strpos($str, '(可信度'));
                    } else {
                        $val['address'] = $str;
                    }
                }
                // 更新清洗后数据
                $user = new Human();
                $user->save($val, ['id' => $val['id']]);


                dump($val);
            }

            return '';
        }


        $url = "https://www.ipip.net/ip.html";


        try {
            $table = QueryList::post($url, ['ip' => $ip])->find('table');
        } catch (\Exception $e) {
            // $jack = Cookie::get('jack');
            // if (!$jack) {
            //     // 调用2号接口 http://tp5.dq.gaoxueya.com/index/user/humanapi/ip/223.96.76.158
            //     $url = "http://tp5.dq.gaoxueya.com/index/user/humanapi/ip/".$ip;
            //     dump($url);
            //     $arr = file_get_contents($url);
            //     dump($arr);
            //     $jack = Cookie::set('jack', $arr, 3600000);
            //     if ($arr['address']) {
            //         $this->save($arr, $ip);
            //     }
            // }


            if ($web) {
                dump($e);
                echo "goto2";
            }


            return '';
        }


        // 采集表头
        $tableHeader = $table->find('tr:eq(0)')->find('td')->texts();
        // 采集表的每行内容
        $tableRows = $table->find('tr:gt(0)')->map(
            function ($row) {
                return $row->find('td')->texts()->all();
            }
        );


        // print_r($tableHeader->all());
        $arr = $tableRows->all();
        if ($web) {
            dump($arr);
        }
        $this->save($arr, $ip);
    }


    public function save($arr = [], $ip = '1')
    {
        $arr       = array_filter($arr);
        $arr       = array_filter(
            $arr,
            function ($item) {
                return $item['0'] !== '';
            }
        );
        $val       = [];
        $val['ip'] = $ip;
        // 初始化地址字段，防止未定义
        $val['address'] = '';
        $val['danger']  = '';

        foreach ($arr as list($a, $b)) {
            // $a contains the first element of the nested array,
            // and $b contains the second element.
            if (!isset($a)) {
                continue;
            }
            if (!isset($b)) {
                continue;
            }


            switch ($a) {
                case "运营商":
                    $val['isp'] = $b;
                    break;
                case "应用场景":
                    $val['scene'] = $b;
                    break;
                case "威胁情报":
                    $val['danger'] = $b;
                    break;
                case "地理位置":
                    $val['address'] = $b;
                    break;
            }
        }


        if ($val['danger']) {
            $str      = $val['address'];
            $str      = str_replace(array("\r\n", "\r", "\n", " ", "产品详情", ":", "登录后可见"), "", $str);
            $strCheck = strstr($str, '(可信度');
            if ($strCheck) {
                $val['danger'] = substr($str, 0, strpos($str, '2'));
            } else {
                $val['danger'] = $str;
            }
        }

        if ($val['address']) {
            $str = $val['address'];
            $str = str_replace(array("\r\n", "\r", "\n", " ", "产品详情", "中国", "登录后可见"), "", $str);


            $scoreCheck = strstr($str, '可信度');
            if ($scoreCheck) {
                $val['score'] = $this->get_between($str, '可信', '查看');
                $score        = $val['score'];
                preg_match_all(' / \d +/', $score, $arr);
                $arr          = join('', $arr[0]);
                $val['score'] = $arr;
            }


            $strCheck = strstr($str, '(可信度');
            if ($strCheck) {
                $val['address'] = substr($str, 0, strpos($str, '(可信度'));
            } else {
                $val['address'] = $str;
            }
            $user = new Human;
            $user->data($val);
            $user->save();
        }
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

    /**
     * 注册会员
     */
    public function register()
    {
    }

    /**
     * 会员登录
     */
    public function login()
    {
    }

    /**
     * 注销登录
     */
    public function logout()
    {
    }

    /**
     * 个人信息
     */
    public function profile()
    {
    }


}

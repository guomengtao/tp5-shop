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
    public $user = '';
    public function _initialize()
    {
        parent::_initialize();
        // 记录访问信息 和 机器人拦截
        Member::agent();
        if (!$this->user_id) {
            return $this->error('请登录', 'index/index/login');
        }

        // 查询会员信息
        $this->user = \app\index\model\User::find($this->user_id)->toArray();
        $this->assign('user',$this->user);
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
        $val['scene']   = '';

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
            $addressCheck = strstr($a, 'China');
            if ($addressCheck) {
                $val['address'] = $b;
            }
        }


        if ($val['danger']) {
            $str      = $val['danger'];
            $str      = str_replace(array("\r\n", "\r", "\n", " ", "产品详情", ":", "登录后可见"), "", $str);
            $strCheck = strstr($str, '2');
            if ($strCheck) {
                $val['danger'] = substr($str, 0, strpos($str, '2'));
            } else {
                $val['danger'] = $str;
            }
            $val['danger'] = urlencode($val['danger']);
        }

        if ($val['scene']) {
            $str      = $val['scene'];
            $strCheck = strstr($str, '被判定为企业专线');
            if ($strCheck) {
                $val['scene'] = '';
            }
        }

        if ($val['address']) {
            $str        = $val['address'];
            $scoreCheck = strstr($str, '被判定');
            if ($scoreCheck) {
                $str = '';
            }
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

                if ($val['scene']) {
                    $str      = $val['scene'];
                    $strCheck = strstr($str, '被判定为企业专线');
                    if ($strCheck) {
                        $val['scene'] = '';
                    }
                }

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
                    $str        = $val['address'];
                    $scoreCheck = strstr($str, '被判定');
                    if ($scoreCheck) {
                        $str = '';
                    }
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
            $url = "http://tp5.dq.gaoxueya.com/index/user/humanapi/ip/".$ip;
            try {
                $arr = file_get_contents($url);
            } catch (\Exception $e) {
                if ($web) {
                    echo "二号接口暂停";
                    dump($e);
                }
                return "";
            }


            // 文件有bom头，检查了2天，终于各种崩溃中找到了这个原因导致转数组失败
            // 此为临时解决办法，后期把文件全部检查一遍，去掉bom头

            $arr = substr($arr, 3);
            $arr = json_decode($arr, true);

            if ($arr['address']) {
                $user = new Human;
                $user->data($arr);
                $user->save();
            }

            if ($web) {
                echo "备用接口启动";
                dump($arr);
                dump($e);
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
        $val['scene']   = '';

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
            $addressCheck = strstr($a, 'China');
            if ($addressCheck) {
                $val['address'] = $b;
            }
        }


        if ($val['scene']) {
            $str      = $val['scene'];
            $strCheck = strstr($str, '被判定为企业专线');
            if ($strCheck) {
                $val['scene'] = '';
            }
        }
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
            $str        = $val['address'];
            $scoreCheck = strstr($str, '被判定');
            if ($scoreCheck) {
                $str = '';
            }
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
        if ($val['address']) {
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
        $this->assign('title', '账号设置');
        return $this->fetch();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->assign('title', '会员中心');
        return $this->fetch();
    }


}

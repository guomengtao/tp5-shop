<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\index\model\Footprint;
use app\index\model\Human;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;
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

    /**
     * 会员中心
     */
    public function human($ip = '119.62.42.104')
    {

        $web = input('web');

        $footpirnt = Footprint::where('ip', $ip)->count();
        if (!$footpirnt) {
            return "";
        }
        // 已存在的跳过
        $human = Human::where('ip', $ip)->find();
        if ($human) {
            if ($web) {
                dump($human->toArray());
                echo "--ok--";
            }

            return "";
        }

        $url = "https://www.ipip.net/ip.html";

        $table = QueryList::post($url, ['ip' => $ip])->find('table');

        // 采集表头
        $tableHeader = $table->find('tr:eq(0)')->find('td')->texts();
        // 采集表的每行内容
        $tableRows = $table->find('tr:gt(0)')->map(function ($row) {
            return $row->find('td')->texts()->all();
        });

        // print_r($tableHeader->all());

        if ($web) {
            print_r($tableRows->all());
        }

        $arr = $tableRows->all();
        $this->save($arr, $ip);


    }

    public function save($arr = [], $ip = '1')
    {


        $arr = array_filter($arr);
        $arr = array_filter($arr, function ($item) {

            return $item['0'] !== '';

        });
        // dump($arr);
        $val = [];

        foreach ($arr as list($a, $b)) {
            // $a contains the first element of the nested array,
            // and $b contains the second element.
            if (!isset($a)) {
                continue;
            }
            if (!isset($b)) {
                continue;
            }

            echo "A: $a B: $b\n";

            // 1	id主键	int(100)			否	无	ID	AUTO_INCREMENT	修改 修改	删除 删除
            // 更多 更多
            // 	2	ip	varchar(50)	utf8_general_ci		否	无	ip		修改 修改	删除 删除
            // 更多 更多
            // 	3	address	varchar(200)	utf8_general_ci		否	无	地理位置		修改 修改	删除 删除
            // 更 更多
            // // 	5	scene	varchar(100)	utf8_general_ci		否	无	应用场景		修改 修改	删除 删除
            // // 更多 更多
            // // 	6	score	int(10)			否	无	真人率		修改 修改	删除 删除
            // // 更多 更多
            // // 	7	danger	varchar(200)	utf8_general_ci		否	无	威胁警报		修改 修改	删除 删除
            // // 更多 更多多 更多
            // 	4	isp	varchar(200)	utf8_general_ci		否	无	运营商		修改 修改	删除 删除
            // 更多

            $val['ip'] = $ip;

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
        if ($val['address']) {

            $str          = $val['address'];
            $val['score'] = $this->get_between($str, '：', ') 查');


            $strCheck = strstr($str, '\\');
            if ($strCheck) {
                $val['address'] = substr($str, 0, strpos($str, '\\'));
            }
            $user = new Human;
            $user->data($val);
            $user->save();
        }


        dump($val);
    }

    public function get_between($input, $start, $end)
    {
        $substr = substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
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
    public
    function login()
    {

    }

    /**
     * 注销登录
     */
    public
    function logout()
    {

    }

    /**
     * 个人信息
     */
    public
    function profile()
    {

    }

    /**
     * 修改密码
     */
    public
    function changePassword()
    {

    }
}

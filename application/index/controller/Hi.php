<?php
namespace app\index\controller;
 use think\Db;

 class Hi extends \think\Controller
{

    public function tom()
    {
        echo  "<h1>^_^</h1>";
        // 后面的数据库查询代码都放在这个位置
        // 插入记录 - 用原生写法 - 优点：直接用sql语句
    $result = Db::query('select * from think_data where id = 100');
      dump($result);
    }
    public function add($name)
    {
        

        return '查看name=' . $name . '的内容';
    }

    public function del($year, $month)
    {
        return '查看' . $year . '/' . $month . '的归档内容';
    }
    public function edit($year, $month)
    {
        return '查看' . $year . '/' . $month . '的归档内容';
    }
    public function sel($year, $month)
    {
        return '查看' . $year . '/' . $month . '的归档内容';
    }
}
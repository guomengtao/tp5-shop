<?php

namespace app\index\controller;

use think\Controller;

use app\admin\model\Data;  //引入
// use think\Loader;


class Model extends Controller
{


    public function delete()
    {


        // 删除模型数据，可以在实例化后调用delete方法。
        // $user = Data::get(316);
        // $user->delete(true);


        // 或者直接调用静态方法
        // Data::destroy(317,true);
        // 支持批量删除多个数据
        // Data::destroy('272,271,270');
        // 或者
        // $tom = Data::destroy([272,266,268]);
        // dump($tom);
        // 条件删除

        // 使用数组进行条件删除，例如：
        // $tom = Data::destroy(['id' => '185']);
        // dump($tom);

        // 使用闭包删除，例如：
        // Data::destroy(function($query){
        // 				 $query->where('id','>',318);
        // });
        // 或者通过数据库类的查询条件删除 直接删除
        // $tom = Data::where('id','=','314')->delete();
        // dump($tom);


    }

    public function updata()
    {
        // 在取出数据后，更改字段内容后更新数据。
        //   	$user = Data::get(277);
        // $user->title      = 'Tom123456';
        // $user->content    = '250285636t@qq.com';
        // $user->save();

        // 直接带更新条件来更新数据
        // $user = new Data;
        // // save方法第二个参数为更新条件
        // $user->save([
        //     'title'   => 'Rinuo.com277',
        //     'content' => 'thinkphp@qq.com'
        // ],['id' => 277]);

        // 模型支持静态方法直接更新数据，例如：
        // Data::where('id', 277)
        //   	->update(['title' => '245+245=450']);
        //或者使用：
        //Data::update(['id' => 277, 'title' => '246+246']);

        // 可以通过闭包函数使用更复杂的更新条件，例如：
        $user = new Data;
        $user->save(['title' => 'www.Rinuo.com'], function ($query) {
            // 更新status值为1 并且id大于10的数据
            $query->where('title', "onethink")->where('id', '>', 268);
        });


    }

    public function save()
    {
        // 添加一条数据;

        // 第一种是实例化模型对象后赋值并保存：
        // 		$tom             = new Data;
        // $tom->title      = 'haha^_^123456789';
        // $tom->content    = '250285636@qq.com';
        // $tom->save();

        // data方法批量赋值：
        // $tom = new Data;
        // $tom->data([
        //   'title'   =>  'thinkphp^_^123456',
        //    'content' =>  '250285636@qq.com'
        // ]);
        // $tom->save();

        // 直接在实例化的时候传入数据
        // $user = new Data([
        //     'title'   =>  'Rinuo.com',
        //     'content' =>  '250285636@qq.com'
        // ]);
        // $user->save();
        //获取自增ID
        // echo $user->id;

        // 静态方法
        // $user = Data::create([
        //     'title'   =>  'thinkphp1212121212',
        //     'content' =>  'thinkphp@qq.com'
        // ]);
        // echo $user->id;

        // 使用model助手函数实例化User模型
        // $user = model('Data');
        //模型对象赋值
        // $user->data([
        //     'title'   =>  'thinkphp123456',
        //     'content' =>  'thinkphp@qq.com'
        // ]);
        // $user->save();

        $user = model('Data');
        // 批量新增
        $list = [
            ['title' => 'Rinuo.com', 'content' => '250285636@qq.com'],
            ['title' => 'onethink', 'content' => 'onethink@qq.com']
        ];
        $user->saveAll($list);


        //echo $user->id; // 获取自增ID


    }

    public function find()
    {
        // 使用主键查询
        //$data = Data::get(250);
        //dump($data);
        // echo $data->content;

        // 使用数组查询
        //$data = Data::get(['title' => '250285636']);

        // 使用闭包查询
        // $data = Data::get(function($query){
        // $query->where('title', '=','250285636')
        // ->order('id', 'asc');
        // });


        // 实例化后查询
        // $data = new Data();
        // //查询单个数据
        // $data = $data->where('title','<>', '250285636')
        // 		->find();


        // echo $data->title;

        // $data = $data->toArray();

        //  dump($data);

        // 根据主键获取多个数据
        // $data = Data::all('179,197,198');

        // 或者使用数组
        // $data = Data::all([197,179]);
        // foreach($data as $key=>$user){
        //     echo $user->title . "<br />";
        // }


        // 使用数组查询
        // $data = Data::all(['id'=>197]);
        // // 使用闭包查询
        // $data = Data::all(function($query){
        //    $query->where('id', '>',179)->limit(30)->order('id', 'asc');
        // });
        // foreach($data as $key=>$user){
        //     echo $user->id ."<br />";
        // }

        // 使用实例化查询
        // $data = new Data();
        // //查询数据集
        // $data = $data->where('id','>', '196')
        // 		     ->limit(10)
        // 		     ->order('id', 'desc')
        // 		     ->select();

        // foreach($data as $key=>$user){
        //     echo $user->id . $user->title ."<br />";
        // }
        // dump($data);

        // 使用数据库的查询 最方便
        $data = data::where('id', '>', 325)->select();

        // 查询包括软删除的数据
        // $data = data::withTrashed()->where('id','>',107)->select();

        // 仅查询软删除了的数据
        //$data = Data::onlyTrashed()->where('id','>',185)->select();

        foreach ($data as $key => $user) {
            echo $user->id . "------";
            echo $user->create_time . "-----"; // 输出类似 2016-10-12 14:20:10
            echo $user->update_time . "<br />"; // 输出类似 2016-10-12 14:20:10
        }
        //dump($data);

    }

    public function index()
    {
        // 静态调用的写法 1
        $res = Data::get(197);

        // new的写法 2
        //$res = new Data();
        //$res = Data::get(177);

        // 可以加多个模型的写法 3
        //$res = Loader::model("Data");
        //$res = $res::get(179);

        // 使用助手函数的写法 4
        //$res = model("Data");

        //$res = $res::get(197);


        // 读取出对象其中的数组的值
        $res = $res->toArray();


        dump($res);
    }
}
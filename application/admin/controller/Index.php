<?php

namespace app\admin\controller;

use think\Db;
use think\Request;
use app\index\model\User;
use app\index\model\Shop;
use think\Cookie;

class Index extends \think\Controller
{
    public function tom()
    {

        $im = imagecreatetruecolor(100, 100);


        // 将背景设为红色
        $red = imagecolorallocate($im, 255, 0, 0);
        imagefill($im, 90, 90, $red);

        header('Content-type: image/jpg');
        imagepng($im);
        imagedestroy($im);

        die();

        //         if(extension_loaded('gd')){
        //     echo '可以使用gd<br>';
        //     foreach(gd_info() as $cate=>$value){
        //         echo "$cate:$value<br>";
        //     }
        // }
        // else{
        //     echo '没有安装gd扩展';
        // }
        // die();
        $im = imagecreatetruecolor(100, 100);


// 将背景设为红色
        $red = imagecolorallocate($im, 255, 0, 0);
        imagefill($im, 90, 90, $red);

        header('Content-type: image/png');
        imagepng($im);
        imagedestroy($im);

        die();

        header("Content-type: image/png");

//创建图像
        $im = @imagecreate(200, 50) or die("创建图像资源失败");

//图片背景颜色
        $bg = imagecolorallocate($im, 255, 255, 255);

//文字颜色
        $text_color = imagecolorallocate($im, 0, 0, 255);

//水平画一行字，要输出中文等需要 TTF 字体支持的请使用 magettftext() 函数
        imagestring($im, 5, 0, 0, "Hello world!", $text_color);

//以PNG格式输出图像
        imagepng($im);

//销毁图像资源
        imagedestroy($im);

        die();

        // dump(gd_info());
        // die();
        // echo phpinfo();
        // die();
        header('Content-Type: image/png');
        $im = @imagecreatetruecolor(120, 20)
        or die('Cannot Initialize new GD image stream');
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5, 'A Simple Text String', $text_color);
        imagepng($im);
        imagedestroy($im);

        die();
        echo "ok";
    }

    public function login()
    {


        $warning      = "";
        $get_password = "";

        // 登录功能
        if (Request::instance()->isPost()) {

            $phone    = input('phone');
            $password = input('password');

            $get_token = User::where('phone', '=', $phone)->value('token');

            // 先判断用户是否存在，用户不存在，先通知一下
            if (!$get_token) {

                $warning = "此管理员不存在";
            }

            if ($get_token <> '') {

                // 查询密码和账号是否正确
                $get_password = User::where('password', '=', md5(trim($password)))
                    ->where('phone', $phone)
                    ->count();

                if (!$get_password) {

                    // 此处可以加一个Session或者数据库加一个记录，记录密码错误次数
                    $warning = "密码不正确";

                    // 多设置一个直接停止，有用户先支付后逻辑错
                    $this->assign('warning', $warning);;

                    return view('loginlaravel');

                }
            }

            // 确认账号密码一致开始登录操作

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


                // 设置管理方便区分管理员
                if ($phone == "18210787405") {
                    Cookie::set('admin', 1, 3600000);
                    // 跳转之前再加一个验证是否是管理员身份  此处略
                    return $this->success('管理员您好^_^', 'admin/index/index');
                } else {
                    return $this->success('普通用户，登录成功^_^', 'index/index/index');
                }

            }


        }

        $this->assign('warning', $warning);

        return view('loginlaravel');
    }

    public function t()
    {
        return "test";
    }

    public function update()
    {

        $id      = input('id');
        $title   = input('param.title');
        $content = input('param.content');
        $content = Request::instance()->param('content', '', null);
        $price   = input('param.price');
        $label   = input('param.label');
        $address = input('param.address');
        $sort    = input('param.sort');
        $product = input('param.product');

        if (!$id) {
            return "商品id不能为空";
        }

        // 取出主键为1的数据
        $list = Shop::get($id);
        // echo $user->name;
        // dump($list);


        if ($title) {
            // return $price;
            // 模型的 静态方法
            Shop::update([
                'id'      => $id,
                'title'   => $title,
                'price'   => $price,
                'content' => $content,
                'label'   => $label,
                'address' => $address,
                'sort'    => $sort,
                'product' => $product,

            ]);

            return $this->success('恭喜商品修改成功^_^', 'show');


        }


        $this->assign('list', $list);


        // 渲染模板输出
        return $this->fetch();


    }


    public function show()
    {


        $show = Shop::where('id', '>', 0)->order('sort', 'desc')->paginate(5, 30);


        $show = Db::name("shop")->order('sort', 'desc')->paginate(10);


        $this->assign('show', $show);
        // 渲染模板输出

        return $this->fetch();


    }

    public function upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                // 成功上传后 获取上传信息
                // 输出 jpg
                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getFilename();
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function header()
    {


        $this->assign('show', "header");
        // 渲染模板输出
        return $this->fetch();


    }

    public function footer()
    {


        // 渲染模板输出
        return $this->fetch();


    }

    public function index()
    {


        // DB写法
        // $show = Db::name('data')->where('id','>',0)->order('id', 'desc')->paginate(10,80);

        // dump($show);
        // 模型写法
        $show = Shop::where('id', '>', 0)->order('sort', 'desc')->paginate(5, 30);

        // $show = $show->toArray();

        // dump($show);
        // exit();
        $this->assign('show', $show);
        // 渲染模板输出

        return $this->fetch();


    }

    public function view()
    {


        //echo input('param.id');

        $id = input('id');

        if ($id <> '') {


            // 查询数据 - 查询留言详情内容
            $list = Db::name('shop')
                ->where('id', '=', $id)
                ->select();
            // dump($list);

            // 查询数据 - 上一页
            //echo $id;
            $up = Db::name('shop')
                ->where('id', '>', $id)
                ->order('id', '')
                ->limit(1)
                ->value('id');
            //dump($up);

            // 查询数据 - 下一页
            $next = Db::name('shop')
                ->where('id', '<', $id)
                ->order('id', 'desc')
                ->limit(1)
                ->value('id');

            //dump($next);

            $this->assign('up', $up);
            $this->assign('next', $next);
            $this->assign('list', $list);


            // 渲染模板输出
            return $this->fetch();


        }
        return $this->fetch('no');
        return "留言不存在";

    }

    public function add()
    {


        $title   = input('param.title');
        $content = input('content');
//        $content = Request::instance()->param('content','','htmlspecialchars');
        $content = Request::instance()->param('content', '', null);
        $price   = input('price');
        $label   = input('param.label');
        $address = input('param.address');
        $sort    = input('param.sort');
        $product = input('param.product');

//        Request::instance()->get('name','','htmlspecialchars'); // 获取get变量 并用htmlspecialchars函数过滤


        $lesson = "";
        //dump($title);

        if ($title <> '') {


            // 插入记录 - 去掉表前缀
            // $result = Db::name('data')
            // ->insert(['title' => $title, 'content' => $content, 'create_time' => time()]);
            //dump($result);


            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('lesson');

            // 移动到框架应用根目录/public/uploads/ 目录下
            if ($file) {
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    // 成功上传后 获取上传信息
                    // 输出 jpg
                    echo $info->getExtension() . "<br>";
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    echo $info->getSaveName() . "<br>";
                    // 输出 42a79759f284b767dfcb2a0197904287.jpg
                    echo $info->getFilename() . "<br>";
                    $lesson = $info->getSaveName();

                    // echo $lesson;
                    // exit();

                } else {
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }


            // 模型的 静态方法
            $user = Shop::create([
                'title'   => $title,
                'price'   => $price,
                'lesson'  => $lesson,
                'content' => $content,
                'label'   => $label,
                'address' => $address,
                'sort'    => $sort,
                'product' => $product,

            ]);

            return $this->success('恭喜您发布课程成功^_^', 'add');

        }

        return $this->fetch();
    }

    public function delete()
    {


        //echo input('param.id');

        $id = input('id');

        $id = 2;


        if ($id <> '') {


            // 或者直接调用静态方法
            Shop::destroy($id);

        }
        return $this->success('删除成功^_^', 'show');

    }

    public function ajax()
    {


        return $this->fetch();


    }
}
 
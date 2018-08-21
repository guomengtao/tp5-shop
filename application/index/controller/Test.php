<?php   
namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\User;
use think\Loader;

class Test extends Controller{
  public function iframe(){

     // return 10086;
     return view();

  }
  public function index(){
    //1
    /*$res = User::get(2);*/
    //2
    /*$user = new User;
    $res = $user::get(2);*/
    //3
    $user = Loader::model("User");
    $res = $user::get(4);
    $res = $res->toArray();
    dump($res);
  }
}

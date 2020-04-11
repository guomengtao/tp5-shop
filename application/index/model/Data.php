<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use think\Cookie;
use app\index\model\Likes;


class Data extends model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    protected $auto = [];
    protected $insert = ['name', 'ip'];
    protected $update = ['name'];

    public function getOnAttr($value, $data)
    {
        // 查询当前用户有没有点赞
        $on = likes::where('data_id', '=', $data['id'])
            ->where('user_id', '=', Cookie::get('user_id'))
            ->count();
        return $on;
    }


    public static function add()
    {
        if (!trim(input('titleChat_send'))) {
            return 0;
        }
        $user                = new Data;
        $user->title         = input('titleChat_send');
        $user->age           = input('reply', 0);
        $user->user_id       = Cookie::get('user_id');
        $user->reply_user_id = input('reply_user_id');
        $user->shop          = 150;
        $user->save();


        // 获取自增ID
        return $user->id;
    }

    public static function jack($user_id = 0)
    {
        if ($user_id) {
            // 查询最新的聊天信息
            $talk_new = Data::with('watermelon,user,dataSelf,likesList,dataselfreply')
                ->withCount('likeslist')
                ->withCount('dataselfreply')
                ->where('user_id', $user_id)
                ->order('id', 'desc')
                ->paginate(10);
        } else {
            // 查询最新的聊天信息
            $talk_new = Data::with('watermelon,user,dataSelf,likesList,dataselfreply')
                ->withCount('likeslist')
                ->withCount('dataselfreply')
                ->where('age', 0)
                ->order('id', 'desc')
                ->paginate(10);
        }


        return $talk_new;
    }

    public function test()
    {
        return 12;
    }

    public function foot()
    {
        return $this->hasOne('Footprint');
    }


    public function watermelon()
    {
        return $this->hasOne('Shop', 'id', 'shop');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function dataComment()
    {
        $this->hasMany('Data', 'age', 'id');
    }

    // 测试 查询关联的多条点赞
    public function likesList()
    {
        return $this->hasMany('likes', 'data_id');
    }


    public function comments()
    {
        return $this->hasMany('Data', 'age');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function shop_title()
    {
        return $this->hasOne('Shop', 'id', 'shop');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function reply()
    {
        return $this->hasOne('Data','id','age');
    }

    

    public function user()
    {
        // return $this->belongsTo('User');
        // return $this->belongsTo('User','user_id','id');
        return $this->hasOne('User', 'id', 'user_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    // 关联自己 - 查询回复的哪条留言
    public function dataself()
    {
        return $this->hasOne('Data', 'id', 'age');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


    // 关联自己 - 查询回复自己的所有回复
    public function dataselfreply()
    {
        return $this->hasMany('Data', 'age', 'id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public function good()
    {
        // return $this->hasMany('likes','data_id');
        return $this->hasMany('data', 'age', 'id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


    public function ipinfo()
    {
        return $this->hasone('Ipinfo', 'data_id');
    }


    public function userinfo()
    {
        return $this->hasOne('Userinfo', 'id', 'id');
    }

    public function getCreate_timeAttr($value)
    {
        return "123";
    }

    public function setNameAttr($value)
    {
        // return request()->ip();
        return "30";
    }

    public function setIpAttr($value)
    {
        // return request()->ip();
        return "30";
    }


    public function setTitleAttr($value)
    {
        // return request()->ip();
//        return strtolower($value);
        return $value;
    }

    public function setContentAttr($value)
    {
//        return strtolower($value);
        return $value;
    }

    public function getTitleAttr($value)
    {
        return $value;
    }

    public function getContentAttr($value)
    {
        // $title = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        return $value;
    }

}
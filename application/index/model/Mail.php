<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;
use think\Cookie;

class Mail extends model
{

    public function footprint()
    {
        return $this->hasOne('Footprint', 'ip', 'ip');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }

    public static function send()
    {
        $from     = Cookie::get('user_id');
        $to       = input('to');
        $title    = input('title');
        $group_id = input('group_id');

        if (!$to) {
            $to = input('from');
        }
        $arr = [
            'from'     => $from,
            'to'       => $to,
            'title'    => $title,
            'group_id' => $group_id,
        ];

        $add = new Mail();
        $add->data($arr);
        $add->save();


        // 更新私信id到group_id
        if (!$group_id) {
            $group_id = $add->id;
            Mail::update(['id' => $group_id, 'group_id' => $group_id]);
        }


        return $add->id;
    }

    public function getMsgNullAllAttr($value,$data)
    {

        $group_id = $data['group_id'];

        $data = Mail::Where('group_id',$group_id)
            ->where('msg',null)
            ->count();


        return $data;
    }

    public function MsgNullAllTest()
    {
        return $this->hasMany('Mail', 'group_id ', 'group_id ');
    }

    public function user()
    {
        return $this->hasOne('User', 'id', 'from');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }


}
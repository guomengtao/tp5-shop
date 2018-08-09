<?php
namespace app\index\model;

use think\Model;

class Article extends Model 
{
    public function comments()
    {
        return $this->hasMany('Comment','commentable_id');
        //hasOne('关联模型名','外键名','主键名',['模型别名定义'],'join类型');
    }
}
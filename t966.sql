SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `think_article` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `date` bigint(20) NOT NULL COMMENT '日期',
  `content` longtext NOT NULL COMMENT '内容',
  `excerpt` longtext NOT NULL COMMENT '摘要',
  `alias` varchar(200) NOT NULL DEFAULT '' COMMENT '别名',
  `author` int(10) NOT NULL DEFAULT '1' COMMENT '作者',
  `sortid` int(10) NOT NULL DEFAULT '-1' COMMENT '分类ID',
  `type` varchar(20) NOT NULL DEFAULT 'blog' COMMENT '标签',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数',
  `tagid` varchar(255) NOT NULL DEFAULT '0',
  `attnum` int(10) unsigned NOT NULL DEFAULT '0',
  `top` enum('n','y') NOT NULL DEFAULT 'n' COMMENT '全局置顶',
  `sortop` enum('n','y') NOT NULL DEFAULT 'n' COMMENT '分类置顶',
  `hide` enum('n','y') NOT NULL DEFAULT 'n' COMMENT '文章隐藏',
  `checked` enum('n','y') NOT NULL DEFAULT 'y',
  `allow_remark` enum('n','y') NOT NULL DEFAULT 'y',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '文章密码',
  `template` varchar(255) NOT NULL DEFAULT '' COMMENT '文章模板',
  `status` varchar(255) NOT NULL COMMENT '-1 => ''删除'', 0 => ''禁用'', 1 => ''正常'', 2 => ''待审核''',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `author` (`author`),
  KEY `sortid` (`sortid`),
  KEY `type` (`type`),
  KEY `views` (`views`),
  KEY `comnum` (`tagid`),
  KEY `hide` (`hide`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=277 ;

CREATE TABLE IF NOT EXISTS `think_article_tag` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tagname` varchar(60) NOT NULL DEFAULT '',
  `gid` text NOT NULL,
  PRIMARY KEY (`tid`),
  KEY `tagname` (`tagname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

CREATE TABLE IF NOT EXISTS `think_config` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `appid` int(11) NOT NULL,
  `appkey` text NOT NULL,
  `my_url` text NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `password` text,
  `status` tinyint(2) DEFAULT '0' COMMENT '状态',
  `phone` bigint(11) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `invite` int(11) DEFAULT NULL,
  `token` varchar(200) DEFAULT NULL,
  `online_time` int(11) DEFAULT NULL,
  `start_time` int(11) DEFAULT NULL,
  `expiration_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `rand` int(11) DEFAULT '0',
  `ip` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `phone_2` (`phone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `think_data` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `shop` int(11) NOT NULL,
  `phone` bigint(20) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `title` varchar(1000) NOT NULL,
  `age` int(11) NOT NULL,
  `ip` text,
  `session_id` text,
  `delete_time` int(11) DEFAULT NULL,
  `content` text,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2072 ;

CREATE TABLE IF NOT EXISTS `think_footprint` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone` bigint(255) DEFAULT NULL COMMENT '名称',
  `lesson` varchar(255) DEFAULT NULL,
  `referer` text COMMENT '来路',
  `domain` text COMMENT '来自域名',
  `browser` text COMMENT '浏览器类型',
  `pathinfo` text,
  `address` text,
  `url` text,
  `product` text,
  `os` text COMMENT '操作系统',
  `mobile` text,
  `status` tinyint(2) DEFAULT '0' COMMENT '状态',
  `title` text CHARACTER SET utf8,
  `age` int(11) DEFAULT NULL,
  `ip` text,
  `delete_time` int(11) DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=22386 ;

CREATE TABLE IF NOT EXISTS `think_ipinfo` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone` bigint(255) DEFAULT NULL COMMENT '名称',
  `country` varchar(255) DEFAULT NULL COMMENT '国家',
  `area` text COMMENT '地区',
  `isp` text COMMENT '网路',
  `region` text COMMENT '浏览器类型',
  `city` text,
  `country_id` text,
  `area_id` text,
  `region_id` text,
  `city_id` text COMMENT '操作系统',
  `isp_id` text,
  `status` tinyint(2) DEFAULT '0' COMMENT '状态',
  `title` text CHARACTER SET utf8,
  `age` int(11) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=2073 ;

CREATE TABLE IF NOT EXISTS `think_likes` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone` bigint(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `data_id` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `ip` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `phone_2` (`phone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=400 ;

CREATE TABLE IF NOT EXISTS `think_money` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone` bigint(255) NOT NULL COMMENT '名称',
  `lesson` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `address` text,
  `product` text,
  `label` text,
  `money` decimal(10,0) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '状态',
  `title` text CHARACTER SET utf8,
  `age` int(11) DEFAULT NULL,
  `ip` text,
  `delete_time` int(11) DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=331 ;

CREATE TABLE IF NOT EXISTS `think_order` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `phone` bigint(13) NOT NULL,
  `age` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `rand` int(11) DEFAULT NULL,
  `ip` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `buyer_email` text NOT NULL COMMENT '买家支付宝手机或邮箱',
  `buyer_id` bigint(20) NOT NULL COMMENT '买家支付宝唯一用户号',
  `total_fee` decimal(10,2) NOT NULL COMMENT '交易金额',
  `body` text NOT NULL COMMENT '商品描述',
  `subject` text NOT NULL COMMENT '商品名称',
  `out_trade_no` text NOT NULL COMMENT '唯一订单号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=718 ;

CREATE TABLE IF NOT EXISTS `think_shop` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '名称',
  `lesson` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `address` text,
  `product` text,
  `label` text NOT NULL,
  `price` decimal(5,2) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `title` text CHARACTER SET utf8 NOT NULL,
  `age` int(11) NOT NULL,
  `page_view` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=263 ;

CREATE TABLE IF NOT EXISTS `think_sms` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `phone` bigint(13) NOT NULL,
  `age` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `rand` int(11) NOT NULL,
  `ip` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=749 ;

CREATE TABLE IF NOT EXISTS `think_user` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `password` text,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `phone` bigint(11) NOT NULL,
  `age` int(11) NOT NULL,
  `invite` int(11) DEFAULT NULL,
  `token` varchar(200) DEFAULT NULL,
  `online_time` int(11) DEFAULT NULL,
  `start_time` int(11) DEFAULT NULL,
  `expiration_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `rand` int(11) DEFAULT '0',
  `ip` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `phone_2` (`phone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=393 ;

CREATE TABLE IF NOT EXISTS `think_userinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=17998 ;

CREATE TABLE IF NOT EXISTS `think_user_qq` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL COMMENT '名称',
  `openid` text,
  `figureurl_qq_2` text COMMENT '状态',
  `phone` bigint(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `invite` int(11) DEFAULT NULL,
  `gender` text,
  `start_time` int(11) DEFAULT NULL,
  `figureurl_qq_1` text,
  `delete_time` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip` text,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `phone_2` (`phone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

CREATE TABLE IF NOT EXISTS `think_video` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone` bigint(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `title` varchar(255) NOT NULL,
  `age` float NOT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `content` text,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `shop` int(11) DEFAULT NULL,
  `ip` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1566 ;
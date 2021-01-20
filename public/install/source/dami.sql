/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50562
Source Host           : localhost:3306
Source Database       : dami_old

Target Server Type    : MYSQL
Target Server Version : 50562
File Encoding         : 65001

Date: 2021-01-20 03:57:17
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `dami_access`
-- ----------------------------
DROP TABLE IF EXISTS `dami_access`;
CREATE TABLE `dami_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `pid` int(11) NOT NULL,
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_access
-- ----------------------------
INSERT INTO `dami_access` VALUES ('4', '7', '2', null, '37');
INSERT INTO `dami_access` VALUES ('4', '4', '2', null, '37');
INSERT INTO `dami_access` VALUES ('4', '3', '2', null, '37');
INSERT INTO `dami_access` VALUES ('4', '2', '2', null, '37');
INSERT INTO `dami_access` VALUES ('4', '1', '0', null, '37');
INSERT INTO `dami_access` VALUES ('4', '37', '1', null, '0');
INSERT INTO `dami_access` VALUES ('5', '35', '3', null, '31');
INSERT INTO `dami_access` VALUES ('5', '34', '3', null, '31');
INSERT INTO `dami_access` VALUES ('5', '33', '3', null, '31');
INSERT INTO `dami_access` VALUES ('5', '31', '2', null, '37');
INSERT INTO `dami_access` VALUES ('5', '36', '3', null, '31');

-- ----------------------------
-- Table structure for `dami_ad`
-- ----------------------------
DROP TABLE IF EXISTS `dami_ad`;
CREATE TABLE `dami_ad` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) DEFAULT NULL,
  `content` text,
  `description` text,
  `addtime` varchar(32) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_ad
-- ----------------------------
INSERT INTO `dami_ad` VALUES ('28', '顶部banner1000*205PX', '<a href=\"http://www.damicms.cn\"><img src=\"/Public/Uploads/ad/banner.png\"/></a>', '', '2014-02-21 11:51:57', '1', '0');
INSERT INTO `dami_ad` VALUES ('29', '首页中右广告', '<img src=\"/Public/Uploads/ad/1361775546.gif\"/>', '', '2015-07-01 12:52:47', '1', '1');

-- ----------------------------
-- Table structure for `dami_admin`
-- ----------------------------
DROP TABLE IF EXISTS `dami_admin`;
CREATE TABLE `dami_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `lastlogintime` int(10) NOT NULL,
  `lastloginip` varchar(20) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_client` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_admin
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_admin_lock`
-- ----------------------------
DROP TABLE IF EXISTS `dami_admin_lock`;
CREATE TABLE `dami_admin_lock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(60) DEFAULT NULL,
  `expire_time` int(11) DEFAULT '0',
  `username` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_admin_lock
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_article`
-- ----------------------------
DROP TABLE IF EXISTS `dami_article`;
CREATE TABLE `dami_article` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) DEFAULT NULL,
  `titlecolor` varchar(80) DEFAULT NULL,
  `author` char(20) DEFAULT NULL,
  `keywords` char(40) DEFAULT NULL,
  `description` mediumtext,
  `note` mediumtext,
  `linkurl` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '0',
  `copyfrom` varchar(100) DEFAULT NULL,
  `addtime` varchar(32) DEFAULT NULL,
  `islink` tinyint(1) unsigned DEFAULT '0',
  `isflash` tinyint(1) DEFAULT '0',
  `istop` tinyint(1) unsigned DEFAULT '0',
  `isimg` tinyint(1) unsigned DEFAULT '0',
  `imgurl` varchar(255) DEFAULT NULL,
  `ishot` tinyint(1) unsigned DEFAULT '0',
  `pagenum` int(8) unsigned DEFAULT '0',
  `hits` mediumint(9) DEFAULT '1',
  `good_tp` mediumint(9) DEFAULT '0',
  `content` longtext,
  `typeid` smallint(5) unsigned DEFAULT NULL,
  `voteid` int(10) unsigned DEFAULT '0',
  `is_from_mobile` int(11) DEFAULT '0' COMMENT '是否来自手机发布',
  `price` text,
  `color` text,
  `product_xinghao` text,
  `dami_uid` int(11) DEFAULT '0',
  PRIMARY KEY (`aid`),
  KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_article
-- ----------------------------
INSERT INTO `dami_article` VALUES ('55', '企业概况', null, '未知', '', '\r\n	大米CMS(又名3gcms)是一个免费开源、快速、简单的PC建站和手机建站集成一体化系统， 我们致力于为用户提供简单、快捷的PC建站和智能手机建站解决方案。 提供开源的安卓手机客户端（APK）和', '...', null, '1', '', '2014-02-24 10:35:25', '0', '0', '0', '0', null, '0', null, '21', '0', '<p style=\"margin-top:0px;margin-bottom:0px;padding:0px;line-height:30px;letter-spacing:1px;text-align:justify;font-family:Tahoma, \'Microsoft Yahei\', Simsun;font-size:14px;white-space:normal;background-color:#FFFFFF;\">\r\n	大米CMS(又名3gcms)是一个免费开源、快速、简单的PC建站和手机建站集成一体化系统， 我们致力于为用户提供简单、快捷的PC建站和智能手机建站解决方案。 提供开源的安卓手机客户端（APK）和对应的服务器端系统源码下载。 您有什么建议或发现什么BUG，请发送邮件至cchaoming@163.com,我们将尽快为您解决\r\n</p>\r\n<div class=\"detail-bd\" style=\"margin:0px;padding:0px;font-family:Tahoma, \'Microsoft Yahei\', Simsun;font-size:14px;line-height:21px;white-space:normal;background-color:#FFFFFF;\">\r\n	<br style=\"margin:0px;padding:0px;\" />\r\n大米CMS特点<br style=\"margin:0px;padding:0px;\" />\r\n<br style=\"margin:0px;padding:0px;\" />\r\n1:扩展字段自定义,根据自己系统需要无限扩展字段<br style=\"margin:0px;padding:0px;\" />\r\n<br style=\"margin:0px;padding:0px;\" />\r\n2:后台栏目分类无限极，并可以控制字段的显示或隐藏,方便管理<br style=\"margin:0px;padding:0px;\" />\r\n<br style=\"margin:0px;padding:0px;\" />\r\n3：列表模板（list目录下）和详细模板（page目录下）自定义<br style=\"margin:0px;padding:0px;\" />\r\n<br style=\"margin:0px;padding:0px;\" />\r\n4:基于thinkphp框架开发（官网www.thinkphp.cn）, 内置大量函数方便前台模板调用标签请参看tp官网<br style=\"margin:0px;padding:0px;\" />\r\n<br style=\"margin:0px;padding:0px;\" />\r\n5:作站灵活,可以将该系统做成任何类型网站，内置新闻类型站、企业站、手机3g站模型,通过http://***安装目录****/?t=xinwen这种查看，方便二次开发出不同模板<br style=\"margin:0px;padding:0px;\" />\r\n<br style=\"margin:0px;padding:0px;\" />\r\n6:支持手机访问\r\n</div>\r\n<br />', '15', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('56', '企业文化', null, '未知', '', '', '', null, '1', '', '2013-02-20 15:18:15', '0', '0', '0', '0', null, '0', '0', '12', '0', '<span style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">企业文化写这里</span>', '16', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('57', '企业荣誉', null, '未知', '', '', '企业荣誉放这里', null, '1', '', '2013-02-20 15:18:38', '0', '0', '0', '0', null, '0', '0', '9', '0', '企业荣誉放这里', '17', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('58', '信息时代不用手机,活得很糟糕?', null, '不详', '', '', '“我', null, '1', '网络', '2013-02-20 15:19:04', '0', '0', '0', '0', null, '0', '0', '8', '0', '<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">“我没有智能手机。我甚至一部手机也没有。”</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">Roz Warren 是一名 57 岁的大叔，在图书馆工作，在给《纽约时报》的撰稿中，他从一个非手机用户的角度，描述了自手机出现后，身边的人荒诞的行为。当然，这些行为在你我看来也许再正常不过。 没有手机给 Roz 的生活带来不少方便。比如当他跟朋友约好下午 1 点在星巴克见面，朋友在 12：50 没办法打电话告诉他：“我得迟一点，大概 20 分钟。”这迫使朋友不得不准时出现。而如果朋友无法准时，Roz 也能预料到。</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">然而在有手机的人那里，约会是另一种情形。首先，“1 点星巴克见”并不意味着真的是 1 点，甚至也不是在星巴克。当你出现在星巴克附近时，你就开始跟对方电话： ——我下地铁了。 ——我在停车。 ——我现在从 XX 路口走下来。 ——你在路的哪边？ ——我看到你了。右边，我在挥手。 ——我也看到了。</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">Roz 把这形象地称为“Smartphone Tango（智能手机探戈）”。没有手机，他自然无法参与其中，自私地剥夺了朋友的乐趣。他可不想获得这种手机乐趣。在他看来，人们过于追求手机所带来的兴奋，而这些“症状”被他称为“cellgasms（cell+orgasm）”——这就不只是 Smartphone Tango，而是任何与智能手机互动所带来的兴奋，人们已经对手机上瘾了。</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">有一天 Roz 与朋友 Deb 去纽约。结果在地铁上，Deb 发现自己的 iPhone 落在家里，那个下午，她无数次提到自己没有带电话，时不时拍下口袋，仿佛她身体丢失的某部分会突然出现。基于手机的一切乐趣——搜索、更新状态、发信息——都丢失了。她只能毫无联系地待在那一刻那个地方，就像很多年前她没有智能手机一样。 Roz 自认过时，要联系他，可以打固话，如果他不在家，你可以留言。在这 57 年里，他认为没有什么事情如此重要，需要立刻联系上，无法等到他回到家。特别当他儿子已经长大，他不需要被叫到学校校医室，而他的图书馆工作更需要保持安静。</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">如果有紧急灾难呢？没有手机如何求助？Roz 觉得这个问题很可笑，难道带着一部手机就是为了以防万一？手机真的能在紧要关头救你一命？Roz 认为带着手机更多时候只是在最后一刻跟亲友道别。而他不打算这样做，如果那一刻真的来临，他希望尖叫着、怒吼着，绝望地试图跟上帝讨价还价，如原始人般。 何伟在《寻路中国》一开始就讲到他在中国的“神奇”经历：当他在公路上开着车，农民们在路上晒谷子，特别欢迎他将车轮碾过去。我看了觉得一点都不新奇，但接下来何伟一句话就点醒了我：这同时违反了交通安全和食品安全的法律。 如果你问我为什么要在一个科技网站介绍一个非手机用户的观点，我只能说我们时不时需要换个角度看问题。好不容易放假回到家中，你仍低着头玩手机，可能有时你会后悔，觉得是不是该陪陪爸妈说说话，但实在躲不开手机的诱惑。这难道不反常？甚至我们的情感也寄托在手机上：多少女生因为男朋友没有及时回复自己短信，没有经常打电话，积怨已久，某天突然在电话那端提出分手。</p>', '19', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('59', '通过手机网络发展潜在客户', null, '不详', '', '', '...', null, '1', '网络', '2013-02-20 15:33:55', '0', '0', '0', '0', null, '0', null, '34', '0', '<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">\r\n	年轻有为的程序员 Luke Thomas 不仅喜欢写程序，还十分上心与发展客户 / 用户的技巧。用他自己的话说，“让一个项目从企划成为现实真是太美妙了”。\r\n</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">\r\n	他认为，一个创业计划在上路的时候是最为艰难的，因为一切都还只是一片空白。对于如何通过网络渠道发展潜在客户，Luke 在博客中分享了自己的秘诀。 利用搜索引擎 Luke 推荐的是 Google 关键字工具（Google Adwords Keyword Tool）。假设有一位创业者想办一家车险公司，创业者在宣传中既可以说自己经营的是“汽车保险（car insurance）”，也可以说自己经营的是“车辆保险（auto insurance）”。\r\n</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">\r\n	Google 关键字工具提供的信息会显示，人们搜索“汽车保险（car insurance）”的次数比“车辆保险（auto insurance）”要多，所以选择前者更符合消费者的兴趣。 这种做法的理论依据是“使用消费者的惯用语言”。除了利用搜索引擎外，Luke 还推荐了阅读相关书籍、阅读业内博客、逛行业论坛、逛行业相关社交群组、与业内人士聚会等办法。\r\n</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">\r\n	鉴于国内的情况，Google 能帮上忙的地方可能不多，百度似乎没有提供类似于 Google 关键字工具的服务，但有一个“百度指数搜索”。通过这个工具我们可以获知，对 36 氪感兴趣的网友主要分布在北京、上海、广州、杭州，以男性居多（81.64%），他们多数是 IT 从业者，大部分人的年龄在 30 至 39 岁之间。 如何应用国内搜索引擎提供的数据服务帮助自己发展潜在客户 / 用户呢？大家可以摸索摸索。 利用社交网络的公共主页 Luke 认为，Facebook 等社交网络的兴起极大地简化了市场调研的过程。他举例，一个创业者准备入驻自主学习（Homeschool）行业，社交网络的公共主页就是他最好的市场信息来源。关注自主学习在 Facebook 上的粉丝页，可以看到这个组群里有超过 20000 人。他们最热议的话题是，应该选用哪一本教材。怎样，有灵感了吗？ Luke 表示，这些公共主页的价值不仅如此，你可以给群组内的成员发信息，与他们进一步沟通。你还可以一个一个地点开这些组内成员的个人主页，看看他们的兴趣所在，说不定会有惊人的发现。\r\n</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">\r\n	利用微博名人 Luke 建议创业者们去搜索一下自己行业内的 Twitter 名人，比如那个准备入驻自主学习行业的创业者就可以在 Twitter 上搜索 Homeschool，看看哪些名字在圈子里面是最有价值的。新浪微博也有搜索的功能，我们通过标签搜索“创业”，可以看到李开复、薛蛮子等人。 找到名人了然后呢，直接给他们发信息让对方 @你吗？当然不是。Luke 建议大家还是脚踏实地，一步一步来，先在小圈子内培养自己的名声，做出一定成果后再去联系他们。当你的名字出现在这些大咖的页面上，这就是很好的推广。 学会发信息找到若干潜在客户 / 用户后，如何经营和他们的关系，从他们身上获取可贵的市场信息呢？发一封电邮不乏为真诚且恰当的做法。邮件内容要简单，没有任何的导向性，这样获取的信息才不会有失偏颇。比如：在 XXX 领域，如果你有能力，你最愿意优化 ____？\r\n</p>\r\n<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">\r\n	假设你要上线一个网站，你可以直接发去两个网站入口，询问对方更加偏好哪一种。面对大量的采访信息，你可能需要借助于一些数据分析软件来帮助分析结论。 对于如何问问题，这里面有一些讲究。使用选择题会使答案受限于几个你预设的框架，而开放性问题容易答非所问。Luke 表示，自己曾向潜在客户咨询某领域的优化建议，结果对方回复他，该被优化的是 Safari 浏览器，这显然不是他要的结果。福特汽车创始人、让汽车普及的 Henry Ford 有名言：“如果我当时问大众需要什么，大家只会告诉我，他们要跑得更快的马儿。” 推己及人不论你采取哪种方法，请记住推己及人。与潜在客户交洽时把自己放在对方的位置上，让他们感到舒服。\r\n</p>', '19', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('60', '大米cms1.21发布', null, '不详', '', '新增： 1、入口页面UI美化 2、手机客户端访问识别。 3、非手机客户端访问用到某些功能时，提示安装客户端 修正： 1、入口页布局 2、个人中心页布局 3、图片云上传机制优化。', '新增...', null, '1', '网络', '2013-02-20 15:34:28', '0', '0', '0', '0', null, '1', null, '49', '0', '<p style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">新增： 1、入口页面UI美化 2、手机客户端访问识别。 3、非手机客户端访问用到某些功能时，提示安装客户端 修正： 1、入口页布局 2、个人中心页布局 3、图片云上传机制优化。</p>', '20', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('72', '测试测试哈哈哈', null, '不详', '', '', 'dfdfsfdfdfdfdfdff\r\n\r\n\r\n	中文测试\r\n\r\n\r\n	\r\n\r\n\r\n	牛人\r\n\r\n\r\n	dfdfdsf...', null, '1', '网络', '2013-02-27 14:32:57', '0', '0', '0', '0', null, '0', null, '40', '0', '<p>\r\n	dfdfsfdfdfdfdfdff\r\n</p>\r\n<p>\r\n	中文测试\r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	牛人\r\n</p>\r\n<p>\r\n	dfdfdsf\r\n</p>', '19', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('61', '大米CMS手机客户端1.2发布', null, '不详', '', '测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试', '测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试...', null, '1', '网络', '2013-02-20 15:34:59', '0', '0', '0', '0', null, '0', null, '54', '0', '测试测试测试测试测试测试测试测试<span style=\"white-space:normal;\">测试测试测试测试测试测试测试测试<span style=\"white-space:normal;\">测试测试测试测试测试测试测试测试<span style=\"white-space:normal;\">测试测试测试测试测试测试测试测试<span style=\"white-space:normal;\">测试测试测试测试测试测试测试测试</span></span></span></span>', '20', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('62', '公司公告内测1', null, '未知', '', '', '', null, '1', '', '2013-02-20 15:35:50', '0', '0', '0', '0', null, '0', '0', '23', '0', '<span style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1 公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1 公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1 公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1公司公告内测1</span>', '21', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('63', '大米CMS全新UI内测中', null, '未知', '', '大米CMS为改进用户体验，全面更新UI，内测中，在使用的过程中，可能会遇到一些问题，我们会在后续的版本解决。 如果有你建议，请到我们团队论坛中留言。 http://www.damicms.com/bb', '...', null, '1', '', '2013-02-20 15:36:17', '0', '0', '0', '0', null, '0', null, '37', '0', '<span style=\"color:#333333;font-family:Helvetica, Arial, sans-serif;font-size:medium;line-height:normal;background-color:#f9f9f9;\">大米CMS为改进用户体验，全面更新UI，内测中，在使用的过程中，可能会遇到一些问题，我们会在后续的版本解决。 如果有你建议，请到我们团队论坛中留言。 http://www.damicms.com/bbs</span>', '21', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('66', '测试产品', null, '未知', '', '型号: 价格：面议 颜色:', 'android开发android开发android开发a...', null, '1', '', '2014-02-24 09:57:45', '0', '0', '0', '1', '/Public/Uploads/thumb/thumb_1393207060.jpg', '0', null, '70', '0', 'android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发<span style=\"white-space:normal;\">android开发</span></span></span></span></span></span></span></span></span></span></span></span></span></span>', '24', null, '1', '4000', '灰色', 'M002457J', '0');
INSERT INTO `dami_article` VALUES ('68', '招聘PHP开发人员', null, '未知', '', '待遇4K-6k名额:2', '...', null, '1', '', '2013-02-20 15:46:25', '0', '0', '0', '0', null, '0', null, '0', '0', '<h3 class=\"ui-li-heading\" style=\"font-size:16px;margin:0.6em 0px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;color:#333333;font-family:Helvetica, Arial, sans-serif;line-height:normal;\"><span style=\"font-size:12px;font-weight:normal;\">精通PHP +mysql开发，至少熟练掌握一个框架！有项目开发经验</span></h3>', '25', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('69', '大米CMS手机开发专版', null, '未知', '大米测试', '型号: 价格：面议 颜色:', '大米CMS手机开发专版大米CMS手机开发专版大米CMS手机开发专版大米CMS手机开发专版', null, '1', '', '2021-01-16 15:33:31', '0', '0', '0', '1', '/Public/Uploads/thumb/1610782404.png', '1', null, '74', '0', '大米CMS手机开发专版大米CMS手机开发专版大米CMS手机开发专版大米CMS手机开发专版', '23', null, '1', '5400', '灰色', 'M002456J', '0');
INSERT INTO `dami_article` VALUES ('70', '大米手机CMS', null, '未知', '', '型号: 价格：面议 颜色:', 'PHP项目开发', null, '1', '', '2021-01-16 15:07:28', '0', '0', '0', '1', '/Public/Uploads/thumb/thumb_1393218295.jpg', '0', null, '278', '0', 'PHP项目开发', '23', null, '1', '5400', '灰色', 'M002458J', '0');
INSERT INTO `dami_article` VALUES ('124', '联系我们', null, '未知', '', '联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们', '联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们', null, '1', '', '2014-02-24 13:40:19', '0', '0', '0', '0', null, '0', '0', '4', '0', '联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们联系我们', '35', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('125', '测试案例', null, '不详', '', '测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例', '测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例', null, '1', '网络', '2014-02-24 14:32:58', '0', '0', '0', '1', '/Public/Uploads/thumb/thumb_1393223678.jpg', '0', '0', '11', '0', '测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例测试案例', '34', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('126', '测试产品案例', null, '不详', '', '测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例', '测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例', null, '1', '网络', '2014-02-24 14:35:37', '0', '0', '0', '1', '/Public/Uploads/thumb/thumb_1393223761.jpg', '0', '0', '34', '0', '测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例测试产品案例', '34', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('130', '测试行业新闻', null, '不详', '', '', '测试行业新闻测试行业新闻测试行业新闻测试行业新闻测试行业新闻测试行业新闻测试行业新闻测试行业新闻', null, '1', '网络', '2014-12-11 17:00:11', '0', '0', '0', '0', null, '0', null, '13', '0', '测试行业新闻测试行业新闻测试行业新闻测试行业新闻测试行业新闻测试行业新闻测试行业新闻测试行业新闻', '19', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('135', '瘦脸针', null, '不详', '55656', '', '144465wo aizhongguodfdnidfdsfslfsdfs', null, '1', '网络', '2021-01-16 14:13:24', '0', '0', '0', '0', null, '0', '0', '1', '0', '144465wo aizhongguodfdnidfdsfslfsdfs', '19', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('131', '大米CMS前端改版', null, '不详', '', '大米CMS前端改版，让用户更容易学习大米CMS标签，自己diy模板', '大米CMS前端改版，让用户更容易学习大米CMS标签，自己diy模板', null, '1', '网络', '2014-12-11 17:00:38', '0', '0', '0', '0', null, '0', '0', '28', '1', '大米CMS前端改版，让用户更容易学习大米CMS标签，自己diy模板', '20', '0', '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('132', '大米CMS强大的扩张性您知多少?', null, '不详', '', '大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?...', '大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?...', null, '1', '网络', '2014-12-11 17:01:15', '0', '0', '0', '0', null, '0', null, '18', '0', '大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?大米CMS强大的扩张性您知多少?', '20', null, '0', null, null, null, '0');
INSERT INTO `dami_article` VALUES ('133', '售后服务', null, '不详', '', '售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务', '售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后', null, '1', '网络', '2014-12-11 17:13:30', '0', '0', '0', '0', '', '0', null, '2', '0', '售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务售后服务', '37', null, '0', null, null, null, '0');

-- ----------------------------
-- Table structure for `dami_card`
-- ----------------------------
DROP TABLE IF EXISTS `dami_card`;
CREATE TABLE `dami_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_number` varchar(60) CHARACTER SET gbk DEFAULT NULL,
  `card_pwd` varchar(60) CHARACTER SET gbk DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT '0：未使用 1: 已使用',
  `money` float(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_card
-- ----------------------------
INSERT INTO `dami_card` VALUES ('2', '59847411515831953', 'hqt9x64spbhqn2pa1t', '1611027157', '0', '100.00');

-- ----------------------------
-- Table structure for `dami_config`
-- ----------------------------
DROP TABLE IF EXISTS `dami_config`;
CREATE TABLE `dami_config` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `updown` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sitetitle` text,
  `sitetitle2` text,
  `sitedescription` text,
  `siteurl` text,
  `waptpl` varchar(32) DEFAULT 'w3g',
  `sitetpl` varchar(32) NOT NULL DEFAULT 'default',
  `sitekeywords` text NOT NULL,
  `sitetcp` text NOT NULL,
  `sitelang` tinyint(1) NOT NULL DEFAULT '0',
  `watermark` tinyint(1) NOT NULL DEFAULT '0',
  `watermarkimg` text NOT NULL,
  `sitelx` text NOT NULL,
  `indexrec` tinyint(2) unsigned NOT NULL,
  `indexhot` tinyint(2) unsigned NOT NULL,
  `flashmode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `noticeid` int(5) unsigned NOT NULL,
  `noticenum` tinyint(2) unsigned NOT NULL,
  `rollnum` tinyint(2) unsigned NOT NULL,
  `isping` tinyint(1) unsigned NOT NULL,
  `sitelogo` text NOT NULL,
  `iszy` tinyint(1) unsigned NOT NULL,
  `pingoff` tinyint(1) unsigned NOT NULL,
  `postovertime` tinyint(2) unsigned NOT NULL DEFAULT '15',
  `bookoff` tinyint(1) unsigned NOT NULL,
  `mood` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ishits` tinyint(1) unsigned NOT NULL,
  `iscopyfrom` tinyint(1) unsigned NOT NULL,
  `isauthor` tinyint(1) unsigned NOT NULL,
  `indexvote` tinyint(2) unsigned NOT NULL,
  `xgwz` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ishomeimg` int(11) unsigned NOT NULL,
  `mouseimg` tinyint(1) unsigned NOT NULL,
  `artlistnum` int(2) unsigned NOT NULL,
  `artlisthot` tinyint(2) unsigned NOT NULL,
  `artlistrec` tinyint(2) unsigned NOT NULL,
  `articlehot` tinyint(2) unsigned NOT NULL,
  `articlerec` tinyint(2) unsigned NOT NULL,
  `urlmode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `suffix` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `indexlink` tinyint(1) NOT NULL DEFAULT '1',
  `indexpic` tinyint(1) NOT NULL DEFAULT '1',
  `indexpicnum` tinyint(2) NOT NULL DEFAULT '4',
  `indexpicscroll` tinyint(1) NOT NULL DEFAULT '0',
  `indexnoticetitle` varchar(32) NOT NULL,
  `indexrectitle` varchar(32) NOT NULL,
  `indexhottitle` varchar(32) NOT NULL,
  `indexlinktitle` varchar(32) NOT NULL,
  `indexlinkimg` tinyint(1) NOT NULL DEFAULT '0',
  `indexdiylink` tinyint(1) NOT NULL DEFAULT '1',
  `listrectitle` varchar(32) NOT NULL,
  `listhottitle` varchar(32) NOT NULL,
  `listshowmode` tinyint(1) NOT NULL DEFAULT '0',
  `artrectitle` varchar(32) NOT NULL,
  `arthottitle` varchar(32) NOT NULL,
  `indexvotetitle` varchar(32) NOT NULL,
  `indexpictitle` varchar(32) NOT NULL,
  `is_build_html` int(1) DEFAULT '0',
  `istrade` int(1) DEFAULT '0',
  `isread` int(1) DEFAULT '0' COMMENT '开启用户组阅读权限',
  `defaultup` int(11) DEFAULT '0' COMMENT '默认用户组',
  `defaultmp` int(11) DEFAULT '0' COMMENT '注册用户所在组',
  `islocalpic` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_config
-- ----------------------------
INSERT INTO `dami_config` VALUES ('1', '1', '大米CMS', 'PC、手机APP、微信集成，大米官网生成APK', '大米CMS', '/', 'w3g', 'default', 'damicms', '蜀ICP备123456', '0', '0', 'logo.png', '地  址：成都市建设南路88号<br />\r\n电  话：028-98765432<br />\r\n传  真：028-98765430<br />\r\n网  址：www.damicms.com<br />\r\n手  机：15982072714<br />\r\n邮  编：610000', '5', '8', '1', '5', '5', '1', '1', 'logo.png', '1', '1', '15', '1', '1', '1', '1', '1', '1', '1', '5', '1', '15', '10', '5', '10', '5', '2', '0', '1', '1', '4', '0', '站内公告', '推荐文章', '热点排行', '友情链接', '1', '1', '推荐文章', '热点排行', '1', '推荐文章', '热点排行', '热门投票', '推荐图文', '0', '1', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `dami_extend_fieldes`
-- ----------------------------
DROP TABLE IF EXISTS `dami_extend_fieldes`;
CREATE TABLE `dami_extend_fieldes` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `show_text` varchar(80) DEFAULT NULL,
  `field_name` varchar(250) DEFAULT NULL,
  `field_type` int(11) DEFAULT NULL COMMENT '0:单行文本1:多行文本2:编辑器3:下拉列表4:radio 5:多选列表6:多选按钮7:文件上传',
  `set_option` varchar(5000) DEFAULT NULL COMMENT '显示文本|值,显示文本|值',
  `default_option` varchar(5000) DEFAULT NULL,
  `orders` int(11) DEFAULT '255' COMMENT '排序',
  `css` varchar(255) DEFAULT NULL COMMENT 'css样式控制',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_extend_fieldes
-- ----------------------------
INSERT INTO `dami_extend_fieldes` VALUES ('19', '产品价格', 'price', '0', null, '0', '255', 'style=\'width:90px;\'');
INSERT INTO `dami_extend_fieldes` VALUES ('20', '颜色', 'color', '0', null, '', '255', '');
INSERT INTO `dami_extend_fieldes` VALUES ('21', '型号', 'product_xinghao', '0', null, '', '255', '');

-- ----------------------------
-- Table structure for `dami_extend_menu`
-- ----------------------------
DROP TABLE IF EXISTS `dami_extend_menu`;
CREATE TABLE `dami_extend_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(100) DEFAULT NULL,
  `typeid` int(11) DEFAULT '0',
  `action_name` varchar(200) DEFAULT NULL COMMENT '控制器名称',
  `table_name` varchar(100) DEFAULT NULL COMMENT '表名称',
  `enable` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_extend_menu
-- ----------------------------
INSERT INTO `dami_extend_menu` VALUES ('16', '新闻中心', '18', null, null, '1');
INSERT INTO `dami_extend_menu` VALUES ('17', '产品展示', '22', null, null, '1');
INSERT INTO `dami_extend_menu` VALUES ('18', '招聘', '25', null, null, '1');
INSERT INTO `dami_extend_menu` VALUES ('19', '工程案例', '27', null, null, '1');
INSERT INTO `dami_extend_menu` VALUES ('20', '操作日志', '0', 'Adminlog', 'dami_log', '1');

-- ----------------------------
-- Table structure for `dami_extend_show`
-- ----------------------------
DROP TABLE IF EXISTS `dami_extend_show`;
CREATE TABLE `dami_extend_show` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` int(11) DEFAULT NULL COMMENT '栏目ID',
  `field_id` int(11) DEFAULT NULL COMMENT '扩展字段ID',
  `is_show` int(11) DEFAULT '0' COMMENT '0:不显示1：显示',
  `orders` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_extend_show
-- ----------------------------
INSERT INTO `dami_extend_show` VALUES ('19', '23', '19', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('20', '23', '20', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('21', '23', '21', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('22', '24', '19', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('23', '24', '20', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('24', '24', '21', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('25', '35', '19', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('26', '35', '20', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('27', '35', '21', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('28', '34', '19', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('29', '34', '20', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('30', '34', '21', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('31', '38', '19', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('32', '38', '20', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('33', '38', '21', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('34', '39', '19', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('35', '39', '20', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('36', '39', '21', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('37', '40', '19', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('38', '40', '20', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('39', '40', '21', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('40', '41', '19', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('41', '41', '20', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('42', '41', '21', '1', '255');
INSERT INTO `dami_extend_show` VALUES ('43', '19', '19', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('44', '19', '20', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('45', '19', '21', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('46', '14', '19', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('47', '14', '20', '0', '255');
INSERT INTO `dami_extend_show` VALUES ('48', '14', '21', '0', '255');

-- ----------------------------
-- Table structure for `dami_favorites`
-- ----------------------------
DROP TABLE IF EXISTS `dami_favorites`;
CREATE TABLE `dami_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_favorites
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_find_password`
-- ----------------------------
DROP TABLE IF EXISTS `dami_find_password`;
CREATE TABLE `dami_find_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `hash` text,
  `addtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_find_password
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_flash`
-- ----------------------------
DROP TABLE IF EXISTS `dami_flash`;
CREATE TABLE `dami_flash` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `pic` varchar(255) NOT NULL,
  `title` varchar(50) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rank` mediumint(5) unsigned NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_flash
-- ----------------------------
INSERT INTO `dami_flash` VALUES ('7', '/', '1393307680.jpg', '企业站幻灯一', '1', '1');
INSERT INTO `dami_flash` VALUES ('8', '/', '1393307691.jpg', '企业站幻灯图片二', '1', '2');

-- ----------------------------
-- Table structure for `dami_guestbook`
-- ----------------------------
DROP TABLE IF EXISTS `dami_guestbook`;
CREATE TABLE `dami_guestbook` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(32) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` text NOT NULL,
  `addtime` varchar(20) NOT NULL,
  `recontent` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `tel` varchar(20) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_guestbook
-- ----------------------------
INSERT INTO `dami_guestbook` VALUES ('1', '游客', null, '我是第一个留言的', '2011-11-09 16:26:45', '', '1', null, null);
INSERT INTO `dami_guestbook` VALUES ('4', '游客', null, '再整个论坛吧', '2011-11-09 16:40:31', '', '1', null, null);
INSERT INTO `dami_guestbook` VALUES ('5', '游客', null, '还行,在看看', '2011-11-09 16:42:07', '', '1', null, null);

-- ----------------------------
-- Table structure for `dami_key`
-- ----------------------------
DROP TABLE IF EXISTS `dami_key`;
CREATE TABLE `dami_key` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(40) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `rank` tinyint(2) unsigned NOT NULL,
  `num` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_key
-- ----------------------------
INSERT INTO `dami_key` VALUES ('2', '百度', 'http://www.baidu.com', '1', '5');

-- ----------------------------
-- Table structure for `dami_label`
-- ----------------------------
DROP TABLE IF EXISTS `dami_label`;
CREATE TABLE `dami_label` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL,
  `content` text NOT NULL,
  `addtime` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_label
-- ----------------------------
INSERT INTO `dami_label` VALUES ('6', '联系电话', '400-800-888', '2018-09-13 10:42:19', '1');

-- ----------------------------
-- Table structure for `dami_link`
-- ----------------------------
DROP TABLE IF EXISTS `dami_link`;
CREATE TABLE `dami_link` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(40) NOT NULL,
  `url` varchar(80) NOT NULL,
  `logo` text NOT NULL,
  `islogo` tinyint(1) unsigned NOT NULL,
  `rank` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_link
-- ----------------------------
INSERT INTO `dami_link` VALUES ('7', '大米CMS', 'http://www.damicms.com', 'http://www.damicms.com/logo.png', '1', '1', '1');

-- ----------------------------
-- Table structure for `dami_log`
-- ----------------------------
DROP TABLE IF EXISTS `dami_log`;
CREATE TABLE `dami_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) DEFAULT NULL,
  `operate` text,
  `usergroup` varchar(255) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `result` text,
  `ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=411 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_log
-- ----------------------------
INSERT INTO `dami_log` VALUES ('1', '游客', 'admin登录成功', '', '1610108082', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('2', 'admin', '操作扩展字段', 'super', '1610108129', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('3', 'admin', '操作APK配置', 'super', '1610108166', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('4', 'admin', '操作网站配置', 'super', '1610108168', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('5', '游客', 'admin登录成功', '', '1610273309', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('6', '游客', 'admin登录成功', '', '1610527157', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('7', '游客', 'admin登录成功', '', '1610638602', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('8', 'admin', 'admin登录成功', 'super', '1610638853', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('9', '游客', 'admin登录成功', '', '1610638887', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('10', '游客', 'admin登录成功', '', '1610638969', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('11', '游客', 'admin登录成功', '', '1610639019', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('12', '游客', 'admin登录成功', '', '1610639119', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('13', '游客', 'admin登录成功', '', '1610639422', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('14', '游客', 'admin登录成功', '', '1610639621', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('15', '游客', 'admin登录成功', '', '1610639808', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('16', '游客', 'admin登录成功', '', '1610640082', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('17', '游客', 'admin登录成功', '', '1610640486', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('18', '游客', 'admin登录成功', '', '1610679743', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('19', 'admin', 'admin退出登录', 'super', '1610680970', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('20', '游客', 'admin登录成功', '', '1610682886', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('21', '游客', 'admin登录成功', '', '1610685914', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('22', 'admin', '操作扩展字段', 'super', '1610685917', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('23', 'admin', '操作扩展字段', 'super', '1610685917', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('24', '游客', 'admin登录成功', '', '1610690401', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('25', '游客', 'admin登录成功', '', '1610717354', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('26', '游客', 'admin登录成功', '', '1610722617', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('27', '游客', 'admin登录成功', '', '1610730776', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('28', '游客', 'admin登录成功', '', '1610770764', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('29', '游客', 'admin登录成功', '', '1610774022', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('30', 'admin', '修改文章ID:127成功', 'super', '1610774034', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('31', 'admin', '修改文章ID:127成功', 'super', '1610774048', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('32', 'admin', '修改文章ID:130成功', 'super', '1610774224', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('33', 'admin', '修改文章ID:130成功', 'super', '1610774345', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('34', '游客', 'admin登录成功', '', '1610776734', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('35', 'admin', '发布文章：瘦脸针成功', 'super', '1610777661', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('36', '游客', 'admin登录成功', '', '1610780791', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('37', 'admin', '删除文章ID：127', 'super', '1610780811', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('38', 'admin', '修改文章ID:70成功', 'super', '1610780848', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('39', 'admin', '修改文章ID:69成功', 'super', '1610780912', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('40', 'admin', '操作扩展字段', 'super', '1610782385', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('41', 'admin', '修改文章ID:69成功', 'super', '1610782411', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('42', 'admin', '发布文章：瘦脸针成功', 'super', '1610782443', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('43', 'admin', '批量待审文章136成功', 'super', '1610782573', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('44', 'admin', '批量幻灯文章136成功', 'super', '1610782592', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('45', 'admin', '批量删除文章136成功', 'super', '1610782605', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('46', 'admin', '搜索文章包含:大米', 'super', '1610783362', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('47', 'admin', '搜索文章包含:大米', 'super', '1610783406', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('48', 'admin', '搜索文章包含:大米', 'super', '1610783487', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('49', 'admin', '搜索文章包含:大米', 'super', '1610783516', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('50', 'admin', '搜索文章包含:大米', 'super', '1610783764', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('51', 'admin', '搜索文章包含:55656', 'super', '1610783769', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('52', 'admin', '搜索文章包含:大米', 'super', '1610783782', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('53', 'admin', '搜索文章包含:大米', 'super', '1610784028', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('54', 'admin', '搜索文章包含:大米', 'super', '1610784032', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('55', 'admin', '搜索文章包含:瘦脸', 'super', '1610784077', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('56', 'admin', '操作扩展字段', 'super', '1610784109', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('57', '游客', 'admin登录成功', '', '1610789236', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('58', '游客', 'admin登录成功', '', '1610792474', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('59', 'admin', '添加栏目帅哥', 'super', '1610792510', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('60', 'admin', '修改栏目帅哥', 'super', '1610793108', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('61', 'admin', '修改栏目帅哥', 'super', '1610793662', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('62', 'admin', '修改栏目帅哥', 'super', '1610793900', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('63', 'admin', '修改栏目帅哥', 'super', '1610793947', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('64', 'admin', '添加栏目shuaibadfd', 'super', '1610794041', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('65', 'admin', '修改栏目帅哥', 'super', '1610794096', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('66', 'admin', '删除栏目ID：42', 'super', '1610794317', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('67', 'admin', '操作扩展字段', 'super', '1610796111', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('68', '游客', 'admin登录成功', '', '1610803445', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('69', '游客', 'admin登录成功', '', '1610813709', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('70', 'admin', '操作网站配置', 'super', '1610813713', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('71', '游客', 'admin登录成功', '', '1610854842', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('72', 'admin', '操作广告管理', 'super', '1610856314', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('73', 'admin', '操作单页标签', 'super', '1610857892', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('74', 'admin', '操作附件清理', 'super', '1610859360', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('75', '游客', 'admin登录成功', '', '1610862756', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('76', 'admin', '操作幻灯管理', 'super', '1610862760', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('77', 'admin', '操作网站配置', 'super', '1610863071', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('78', '游客', 'admin登录成功', '', '1610867941', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('79', '游客', 'admin登录成功', '', '1610870743', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('80', 'admin', '添加管理用户:testuser成功', 'super', '1610870826', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('81', 'admin', '添加管理用户:testuser成功', 'super', '1610871218', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('82', 'admin', '修改管理用户:testuser改密码成功', 'super', '1610871592', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('83', 'admin', '修改管理用户:testuser成功', 'super', '1610871601', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('84', 'admin', '禁用管理员ID：5成功', 'super', '1610871652', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('85', 'admin', '启用管理员ID：5成功', 'super', '1610871655', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('86', 'admin', '禁用管理员ID：5成功', 'super', '1610871656', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('87', 'admin', '删除管理员ID：5成功', 'super', '1610871702', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('88', '游客', 'admin登录成功', '', '1610873653', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('89', 'admin', 'admin登录成功', 'super', '1610873655', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('90', 'admin', '操作网站配置', 'super', '1610873683', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('91', '游客', 'admin登录成功', '', '1610874020', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('92', 'admin', '管理组添加成功', 'super', '1610874437', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('93', 'admin', '修改管理组成功', 'super', '1610874632', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('94', 'admin', '修改管理组成功', 'super', '1610874639', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('95', 'admin', '修改管理权限成功', 'super', '1610877081', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('96', 'admin', '操作网站配置', 'super', '1610877224', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('97', 'admin', '操作附件清理', 'super', '1610877225', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('98', 'admin', '操作单页标签', 'super', '1610877226', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('99', 'admin', '操作广告管理', 'super', '1610877226', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('100', 'admin', '操作附件清理', 'super', '1610877227', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('101', 'admin', '操作幻灯管理', 'super', '1610877227', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('102', 'admin', '操作网站配置', 'super', '1610877228', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('103', 'admin', '操作留言管理', 'super', '1610878761', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('104', '游客', 'admin登录成功', '', '1610884715', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('105', '游客', 'admin登录成功', '', '1610886289', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('106', 'admin', '操作评论管理', 'super', '1610886292', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('107', 'admin', '操作留言管理', 'super', '1610886294', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('108', 'admin', '操作评论管理', 'super', '1610886295', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('109', 'admin', '操作留言管理', 'super', '1610886750', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('110', 'admin', '操作评论管理', 'super', '1610886756', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('111', 'admin', '操作友情链接', 'super', '1610887731', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('112', 'admin', '操作评论管理', 'super', '1610888077', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('113', 'admin', '操作友情链接', 'super', '1610888078', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('114', 'admin', '操作评论管理', 'super', '1610888825', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('115', 'admin', '操作友情链接', 'super', '1610888826', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('116', 'admin', '操作投票管理', 'super', '1610890007', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('117', 'admin', '操作友情链接', 'super', '1610890024', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('118', 'admin', '操作评论管理', 'super', '1610890025', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('119', 'admin', '操作投票管理', 'super', '1610890026', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('120', 'admin', '操作文章内链', 'super', '1610893778', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('121', '游客', 'admin登录成功', '', '1610895863', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('122', 'admin', '操作模板管理', 'super', '1610895866', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('123', '游客', 'admin登录成功', '', '1610897393', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('124', 'admin', '操作评论管理', 'super', '1610897836', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('125', 'admin', '操作留言管理', 'super', '1610897837', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('126', 'admin', '操作友情链接', 'super', '1610897837', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('127', 'admin', '操作投票管理', 'super', '1610897838', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('128', 'admin', '操作模板管理', 'super', '1610897839', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('129', 'admin', '操作扩展字段', 'super', '1610897955', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('130', 'admin', '操作幻灯管理', 'super', '1610897961', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('131', 'admin', '操作模板管理', 'super', '1610897966', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('132', 'admin', '操作文章内链', 'super', '1610901017', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('133', 'admin', '操作模板管理', 'super', '1610901021', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('134', 'admin', '操作文章内链', 'super', '1610901022', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('135', 'admin', '操作模板管理', 'super', '1610901024', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('136', 'admin', '操作评论管理', 'super', '1610901123', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('137', 'admin', '操作友情链接', 'super', '1610901124', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('138', 'admin', '操作投票管理', 'super', '1610901126', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('139', '游客', 'admin登录成功', '', '1610936711', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('140', 'admin', '操作幻灯管理', 'super', '1610936722', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('141', 'admin', '操作附件清理', 'super', '1610936723', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('142', 'admin', '操作单页标签', 'super', '1610936724', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('143', 'admin', '操作广告管理', 'super', '1610936725', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('144', 'admin', '操作留言管理', 'super', '1610936728', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('145', 'admin', '操作评论管理', 'super', '1610936729', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('146', 'admin', '操作友情链接', 'super', '1610936729', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('147', 'admin', '操作友情链接', 'super', '1610936731', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('148', 'admin', '操作投票管理', 'super', '1610936732', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('149', 'admin', '操作文章内链', 'super', '1610936733', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('150', 'admin', '操作模板管理', 'super', '1610936735', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('151', 'admin', '操作数据备份', 'super', '1610938234', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('152', 'admin', '操作数据工具', 'super', '1610938321', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('153', 'admin', '操作数据备份', 'super', '1610938471', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('154', 'admin', '操作数据工具', 'super', '1610938551', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('155', '游客', 'admin登录成功', '', '1610940420', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('156', 'admin', '操作投票管理', 'super', '1610940425', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('157', 'admin', '操作文章内链', 'super', '1610940425', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('158', 'admin', '操作模板管理', 'super', '1610940427', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('159', 'admin', '操作数据备份', 'super', '1610940428', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('160', 'admin', '操作数据工具', 'super', '1610940435', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('161', 'admin', '操作数据还原', 'super', '1610940438', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('162', 'admin', '操作数据备份', 'super', '1610940942', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('163', 'admin', '操作数据工具', 'super', '1610940999', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('164', 'admin', '操作数据还原', 'super', '1610941134', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('165', 'admin', '操作数据工具', 'super', '1610941162', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('166', 'admin', '操作数据还原', 'super', '1610941233', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('167', 'admin', '操作数据工具', 'super', '1610941236', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('168', 'admin', '操作数据备份', 'super', '1610941240', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('169', 'admin', '操作广告管理', 'super', '1610941286', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('170', 'admin', '操作数据备份', 'super', '1610941382', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('171', 'admin', '操作数据还原', 'super', '1610941382', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('172', 'admin', '操作数据工具', 'super', '1610941384', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('173', 'admin', '操作数据还原', 'super', '1610941428', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('174', 'admin', '操作数据备份', 'super', '1610941429', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('175', 'admin', '操作模板管理', 'super', '1610941430', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('176', 'admin', '操作数据备份', 'super', '1610941431', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('177', 'admin', '操作数据还原', 'super', '1610941432', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('178', '游客', 'admin登录成功', '', '1610946852', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('179', 'admin', '操作数据工具', 'super', '1610946855', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('180', 'admin', '操作数据采集', 'super', '1610946857', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('181', '游客', 'admin登录成功', '', '1610948315', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('182', 'admin', '操作广告管理', 'super', '1610948334', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('183', 'admin', '操作数据采集', 'super', '1610948341', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('184', 'admin', '操作评论管理', 'super', '1610952075', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('185', 'admin', '操作友情链接', 'super', '1610952075', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('186', 'admin', '操作文章内链', 'super', '1610952076', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('187', 'admin', '操作数据采集', 'super', '1610952079', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('188', 'admin', '操作数据还原', 'super', '1610952692', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('189', 'admin', '操作数据备份', 'super', '1610952693', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('190', 'admin', '操作数据采集', 'super', '1610952694', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('191', '游客', 'admin登录成功', '', '1610953290', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('192', 'admin', '操作模板管理', 'super', '1610955651', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('193', 'admin', '操作数据采集', 'super', '1610955727', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('194', 'admin', '操作数据还原', 'super', '1610957704', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('195', 'admin', '操作数据备份', 'super', '1610957706', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('196', 'admin', '操作数据采集', 'super', '1610957708', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('197', '游客', 'admin登录成功', '', '1610961950', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('198', 'admin', '操作生成静态页', 'super', '1610961952', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('199', 'admin', '操作数据采集', 'super', '1610961953', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('200', 'admin', '操作生成静态页', 'super', '1610961955', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('201', '游客', 'admin登录成功', '', '1610964379', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('202', 'admin', '操作数据采集', 'super', '1610964626', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('203', 'admin', '操作生成静态页', 'super', '1610964628', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('204', 'admin', '操作数据采集', 'super', '1610964832', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('205', 'admin', '操作生成静态页', 'super', '1610964834', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('206', 'admin', '操作数据还原', 'super', '1610965051', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('207', 'admin', '操作数据采集', 'super', '1610965052', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('208', 'admin', '操作生成静态页', 'super', '1610965053', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('209', '游客', 'admin登录成功', '', '1610968443', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('210', 'admin', '操作万能表格', 'super', '1610968448', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('211', 'admin', '操作数据备份', 'super', '1610969220', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('212', 'admin', '操作数据还原', 'super', '1610969224', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('213', 'admin', '操作数据备份', 'super', '1610969225', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('214', 'admin', '操作数据还原', 'super', '1610969229', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('215', 'admin', '操作数据采集', 'super', '1610969231', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('216', 'admin', '操作扩展字段', 'super', '1610969245', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('217', 'admin', '操作万能表格', 'super', '1610969982', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('218', '游客', 'admin登录成功', '', '1610973966', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('219', 'admin', '操作生成静态页', 'super', '1610973970', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('220', 'admin', '操作万能表格', 'super', '1610973971', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('221', 'admin', '操作生成静态页', 'super', '1610977571', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('222', 'admin', '操作数据采集', 'super', '1610977572', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('223', 'admin', '操作数据还原', 'super', '1610977573', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('224', 'admin', '操作数据备份', 'super', '1610977574', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('225', 'admin', '操作模板管理', 'super', '1610977574', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('226', 'admin', '操作文章内链', 'super', '1610977575', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('227', 'admin', '操作投票管理', 'super', '1610977576', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('228', 'admin', '操作友情链接', 'super', '1610977577', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('229', 'admin', '操作评论管理', 'super', '1610977578', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('230', 'admin', '操作留言管理', 'super', '1610977579', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('231', 'admin', '操作数据采集', 'super', '1610977814', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('232', 'admin', '操作数据备份', 'super', '1610977814', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('233', 'admin', '操作投票管理', 'super', '1610977814', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('234', 'admin', '操作友情链接', 'super', '1610977815', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('235', 'admin', '操作模板管理', 'super', '1610977815', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('236', 'admin', '操作数据还原', 'super', '1610977816', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('237', '游客', 'admin登录成功', '', '1610983666', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('238', '游客', 'admin登录成功', '', '1611023007', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('239', 'admin', '操作扩展字段', 'super', '1611023013', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('240', 'admin', '操作APK配置', 'super', '1611024140', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('241', 'admin', '操作VIP账户配置', 'super', '1611024505', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('242', 'admin', '操作APK配置', 'super', '1611024511', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('243', 'admin', '操作VIP账户配置', 'super', '1611024519', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('244', 'admin', '操作APK配置', 'super', '1611024522', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('245', 'admin', '操作VIP账户配置', 'super', '1611024522', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('246', 'admin', '操作VIP账户配置', 'super', '1611024523', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('247', 'admin', '操作APK配置', 'super', '1611024523', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('248', 'admin', '操作APK配置', 'super', '1611024524', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('249', 'admin', '操作VIP账户配置', 'super', '1611024653', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('250', 'admin', '操作APK配置', 'super', '1611024657', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('251', 'admin', '操作VIP账户配置', 'super', '1611025105', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('252', 'admin', '操作APK配置', 'super', '1611025110', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('253', 'admin', '操作VIP账户配置', 'super', '1611025639', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('254', 'admin', '操作APK配置', 'super', '1611025639', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('255', 'admin', '操作APK配置', 'super', '1611025640', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('256', 'admin', '操作VIP账户配置', 'super', '1611025861', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('257', 'admin', '操作APK配置', 'super', '1611025863', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('258', '游客', 'admin登录成功', '', '1611029934', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('259', 'admin', '操作扩展字段', 'super', '1611029942', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('260', 'admin', '操作支付宝配置', 'super', '1611029947', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('261', 'admin', '操作会员系统', 'super', '1611030391', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('262', 'admin', '操作支付宝配置', 'super', '1611030395', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('263', 'admin', '操作会员系统', 'super', '1611030398', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('264', 'admin', '操作支付宝配置', 'super', '1611030489', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('265', '游客', 'admin登录成功', '', '1611040593', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('266', 'admin', '操作会员系统', 'super', '1611040599', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('267', 'admin', '操作支付宝配置', 'super', '1611040804', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('268', 'admin', '操作QQ快捷登陆设置', 'super', '1611040806', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('269', 'admin', '操作会员系统', 'super', '1611040808', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('270', 'admin', '操作支付宝配置', 'super', '1611040810', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('271', 'admin', '操作QQ快捷登陆设置', 'super', '1611040810', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('272', 'admin', '操作会员系统', 'super', '1611040811', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('273', 'admin', '操作支付宝配置', 'super', '1611041083', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('274', 'admin', '操作会员系统', 'super', '1611041085', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('275', '游客', 'admin登录成功', '', '1611041632', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('276', 'admin', '操作扩展字段', 'super', '1611041644', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('277', 'admin', '操作会员系统', 'super', '1611041648', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('278', 'admin', '操作支付宝配置', 'super', '1611043480', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('279', 'admin', '操作会员系统', 'super', '1611043482', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('280', 'admin', '操作QQ快捷登陆设置', 'super', '1611043485', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('281', 'admin', '操作会员系统', 'super', '1611043546', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('282', 'admin', '操作QQ快捷登陆设置', 'super', '1611043667', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('283', 'admin', '操作会员系统', 'super', '1611043719', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('284', 'admin', '操作订单管理', 'super', '1611043755', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('285', 'admin', '操作会员系统', 'super', '1611043867', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('286', 'admin', '操作会员管理', 'super', '1611043873', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('287', 'admin', '操作会员系统', 'super', '1611043947', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('288', 'admin', '操作会员管理', 'super', '1611043969', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('289', 'admin', '操作会员系统', 'super', '1611043987', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('290', 'admin', '操作会员管理', 'super', '1611044037', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('291', 'admin', '操作会员系统', 'super', '1611044092', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('292', 'admin', '操作会员管理', 'super', '1611044146', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('293', 'admin', '操作会员系统', 'super', '1611044163', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('294', 'admin', '操作会员管理', 'super', '1611045204', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('295', 'admin', '操作会员系统', 'super', '1611045206', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('296', 'admin', '操作会员管理', 'super', '1611045253', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('297', 'admin', '操作会员系统', 'super', '1611045256', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('298', 'admin', '操作会员管理', 'super', '1611045375', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('299', 'admin', '操作会员系统', 'super', '1611045377', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('300', 'admin', '操作会员管理', 'super', '1611045742', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('301', 'admin', '操作会员系统', 'super', '1611045744', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('302', 'admin', '操作会员管理', 'super', '1611045769', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('303', 'admin', '操作会员系统', 'super', '1611045778', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('304', 'admin', '操作会员管理', 'super', '1611045799', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('305', 'admin', '操作会员系统', 'super', '1611045811', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('306', 'admin', '操作会员管理', 'super', '1611045968', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('307', 'admin', '操作会员系统', 'super', '1611045970', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('308', 'admin', '操作会员管理', 'super', '1611046149', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('309', 'admin', '操作会员系统', 'super', '1611046154', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('310', 'admin', '操作用户提现', 'super', '1611046165', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('311', 'admin', '操作会员系统', 'super', '1611046167', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('312', 'admin', '操作会员管理', 'super', '1611046168', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('313', 'admin', '操作会员系统', 'super', '1611046185', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('314', 'admin', '操作会员管理', 'super', '1611046268', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('315', 'admin', '操作会员系统', 'super', '1611046273', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('316', 'admin', '操作会员管理', 'super', '1611046437', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('317', 'admin', '操作会员系统', 'super', '1611046861', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('318', 'admin', '操作会员管理', 'super', '1611046914', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('319', 'admin', '操作会员系统', 'super', '1611046916', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('320', 'admin', '操作会员管理', 'super', '1611047342', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('321', 'admin', '操作会员系统', 'super', '1611047344', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('322', 'admin', '操作会员管理', 'super', '1611047638', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('323', 'admin', '操作会员系统', 'super', '1611047640', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('324', 'admin', '操作会员管理', 'super', '1611047707', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('325', 'admin', '操作会员系统', 'super', '1611047709', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('326', 'admin', '操作会员管理', 'super', '1611047723', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('327', 'admin', '操作会员系统', 'super', '1611047723', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('328', 'admin', '操作订单管理', 'super', '1611047724', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('329', 'admin', '操作QQ快捷登陆设置', 'super', '1611047725', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('330', 'admin', '操作订单管理', 'super', '1611047726', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('331', 'admin', '操作用户提现', 'super', '1611047782', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('332', 'admin', '操作会员管理', 'super', '1611047783', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('333', 'admin', '操作会员系统', 'super', '1611047784', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('334', 'admin', '操作订单管理', 'super', '1611047785', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('335', '游客', 'admin登录成功', '', '1611070836', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('336', 'admin', '操作会员系统', 'super', '1611070840', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('337', 'admin', '操作支付宝配置', 'super', '1611070842', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('338', 'admin', '操作订单管理', 'super', '1611070843', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('339', 'admin', '操作会员系统', 'super', '1611070843', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('340', 'admin', '操作会员系统', 'super', '1611070844', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('341', 'admin', '操作订单管理', 'super', '1611070845', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('342', 'admin', '操作会员系统', 'super', '1611070846', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('343', 'admin', '操作订单管理', 'super', '1611070846', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('344', 'admin', '操作订单管理', 'super', '1611070846', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('345', 'admin', '操作订单管理', 'super', '1611070846', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('346', 'admin', '操作订单管理', 'super', '1611070846', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('347', 'admin', '操作订单管理', 'super', '1611070846', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('348', 'admin', '操作订单管理', 'super', '1611070847', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('349', 'admin', '操作会员系统', 'super', '1611070847', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('350', 'admin', '操作会员系统', 'super', '1611070847', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('351', 'admin', '操作会员系统', 'super', '1611070847', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('352', 'admin', '操作会员系统', 'super', '1611070847', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('353', 'admin', '操作会员系统', 'super', '1611070848', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('354', 'admin', '操作订单管理', 'super', '1611070848', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('355', 'admin', '操作订单管理', 'super', '1611070848', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('356', 'admin', '操作订单管理', 'super', '1611070848', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('357', 'admin', '操作订单管理', 'super', '1611070848', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('358', 'admin', '操作订单管理', 'super', '1611070848', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('359', 'admin', '操作订单管理', 'super', '1611070904', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('360', 'admin', '操作会员系统', 'super', '1611071103', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('361', 'admin', '操作订单管理', 'super', '1611071138', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('362', 'admin', '操作会员系统', 'super', '1611071139', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('363', 'admin', '操作会员系统', 'super', '1611071139', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('364', 'admin', '操作订单管理', 'super', '1611071151', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('365', 'admin', '操作会员系统', 'super', '1611071151', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('366', 'admin', '操作会员系统', 'super', '1611071151', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('367', 'admin', '操作会员系统', 'super', '1611071152', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('368', 'admin', '操作会员系统', 'super', '1611071152', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('369', '游客', 'admin登录成功', '', '1611071221', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('370', 'admin', '操作支付宝配置', 'super', '1611072202', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('371', 'admin', '操作QQ快捷登陆设置', 'super', '1611072205', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('372', 'admin', '操作会员管理', 'super', '1611072211', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('373', 'admin', '操作会员系统', 'super', '1611072214', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('374', 'admin', '操作用户提现', 'super', '1611072217', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('375', 'admin', '操作会员系统', 'super', '1611072220', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('376', 'admin', '操作会员管理', 'super', '1611072223', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('377', 'admin', '操作订单管理', 'super', '1611072225', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('378', 'admin', '操作会员系统', 'super', '1611072233', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('379', 'admin', '操作订单管理', 'super', '1611072336', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('380', 'admin', '操作会员系统', 'super', '1611072338', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('381', 'admin', '操作订单管理', 'super', '1611073058', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('382', 'admin', '操作会员系统', 'super', '1611073060', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('383', 'admin', '操作会员管理', 'super', '1611073353', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('384', 'admin', '操作会员系统', 'super', '1611073354', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('385', 'admin', '操作订单管理', 'super', '1611073354', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('386', 'admin', '操作支付宝配置', 'super', '1611073355', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('387', 'admin', '操作QQ快捷登陆设置', 'super', '1611073356', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('388', 'admin', '操作订单管理', 'super', '1611073356', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('389', 'admin', '操作会员系统', 'super', '1611073357', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('390', 'admin', '操作订单管理', 'super', '1611073358', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('391', 'admin', '操作会员系统', 'super', '1611073360', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('392', 'admin', '操作订单管理', 'super', '1611073465', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('393', 'admin', '操作会员系统', 'super', '1611073475', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('394', 'admin', '操作订单管理', 'super', '1611073479', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('395', 'admin', '操作会员系统', 'super', '1611073482', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('396', 'admin', '操作扩展字段', 'super', '1611073609', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('397', 'admin', '操作附件清理', 'super', '1611073654', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('398', 'admin', '操作单页标签', 'super', '1611073656', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('399', 'admin', '操作幻灯管理', 'super', '1611073656', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('400', 'admin', '操作网站配置', 'super', '1611073657', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('401', 'admin', '操作附件清理', 'super', '1611073657', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('402', 'admin', '操作会员系统', 'super', '1611073661', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('403', 'admin', '操作订单管理', 'super', '1611073663', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('404', 'admin', '操作QQ快捷登陆设置', 'super', '1611073665', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('405', 'admin', '操作支付宝配置', 'super', '1611073666', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('406', 'admin', '操作会员系统', 'super', '1611073667', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('407', 'admin', '操作会员管理', 'super', '1611073667', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('408', 'admin', '操作APK配置', 'super', '1611073670', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('409', 'admin', '操作VIP账户配置', 'super', '1611073675', '成功', '127.0.0.1');
INSERT INTO `dami_log` VALUES ('410', 'admin', '操作APK配置', 'super', '1611073676', '成功', '127.0.0.1');

-- ----------------------------
-- Table structure for `dami_member`
-- ----------------------------
DROP TABLE IF EXISTS `dami_member`;
CREATE TABLE `dami_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `userpwd` varchar(60) NOT NULL,
  `realname` varchar(60) DEFAULT NULL,
  `tel` varchar(60) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `qq` varchar(30) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `area` varchar(60) DEFAULT NULL,
  `birthday` varchar(30) DEFAULT NULL,
  `zhiwu` varchar(60) DEFAULT NULL,
  `xuexing` varchar(20) DEFAULT NULL,
  `descript` text,
  `money` float(11,2) DEFAULT '0.00' COMMENT '余额',
  `score` int(11) DEFAULT '0' COMMENT '积分',
  `is_lock` int(11) DEFAULT '0' COMMENT '是否锁定',
  `addtime` int(11) DEFAULT NULL,
  `icon` varchar(300) DEFAULT NULL,
  `qid` varchar(300) DEFAULT NULL,
  `wx_openid` varchar(300) DEFAULT NULL,
  `last_uptime` int(11) DEFAULT NULL,
  `online` int(1) DEFAULT '0',
  `channel_id` varchar(100) DEFAULT NULL COMMENT '百度云管道id',
  `channel_uid` varchar(100) DEFAULT NULL COMMENT '百度云管道UID',
  `group_id` int(11) DEFAULT '0' COMMENT '用户所在组',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_member
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_member_group`
-- ----------------------------
DROP TABLE IF EXISTS `dami_member_group`;
CREATE TABLE `dami_member_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(80) DEFAULT NULL,
  `group_vail` text,
  `descript` text,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_member_group
-- ----------------------------
INSERT INTO `dami_member_group` VALUES ('2', '超级会员', '18,19,20,21', '');
INSERT INTO `dami_member_group` VALUES ('3', '游客', '18,19,20,21', '访客权限');
INSERT INTO `dami_member_group` VALUES ('4', '普通会员组', '14,15,16,17,35', '');

-- ----------------------------
-- Table structure for `dami_member_menu`
-- ----------------------------
DROP TABLE IF EXISTS `dami_member_menu`;
CREATE TABLE `dami_member_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `is_show` int(11) DEFAULT '1' COMMENT '是否显示',
  `drand` int(11) DEFAULT '500' COMMENT '排序',
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_member_menu
-- ----------------------------
INSERT INTO `dami_member_menu` VALUES ('1', '会员资料', '/index.php/Member/main', '1', '500');
INSERT INTO `dami_member_menu` VALUES ('2', '修改密码', '/index.php/Member/changepwd', '1', '500');
INSERT INTO `dami_member_menu` VALUES ('3', '我的投稿', '/index.php/Member/tougaolist', '1', '500');
INSERT INTO `dami_member_menu` VALUES ('4', '我的订单', '/index.php/Member/buylist', '1', '500');
INSERT INTO `dami_member_menu` VALUES ('5', '在线充值', '/index.php/Member/chongzhi', '1', '500');
INSERT INTO `dami_member_menu` VALUES ('6', '我的收藏夹', '/index.php/Member/fav', '1', '500');

-- ----------------------------
-- Table structure for `dami_member_trade`
-- ----------------------------
DROP TABLE IF EXISTS `dami_member_trade`;
CREATE TABLE `dami_member_trade` (
  `buy_id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `uid` int(11) DEFAULT '0' COMMENT '购买者ID',
  `price` float(11,2) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `area` varchar(60) DEFAULT NULL,
  `sh_name` varchar(60) DEFAULT NULL,
  `sh_tel` varchar(60) DEFAULT NULL,
  `group_trade_no` varchar(200) DEFAULT NULL,
  `out_trade_no` varchar(200) NOT NULL COMMENT '订单号',
  `servial` varchar(200) DEFAULT NULL COMMENT '产品型号',
  `status` int(11) DEFAULT '-1' COMMENT '0=>''等待付款'',1=>''已付款，等待发货'',2=>''已收货，交易完成'',3=>''换货处理中'',4=>''换货完成'',5=>''退货处理中'',6=>''退货完成''',
  `remark` text,
  `num` int(11) DEFAULT '1' COMMENT '购买数量',
  `trade_type` int(11) DEFAULT '0' COMMENT '交易方式',
  `addtime` int(11) DEFAULT NULL,
  `goods_title` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`buy_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_member_trade
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_money_log`
-- ----------------------------
DROP TABLE IF EXISTS `dami_money_log`;
CREATE TABLE `dami_money_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `price` float(11,2) DEFAULT NULL,
  `log_type` int(11) DEFAULT '0' COMMENT '0：收入 1:支出',
  `remark` text,
  `addtime` int(11) DEFAULT NULL,
  `trade_no` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_money_log
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_mood`
-- ----------------------------
DROP TABLE IF EXISTS `dami_mood`;
CREATE TABLE `dami_mood` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mood1` mediumint(8) unsigned DEFAULT '0',
  `mood2` mediumint(8) unsigned DEFAULT '0',
  `mood3` mediumint(8) unsigned DEFAULT '0',
  `mood4` mediumint(8) unsigned DEFAULT '0',
  `mood5` mediumint(8) unsigned DEFAULT '0',
  `mood6` mediumint(8) unsigned DEFAULT '0',
  `mood7` mediumint(8) unsigned DEFAULT '0',
  `mood8` mediumint(8) unsigned DEFAULT '0',
  `aid` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_mood
-- ----------------------------
INSERT INTO `dami_mood` VALUES ('1', '5', '0', '1', '0', '2', '0', '0', '0', '42');
INSERT INTO `dami_mood` VALUES ('2', '0', '0', '0', '1', '2', '0', '0', '0', '47');
INSERT INTO `dami_mood` VALUES ('3', '0', '0', '0', '1', '2', '0', '0', '0', '55');
INSERT INTO `dami_mood` VALUES ('4', '0', '0', '0', '0', '1', '1', '0', '0', '10');
INSERT INTO `dami_mood` VALUES ('5', '0', '0', '0', '0', '2', '0', '0', '0', '36');
INSERT INTO `dami_mood` VALUES ('6', '0', '0', '1', '1', '2', '1', '1', '0', '53');
INSERT INTO `dami_mood` VALUES ('7', '0', '0', '0', '0', '1', '0', '0', '0', '16');
INSERT INTO `dami_mood` VALUES ('8', '0', '0', '0', '0', '0', '1', '0', '0', '17');
INSERT INTO `dami_mood` VALUES ('9', '0', '0', '0', '0', '0', '1', '0', '0', '20');
INSERT INTO `dami_mood` VALUES ('10', '0', '0', '0', '0', '0', '0', '0', '0', '52');
INSERT INTO `dami_mood` VALUES ('11', '0', '0', '0', '0', '1', '0', '0', '0', '40');
INSERT INTO `dami_mood` VALUES ('12', '0', '0', '0', '1', '1', '0', '0', '0', '22');
INSERT INTO `dami_mood` VALUES ('13', '0', '0', '0', '0', '0', '0', '0', '0', '34');
INSERT INTO `dami_mood` VALUES ('14', '0', '0', '0', '1', '0', '0', '0', '0', '29');
INSERT INTO `dami_mood` VALUES ('15', '0', '0', '0', '0', '0', '0', '0', '0', '35');
INSERT INTO `dami_mood` VALUES ('16', '0', '0', '0', '0', '0', '0', '0', '0', '5');
INSERT INTO `dami_mood` VALUES ('17', '0', '0', '0', '0', '0', '0', '0', '0', '44');
INSERT INTO `dami_mood` VALUES ('18', '0', '0', '0', '0', '0', '0', '0', '0', '21');
INSERT INTO `dami_mood` VALUES ('19', '0', '0', '0', '1', '0', '0', '0', '0', '48');
INSERT INTO `dami_mood` VALUES ('20', '0', '0', '0', '0', '0', '0', '0', '0', '59');
INSERT INTO `dami_mood` VALUES ('21', '0', '0', '0', '0', '0', '0', '0', '0', '61');
INSERT INTO `dami_mood` VALUES ('22', '0', '0', '0', '0', '0', '0', '0', '0', '1');
INSERT INTO `dami_mood` VALUES ('31', '0', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `dami_mood` VALUES ('24', '0', '0', '0', '0', '0', '0', '0', '0', '39');
INSERT INTO `dami_mood` VALUES ('25', '0', '0', '0', '0', '0', '0', '0', '0', '63');
INSERT INTO `dami_mood` VALUES ('26', '0', '0', '0', '0', '0', '0', '0', '0', '64');
INSERT INTO `dami_mood` VALUES ('27', '0', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `dami_mood` VALUES ('28', '0', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `dami_mood` VALUES ('29', '0', '0', '0', '0', '0', '0', '0', '0', '65');
INSERT INTO `dami_mood` VALUES ('32', '0', '0', '0', '0', '0', '0', '0', '0', '46');
INSERT INTO `dami_mood` VALUES ('33', '0', '0', '0', '1', '0', '0', '0', '0', '38');
INSERT INTO `dami_mood` VALUES ('34', '0', '0', '0', '0', '0', '0', '0', '0', '19');
INSERT INTO `dami_mood` VALUES ('35', '0', '0', '0', '0', '0', '0', '0', '0', '54');
INSERT INTO `dami_mood` VALUES ('36', '0', '0', '0', '0', '0', '0', '0', '0', '27');
INSERT INTO `dami_mood` VALUES ('37', '0', '0', '0', '0', '0', '0', '0', '0', '25');
INSERT INTO `dami_mood` VALUES ('38', '0', '0', '0', '0', '0', '0', '0', '0', '28');
INSERT INTO `dami_mood` VALUES ('39', '0', '0', '0', '1', '0', '0', '0', '0', '45');
INSERT INTO `dami_mood` VALUES ('40', '0', '0', '0', '0', '0', '0', '0', '0', '51');
INSERT INTO `dami_mood` VALUES ('41', '0', '0', '0', '0', '0', '0', '0', '0', '58');
INSERT INTO `dami_mood` VALUES ('42', '0', '0', '0', '0', '0', '0', '0', '0', '68');
INSERT INTO `dami_mood` VALUES ('43', '0', '0', '0', '0', '0', '0', '0', '0', '71');
INSERT INTO `dami_mood` VALUES ('44', '0', '0', '0', '0', '0', '0', '0', '0', '56');
INSERT INTO `dami_mood` VALUES ('45', '0', '0', '0', '0', '0', '0', '0', '0', '60');
INSERT INTO `dami_mood` VALUES ('46', '0', '0', '0', '0', '0', '0', '0', '0', '73');
INSERT INTO `dami_mood` VALUES ('47', '0', '0', '0', '0', '0', '0', '0', '0', '57');
INSERT INTO `dami_mood` VALUES ('48', '0', '0', '0', '0', '0', '0', '0', '0', '69');
INSERT INTO `dami_mood` VALUES ('49', '0', '0', '0', '0', '0', '0', '0', '0', '72');
INSERT INTO `dami_mood` VALUES ('50', '0', '0', '0', '0', '0', '0', '0', '0', '127');
INSERT INTO `dami_mood` VALUES ('51', '0', '0', '0', '0', '0', '0', '0', '0', '70');
INSERT INTO `dami_mood` VALUES ('52', '0', '0', '0', '0', '0', '0', '0', '0', '62');
INSERT INTO `dami_mood` VALUES ('53', '0', '0', '0', '0', '0', '0', '0', '0', '126');

-- ----------------------------
-- Table structure for `dami_node`
-- ----------------------------
DROP TABLE IF EXISTS `dami_node`;
CREATE TABLE `dami_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `title` varchar(100) NOT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `menu_pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `pid` smallint(6) DEFAULT '0',
  `ismenu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `level` (`level`),
  KEY `pid` (`menu_pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_node
-- ----------------------------
INSERT INTO `dami_node` VALUES ('1', 'core', null, '1', '系统核心', null, '37', '0', '37', '1');
INSERT INTO `dami_node` VALUES ('2', 'Fields', null, '1', '扩展字段', null, '1', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('3', 'Type', null, '1', '栏目管理', null, '1', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('4', 'Article', null, '1', '内容管理', null, '1', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('5', 'BaseM', null, '1', '基本管理', null, '37', '0', '37', '1');
INSERT INTO `dami_node` VALUES ('6', 'Config', null, '1', '网站配置', null, '5', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('7', 'Admin', null, '1', '管理员管理', null, '5', '2', '37', '0');
INSERT INTO `dami_node` VALUES ('8', 'Flash', null, '1', '幻灯管理', null, '5', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('9', 'Clear', null, '1', '附件清理', null, '5', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('10', 'Label', null, '1', '单页标签', null, '5', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('11', 'Ad', null, '1', '广告管理', null, '5', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('12', 'clearcache', null, '1', '清理缓存', null, '9', '0', '9', '0');
INSERT INTO `dami_node` VALUES ('13', 'plus', null, '1', '插件工具', null, '37', '0', '37', '0');
INSERT INTO `dami_node` VALUES ('16', 'Guestbook', null, '1', '留言管理', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('17', 'Pl', null, '1', '评论管理', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('18', 'Link', null, '1', '友情链接', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('19', 'Vote', null, '1', '投票管理', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('20', 'Key', null, '1', '文章内链', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('21', 'Tpl', null, '1', '模板管理', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('22', 'Backup', null, '1', '数据工具', null, '13', '2', '37', '0');
INSERT INTO `dami_node` VALUES ('23', 'index', null, '1', '数据备份', null, '22', '3', '22', '1');
INSERT INTO `dami_node` VALUES ('24', 'restore', null, '1', '数据还原', null, '22', '3', '22', '1');
INSERT INTO `dami_node` VALUES ('25', 'Apk', null, '1', 'APK配置', null, '37', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('26', 'Caiji', null, '1', '数据采集', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('27', 'Build', null, '1', '生成静态页', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('28', 'Mtable', null, '1', '万能表格', null, '13', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('29', 'config', null, '1', 'APK基础配置', null, '25', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('30', 'vip_config', null, '1', 'VIP账户配置', null, '25', '3', '25', '1');
INSERT INTO `dami_node` VALUES ('31', 'Member', null, '1', '会员系统', null, '37', '2', '37', '1');
INSERT INTO `dami_node` VALUES ('32', 'ap_set', null, '1', '支付宝配置', null, '31', '3', '31', '1');
INSERT INTO `dami_node` VALUES ('33', 'qq_set', null, '1', 'QQ快捷登陆设置', null, '31', '3', '31', '1');
INSERT INTO `dami_node` VALUES ('34', 'cartlist', null, '1', '订单管理', null, '31', '3', '31', '1');
INSERT INTO `dami_node` VALUES ('35', 'userlist', null, '1', '会员管理', null, '31', '3', '31', '1');
INSERT INTO `dami_node` VALUES ('36', 'tixianlist', null, '1', '用户提现', null, '31', '3', '31', '1');
INSERT INTO `dami_node` VALUES ('37', 'Admin', null, '1', '系统后台', null, '0', '1', '0', '0');
INSERT INTO `dami_node` VALUES ('38', 'testmodule', null, '1', '测试模块', null, '1', '2', '1', '1');

-- ----------------------------
-- Table structure for `dami_pl`
-- ----------------------------
DROP TABLE IF EXISTS `dami_pl`;
CREATE TABLE `dami_pl` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `aid` mediumint(5) unsigned NOT NULL,
  `author` varchar(40) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `ptime` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `recontent` text,
  `status` tinyint(1) DEFAULT '1',
  `pl_uid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_pl
-- ----------------------------
INSERT INTO `dami_pl` VALUES ('18', '127', 'shadowgirl', '127.0.0.1', '2021-01-14 00:37:22', '测试评价hahaha', null, '1', '10');

-- ----------------------------
-- Table structure for `dami_role`
-- ----------------------------
DROP TABLE IF EXISTS `dami_role`;
CREATE TABLE `dami_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `pid` smallint(6) DEFAULT '0',
  `status` tinyint(1) unsigned DEFAULT '1',
  `remark` varchar(255) DEFAULT NULL,
  `typeids` text,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_role
-- ----------------------------
INSERT INTO `dami_role` VALUES ('1', 'super', '0', '1', '超级管理员', null);
INSERT INTO `dami_role` VALUES ('4', '测试组', '0', '1', '测试下', '18,19,20,22,23,24,28,33');
INSERT INTO `dami_role` VALUES ('5', '销售', '0', '1', '', '14,15,16,17,35');

-- ----------------------------
-- Table structure for `dami_role_admin`
-- ----------------------------
DROP TABLE IF EXISTS `dami_role_admin`;
CREATE TABLE `dami_role_admin` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_role_admin
-- ----------------------------
INSERT INTO `dami_role_admin` VALUES ('1', '1');

-- ----------------------------
-- Table structure for `dami_tag`
-- ----------------------------
DROP TABLE IF EXISTS `dami_tag`;
CREATE TABLE `dami_tag` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(60) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `typeid` int(11) DEFAULT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_tag
-- ----------------------------
INSERT INTO `dami_tag` VALUES ('1', '55656', '1', '19');

-- ----------------------------
-- Table structure for `dami_tixian`
-- ----------------------------
DROP TABLE IF EXISTS `dami_tixian`;
CREATE TABLE `dami_tixian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `money` float(11,2) DEFAULT '0.00',
  `uid` int(11) DEFAULT NULL,
  `remark` text,
  `status` int(11) DEFAULT '0' COMMENT '提现状态0:待处理1:已处理',
  `addtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_tixian
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_trade_log`
-- ----------------------------
DROP TABLE IF EXISTS `dami_trade_log`;
CREATE TABLE `dami_trade_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taobao_no` varchar(200) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `money` float(11,2) DEFAULT NULL,
  `trade_no` varchar(200) DEFAULT NULL,
  `uid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_trade_log
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_type`
-- ----------------------------
DROP TABLE IF EXISTS `dami_type`;
CREATE TABLE `dami_type` (
  `typeid` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `typename` varchar(20) NOT NULL,
  `keywords` char(40) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `isindex` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `indexnum` tinyint(2) unsigned NOT NULL,
  `pernum` tinyint(1) DEFAULT '15',
  `islink` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `url` char(255) NOT NULL,
  `isuser` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `target` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `readme` varchar(255) NOT NULL,
  `drank` mediumint(5) unsigned NOT NULL,
  `irank` mediumint(5) NOT NULL,
  `fid` mediumint(5) unsigned NOT NULL,
  `path` varchar(128) NOT NULL,
  `show_fields` varchar(120) DEFAULT NULL,
  `list_path` varchar(250) DEFAULT 'list/list_default.html',
  `page_path` varchar(255) DEFAULT 'page/page_default.html',
  `icon` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`typeid`),
  KEY `typename` (`typename`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_type
-- ----------------------------
INSERT INTO `dami_type` VALUES ('14', '关于我们', '', '', '1', '0', '10', '15', '0', '/index.php?s=lists/15', '0', '1', '', '1', '10', '0', '0', '1|1|1|1|1|1|1|1|1|1|1|1|1|1|1|1', 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('15', '企业概况', '', '', '0', '0', '10', '15', '0', '', '0', '1', '', '10', '10', '14', '0-14', '1|0|0|0|0|0|0|0|0|0|1|1|0|0|0|0', 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('16', '企业文化', '', '', '0', '0', '10', '15', '0', '', '0', '1', '', '10', '10', '14', '0-14', '1|0|0|0|0|0|0|0|0|0|1|1|0|0|0|0', 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('17', '企业荣誉', '', '', '0', '0', '10', '15', '0', '', '0', '1', '', '10', '10', '14', '0-14', '1|0|0|0|0|0|0|0|0|0|1|1|0|0|0|0', 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('18', '新闻中心', '', '', '1', '1', '10', '15', '0', '', '1', '1', '', '2', '10', '0', '0', null, 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('19', '行业新闻', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '18', '0-18', '1|1|0|1|1|1|0|0|0|0|1|1|1|1|0|0', 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('20', '公司新闻', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '18', '0-18', '1|0|0|1|1|1|0|0|0|0|1|1|1|1|0|0', 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('21', '公司公告', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '18', '0-18', '1|0|0|0|0|0|0|0|0|0|1|1|1|0|0|0', 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('22', '产品展示', '', '', '1', '1', '10', '15', '0', '', '1', '1', '', '3', '10', '0', '0', null, 'list/list_product.html', 'page/page_product.html', null);
INSERT INTO `dami_type` VALUES ('23', '移动互联网开发', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '22', '0-22', '1|1|1|0|0|1|0|0|1|0|0|1|0|1|0|0', 'list/list_product.html', 'page/page_product.html', null);
INSERT INTO `dami_type` VALUES ('24', 'JAVA软件开发', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '22', '0-22', '1|1|1|0|0|1|0|0|1|0|0|1|0|1|0|0', 'list/list_product.html', 'page/page_product.html', null);
INSERT INTO `dami_type` VALUES ('25', '招聘信息', '', '', '1', '1', '10', '15', '0', '', '1', '1', '', '5', '10', '0', '0', '1|0|1|0|0|0|0|0|0|0|1|1|1|1|0|0', 'list/list_zhaoping.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('26', '在线留言', '', '', '1', '1', '10', '15', '1', '/index.php?s=guestbook', '1', '1', '', '10', '10', '0', '0', null, 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('27', '工程案例', '', '', '1', '1', '10', '15', '0', '', '1', '1', '', '4', '10', '0', '0', null, 'list/list_product.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('28', '网站案例', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '27', '0-27', null, 'list/list_product.html', 'page/page_anli.html', null);
INSERT INTO `dami_type` VALUES ('33', '安卓开发', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '27', '0-27', null, 'list/list_product.html', 'page/page_anli.html', null);
INSERT INTO `dami_type` VALUES ('35', '联系我们', '', '', '0', '0', '10', '15', '0', '', '0', '1', '', '10', '10', '14', '0-14', '1|0|0|0|0|1|0|0|0|0|0|1|0|0|0|0', 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('34', 'B/S软件', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '27', '0-27', '1|1|1|1|1|1|0|0|1|0|1|1|1|1|1|0', 'list/list_product.html', 'page/page_anli.html', null);
INSERT INTO `dami_type` VALUES ('37', '单页管理', '', '', '0', '0', '10', '15', '0', '', '0', '1', '', '10', '10', '0', '0', null, 'list/list_default.html', 'page/page_default.html', null);
INSERT INTO `dami_type` VALUES ('38', '网站建设', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '23', '0-22-23', '1|1|1|0|0|1|0|0|1|0|0|1|0|1|0|0', 'list/list_product.html', 'page/page_product.html', null);
INSERT INTO `dami_type` VALUES ('39', '手机微应用', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '23', '0-22-23', '1|1|1|0|0|1|0|0|1|0|0|1|0|1|0|0', 'list/list_product.html', 'page/page_product.html', null);
INSERT INTO `dami_type` VALUES ('40', '安卓开发', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '23', '0-22-23', '1|1|1|0|0|1|0|0|1|0|0|1|0|1|0|0', 'list/list_product.html', 'page/page_product.html', null);
INSERT INTO `dami_type` VALUES ('41', '苹果开发', '', '', '0', '1', '10', '15', '0', '', '1', '1', '', '10', '10', '23', '0-22-23', '1|1|1|0|0|1|0|0|1|0|0|1|0|1|0|0', 'list/list_product.html', 'page/page_product.html', null);

-- ----------------------------
-- Table structure for `dami_vip_mess`
-- ----------------------------
DROP TABLE IF EXISTS `dami_vip_mess`;
CREATE TABLE `dami_vip_mess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vip_name` varchar(60) DEFAULT NULL,
  `vip_pwd` varchar(200) DEFAULT NULL,
  `vip_apk_url` varchar(200) DEFAULT NULL,
  `com_apk_url` varchar(200) DEFAULT NULL,
  `vip_sn` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_vip_mess
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_vote`
-- ----------------------------
DROP TABLE IF EXISTS `dami_vote`;
CREATE TABLE `dami_vote` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL,
  `vote` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `stype` tinyint(1) unsigned NOT NULL,
  `starttime` varchar(32) NOT NULL,
  `overtime` varchar(32) NOT NULL,
  `rank` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_vote
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_wx_menu`
-- ----------------------------
DROP TABLE IF EXISTS `dami_wx_menu`;
CREATE TABLE `dami_wx_menu` (
  `id` int(11) DEFAULT NULL,
  `menu_name` varchar(60) DEFAULT NULL,
  `menu_value` varchar(300) DEFAULT NULL,
  `menu_type` int(1) DEFAULT '0' COMMENT '0：网页链接1:推送图文',
  `pid` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_wx_menu
-- ----------------------------
INSERT INTO `dami_wx_menu` VALUES ('1', '新闻', '/index.php/lists/18.html', '0', '0');
INSERT INTO `dami_wx_menu` VALUES ('2', '产品', '', '2', '0');
INSERT INTO `dami_wx_menu` VALUES ('4', '大米CMS', '大米cms', '1', '2');
INSERT INTO `dami_wx_menu` VALUES ('5', '移动APP开发', '/index.php/lists/23.html', '0', '2');
INSERT INTO `dami_wx_menu` VALUES ('3', '关于我们', '', '2', '0');
INSERT INTO `dami_wx_menu` VALUES ('6', '企业概况', '/index.php/lists/15.html', '0', '3');
INSERT INTO `dami_wx_menu` VALUES ('7', '企业荣誉', '/index.php/lists/17.html', '0', '3');
INSERT INTO `dami_wx_menu` VALUES ('8', '联系我们', '/index.php/lists/35.html', '0', '3');
INSERT INTO `dami_wx_menu` VALUES ('9', '安卓开发', '/index.php/lists/24.html', '0', '2');
INSERT INTO `dami_wx_menu` VALUES ('10', '绑定微信', '绑定微信', '1', '3');

-- ----------------------------
-- Table structure for `dami_wx_prize`
-- ----------------------------
DROP TABLE IF EXISTS `dami_wx_prize`;
CREATE TABLE `dami_wx_prize` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prize_name` varchar(120) DEFAULT NULL COMMENT '抽奖名称',
  `prize_project` text COMMENT '奖项目',
  `start_time` int(11) DEFAULT NULL COMMENT '抽奖开始日期',
  `end_time` int(11) DEFAULT NULL COMMENT '抽奖结束日期',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dami_wx_prize
-- ----------------------------

-- ----------------------------
-- Table structure for `dami_wx_prize_user`
-- ----------------------------
DROP TABLE IF EXISTS `dami_wx_prize_user`;
CREATE TABLE `dami_wx_prize_user` (
  `id` int(11) NOT NULL DEFAULT '0',
  `prize_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MRG_MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dami_wx_prize_user
-- ----------------------------

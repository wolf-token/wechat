<?php 

global $_W;

$sql = "
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ims_move_activity
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_activity`;
CREATE TABLE `ims_move_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '活动的id',
  `name` varchar(255) DEFAULT NULL COMMENT '活动主题',
  `picture` varchar(255) DEFAULT NULL COMMENT '展示图',
  `content` text COMMENT '活动内容',
  `status` int(11) DEFAULT '0' COMMENT '状态： 0 启用 1 拉黑',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='活动表';

-- ----------------------------
-- Table structure for ims_move_backup
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_backup`;
CREATE TABLE `ims_move_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '备份的id',
  `name` varchar(255) DEFAULT NULL COMMENT '备份姓名',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `phone` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `accout` varchar(255) DEFAULT NULL COMMENT '宽带账号',
  `uid` int(11) DEFAULT NULL COMMENT '微信用户的uid',
  `time` int(11) DEFAULT NULL COMMENT '时间',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户的openid',
  `avatar` varchar(255) DEFAULT NULL COMMENT '微信头像',
  `code` varchar(255) DEFAULT NULL COMMENT '二维码',
  `niackname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `pid` int(11) DEFAULT NULL COMMENT '扫码人的id 0为没有人推荐',
  `type` int(11) DEFAULT NULL COMMENT '扫码的类型： 2为扫员工码 1 为扫客户码 3自己关注',
  `math` int(11) DEFAULT '0' COMMENT '推广的人数',
  `plot` varchar(255) DEFAULT NULL COMMENT '小区名称',
  `satatus` int(11) DEFAULT '1' COMMENT '状态： 1 已完成注册  2 删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户信息备份表';

-- ----------------------------
-- Table structure for ims_move_boss
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_boss`;
CREATE TABLE `ims_move_boss` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公司招聘信息的id',
  `content` text COMMENT '招聘内容',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `status` int(11) DEFAULT '0' COMMENT '状态： 0 启用 1下线',
  `name` varchar(255) DEFAULT NULL COMMENT '招聘主题',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='招聘信息表';

-- ----------------------------
-- Table structure for ims_move_broadband
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_broadband`;
CREATE TABLE `ims_move_broadband` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '宽带的id',
  `name` varchar(255) DEFAULT NULL COMMENT '宽带兆数',
  `price` text COMMENT '价格',
  `favorable` varchar(255) DEFAULT NULL COMMENT '优惠',
  `new` varchar(255) DEFAULT NULL COMMENT '续费优惠',
  `status` int(11) DEFAULT NULL COMMENT '状态：0 启用 1 下架',
  `content` text COMMENT '详情介绍',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='宽带信息表';

-- ----------------------------
-- Table structure for ims_move_control
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_control`;
CREATE TABLE `ims_move_control` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '开关的id',
  `status` int(11) DEFAULT NULL COMMENT '状态： 0 开启 1关闭',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='控制员工修改信息表';

-- ----------------------------
-- Table structure for ims_move_customer
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_customer`;
CREATE TABLE `ims_move_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '客户的id',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `phone` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `account` bigint(200) DEFAULT NULL COMMENT '宽带账号',
  `uid` int(11) DEFAULT NULL COMMENT '微信用户的uid',
  `time` int(11) DEFAULT NULL COMMENT '添加时间',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户的openid',
  `avatar` varchar(255) DEFAULT NULL COMMENT '微信头像',
  `code` varchar(255) DEFAULT NULL COMMENT '二维码',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `pid` int(11) DEFAULT NULL COMMENT '扫码人的id 0为没有人推荐',
  `type` int(11) DEFAULT NULL COMMENT '扫码的类型： 2为扫员工码 1 为扫客户码 3自己关注',
  `math` int(11) DEFAULT '0' COMMENT '推广的人数',
  `plot` varchar(255) DEFAULT NULL COMMENT '小区名称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户信息表';

-- ----------------------------
-- Table structure for ims_move_declare
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_declare`;
CREATE TABLE `ims_move_declare` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '申报信息的id',
  `plat` varchar(255) DEFAULT NULL COMMENT '申报人所在小区',
  `phone` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `type` int(11) DEFAULT NULL COMMENT '保修类型： 0 报装 1 报修',
  `details` text COMMENT '故障详情',
  `status` int(11) DEFAULT '0' COMMENT '状态： 0 未做任何处理 1 处理中 2 订单分配失败 3处理完成 4 已分配 5 维修工拒绝接单',
  `time` int(11) DEFAULT NULL COMMENT '申报时间',
  `pid` int(11) DEFAULT NULL COMMENT '维修工的id',
  `uid` int(11) DEFAULT NULL COMMENT '申报人的id',
  `star` int(11) DEFAULT NULL COMMENT '星级： 1,2,3,4,5分别是1星。。。',
  `wid` int(11) DEFAULT NULL COMMENT '微信端的uid',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信端用户的varchar',
  `come` int(11) DEFAULT NULL COMMENT '申报人的信息来源： 0 员工推荐 1 客户推荐 2后台添加',
  `appraise` int(11) DEFAULT '0' COMMENT '评价状态 0代表为评价 1代表已评价 默认未平价 3此员工已删除',
  `nickname` varchar(255) DEFAULT NULL COMMENT '昵称',
  `refuse` text COMMENT '拒绝接单信息',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='申报信息表';

-- ----------------------------
-- Table structure for ims_move_fixed
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_fixed`;
CREATE TABLE `ims_move_fixed` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '固话表',
  `name` varchar(255) DEFAULT NULL COMMENT '固化名称',
  `theme` varchar(255) DEFAULT NULL COMMENT '固话主题',
  `status` int(11) DEFAULT NULL COMMENT '状态： 0 启用 1 下架',
  `introduce` text COMMENT '固话介绍',
  `content` text COMMENT '固话简介',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `photo` varchar(255) DEFAULT NULL COMMENT '展示图片信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='固话信息表';

-- ----------------------------
-- Table structure for ims_move_internet
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_internet`;
CREATE TABLE `ims_move_internet` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '互联网家表',
  `name` varchar(255) DEFAULT NULL COMMENT '互联网家名称',
  `theme` varchar(255) DEFAULT NULL COMMENT '互联网家主题',
  `status` int(11) DEFAULT NULL COMMENT '状态： 0 启用 1 下架',
  `introduce` text COMMENT '互联网家介绍',
  `content` text COMMENT '互联网家简介',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `photo` varchar(255) DEFAULT NULL COMMENT '展示图片信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='互联网家信息表';

-- ----------------------------
-- Table structure for ims_move_interact
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_interact`;
CREATE TABLE `ims_move_interact` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠活动的id',
  `name` varchar(255) DEFAULT NULL COMMENT '活动标题',
  `content` text COMMENT '活动内容',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `status` int(11) DEFAULT '0' COMMENT '状态： 0 启用 1下线',
  `photo` varchar(255) DEFAULT NULL COMMENT '宣传图',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠活动表';

-- ----------------------------
-- Table structure for ims_move_nearby
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_nearby`;
CREATE TABLE `ims_move_nearby` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '附近店面的id',
  `name` varchar(255) DEFAULT NULL COMMENT '店面名称',
  `photo` text COMMENT '门店图片信息',
  `position` varchar(255) DEFAULT NULL COMMENT '地址信息',
  `lat` double(255,6) DEFAULT NULL COMMENT '店面维度',
  `lng` double(255,6) DEFAULT NULL COMMENT '店面经度',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附近店面表';

-- ----------------------------
-- Table structure for ims_move_photo
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_photo`;
CREATE TABLE `ims_move_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '宣传图的id',
  `status` int(11) DEFAULT NULL COMMENT '状态： 0 代表启用 1 下架',
  `name` varchar(255) DEFAULT NULL COMMENT '主题',
  `content` text COMMENT '宣传图信息',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='宽带宣传图表';

-- ----------------------------
-- Table structure for ims_move_question
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_question`;
CREATE TABLE `ims_move_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '常见问题的id',
  `name` varchar(255) DEFAULT NULL COMMENT '常见问题主题',
  `content` text COMMENT '常见问题信息',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `status` int(11) DEFAULT '0' COMMENT '状态： 0 启用 1 下线',
  `uid` int(11) DEFAULT NULL COMMENT '所属分类的id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='常见问题表';

-- ----------------------------
-- Table structure for ims_move_recommend
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_recommend`;
CREATE TABLE `ims_move_recommend` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '推荐业务的id',
  `name` varchar(255) DEFAULT NULL COMMENT '用户姓名',
  `address` varchar(255) DEFAULT NULL COMMENT '用户地址',
  `phone` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `content` text COMMENT '推荐业务描述',
  `uid` int(11) DEFAULT NULL COMMENT '推荐人的uid',
  `openid` varchar(255) DEFAULT NULL COMMENT '推荐人的openid',
  `time` int(11) DEFAULT NULL COMMENT '添加时间',
  `pid` int(11) DEFAULT NULL COMMENT '推荐人的id',
  `come` int(11) DEFAULT NULL COMMENT '推荐信息人的来源：0 员工推荐 1：客户推荐 2:后台添加',
  `status` int(11) DEFAULT '0' COMMENT '状态： 0 待分配 1：已分配',
  `allot` int(11) DEFAULT NULL COMMENT '分配的id',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '推荐人头像信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='业务推荐的信息表';

-- ----------------------------
-- Table structure for ims_move_record
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_record`;
CREATE TABLE `ims_move_record` (
 `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '客户的id',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `phone` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `account` bigint(200) DEFAULT NULL COMMENT '宽带账号',
  `uid` int(11) DEFAULT NULL COMMENT '微信用户的uid',
  `time` int(11) DEFAULT NULL COMMENT '添加时间',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户的openid',
  `avatar` varchar(255) DEFAULT NULL COMMENT '微信头像',
  `code` varchar(255) DEFAULT NULL COMMENT '二维码',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `pid` int(11) DEFAULT NULL COMMENT '扫码人的id 0为没有人推荐',
  `type` int(11) DEFAULT NULL COMMENT '扫码的类型： 2为扫员工码 1 为扫客户码 3自己关注',
  `math` int(11) DEFAULT '0' COMMENT '推广的人数',
  `plot` varchar(255) DEFAULT NULL COMMENT '小区名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户信息记录表';

-- ----------------------------
-- Table structure for ims_move_staff
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_staff`;
CREATE TABLE `ims_move_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '员工的id',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像信息',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信的openid',
  `nickname` varchar(255) DEFAULT NULL COMMENT '昵称',
  `uid` int(11) DEFAULT NULL COMMENT '微信端的uid',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `phone` bigint(200) DEFAULT NULL COMMENT '电话',
  `pass` varchar(255) DEFAULT NULL COMMENT '密码',
  `time` int(11) DEFAULT NULL COMMENT '添加时间',
  `code` varchar(255) DEFAULT NULL COMMENT '推广二维码',
  `math` int(11) DEFAULT '0' COMMENT '推广人数',
  `service` int(11) DEFAULT '0' COMMENT '维修工已经分配的数量',
  `tid` int(11) DEFAULT NULL COMMENT '所属班组的id',
  `team` varchar(255) DEFAULT NULL COMMENT '班组名称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工信息表';

-- ----------------------------
-- Table structure for ims_move_team
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_team`;
CREATE TABLE `ims_move_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '小区id',
  `name` varchar(255) DEFAULT NULL COMMENT '小区名称',
  `pid` int(11) DEFAULT NULL COMMENT '班组id',
  `nickname` varchar(255) DEFAULT NULL COMMENT '班组名称',
  `aid` varchar(255) DEFAULT NULL COMMENT '地市id',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `xid` int(11) DEFAULT NULL COMMENT '区县id',
  `jid` varchar(255) DEFAULT NULL COMMENT '街道id',
  `lid` varchar(255) DEFAULT NULL COMMENT '路/村ID',
  `qid` varchar(255) DEFAULT NULL COMMENT '小区/班组id',
  `time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `qid` (`qid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='小区信息表';

-- ----------------------------
-- Table structure for ims_move_test
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_test`;
CREATE TABLE `ims_move_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `math` varchar(255) DEFAULT NULL COMMENT 'ds',
  `time` datetime DEFAULT NULL COMMENT 'dsada',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='测试';

-- ----------------------------
-- Table structure for ims_move_token
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_token`;
CREATE TABLE `ims_move_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `token` varchar(255) DEFAULT NULL COMMENT '微信的token',
  `tiket` varchar(255) DEFAULT NULL COMMENT '微信的tiket',
  `url` varchar(255) DEFAULT NULL COMMENT 'url',
  `scene_id` varchar(255) DEFAULT NULL COMMENT '参数',
  `expire_seconds` int(11) DEFAULT NULL COMMENT '时间',
  `action_name` varchar(255) DEFAULT NULL COMMENT '微信action_name',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户的openid',
  `remark` varchar(255) DEFAULT NULL COMMENT 'remark',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信的获取表';

-- ----------------------------
-- Table structure for ims_move_type
-- ----------------------------
DROP TABLE IF EXISTS `ims_move_type`;
CREATE TABLE `ims_move_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类的id',
  `name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `content` text COMMENT '分类描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='常见问题分类表';

-- ----------------------------
-- Table structure for ims_users
-- ----------------------------
ALTER TABLE `ims_users`
ADD COLUMN `avatar`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像信息' AFTER `endtime`,
ADD COLUMN `openid`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信端的openid' AFTER `avatar`,
ADD COLUMN `nickname`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信昵称' AFTER `openid`,
ADD COLUMN `service`  int(11) NULL DEFAULT 0 COMMENT '分配数量' AFTER `nickname`;

-- ----------------------------
-- Table structure for ims_users_permission
-- ----------------------------
ALTER TABLE `ims_users_permission`
ADD COLUMN `move`  int(11) NULL DEFAULT 2 COMMENT '模块操作权限： 1：代表有权限 2：代表无权限' AFTER `url`;
-- ----------------------------
-- Table structure for ims_users_permission
-- ----------------------------
INSERT INTO `ims_users_permission` (`uniacid`, `uid`, `type`, `permission`, `url`, `move`) VALUES(1, 1, 'system', 'all','' , 1);

-- ----------------------------
-- Records of ims_move_control
-- ----------------------------
INSERT INTO `ims_move_control` VALUES ('1', '1', '2017-05-06 13:42:44');

-- ----------------------------
-- Table structure for ims_qrcode_stat
-- ----------------------------
ALTER TABLE `ims_qrcode_stat`
MODIFY COLUMN `scene_str`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `qrcid`;

";


pdo_query($sql);
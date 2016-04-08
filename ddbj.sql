/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50520
Source Host           : localhost:3306
Source Database       : ddbj

Target Server Type    : MYSQL
Target Server Version : 50520
File Encoding         : 65001

Date: 2015-04-07 14:22:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for m_admin
-- ----------------------------
DROP TABLE IF EXISTS `m_admin`;
CREATE TABLE `m_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员名称',
  `phone` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(32) NOT NULL DEFAULT '',
  `salt` varchar(5) NOT NULL DEFAULT '' COMMENT '盐（提高密码安全性）',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '(1:超管；0:非超管（不是超管，登录时读取其他后台管理表）)',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登陆时间',
  `login_ip` int(11) NOT NULL DEFAULT '0' COMMENT '最后登陆IP',
  `login_count` int(11) NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `is_lock` tinyint(1) DEFAULT '1' COMMENT '(是否锁定；0:是；1：否)',
  `role_id` int(11) DEFAULT '0' COMMENT '超级管理员0；其他代理；管理角色id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

-- ----------------------------
-- Records of m_admin
-- ----------------------------
INSERT INTO `m_admin` VALUES ('1', 'administrator', '123456', 'c6fe424a4c3570d2e853ed1ccbf82f1e', '4ckm', '1', '1428387685', '2130706433', '141', '1', '0');
INSERT INTO `m_admin` VALUES ('8', '后管1', '18770002222', '6936d6fb241ee4c2134e8d13ecda70b0', 'gndn', '0', '1428049694', '2130706433', '15', '1', '5');
INSERT INTO `m_admin` VALUES ('10', '后管2', '18770003333', '2e374567025373b698dae85f02c5234b', 'nhli', '0', '1427946713', '2130706433', '1', '1', '5');

-- ----------------------------
-- Table structure for m_admin_module
-- ----------------------------
DROP TABLE IF EXISTS `m_admin_module`;
CREATE TABLE `m_admin_module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '模块英文名称',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '模块中文名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（1可用，0禁用）',
  `sort` tinyint(4) NOT NULL COMMENT '排序',
  `pid` int(11) NOT NULL COMMENT '上级模块ID',
  `is_view` tinyint(4) NOT NULL DEFAULT '1' COMMENT '导航菜单显示(1:是；0：否)',
  `o_level` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='超级模块表';

-- ----------------------------
-- Records of m_admin_module
-- ----------------------------
INSERT INTO `m_admin_module` VALUES ('1', '首页', 'Index', '1', '100', '0', '1', '1');
INSERT INTO `m_admin_module` VALUES ('2', '模块管理', 'Module', '1', '100', '0', '1', '1');
INSERT INTO `m_admin_module` VALUES ('3', '模块列表', 'index', '1', '50', '5', '1', '3');
INSERT INTO `m_admin_module` VALUES ('4', '模块新增', 'add', '1', '50', '5', '1', '3');
INSERT INTO `m_admin_module` VALUES ('5', '模块开发', 'index', '1', '50', '2', '1', '2');
INSERT INTO `m_admin_module` VALUES ('6', '新增处理', 'add_ajax', '1', '50', '5', '0', '3');
INSERT INTO `m_admin_module` VALUES ('7', '编辑处理', 'edit_ajax', '1', '50', '5', '0', '3');
INSERT INTO `m_admin_module` VALUES ('8', '删除处理', 'delete_one', '1', '50', '5', '0', '3');
INSERT INTO `m_admin_module` VALUES ('9', '编辑处理', 'edit', '1', '50', '5', '0', '3');
INSERT INTO `m_admin_module` VALUES ('10', '基本设置', 'Basic', '1', '100', '0', '1', '1');
INSERT INTO `m_admin_module` VALUES ('11', '网站信息', 'Site', '1', '50', '10', '1', '2');
INSERT INTO `m_admin_module` VALUES ('12', '角色管理', 'Role', '1', '50', '10', '1', '2');
INSERT INTO `m_admin_module` VALUES ('13', '信息设置', 'siteadd', '1', '50', '11', '1', '3');
INSERT INTO `m_admin_module` VALUES ('15', '角色新增', 'roleadd', '1', '60', '12', '1', '3');
INSERT INTO `m_admin_module` VALUES ('22', '处理角色新增', 'role_add_ajax', '1', '50', '12', '0', '3');
INSERT INTO `m_admin_module` VALUES ('16', '个人信息', 'info', '1', '50', '1', '1', '2');
INSERT INTO `m_admin_module` VALUES ('17', '密码管理', 'password', '1', '50', '1', '1', '2');
INSERT INTO `m_admin_module` VALUES ('18', '查看信息', 'infoshow', '1', '50', '16', '1', '3');
INSERT INTO `m_admin_module` VALUES ('19', '修改密码', 'changepassword', '1', '50', '17', '1', '3');
INSERT INTO `m_admin_module` VALUES ('21', '角色列表', 'rolelist', '1', '50', '12', '1', '3');
INSERT INTO `m_admin_module` VALUES ('23', '角色编辑', 'roledit', '1', '40', '12', '0', '3');
INSERT INTO `m_admin_module` VALUES ('24', '处理角色编辑', 'role_edit_ajax', '1', '30', '12', '0', '3');
INSERT INTO `m_admin_module` VALUES ('25', '角色删除', 'role_delete', '1', '10', '12', '0', '3');
INSERT INTO `m_admin_module` VALUES ('26', '人员管理', 'admin', '1', '40', '10', '1', '2');
INSERT INTO `m_admin_module` VALUES ('27', '人员列表', 'admin_list', '1', '50', '26', '1', '3');
INSERT INTO `m_admin_module` VALUES ('28', '人员新增', 'admin_add', '1', '40', '26', '1', '3');
INSERT INTO `m_admin_module` VALUES ('30', '人员编辑', 'admin_edit', '1', '30', '26', '0', '3');
INSERT INTO `m_admin_module` VALUES ('31', '人员编辑处理', 'admin_edit_ajax', '1', '10', '26', '0', '3');
INSERT INTO `m_admin_module` VALUES ('32', '人员删除', 'admin_delete', '1', '5', '26', '0', '3');
INSERT INTO `m_admin_module` VALUES ('34', '权限分配', 'node_list', '1', '40', '12', '1', '3');
INSERT INTO `m_admin_module` VALUES ('35', '权限添加页', 'node_add', '1', '40', '12', '0', '3');
INSERT INTO `m_admin_module` VALUES ('36', '处理权限添加', 'node_add_ajax', '1', '20', '12', '0', '3');
INSERT INTO `m_admin_module` VALUES ('37', '处理人员新增', 'admin_add_ajax', '1', '20', '26', '0', '3');

-- ----------------------------
-- Table structure for m_config
-- ----------------------------
DROP TABLE IF EXISTS `m_config`;
CREATE TABLE `m_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sitename` varchar(45) NOT NULL DEFAULT '' COMMENT '网站名称',
  `siteurl` varchar(45) NOT NULL DEFAULT '' COMMENT '网站地址',
  `email` varchar(45) NOT NULL DEFAULT '' COMMENT '网站管理员邮箱',
  `record_num` varchar(45) NOT NULL DEFAULT '' COMMENT '网站备案号',
  `copyright` varchar(200) NOT NULL DEFAULT '' COMMENT '底部版权信息',
  `title` varchar(45) NOT NULL DEFAULT '' COMMENT '首页标题',
  `keyword` varchar(200) NOT NULL DEFAULT '' COMMENT '网站关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '网站描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='网站基本信息设置';

-- ----------------------------
-- Records of m_config
-- ----------------------------
INSERT INTO `m_config` VALUES ('1', '滴滴保健', 'www.baidu.com', 'admin@admin.com', '1111', '版权所有 © 2014-2015 南昌微聚科技有限公司 并保留所有权利', '滴滴保健', '滴滴，保健', '滴滴，保健；哈哈哈');

-- ----------------------------
-- Table structure for m_module
-- ----------------------------
DROP TABLE IF EXISTS `m_module`;
CREATE TABLE `m_module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL COMMENT '关联权限表',
  `module_id` int(10) unsigned NOT NULL COMMENT '关联模块表',
  PRIMARY KEY (`id`),
  KEY `m_role1` (`role_id`) USING BTREE,
  KEY `module1_id` (`module_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='模块权限表';

-- ----------------------------
-- Records of m_module
-- ----------------------------
INSERT INTO `m_module` VALUES ('42', '1', '19');
INSERT INTO `m_module` VALUES ('41', '1', '17');
INSERT INTO `m_module` VALUES ('40', '1', '18');
INSERT INTO `m_module` VALUES ('39', '1', '16');
INSERT INTO `m_module` VALUES ('38', '1', '1');

-- ----------------------------
-- Table structure for m_role
-- ----------------------------
DROP TABLE IF EXISTS `m_role`;
CREATE TABLE `m_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '角色名称',
  `duty` varchar(20) NOT NULL DEFAULT '' COMMENT '角色主要职责',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of m_role
-- ----------------------------
INSERT INTO `m_role` VALUES ('1', '普通商家管理', '商家后台管理员');
INSERT INTO `m_role` VALUES ('2', '普通门店管理', '门店后台管理员');
INSERT INTO `m_role` VALUES ('3', '技师端', '技师后台管理员');
INSERT INTO `m_role` VALUES ('4', '普通代理', '超级管理员-代理');
INSERT INTO `m_role` VALUES ('5', 'VIP代理', '超级管理员代理2');
INSERT INTO `m_role` VALUES ('6', 'VIP商家管理', 'VIP商家管理员');
INSERT INTO `m_role` VALUES ('7', 'VIP门店管理', 'VIP门店管理员');

-- ----------------------------
-- Table structure for m_wx_fans
-- ----------------------------
DROP TABLE IF EXISTS `m_wx_fans`;
CREATE TABLE `m_wx_fans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL COMMENT '公众号id',
  `from_user` varchar(50) NOT NULL DEFAULT '' COMMENT '用户的唯一身份ID',
  `follow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否订阅(1:是；0:否)',
  `credit` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `ctime` int(11) NOT NULL DEFAULT '0' COMMENT '加入时间',
  `truename` varchar(10) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(20) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(150) NOT NULL DEFAULT '' COMMENT '头像',
  `qq` varchar(15) NOT NULL DEFAULT '',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号码',
  `vip` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'VIP级别(0:普通会员;1:是)',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别(0:保密 1:男 2:女)',
  `city` varchar(15) NOT NULL DEFAULT '' COMMENT '用户所在城市',
  `province` varchar(5) NOT NULL DEFAULT '' COMMENT '用户所在省份',
  `country` varchar(20) NOT NULL DEFAULT '' COMMENT '用户所在国家',
  `subscribe_time` int(11) NOT NULL DEFAULT '0' COMMENT '用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
-- Records of m_wx_fans
-- ----------------------------

-- ----------------------------
-- Table structure for m_wx_rule_key
-- ----------------------------
DROP TABLE IF EXISTS `m_wx_rule_key`;
CREATE TABLE `m_wx_rule_key` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL COMMENT '公众号id',
  `name` varchar(45) NOT NULL DEFAULT '' COMMENT '规则名称',
  `sort` tinyint(4) NOT NULL DEFAULT '50' COMMENT '排序，默认50',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '规则状态,(0禁用，1启用，2置顶)',
  `default` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否默认 1是 0否(1系统默认，0自定义规则)',
  `keyword` varchar(50) NOT NULL DEFAULT '' COMMENT '规则关键字',
  `ctime` int(11) NOT NULL COMMENT '创建时间',
  `cid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:文本；2：图文；',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '链接：模块',
  `controller` varchar(255) NOT NULL DEFAULT '' COMMENT '链接：控制器',
  `function` varchar(255) NOT NULL DEFAULT '' COMMENT '链接：方法',
  PRIMARY KEY (`id`),
  KEY `weid` (`weid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of m_wx_rule_key
-- ----------------------------

-- ----------------------------
-- Table structure for m_wx_rule_news
-- ----------------------------
DROP TABLE IF EXISTS `m_wx_rule_news`;
CREATE TABLE `m_wx_rule_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL COMMENT '规则id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `description` text,
  `picurl` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图文回复';

-- ----------------------------
-- Records of m_wx_rule_news
-- ----------------------------

-- ----------------------------
-- Table structure for m_wx_rule_text
-- ----------------------------
DROP TABLE IF EXISTS `m_wx_rule_text`;
CREATE TABLE `m_wx_rule_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL COMMENT '关联规则表',
  `content` varchar(1000) NOT NULL DEFAULT '' COMMENT '回复内容',
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文本回复';

-- ----------------------------
-- Records of m_wx_rule_text
-- ----------------------------

-- ----------------------------
-- Table structure for m_wx_wechats
-- ----------------------------
DROP TABLE IF EXISTS `m_wx_wechats`;
CREATE TABLE `m_wx_wechats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(5) NOT NULL DEFAULT '' COMMENT '用户标识. 随机生成保持不重复',
  `token` varchar(32) NOT NULL DEFAULT '' COMMENT '随机生成密钥',
  `access_token` varchar(300) NOT NULL DEFAULT '' COMMENT '存取凭证结构',
  `jsapi_ticket` varchar(300) NOT NULL DEFAULT '' COMMENT '微信认证jsapi_ticket',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '公众号名称',
  `account` varchar(30) NOT NULL DEFAULT '' COMMENT '微信帐号',
  `original` varchar(50) NOT NULL DEFAULT '' COMMENT '微信原始Id',
  `signature` varchar(100) NOT NULL DEFAULT '' COMMENT 'signature',
  `country` varchar(10) NOT NULL DEFAULT '',
  `province` varchar(5) NOT NULL DEFAULT '',
  `city` varchar(15) NOT NULL DEFAULT '',
  `username` varchar(40) NOT NULL DEFAULT '' COMMENT '微信公众平台登录名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '微信公众平台登录密码',
  `welcome` varchar(1000) NOT NULL DEFAULT '' COMMENT '欢迎信息',
  `default` varchar(1000) NOT NULL DEFAULT '' COMMENT '默认回复信息',
  `appId` varchar(50) NOT NULL DEFAULT '' COMMENT 'appId值',
  `appSecret` varchar(50) NOT NULL DEFAULT '' COMMENT 'appSecret值',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态：0删除，1正常',
  `pubmenu` text COMMENT '公众号菜单数据(json格式)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='公众号信息';

-- ----------------------------
-- Records of m_wx_wechats
-- ----------------------------
INSERT INTO `m_wx_wechats` VALUES ('1', 'tm1to', 'R4rGeCL6rj68XEHsXmBvpaYhk6TkwZer', '', '', '微聚内测', 'wjkjtest', 'gh_ff3de856ebc1', '', '', '', '', 'juyi@weijukeji.com', '222222', '', '', 'wxdb6bdd490bf2b529', '2ff35ff2d1a2c5d288636d471a0747d6', '1', null);

/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-04-28 16:28:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ims_users
-- ----------------------------
ALTER TABLE `ims_users`
ADD COLUMN `avatar`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像信息' AFTER `name`,
ADD COLUMN `openid`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信端的openid' AFTER `avatar`,
ADD COLUMN `nickname`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信昵称' AFTER `openid`,
ADD COLUMN `service`  int(11) NULL DEFAULT 0 COMMENT '分配数量' AFTER `nickname`;

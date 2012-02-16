/*
 Navicat Premium Backup

 Source Server         : MySql-Mamp
 Source Server Type    : MySQL
 Source Server Version : 50509
 Source Host           : localhost
 Source Database       : cistarter

 Target Server Type    : MySQL
 Target Server Version : 50509
 File Encoding         : utf-8

 Date: 02/15/2012 08:55:52 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `categories`
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `category_id` int(9) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;


-- ----------------------------
--  Table structure for `ci_sessions`
-- ----------------------------
DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- ----------------------------
--  Table structure for `groups`
-- ----------------------------
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_bin NOT NULL,
  `description` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
--  Records of `groups`
-- ----------------------------
BEGIN;
INSERT INTO `groups` VALUES ('1', 'admin', 'Administrator'), ('2', 'members', 'General User');
COMMIT;

-- ----------------------------
--  Table structure for `products`
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `active` int(1) NOT NULL,
  `option` int(9) NOT NULL,
  `image` varchar(256) NOT NULL,
  `category_id` int(9) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` int(10) unsigned NOT NULL,
  `username` varchar(15) COLLATE utf8_bin NOT NULL,
  `password` varchar(40) COLLATE utf8_bin NOT NULL,
  `salt` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `activation_code` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `forgotten_password_code` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `remember_code` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `company` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
--  Records of `users`
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('1', '2130706433', 'administrator', '59beecdf7fc966e2f17fd8f65a4a9aeb09d4a3d4', '9462e8eee0', 'admin@admin.com', '', null, null, '1268889823', '1329250830', '1', 'Admin', 'istrator', 'ADMIN', '0');
COMMIT;

-- ----------------------------
--  Table structure for `users_groups`
-- ----------------------------
DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
--  Records of `users_groups`
-- ----------------------------
BEGIN;
INSERT INTO `users_groups` VALUES ('1', '1', '1');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

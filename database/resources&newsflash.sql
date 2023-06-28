/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50621
Source Host           : localhost:3306
Source Database       : wt

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2015-08-05 10:31:07
*/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `resources_type`;
CREATE TABLE `resources_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resources`;
CREATE TABLE `resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `creator_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `re_cr_id` (`creator_id`),
  CONSTRAINT `re_cr_id` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`),
  KEY `re_re_ty_id` (`type_id`),
  CONSTRAINT `re_re_ty_id` FOREIGN KEY (`type_id`) REFERENCES `resources_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `jobsite` ADD `newsflash_allowed` int(1) NOT NULL DEFAULT 0;

ALTER TABLE `notification` DROP FOREIGN KEY no_ap_hi_id;
ALTER TABLE `notification` DROP `app_case_history_id`;
ALTER TABLE `notification` ADD `is_read` int(1) NOT NULL DEFAULT 0;
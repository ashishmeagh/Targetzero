
-- ----------------------------
-- Table structure for changes_tracker
-- ----------------------------
DROP TABLE IF EXISTS `changes_tracker`;
CREATE TABLE `changes_tracker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `model_id` int(11) NOT NULL,
  `model_name` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `before_state` varchar(255) NOT NULL,
  `after_state` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ch_tr_us_id` (`user_id`),
  CONSTRAINT `ch_tr_us_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for login_tracker
-- ----------------------------
DROP TABLE IF EXISTS `login_tracker`;
CREATE TABLE `login_tracker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `device` varchar(255) NOT NULL,
  `device_id` varchar(255),
  `ip_address` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lo_tr_us_id` (`user_id`),
  CONSTRAINT `lo_tr_us_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for contractor_jobsite
-- ----------------------------
DROP TABLE IF EXISTS `contractor_jobsite`;
CREATE TABLE `contractor_jobsite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contractor_id` int(11) NOT NULL,
  `jobsite_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `co_jo_co_id` (`contractor_id`),
  KEY `co_jo_jo_id` (`jobsite_id`),
  CONSTRAINT `co_jo_jo_id` FOREIGN KEY (`jobsite_id`) REFERENCES `jobsite` (`id`),
  CONSTRAINT `co_jo_co_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Alter to set default jobsite on mobile app
-- ----------------------------


ALTER TABLE `user` ADD `default_jobsite` int(11) DEFAULT NULL;
ALTER TABLE `user` ADD KEY `de_jo_jo_id` (`default_jobsite`);
ALTER TABLE `user` ADD CONSTRAINT `de_jo_jo_id` FOREIGN KEY (`default_jobsite`) REFERENCES `jobsite` (`id`);
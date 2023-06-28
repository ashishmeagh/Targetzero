
CREATE TABLE `timezone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  `timezone` varchar(255) NOT NULL,
  `timezone_code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `timezone` ADD `timezone_code` varchar(255) NOT NULL;
ALTER TABLE `jobsite` ADD `timezone_id` int(11) NOT NULL DEFAULT 2;
-- ALTER TABLE jobsite ADD DEFAULT 2 FOR timezone_id
ALTER TABLE `jobsite` ADD KEY `jo_ti_id` (`timezone_id`);
ALTER TABLE `jobsite` ADD CONSTRAINT `jo_ti_id` FOREIGN KEY (`timezone_id`) REFERENCES `timezone` (`id`);

INSERT INTO `timezone` VALUES ('1', '1', '2016-02-02 11:37:27', '2016-02-02 11:37:31', 'EST', 'America/New_York');
INSERT INTO `timezone` VALUES ('2', '1', '2016-02-02 11:37:27', '2016-02-02 11:37:31', 'CST', 'America/Chicago');
INSERT INTO `timezone` VALUES ('3', '1', '2016-02-02 11:37:27', '2016-02-02 11:37:31', 'MST', 'America/Denver');
INSERT INTO `timezone` VALUES ('4', '1', '2016-02-02 11:37:27', '2016-02-02 11:37:31', 'MST (no DST)', 'America/Phoenix');
INSERT INTO `timezone` VALUES ('5', '1', '2016-02-02 11:37:27', '2016-02-02 11:37:31', 'PST', 'America/Los_Angeles');
INSERT INTO `timezone` VALUES ('6', '1', '2016-02-02 11:37:27', '2016-02-02 11:37:31', 'AKST', 'America/Anchorage');
INSERT INTO `timezone` VALUES ('7', '1', '2016-02-02 11:37:27', '2016-02-02 11:37:31', 'HAST', 'America/Adak');
INSERT INTO `timezone` VALUES ('8', '1', '2016-02-02 11:37:27', '2016-02-02 11:37:31', 'HAST (no DST)', 'America/Honolulu');
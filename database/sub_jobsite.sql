
CREATE TABLE `sub_jobsite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  `subjobsite` varchar(255) NOT NULL,
  `jobsite_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subjo_jo_id` (`jobsite_id`),
  CONSTRAINT `subjo_jo_id` FOREIGN KEY (`jobsite_id`) REFERENCES `jobsite` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

ALTER TABLE `app_case` ADD `sub_jobsite_id` int(11) DEFAULT NULL;
ALTER TABLE `app_case` ADD KEY `ap_ca_sub_jo` (`sub_jobsite_id`);
ALTER TABLE `app_case` ADD CONSTRAINT `ap_ca_sub_jo` FOREIGN KEY (`sub_jobsite_id`) REFERENCES `sub_jobsite` (`id`);



/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50621
Source Host           : localhost:3306
Source Database       : whiting_turner_test

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2015-06-17 15:23:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for action
-- ----------------------------
DROP TABLE IF EXISTS `action`;
CREATE TABLE `action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of action
-- ----------------------------
INSERT INTO `action` VALUES ('9', 'login');
INSERT INTO `action` VALUES ('10', 'logout');
INSERT INTO `action` VALUES ('11', 'parameters');
INSERT INTO `action` VALUES ('12', 'profile');
INSERT INTO `action` VALUES ('13', 'cases');
INSERT INTO `action` VALUES ('14', 'issue read');
INSERT INTO `action` VALUES ('15', 'issue create');
INSERT INTO `action` VALUES ('16', 'issue edit');
INSERT INTO `action` VALUES ('17', 'issue delete');
INSERT INTO `action` VALUES ('18', 'issue close');
INSERT INTO `action` VALUES ('19', 'issue reopen');
INSERT INTO `action` VALUES ('20', 'issue assign');
INSERT INTO `action` VALUES ('21', 'issue dashboard');
INSERT INTO `action` VALUES ('22', 'jobsite read');
INSERT INTO `action` VALUES ('23', 'jobsite create');
INSERT INTO `action` VALUES ('24', 'jobsite edit');
INSERT INTO `action` VALUES ('25', 'jobsite delete');
INSERT INTO `action` VALUES ('26', 'jobsite close');
INSERT INTO `action` VALUES ('27', 'jobsite reopen');
INSERT INTO `action` VALUES ('28', 'jobsite assign');
INSERT INTO `action` VALUES ('29', 'user read');
INSERT INTO `action` VALUES ('30', 'user create');
INSERT INTO `action` VALUES ('31', 'user edit');
INSERT INTO `action` VALUES ('32', 'user delete');
INSERT INTO `action` VALUES ('33', 'userClose');
INSERT INTO `action` VALUES ('34', 'userReopen');
INSERT INTO `action` VALUES ('35', 'userAssign');
INSERT INTO `action` VALUES ('36', 'jobsiteDashboard');
INSERT INTO `action` VALUES ('37', 'userDashboard');
INSERT INTO `action` VALUES ('38', 'comment');
INSERT INTO `action` VALUES ('39', 'attach');
INSERT INTO `action` VALUES ('40', 'get attachment');

-- ----------------------------
-- Table structure for app_case
-- ----------------------------
DROP TABLE IF EXISTS `app_case`;
CREATE TABLE `app_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `creator_id` int(11) NOT NULL,
  `updated` datetime NOT NULL,
  `jobsite_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `floor_id` int(11) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `contractor_id` int(11) NOT NULL,
  `affected_user_id` int(11) DEFAULT NULL,
  `app_case_type_id` int(11) NOT NULL,
  `app_case_status_id` int(11) NOT NULL,
  `app_case_sf_code_id` int(11) NOT NULL,
  `app_case_priority_id` int(11) NOT NULL,
  `trade_id` int(11) NOT NULL,
  `additional_information` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ap_cr_id` (`creator_id`),
  KEY `ap_jo_id` (`jobsite_id`),
  KEY `ap_bu_id` (`building_id`),
  KEY `ap_fl_id` (`floor_id`),
  KEY `ap_ar_id` (`area_id`),
  KEY `ap_co_id` (`contractor_id`),
  KEY `ap_af_us_id` (`affected_user_id`),
  KEY `ap_tr_id` (`trade_id`),
  KEY `ap_ap_ty_id` (`app_case_type_id`) USING BTREE,
  KEY `ap_ap_st_id` (`app_case_status_id`) USING BTREE,
  KEY `ap_ap_sf_co_id` (`app_case_sf_code_id`) USING BTREE,
  KEY `ap_ap_pr_id` (`app_case_priority_id`) USING BTREE,
  CONSTRAINT `ap_af_us_id` FOREIGN KEY (`affected_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ap_ap_pr_id` FOREIGN KEY (`app_case_priority_id`) REFERENCES `app_case_priority` (`id`),
  CONSTRAINT `ap_ap_sf_co_id` FOREIGN KEY (`app_case_sf_code_id`) REFERENCES `app_case_sf_code` (`id`),
  CONSTRAINT `ap_ap_st_id` FOREIGN KEY (`app_case_status_id`) REFERENCES `app_case_status` (`id`),
  CONSTRAINT `ap_ap_ty_id` FOREIGN KEY (`app_case_type_id`) REFERENCES `app_case_type` (`id`),
  CONSTRAINT `ap_ar_id` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`),
  CONSTRAINT `ap_bu_id` FOREIGN KEY (`building_id`) REFERENCES `building` (`id`),
  CONSTRAINT `ap_co_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`id`),
  CONSTRAINT `ap_cr_id` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ap_fl_id` FOREIGN KEY (`floor_id`) REFERENCES `floor` (`id`),
  CONSTRAINT `ap_jo_id` FOREIGN KEY (`jobsite_id`) REFERENCES `jobsite` (`id`),
  CONSTRAINT `ap_tr_id` FOREIGN KEY (`trade_id`) REFERENCES `trade` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case
-- ----------------------------

-- ----------------------------
-- Table structure for app_case_history
-- ----------------------------
DROP TABLE IF EXISTS `app_case_history`;
CREATE TABLE `app_case_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `creator_id` int(11) NOT NULL,
  `app_case_id` int(11) NOT NULL,
  `log` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ap__hi_cr_id` (`creator_id`),
  KEY `ap__hi_ap__id` (`app_case_id`),
  CONSTRAINT `ap__hi_ap__id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`),
  CONSTRAINT `ap__hi_cr_id` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_history
-- ----------------------------

-- ----------------------------
-- Table structure for app_case_incident
-- ----------------------------
DROP TABLE IF EXISTS `app_case_incident`;
CREATE TABLE `app_case_incident` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_case_id` int(11) NOT NULL,
  `report_type_id` int(11) NOT NULL,
  `report_topic_id` int(11) NOT NULL,
  `incident_datetime` datetime NOT NULL,
  `recordable` int(1) NOT NULL,
  `lost_time` int(11) DEFAULT '0',
  `body_part_id` int(11) DEFAULT NULL,
  `injury_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ap__in_ap__id` (`app_case_id`),
  KEY `ap__in_re_ty_id` (`report_type_id`),
  KEY `ap__in_re_to_id` (`report_topic_id`),
  KEY `ap__in_bo_pa_id` (`body_part_id`),
  KEY `ap__in_in_ty_id` (`injury_type_id`),
  CONSTRAINT `ap__in_ap__id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`),
  CONSTRAINT `ap__in_bo_pa_id` FOREIGN KEY (`body_part_id`) REFERENCES `body_part` (`id`),
  CONSTRAINT `ap__in_in_ty_id` FOREIGN KEY (`injury_type_id`) REFERENCES `injury_type` (`id`),
  CONSTRAINT `ap__in_re_to_id` FOREIGN KEY (`report_topic_id`) REFERENCES `report_topic` (`id`),
  CONSTRAINT `ap__in_re_ty_id` FOREIGN KEY (`report_type_id`) REFERENCES `report_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_incident
-- ----------------------------

-- ----------------------------
-- Table structure for app_case_observation
-- ----------------------------
DROP TABLE IF EXISTS `app_case_observation`;
CREATE TABLE `app_case_observation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_case_id` int(11) NOT NULL,
  `foreman_id` int(11) NOT NULL,
  `correction_date` datetime NOT NULL,
  `coaching_provider` text,
  PRIMARY KEY (`id`),
  KEY `ap__ob_ap__id` (`app_case_id`),
  KEY `ap__ob_fo_id` (`foreman_id`),
  CONSTRAINT `ap__ob_ap__id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`),
  CONSTRAINT `ap__ob_fo_id` FOREIGN KEY (`foreman_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_observation
-- ----------------------------

-- ----------------------------
-- Table structure for app_case_priority
-- ----------------------------
DROP TABLE IF EXISTS `app_case_priority`;
CREATE TABLE `app_case_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `priority` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_priority
-- ----------------------------
INSERT INTO `app_case_priority` VALUES ('1', '1', '2015-04-17 12:36:28', '2015-05-12 18:31:25', 'HIGH');
INSERT INTO `app_case_priority` VALUES ('2', '1', '2015-05-06 15:33:22', '2015-05-06 15:33:22', 'LOW');
INSERT INTO `app_case_priority` VALUES ('3', '1', '2015-05-06 15:33:22', '2015-05-06 15:33:22', 'MEDIUM');

-- ----------------------------
-- Table structure for app_case_recognition
-- ----------------------------
DROP TABLE IF EXISTS `app_case_recognition`;
CREATE TABLE `app_case_recognition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_case_id` int(11) NOT NULL,
  `foreman_id` int(11) NOT NULL,
  `correction_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ap__re_ap__id` (`app_case_id`),
  KEY `ap__re_fo_id` (`foreman_id`),
  CONSTRAINT `ap__re_ap__id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`),
  CONSTRAINT `ap__re_fo_id` FOREIGN KEY (`foreman_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_recognition
-- ----------------------------

-- ----------------------------
-- Table structure for app_case_sf_code
-- ----------------------------
DROP TABLE IF EXISTS `app_case_sf_code`;
CREATE TABLE `app_case_sf_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ap_sf_co_pa_id` (`parent_id`) USING BTREE,
  CONSTRAINT `ap_sf_co_pa_id` FOREIGN KEY (`parent_id`) REFERENCES `app_case_sf_code` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=697 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_sf_code
-- ----------------------------
INSERT INTO `app_case_sf_code` VALUES ('1', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'I', 'PERSONAL PROTECTIVE EQUIPMENT', null);
INSERT INTO `app_case_sf_code` VALUES ('2', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'J', 'GENERAL ENVIROMENTAL CONTROLS', null);
INSERT INTO `app_case_sf_code` VALUES ('3', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'C', 'GENERAL SAFETY AND HEALTH PROVISIONS', null);
INSERT INTO `app_case_sf_code` VALUES ('4', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'D', 'OCCUPATIONAL HEALTH AND ENVIRONMENTAL CONTROLS', null);
INSERT INTO `app_case_sf_code` VALUES ('5', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'E', 'PERSONAL PROTECTIVE AND LIFE SAVING EQUIPMENT', null);
INSERT INTO `app_case_sf_code` VALUES ('6', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'F', 'FIRE PROTECTION AND PREVENTION', null);
INSERT INTO `app_case_sf_code` VALUES ('7', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'G', 'SIGNS, SIGNALS AND BARRICADES', null);
INSERT INTO `app_case_sf_code` VALUES ('8', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'H', 'MATERIAL HANDLING, STORAGE, USE AND DISPOSAL', null);
INSERT INTO `app_case_sf_code` VALUES ('9', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'I', 'TOOLS, HAND AND POWER', null);
INSERT INTO `app_case_sf_code` VALUES ('10', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'J', 'WELDING AND CUTTING', null);
INSERT INTO `app_case_sf_code` VALUES ('11', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'K', 'ELECTRICAL', null);
INSERT INTO `app_case_sf_code` VALUES ('12', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'L', 'SCAFFOLDS', null);
INSERT INTO `app_case_sf_code` VALUES ('13', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'M', 'FALL PROTECTION', null);
INSERT INTO `app_case_sf_code` VALUES ('14', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'O', 'MOTOR VEHICLES, MECHANIZED EQUIPMENT AND MARINE OPS', null);
INSERT INTO `app_case_sf_code` VALUES ('15', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'P', 'EXCAVATIONS', null);
INSERT INTO `app_case_sf_code` VALUES ('16', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'Q', 'CONCRETE AND MASONRY CONSTRUCTION', null);
INSERT INTO `app_case_sf_code` VALUES ('17', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'R', 'STEEL ERECTION', null);
INSERT INTO `app_case_sf_code` VALUES ('18', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'T', 'DEMOLITION', null);
INSERT INTO `app_case_sf_code` VALUES ('19', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'X', 'STAIRWAYS AND LADDERS', null);
INSERT INTO `app_case_sf_code` VALUES ('20', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'CC', 'CRANES AND DERRICKS IN CONSTRUCTION', null);
INSERT INTO `app_case_sf_code` VALUES ('21', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT', 'SAFETY STANDARD', null);
INSERT INTO `app_case_sf_code` VALUES ('22', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.910.134', 'Respiratory protection', '1');
INSERT INTO `app_case_sf_code` VALUES ('23', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.910.146', 'Permit-required confined spaces', '2');
INSERT INTO `app_case_sf_code` VALUES ('24', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.20', 'General safety and health provisions', '3');
INSERT INTO `app_case_sf_code` VALUES ('25', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.21', 'Safety training and education', '3');
INSERT INTO `app_case_sf_code` VALUES ('26', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.25', 'Housekeeping', '3');
INSERT INTO `app_case_sf_code` VALUES ('27', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.34', 'Means of Egress', '3');
INSERT INTO `app_case_sf_code` VALUES ('28', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.50', 'Medical Services and First Aid', '4');
INSERT INTO `app_case_sf_code` VALUES ('29', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.51', 'Sanitation', '4');
INSERT INTO `app_case_sf_code` VALUES ('30', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.52', 'Occupational Noise Exposure', '4');
INSERT INTO `app_case_sf_code` VALUES ('31', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.54', 'Non-ionizing Radiation', '4');
INSERT INTO `app_case_sf_code` VALUES ('32', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.55', 'Gases, Vapors, Fumes, Dusts and Mists', '4');
INSERT INTO `app_case_sf_code` VALUES ('33', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.56', 'Illumination', '4');
INSERT INTO `app_case_sf_code` VALUES ('34', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.64', 'Process safety management of highly hazardous chemicals', '4');
INSERT INTO `app_case_sf_code` VALUES ('35', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.95', 'Criteria for Personal Protective Equipment', '5');
INSERT INTO `app_case_sf_code` VALUES ('36', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.100', 'Head Protection', '5');
INSERT INTO `app_case_sf_code` VALUES ('37', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.101', 'Hearing Protection', '5');
INSERT INTO `app_case_sf_code` VALUES ('38', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.102', 'Eye and Face Protection', '5');
INSERT INTO `app_case_sf_code` VALUES ('39', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.103', 'Respiratory Protection', '5');
INSERT INTO `app_case_sf_code` VALUES ('40', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.104', 'Safety belts, lifelines, and lanyards', '5');
INSERT INTO `app_case_sf_code` VALUES ('41', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.150', 'Fire Protection', '6');
INSERT INTO `app_case_sf_code` VALUES ('42', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.151', 'Fire Prevention', '6');
INSERT INTO `app_case_sf_code` VALUES ('43', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.152', 'Flammable Liquids', '6');
INSERT INTO `app_case_sf_code` VALUES ('44', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.153', 'Liquefied Petroleum Gas (LP Gas)', '6');
INSERT INTO `app_case_sf_code` VALUES ('45', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.200', 'Accident Prevention Signs and Tags', '7');
INSERT INTO `app_case_sf_code` VALUES ('46', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.201', 'Signaling', '7');
INSERT INTO `app_case_sf_code` VALUES ('47', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.202', 'Barricades', '7');
INSERT INTO `app_case_sf_code` VALUES ('48', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.250', 'General Requirements for Storage', '8');
INSERT INTO `app_case_sf_code` VALUES ('49', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.251', 'Rigging Equipment for Material Handling', '8');
INSERT INTO `app_case_sf_code` VALUES ('50', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.252', 'Disposal of Waste Materials', '8');
INSERT INTO `app_case_sf_code` VALUES ('51', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.300', 'General Requirements', '9');
INSERT INTO `app_case_sf_code` VALUES ('52', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.301', 'Hand Tools', '9');
INSERT INTO `app_case_sf_code` VALUES ('53', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.302', 'Power-Operated Hand Tools', '9');
INSERT INTO `app_case_sf_code` VALUES ('54', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.303', 'Abrasive Wheels and Tools', '9');
INSERT INTO `app_case_sf_code` VALUES ('55', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.304', 'Woodworking Tools', '9');
INSERT INTO `app_case_sf_code` VALUES ('56', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.305', 'Jacks: Lever, Ratchet, Screw and Hydraulic', '9');
INSERT INTO `app_case_sf_code` VALUES ('57', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.350', 'Gas Welding and Cutting', '10');
INSERT INTO `app_case_sf_code` VALUES ('58', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.351', 'Arc Welding and Cutting', '10');
INSERT INTO `app_case_sf_code` VALUES ('59', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.352', 'Fire Prevention', '10');
INSERT INTO `app_case_sf_code` VALUES ('60', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.403', 'General Requirements', '11');
INSERT INTO `app_case_sf_code` VALUES ('61', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.404', 'Wiring Design and Protection', '11');
INSERT INTO `app_case_sf_code` VALUES ('62', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.405', 'Wiring Methods, Components and Equipment for General Use', '11');
INSERT INTO `app_case_sf_code` VALUES ('63', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.416', 'Safety-Related Work Practices', '11');
INSERT INTO `app_case_sf_code` VALUES ('64', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.417', 'Locking and tagging of circuits', '11');
INSERT INTO `app_case_sf_code` VALUES ('65', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.451', 'General Requirements', '12');
INSERT INTO `app_case_sf_code` VALUES ('66', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.452', 'Requirements for specific types of scaffolds', '12');
INSERT INTO `app_case_sf_code` VALUES ('67', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.453', 'Aerial lifts', '12');
INSERT INTO `app_case_sf_code` VALUES ('68', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.501', 'Duty to have fall protection - General', '13');
INSERT INTO `app_case_sf_code` VALUES ('69', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.503', 'Training', '13');
INSERT INTO `app_case_sf_code` VALUES ('70', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.600', 'Equipment', '14');
INSERT INTO `app_case_sf_code` VALUES ('71', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.601', 'Motor Vehicles', '14');
INSERT INTO `app_case_sf_code` VALUES ('72', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.602', 'Material handling equipment', '14');
INSERT INTO `app_case_sf_code` VALUES ('73', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.604', 'Site clearing', '14');
INSERT INTO `app_case_sf_code` VALUES ('74', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.651', 'Specific excavation requirements', '15');
INSERT INTO `app_case_sf_code` VALUES ('75', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.652', 'Requirements for protective systems', '15');
INSERT INTO `app_case_sf_code` VALUES ('76', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.701', 'General requirements', '16');
INSERT INTO `app_case_sf_code` VALUES ('77', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.702', 'Requirements for equipment and tools', '16');
INSERT INTO `app_case_sf_code` VALUES ('78', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.752', 'Site layout, site-specific erection plan, and construction sequence', '17');
INSERT INTO `app_case_sf_code` VALUES ('79', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.753', 'Hoisting and rigging', '17');
INSERT INTO `app_case_sf_code` VALUES ('80', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.754', 'Structural steel assembly', '17');
INSERT INTO `app_case_sf_code` VALUES ('81', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.755', 'Column anchorage', '17');
INSERT INTO `app_case_sf_code` VALUES ('82', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.756', 'Beams and columns', '17');
INSERT INTO `app_case_sf_code` VALUES ('83', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.760', 'Fall protection', '17');
INSERT INTO `app_case_sf_code` VALUES ('84', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.761', 'Training', '18');
INSERT INTO `app_case_sf_code` VALUES ('85', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.850', 'Preparatory operations', '19');
INSERT INTO `app_case_sf_code` VALUES ('86', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.851', 'Stairs, passageways, and ladders', '19');
INSERT INTO `app_case_sf_code` VALUES ('87', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.852', 'Chutes', '19');
INSERT INTO `app_case_sf_code` VALUES ('88', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1.926.856', 'Removal of wall, floors, and material with equipment', '19');
INSERT INTO `app_case_sf_code` VALUES ('89', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.051', 'General requirements', '20');
INSERT INTO `app_case_sf_code` VALUES ('90', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.052', 'Stairways', '20');
INSERT INTO `app_case_sf_code` VALUES ('91', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.053', 'Ladders', '20');
INSERT INTO `app_case_sf_code` VALUES ('92', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.060', 'Training', '20');
INSERT INTO `app_case_sf_code` VALUES ('93', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.402', 'Ground Conditions', '21');
INSERT INTO `app_case_sf_code` VALUES ('94', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.404', 'Assembly / Disassembly: General requirements', '21');
INSERT INTO `app_case_sf_code` VALUES ('95', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.412', 'Inspections', '21');
INSERT INTO `app_case_sf_code` VALUES ('96', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.413', 'Wire Rope Inspection', '21');
INSERT INTO `app_case_sf_code` VALUES ('97', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.414', 'Wire Rope Selection & Install Requirements', '21');
INSERT INTO `app_case_sf_code` VALUES ('98', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.417', 'Operation', '21');
INSERT INTO `app_case_sf_code` VALUES ('99', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.422', 'Signals', '21');
INSERT INTO `app_case_sf_code` VALUES ('100', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.424', 'Work area control', '21');
INSERT INTO `app_case_sf_code` VALUES ('101', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.428', 'Signal person qualifications', '21');
INSERT INTO `app_case_sf_code` VALUES ('102', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '19.261.433', 'Design, construction and testing', '21');
INSERT INTO `app_case_sf_code` VALUES ('103', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 010', 'Smoking', '22');
INSERT INTO `app_case_sf_code` VALUES ('104', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 020', 'Welding & Burning', '22');
INSERT INTO `app_case_sf_code` VALUES ('105', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 030', 'Compressed Gas Storage', '22');
INSERT INTO `app_case_sf_code` VALUES ('106', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 040', 'Site Rules', '22');
INSERT INTO `app_case_sf_code` VALUES ('107', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 050', 'Housekeeping', '22');
INSERT INTO `app_case_sf_code` VALUES ('108', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 060', 'Personal Protective Equipment', '22');
INSERT INTO `app_case_sf_code` VALUES ('109', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 070', 'Fall/Trip Protection', '22');
INSERT INTO `app_case_sf_code` VALUES ('110', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 080', 'General Storage', '22');
INSERT INTO `app_case_sf_code` VALUES ('111', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 090', 'Tools - Hand & Powered', '22');
INSERT INTO `app_case_sf_code` VALUES ('112', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 100', 'Ladders', '22');
INSERT INTO `app_case_sf_code` VALUES ('113', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 110', 'Scaffolding', '22');
INSERT INTO `app_case_sf_code` VALUES ('114', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 120', 'Guardrails, Handrails, Covers', '22');
INSERT INTO `app_case_sf_code` VALUES ('115', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 130', 'Confined Spaces', '22');
INSERT INTO `app_case_sf_code` VALUES ('116', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 140', 'Material Safety Data Sheets', '22');
INSERT INTO `app_case_sf_code` VALUES ('117', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 150', 'Cranes', '22');
INSERT INTO `app_case_sf_code` VALUES ('118', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 160', 'Lock Out/Tag Out', '22');
INSERT INTO `app_case_sf_code` VALUES ('119', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 170', 'Fire Control', '22');
INSERT INTO `app_case_sf_code` VALUES ('120', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT - 180', 'Environmental', '22');
INSERT INTO `app_case_sf_code` VALUES ('121', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(a)(1)', 'Permissible Practice. Primary objective shall be to prevent atmospheric contamination from harmful dusts, fogs, fumes, mists, gases, smokes, sprays, or vapors. This shall be accomplished as far as feasible by accepted engineering control measures (for example, enclosure or confinement of the operation, general and local ventilation, and substitution of less toxic materials). When effective engineering controls are not feasible, or while they are being instituted, appropriate respirators shall be used pursuant to this section.', '22');
INSERT INTO `app_case_sf_code` VALUES ('122', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(a)(2)', 'Respirators. A respirator shall be provided to each employee when such equipment is necessary to protect the health of such employee. The employer shall provide the respirators which are applicable and suitable for the purpose intended. The employer shall be responsible for the establishment and maintenance of a respiratory program. The program shall cover each employee required by this section to use a respirator.', '22');
INSERT INTO `app_case_sf_code` VALUES ('123', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(d)(1)(i)', 'The employer shall select and provide an appropriate respirator based on the respiratory hazard(s) to which the worker is exposed and workplace and user factors that affect respirator performance and reliability.', '22');
INSERT INTO `app_case_sf_code` VALUES ('124', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(d)(1)(ii)', 'The employer shall select a NIOSH-certified respirator. The respirator shall be used in compliance with the conditions of its certification.', '22');
INSERT INTO `app_case_sf_code` VALUES ('125', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(d)(1)(iii)', 'The employer shall identify and evaluate the respiratory hazard(s) in the workplace; this evaluation shall include a reasonable estimate of employee exposures to respiratory hazard(s) and an identification of the contaminant\'s chemical state and physical form. Where the employer cannot identify or reasonably estimate the employee exposure, the employer shall consider the atmosphere to be IDLH.', '22');
INSERT INTO `app_case_sf_code` VALUES ('126', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(d)(1)(iv)', 'The employer shall select respirators from a sufficient number of respirator models and sizes so that the respirator is acceptable to, and correctly fits, the user.', '22');
INSERT INTO `app_case_sf_code` VALUES ('127', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(e)(1)', 'Medical evaluation. The employer shall provide a medical evaluation to determine the employee\'s ability to use a respirator, before the employee is fit tested or required to use the respirator in the workplace. The employer may discontinue an employee\'s medical evaluations when the employee is no longer required to use a respirator.', '22');
INSERT INTO `app_case_sf_code` VALUES ('128', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(f)', 'Fit testing. Before an employee may be required to use any resepirator with a negative or positive pressure tight-fitting facepiece, the employee must be fit tested with the same make, model, style, and size of respirator that will be used.', '22');
INSERT INTO `app_case_sf_code` VALUES ('129', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(g)(1)(i-iii)', 'Use of respirators. The employer shall not permit respirators with tight-fitting facepieces to be worn by employees who have: facial hair or any condition that interferes with face-to-facepiece seal or valve function. If an employee wears corrective glasses or goggles or other PPE, the employer shall ensure that such equipment is worn in a manner that does not interfere with the seal of the facepiece to the face of the user. Employers shall also ensure that employees perform a user seal check each time they put on the respirator.', '22');
INSERT INTO `app_case_sf_code` VALUES ('130', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(h)(1)', 'Cleaning and disinfecting. The employer shall provide each respirator user with a respirator that is clean, sanitary, and in good working order. The employer shall ensure that respirators are cleaned and disinfected using procedures recommended by the respirator manufacturer.', '22');
INSERT INTO `app_case_sf_code` VALUES ('131', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(h)(2)(i)', 'Storage. Employers shall ensure that respirators are stored to protect them from damage, contamination, dust, sunlight, extreme conditions, excessive moisture, and damaging chemicals, and they shall be packed or stored to prevent deformation of the facepiece and exhalation valves.', '22');
INSERT INTO `app_case_sf_code` VALUES ('132', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(h)(3)(i)(A)', 'Inspections. All respirators used in routine situations shall be inspected before each use and during cleaning.', '22');
INSERT INTO `app_case_sf_code` VALUES ('133', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(j)', 'Identificatioin of filters, cartridges, and canisters. The employer shall ensure that all filters, cartridges, and canisters used in the workplace are labeled and color coded with the NIOSH approval label that the label is not removed and remains legible.', '22');
INSERT INTO `app_case_sf_code` VALUES ('134', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.134(k)', 'Training. The employer shall provide effective training to employees who are required to use respirators. The training must be comprehensive, understandable, and recur annually, and more often if necessary.', '22');
INSERT INTO `app_case_sf_code` VALUES ('135', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.146(e)(1)', 'Before entry is authorized, the employer shall document the completion of measures required.', '23');
INSERT INTO `app_case_sf_code` VALUES ('136', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.146(e)(2)', 'Before entry begins, the entry supervisor identified on the permit shall the sign the entry permit to authorize entry.', '23');
INSERT INTO `app_case_sf_code` VALUES ('137', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.146(e)(3)', 'The completed permit shall be made available at the time of entry to all authorized entrants or their authorized representatives, by posting it at the entry portal or by any other equally effective means, so that the entrants can confirm that pre-entry preparations have been completed.', '23');
INSERT INTO `app_case_sf_code` VALUES ('138', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.146(e)(4)', 'The duration of the permit may not exceed the time required to complete the assigned task or job identified on the permit.', '23');
INSERT INTO `app_case_sf_code` VALUES ('139', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.146(e)(5)(i-ii)', 'The entry supervisor shall terminate the entry and cancel the entry permit when: The entry operations covered by the entry permit have been completed; or a condition that is not allowed under the entry permit arises in or near the permit space.', '23');
INSERT INTO `app_case_sf_code` VALUES ('140', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.146(f)(1-15)', 'Permit content. Entry permit shall identify the following: permit space to be entered, purpose of entry, date and the authorized duration of the entry permit, authorized entrants within the space, personnel serving as attendants, individual serving as entry supervisor, hazards of the permit space to be entered, measures used to isolate the permit space and to eliminate or control permit space hazards, acceptable entry conditions, results of initial and periodic tests performed accompanied by initials or names of the testers and by an indication of when the tests were performed, rescue and emergency services that can be summoned and the means for summoning those services, communication procedures used by authorized entrants and attendants to maintain contact, equipment such as PPE and testing equipment, any other info. whose inclusion is necessary in order to ensure employee safety, and any additional permits that have been issued to authorize work in the permit space.', '23');
INSERT INTO `app_case_sf_code` VALUES ('141', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1910.146(g)(2)(i-iv)', 'Training. Training shall be provided to each affected employee: before the employee is first assigned duties, before there is a change in assigned duties, whenever there is a change in permit space operations that presents a hazard about which an employee has not previously been trained, or whenever the employer has reason to believe either that there are deviations from the permit space entry procedures or inadequacies in the employee\'s knowledge or use of these procedures.', '23');
INSERT INTO `app_case_sf_code` VALUES ('142', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.20(b)(2)', 'Such programs shall provide for frequent and regular inspections of the job sites, materials, and equipment to be made by competent persons designated by the employers.', '24');
INSERT INTO `app_case_sf_code` VALUES ('143', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.20(f)(1)', 'Personal protective equipment. Standards in this part requiring the employer to provide personal protective equipment (PPE), including respirators and other types of PPE, because of hazards to employees impose a seperate compliance duty with respect to each employee covered by the requirement. The employer must provide PPE to each employee required to use the PPE, and each failure to provide PPE to an employee may be considered a seperate violation.', '24');
INSERT INTO `app_case_sf_code` VALUES ('144', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.20(f)(2)', 'Training. Standards in this part requiring training on hazards and related maters, such as standards requiring that employees receive training or that the employer train employees, provide training to employees, or institute or implement a training program, impose a separate compliance duty with respect to each employee covered by the requirement. The employer must train each affected employee in the manner required by the standard, and each failure to train an employee may be considered a seperate violation.', '24');
INSERT INTO `app_case_sf_code` VALUES ('145', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.21(b)(2)', 'The employer shall instruct each employee in the recognition and avoidance of unsafe conditions and the regulations applicable to his work environment to control or eliminate any hazards or other exposure to illness or injury.', '25');
INSERT INTO `app_case_sf_code` VALUES ('146', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.21(b)(3)', 'Employees required to handle or use poisons, caustics, and other harmful substances shall be instructed regarding the safe handling and use, and be made aware of the potential hazards, personal hygiene, and personal protective measures required.', '25');
INSERT INTO `app_case_sf_code` VALUES ('147', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.21(b)(4)', 'In job site areas where harmful plants or animals are present, employees who may be exposed shall be instructed regarding the potential hazards, and how to avoid injury, and the first aid procedures to be used in the event of injury.', '25');
INSERT INTO `app_case_sf_code` VALUES ('148', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.21(b)(6)(i)', 'Confined Space.  All employees required to enter into confined or enclosed spaces shall be instructed as to the nature of the hazards involved, the necessary precautions to be taken, and in the use of protective and emergency equipment required. The employer shall comply with any specific regulations that apply to work in dangerous or potentially dangerous areas.', '25');
INSERT INTO `app_case_sf_code` VALUES ('149', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.25(a)', 'During the course of construction, alteration, or repairs, form and scrap lumber with protruding nails, and all other debris, shall be kept cleared from work areas, passageways, and stairs, in and around buildings or other structures.', '26');
INSERT INTO `app_case_sf_code` VALUES ('150', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.25(b)', 'Combustible scrap and debris shall be removed at regular intervals during the course of construction. Safe means shall be provided to facilitate such removal.', '26');
INSERT INTO `app_case_sf_code` VALUES ('151', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.25(c)', 'Containers shall be provided for the collection and separation of waste, trash, oily and used rags, and other refuse. Containers used for garbage and other oily, flammable, or hazardous wastes, such as caustics, acids, harmful dusts, etc. shall be equipped with covers. Garbage and other waste shall be disposed of at frequent and regular intervals.', '26');
INSERT INTO `app_case_sf_code` VALUES ('152', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.34(a)', 'General. In every building or structure exits shall be so arranged and maintained as to provide free and unobstructed egress from all parts of the building or structure at all times when it is occupied. No lock or fastening to prevent free escape from the inside of any building shall be installed except in mental, penal, or corrective institutions where supervisory personnel is continually on duty and effective provisions are made to remove occupants in case of fire or other emergency.', '27');
INSERT INTO `app_case_sf_code` VALUES ('153', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.34(b)', 'Exit marking. Exits shall be marked by a readily visible sign. Access to exits shall be marked by readily visible signs in all cases where the exit or way to reach it is not immediately visible to the occupants.', '27');
INSERT INTO `app_case_sf_code` VALUES ('154', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.34(c)', 'Maintenance and workmanship. Means of egress shall be continually maintained free of all obstructions or impediments to full instant use in the case of fire or other emergency.', '27');
INSERT INTO `app_case_sf_code` VALUES ('155', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.50(d)', 'The contents of the first aid kit shall be placed in a weatherproof container with individual sealed packages for each type of item, and shall be checked by the employer before being sent out on each job and at least weekly on each job to ensure that the expended items are replaced.', '28');
INSERT INTO `app_case_sf_code` VALUES ('156', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.51(a)(1)', 'An adequate supply of potable water shall be provided in all places of employment.', '29');
INSERT INTO `app_case_sf_code` VALUES ('157', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.51(a)(2)', 'Portable containers used to dispense drinking water shall be capable of being tightly closed, and equipped with a tap. Water shall not be dipped from containers.', '29');
INSERT INTO `app_case_sf_code` VALUES ('158', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.51(a)(3)', 'Any container used to distribute drinking water shall be clearly marked as to the nature of its contents and not used for any other purpose.', '29');
INSERT INTO `app_case_sf_code` VALUES ('159', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.51(a)(4)', 'The common drinking cup is prohibited.', '29');
INSERT INTO `app_case_sf_code` VALUES ('160', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.51(a)(5)', 'Where single service cups (to be used but once) are supplied, both a sanitary container for the unused cups and a receptacle for disposing of the used cups shall be provided.', '29');
INSERT INTO `app_case_sf_code` VALUES ('161', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.51(c)(1)', 'Toilets shall be provided for employees according to the following table: D-1. 20 workers or less = 1 toilet; 20 workers or more = 1 toilet seat and 1 urinal per 40 workers; 200 workers or more = 1 toilet seat and 1 urinal per 50 workers', '29');
INSERT INTO `app_case_sf_code` VALUES ('162', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.52(c)', 'The employer shall administer a continuing, effective hearing conservation program, as described in paragraphs (c) through (o) of this section, whenever employee noise exposures equal or exceed an 8-hour time-weighted average sound level (TWA) of 85 decibels measured on the A scale (slow response) or, equivalently, a dose of fifty percent. For purposes of the hearing conservation program, employee noise exposures shall be computed in accordance with appendix A and Table G-16a, and without regard to any attenuation provided by the use of personal protective equipment.', '30');
INSERT INTO `app_case_sf_code` VALUES ('163', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.54(a)', 'Only qualified and trained employees shall be assigned to install, adjust, and operate laser equipment.', '31');
INSERT INTO `app_case_sf_code` VALUES ('164', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.54(b)', 'Proof of qualification of the laser equipment operator shall be available and in possession of the operator at all times.', '31');
INSERT INTO `app_case_sf_code` VALUES ('165', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.54(d)', 'Areas in which lasers are used shall be posted with standard laser warning placards.', '31');
INSERT INTO `app_case_sf_code` VALUES ('166', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.54(l)', 'Employees shall not be exposed to microwave power densities in excess of 10 milliwatts per square centimeter.', '31');
INSERT INTO `app_case_sf_code` VALUES ('167', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.55(a)', 'Exposure of employees to inhalation, ingestion, skin absorption, or contact with any material or substance at a concentration above those specified in the �Threshold Limit Values of Airborne Contaminants for 1970� of the American Conference of Governmental Industrial Hygienists, shall be avoided.', '32');
INSERT INTO `app_case_sf_code` VALUES ('168', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.56(a)', 'General construction areas, concrete placement, excavation and waste areas, accessways, active storage areas, loading platforms, refueling, and field maintenance areas = 3 fc; Tunnels, shafts, and general underground work areas: (Exception: minimum of 10 foot-candles is required at tunnel and shaft heading during drilling, mucking, and scaling. Bureau of Mines approved cap lights shall be acceptable for use in the tunnel heading.)= 5 fc; Indoors: warehouses, corridors, hallways, and exitways = 5 fc; General construction plant and shops (e.g., batch plants, screening plants, mechanical and electrical equipment rooms, carpenter shops, rigging lofts and active storerooms, barracks or living quarters, locker or dressing rooms, mess halls, and indoor toilets and workrooms) = 10 fc; First aid stations, infirmaries, and offices = 30 fc.', '32');
INSERT INTO `app_case_sf_code` VALUES ('169', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.64(f)(4)', 'Control of hazardous energy. The employer shall develop and implement safe work practices to provide for the control of hazards during operations such as lockout/tagout; confined space entry; opening process equipment or piping; and control over entrance into a facility by maintenance, contractor, laboratory, or other support personnel. These safe work practices shall apply to employees and contractor employees.', '34');
INSERT INTO `app_case_sf_code` VALUES ('170', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.95(a)', 'Application. Protective equipment, including personal protective equipment for eyes, face, head, and extremities, protective clothing, respiratory devices, and protective shields and barriers, shall be provided, used, and maintained in a sanitary and reliable condition wherever it is necessary by reason of hazards of processes or environment, chemical hazards, radiological hazards, or mechanical irritants encountered in a manner capable of causing injury or impairment in the function of any part of the body through absorption, inhalation or physical contact.', '35');
INSERT INTO `app_case_sf_code` VALUES ('171', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.95(b)', 'Employee-owned equipment. Where employees provide their own protective equipment, the employer shall be responsible to assure its adequacy, including proper maintenance, and sanitation of such equipment.', '35');
INSERT INTO `app_case_sf_code` VALUES ('172', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.100(a)', 'Employees working in areas where there is a possible danger of head injury from impact, or from falling or flying objects, or from electrical shock and burns, shall be protected by protective helmets.', '36');
INSERT INTO `app_case_sf_code` VALUES ('173', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.101(b)', 'Wherever it is not feasible to reduce the noise levels or duration of exposures to those specified in Table D-2, Permissible Noise Exposures, in 1926.52, ear protective devices shall be provided and used.', '37');
INSERT INTO `app_case_sf_code` VALUES ('174', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.101(b)', 'Ear protective devices inserted in the ear shall be fitted or determined individually by competent persons.', '37');
INSERT INTO `app_case_sf_code` VALUES ('175', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.101(c)', 'Plain cotton is not an acceptable protective device.', '37');
INSERT INTO `app_case_sf_code` VALUES ('176', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.102(a)(1)', 'Employees shall be provided with eye and face protection equipment when machines or operations present potential eye or face injury from physical, chemical, or radiation agents.', '38');
INSERT INTO `app_case_sf_code` VALUES ('177', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.102(a)(3)(i-iii)', 'Corrective lenses. Employees whose vision requires the use of corrective lenses in spectacles, when required to wear eye protection, shall be protected by goggles or spectacles of one of the following types: spectacles whose protective lenses provide optical correction; goggles that can be worn over corrective spectacles without disturbing the adjustment of the spectacles; goggles that incorporate corrective lenses mounted behind the protective lenses.', '38');
INSERT INTO `app_case_sf_code` VALUES ('178', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.103(a)(1)', 'In the control of those occupational diseases caused by breathing air contaminated with harmful dusts, fogs, fumes, mists, gases, smokes, sprays, or vapors, the primary objective shall be to prevent atmospheric contamination. This shall be accomplished as far as feasible by accepted engineering control measures (for example, enclosure or confinement of the operation, general and local ventilation, and substitution of less toxic materials). When effective engineering controls are not feasible, or while they are being instituted, appropriate respirators shall be used pursuant to this section.', '39');
INSERT INTO `app_case_sf_code` VALUES ('179', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.103(a)(2)', 'A respirator shall be provided to each employee when such equipment is necessary to protect the health of such employee. The employer shall provide the respirators which are applicable and suitable for the purpose intended. The employer shall be responsible for the establishment and maintenance of a respiratory protection program, which shall include the requirements outlined in paragraph (c) of this section. The program shall cover each employee required by this section to use a respirator.', '39');
INSERT INTO `app_case_sf_code` VALUES ('180', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.103(c)', 'Respiratory protection program. This paragraph requires the employer to develop and implement a written respiratory protection program with required worksite-specific procedures and elements for required respirator use. The program must be administered by a suitably trained program administrator. In addition, certain program elements may be required for voluntary use to prevent potential hazards associated with the use of the respirator.', '39');
INSERT INTO `app_case_sf_code` VALUES ('181', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.103(c)(1)(i)', 'An employer may provide respirators at the request of employees or permit employees to use their own respirators, if the employer determines that such respirator use will not in itself create a hazard. If the employer determines that any voluntary respirator use is permissible, the employer shall provide the respirator users with the information contained in appendix D to this section (�Information for Employees Using Respirators When Not Required Under the Standard�)', '39');
INSERT INTO `app_case_sf_code` VALUES ('182', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.103(c)(ii)', 'In addition, the employer must establish and implement those elements of a written respiratory protection program necessary to ensure that any employee using a respirator voluntarily is medically able to use that respirator, and that the respirator is cleaned, stored, and maintained so that its use does not present a health hazard to the user. Exception: Employers are not required to include in a written respiratory protection program those employees whose only use of respirators involves the voluntary use of filtering face pieces (dust masks).', '39');
INSERT INTO `app_case_sf_code` VALUES ('183', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.104(a)', 'Lifelines, safety belts, and lanyards shall be used only for employee safeguarding. Any lifeline, safety belt, or lanyard actually subjected to in-service loading, as distinguished from static load testing, shall be immediately removed from service and shall not be used again for employee safeguarding.', '40');
INSERT INTO `app_case_sf_code` VALUES ('184', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.104(b)', 'Lifelines shall be secured above the point of operation to an anchorage or structural member capable of supporting a minimum dead weight of 5,400 pounds.', '40');
INSERT INTO `app_case_sf_code` VALUES ('185', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.104(d)', 'Lanyard shall be a minimum of 1/2 inch nylon, or equivalent, with a maximum length to provide for a fall of not greater than 6 feet. The rope shall have a nominal breaking strength of 5,400 pounds.', '40');
INSERT INTO `app_case_sf_code` VALUES ('186', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.150(a)(2)', 'Access to all available firefighting equipment shall be maintained at all times.', '41');
INSERT INTO `app_case_sf_code` VALUES ('187', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.150(a)(3)', 'All firefighting equipment, provided by the employer, shall be conspicuously located.', '41');
INSERT INTO `app_case_sf_code` VALUES ('188', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.150(a)(4)', 'All firefighting equipment shall be periodically inspected and maintained in operating condition. Defective equipment shall be immediately replaced.', '41');
INSERT INTO `app_case_sf_code` VALUES ('189', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.150(c)(1)(i)', 'Portable firefighting equipment. Fire extinguishers and small hose lines. (i) A fire extinguisher, rated not less than 2A, shall be provided for each 3,000 square feet of the protected building area, or major fraction thereof. Travel distance from any point of the protected area to the nearest fire extinguisher shall not exceed 100 feet.', '41');
INSERT INTO `app_case_sf_code` VALUES ('190', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.150(c)(1)(vi)', 'A fire extinguisher, rated not less than 10B, shall be provided within 50 feet of wherever more than 5 gallons of flammable or combustible liquids or 5 pounds of flammable gas are being used on the jobsite. This requirement does not apply to the integral fuel tanks of motor vehicles.', '41');
INSERT INTO `app_case_sf_code` VALUES ('191', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.151(b)(1)', 'No temporary building shall be erected where it will adversely affect any means of exit.', '42');
INSERT INTO `app_case_sf_code` VALUES ('192', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.151(b)(2)', 'Temporary buildings, when located within another building or structure, shall be of either noncombustible construction or of combustible construction having a fire resistance of not less than 1 hour.', '42');
INSERT INTO `app_case_sf_code` VALUES ('193', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.151(c)(6)', 'Open yard storage. Portable fire extinguishing equipment, suitable for the fire hazard involved, shall be provided at convenient, conspicuously accessible locations in the yard area. Portable fire extinguishers, rated not less than 2A, shall be placed so that maximum travel distance to the nearest unit shall not exceed 100 feet.', '42');
INSERT INTO `app_case_sf_code` VALUES ('194', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.151(d)(5)', 'Indoor storage. Clearance of at least 36 inches shall be maintained between the top level of the stored material and the sprinkler deflectors.', '42');
INSERT INTO `app_case_sf_code` VALUES ('195', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(a)(1)', 'General requirements. Only approved containers and portable tanks shall be used for storage and handling of flammable liquids. Approved safety cans or Department of Transportation approved containers shall be used for the handling and use of flammable liquids in quantities of 5 gallons or less, except that this shall not apply to those flammable liquid materials which are highly viscid (extremely hard to pour), which may be used and handled in original shipping containers. For quantities of one gallon or less, the original container may be used, for storage, use and handling of flammable liquids.', '43');
INSERT INTO `app_case_sf_code` VALUES ('196', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(a)(2)', 'Flammable liquids shall not be stored in areas used for exits, stairways, or normally used for the safe passage of people.', '43');
INSERT INTO `app_case_sf_code` VALUES ('197', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(c)(1)', 'Storage outside buildings. Storage of containers (not more than 60 gallons each) shall not exceed 1,100 gallons in any one pile or area. Piles or groups of containers shall be separated by a 5-foot clearance. Piles or groups of containers shall not be nearer than 20 feet to a building.', '43');
INSERT INTO `app_case_sf_code` VALUES ('198', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(c)(4)(i)', 'Outdoor portable tank storage. Tanks shall not be nearer than 20 feet from any building. Two or more portable tanks, grouped together, having a combined capacity in excess of 2,200 gallons, shall be separated by a 5 foot clear area. Individual portable tanks exceeding 1,100 gallons shall be separated by a 5 foot clear area.', '43');
INSERT INTO `app_case_sf_code` VALUES ('199', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(c)(4)(ii)', 'Outdoor portable tank storage. Within 200 feet of each portable tank, there shall be a 12 foot wide access way to permit approach of fire control apparatus.', '43');
INSERT INTO `app_case_sf_code` VALUES ('200', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(d)(1)', 'Fire control for flammable liquid storage. (1) At least one portable fire extinguisher, having a rating of not less than 20-B units, shall be located outside of, but not more than 10 feet from, the door opening into any room used for storage of more than 60 gallons of flammable liquids.', '43');
INSERT INTO `app_case_sf_code` VALUES ('201', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(d)(2)', 'At least one portable fire extinguisher having a rating of not less than 20-B units shall be located not less than 25 feet, nor more than 75 feet, from any flammable liquid storage area located outside.', '43');
INSERT INTO `app_case_sf_code` VALUES ('202', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(e)(2)', 'Transfer of Category 1, 2, or 3 flammable liquids from one container to another shall be done only when containers are electrically interconnected (bonded).', '43');
INSERT INTO `app_case_sf_code` VALUES ('203', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(e)(3)', 'Dispensing. Flammable liquids shall be drawn from or transferred into vessels, containers, or tanks only through a closed piping system, from safety cans, by means of a device drawing through the top, or portable tanks, by gravity or pump, through an approved self-closing valve.', '43');
INSERT INTO `app_case_sf_code` VALUES ('204', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(f)(1)', 'Handling liquids at point of final use. Category 1,2, or 3 flammable liquids shall be kept in closed containers when not actually in use.', '43');
INSERT INTO `app_case_sf_code` VALUES ('205', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(f)(2)', 'Leakage or spillage of flammable liquids shall be disposed of promptly and safely.', '43');
INSERT INTO `app_case_sf_code` VALUES ('206', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(f)(3)', 'Category 1,2, or 3 flammable liquids may be used only where there are no open flames or other sources of ignition, unless conditions warrant greater clearance.', '43');
INSERT INTO `app_case_sf_code` VALUES ('207', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(g)(1)', 'Flammable liquids shall be stored in approved closed containers, in tanks located underground, or in aboveground portable tanks.', '43');
INSERT INTO `app_case_sf_code` VALUES ('208', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(g)(2)', 'The tank trucks shall comply with the requirements covered in the Standards for Tank Vehicles for Flammable and Combustible Liquids, NFPA No. 385-1996.', '43');
INSERT INTO `app_case_sf_code` VALUES ('209', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(g)(8)', 'There shall be no smoking or open flames in the areas used for fueling, servicing fuel systems for internal combustion engines, receiving or dispensing of flammable liquids.', '43');
INSERT INTO `app_case_sf_code` VALUES ('210', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(g)(10)', 'The motors of all equipment being fueled shall be shut off during the fueling operation.', '43');
INSERT INTO `app_case_sf_code` VALUES ('211', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.152(g)(11)', 'Each service or fueling area shall be provided with at least one fire extinguisher having a rating of not less than 20-B:C located so that an extinguisher will be within 75 feet of pump, dispenser, underground fill pipe opening, and lubrication or service area.', '43');
INSERT INTO `app_case_sf_code` VALUES ('212', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.153(h)(8)', 'Portable heaters, including salamanders, shall be equipped with an approved automatic device to shut off the flow of gas to the main burner, and pilot if used, in the event of flame failure. Such heaters, having inputs above 50,000 B.t.u. per hour, shall be equipped with either a pilot, which must be lighted and proved before the main burner can be turned on, or an electrical ignition system.', '44');
INSERT INTO `app_case_sf_code` VALUES ('213', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.153(j)', 'Storage of LPG containers. Storage of LPG within buildings is prohibited.', '44');
INSERT INTO `app_case_sf_code` VALUES ('214', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.153(k)(1)', 'Storage outside of buildings. Storage outside of buildings, for containers awaiting use, shall be located from the nearest building or group of buildings, in accordance with the following: =<500 lbs. = 0 ft., >500<6001 lbs. = 10 ft.; >6000<10001 lbs. = 20 ft.; > 10001 lbs. = 25 ft.', '44');
INSERT INTO `app_case_sf_code` VALUES ('215', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.153(l)', 'Fire protection. Storage locations shall be provided with at least one approved portable fire extinguisher having a rating of not less than 20-B:C.', '44');
INSERT INTO `app_case_sf_code` VALUES ('216', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.153(n)', 'When LP-Gas and one or more other gases are stored or used in the same area, the containers shall be marked to identify their content. Marking shall be in compliance with ANSI Z48.1-1954.', '44');
INSERT INTO `app_case_sf_code` VALUES ('217', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.153(o)', 'Damage from vehicles. When damage to LP-Gas systems from vehicular traffic is a possibility, precautions against such damage shall be taken.', '44');
INSERT INTO `app_case_sf_code` VALUES ('218', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.200(a)', 'General. Signs and symbols required by this subpart shall be visible at all times when work is being performed, and shall be removed or covered promptly when the hazards no longer exist.', '45');
INSERT INTO `app_case_sf_code` VALUES ('219', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.200(b)(1)', 'Danger signs shall only be used where an immediate hazard exists.', '45');
INSERT INTO `app_case_sf_code` VALUES ('220', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.200(d)', 'Exit signs. Exit signs, when required, shall be lettered in legible red letters, not less than 6 inches high, on a white field and the principal stroke of the letters shall be at least three-fourths inch in width.', '45');
INSERT INTO `app_case_sf_code` VALUES ('221', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.200(g)(2)', 'All traffic control signs or devices used for protection of construction workers shall conform to Part VI of the Manual of Uniform Traffic Control Devices (AMUTCD�), 1988 Edition, Revision 3, September 3, 1993, FHWA-SA-94-027 or Part VI of the Manual on Uniform Traffic Control Devices, Millennium Edition, December 2000, FHWA, which are incorporated by reference.', '45');
INSERT INTO `app_case_sf_code` VALUES ('222', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.201(a)', 'Flaggers. Signaling by flaggers and the use of flaggers, including warning garments worn by flaggers shall conform to Part VI of the Manual on Uniform Traffic Control Devices, (1988 Edition, Revision 3 or the Millennium Edition), which are incorporated by reference in � 1926.200(g)(2).', '46');
INSERT INTO `app_case_sf_code` VALUES ('223', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926,202', 'Barricades for protection of employees shall conform to Part VI of the Manual on Uniform Traffic Control Devices (1988 Edition, Revision 3 or Millennium Edition), which are incorporated by reference in � 1926.200(g)(2).', '47');
INSERT INTO `app_case_sf_code` VALUES ('224', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(a)(1)', 'General. All materials stored in tiers shall be stacked, racked, blocked, interlocked, or otherwise secured to prevent sliding, falling or collapse.', '48');
INSERT INTO `app_case_sf_code` VALUES ('225', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(a)(2)', 'Maximum safe load limits of floors within buildings and structures, in pounds per square foot, shall be conspicuously posted in all storage areas, except for floor or slab on grade. Maximum safe loads shall not be exceeded.', '48');
INSERT INTO `app_case_sf_code` VALUES ('226', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(a)(3)', 'Aisles and passageways shall be kept clear to provide for the free and safe movement of material handling equipment or employees. Such areas shall be kept in good repair.', '48');
INSERT INTO `app_case_sf_code` VALUES ('227', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(b)(1)', 'Material storage. Material stored inside buildings under construction shall not be placed within 6 feet of any hoistway or inside floor openings, nor within 10 feet of an exterior wall which does not extend above the top of the material stored.', '48');
INSERT INTO `app_case_sf_code` VALUES ('228', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(b)(5)', 'Materials shall not be stored on scaffolds or runways in excess of supplies needed for immediate operations.', '48');
INSERT INTO `app_case_sf_code` VALUES ('229', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(b)(6)', 'Brick stacks shall not be more than 7 feet in height. When a loose brick stack reaches a height of 4 feet, it shall be tapered back 2 inches in every foot of height above the 4-foot level.', '48');
INSERT INTO `app_case_sf_code` VALUES ('230', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(b)(7)', 'When masonry blocks are stacked higher than 6 feet, the stack shall be tapered back one-half block per tier above the 6-foot level.', '48');
INSERT INTO `app_case_sf_code` VALUES ('231', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(b)(8)(i)', 'Lumber. Used lumber shall have all nails withdrawn before stacking.', '48');
INSERT INTO `app_case_sf_code` VALUES ('232', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(b)(8)(iii)', 'Lumber shall be so stacked as to be stable and self-supporting.', '48');
INSERT INTO `app_case_sf_code` VALUES ('233', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.250(b)(9)', 'Structural steel, poles, pipe, bar stock, and other cylindrical materials, unless racked, shall be stacked and blocked so as to prevent spreading or tilting.', '48');
INSERT INTO `app_case_sf_code` VALUES ('234', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(a)(1)', 'Rigging for material handling. Rigging equipment for material handling shall be inspected prior to use on each shift and as necessary during its use to ensure that it is safe. Defective rigging equipment shall be removed from service.', '49');
INSERT INTO `app_case_sf_code` VALUES ('235', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(a)(2)(i)', 'General. Has permanently affixed and legible identification markings as prescribed by the manufacturer that indicate the recommended safe working load;', '49');
INSERT INTO `app_case_sf_code` VALUES ('236', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(a)(6)', 'Inspections. Each day before being used, the sling and all fastenings and attachments shall be inspected for damage or defects by a competent person designated by the employer. Additional inspections shall be performed during sling use, where service conditions warrant. Damaged or defective slings shall be immediately removed from service.', '49');
INSERT INTO `app_case_sf_code` VALUES ('237', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(b)(3)', 'Job or shop hooks and links, or makeshift fasteners, formed from bolts, rods, etc., or other such attachments, shall not be used.', '49');
INSERT INTO `app_case_sf_code` VALUES ('238', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(b)(6)(ii)', 'The employer shall make and maintain a record of the most recent month in which each alloy steel chain sling was thoroughly inspected, and shall make such record available for examination.', '49');
INSERT INTO `app_case_sf_code` VALUES ('239', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(c)(4)(iii)', 'Wire Rope. Eyes in wire rope bridles, slings, or bull wires shall not be formed by wire rope clips or knots.', '49');
INSERT INTO `app_case_sf_code` VALUES ('240', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(c)(5)(i)', 'When used for eye splices, the U-bolt shall be applied so that the �U� section is in contact with the dead end of the rope.', '49');
INSERT INTO `app_case_sf_code` VALUES ('241', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(c)(6)', 'Slings shall not be shortened with knots or bolts or other makeshift devices.', '49');
INSERT INTO `app_case_sf_code` VALUES ('242', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(c)(9)', 'Slings shall be padded or protected from the sharp edges of their loads.', '49');
INSERT INTO `app_case_sf_code` VALUES ('243', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(c)(16)', 'Wire rope slings shall have permanently affixed, legible identification markings stating size, rated capacity for the type(s) of hitch(es) used and the angle upon which it is based, and the number of legs if more than one.', '49');
INSERT INTO `app_case_sf_code` VALUES ('244', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(d)(2)(v)', 'Knots shall not be used in lieu of splices.', '49');
INSERT INTO `app_case_sf_code` VALUES ('245', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(e)(1)', 'The employer shall have each synthetic web sling marked or coded to show: name or trademark of the manufacturer; rated capacities for the type of hitch; type of material.', '49');
INSERT INTO `app_case_sf_code` VALUES ('246', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.251(e)(8)', 'Synthetic web slings shall be immediately removed from service if any of the following conditions are present: acid or caustic burns; melting or charring of any part of the sling surface; snags, punctures, tears or cuts; broken or worn stitches; distortion of fittings.', '49');
INSERT INTO `app_case_sf_code` VALUES ('247', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.252(a)', 'Whenever materials are dropped more than 20 feet to any point lying outside the exterior walls of the building, an enclosed chute of wood, or equivalent material, shall be used. For the purpose of this paragraph, an enclosed chute is a slide, closed in on all sides, through which material is moved from a high place to a lower one.', '50');
INSERT INTO `app_case_sf_code` VALUES ('248', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.252(b)', 'When debris is dropped through holes in the floor without the use of chutes, the area onto which the material is dropped shall be completely enclosed with barricades not less than 42 inches high and not less than 6 feet back from the projected edge of the opening above. Signs warning of the hazard of falling materials shall be posted at each level. Removal shall not be permitted in this lower area until debris handling ceases above.', '50');
INSERT INTO `app_case_sf_code` VALUES ('249', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.300(a)', 'Condition of tools. All hand and power tools and similar equipment, whether furnished by the employer or the employee, shall be maintained in a safe condition.', '51');
INSERT INTO `app_case_sf_code` VALUES ('250', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.300(b)(1)', 'When power operated tools are designed to accommodate guards, they shall be equipped with such guards when in use.', '51');
INSERT INTO `app_case_sf_code` VALUES ('251', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.301(a)', 'Employers shall not issue or permit the use of unsafe hand tools.', '52');
INSERT INTO `app_case_sf_code` VALUES ('252', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.301(b)', 'Wrenches, including adjustable, pipe, end, and socket wrenches shall not be used when jaws are sprung to the point that slippage occurs.', '52');
INSERT INTO `app_case_sf_code` VALUES ('253', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.301(c)', 'Impact tools, such as drift pins, wedges, and chisels, shall be kept free of mushroomed heads.', '52');
INSERT INTO `app_case_sf_code` VALUES ('254', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.301(d)', 'The wooden handles of tools shall be kept free of splinters or cracks and shall be kept tight in the tool.', '52');
INSERT INTO `app_case_sf_code` VALUES ('255', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(a)(1)', 'Electric power-operated tools. Electric power operated tools shall either be of the approved double-insulated type or grounded in accordance with subpart K of this part.', '53');
INSERT INTO `app_case_sf_code` VALUES ('256', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(a)(2)', 'Electric power-operated tools. The use of electric cords for hoisting or lowering tools shall not be permitted.', '53');
INSERT INTO `app_case_sf_code` VALUES ('257', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(b)(1)', 'Pneumatic power tools shall be secured to the hose or whip by some positive means to prevent the tool from becoming accidentally disconnected.', '53');
INSERT INTO `app_case_sf_code` VALUES ('258', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(b)(4)', 'Compressed air shall not be used for cleaning purposes except where reduced to less than 30 p.s.i. and then only with effective chip guarding and personal protective equipment.', '53');
INSERT INTO `app_case_sf_code` VALUES ('259', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(b)(6)', 'The use of hoses for hoisting or lowering tools shall not be permitted.', '53');
INSERT INTO `app_case_sf_code` VALUES ('260', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(b)(7)', 'All hoses exceeding 1/2 inch inside diameter shall have a safety device at the source of supply or branch line to reduce pressure in case of hose failure.', '53');
INSERT INTO `app_case_sf_code` VALUES ('261', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(c)(1)', 'All fuel powered tools shall be stopped while being refueled, serviced, or maintained, and fuel shall be transported, handled, and stored in accordance with subpart F of this part.', '53');
INSERT INTO `app_case_sf_code` VALUES ('262', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(e)(1)', 'Powder-actuated tools. Only employees who have been trained in the operation of the particular tool in use shall be allowed to operate a powder-actuated tool.', '53');
INSERT INTO `app_case_sf_code` VALUES ('263', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(e)(6)', 'Powder-actuated tools. Loaded tools shall not be left unattended.', '53');
INSERT INTO `app_case_sf_code` VALUES ('264', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.302(e)(12)', 'Powder-actuated tools used by employees shall meet all other applicable requirements of ANSI, A10.3-1970, Safety Requirements for Explosive-Actuated Fastening Tools.', '53');
INSERT INTO `app_case_sf_code` VALUES ('265', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.303(c)(2)', 'Floor and bench-mounted grinders shall be provided with work rests which are rigidly supported and readily adjustable. Such work rests shall be kept at a distance not to exceed one-eighth inch (1/8\") from the surface of the wheel.', '54');
INSERT INTO `app_case_sf_code` VALUES ('266', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.304(d)', 'Guarding. All portable, power-driven circular saws shall be equipped with guards above and below the base plate or shoe. The upper guard shall cover the saw to the depth of the teeth, except for the minimum arc required to permit the base to be tilted for bevel cuts. The lower guard shall cover the saw to the depth of the teeth, except for the minimum arc required to allow proper retraction and contact with the work. When the tool is withdrawn from the work, the lower guard shall automatically and instantly return to the covering position.', '55');
INSERT INTO `app_case_sf_code` VALUES ('267', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.304(i)(1)', 'Hand-fed ripsaws. Each circular hand-fed ripsaw shall be guarded by a hood which shall completely enclose that portion of the saw above the table and that portion of the saw above the material being cut. The hood and mounting shall be arranged so that the hood will automatically adjust itself to the thickness of and remain in contact with the material being cut but it shall not offer any considerable resistance to insertion of material to saw or to passage of the material being sawed. The hood shall be made of adequate strength to resist blows and strains incidental to reasonable operation, adjusting, and handling, and shall be so designed as to protect the operator from flying splinters and broken saw teeth. It shall be made of material that is soft enough so that it will be unlikely to cause tooth breakage. The hood shall be so mounted as to insure that its operation will be positive, reliable, and in true alignment with the saw; and the mounting shall be adequate in strength to resist any reasonable side thrust or other force tending to throw it out of line.', '55');
INSERT INTO `app_case_sf_code` VALUES ('268', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.305(a)(1)', 'General Requirements. The manufacturer\'s rated capacity shall be legibly marked on all jacks and shall not be exceeded.', '56');
INSERT INTO `app_case_sf_code` VALUES ('269', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.305(d)(1)(i)', 'Operation and maintenance. After the load has been raised, it shall be cribbed, blocked, or otherwise secured at once.', '56');
INSERT INTO `app_case_sf_code` VALUES ('270', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.305(d)(1)(iv)', 'Each jack shall be thoroughly inspected at times which depend upon the service conditions. Inspections shall be not less frequent than the following: for constant or intermittent use at one locality, once every 6 months; for jacks sent out of shop for special work, when sent out and when returned; for a jack subjected to abnormal load or shock, immediately before and immediately thereafter;', '56');
INSERT INTO `app_case_sf_code` VALUES ('271', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(a)(1)', 'Transporting, moving, and storing compressed gas cylinders. Valve protection caps shall be in place and secured.', '57');
INSERT INTO `app_case_sf_code` VALUES ('272', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(a)(2)', 'Transporting, moving, and storing compressed gas cylinders. When cylinders are hoisted, they shall be secured on a cradle, slingboard, or pallet. They shall not be hoisted or transported by means of magnets or choker slings.', '57');
INSERT INTO `app_case_sf_code` VALUES ('273', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(a)(4)', 'Transporting, moving, and storing compressed gas cylinders. When cylinders are transported by powered vehicles, they shall be secured in a vertical position.', '57');
INSERT INTO `app_case_sf_code` VALUES ('274', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(a)(6)', 'Transporting, moving, and storing compressed gas cylinders. Unless cylinders are firmly secured on a special carrier intended for this purpose, regulators shall be removed and valve protection caps put in place before cylinders are moved.', '57');
INSERT INTO `app_case_sf_code` VALUES ('275', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(a)(7)', 'Transporting, moving, and storing compressed gas cylinders.  A suitable cylinder truck, chain, or other steadying device shall be used to keep cylinders from being knocked over while in use.', '57');
INSERT INTO `app_case_sf_code` VALUES ('276', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(a)(8)', 'When work is finished, when cylinders are empty, or when cylinders are moved at any time, the cylinder valve shall be closed.', '57');
INSERT INTO `app_case_sf_code` VALUES ('277', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(a)(9)', 'Transporting, moving, and storing compressed gas cylinders.  Compressed gas cylinders shall be secured in an upright position at all times except, if necessary, for short periods of time while cylinders are actually being hoisted or carried.', '57');
INSERT INTO `app_case_sf_code` VALUES ('278', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(a)(10)', 'Transporting, moving, and storing compressed gas cylinders. Oxygen cylinders in storage shall be separated from fuel-gas cylinders or combustible materials (especially oil or grease), a minimum distance of 20 feet (6.1 m) or by a noncombustible barrier at least 5 feet (1.5 m) high having a fire-resistance rating of at least one-half hour.', '57');
INSERT INTO `app_case_sf_code` VALUES ('279', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(b)(1)', 'Placing of cylinders. Cylinders shall be kept far enough away from the actual welding or cutting operation so that sparks, hot slag, or flame will not reach them. When this is impractical, fire resistant shields shall be provided.', '57');
INSERT INTO `app_case_sf_code` VALUES ('280', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(b)(4)', 'Placing of cylinders.  Cylinders containing oxygen or acetylene or other fuel gas shall not be taken into confined spaces.', '57');
INSERT INTO `app_case_sf_code` VALUES ('281', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(g)(3)', 'Torches shall be lighted by friction lighters or other approved devices, and not by matches or from hot work.', '57');
INSERT INTO `app_case_sf_code` VALUES ('282', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(h)', 'Regulators and gauges. Oxygen and fuel gas pressure regulators, including their related gauges, shall be in proper working order while in use.', '57');
INSERT INTO `app_case_sf_code` VALUES ('283', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.350(i)', 'Oil and grease. Oxygen cylinders and fittings shall be kept away from oil or grease. Cylinders, cylinder caps and valves, couplings, regulators, hose, and apparatus shall be kept free from oil or greasy substances and shall not be handled with oily hands or gloves.', '57');
INSERT INTO `app_case_sf_code` VALUES ('284', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.351(b)(2)', 'Only cable free from repair or splices for a minimum distance of 10 feet from the cable end to which the electrode holder is connected shall be used, except that cables with standard insulated connectors or with splices whose insulating quality is equal to that of the cable are permitted.', '58');
INSERT INTO `app_case_sf_code` VALUES ('285', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.351(c)(2)', 'Pipelines containing gases or flammable liquids, or conduits containing electrical circuits, shall not be used as a ground return.', '58');
INSERT INTO `app_case_sf_code` VALUES ('286', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.351(d)(1)', 'Operating instructions. When electrode holders are to be left unattended, the electrodes shall be removed and the holders shall be so placed or protected that they cannot make electrical contact with employees or conducting objects.', '58');
INSERT INTO `app_case_sf_code` VALUES ('287', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.351(e)', 'Shielding. Whenever practicable, all arc welding and cutting operations shall be shielded by noncombustible or flameproof screens which will protect employees and other persons working in the vicinity from the direct rays of the arc.', '58');
INSERT INTO `app_case_sf_code` VALUES ('288', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.352(a)', 'When practical, objects to be welded, cut, or heated shall be moved to a designated safe location or, if the objects to be welded, cut, or heated cannot be readily moved, all movable fire hazards in the vicinity shall be taken to a safe place, or otherwise protected.', '59');
INSERT INTO `app_case_sf_code` VALUES ('289', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.352(b)', 'If the object to be welded, cut, or heated cannot be moved and if all the fire hazards cannot be removed, positive means shall be taken to confine the heat, sparks, and slag, and to protect the immovable fire hazards from them.', '59');
INSERT INTO `app_case_sf_code` VALUES ('290', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.352(d)', 'Suitable fire extinguishing equipment shall be immediately available in the work area and shall be maintained in a state of readiness for instant use.', '59');
INSERT INTO `app_case_sf_code` VALUES ('291', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.352(e)', 'Fire Watch. When the welding, cutting, or heating operation is such that normal fire prevention precautions are not sufficient, additional personnel shall be assigned to guard against fire while the actual welding, cutting, or heating operation is being performed, and for a sufficient period of time after completion of the work to ensure that no possibility of fire exists. Such personnel shall be instructed as to the specific anticipated fire hazards and how the firefighting equipment provided is to be used.', '59');
INSERT INTO `app_case_sf_code` VALUES ('292', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.403(a)', 'Approval. All electrical conductors and equipment shall be approved.', '60');
INSERT INTO `app_case_sf_code` VALUES ('293', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.403(b)(1)', 'Examination, installation and use of equipment.  The employer shall ensure that electrical equipment is free from recognized hazards that are likely to cause death or serious physical harm to employees. Safety of equipment shall be determined on the basis of the following considerations: suitability; mechanical strength and durability; electrical insulation; heating effects under conditions of use; arching effects; classification; other factors.', '60');
INSERT INTO `app_case_sf_code` VALUES ('294', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.403(e)', 'Splices. Conductors shall be spliced or joined with splicing devices designed for the use or by brazing, welding, or soldering with a fusible metal or alloy. Soldered splices shall first be so spliced or joined as to be mechanically and electrically secure without solder and then soldered. All splices and joints and the free ends of conductors shall be covered with an insulation equivalent to that of the conductors or with an insulating device designed for the purpose.', '60');
INSERT INTO `app_case_sf_code` VALUES ('295', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.403(i)(1)', 'Working space about electric equipment. Sufficient access and working space shall be provided and maintained about all electric equipment to permit ready and safe operation and maintenance of such equipment.', '60');
INSERT INTO `app_case_sf_code` VALUES ('296', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.403(i)(2)', 'Guarding of live parts. Except as required or permitted elsewhere in this subpart, live parts of electric equipment operating at 50 volts or more shall be guarded against accidental contact by cabinets or other forms of enclosures, or by any of the following means: by location in a room, vault, or similar enclosure that is accessible only to qualified persons; By partitions or screens so arranged that only qualified persons will have access to the space within reach of the live parts. Any openings in such partitions or screens shall be so sized and located that persons are not likely to come into accidental contact with the live parts or to bring conducting objects into contact with them; By location on a balcony, gallery, or platform so elevated and arranged as to exclude unqualified persons; By elevation of 8 feet (2.44 m) or more above the floor or other working surface and so installed as to exclude unqualified persons.', '60');
INSERT INTO `app_case_sf_code` VALUES ('297', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.403(i)(2)(iii)', 'Entrances to rooms and other guarded locations containing exposed live parts shall be marked with conspicuous warning signs forbidding unqualified persons to enter.', '60');
INSERT INTO `app_case_sf_code` VALUES ('298', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.404(b)(1)(ii)', 'Branch circuits. Ground-fault circuit interrupters. All 120-volt, single-phase, 15- and 20-ampere receptacle outlets on construction sites, which are not a part of the permanent wiring of the building or structure and which are in use by employees, shall have approved ground-fault circuit interrupters for personnel protection. Receptacles on a two-wire, single-phase portable or vehicle-mounted generator rated not more than 5kW, where the circuit conductors of the generator are insulated from the generator frame and all other grounded surfaces, need not be protected with ground-fault circuit interrupters.', '61');
INSERT INTO `app_case_sf_code` VALUES ('299', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(a)(2)(ii)(B)', 'Temporary wiring . General requirements. Branch circuits shall originate in a power outlet or panelboard. Conductors shall be run as multiconductor cord or cable assemblies or open conductors, or shall be run in raceways. All conductors shall be protected by overcurrent devices at their ampacity. Runs of open conductors shall be located where the conductors will not be subject to physical damage, and the conductors shall be fastened at intervals not exceeding 10 feet (3.05 m). No branch-circuit conductors shall be laid on the floor. Each branch circuit that supplies receptacles or fixed equipment shall contain a separate equipment grounding conductor if the branch circuit is run as open conductors.', '62');
INSERT INTO `app_case_sf_code` VALUES ('300', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(a)(2)(ii)(E)', 'All lamps for general illumination shall be protected from accidental contact or breakage. Metal-case sockets shall be grounded.', '62');
INSERT INTO `app_case_sf_code` VALUES ('301', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(a)(2)(ii)(F)', 'Temporary lights shall not be suspended by their electric cords unless cords and lights are designed for this means of suspension.', '62');
INSERT INTO `app_case_sf_code` VALUES ('302', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(a)(2)(ii)(G)', 'Portable electric lighting used in wet and/or other conductive locations, as for example, drums, tanks, and vessels, shall be operated at 12 volts or less. However, 120-volt lights may be used if protected by a ground-fault circuit interrupter.', '62');
INSERT INTO `app_case_sf_code` VALUES ('303', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(a)(2)(ii)(I)', 'Flexible cords and cables shall be protected from damage. Sharp corners and projections shall be avoided. Flexible cords and cables may pass through doorways or other pinch points, if protection is provided to avoid damage.', '62');
INSERT INTO `app_case_sf_code` VALUES ('304', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(a)(2)(ii)(J)', 'Extension cord sets used with portable electric tools and appliances shall be of three-wire type and shall be designed for hard or extra-hard usage. Flexible cords used with temporary and portable lights shall be designed for hard or extra-hard usage.', '62');
INSERT INTO `app_case_sf_code` VALUES ('305', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(b)(1)', 'Cabinets, boxes, and fittings.  Conductors entering boxes, cabinets, or fittings. Conductors entering boxes, cabinets, or fittings shall be protected from abrasion, and openings through which conductors enter shall be effectively closed. Unused openings in cabinets, boxes, and fittings shall also be effectively closed.', '62');
INSERT INTO `app_case_sf_code` VALUES ('306', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(b)(2)', 'Covers and canopies. All pull boxes, junction boxes, and fittings shall be provided with covers. If metal covers are used, they shall be grounded. In energized installations each outlet box shall have a cover, faceplate, or fixture canopy. Covers of outlet boxes having holes through which flexible cord pendants pass shall be provided with bushings designed for the purpose or shall have smooth, well-rounded surfaces on which the cords may bear.', '62');
INSERT INTO `app_case_sf_code` VALUES ('307', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(d)', 'Switchboards and panelboards. Switchboards that have any exposed live parts shall be located in permanently dry locations and accessible only to qualified persons. Panelboards shall be mounted in cabinets, cutout boxes, or enclosures designed for the purpose and shall be dead front. However, panelboards other than the dead front externally-operable type are permitted where accessible only to qualified persons. Exposed blades of knife switches shall be dead when open.', '62');
INSERT INTO `app_case_sf_code` VALUES ('308', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(e)(1)', 'Enclosures for damp or wet locations. Cabinets, fittings, and boxes. Cabinets, cutout boxes, fittings, boxes, and panelboard enclosures in damp or wet locations shall be installed so as to prevent moisture or water from entering and accumulating within the enclosures. In wet locations the enclosures shall be weatherproof.', '62');
INSERT INTO `app_case_sf_code` VALUES ('309', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(e)(1)', 'Switches and circuit breakers. Switches, circuit breakers, and switchboards installed in wet locations shall be enclosed in weatherproof enclosures.', '62');
INSERT INTO `app_case_sf_code` VALUES ('310', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(g)(2)(iii)', 'Flexible cords shall be used only in continuous lengths without splice or tap. Hard service flexible cords No. 12 or larger may be repaired if spliced so that the splice retains the insulation, outer sheath properties, and usage characteristics of the cord being spliced.', '62');
INSERT INTO `app_case_sf_code` VALUES ('311', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(g)(2)(iv)', 'Strain relief. Flexible cords shall be connected to devices and fittings so that strain relief is provided which will prevent pull from being directly transmitted to joints or terminal screws.', '62');
INSERT INTO `app_case_sf_code` VALUES ('312', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(g)(2)(v)', 'Cords passing through holes. Flexible cords and cables shall be protected by bushings or fittings where passing through holes in covers, outlet boxes, or similar enclosures.', '62');
INSERT INTO `app_case_sf_code` VALUES ('313', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(j)(1)(iiii)(A)', 'Metal shell, paper lined lampholders shall not be used.', '62');
INSERT INTO `app_case_sf_code` VALUES ('314', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.405(j)(2)(ii)', 'Damp and wet locations. A receptacle installed in a wet or damp location shall be designed for the location.', '62');
INSERT INTO `app_case_sf_code` VALUES ('315', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.416(a)(1)', 'No employer shall permit an employee to work in such proximity to any part of an electric power circuit that the employee could contact the electric power circuit in the course of work, unless the employee is protected against electric shock by deenergizing the circuit and grounding it or by guarding it effectively by insulation or other means.', '63');
INSERT INTO `app_case_sf_code` VALUES ('316', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.416(b)(2)', 'Working spaces, walkways, and similar locations shall be kept clear of cords so as not to create a hazard to employees.', '63');
INSERT INTO `app_case_sf_code` VALUES ('317', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.416(e)(1)', 'Cords and cables. Worn or frayed electric cords or cables shall not be used.', '63');
INSERT INTO `app_case_sf_code` VALUES ('318', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.416(e)(2)', 'Cords and cables. Extension cords shall not be fastened with staples, hung from nails, or suspended by wire.', '63');
INSERT INTO `app_case_sf_code` VALUES ('319', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.417(a)', 'Controls. Controls that are to be deactivated during the course of work on energized or deenergized equipment or circuits shall be tagged.', '64');
INSERT INTO `app_case_sf_code` VALUES ('320', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.417(b)', 'Equipment and circuits. Equipment or circuits that are deenergized shall be rendered inoperative and shall have tags attached at all points where such equipment or circuits can be energized.', '64');
INSERT INTO `app_case_sf_code` VALUES ('321', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(a)(6)', 'Capacity. Scaffolds shall be designed by a qualified person and shall be constructed and loaded in accordance with that design.', '65');
INSERT INTO `app_case_sf_code` VALUES ('322', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(b)(1)(i)', 'Each platform unit (e.g., scaffold plank, fabricated plank, fabricated deck, or fabricated platform) shall be installed so that the space between adjacent units and the space between the platform and the uprights is no more than 1 inch (2.5 cm) wide, except where the employer can demonstrate that a wider space is necessary (for example, to fit around uprights when side brackets are used to extend the width of the platform).', '65');
INSERT INTO `app_case_sf_code` VALUES ('323', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(b)(5)(i)', 'Each end of a platform 10 feet or less in length shall not extend over its support more than 12 inches (30 cm) unless the platform is designed and installed so that the cantilevered portion of the platform is able to support employees and/or materials without tipping, or has guardrails which block employee access to the cantilevered end.', '65');
INSERT INTO `app_case_sf_code` VALUES ('324', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(b)(7)', 'On scaffolds where platforms are overlapped to create a long platform, the overlap shall occur only over supports, and shall not be less than 12 inches unless the platforms are nailed together or otherwise restrained to prevent movement.', '65');
INSERT INTO `app_case_sf_code` VALUES ('325', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(b)(8)', 'At all points of a scaffold where the platform changes direction, such as turning a corner, any platform that rests on a bearer at an angle other than a right angle shall be laid first, and platforms which rest at right angles over the same bearer shall be laid second, on top of the first platform.', '65');
INSERT INTO `app_case_sf_code` VALUES ('326', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(b)(9)', 'Wood platforms shall not be covered with opaque finishes, except that platform edges may be covered or marked for identification. Platforms may be coated periodically with wood preservatives, fire-retardant finishes, and slip-resistant finishes; however, the coating may not obscure the top or bottom wood surfaces.', '65');
INSERT INTO `app_case_sf_code` VALUES ('327', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(b)(10)', 'Scaffold components manufactured by different manufacturers shall not be intermixed unless the components fit together without force and the scaffold\'s structural integrity is maintained by the user. Scaffold components manufactured by different manufacturers shall not be modified in order to intermix them unless a competent person determines the resulting scaffold is structurally sound.', '65');
INSERT INTO `app_case_sf_code` VALUES ('328', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(b)(11)', 'Scaffold components made of dissimilar metals shall not be used together unless a competent person has determined that galvanic action will not reduce the strength of any component to a level below that required by paragraph (a)(1) of this section.', '65');
INSERT INTO `app_case_sf_code` VALUES ('329', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(c)(2)', 'Supported scaffold poles, legs, posts, frames, and uprights shall bear on base plates and mud sills or other adequate firm foundation.', '65');
INSERT INTO `app_case_sf_code` VALUES ('330', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(c)(2)(i)', 'Footings shall be level, sound, rigid, and capable of supporting the loaded scaffold without settling or displacement.', '65');
INSERT INTO `app_case_sf_code` VALUES ('331', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(c)(2)(ii)', 'Unstable objects shall not be used to support scaffolds or platform units.', '65');
INSERT INTO `app_case_sf_code` VALUES ('332', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(c)(2)(iii)', 'Unstable objects shall not be used as working platforms.', '65');
INSERT INTO `app_case_sf_code` VALUES ('333', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(c)(2)(iv)', 'Front-end loaders and similar pieces of equipment shall not be used to support scaffold platforms unless they have been specifically designed by the manufacturer for such use.', '65');
INSERT INTO `app_case_sf_code` VALUES ('334', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(c)(2)(v)', 'Fork-lifts shall not be used to support scaffold platforms unless the entire platform is attached to the fork and the fork-lift is not moved horizontally while the platform is occupied.', '65');
INSERT INTO `app_case_sf_code` VALUES ('335', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(c)(3)', 'Supported scaffold poles, legs, posts, frames, and uprights shall be plumb and braced to prevent swaying and displacement.', '65');
INSERT INTO `app_case_sf_code` VALUES ('336', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(1)', 'Criteria for suspension scaffolds. All suspension scaffold support devices, such as outrigger beams, cornice hooks, parapet clamps, and similar devices, shall rest on surfaces capable of supporting at least 4 times the load imposed on them by the scaffold operating at the rated load of the hoist (or at least 1.5 times the load imposed on them by the scaffold at the stall capacity of the hoist, whichever is greater).', '65');
INSERT INTO `app_case_sf_code` VALUES ('337', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(3)(i)', 'Before the scaffold is used, direct connections shall be evaluated by a competent person who shall confirm, based on the evaluation, that the supporting surfaces are capable of supporting the loads to be imposed. In addition, masons\' multi-point adjustable suspension scaffold connections shall be designed by an engineer experienced in such scaffold design.', '65');
INSERT INTO `app_case_sf_code` VALUES ('338', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(3)(ii)', 'Counterweights shall be made of non-flowable material. Sand, gravel and similar materials that can be easily dislocated shall not be used as counterweights.', '65');
INSERT INTO `app_case_sf_code` VALUES ('339', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(3)(iii)', 'Only those items specifically designed as counterweights shall be used to counterweight scaffold systems. Construction materials such as, but not limited to, masonry units and rolls of roofing felt, shall not be used as counterweights.', '65');
INSERT INTO `app_case_sf_code` VALUES ('340', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(3)(iv)', 'Counterweights shall be secured by mechanical means to the outrigger beams to prevent accidental displacement.', '65');
INSERT INTO `app_case_sf_code` VALUES ('341', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(3)(vii)', 'Tiebacks shall be equivalent in strength to the suspension ropes.', '65');
INSERT INTO `app_case_sf_code` VALUES ('342', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(3)(ix)', 'Tiebacks shall be secured to a structurally sound anchorage on the building or structure. Sound anchorages include structural members, but do not include standpipes, vents, other piping systems, or electrical conduit.', '65');
INSERT INTO `app_case_sf_code` VALUES ('343', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(7)', 'The use of repaired wire rope as suspension rope is prohibited.', '65');
INSERT INTO `app_case_sf_code` VALUES ('344', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(10)', 'Ropes shall be inspected for defects by a competent person prior to each workshift and after every occurrence which could affect a rope\'s integrity.', '65');
INSERT INTO `app_case_sf_code` VALUES ('345', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(d)(12)(i-vI)', 'When wire rope clips are used on suspension scaffolds: there shall be a minimum of 3 wire rope clips installed, with the clips a minimum of 6 rope diameters apart; clips shall be installed according to the manufacturer\'s recommendations; clips shall be retightened to the manufacturer\'s recommendations after the initial loading; clips shall be inspected and retightened to the manufacturer\'s recommendations at the start of each workshift thereafter; U-bolt clips shall not be used at the point of suspension for any scaffold hoist; When U-bolt clips are used, the U-bolt shall be placed over the dead end of the rope, and the saddle shall be placed over the live end of the rope.', '65');
INSERT INTO `app_case_sf_code` VALUES ('346', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(f)(3)', 'Use. Scaffolds and scaffold components shall be inspected for visible defects by a competent person before each work shift, and after any occurrence which could affect a scaffold\'s structural integrity.', '65');
INSERT INTO `app_case_sf_code` VALUES ('347', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(f)(15)(i-iv)', 'Ladders shall not be used on scaffolds to increase the working level height of employees, except on large area scaffolds where employers have satisfied the following criteria: when the ladder is placed against a structure which is not a part of the scaffold, the scaffold shall be secured against the sideways thrust exerted by the ladder; the platform units shall be secured to the scaffold to prevent their movement; the ladder legs shall be on the same platform or other means shall be provided to stabilize the ladder against unequal platform deflection, and The ladder legs shall be secured to prevent them from slipping or being pushed off the platform.', '65');
INSERT INTO `app_case_sf_code` VALUES ('348', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(f)(16)', 'Platforms shall not deflect more than 1/60 of the span when loaded.', '65');
INSERT INTO `app_case_sf_code` VALUES ('349', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(g)(1)(vi)', 'Fall protection. Each employee performing overhand bricklaying operations from a supported scaffold shall be protected from falling from all open sides and ends of the scaffold (except at the side next to the wall being laid) by the use of a personal fall arrest system or guardrail system (with minimum 200 pound toprail capacity).', '65');
INSERT INTO `app_case_sf_code` VALUES ('350', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(g)(2)', 'Fall protection. The employer shall have a competent person determine the feasibility and safety of providing fall protection for employees erecting or dismantling supported scaffolds. Employers are required to provide fall protection for employees erecting or dismantling supported scaffolds where the installation and use of such protection is feasible and does not create a greater hazard.', '65');
INSERT INTO `app_case_sf_code` VALUES ('351', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(g)(3)(I)', 'Fall protection. When vertical lifelines are used, they shall be fastened to a fixed safe point of anchorage, shall be independent of the scaffold, and shall be protected from sharp edges and abrasion. Safe points of anchorage include structural members of buildings, but do not include standpipes, vents, other piping systems, electrical conduit, outrigger beams, or counterweights.', '65');
INSERT INTO `app_case_sf_code` VALUES ('352', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(g)(4)(xv)', 'Crossbracing is acceptable in place of a midrail when the crossing point of two braces is between 20 inches (0.5 m) and 30 inches (0.8 m) above the work platform or as a toprail when the crossing point of two braces is between 38 inches (0.97 m) and 48 inches (1.3 m) above the work platform. The end points at each upright shall be no more than 48 inches (1.3 m) apart.', '65');
INSERT INTO `app_case_sf_code` VALUES ('353', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.451(h)(1)', 'Falling object protection. In addition to wearing hardhats each employee on a scaffold shall be provided with additional protection from falling hand tools, debris, and other small objects through the installation of toeboards, screens, or guardrail systems, or through the erection of debris nets, catch platforms, or canopy structures that contain or deflect the falling objects. When the falling objects are too large, heavy or massive to be contained or deflected by any of the above-listed measures, the employer shall place such potential falling objects away from the edge of the surface from which they could fall and shall secure those materials as necessary to prevent their falling.', '65');
INSERT INTO `app_case_sf_code` VALUES ('354', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(w)(1)', 'Scaffolds shall be braced by cross, horizontal, or diagonal braces, or combination thereof, to prevent racking or collapse of the scaffold and to secure vertical members together laterally so as to automatically square and align the vertical members. Scaffolds shall be plumb, level, and squared. All brace connections shall be secured.', '66');
INSERT INTO `app_case_sf_code` VALUES ('355', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(w)(2)', 'Scaffold casters and wheels shall be locked with positive wheel and/or wheel and swivel locks, or equivalent means, to prevent movement of the scaffold while the scaffold is used in a stationary manner.', '66');
INSERT INTO `app_case_sf_code` VALUES ('356', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(w)(6)(i)', 'Occupied mobile scaffold movement. The surface on which the scaffold is being moved is within 3 degrees of level, and free of pits, holes, and obstructions.', '66');
INSERT INTO `app_case_sf_code` VALUES ('357', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(w)(6)(ii)', 'Occupied mobile scaffold movement. The height to base width ratio of the scaffold during movement is two to one or less, unless the scaffold is designed and constructed to meet or exceed nationally recognized stability test requirements such as those listed in paragraph (x) of appendix A to this subpart (ANSI/SIA A92.5 and A92.6).', '66');
INSERT INTO `app_case_sf_code` VALUES ('358', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(w)(6)(iii)', 'Occupied mobile scaffold movement. Outrigger frames, when used, are installed on both sides of the scaffold', '66');
INSERT INTO `app_case_sf_code` VALUES ('359', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(w)(6)(iv)', 'Occupied mobile scaffold movement. When power systems are used, the propelling force is applied directly to the wheels, and does not produce a speed in excess of 1 foot per second (.3 mps).', '66');
INSERT INTO `app_case_sf_code` VALUES ('360', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(w)(6)(v)', 'Occupied mobile scaffold movement. No employee is on any part of the scaffold which extends outward beyond the wheels, casters, or other supports', '66');
INSERT INTO `app_case_sf_code` VALUES ('361', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(y)(1)', 'Stilts. An employee may wear stilts on a scaffold only if it is a large area scaffold.', '66');
INSERT INTO `app_case_sf_code` VALUES ('362', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(y)(2)', 'Stilts. When an employee is using stilts on a large area scaffold where a guardrail system is used to provide fall protection, the guardrail system shall be increased in height by an amount equal to the height of the stilts being used by the employee.', '66');
INSERT INTO `app_case_sf_code` VALUES ('363', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(y)(3)', 'Stilts. Surfaces on which stilts are used shall be flat and free of pits, holes and obstructions, such as debris, as well as other tripping and falling hazards.', '66');
INSERT INTO `app_case_sf_code` VALUES ('364', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.452(y)(4)', 'Stilts. Stilts shall be properly maintained. Any alteration of the original equipment shall be approved by the manufacturer.', '66');
INSERT INTO `app_case_sf_code` VALUES ('365', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.453(b)(2)(i)', 'Lift controls shall be tested each day prior to use to determine that such controls are in safe working condition.', '67');
INSERT INTO `app_case_sf_code` VALUES ('366', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.453(b)(2)(ii)', 'Only authorized persons shall operate an aerial lift.', '67');
INSERT INTO `app_case_sf_code` VALUES ('367', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.453(b)(2)(iii)', 'Belting off to an adjacent pole, structure, or equipment while working from an aerial lift shall not be permitted.', '67');
INSERT INTO `app_case_sf_code` VALUES ('368', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.453(b)(2)(iv)', 'Employees shall always stand firmly on the floor of the basket, and shall not sit or climb on the edge of the basket or use planks, ladders, or other devices for a work position.', '67');
INSERT INTO `app_case_sf_code` VALUES ('369', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.453(b)(2)(v)', 'A body belt shall be worn and a lanyard attached to the boom or basket when working from an aerial lift.', '67');
INSERT INTO `app_case_sf_code` VALUES ('370', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.453(b)(2)(vi)', 'Boom and basket load limits specified by the manufacturer shall not be exceeded.', '67');
INSERT INTO `app_case_sf_code` VALUES ('371', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.453(b)(2)(ix)', 'Articulating boom and extensible boom platforms, primarily designed as personnel carriers, shall have both platform (upper) and lower controls. Upper controls shall be in or beside the platform within easy reach of the operator. Lower controls shall provide for overriding the upper controls. Controls shall be plainly marked as to their function. Lower level controls shall not be operated unless permission has been obtained from the employee in the lift, except in case of emergency.', '67');
INSERT INTO `app_case_sf_code` VALUES ('372', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(2)', 'The employer shall determine if the walking/working surfaces on which its employees are to work have the strength and structural integrity to support employees safely. Employees shall be allowed to work on those surfaces only when the surfaces have the requisite strength and structural integrity.', '68');
INSERT INTO `app_case_sf_code` VALUES ('373', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(1)', 'Unprotected sides and edges. Each employee on a walking/working surface (horizontal and vertical surface) with an unprotected side or edge which is 6 feet (1.8 m) or more above a lower level shall be protected from falling by the use of guardrail systems, safety net systems, or personal fall arrest systems.', '68');
INSERT INTO `app_case_sf_code` VALUES ('374', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(2)(i)', 'Leading edges. Each employee who is constructing a leading edge 6 feet (1.8 m) or more above lower levels shall be protected from falling by guardrail systems, safety net systems, or personal fall arrest systems. Exception: When the employer can demonstrate that it is infeasible or creates a greater hazard to use these systems, the employer shall develop and implement a fall protection plan which meets the requirements of paragraph (k) of � 1926.502.', '68');
INSERT INTO `app_case_sf_code` VALUES ('375', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(2)(ii)', 'Each employee on a walking/working surface 6 feet (1.8 m) or more above a lower level where leading edges are under construction, but who is not engaged in the leading edge work, shall be protected from falling by a guardrail system, safety net system, or personal fall arrest system. If a guardrail system is chosen to provide the fall protection, and a controlled access zone has already been established for leading edge work, the control line may be used in lieu of a guardrail along the edge that parallels the leading edge.', '68');
INSERT INTO `app_case_sf_code` VALUES ('376', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(3)', 'Hoist areas. Each employee in a hoist area shall be protected from falling 6 feet (1.8 m) or more to lower levels by guardrail systems or personal fall arrest systems. If guardrail systems, [or chain, gate, or guardrail] or portions thereof, are removed to facilitate the hoisting operation (e.g., during landing of materials), and an employee must lean through the access opening or out over the edge of the access opening (to receive or guide equipment and materials, for example), that employee shall be protected from fall hazards by a personal fall arrest system.', '68');
INSERT INTO `app_case_sf_code` VALUES ('377', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(4)(i)', 'Holes. Each employee on walking/working surfaces shall be protected from falling through holes (including skylights) more than 6 feet (1.8 m) above lower levels, by personal fall arrest systems, covers, or guardrail systems erected around such holes.', '68');
INSERT INTO `app_case_sf_code` VALUES ('378', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(4)(ii)', 'Holes. Each employee on a walking/working surface shall be protected from tripping in or stepping into or through holes (including skylights) by covers.', '68');
INSERT INTO `app_case_sf_code` VALUES ('379', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(4)(iii)', 'Holes. Each employee on a walking/working surface shall be protected from objects falling through holes (including skylights) by covers.', '68');
INSERT INTO `app_case_sf_code` VALUES ('380', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(5)', 'Formwork and reinforcing steel. Each employee on the face of formwork or reinforcing steel shall be protected from falling 6 feet (1.8 m) or more to lower levels by personal fall arrest systems, safety net systems, or positioning device systems.', '68');
INSERT INTO `app_case_sf_code` VALUES ('381', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(6)', 'Ramps, runways, and other walkways. Each employee on ramps, runways, and other walkways shall be protected from falling 6 feet (1.8 m) or more to lower levels by guardrail systems.', '68');
INSERT INTO `app_case_sf_code` VALUES ('382', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(7)(i)', 'Each employee at the edge of an excavation 6 feet or more in depth shall be protected from falling by guardrail systems, fences, or barricades when the excavations are not readily seen because of plant growth or other visual barrier.', '68');
INSERT INTO `app_case_sf_code` VALUES ('383', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(7)(ii)', 'Excavations. Each employee at the edge of a well, pit, shaft, and similar excavation 6 feet (1.8 m) or more in depth shall be protected from falling by guardrail systems, fences, barricades, or covers.', '68');
INSERT INTO `app_case_sf_code` VALUES ('384', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(9)(i)', 'Overhand bricklaying and related work. Except as otherwise provided in paragraph (b) of this section, each employee performing overhand bricklaying and related work 6 feet (1.8 m) or more above lower levels, shall be protected from falling by guardrail systems, safety net systems, personal fall arrest systems, or shall work in a controlled access zone.', '68');
INSERT INTO `app_case_sf_code` VALUES ('385', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(9)(ii)', 'Each employee reaching more than 10 inches below the level of the walking/working surface on which they are working, shall be protected from falling by a guardrail system, safety net system, or personal fall arrest system.', '68');
INSERT INTO `app_case_sf_code` VALUES ('386', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(10)', 'Roofing work on Low-slope roofs. Except as otherwise provided in paragraph (b) of this section, each employee engaged in roofing activities on low-slope roofs, with unprotected sides and edges 6 feet (1.8 m) or more above lower levels shall be protected from falling by guardrail systems, safety net systems, personal fall arrest systems, or a combination of warning line system and guardrail system, warning line system and safety net system, or warning line system and personal fall arrest system, or warning line system and safety monitoring system. Or, on roofs 50-feet (15.25 m) or less in width (see appendix A to subpart M of this part), the use of a safety monitoring system alone [i.e. without the warning line system] is permitted.', '68');
INSERT INTO `app_case_sf_code` VALUES ('387', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(12)', 'Precast concrete erection. Each employee engaged in the erection of precast concrete members (including, but not limited to the erection of wall panels, columns, beams, and floor and roof �tees�) and related operations such as grouting of precast concrete members, who is 6 feet (1.8 m) or more above lower levels shall be protected from falling by guardrail systems, safety net systems, or personal fall arrest systems, unless another provision in paragraph (b) of this section provides for an alternative fall protection measure. Exception: When the employer can demonstrate that it is infeasible or creates a greater hazard to use these systems, the employer shall develop and implement a fall protection plan which meets the requirements of paragraph (k) of � 1926.502.', '68');
INSERT INTO `app_case_sf_code` VALUES ('388', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(b)(14)', 'Wall openings. Each employee working on, at, above, or near wall openings (including those with chutes attached) where the outside bottom edge of the wall opening is 6 feet or more above lower levels and the inside bottom edge of the wall opening is less than 39 inches above the walking/working surface, shall be protected from falling by the use of a guardrail system, a safety net system, or a personal fall arrest system.', '68');
INSERT INTO `app_case_sf_code` VALUES ('389', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(c)(1)', 'Protection from falling objects. When an employee is exposed to falling objects, the employer shall have each employee wear a hard hat and shall implement one of the following measures: Erect toeboards, screens, or guardrail systems to prevent objects from falling from higher levels.', '68');
INSERT INTO `app_case_sf_code` VALUES ('390', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(c)(2)', 'Protection from falling objects. Erect a canopy structure and keep potential fall objects far enough from the edge of the higher level so that those objects would not go over the edge if they were accidentally displaced.', '68');
INSERT INTO `app_case_sf_code` VALUES ('391', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.501(c)(3)', 'Protection from falling objects. Barricade the area to which objects could fall, prohibit employees from entering the barricaded area, and keep objects that may fall far enough away from the edge of a higher level so that those objects would not go over the edge if they were accidentally displaced.', '68');
INSERT INTO `app_case_sf_code` VALUES ('392', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(1)', 'Guardrail systems. Top edge height of top rails, or equivalent guardrail system members, shall be 42 inches (1.1 m) plus or minus 3 inches (8 cm) above the walking/working level. When conditions warrant, the height of the top edge may exceed the 45-inch height, provided the guardrail system meets all other criteria of this paragraph. Note: When employees are using stilts, the top edge height of the top rail, or equivalent member, shall be increased an amount equal to the height of the stilts.', '69');
INSERT INTO `app_case_sf_code` VALUES ('393', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(2)', 'Guardrail systems. Midrails, screens, mesh, intermediate vertical members, or equivalent intermediate structural members shall be installed between the top edge of the guardrail system and the walking/working surface when there is no wall or parapet wall at least 21 inches (53 cm) high.', '69');
INSERT INTO `app_case_sf_code` VALUES ('394', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(3)', 'Guardrail systems. Guardrail systems shall be capable of withstanding, without failure, a force of at least 200 pounds (890 N) applied within 2 inches (5.1 cm) of the top edge, in any outward or downward direction, at any point along the top edge.', '69');
INSERT INTO `app_case_sf_code` VALUES ('395', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(4)', 'Guardrail systems. When the 200 pound test load specified in paragraph (b)(3) of this section is applied in a downward direction, the top edge of the guardrail shall not deflect to a height less than 39 inches (1.0 m) above the walking/working level. Guardrail system components selected and constructed in accordance with the appendix B to subpart M of this part will be deemed to meet this requirement.', '69');
INSERT INTO `app_case_sf_code` VALUES ('396', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(5)', 'Midrails, screens, mesh, intermediate vertical members, solid panels, and equivalent structural members shall be capable of withstanding, without failure, a force of at least 150 pounds applied in any downward or outward direction at any point along the rail or other member.', '69');
INSERT INTO `app_case_sf_code` VALUES ('397', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(6)', 'Guardrail systems. Guardrail systems shall be so surfaced as to prevent injury to an employee from punctures or lacerations, and to prevent snagging of clothing.', '69');
INSERT INTO `app_case_sf_code` VALUES ('398', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(7)', 'Guardrail systems. The ends of all top rails and midrails shall not overhang the terminal posts, except where such overhang does not constitute a projection hazard.', '69');
INSERT INTO `app_case_sf_code` VALUES ('399', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(8)', 'Guardrail systems. Steel banding and plastic banding shall not be used as top rails or midrails.', '69');
INSERT INTO `app_case_sf_code` VALUES ('400', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(9)', 'Guardrail systems. Top rails and midrails shall be at least one-quarter inch (0.6 cm) nominal diameter or thickness to prevent cuts and lacerations. If wire rope is used for top rails, it shall be flagged at not more than 6-foot (1.8 m) intervals with high-visibility material.', '69');
INSERT INTO `app_case_sf_code` VALUES ('401', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(10)', 'Guardrail systems. When guardrail systems are used at hoisting areas, a chain, gate or removable guardrail section shall be placed across the access opening between guardrail sections when hoisting operations are not taking place.', '69');
INSERT INTO `app_case_sf_code` VALUES ('402', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(11)', 'Guardrail systems. When guardrail systems are used at holes, they shall be erected on all unprotected sides or edges of the hole.', '69');
INSERT INTO `app_case_sf_code` VALUES ('403', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(12)', 'Guardrail systems. When guardrail systems are used around holes used for the passage of materials, the hole shall have not more than two sides provided with removable guardrail sections to allow the passage of materials. When the hole is not in use, it shall be closed over with a cover, or a guardrail system shall be provided along all unprotected sides or edges.', '69');
INSERT INTO `app_case_sf_code` VALUES ('404', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(13)', 'Guardrail systems. When guardrail systems are used around holes which are used as points of access (such as ladderways), they shall be provided with a gate, or be so offset that a person cannot walk directly into the hole.', '69');
INSERT INTO `app_case_sf_code` VALUES ('405', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(14)', 'Guardrail systems. Guardrail systems used on ramps and runways shall be erected along each unprotected side or edge.', '69');
INSERT INTO `app_case_sf_code` VALUES ('406', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(b)(15)', 'Guardrail systems. Manila, plastic or synthetic rope being used for top rails or midrails shall be inspected as frequently as necessary to ensure that it continues to meet the strength requirements of paragraph (b)(3) of this section.', '69');
INSERT INTO `app_case_sf_code` VALUES ('407', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(c)(1)', 'Safety net systems. Safety nets shall be installed as close as practicable under the walking/working surface on which employees are working, but in no case more than 30 feet (9.1 m) below such level. When nets are used on bridges, the potential fall area from the walking/working surface to the net shall be unobstructed.', '69');
INSERT INTO `app_case_sf_code` VALUES ('408', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(1)', 'Personal fall arrest systems. Connectors shall be drop forged, pressed or formed steel, or made of equivalent materials.', '69');
INSERT INTO `app_case_sf_code` VALUES ('409', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(2)', 'Connectors shall have a corrosion-resistant finish, and all surfaces and edges shall be smooth to prevent damage to interfacing parts of the system.', '69');
INSERT INTO `app_case_sf_code` VALUES ('410', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(3)', 'Dee-rings and snaphooks shall have a minimum tensile strength of 5,000 pounds.', '69');
INSERT INTO `app_case_sf_code` VALUES ('411', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(5)', 'Only locking type snaphooks shall be used.', '69');
INSERT INTO `app_case_sf_code` VALUES ('412', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(6)(i)', 'Personal fall arrest systems. Unless the snaphook is a locking type and designed for the following connections, snaphooks shall not be engaged directly to webbing, rope or wire rope.', '69');
INSERT INTO `app_case_sf_code` VALUES ('413', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(6)(ii)', 'Personal fall arrest systems. Unless the snaphook is a locking type and designed for the following connections, snaphooks shall not be engaged to each other.', '69');
INSERT INTO `app_case_sf_code` VALUES ('414', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(6)(iii)', 'Personal fall arrest systems. Unless the snaphook is a locking type and designed for the following connections, snaphooks shall not be engaged to a Dee-ring to which another snaphook or other connector is attached.', '69');
INSERT INTO `app_case_sf_code` VALUES ('415', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(6)(iv)', 'Personal fall arrest systems. Unless the snaphook is a locking type and designed for the following connections, snaphooks shall not be engaged to a horizontal lifeline.', '69');
INSERT INTO `app_case_sf_code` VALUES ('416', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(6)(v)', 'Personal fall arrest systems. Unless the snaphook is a locking type and designed for the following connections, snaphooks shall not be engaged to any object which is incompatibly shaped or dimensioned in relation to the snaphook such that unintentional disengagement could occur by the connected object being able to depress the snaphook keeper and release itself.', '69');
INSERT INTO `app_case_sf_code` VALUES ('417', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(8)', 'Personal fall arrest systems. Horizontal lifelines shall be designed, installed, and used, under the supervision of a qualified person, as part of a complete personal fall arrest system, which maintains a safety factor of at least two.', '69');
INSERT INTO `app_case_sf_code` VALUES ('418', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(11)', 'Personal fall arrest systems. Lifelines shall be protected against being cut or abraded.', '69');
INSERT INTO `app_case_sf_code` VALUES ('419', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(12)', 'Personal fall arrest systems. Self-retracting lifelines and lanyards which automatically limit free fall distance to 2 feet (0.61 m) or less shall be capable of sustaining a minimum tensile load of 3,000 pounds (13.3 kN) applied to the device with the lifeline or lanyard in the fully extended position.', '69');
INSERT INTO `app_case_sf_code` VALUES ('420', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(13)', 'Personal fall arrest systems. Ropes and straps (webbing) used in lanyards, lifelines, and strength components of body belts and body harnesses shall be made from synthetic fibers.', '69');
INSERT INTO `app_case_sf_code` VALUES ('421', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(15)', 'Personal fall arrest systems. Anchorages used for attachment of personal fall arrest equipment shall be independent of any anchorage being used to support or suspend platforms and capable of supporting at least 5,000 pounds (22.2 kN) per employee attached.', '69');
INSERT INTO `app_case_sf_code` VALUES ('422', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(16)(iii)', 'Personal fall arrest systems. Anchorages used for attachment of personal fall arrest equipment shall be independent of any anchorage being used to support or suspend platforms and capable of supporting at least 5,000 pounds (22.2 kN) per employee attached, or shall be designed, installed, and be rigged such that an employee can neither free fall more than 6 feet (1.8 m), nor contact any lower level.', '69');
INSERT INTO `app_case_sf_code` VALUES ('423', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(17)', 'Personal fall arrest systems. The attachment point of the body belt shall be located in the center of the wearer\'s back. The attachment point of the body harness shall be located in the center of the wearer\'s back near shoulder level, or above the wearer\'s head.', '69');
INSERT INTO `app_case_sf_code` VALUES ('424', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(18)', 'Body belts, harnesses, and components shall be used only for employee protection (as part of a personal fall arrest system or positioning device system) and not to hoist materials.', '69');
INSERT INTO `app_case_sf_code` VALUES ('425', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(19)', 'Personal fall arrest systems and components subjected to impact loading shall be immediately removed from service and shall not be used again for employee protection until inspected and determined by a competent person to be undamaged and suitable for reuse.', '69');
INSERT INTO `app_case_sf_code` VALUES ('426', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(20)', 'Personal fall arrest systems. The employer shall provide for prompt rescue of employees in the event of a fall or shall assure that employees are able to rescue themselves', '69');
INSERT INTO `app_case_sf_code` VALUES ('427', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(21)', 'Personal fall arrest systems. Personal fall arrest systems shall be inspected prior to each use for wear, damage and other deterioration, and defective components shall be removed from service.', '69');
INSERT INTO `app_case_sf_code` VALUES ('428', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(d)(23)', 'Personal fall arrest systems. Personal fall arrest systems shall not be attached to guardrail systems, nor shall they be attached to hoists except as specified in other subparts of this part.', '69');
INSERT INTO `app_case_sf_code` VALUES ('429', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(1)', 'Warning line systems. The warning line shall be erected around all sides of the roof work area.', '69');
INSERT INTO `app_case_sf_code` VALUES ('430', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(1)(i)', 'Warning line systems. When mechanical equipment is not being used, the warning line shall be erected not less than 6 feet (1.8 m) from the roof edge.', '69');
INSERT INTO `app_case_sf_code` VALUES ('431', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(1)(ii)', 'Warning line systems. When mechanical equipment is being used, the warning line shall be erected not less than 6 feet (1.8 m) from the roof edge which is parallel to the direction of mechanical equipment operation, and not less than 10 feet (3.1 m) from the roof edge which is perpendicular to the direction of mechanical equipment operation.', '69');
INSERT INTO `app_case_sf_code` VALUES ('432', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(1)(iii)', 'Warning line systems. Points of access, materials handling areas, storage areas, and hoisting areas shall be connected to the work area by an access path formed by two warning lines.', '69');
INSERT INTO `app_case_sf_code` VALUES ('433', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(1)(iv)', 'Warning line systems. When the path to a point of access is not in use, a rope, wire, chain, or other barricade, equivalent in strength and height to the warning line, shall be placed across the path at the point where the path intersects the warning line erected around the work area, or the path shall be offset such that a person cannot walk directly into the work area.', '69');
INSERT INTO `app_case_sf_code` VALUES ('434', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(2)(i)', 'Warning line systems. The rope, wire, or chain shall be flagged at not more than 6-foot (1.8 m) intervals with high-visibility material.', '69');
INSERT INTO `app_case_sf_code` VALUES ('435', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(2)(ii)', 'The rope, wire, or chain shall be rigged and supported in such a way that its lowest point (including sag) is not less than 34 inches from the working/walking surface and its highest point is not more than 39 inches from the walking/working surface.', '69');
INSERT INTO `app_case_sf_code` VALUES ('436', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(2)(iii)', 'After being erected, with the rope, wire, or chain attached, stanchions shall be capable of resisting, without tipping over, a force of at least 16 pounds applied horizontally against the stanchion, 30 inches above the walking/working surface, perpendicular to the warning line, and in the direction of the floor, roof, or platform edge.', '69');
INSERT INTO `app_case_sf_code` VALUES ('437', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(f)(3)', 'Warning line systems. No employee shall be allowed in the area between a roof edge and a warning line unless the employee is performing roofing work in that area.', '69');
INSERT INTO `app_case_sf_code` VALUES ('438', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(g)(1)(i)', 'Controlled access zones. When control lines are used, they shall be erected not less than 6 feet (1.8 m) nor more than 25 feet (7.7 m) from the unprotected or leading edge, except when erecting precast concrete members.', '69');
INSERT INTO `app_case_sf_code` VALUES ('439', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(g)(1)(iii)', 'Controlled access zones. The control line shall extend along the entire length of the unprotected or leading edge and shall be approximately parallel to the unprotected or leading edge.', '69');
INSERT INTO `app_case_sf_code` VALUES ('440', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(g)(1)(iv)', 'Controlled access zones. The control line shall be connected on each side to a guardrail system or wall.', '69');
INSERT INTO `app_case_sf_code` VALUES ('441', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(g)(3)', 'Controlled access zones. Each line shall be flagged or otherwise clearly marked at not more than 6-foot (1.8 m) intervals with high-visibility material.', '69');
INSERT INTO `app_case_sf_code` VALUES ('442', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(h)(1)(i)', 'Safety monitoring system. The safety monitor shall be competent to recognize fall hazards.', '69');
INSERT INTO `app_case_sf_code` VALUES ('443', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(h)(1)(iii)', 'Safety monitoring system. The safety monitor shall be on the same walking/working surface and within visual sighting distance of the employee being monitored.', '69');
INSERT INTO `app_case_sf_code` VALUES ('444', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(h)(1)(iv)', 'Safety monitoring system. The safety monitor shall be close enough to communicate orally with the employee.', '69');
INSERT INTO `app_case_sf_code` VALUES ('445', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(h)(1)(v)', 'Safety monitoring system. The safety monitor shall not have other responsibilities which could take the monitor\'s attention from the monitoring function.', '69');
INSERT INTO `app_case_sf_code` VALUES ('446', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(h)(2)', 'Safety monitoring system. Mechanical equipment shall not be used or stored in areas where safety monitoring systems are being used to monitor employees engaged in roofing operations on low-slope roofs.', '69');
INSERT INTO `app_case_sf_code` VALUES ('447', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(h)(3)', 'Safety monitoring system. No employee, other than an employee engaged in roofing work [on low-sloped roofs] or an employee covered by a fall protection plan, shall be allowed in an area where an employee is being protected by a safety monitoring system.', '69');
INSERT INTO `app_case_sf_code` VALUES ('448', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(i)(1)', 'Covers. Covers located in roadways and vehicular aisles shall be capable of supporting, without failure, at least twice the maximum axle load of the largest vehicle expected to cross over the cover.', '69');
INSERT INTO `app_case_sf_code` VALUES ('449', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(i)(2)', 'Covers. All other covers shall be capable of supporting, without failure, at least twice the weight of employees, equipment, and materials that may be imposed on the cover at any one time.', '69');
INSERT INTO `app_case_sf_code` VALUES ('450', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(i)(3)', 'Covers. All covers shall be secured when installed so as to prevent accidental displacement by the wind, equipment, or employees', '69');
INSERT INTO `app_case_sf_code` VALUES ('451', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(i)(4)', 'Covers. All covers shall be color coded or they shall be marked with the word �HOLE� or �COVER� to provide warning of the hazard.', '69');
INSERT INTO `app_case_sf_code` VALUES ('452', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(j)(1)', 'Protection from falling objects. Toeboards, when used as falling object protection, shall be erected along the edge of the overhead walking/working surface for a distance sufficient to protect employees below.', '69');
INSERT INTO `app_case_sf_code` VALUES ('453', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(j)(3)', 'Protection from falling objects. Toeboards shall be a minimum of 31�2 inches (9 cm) in vertical height from their top edge to the level of the walking/working surface. They shall have not more than 1�4 inch (0.6 cm) clearance above the walking/working surface. They shall be solid or have openings not over 1 inch (2.5 cm) in greatest dimension.', '69');
INSERT INTO `app_case_sf_code` VALUES ('454', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(j)(4)', 'Protection from falling objects. Where tools, equipment, or materials are piled higher than the top edge of a toeboard, paneling or screening shall be erected from the walking/working surface or toeboard to the top of a guardrail system\'s top rail or midrail, for a distance sufficient to protect employees below.', '69');
INSERT INTO `app_case_sf_code` VALUES ('455', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(j)(6)(i)', 'Protection from falling objects. During the performance of overhand bricklaying and related work no materials or equipment except masonry and mortar shall be stored within 4 feet (1.2 m) of the working edge.', '69');
INSERT INTO `app_case_sf_code` VALUES ('456', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(j)(6)(ii)', 'Protection from falling objects. Excess mortar, broken or scattered masonry units, and all other materials and debris shall be kept clear from the work area by removal at regular intervals.', '69');
INSERT INTO `app_case_sf_code` VALUES ('457', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.502.(j)(8)', 'Protection from falling objects. Canopies, when used as falling object protection, shall be strong enough to prevent collapse and to prevent penetration by any objects which may fall onto the canopy.', '69');
INSERT INTO `app_case_sf_code` VALUES ('458', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.503(a)(1)', 'Training Program. The employer shall provide a training program for each employee who might be exposed to fall hazards. The program shall enable each employee to recognize the hazards of falling and shall train each employee in the procedures to be followed in order to minimize these hazards.', '70');
INSERT INTO `app_case_sf_code` VALUES ('459', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.503(a)(2)', 'Certification of training. The employer shall verify compliance with paragraph (a) of this section by preparing a written certification record. The written certification record shall contain the name or other identity of the employee trained, the date(s) of the training, and the signature of the person who conducted the training or the signature of the employer. If the employer relies on training conducted by another employer or completed prior to the effective date of this section, the certification record shall indicate the date the employer determined the prior training was adequate rather than the date of actual training.', '70');
INSERT INTO `app_case_sf_code` VALUES ('460', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.503(c)(1-3)', 'Retraining. When the employer has reason to believe that any affected employee who has already been trained does not have the understanding and skill required by paragraph (a) of this section, the employer shall retrain each such employee. Circumstances that mandate retraining include; Changes in the workplace render previous training obsolete; Changes in the types of fall protection systems or equipment to be used render previous training obsolete; Inadequacies in an affected employee\'s knowledge or use of fall protection systems or equipment indicate that the employee has not retained the requisite understanding or skill.', '70');
INSERT INTO `app_case_sf_code` VALUES ('461', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.600(a)(1)', 'General Requirements. All equipment left unattended at night, adjacent to a highway in normal use, or adjacent to construction areas where work is in progress, shall have appropriate lights or reflectors, or barricades equipped with appropriate lights or reflectors, to identify the location of the equipment.', '71');
INSERT INTO `app_case_sf_code` VALUES ('462', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.600(a)(3)(ii)', 'General Requirements. Whenever the equipment is parked, the parking brake shall be set. Equipment parked on inclines shall have the wheels chocked and the parking brake set.', '71');
INSERT INTO `app_case_sf_code` VALUES ('463', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.601(b)(2)(i)', 'Whenever visibility conditions warrant additional light, all vehicles, or combinations of vehicles, in use shall be equipped with at least two headlights and two taillights in operable condition.', '72');
INSERT INTO `app_case_sf_code` VALUES ('464', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.601(b)(2)(ii)', 'General Requirements. All vehicles, or combination of vehicles, shall have brake lights in operable condition regardless of light conditions.', '72');
INSERT INTO `app_case_sf_code` VALUES ('465', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.601(b)(3)', 'General Requirements. All vehicles shall be equipped with an adequate audible warning device at the operator\'s station and in an operable condition.', '72');
INSERT INTO `app_case_sf_code` VALUES ('466', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.601(b)(4)(i-ii)', 'General Requirements. No employer shall use any motor vehicle equipment having an obstructed view to the rear unless: the vehicle has a reverse signal alarm audible above the surrounding noise level or: The vehicle is backed up only when an observer signals that it is safe to do so.', '72');
INSERT INTO `app_case_sf_code` VALUES ('467', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.601(b)(5)', 'General Requirements. All vehicles with cabs shall be equipped with windshields and powered wipers. Cracked and broken glass shall be replaced. Vehicles operating in areas or under conditions that cause fogging or frosting of the windshields shall be equipped with operable defogging or defrosting devices.', '72');
INSERT INTO `app_case_sf_code` VALUES ('468', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.602(d)', 'Powered industrial truck operator training. The requirements applicable to construction work under this paragraph are identical to those set forth at Sec. 1910.178(l).', '73');
INSERT INTO `app_case_sf_code` VALUES ('469', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.604(a)(1)', 'Employees engaged in site clearing shall be protected from hazards of irritant and toxic plants and suitably instructed in the first aid treatment available.', '74');
INSERT INTO `app_case_sf_code` VALUES ('470', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(a)', 'Surface encumbrances. All surface encumbrances that are located so as to create a hazard to employees shall be removed or supported, as necessary, to safeguard employees.', '75');
INSERT INTO `app_case_sf_code` VALUES ('471', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(b)(1)', 'Underground installations. The estimated location of utility installations, such as sewer, telephone, fuel, electric, water lines, or any other underground installations that reasonably may be expected to be encountered during excavation work, shall be determined prior to opening an excavation.', '75');
INSERT INTO `app_case_sf_code` VALUES ('472', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(b)(2)', 'Underground installations. Utility companies or owners shall be contacted within established or customary local response times, advised of the proposed work, and asked to establish the location of the utility underground installations prior to the start of actual excavation. When utility companies or owners cannot respond to a request to locate underground utility installations within 24 hours (unless a longer period is required by state or local law), or cannot establish the exact location of these installations, the employer may proceed, provided the employer does so with caution, and provided detection equipment or other acceptable means to locate utility installations are used.', '75');
INSERT INTO `app_case_sf_code` VALUES ('473', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(b)(3)', 'Underground installations. When excavation operations approach the estimated location of underground installations, the exact location of the installations shall be determined by safe and acceptable means.', '75');
INSERT INTO `app_case_sf_code` VALUES ('474', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(b)(4)', 'Underground installations. While the excavation is open, underground installations shall be protected, supported or removed as necessary to safeguard employees.', '75');
INSERT INTO `app_case_sf_code` VALUES ('475', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(c)(2)', 'Access and egress. Means of egress from trench excavations. A stairway, ladder, ramp or other safe means of egress shall be located in trench excavations that are 4 feet (1.22 m) or more in depth so as to require no more than 25 feet (7.62 m) of lateral travel for employees.', '75');
INSERT INTO `app_case_sf_code` VALUES ('476', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(d)', 'Vehicular traffic. Employees exposed to public vehicular traffic shall be provided with, and shall wear, warning vests or other suitable garments marked with or made of reflectorized or high-visibility material.', '75');
INSERT INTO `app_case_sf_code` VALUES ('477', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(e)', 'Exposure to falling loads. No employee shall be permitted underneath loads handled by lifting or digging equipment. Employees shall be required to stand away from any vehicle being loaded or unloaded to avoid being struck by any spillage or falling materials. Operators may remain in the cabs of vehicles being loaded or unloaded when the vehicles are equipped, in accordance with � 1926.601(b)(6), to provide adequate protection for the operator during loading and unloading operations.', '75');
INSERT INTO `app_case_sf_code` VALUES ('478', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(f)', 'Warning system for mobile equipment.  When mobile equipment is operated adjacent to an excavation, or when such equipment is required to approach the edge of an excavation, and the operator does not have a clear and direct view of the edge of the excavation, a warning system shall be utilized such as barricades, hand or mechanical signals, or stop logs. If possible, the grade should be away from the excavation.', '75');
INSERT INTO `app_case_sf_code` VALUES ('479', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(g)(1)(i)', 'Where oxygen deficiency (atmospheres containing less than 19.5 percent oxygen) or a hazardous atmosphere exists or could resonably be expected to exist, such as in excavations in landfill areas or excavations in areas where hazardous substances are stored nearby, the atmospheres in the excavation shall be tested before employees enter excavations greater than 4 feet in depth.', '75');
INSERT INTO `app_case_sf_code` VALUES ('480', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(g)(2)', 'Emergency rescue equipment such as breathing apparatus, a safety harness and line, or a basket stretcher, shall be readily available where hazardous atmospheric conditions exist or may reasonably be expected to develop during work in an excavation. This equipment shall be attended when in use.', '75');
INSERT INTO `app_case_sf_code` VALUES ('481', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(h)(1)', 'Protection from hazards associated with water accumulation. Employees shall not work in excavations in which there is accumulated water, or in excavations in which water is accumulating, unless adequate precautions have been taken to protect employees against the hazards posed by water accumulation. The precautions necessary to protect employees adequately vary with each situation, but could include special support or shield systems to protect from cave-ins, water removal to control the level of accumulating water, or use of a safety harness and lifeline.', '75');
INSERT INTO `app_case_sf_code` VALUES ('482', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(h)(2)', 'Protection from hazards associated with water accumulation.  If water is controlled or prevented from accumulating by the use of water removal equipment, the water removal equipment and operations shall be monitored by a competent person to ensure proper operation.', '75');
INSERT INTO `app_case_sf_code` VALUES ('483', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(i)(1)', 'Where the stability of adjoining buildings, walls, or other structures is endangered by excavation operations, support systems such as shoring, bracing, or underpinning shall be provided to ensure the stability of such structures for the protection of employees.', '75');
INSERT INTO `app_case_sf_code` VALUES ('484', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(i)(2)(i-iv)', 'Excavation below the level of the base or footing of any foundation or retaining wall that could be reasonably expected to pose a hazard to employees shall not be permitted except when: a support system, such as underpinning, is provided to ensure the safety of employees and the stability of the structure; or the excavation is in stable rock; or a registered professional engineer has approved the determination that the structure is sufficiently removed from the excavation so as to be unaffected by the excavation activity; or a registered professional engineer has approved the determination that such excavation work will not pose a hazard to the employees.', '75');
INSERT INTO `app_case_sf_code` VALUES ('485', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(k)(1)', 'Inspections. Daily inspections of excavations, the adjacent areas, and protective systems shall be made by a competent person for evidence of a situation that could result in possible cave-ins, indications of failure of protective systems, hazardous atmospheres, or other hazardous conditions. An inspection shall be conducted by the competent person prior to the start of work and as needed throughout the shift. Inspections shall also be made after every rainstorm or other hazard increasing occurrence. These inspections are only required when employee exposure can be reasonably anticipated.', '75');
INSERT INTO `app_case_sf_code` VALUES ('486', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.651(l)', 'Walkways. Walkways shall be provided where employees or equipment are required or permitted to cross over excavations. Guardrails which comply with � 1926.502(b) shall be provided where walkways are 6 feet (1.8 m) or more above lower levels.', '75');
INSERT INTO `app_case_sf_code` VALUES ('487', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.652(a)(i-ii)', 'Each employee in an excavation shall be protected from cave-ins by an adequate protective system designed in accordance with paragraph (b) or (c) of this section except when: excavations are made entirely in stable rock; or excavations are less than 5 feet in depth and examination of the ground by a competent person provides no indication of a potential cave-in.', '76');
INSERT INTO `app_case_sf_code` VALUES ('488', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.652(b)', 'Design of sloping and benching systems. The slopes and configurations of sloping and benching systems shall be selected and constructed by the employer or his disgnee and shall be in accordance with the requirements of paragraph (b)(1); or, in the alternative, paragraph (b)(2); or in the alternative, paragraph (b)(3); or in the alternative, paragraph (b)(4) of this section.', '76');
INSERT INTO `app_case_sf_code` VALUES ('489', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.652(c)', 'Design of support systems, shield systems, and other protective systems. Designs of support systems, shield systems, and other protective systems shall be selected and constructed by the employer or his designee and shall be in accordance with the requirements of paragraph (c)(1); or, in the alternative, paragraph (c)(2); or, in the alternative, paragraph (c)(3); or in the alternative, paragraph (c)(4) of this section.', '76');
INSERT INTO `app_case_sf_code` VALUES ('490', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.652(d)(2)', 'Manufactured materials and equipment used for protective systems shall be used and maintained in a manner that is consistent with the recommendations of the manufacturer, and in a manner that will prevent employee exposure to hazards.', '76');
INSERT INTO `app_case_sf_code` VALUES ('491', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.652(f)', 'Sloping and benching systems. Employees shall not be permitted to work on the faces of sloped or benched excavations at levels above other employees except when employees at the lower levels are adequately protected from the hazard of falling, rolling, or sliding material or equipment.', '76');
INSERT INTO `app_case_sf_code` VALUES ('492', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.652(g)(1)(ii)', 'Shields shall be installed in a manner to restrict lateral or other hazardous movement of the shield in the event of the application of sudden lateral loads.', '76');
INSERT INTO `app_case_sf_code` VALUES ('493', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.652(g)(1)(iv)', 'Employees shall not be allowed in shields when shields are being installed, removed, or moved vertically.', '76');
INSERT INTO `app_case_sf_code` VALUES ('494', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.701(b)', 'Reinforcing steel. All protruding reinforcing steel, onto and into which employees could fall, shall be guarded to eliminate the hazard of impalement.', '77');
INSERT INTO `app_case_sf_code` VALUES ('495', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.701(d)', 'Riding concrete buckets. No employee shall be permitted to ride concrete buckets.', '77');
INSERT INTO `app_case_sf_code` VALUES ('496', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.701(e)(1)', 'Working under loads. No employee shall be permitted to work under concrete buckets while buckets are being elevated or lowered into position.', '77');
INSERT INTO `app_case_sf_code` VALUES ('497', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.702(c)', 'Power concrete trowels. Powered and rotating type concrete troweling machines that are manually guided shall be equipped with a control switch that will automatically shut off the power whenever the hands of the operator are removed from the equipment handles.', '78');
INSERT INTO `app_case_sf_code` VALUES ('498', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.752(b)', 'Commencement of steel erection. A steel erection contractor shall not erect steel unless it has received written notification that the concrete in the footings, piers and walls or the mortar in the masonry piers and walls has attained, on the basis of an appropriate ASTM standard test method of field-cured samples, either 75 percent of the intended minimum compressive design strength or sufficient strength to support the loads imposed during steel erection.', '79');
INSERT INTO `app_case_sf_code` VALUES ('499', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.753(c)(1)(i)(j)', 'Pre-shift visual inspection of cranes. Ground conditions around the hoisting equipment for proper support, including ground settling under and around outriggers, ground water accumulation, or similar conditions;', '80');
INSERT INTO `app_case_sf_code` VALUES ('500', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.753(c)(2)', 'A qualified rigger (a rigger who is also a qualified person) shall inspect the rigging prior to each shift in accordance with Sec.1926.251.', '80');
INSERT INTO `app_case_sf_code` VALUES ('501', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.753(c)(5)(i-ii)', 'General. Safety latches on hooks shall not be deactivated or made inoperable except: when a qualified rigger has determined that the hoisting and placing of purlins and single joists can be performed more safely by doing so; or when equivalent protection is provided in a site-specific erection plan.', '80');
INSERT INTO `app_case_sf_code` VALUES ('502', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.753(d)(1)(i-ii)', 'Working under loads. Routes for suspended loads shall be pre-planned to ensure that no employee is required to work directly below a suspended load except for: employees engaged in the initial connection of the steel; or employees necessary for the hooking or unhooking of the load.', '80');
INSERT INTO `app_case_sf_code` VALUES ('503', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.754.(e)(1)(ii)', 'Metal decking. Hoisting, landing and placing of metal decking bundles. If loose items such as dunnage, flashing, or other materials are placed on the top of metal decking bundles to be hoisted, such items shall be secured to the bundles.', '81');
INSERT INTO `app_case_sf_code` VALUES ('504', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.754.(e)(1)(v)', 'At the end of shift or when environmental or jobsite conditions require, metal decking shall be secured against displacement.', '81');
INSERT INTO `app_case_sf_code` VALUES ('505', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.754.(e)(3)(i)', 'Covering roof and floor openings. Covers for roof and floor openings shall be capable of supporting, without failure, twice the weight of the employees, equipment and materials that may be imposed on the cover at any one time.', '81');
INSERT INTO `app_case_sf_code` VALUES ('506', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.754.(e)(3)(ii)', 'Covering roof and floor openings. All covers shall be secured when installed to prevent accidental displacement by the wind, equipment or employees.', '81');
INSERT INTO `app_case_sf_code` VALUES ('507', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.754.(e)(3)(iii)', 'Covering roof and floor openings. All covers shall be painted with high-visibility paint or shall be marked with the word �HOLE� or �COVER� to provide warning of the hazard.', '81');
INSERT INTO `app_case_sf_code` VALUES ('508', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.755(b)(1)', 'Anchor rods (anchor bolts) shall not be repaired, replaced or field-modified without the approval of the project structural engineer of record.', '82');
INSERT INTO `app_case_sf_code` VALUES ('509', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.756(a)(1)', 'General requirements for erection stability. All columns shall be anchored by a minimum of 4 anchor rods (anchor bolts).', '83');
INSERT INTO `app_case_sf_code` VALUES ('510', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.760(a)(1)', 'General requirements. Except as provided by paragraph (a)(3) of this section, each employee engaged in a steel erection activity who is on a walking/working surface with an unprotected side or edge more than 15 feet (4.6 m) above a lower level shall be protected from fall hazards by guardrail systems, safety net systems, personal fall arrest systems, positioning device systems or fall restraint systems.', '84');
INSERT INTO `app_case_sf_code` VALUES ('511', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.760(b)(3)', '', '84');
INSERT INTO `app_case_sf_code` VALUES ('512', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.760(c)(2)', 'Controlled Decking Zone (CDZ). Access to a CDZ shall be limited to only those employees engaged in leading edge work.', '84');
INSERT INTO `app_case_sf_code` VALUES ('513', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.760(c)(7)', 'Controlled Decking Zone (CDZ). Final deck attachments and installation of shear connectors shall not be performed in the CDZ.', '84');
INSERT INTO `app_case_sf_code` VALUES ('514', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.761(a)', 'Training. Training required by this section shall be provided by a qualified person(s).', '85');
INSERT INTO `app_case_sf_code` VALUES ('515', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.761(b)', 'Fall hazard training. The employer shall train each employee exposed to a fall hazard in accordance with the requirements of this section. The employer shall institute a training program and ensure employee participation in the program.', '85');
INSERT INTO `app_case_sf_code` VALUES ('516', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.761(c)(1)(i-ii)', 'Multiple lift rigging procedure. The employer shall ensure that each employee who performs multiple lift rigging has been provided training in the following areas: The nature of the hazards associated with multiple lifts, and the proper procedures and equipment to perform multiple lifts required by � 1926.753(e).', '85');
INSERT INTO `app_case_sf_code` VALUES ('517', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.761(c)(2)(i-ii)', 'Connector procedures. The employer shall ensure that each connector has been provided training in the following areas: the nature of the hazards associated with connecting; and the establishment, access, proper connecting techniques and work practices required by � 1926.756(c) and � 1926.760(b).', '85');
INSERT INTO `app_case_sf_code` VALUES ('518', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.761(c)(3)(i-ii)', 'Controlled Decking Zone Procedures. Where CDZs are being used, the employer shall assure that each employee has been provided training in the following areas: the nature of the hazards associated with work within a controlled decking zone; and the establishment, access, proper installation techniques and work practices required by � 1926.760(c) and � 1926.754(e).', '85');
INSERT INTO `app_case_sf_code` VALUES ('519', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.850(a)', 'Engineering survey.  Prior to permitting employees to start demolition operations, an engineering survey shall be made, by a competent person, of the structure to determine the condition of the framing, floors, and walls, and possibility of unplanned collapse of any portion of the structure. Any adjacent structure where employees may be exposed shall also be similarly checked. The employer shall have in writing evidence that such a survey has been performed.', '86');
INSERT INTO `app_case_sf_code` VALUES ('520', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.850(g)', 'Guardrails. Where a hazard exists to employees falling through wall openings, the opening shall be protected to a height of approximately 42 inches.', '86');
INSERT INTO `app_case_sf_code` VALUES ('521', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.850(h)', 'When debris is dropped through holes in the floor without the use of chutes, the area onto which the material is dropped shall be completely enclosed with barricades not less than 42 inches high and not less than 6 feet back from the projected edge of the opening above. Signs, warning of the hazard of falling materials, shall be posted at each level. Removal shall not be permitted in this lower area until debris handling ceases above.', '86');
INSERT INTO `app_case_sf_code` VALUES ('522', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.850(i)', 'All floor openings, not used as material drops, shall be covered over with material substantial enough to support the weight of any load which may be imposed. Such material shall be properly secured to prevent its accidental movement.', '86');
INSERT INTO `app_case_sf_code` VALUES ('523', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.850(k)', 'Employee entrances to multi-story structures being demolished shall be completely protected by sidewalk sheds or canopies, or both, providing protection from the face of the building for a minimum of 8 feet. All such canopies shall be at least 2 feet wider than the building entrances or openings (1 foot wider on each side thereof), and shall be capable of sustaining a load of 150 pounds per square foot.', '86');
INSERT INTO `app_case_sf_code` VALUES ('524', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.851(b)', 'All stairs, passageways, ladders and incidental equipment thereto, which are covered by this section, shall be periodically inspected and maintained in a clean safe condition.', '87');
INSERT INTO `app_case_sf_code` VALUES ('525', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.852(a)', 'No material shall be dropped to any point lying outside the exterior walls of the structure unless the area is effectively protected.', '88');
INSERT INTO `app_case_sf_code` VALUES ('526', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.852(c)', 'A substantial gate shall be installed in each chute at or near the discharge end. A competent employee shall be assigned to control the operation of the gate, and the backing and loading of trucks.', '88');
INSERT INTO `app_case_sf_code` VALUES ('527', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.852(e)', 'Any chute opening, into which workmen dump debris, shall be protected by a substantial guardrail approximately 42 inches above the floor or other surface on which the men stand to dump the material. Any space between the chute and the edge of openings in the floors through which it passes shall be solidly covered over.', '88');
INSERT INTO `app_case_sf_code` VALUES ('528', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.852(f)', 'Where the material is dumped from mechanical equipment or wheelbarrows, a securely attached toeboard or bumper, not less than 4 inches thick and 6 inches high, shall be provided at each chute opening.', '88');
INSERT INTO `app_case_sf_code` VALUES ('529', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.856(b)', 'Equipment stops. Floor openings shall have curbs or stop-logs to prevent equipment from running over the edge.', '89');
INSERT INTO `app_case_sf_code` VALUES ('530', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.856(c)', 'Crane inspection.  Cranes, derricks, and other mechanical equipment. Employers must meet the requirements specified in subparts N, O, and CC of this part.', '89');
INSERT INTO `app_case_sf_code` VALUES ('531', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1051(a)', 'A stairway or ladder shall be provided at all personnel points of access where there is a break in elevation of 19 inches (48 cm) or more, and no ramp, runway, sloped embankment, or personnel hoist is provided.', '90');
INSERT INTO `app_case_sf_code` VALUES ('532', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(a)(3)', 'General. Riser height and tread depth shall be uniform within each flight of stairs, including any foundation structure used as one or more treads of the stairs. Variations in riser height or tread depth shall not be over 1�4 -inch (0.6 cm) in any stairway system.', '91');
INSERT INTO `app_case_sf_code` VALUES ('533', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(a)(4)', 'General. Where doors or gates open directly on a stairway, a platform shall be provided, and the swing of the door shall not reduce the effective width of the platform to less than 20 inches (51 cm).', '91');
INSERT INTO `app_case_sf_code` VALUES ('534', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(a)(5)', 'General. Metal pan landings and metal pan treads, when used, shall be secured in place before filling with concrete or other material.', '91');
INSERT INTO `app_case_sf_code` VALUES ('535', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(a)(6)', 'General. All parts of stairways shall be free of hazardous projections, such as protruding nails.', '91');
INSERT INTO `app_case_sf_code` VALUES ('536', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(a)(7)', 'General. Slippery conditions on stairways shall be eliminated before the stairways are used to reach other levels.', '91');
INSERT INTO `app_case_sf_code` VALUES ('537', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(b)(1)', 'Temporary service. Except during stairway construction, foot traffic is prohibited on stairways with pan stairs where the treads and/or landings are to be filled in with concrete or other material at a later date, unless the stairs are temporarily fitted with wood or other solid material at least to the top edge of each pan. Such temporary treads and landings shall be replaced when worn below the level of the top edge of the pan.', '91');
INSERT INTO `app_case_sf_code` VALUES ('538', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(b)(2)', 'Temporary service. Except during stairway construction, foot traffic is prohibited on skeleton metal stairs where permanent treads and/or landings are to be installed at a later date, unless the stairs are fitted with secured temporary treads and landings long enough to cover the entire tread and/or landing area.', '91');
INSERT INTO `app_case_sf_code` VALUES ('539', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(b)(3)', 'Temporary service. Treads for temporary service shall be made of wood or other solid material, and shall be installed the full width and depth of the stair.', '91');
INSERT INTO `app_case_sf_code` VALUES ('540', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(c)(1)(i-ii)', 'Stairrails and handrails. Stairways having four or more risers or rising more than 30 inches (76 cm), whichever is less, shall be equipped with at least one handrail and one stairrail system along each unprotected side or edge.', '91');
INSERT INTO `app_case_sf_code` VALUES ('541', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(c)(3)(i)', 'Stairrails and handrails. Stairrails shall be not less than 36 inches (91.5 cm) from the upper surface of the stairrail system to the surface of the tread, in line with the face of the riser at the forward edge of the tread.', '91');
INSERT INTO `app_case_sf_code` VALUES ('542', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(c)(5)', 'Handrails and the top rails of stairrail systems shall be capable of withstanding, without failure, a force of at least 200 pounds applied within 2 inches of the top edge, in any downward or outward direction, at any point along the top edge.', '91');
INSERT INTO `app_case_sf_code` VALUES ('543', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(c)(6)', 'Stairrails and handrails. The height of handrails shall be not more than 37 inches (94 cm) nor less than 30 inches (76 cm) from the upper surface of the handrail to the surface of the tread, in line with the face of the riser at the forward edge of the tread.', '91');
INSERT INTO `app_case_sf_code` VALUES ('544', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(c)(7)', 'Stairrails and handrails. When the top edge of a stairrail system also serves as a handrail, the height of the top edge shall be not more than 37 inches (94 cm) nor less than 36 inches (91.5 cm) from the upper surface of the stairrail system to the surface of the tread, in line with the face of the riser at the forward edge of the tread.', '91');
INSERT INTO `app_case_sf_code` VALUES ('545', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(c)(11)', 'Stairrails and handrails. Handrails that will not be a permanent part of the structure being built shall have a minimum clearance of 3 inches (8 cm) between the handrail and walls, stairrail systems, and other objects.', '91');
INSERT INTO `app_case_sf_code` VALUES ('546', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1052(c)(12)', 'Stairrails and handrails. Unprotected sides and edges of stairway landings shall be provided with guardrail systems. Guardrail system criteria are contained in subpart M of this part.', '91');
INSERT INTO `app_case_sf_code` VALUES ('547', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(a)(2)', 'General. Ladder rungs, cleats, and steps shall be parallel, level, and uniformly spaced when the ladder is in position for use.', '92');
INSERT INTO `app_case_sf_code` VALUES ('548', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(a)(8)', 'General. A metal spreader or locking device shall be provided on each stepladder to hold the front and back sections in an open position when the ladder is being used.', '92');
INSERT INTO `app_case_sf_code` VALUES ('549', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(a)(12)', 'General. Wood ladders shall not be coated with any opaque covering, except for identification or warning labels which may be placed on one face only of a side rail.', '92');
INSERT INTO `app_case_sf_code` VALUES ('550', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(b)(1)', 'Use. When portable ladders are used for access to an upper landing surface, the ladder side rails shall extend at least 3 feet (.9 m) above the upper landing surface to which the ladder is used to gain access; or, when such an extension is not possible because of the ladder\'s length, then the ladder shall be secured at its top to a rigid support that will not deflect, and a grasping device, such as a grabrail, shall be provided to assist employees in mounting and dismounting the ladder. In no case shall the extension be such that ladder deflection under a load would, by itself, cause the ladder to slip off its support.', '92');
INSERT INTO `app_case_sf_code` VALUES ('551', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(b)(4)', 'Use. Ladders shall be used only for the purpose for which they were designed.', '92');
INSERT INTO `app_case_sf_code` VALUES ('552', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(b)(5)(i)', 'Use. Non-self-supporting ladders shall be used at an angle such that the horizontal distance from the top support to the foot of the ladder is approximately one-quarter of the working length of the ladder (the distance along the ladder between the foot and the top support).', '92');
INSERT INTO `app_case_sf_code` VALUES ('553', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(b)(6)', 'Use. Ladders shall be used only on stable and level surfaces unless secured to prevent accidental displacement.', '92');
INSERT INTO `app_case_sf_code` VALUES ('554', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(b)(9)', 'Use. The area around the top and bottom of ladders shall be kept clear.', '92');
INSERT INTO `app_case_sf_code` VALUES ('555', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(b)(11)', 'Use. Ladders shall not be moved, shifted, or extended while occupied.', '92');
INSERT INTO `app_case_sf_code` VALUES ('556', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(b)(13)', 'Use. The top or top step of a stepladder shall not be used as a step.', '92');
INSERT INTO `app_case_sf_code` VALUES ('557', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1053(b)(15)', 'Use. Ladders shall be inspected by a competent person for visible defects on a periodic basis and after any occurrence that could affect their safe use.', '92');
INSERT INTO `app_case_sf_code` VALUES ('558', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1060(b)', 'Retraining. Retraining shall be provided for each employee as necessary so that the employee maintains the understanding and knowledge acquired through compliance with this section.', '93');
INSERT INTO `app_case_sf_code` VALUES ('559', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1402(b)', 'Equipment must not be used or assembled unless ground conditions are firm, drained, and graded to a sufficient extent. Manufacturer\'s specifications for sufficient support and degree of level must be met.', '94');
INSERT INTO `app_case_sf_code` VALUES ('560', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1404(q)(1)', 'Outriggers and Stabilizers. When the load to be handled and the operating radius require the use of outriggers or stabilizers, or at any time when outriggers or stabilizers are used, all of the following requirements must be met (except as otherwise indicated), The outriggers or stabilizers must be either fully extended or, if manufacturer procedures permit, deployed as specified in the load chart.', '95');
INSERT INTO `app_case_sf_code` VALUES ('561', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1404(r)(2)', 'Synthetic slings.  Synthetic slings are protected from: Abrasive, sharp or acute edges, and configurations that could cause a reduction of the sling\'s rated capacity, such as distortion or localized compression. Note: Requirements for the protection of wire rope slings are contained in 29 CFR 1926.251(c)(9).', '95');
INSERT INTO `app_case_sf_code` VALUES ('562', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1412(d)(1)', 'Shift. Equipment must be inspected by a competent person prior to each shift the equipment will be used.', '96');
INSERT INTO `app_case_sf_code` VALUES ('563', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1412(e)(3)(i-ii)', 'Monthly. Each month equipment is in service it must be inspected by a competent person with items inspected and their results listed, name and signature of person who conducted the inspection, and date. This document must be retained for a minimum of three months.', '96');
INSERT INTO `app_case_sf_code` VALUES ('564', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1412(f)(1-7)', 'Annual. At least every 12 months a comprehensive equipment inspection must be peformed by a qualified person with items inspected and their results listed, name and signature of qualified person that inspected the equipment, and date. This document must be retained for a minimum of 12 months.', '96');
INSERT INTO `app_case_sf_code` VALUES ('565', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1413(a)(1-4)', 'Wire rope. A competent person must inspect wire rope prior to shift to identify apparent deficiencies and remove damaged pieces from service. Where a wire rope is required to be removed from service, either the equipment as a whole or the hoist with that wire rope must be tagged out until that wire rope is repaired or replaced.', '97');
INSERT INTO `app_case_sf_code` VALUES ('566', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1414(a)', 'Wire rope replacement. Original equipment wire rope and replacement rope must be selected and installed in accordance with recommendations of manufacturer, or a qualified person.', '98');
INSERT INTO `app_case_sf_code` VALUES ('567', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1417(a)', 'Manufacturer procedures. The employer must comply with all manufacturer procedures applicable to the operational functions of equipment, including its use with attachments.', '99');
INSERT INTO `app_case_sf_code` VALUES ('568', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1417(d)', 'Distractions. The operator must not engage in any practice or activity that diverts his/her attention while actually engaged in operating the equipment, such as the use of cellular phones (other than when used for signal communications).', '99');
INSERT INTO `app_case_sf_code` VALUES ('569', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1417(h)', 'Storms. When a local storm warning has been issued, the competent person must determine whether it is necessary to implement manufacturer recommendations for securing equipment.', '99');
INSERT INTO `app_case_sf_code` VALUES ('570', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1417(o)', 'Capacity. Equipment must not be operated in excess of its rated capacity.', '99');
INSERT INTO `app_case_sf_code` VALUES ('571', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1417(q)', 'Side pulls. The equipment must not be used to drag or pull loads sideways.', '99');
INSERT INTO `app_case_sf_code` VALUES ('572', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '10.261.422', 'Hand signal chart. Hand signal charts must be either posted on the equipment or conspicuously posted in the vicinity of the hoisting operations.', '100');
INSERT INTO `app_case_sf_code` VALUES ('573', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1424(a)(2)(ii)', 'Swing radius. Erect and maintain control lines, warning lines, railings or similar barriers to mark the boundaries of the hazard areas. Exception: When the employer can demonstrate that it is neither feasible to erect such barriers on the ground nor on the equipment, the hazard areas must be clearly marked by a combination of warning signs (such as �Danger�Swing/Crush Zone�) and high visibility markings on the equipment that identify the hazard areas. In addition, the employer must train each employee to understand what these markings signify.', '101');
INSERT INTO `app_case_sf_code` VALUES ('574', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1428(b)', 'Signal person. If subsequent actions by the signal person indicate that the individual does not meet the Qualification Requirements ( see paragraph (c) of this section), the employer must not allow the individual to continue working as a signal person until re-training is provided and a re-assessment is made in accordance with paragraph (a) of this section that confirms that the individual meets the Qualification Requirements.', '102');
INSERT INTO `app_case_sf_code` VALUES ('575', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1433(d)(3)', 'Load ratings. Hook and ball assemblies and load blocks must be marked with their rated capacity and weight.', '103');
INSERT INTO `app_case_sf_code` VALUES ('576', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', '1926.1433(d)(4)(i)', 'Latching hooks. Hooks must be equipped with latches, except where the requirements of paragraph (d)(4)(ii) of this section are met.', '103');
INSERT INTO `app_case_sf_code` VALUES ('577', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-010(a)', 'No smoking within 25\' of building', '104');
INSERT INTO `app_case_sf_code` VALUES ('578', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-020(a)', 'Hot Work Permits Required/Fire Extinguishers', '105');
INSERT INTO `app_case_sf_code` VALUES ('579', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-020(b)', 'Flash Shields/Fire Blankets', '105');
INSERT INTO `app_case_sf_code` VALUES ('580', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-030(a)', 'Separated by 25\' or 5\' tall 1/2 hour rated divider', '106');
INSERT INTO `app_case_sf_code` VALUES ('581', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(a)', 'No horseplay, fighting or weapons', '107');
INSERT INTO `app_case_sf_code` VALUES ('582', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(b)', 'No gambling or card-playing during work hours', '107');
INSERT INTO `app_case_sf_code` VALUES ('583', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(c)', 'No firearms or other weapons', '107');
INSERT INTO `app_case_sf_code` VALUES ('584', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(d)', 'No sleeping or leaving job site during work hours', '107');
INSERT INTO `app_case_sf_code` VALUES ('585', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(e)', 'No falsifying or forging reports/documents', '107');
INSERT INTO `app_case_sf_code` VALUES ('586', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(f)', 'Must use sanitary facilities', '107');
INSERT INTO `app_case_sf_code` VALUES ('587', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(g)', 'Licensed operators with documentation provided to WT', '107');
INSERT INTO `app_case_sf_code` VALUES ('588', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(h)', 'No riding on equipment allowed', '107');
INSERT INTO `app_case_sf_code` VALUES ('589', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(i)', 'No riding in truck bed unless seat & belt provided', '107');
INSERT INTO `app_case_sf_code` VALUES ('590', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(j)', 'Site speed limit 10 mph', '107');
INSERT INTO `app_case_sf_code` VALUES ('591', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(k)', '2-way radios only - no personal radios', '107');
INSERT INTO `app_case_sf_code` VALUES ('592', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(l)', 'No harassment of personnel', '107');
INSERT INTO `app_case_sf_code` VALUES ('593', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(m)', 'No use or possession of alcoholic beverages/illegal drugs', '107');
INSERT INTO `app_case_sf_code` VALUES ('594', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(n)', 'No glass bottles', '107');
INSERT INTO `app_case_sf_code` VALUES ('595', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(o)', 'Documents are required to be secured at all times', '107');
INSERT INTO `app_case_sf_code` VALUES ('596', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(p)', 'Daily equipment inspections are required', '107');
INSERT INTO `app_case_sf_code` VALUES ('597', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(q)', 'Job Hazard Analysis (JHA\'s) are required for all tasks', '107');
INSERT INTO `app_case_sf_code` VALUES ('598', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(r)', 'Danger tape signage is required including company, list of hazard(s), and contact information', '107');
INSERT INTO `app_case_sf_code` VALUES ('599', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(s)', 'Crossing danger tape without authorization from controlling contractor', '107');
INSERT INTO `app_case_sf_code` VALUES ('600', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(t)', 'Identification badges must be worn at all times when on site and cannot be left in car, lunch box, etc.', '107');
INSERT INTO `app_case_sf_code` VALUES ('601', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(u)', 'Materials and equipment being transported must be adequately secured.', '107');
INSERT INTO `app_case_sf_code` VALUES ('602', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(v)', 'Cellphones are not permitted to be used while on the project site.', '107');
INSERT INTO `app_case_sf_code` VALUES ('603', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-040(w)', 'Failure to report incident.', '107');
INSERT INTO `app_case_sf_code` VALUES ('604', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-050(a)', 'Broom swept daily', '108');
INSERT INTO `app_case_sf_code` VALUES ('605', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-050(b)', 'No combustible debris on roof', '108');
INSERT INTO `app_case_sf_code` VALUES ('606', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-050(c)', 'Temporary loading approved by WT', '108');
INSERT INTO `app_case_sf_code` VALUES ('607', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-050(d)', 'Aisles kept free of obstruction', '108');
INSERT INTO `app_case_sf_code` VALUES ('608', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(a)', '100% PPE at all times - hard hat, safety glasses, and safety vest', '109');
INSERT INTO `app_case_sf_code` VALUES ('609', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(b)', 'Hard hats ANSI Z89.1 minimum', '109');
INSERT INTO `app_case_sf_code` VALUES ('610', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(c)', 'Safety glasses ANSI Z 87 minimum', '109');
INSERT INTO `app_case_sf_code` VALUES ('611', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(d)', 'No shorts, cut offs, tank tops or net shirts', '109');
INSERT INTO `app_case_sf_code` VALUES ('612', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(e)', 'Shirts w/ 4\" past crown of shoulders', '109');
INSERT INTO `app_case_sf_code` VALUES ('613', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(f)', 'Leather boots, no loafers, sandals or tennis shoes', '109');
INSERT INTO `app_case_sf_code` VALUES ('614', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(g)', 'Protective metatarsal/metacarpal footwear/task', '109');
INSERT INTO `app_case_sf_code` VALUES ('615', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(h)', 'Face shields when abrading', '109');
INSERT INTO `app_case_sf_code` VALUES ('616', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(i)', '100% gloves while working', '109');
INSERT INTO `app_case_sf_code` VALUES ('617', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(j)', 'Task prescribed gloves', '109');
INSERT INTO `app_case_sf_code` VALUES ('618', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-060(k)', 'Hearing protection provided above 85db', '109');
INSERT INTO `app_case_sf_code` VALUES ('619', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-070(a)', '100% above 6\'', '110');
INSERT INTO `app_case_sf_code` VALUES ('620', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-070(b)', 'WT hole cover policy', '110');
INSERT INTO `app_case_sf_code` VALUES ('621', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-070(c)', 'Anchorage points 5000 lb.', '110');
INSERT INTO `app_case_sf_code` VALUES ('622', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-070(d)', 'Guardrail systems', '110');
INSERT INTO `app_case_sf_code` VALUES ('623', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-070(e)', 'Personal fall arrest systems', '110');
INSERT INTO `app_case_sf_code` VALUES ('624', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-070(f)', 'Catch platforms', '110');
INSERT INTO `app_case_sf_code` VALUES ('625', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-080(a)', 'Limited materials in work area', '111');
INSERT INTO `app_case_sf_code` VALUES ('626', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-080(b)', 'Cylindrical materials racked/stacked/blocked to prevent spreading/rolling/tilting', '111');
INSERT INTO `app_case_sf_code` VALUES ('627', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-080(c)', 'All containers labeled', '111');
INSERT INTO `app_case_sf_code` VALUES ('628', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(a)', 'All guards in place', '112');
INSERT INTO `app_case_sf_code` VALUES ('629', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(b)', 'Dead man switch', '112');
INSERT INTO `app_case_sf_code` VALUES ('630', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(c)', 'Extension cords - no post manufacturer alterations', '112');
INSERT INTO `app_case_sf_code` VALUES ('631', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(d)', 'Extension cords to have GFCI protection', '112');
INSERT INTO `app_case_sf_code` VALUES ('632', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(e)', 'Extension cords - assured electrical grounding conductor program', '112');
INSERT INTO `app_case_sf_code` VALUES ('633', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(f)', 'Welding leads checked daily for damage', '112');
INSERT INTO `app_case_sf_code` VALUES ('634', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(g)', 'Welding leads - damaged cables removed from service', '112');
INSERT INTO `app_case_sf_code` VALUES ('635', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(h)', 'Welding leads - cables not strung across walkways or paths of egress', '112');
INSERT INTO `app_case_sf_code` VALUES ('636', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(i)', 'No gas operated tools/equipment in building w/o exhaust scrubbers', '112');
INSERT INTO `app_case_sf_code` VALUES ('637', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-090(j)', 'Portable generators placed firmly on stable surface', '112');
INSERT INTO `app_case_sf_code` VALUES ('638', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-100(a)', 'No portable metal ladders', '113');
INSERT INTO `app_case_sf_code` VALUES ('639', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-100(b)', 'Damaged ladders tagged & removed from site', '113');
INSERT INTO `app_case_sf_code` VALUES ('640', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-100(c)', 'Safety & operation labels visible & in good condition', '113');
INSERT INTO `app_case_sf_code` VALUES ('641', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-100(d)', 'All extension ladders tied off 100%', '113');
INSERT INTO `app_case_sf_code` VALUES ('642', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-110(a)', 'No bakers scaffolding', '114');
INSERT INTO `app_case_sf_code` VALUES ('643', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-110(b)', 'All scaffolding to have inspection tags', '114');
INSERT INTO `app_case_sf_code` VALUES ('644', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-120(a)', 'Floor/wall/roof openings responsibility of contractor creating it', '115');
INSERT INTO `app_case_sf_code` VALUES ('645', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-120(b)', 'Removal of floor cover requires log to be signed', '115');
INSERT INTO `app_case_sf_code` VALUES ('646', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-120(c)', 'Person to guard opening', '115');
INSERT INTO `app_case_sf_code` VALUES ('647', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-120(d)', 'Standard guardrail w/ orange fence on open sided floors/openings', '115');
INSERT INTO `app_case_sf_code` VALUES ('648', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-130(a)', 'Responsibility of contractor entering space', '116');
INSERT INTO `app_case_sf_code` VALUES ('649', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-130(b)', 'Entry permit required', '116');
INSERT INTO `app_case_sf_code` VALUES ('650', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-130(c)', 'Retrieval system', '116');
INSERT INTO `app_case_sf_code` VALUES ('651', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-130(d)', 'Constant air monitoring', '116');
INSERT INTO `app_case_sf_code` VALUES ('652', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-140(a)', 'Contractor maintains MSDS on construction site', '117');
INSERT INTO `app_case_sf_code` VALUES ('653', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(a)', 'Pick plans 72 hours in advance of pick', '118');
INSERT INTO `app_case_sf_code` VALUES ('654', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(b)', 'Hardware ID tags/slings, chokers etc.', '118');
INSERT INTO `app_case_sf_code` VALUES ('655', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(c)', 'Tag lines on all lifts', '118');
INSERT INTO `app_case_sf_code` VALUES ('656', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(d)', 'No open hooks on lifts', '118');
INSERT INTO `app_case_sf_code` VALUES ('657', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(e)', 'Annual inspection', '118');
INSERT INTO `app_case_sf_code` VALUES ('658', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(f)', 'Daily inspection on crane', '118');
INSERT INTO `app_case_sf_code` VALUES ('659', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(g)', 'NCCCO operators', '118');
INSERT INTO `app_case_sf_code` VALUES ('660', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(h)', 'All certs/licenses provided to WT 2 days prior to event', '118');
INSERT INTO `app_case_sf_code` VALUES ('661', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(i)', 'Cranes not operated in inclement weather', '118');
INSERT INTO `app_case_sf_code` VALUES ('662', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-150(j)', 'Trained signal people', '118');
INSERT INTO `app_case_sf_code` VALUES ('663', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-160(a)', 'Review w/ WT prior to maintenance or service', '119');
INSERT INTO `app_case_sf_code` VALUES ('664', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-160(b)', 'All hazardous energy locked & tagged', '119');
INSERT INTO `app_case_sf_code` VALUES ('665', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-160(c)', 'Contractors supply own locks/tags', '119');
INSERT INTO `app_case_sf_code` VALUES ('666', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-160(d)', 'Tags indicate ID of worker applying lock', '119');
INSERT INTO `app_case_sf_code` VALUES ('667', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-160(e)', 'Danger - Do Not Operate on tags', '119');
INSERT INTO `app_case_sf_code` VALUES ('668', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-160(f)', 'Only red locks', '119');
INSERT INTO `app_case_sf_code` VALUES ('669', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-160(g)', 'WT notified when removing LOTO', '119');
INSERT INTO `app_case_sf_code` VALUES ('670', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-170(a)', 'No open fires, fire barrels, or hot boxes', '120');
INSERT INTO `app_case_sf_code` VALUES ('671', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-170(b)', 'Trailers & offices - min. 10 lb. ABC fire extinguisher', '120');
INSERT INTO `app_case_sf_code` VALUES ('672', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-170(c)', 'Equipment - min. 5 lb. ABC fire extinguisher', '120');
INSERT INTO `app_case_sf_code` VALUES ('673', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-170(d)', 'Fire Watch - min. 20 lb. ABC fire extinguisher', '120');
INSERT INTO `app_case_sf_code` VALUES ('674', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-170(e)', 'Annual inspection of fire extinguisher(s)', '120');
INSERT INTO `app_case_sf_code` VALUES ('675', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-180(a)', 'Spills reported immediately', '121');
INSERT INTO `app_case_sf_code` VALUES ('676', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-180(b)', 'Repair/replace damaged containment', '121');
INSERT INTO `app_case_sf_code` VALUES ('677', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-180(c)', 'Spill kits available', '121');
INSERT INTO `app_case_sf_code` VALUES ('678', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'WT-180(d)', 'EPA cleanup w/ manifest', '121');

-- ----------------------------
-- Table structure for app_case_status
-- ----------------------------
DROP TABLE IF EXISTS `app_case_status`;
CREATE TABLE `app_case_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_status
-- ----------------------------
INSERT INTO `app_case_status` VALUES ('1', '1', '2015-04-17 12:36:28', '2015-05-12 18:45:29', 'OPEN');
INSERT INTO `app_case_status` VALUES ('2', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'CLOSED');
INSERT INTO `app_case_status` VALUES ('3', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'OVERDUE');

-- ----------------------------
-- Table structure for app_case_type
-- ----------------------------
DROP TABLE IF EXISTS `app_case_type`;
CREATE TABLE `app_case_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_type
-- ----------------------------
INSERT INTO `app_case_type` VALUES ('1', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'violation');
INSERT INTO `app_case_type` VALUES ('2', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'recognition');
INSERT INTO `app_case_type` VALUES ('3', '1', '2015-04-17 12:36:28', '2015-04-17 12:36:28', 'incident');
INSERT INTO `app_case_type` VALUES ('4', '1', '2015-05-28 13:27:27', '2015-05-28 13:27:30', 'observation');

-- ----------------------------
-- Table structure for app_case_violation
-- ----------------------------
DROP TABLE IF EXISTS `app_case_violation`;
CREATE TABLE `app_case_violation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_case_id` int(11) NOT NULL,
  `foreman_id` int(11) NOT NULL,
  `correction_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ap_vi_ap_id` (`app_case_id`),
  KEY `ap_vi_fo_id` (`foreman_id`),
  CONSTRAINT `ap_vi_ap_id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`),
  CONSTRAINT `ap_vi_fo_id` FOREIGN KEY (`foreman_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_case_violation
-- ----------------------------

-- ----------------------------
-- Table structure for area
-- ----------------------------
DROP TABLE IF EXISTS `area`;
CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `floor_id` int(11) NOT NULL,
  `area` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fl_ar_id` (`floor_id`),
  CONSTRAINT `fl_ar_id` FOREIGN KEY (`floor_id`) REFERENCES `floor` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of area
-- ----------------------------
INSERT INTO `area` VALUES ('1', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', 'SITE');
INSERT INTO `area` VALUES ('2', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', 'PARKING LOT');
INSERT INTO `area` VALUES ('3', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', 'FSA');
INSERT INTO `area` VALUES ('4', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', 'EB');
INSERT INTO `area` VALUES ('5', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', 'DC');
INSERT INTO `area` VALUES ('6', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', 'CCNR');
INSERT INTO `area` VALUES ('7', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', 'CUB');
INSERT INTO `area` VALUES ('8', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', 'LOADING DOCK');
INSERT INTO `area` VALUES ('9', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '2', 'ROOF');
INSERT INTO `area` VALUES ('10', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '3', 'SITE');
INSERT INTO `area` VALUES ('11', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '3', 'FSA');
INSERT INTO `area` VALUES ('12', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '3', 'EB');
INSERT INTO `area` VALUES ('13', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '3', 'DC');
INSERT INTO `area` VALUES ('14', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '3', 'CCNR');
INSERT INTO `area` VALUES ('15', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '3', 'LOADING DOCK');
INSERT INTO `area` VALUES ('16', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '4', 'ROOF');
INSERT INTO `area` VALUES ('17', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', 'SITE');
INSERT INTO `area` VALUES ('18', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', 'FSA');
INSERT INTO `area` VALUES ('19', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', 'PARKING LOT');
INSERT INTO `area` VALUES ('20', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', 'EB');
INSERT INTO `area` VALUES ('21', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', 'DC');
INSERT INTO `area` VALUES ('22', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', 'CNR');
INSERT INTO `area` VALUES ('23', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', 'LOADING DOCK');
INSERT INTO `area` VALUES ('24', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '6', 'ROOF');
INSERT INTO `area` VALUES ('25', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', 'SITE');
INSERT INTO `area` VALUES ('26', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', 'PARKING LOT');
INSERT INTO `area` VALUES ('27', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', 'FSA');
INSERT INTO `area` VALUES ('28', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', 'EB');
INSERT INTO `area` VALUES ('29', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', 'DC');
INSERT INTO `area` VALUES ('30', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', 'CNR');
INSERT INTO `area` VALUES ('31', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', 'CUB');
INSERT INTO `area` VALUES ('32', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', 'LOADING DOCK');
INSERT INTO `area` VALUES ('33', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '8', 'FSA');
INSERT INTO `area` VALUES ('34', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '8', 'EB');
INSERT INTO `area` VALUES ('35', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '8', 'DC');
INSERT INTO `area` VALUES ('36', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '8', 'CNR');
INSERT INTO `area` VALUES ('37', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '9', 'ROOF');
INSERT INTO `area` VALUES ('38', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '9', 'CUB ROOF');
INSERT INTO `area` VALUES ('39', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', 'SITE');
INSERT INTO `area` VALUES ('40', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', 'FSA');
INSERT INTO `area` VALUES ('41', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', 'EB');
INSERT INTO `area` VALUES ('42', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', 'DC');
INSERT INTO `area` VALUES ('43', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', 'CNR');
INSERT INTO `area` VALUES ('44', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', 'LOADING DOCK');
INSERT INTO `area` VALUES ('45', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '11', 'FSA');
INSERT INTO `area` VALUES ('46', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '11', 'EB');
INSERT INTO `area` VALUES ('47', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '11', 'DC');
INSERT INTO `area` VALUES ('48', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '11', 'CNR');
INSERT INTO `area` VALUES ('49', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '12', 'ROOF');
INSERT INTO `area` VALUES ('50', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '12', 'CUB ROOF');
INSERT INTO `area` VALUES ('51', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', 'SITE');
INSERT INTO `area` VALUES ('52', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', 'NORTH CORRIDOR');
INSERT INTO `area` VALUES ('53', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', 'FSA');
INSERT INTO `area` VALUES ('54', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', 'EB');
INSERT INTO `area` VALUES ('55', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', 'DC');
INSERT INTO `area` VALUES ('56', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', 'CNR');
INSERT INTO `area` VALUES ('57', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', 'CUB');
INSERT INTO `area` VALUES ('58', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', 'LOADING DOCK');
INSERT INTO `area` VALUES ('59', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '14', 'FSA');
INSERT INTO `area` VALUES ('60', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '15', 'EB');
INSERT INTO `area` VALUES ('61', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '15', 'DC');
INSERT INTO `area` VALUES ('62', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '15', 'CNR');
INSERT INTO `area` VALUES ('63', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '16', 'ROOF');
INSERT INTO `area` VALUES ('64', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '16', 'CUB ROOF');
INSERT INTO `area` VALUES ('65', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '17', 'SITE');
INSERT INTO `area` VALUES ('66', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '17', 'NORTH CORRIDOR');
INSERT INTO `area` VALUES ('67', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '17', 'PARKING LOT');
INSERT INTO `area` VALUES ('68', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '17', 'FSA');
INSERT INTO `area` VALUES ('69', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '17', 'EB');
INSERT INTO `area` VALUES ('70', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '17', 'DC');
INSERT INTO `area` VALUES ('71', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '17', 'CNR');
INSERT INTO `area` VALUES ('72', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '17', 'LOADING DOCK');
INSERT INTO `area` VALUES ('73', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '18', 'FSA');
INSERT INTO `area` VALUES ('74', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '19', 'EB');
INSERT INTO `area` VALUES ('75', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '19', 'DC');
INSERT INTO `area` VALUES ('76', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '19', 'CNR');
INSERT INTO `area` VALUES ('77', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '20', 'ROOF');
INSERT INTO `area` VALUES ('78', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '21', 'SITE');
INSERT INTO `area` VALUES ('79', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '21', 'NORTH CORRIDOR');
INSERT INTO `area` VALUES ('80', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '21', 'FSA');
INSERT INTO `area` VALUES ('81', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '21', 'EB');
INSERT INTO `area` VALUES ('82', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '21', 'DC');
INSERT INTO `area` VALUES ('83', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '21', 'CNR');
INSERT INTO `area` VALUES ('84', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '21', 'LOADING DOCK');
INSERT INTO `area` VALUES ('85', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '22', 'FSA');
INSERT INTO `area` VALUES ('86', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '23', 'EB');
INSERT INTO `area` VALUES ('87', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '23', 'DC');
INSERT INTO `area` VALUES ('88', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '23', 'CNR');
INSERT INTO `area` VALUES ('89', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '24', 'ROOF');
INSERT INTO `area` VALUES ('90', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '25', 'SITE');
INSERT INTO `area` VALUES ('91', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '25', 'NORTH CORRIDOR');
INSERT INTO `area` VALUES ('92', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '25', 'FSA');
INSERT INTO `area` VALUES ('93', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '25', 'EB');
INSERT INTO `area` VALUES ('94', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '25', 'DC');
INSERT INTO `area` VALUES ('95', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '25', 'CNR');
INSERT INTO `area` VALUES ('96', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '25', 'LOADING DOCK');
INSERT INTO `area` VALUES ('97', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '26', 'FSA');
INSERT INTO `area` VALUES ('98', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '27', 'EB');
INSERT INTO `area` VALUES ('99', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '27', 'DC');
INSERT INTO `area` VALUES ('100', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '27', 'CNR');
INSERT INTO `area` VALUES ('101', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '27', 'ROOF');
INSERT INTO `area` VALUES ('102', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '29', 'SITE');
INSERT INTO `area` VALUES ('103', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '29', 'NORTH CORRIDOR');
INSERT INTO `area` VALUES ('104', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '29', 'FSA');
INSERT INTO `area` VALUES ('105', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '29', 'EB');
INSERT INTO `area` VALUES ('106', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '29', 'DC');
INSERT INTO `area` VALUES ('107', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '29', 'CNR');
INSERT INTO `area` VALUES ('108', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '29', 'LOADING DOCK');
INSERT INTO `area` VALUES ('109', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '30', 'FSA');
INSERT INTO `area` VALUES ('110', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '30', 'EB');
INSERT INTO `area` VALUES ('111', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '30', 'DC');
INSERT INTO `area` VALUES ('112', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '30', 'CNR');
INSERT INTO `area` VALUES ('113', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '31', 'ROOF');
INSERT INTO `area` VALUES ('114', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '32', 'SITE');
INSERT INTO `area` VALUES ('115', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '32', 'FSA');
INSERT INTO `area` VALUES ('116', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '32', 'EB');
INSERT INTO `area` VALUES ('117', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '32', 'MCU');
INSERT INTO `area` VALUES ('118', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '32', 'COOLING TOWER');
INSERT INTO `area` VALUES ('119', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '33', 'FSA');
INSERT INTO `area` VALUES ('120', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '33', 'GEN');
INSERT INTO `area` VALUES ('121', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '34', 'ROOF');
INSERT INTO `area` VALUES ('122', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '35', 'SITE');
INSERT INTO `area` VALUES ('123', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '35', 'PARKING LOT');
INSERT INTO `area` VALUES ('124', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '35', 'FSA');
INSERT INTO `area` VALUES ('125', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '35', 'EB');
INSERT INTO `area` VALUES ('126', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '35', 'DC');
INSERT INTO `area` VALUES ('127', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '35', 'LOADING DOCK');
INSERT INTO `area` VALUES ('128', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '35', 'SOUTH CORRIDOR');
INSERT INTO `area` VALUES ('129', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '36', 'FSA');
INSERT INTO `area` VALUES ('130', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '36', 'EB');
INSERT INTO `area` VALUES ('131', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '36', 'DC');
INSERT INTO `area` VALUES ('132', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '36', 'CCNR');
INSERT INTO `area` VALUES ('133', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '37', 'FSA');
INSERT INTO `area` VALUES ('134', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '37', 'EB');
INSERT INTO `area` VALUES ('135', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '37', 'DC');
INSERT INTO `area` VALUES ('136', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '37', 'CCNR');
INSERT INTO `area` VALUES ('137', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '38', 'FSA');
INSERT INTO `area` VALUES ('138', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '38', 'EB');
INSERT INTO `area` VALUES ('139', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '38', 'DC');
INSERT INTO `area` VALUES ('140', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '39', 'ROOF');
INSERT INTO `area` VALUES ('141', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '40', 'SITE');
INSERT INTO `area` VALUES ('142', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '40', 'FSA');
INSERT INTO `area` VALUES ('143', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '40', 'EB');
INSERT INTO `area` VALUES ('144', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '40', 'DC');
INSERT INTO `area` VALUES ('145', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '40', 'LOADING DOCK');
INSERT INTO `area` VALUES ('146', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '40', 'SOUTH CORRIDOR');
INSERT INTO `area` VALUES ('147', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '41', 'FSA');
INSERT INTO `area` VALUES ('148', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '41', 'EB');
INSERT INTO `area` VALUES ('149', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '41', 'DC');
INSERT INTO `area` VALUES ('150', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '41', 'CCNR');
INSERT INTO `area` VALUES ('151', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '42', 'FSA');
INSERT INTO `area` VALUES ('152', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '42', 'EB');
INSERT INTO `area` VALUES ('153', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '42', 'DC');
INSERT INTO `area` VALUES ('154', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '42', 'CCNR');
INSERT INTO `area` VALUES ('155', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '43', 'FSA');
INSERT INTO `area` VALUES ('156', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '43', 'EB');
INSERT INTO `area` VALUES ('157', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '43', 'DC');
INSERT INTO `area` VALUES ('158', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '44', 'ROOF');
INSERT INTO `area` VALUES ('159', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '45', 'SITE');
INSERT INTO `area` VALUES ('160', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '45', 'FSA');
INSERT INTO `area` VALUES ('161', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '45', 'EB');
INSERT INTO `area` VALUES ('162', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '45', 'DC');
INSERT INTO `area` VALUES ('163', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '45', 'LOADING DOCK');
INSERT INTO `area` VALUES ('164', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '45', 'SOUTH CORRIDOR');
INSERT INTO `area` VALUES ('165', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '46', 'FSA');
INSERT INTO `area` VALUES ('166', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '46', 'EB');
INSERT INTO `area` VALUES ('167', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '46', 'DC');
INSERT INTO `area` VALUES ('168', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '46', 'CCNR');
INSERT INTO `area` VALUES ('169', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '47', 'FSA');
INSERT INTO `area` VALUES ('170', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '47', 'EB');
INSERT INTO `area` VALUES ('171', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '47', 'DC');
INSERT INTO `area` VALUES ('172', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '47', 'CCNR');
INSERT INTO `area` VALUES ('173', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '48', 'FSA');
INSERT INTO `area` VALUES ('174', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '48', 'EB');
INSERT INTO `area` VALUES ('175', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '48', 'DC');
INSERT INTO `area` VALUES ('176', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '49', 'ROOF');
INSERT INTO `area` VALUES ('177', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', '1 SERIES SUBSTATION');
INSERT INTO `area` VALUES ('178', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', '2 SERIES SUBSTATION');
INSERT INTO `area` VALUES ('179', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', 'WT TRAILER COMPLEX');
INSERT INTO `area` VALUES ('180', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', 'WT PARKING LOT');
INSERT INTO `area` VALUES ('181', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', 'SITE MAIN ENTRANCE');
INSERT INTO `area` VALUES ('182', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', 'OWNER ENTRANCE');
INSERT INTO `area` VALUES ('183', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', 'LAYDOWN YARD');
INSERT INTO `area` VALUES ('184', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', 'PUMP STATION');
INSERT INTO `area` VALUES ('185', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', '2 SERIES FUEL FARM');
INSERT INTO `area` VALUES ('186', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', '3 SERIES FUEL FARM');
INSERT INTO `area` VALUES ('187', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '50', 'SUBCONTRACTOR PARKING LOT');

-- ----------------------------
-- Table structure for body_part
-- ----------------------------
DROP TABLE IF EXISTS `body_part`;
CREATE TABLE `body_part` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `body_part` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of body_part
-- ----------------------------
INSERT INTO `body_part` VALUES ('6', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'N/A');
INSERT INTO `body_part` VALUES ('7', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Finger');
INSERT INTO `body_part` VALUES ('8', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Hand');
INSERT INTO `body_part` VALUES ('9', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Arm');
INSERT INTO `body_part` VALUES ('10', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Shoulder');
INSERT INTO `body_part` VALUES ('11', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Foot');
INSERT INTO `body_part` VALUES ('12', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Ankle');
INSERT INTO `body_part` VALUES ('13', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Leg');
INSERT INTO `body_part` VALUES ('14', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Knee');
INSERT INTO `body_part` VALUES ('15', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Eye');
INSERT INTO `body_part` VALUES ('16', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Ear');
INSERT INTO `body_part` VALUES ('17', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Face');
INSERT INTO `body_part` VALUES ('18', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Head');
INSERT INTO `body_part` VALUES ('20', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Back');
INSERT INTO `body_part` VALUES ('21', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Whole Body');
INSERT INTO `body_part` VALUES ('22', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Illness');

-- ----------------------------
-- Table structure for building
-- ----------------------------
DROP TABLE IF EXISTS `building`;
CREATE TABLE `building` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  `building` varchar(255) NOT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `jobsite_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bu_jo_id` (`jobsite_id`),
  CONSTRAINT `bu_jo_id` FOREIGN KEY (`jobsite_id`) REFERENCES `jobsite` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of building
-- ----------------------------
INSERT INTO `building` VALUES ('1', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:28', 'SLN1-A', 'One Story Data Center', null, '1');
INSERT INTO `building` VALUES ('2', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN1-B', 'One Story Data Center', null, '1');
INSERT INTO `building` VALUES ('3', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN1-C', 'One Story Data Center', null, '1');
INSERT INTO `building` VALUES ('4', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN2-A', 'Two Story Data Center', null, '1');
INSERT INTO `building` VALUES ('5', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN2-B', 'Two Story Data Center', null, '1');
INSERT INTO `building` VALUES ('6', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN2-C', 'Two Story Data Center', null, '1');
INSERT INTO `building` VALUES ('7', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN2-D', 'Two Story Data Center', null, '1');
INSERT INTO `building` VALUES ('8', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN2-E', 'Two Story Data Center', null, '1');
INSERT INTO `building` VALUES ('9', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN2-F', 'Two Story Data Center', null, '1');
INSERT INTO `building` VALUES ('10', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN2-G', 'Two Story Data Center', null, '1');
INSERT INTO `building` VALUES ('11', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN3-Mega CUB', 'Two Story Central Utility Building', null, '1');
INSERT INTO `building` VALUES ('12', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN3-A', 'Four Story Data Center', null, '1');
INSERT INTO `building` VALUES ('13', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN3-B', 'Four Story Data Center', null, '1');
INSERT INTO `building` VALUES ('14', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN3-C', 'Four Story Data Center', null, '1');
INSERT INTO `building` VALUES ('15', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', 'SLN SITE', 'SLN Site Areas (Don\'t have job #\'s)', null, '1');

-- ----------------------------
-- Table structure for comment
-- ----------------------------
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `app_case_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `report_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `com_us_id` (`user_id`),
  KEY `com_ap_id` (`app_case_id`),
  KEY `com_re_po_id` (`report_type_id`),
  CONSTRAINT `com_ap_id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`),
  CONSTRAINT `com_re_po_id` FOREIGN KEY (`report_type_id`) REFERENCES `report_type` (`id`),
  CONSTRAINT `com_us_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of comment
-- ----------------------------

-- ----------------------------
-- Table structure for content
-- ----------------------------
DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(1) NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  `app_case_id` int(11) NOT NULL,
  `type` varchar(75) NOT NULL,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `con_ap_id` (`app_case_id`),
  CONSTRAINT `con_ap_id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of content
-- ----------------------------

-- ----------------------------
-- Table structure for contractor
-- ----------------------------
DROP TABLE IF EXISTS `contractor`;
CREATE TABLE `contractor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(1) NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  `contractor` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contractor
-- ----------------------------
INSERT INTO `contractor` VALUES ('11', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ACADEMY ROOFING', '');
INSERT INTO `contractor` VALUES ('12', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ACME DOCK SPECIALISTS', '');
INSERT INTO `contractor` VALUES ('13', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ADDITIONAL CONCRETE SERVICES', '');
INSERT INTO `contractor` VALUES ('14', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'AG CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('15', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'AHERN RENTALS', '');
INSERT INTO `contractor` VALUES ('16', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ALL PURPOSE CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('17', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ALLIED CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('18', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'AMERICAN DRAINAGE SYSTEMS INC', '');
INSERT INTO `contractor` VALUES ('19', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'AMERICAN FIREPROOFING', '');
INSERT INTO `contractor` VALUES ('20', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'AWS NEBRASKA LLC/HUSKER GLASS', '');
INSERT INTO `contractor` VALUES ('21', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'B&G VENDING', '');
INSERT INTO `contractor` VALUES ('22', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BADGER DAYLIGHTING', '');
INSERT INTO `contractor` VALUES ('23', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BALCON', '');
INSERT INTO `contractor` VALUES ('24', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BECKER TRENCHING & WATER', '');
INSERT INTO `contractor` VALUES ('25', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BERKEL & COMPANY', '');
INSERT INTO `contractor` VALUES ('26', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BIL-DEN GLASS', '');
INSERT INTO `contractor` VALUES ('27', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BLUFFS ELECTRIC', '');
INSERT INTO `contractor` VALUES ('28', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BRUEGMAN EXCAVATION', '');
INSERT INTO `contractor` VALUES ('29', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CAPSTONE CLEANING', '');
INSERT INTO `contractor` VALUES ('30', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CLEAR WATER SYSTEMS', '');
INSERT INTO `contractor` VALUES ('31', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'COMMERCIAL SEEDING', '');
INSERT INTO `contractor` VALUES ('32', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'COMMONWEALTH ELECTRIC', '');
INSERT INTO `contractor` VALUES ('33', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CONTINENTAL FIRE', '');
INSERT INTO `contractor` VALUES ('34', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CONTROLLED CONTAMINATION SERVICES', '');
INSERT INTO `contractor` VALUES ('35', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CORESLAB', '');
INSERT INTO `contractor` VALUES ('36', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'DAEDALUS CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('37', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'DANS OVERHEAD DOORS AND MORE', '');
INSERT INTO `contractor` VALUES ('38', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'DAVIS ERECTION', '');
INSERT INTO `contractor` VALUES ('39', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'D.C. AWNINGS', '');
INSERT INTO `contractor` VALUES ('40', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'DOORS INC', '');
INSERT INTO `contractor` VALUES ('41', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'DUKE AERIAL', '');
INSERT INTO `contractor` VALUES ('42', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'DWS MIDWEST LLC', '');
INSERT INTO `contractor` VALUES ('43', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'E&K OF OMAHA', '');
INSERT INTO `contractor` VALUES ('44', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'EATON', '');
INSERT INTO `contractor` VALUES ('45', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ECHO ELECTRIC SUPPLY', '');
INSERT INTO `contractor` VALUES ('46', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ELECTRIC COMPANY OF OMAHA', '');
INSERT INTO `contractor` VALUES ('47', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'EMERSON ERS', '');
INSERT INTO `contractor` VALUES ('48', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'EMERSON NETWORK POWER', '');
INSERT INTO `contractor` VALUES ('49', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'EPCO', '');
INSERT INTO `contractor` VALUES ('50', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FABCON', '');
INSERT INTO `contractor` VALUES ('51', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FACILITY DYNAMICS ENGINEERING', '');
INSERT INTO `contractor` VALUES ('52', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FIRETEK', '');
INSERT INTO `contractor` VALUES ('53', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FLATBED EXPRESS INC', '');
INSERT INTO `contractor` VALUES ('54', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FLOORS INC', '');
INSERT INTO `contractor` VALUES ('55', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'G & T LAWN AND LANDSCAPE', '');
INSERT INTO `contractor` VALUES ('56', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'G4S SECURE SOLUTIONS', '');
INSERT INTO `contractor` VALUES ('57', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'GENERAL EXCAVATING', '');
INSERT INTO `contractor` VALUES ('58', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'GENERAL FIRE & SAFETY', '');
INSERT INTO `contractor` VALUES ('59', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'GLOBAL POWER TECHNOLOGIES', '');
INSERT INTO `contractor` VALUES ('60', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'GOOGLE', '');
INSERT INTO `contractor` VALUES ('61', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'GRAHAM CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('62', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'GRAYBAR ELECTRIC', '');
INSERT INTO `contractor` VALUES ('63', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'HGM ASSOCIATES', '');
INSERT INTO `contractor` VALUES ('64', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'HUELER TILE', '');
INSERT INTO `contractor` VALUES ('65', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'INTERSTATE POWER SYSTEMS', '');
INSERT INTO `contractor` VALUES ('66', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'JOHNSON CONTROLS', '');
INSERT INTO `contractor` VALUES ('67', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'JOHNSON DRYWALL', '');
INSERT INTO `contractor` VALUES ('68', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'JOHNSON HARDWARE', '');
INSERT INTO `contractor` VALUES ('69', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'JR BARGER & SONS', '');
INSERT INTO `contractor` VALUES ('70', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'KDENT', '');
INSERT INTO `contractor` VALUES ('71', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'KEHM CONTRACTORS', '');
INSERT INTO `contractor` VALUES ('72', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'KEYSTONE GLASS', '');
INSERT INTO `contractor` VALUES ('73', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'KLING STUBBINS', '');
INSERT INTO `contractor` VALUES ('74', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'LEGGETTE BRASHEARS & GRAHAM INC', '');
INSERT INTO `contractor` VALUES ('75', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'LEICK CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('76', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'LOTTMAN CARPENTER', '');
INSERT INTO `contractor` VALUES ('77', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'LUVATA GRENADA', '');
INSERT INTO `contractor` VALUES ('78', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MAINELLI MECHANICAL', '');
INSERT INTO `contractor` VALUES ('79', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MALCHOW CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('80', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MCGILL BROTHERS', '');
INSERT INTO `contractor` VALUES ('81', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MEDCOR', '');
INSERT INTO `contractor` VALUES ('82', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MFT CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('83', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MHC SYSTEMS', '');
INSERT INTO `contractor` VALUES ('84', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MIDAMERICAN ENERGY', '');
INSERT INTO `contractor` VALUES ('85', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MID IOWA ENVIRONMENTAL', '');
INSERT INTO `contractor` VALUES ('86', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MID-PLAINS INSULATION', '');
INSERT INTO `contractor` VALUES ('87', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MIDWEST DRYWALL', '');
INSERT INTO `contractor` VALUES ('88', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MIDWEST SCAFFOLD', '');
INSERT INTO `contractor` VALUES ('89', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MILLER ELECTRIC', '');
INSERT INTO `contractor` VALUES ('90', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MMC CONTRACTORS', '');
INSERT INTO `contractor` VALUES ('91', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MORRISSEY ENGINEERING', '');
INSERT INTO `contractor` VALUES ('92', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MPSS', '');
INSERT INTO `contractor` VALUES ('93', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'MTU ONSITE ENERGY', '');
INSERT INTO `contractor` VALUES ('94', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'NATIONAL CONCRETE CUTTING', '');
INSERT INTO `contractor` VALUES ('95', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'NEBRASKA IOWA SUPPLY', '');
INSERT INTO `contractor` VALUES ('96', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'NEWCOMB & BOYD', '');
INSERT INTO `contractor` VALUES ('97', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'NMC POWER SYSTEMS', '');
INSERT INTO `contractor` VALUES ('98', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'NORTHWAY WELL & PUMP', '');
INSERT INTO `contractor` VALUES ('99', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'OLSSON ASSOCIATES', '');
INSERT INTO `contractor` VALUES ('100', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'O´KEEFE ELEVATOR', '');
INSERT INTO `contractor` VALUES ('101', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PAR ELECTRIC', '');
INSERT INTO `contractor` VALUES ('102', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PARSONS ELECTRIC', '');
INSERT INTO `contractor` VALUES ('103', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PATRIOT CRANE & RIGGING', '');
INSERT INTO `contractor` VALUES ('104', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PAYNECREST ELECTRIC & COMMUNICATIONS', '');
INSERT INTO `contractor` VALUES ('105', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PCI', '');
INSERT INTO `contractor` VALUES ('106', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PENDERGRAPH SYSTEMS', '');
INSERT INTO `contractor` VALUES ('107', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PERMA-PIPE', '');
INSERT INTO `contractor` VALUES ('108', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PETERSON CONTRACTORS', '');
INSERT INTO `contractor` VALUES ('109', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PINK GRADING', '');
INSERT INTO `contractor` VALUES ('110', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PROJECT TEHCNOLOGIES GROUP INC', '');
INSERT INTO `contractor` VALUES ('111', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PRYCO', '');
INSERT INTO `contractor` VALUES ('112', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PVS', '');
INSERT INTO `contractor` VALUES ('113', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PYRAMID SAFETY', '');
INSERT INTO `contractor` VALUES ('114', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'QC COMPANIES', '');
INSERT INTO `contractor` VALUES ('115', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'RAPID REBAR INC', '');
INSERT INTO `contractor` VALUES ('116', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'READY MIXED CONCRETE', '');
INSERT INTO `contractor` VALUES ('117', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'RIEKES', '');
INSERT INTO `contractor` VALUES ('118', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ROLLING PLAINS CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('119', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'RODRIGUEZ MECHANICAL CONTRACTORS', '');
INSERT INTO `contractor` VALUES ('120', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ROVISYS BUILDING TECHNOLOGIES', '');
INSERT INTO `contractor` VALUES ('121', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'S&W FENCE', '');
INSERT INTO `contractor` VALUES ('122', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SACHS ELECTRIC', '');
INSERT INTO `contractor` VALUES ('123', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SAFETY SOLUTION', '');
INSERT INTO `contractor` VALUES ('124', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SAFWAY', '');
INSERT INTO `contractor` VALUES ('125', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SARATOGA ROOFING', '');
INSERT INTO `contractor` VALUES ('126', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SCHINDLER ELEVATOR COMPANY', '');
INSERT INTO `contractor` VALUES ('127', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SCHNEIDER ELECTRIC', '');
INSERT INTO `contractor` VALUES ('128', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SCHUERMAN WELDING INC', '');
INSERT INTO `contractor` VALUES ('129', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SEEDORFF MASONRY', '');
INSERT INTO `contractor` VALUES ('130', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SHERBONDYS GARDEN CENTER', '');
INSERT INTO `contractor` VALUES ('131', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SHERMCO INDUSTRIES', '');
INSERT INTO `contractor` VALUES ('132', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SIEMENS', '');
INSERT INTO `contractor` VALUES ('133', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SPX', '');
INSERT INTO `contractor` VALUES ('134', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'STEPHENS & SMITH', '');
INSERT INTO `contractor` VALUES ('135', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SUNBELT RENTALS', '');
INSERT INTO `contractor` VALUES ('136', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SUPERCLEAN JOBSITE', '');
INSERT INTO `contractor` VALUES ('137', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SYS-TEK', '');
INSERT INTO `contractor` VALUES ('138', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'TERRY MCGILL', '');
INSERT INTO `contractor` VALUES ('139', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'THIELE GEOTECH', '');
INSERT INTO `contractor` VALUES ('140', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'TRADESMAN INTERNATIONAL', '');
INSERT INTO `contractor` VALUES ('141', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'TRUE FIREPROOFING', '');
INSERT INTO `contractor` VALUES ('142', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'TURD TOTERS', '');
INSERT INTO `contractor` VALUES ('143', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'TYCO INTEGRATED SECURITY', '');
INSERT INTO `contractor` VALUES ('144', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'UNITED RENTALS', '');
INSERT INTO `contractor` VALUES ('145', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'WB CONSTRUCTION', '');
INSERT INTO `contractor` VALUES ('146', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'WILLIAM SCOTTSMAN', '');
INSERT INTO `contractor` VALUES ('147', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'WILLIAMS RESTORATION', '');
INSERT INTO `contractor` VALUES ('148', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'WHITING-TURNER', '');
INSERT INTO `contractor` VALUES ('149', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CLIENT', '');

-- ----------------------------
-- Table structure for device
-- ----------------------------
DROP TABLE IF EXISTS `device`;
CREATE TABLE `device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `device` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `de_us_id` (`user_id`),
  CONSTRAINT `de_us_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of device
-- ----------------------------

-- ----------------------------
-- Table structure for floor
-- ----------------------------
DROP TABLE IF EXISTS `floor`;
CREATE TABLE `floor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `building_id` int(11) NOT NULL,
  `floor` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fo_bu_id` (`building_id`),
  CONSTRAINT `fo_bu_id` FOREIGN KEY (`building_id`) REFERENCES `building` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of floor
-- ----------------------------
INSERT INTO `floor` VALUES ('1', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', '1');
INSERT INTO `floor` VALUES ('2', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '1', '2');
INSERT INTO `floor` VALUES ('3', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '2', '1');
INSERT INTO `floor` VALUES ('4', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '2', '2');
INSERT INTO `floor` VALUES ('5', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '3', '1');
INSERT INTO `floor` VALUES ('6', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '3', '2');
INSERT INTO `floor` VALUES ('7', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '4', '1');
INSERT INTO `floor` VALUES ('8', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '4', '2');
INSERT INTO `floor` VALUES ('9', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '4', '3');
INSERT INTO `floor` VALUES ('10', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', '1');
INSERT INTO `floor` VALUES ('11', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', '2');
INSERT INTO `floor` VALUES ('12', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '5', '3');
INSERT INTO `floor` VALUES ('13', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '6', '1');
INSERT INTO `floor` VALUES ('14', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '6', '1,5');
INSERT INTO `floor` VALUES ('15', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '6', '2');
INSERT INTO `floor` VALUES ('16', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '6', '3');
INSERT INTO `floor` VALUES ('17', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', '1');
INSERT INTO `floor` VALUES ('18', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', '1,5');
INSERT INTO `floor` VALUES ('19', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', '2');
INSERT INTO `floor` VALUES ('20', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '7', '3');
INSERT INTO `floor` VALUES ('21', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '8', '1');
INSERT INTO `floor` VALUES ('22', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '8', '1,5');
INSERT INTO `floor` VALUES ('23', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '8', '2');
INSERT INTO `floor` VALUES ('24', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '8', '3');
INSERT INTO `floor` VALUES ('25', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '9', '1');
INSERT INTO `floor` VALUES ('26', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '9', '1,5');
INSERT INTO `floor` VALUES ('27', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '9', '2');
INSERT INTO `floor` VALUES ('28', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '9', '3');
INSERT INTO `floor` VALUES ('29', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', '1');
INSERT INTO `floor` VALUES ('30', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', '2');
INSERT INTO `floor` VALUES ('31', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '10', '3');
INSERT INTO `floor` VALUES ('32', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '11', '1');
INSERT INTO `floor` VALUES ('33', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '11', '2');
INSERT INTO `floor` VALUES ('34', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '11', '3');
INSERT INTO `floor` VALUES ('35', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '12', '1');
INSERT INTO `floor` VALUES ('36', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '12', '2');
INSERT INTO `floor` VALUES ('37', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '12', '3');
INSERT INTO `floor` VALUES ('38', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '12', '4');
INSERT INTO `floor` VALUES ('39', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '12', '5');
INSERT INTO `floor` VALUES ('40', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', '1');
INSERT INTO `floor` VALUES ('41', '1', '2015-05-29 16:25:25', '2015-05-29 16:25:25', '13', '2');
INSERT INTO `floor` VALUES ('42', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '13', '3');
INSERT INTO `floor` VALUES ('43', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '13', '4');
INSERT INTO `floor` VALUES ('44', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '13', '5');
INSERT INTO `floor` VALUES ('45', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '14', '1');
INSERT INTO `floor` VALUES ('46', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '14', '2');
INSERT INTO `floor` VALUES ('47', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '14', '3');
INSERT INTO `floor` VALUES ('48', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '14', '4');
INSERT INTO `floor` VALUES ('49', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '14', '5');
INSERT INTO `floor` VALUES ('50', '1', '0000-00-00 00:00:00', '2015-05-29 16:25:25', '15', '1');

-- ----------------------------
-- Table structure for follower
-- ----------------------------
DROP TABLE IF EXISTS `follower`;
CREATE TABLE `follower` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_case_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fo_us_id` (`user_id`),
  KEY `fo_ap_id` (`app_case_id`),
  CONSTRAINT `fo_ap_id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`),
  CONSTRAINT `fo_us_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of follower
-- ----------------------------

-- ----------------------------
-- Table structure for injury_type
-- ----------------------------
DROP TABLE IF EXISTS `injury_type`;
CREATE TABLE `injury_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `injury_type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of injury_type
-- ----------------------------
INSERT INTO `injury_type` VALUES ('5', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'N/A');
INSERT INTO `injury_type` VALUES ('6', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Abrasion');
INSERT INTO `injury_type` VALUES ('7', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Administration of OTC Medication');
INSERT INTO `injury_type` VALUES ('8', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Allergic (Ill)');
INSERT INTO `injury_type` VALUES ('9', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Allergic (Inj)');
INSERT INTO `injury_type` VALUES ('10', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Altered Mental State');
INSERT INTO `injury_type` VALUES ('11', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Amputation');
INSERT INTO `injury_type` VALUES ('12', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Anaphylaxis');
INSERT INTO `injury_type` VALUES ('13', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Animal Bite');
INSERT INTO `injury_type` VALUES ('14', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Arthritis');
INSERT INTO `injury_type` VALUES ('15', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Avulsion');
INSERT INTO `injury_type` VALUES ('16', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Barotrauma');
INSERT INTO `injury_type` VALUES ('17', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Blister');
INSERT INTO `injury_type` VALUES ('18', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Burn (1st Degree)');
INSERT INTO `injury_type` VALUES ('19', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Burn (2nd Degree)');
INSERT INTO `injury_type` VALUES ('20', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Burn (3rd Degree)');
INSERT INTO `injury_type` VALUES ('21', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Caught Between');
INSERT INTO `injury_type` VALUES ('22', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Cerumen');
INSERT INTO `injury_type` VALUES ('23', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Chemical Exposure (Ill)');
INSERT INTO `injury_type` VALUES ('24', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Chemical Exposure (Inj)');
INSERT INTO `injury_type` VALUES ('25', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Contusion');
INSERT INTO `injury_type` VALUES ('26', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Crush Injury');
INSERT INTO `injury_type` VALUES ('27', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Deformity');
INSERT INTO `injury_type` VALUES ('28', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Dental Injury');
INSERT INTO `injury_type` VALUES ('29', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Diarrhea');
INSERT INTO `injury_type` VALUES ('30', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Dislocation');
INSERT INTO `injury_type` VALUES ('31', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Dizziness');
INSERT INTO `injury_type` VALUES ('32', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Earache');
INSERT INTO `injury_type` VALUES ('33', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Electrical Shock Injury/Exposure');
INSERT INTO `injury_type` VALUES ('34', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Elevated Blood Pressure');
INSERT INTO `injury_type` VALUES ('35', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Expistaxis (Ill)');
INSERT INTO `injury_type` VALUES ('36', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Expistaxis (Inj)');
INSERT INTO `injury_type` VALUES ('37', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Evisceration');
INSERT INTO `injury_type` VALUES ('38', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Eye Irritation (Ill)');
INSERT INTO `injury_type` VALUES ('39', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Eye Irritation (Inj)');
INSERT INTO `injury_type` VALUES ('40', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Fatality');
INSERT INTO `injury_type` VALUES ('41', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Fatigue');
INSERT INTO `injury_type` VALUES ('42', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Foreign Body');
INSERT INTO `injury_type` VALUES ('43', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Fracture');
INSERT INTO `injury_type` VALUES ('44', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Frost Nip');
INSERT INTO `injury_type` VALUES ('45', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Frostbite');
INSERT INTO `injury_type` VALUES ('46', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Head Trauma');
INSERT INTO `injury_type` VALUES ('47', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Headache');

-- ----------------------------
-- Table structure for jobsite
-- ----------------------------
DROP TABLE IF EXISTS `jobsite`;
CREATE TABLE `jobsite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `jobsite` varchar(255) NOT NULL,
  `photo_allowed` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jobsite
-- ----------------------------
INSERT INTO `jobsite` VALUES ('1', '1', '2015-05-29 16:21:59', '2015-05-29 16:22:01', 'SLN\r\n', '1');
INSERT INTO `jobsite` VALUES ('16', '1', '2015-06-01 19:16:13', '2015-06-01 19:16:13', 'JobSite 1', '0');
INSERT INTO `jobsite` VALUES ('17', '1', '2015-06-01 19:16:19', '2015-06-01 19:16:19', 'JobSite 2', '0');
INSERT INTO `jobsite` VALUES ('18', '1', '2015-06-01 19:16:30', '2015-06-01 19:16:30', 'JobSite 3', '0');
INSERT INTO `jobsite` VALUES ('19', '1', '2015-06-01 19:16:37', '2015-06-01 19:16:37', 'JobSite 4', '0');
INSERT INTO `jobsite` VALUES ('20', '1', '2015-06-01 19:16:47', '2015-06-01 19:16:52', 'JobSite 5', '0');
INSERT INTO `jobsite` VALUES ('21', '1', '2015-06-01 19:17:01', '2015-06-01 19:17:01', 'JobSite 6', '0');
INSERT INTO `jobsite` VALUES ('22', '1', '2015-06-01 19:17:09', '2015-06-01 19:17:09', 'JobSite 7', '0');
INSERT INTO `jobsite` VALUES ('23', '1', '2015-06-01 19:17:17', '2015-06-01 19:17:17', 'JobSite 8', '0');
INSERT INTO `jobsite` VALUES ('24', '1', '2015-06-01 19:17:22', '2015-06-01 19:17:22', 'JobSite 9', '0');
INSERT INTO `jobsite` VALUES ('25', '1', '2015-06-01 19:17:28', '2015-06-01 19:17:28', 'JobSite 10', '0');

-- ----------------------------
-- Table structure for notification
-- ----------------------------
DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `app_case_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_case_history_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `no_ap_id` (`app_case_id`),
  KEY `no_us_id` (`user_id`),
  KEY `no_ap_hi_id` (`app_case_history_id`),
  CONSTRAINT `no_ap_hi_id` FOREIGN KEY (`app_case_history_id`) REFERENCES `app_case_history` (`id`),
  CONSTRAINT `no_ap_id` FOREIGN KEY (`app_case_id`) REFERENCES `app_case` (`id`),
  CONSTRAINT `no_us_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of notification
-- ----------------------------

-- ----------------------------
-- Table structure for permission
-- ----------------------------
DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pe_ro_id` (`role_id`),
  KEY `pe_ac_id` (`action_id`),
  CONSTRAINT `pe_ac_id` FOREIGN KEY (`action_id`) REFERENCES `action` (`id`),
  CONSTRAINT `pe_ro_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of permission
-- ----------------------------
INSERT INTO `permission` VALUES ('13', '4', '9');
INSERT INTO `permission` VALUES ('14', '4', '10');
INSERT INTO `permission` VALUES ('15', '4', '11');
INSERT INTO `permission` VALUES ('16', '4', '12');
INSERT INTO `permission` VALUES ('17', '4', '13');
INSERT INTO `permission` VALUES ('19', '4', '14');
INSERT INTO `permission` VALUES ('20', '4', '15');
INSERT INTO `permission` VALUES ('21', '4', '16');
INSERT INTO `permission` VALUES ('22', '4', '17');
INSERT INTO `permission` VALUES ('23', '4', '18');
INSERT INTO `permission` VALUES ('24', '4', '19');
INSERT INTO `permission` VALUES ('25', '4', '20');
INSERT INTO `permission` VALUES ('26', '4', '21');
INSERT INTO `permission` VALUES ('27', '4', '22');
INSERT INTO `permission` VALUES ('28', '4', '24');
INSERT INTO `permission` VALUES ('29', '4', '28');
INSERT INTO `permission` VALUES ('31', '4', '36');
INSERT INTO `permission` VALUES ('32', '5', '9');
INSERT INTO `permission` VALUES ('33', '5', '10');
INSERT INTO `permission` VALUES ('34', '5', '11');
INSERT INTO `permission` VALUES ('35', '5', '12');
INSERT INTO `permission` VALUES ('37', '5', '13');
INSERT INTO `permission` VALUES ('38', '5', '14');
INSERT INTO `permission` VALUES ('39', '5', '15');
INSERT INTO `permission` VALUES ('40', '5', '16');
INSERT INTO `permission` VALUES ('41', '5', '17');
INSERT INTO `permission` VALUES ('42', '5', '18');
INSERT INTO `permission` VALUES ('43', '5', '19');
INSERT INTO `permission` VALUES ('44', '5', '20');
INSERT INTO `permission` VALUES ('45', '5', '21');
INSERT INTO `permission` VALUES ('46', '5', '22');
INSERT INTO `permission` VALUES ('48', '5', '24');
INSERT INTO `permission` VALUES ('52', '5', '28');
INSERT INTO `permission` VALUES ('53', '5', '36');
INSERT INTO `permission` VALUES ('55', '2', '9');
INSERT INTO `permission` VALUES ('56', '2', '10');
INSERT INTO `permission` VALUES ('57', '2', '11');
INSERT INTO `permission` VALUES ('58', '2', '12');
INSERT INTO `permission` VALUES ('59', '2', '13');
INSERT INTO `permission` VALUES ('60', '2', '14');
INSERT INTO `permission` VALUES ('61', '2', '15');
INSERT INTO `permission` VALUES ('62', '2', '16');
INSERT INTO `permission` VALUES ('64', '2', '18');
INSERT INTO `permission` VALUES ('65', '2', '19');
INSERT INTO `permission` VALUES ('66', '2', '20');
INSERT INTO `permission` VALUES ('67', '2', '21');
INSERT INTO `permission` VALUES ('68', '2', '22');
INSERT INTO `permission` VALUES ('69', '2', '36');
INSERT INTO `permission` VALUES ('72', '6', '9');
INSERT INTO `permission` VALUES ('73', '6', '10');
INSERT INTO `permission` VALUES ('74', '6', '11');
INSERT INTO `permission` VALUES ('75', '6', '12');
INSERT INTO `permission` VALUES ('76', '6', '13');
INSERT INTO `permission` VALUES ('77', '6', '14');
INSERT INTO `permission` VALUES ('78', '6', '15');
INSERT INTO `permission` VALUES ('79', '6', '16');
INSERT INTO `permission` VALUES ('80', '6', '17');
INSERT INTO `permission` VALUES ('81', '6', '18');
INSERT INTO `permission` VALUES ('82', '6', '19');
INSERT INTO `permission` VALUES ('83', '6', '20');
INSERT INTO `permission` VALUES ('84', '6', '21');
INSERT INTO `permission` VALUES ('85', '6', '22');
INSERT INTO `permission` VALUES ('86', '6', '23');
INSERT INTO `permission` VALUES ('87', '6', '24');
INSERT INTO `permission` VALUES ('88', '6', '25');
INSERT INTO `permission` VALUES ('89', '6', '26');
INSERT INTO `permission` VALUES ('90', '6', '27');
INSERT INTO `permission` VALUES ('91', '6', '28');
INSERT INTO `permission` VALUES ('92', '6', '29');
INSERT INTO `permission` VALUES ('93', '6', '30');
INSERT INTO `permission` VALUES ('94', '6', '31');
INSERT INTO `permission` VALUES ('95', '6', '32');
INSERT INTO `permission` VALUES ('96', '6', '35');
INSERT INTO `permission` VALUES ('97', '6', '36');
INSERT INTO `permission` VALUES ('98', '6', '37');
INSERT INTO `permission` VALUES ('99', '7', '9');
INSERT INTO `permission` VALUES ('100', '7', '10');
INSERT INTO `permission` VALUES ('101', '7', '11');
INSERT INTO `permission` VALUES ('102', '7', '12');
INSERT INTO `permission` VALUES ('103', '7', '13');
INSERT INTO `permission` VALUES ('104', '7', '14');
INSERT INTO `permission` VALUES ('105', '7', '15');
INSERT INTO `permission` VALUES ('106', '7', '16');
INSERT INTO `permission` VALUES ('107', '7', '18');
INSERT INTO `permission` VALUES ('108', '7', '19');
INSERT INTO `permission` VALUES ('109', '7', '20');
INSERT INTO `permission` VALUES ('110', '7', '21');
INSERT INTO `permission` VALUES ('111', '7', '22');
INSERT INTO `permission` VALUES ('113', '7', '36');
INSERT INTO `permission` VALUES ('114', '8', '21');
INSERT INTO `permission` VALUES ('115', '1', '9');
INSERT INTO `permission` VALUES ('116', '1', '10');
INSERT INTO `permission` VALUES ('117', '1', '11');
INSERT INTO `permission` VALUES ('118', '1', '12');
INSERT INTO `permission` VALUES ('119', '1', '13');
INSERT INTO `permission` VALUES ('120', '1', '14');
INSERT INTO `permission` VALUES ('121', '1', '15');
INSERT INTO `permission` VALUES ('122', '1', '16');
INSERT INTO `permission` VALUES ('123', '1', '17');
INSERT INTO `permission` VALUES ('124', '1', '18');
INSERT INTO `permission` VALUES ('125', '1', '19');
INSERT INTO `permission` VALUES ('126', '1', '20');
INSERT INTO `permission` VALUES ('127', '1', '38');
INSERT INTO `permission` VALUES ('128', '11', '38');
INSERT INTO `permission` VALUES ('129', '1', '39');
INSERT INTO `permission` VALUES ('130', '11', '39');
INSERT INTO `permission` VALUES ('131', '1', '40');
INSERT INTO `permission` VALUES ('132', '11', '40');

-- ----------------------------
-- Table structure for report_topic
-- ----------------------------
DROP TABLE IF EXISTS `report_topic`;
CREATE TABLE `report_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `report_topic` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of report_topic
-- ----------------------------
INSERT INTO `report_topic` VALUES ('4', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'EMS Transport');
INSERT INTO `report_topic` VALUES ('5', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Environmental');
INSERT INTO `report_topic` VALUES ('6', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Fire');
INSERT INTO `report_topic` VALUES ('7', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'First Aid');
INSERT INTO `report_topic` VALUES ('8', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Incident');
INSERT INTO `report_topic` VALUES ('9', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Near Miss');
INSERT INTO `report_topic` VALUES ('10', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'OSHA Visit');
INSERT INTO `report_topic` VALUES ('11', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Police');
INSERT INTO `report_topic` VALUES ('12', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Severe Weather');

-- ----------------------------
-- Table structure for report_type
-- ----------------------------
DROP TABLE IF EXISTS `report_type`;
CREATE TABLE `report_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `report_type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of report_type
-- ----------------------------
INSERT INTO `report_type` VALUES ('1', '1', '0000-00-00 00:00:00', '2015-05-12 18:33:32', 'PRELIMINARY');
INSERT INTO `report_type` VALUES ('2', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'INTERIM');
INSERT INTO `report_type` VALUES ('3', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FINAL');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `role` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'admin');
INSERT INTO `role` VALUES ('2', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'wt personnel');
INSERT INTO `role` VALUES ('3', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'wt safety personnel');
INSERT INTO `role` VALUES ('4', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'wt executive manager');
INSERT INTO `role` VALUES ('5', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'wt project manager');
INSERT INTO `role` VALUES ('6', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'system admin');
INSERT INTO `role` VALUES ('7', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'safety contractor');
INSERT INTO `role` VALUES ('8', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'client manager');
INSERT INTO `role` VALUES ('10', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'contractor owner');
INSERT INTO `role` VALUES ('11', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'contractor foreman');
INSERT INTO `role` VALUES ('12', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'contractor safety manager');
INSERT INTO `role` VALUES ('13', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'contractor project manager');
INSERT INTO `role` VALUES ('14', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'contractor employee');
INSERT INTO `role` VALUES ('15', '1', '2015-05-06 16:28:10', '2015-05-06 16:28:10', 'client safety personnel');

-- ----------------------------
-- Table structure for session
-- ----------------------------
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `token` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `se_us_id` (`user_id`),
  KEY `se_de_id` (`device_id`),
  CONSTRAINT `se_de_id` FOREIGN KEY (`device_id`) REFERENCES `device` (`id`),
  CONSTRAINT `se_us_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of session
-- ----------------------------

-- ----------------------------
-- Table structure for trade
-- ----------------------------
DROP TABLE IF EXISTS `trade`;
CREATE TABLE `trade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `trade` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trade
-- ----------------------------
INSERT INTO `trade` VALUES ('27', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BRICK MASONS');
INSERT INTO `trade` VALUES ('28', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CARPENTERS');
INSERT INTO `trade` VALUES ('29', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'CONCRETE');
INSERT INTO `trade` VALUES ('30', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ELECTRICIANS');
INSERT INTO `trade` VALUES ('31', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ELEVATOR');
INSERT INTO `trade` VALUES ('32', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'EQUIPMENT OPERATORS');
INSERT INTO `trade` VALUES ('33', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'FIREPROOFERS');
INSERT INTO `trade` VALUES ('34', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'INSULATORS');
INSERT INTO `trade` VALUES ('35', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'IRON WORKERS');
INSERT INTO `trade` VALUES ('36', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'LABORERS');
INSERT INTO `trade` VALUES ('37', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PAINTERS/CAULKERS');
INSERT INTO `trade` VALUES ('38', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PILE DRIVERS');
INSERT INTO `trade` VALUES ('39', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PIPEFITTERS');
INSERT INTO `trade` VALUES ('40', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PLUMBERS');
INSERT INTO `trade` VALUES ('41', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'PRECAST CONCRETE');
INSERT INTO `trade` VALUES ('42', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'ROOFERS');
INSERT INTO `trade` VALUES ('43', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SHEET METAL WORKERS');
INSERT INTO `trade` VALUES ('44', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SITE UTILITIES');
INSERT INTO `trade` VALUES ('45', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SPRINKLER FITTER');
INSERT INTO `trade` VALUES ('46', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'SURVEYORS');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `role_id` int(11) NOT NULL,
  `user_name` varchar(20) DEFAULT NULL,
  `first_name` varchar(70) NOT NULL,
  `last_name` varchar(70) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(70) DEFAULT NULL,
  `division` varchar(70) DEFAULT NULL,
  `employee_number` varchar(70) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `contractor_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `us_ro_id` (`role_id`),
  KEY `us_co_id` (`contractor_id`),
  CONSTRAINT `us_co_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`id`),
  CONSTRAINT `us_ro_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '1', '2015-05-26 13:01:14', '2015-06-09 14:33:27', '1', 'admin', 'System', 'Admin', 'ignacio.milano@gmail.com', '123456789', '1', '99', '21232f297a57a5a743894a0e4a801fc3', '148');
INSERT INTO `user` VALUES ('2', '1', '2015-05-27 11:10:37', '2015-06-01 19:20:23', '11', 'foreman', 'Contractor', 'Foreman', 'contractorforeman@wt.com', '97564231', '2', '10', '21232f297a57a5a743894a0e4a801fc3', '148');
INSERT INTO `user` VALUES ('12', '1', '2015-06-09 16:11:13', '2015-06-09 16:11:13', '4', 'executive', 'First Name Executive', 'Last Name', 'executive@wt.com', '12345678', 'wt', '465', '21232f297a57a5a743894a0e4a801fc3', '148');
INSERT INTO `user` VALUES ('13', '1', '2015-06-09 16:13:28', '2015-06-09 16:13:28', '5', 'project', 'First Name Proyect Manager', 'Laste Name', 'proyect_manager@wt.com', '1234567', 'wt', '789', '21232f297a57a5a743894a0e4a801fc3', '148');
INSERT INTO `user` VALUES ('14', '1', '2015-06-09 16:15:00', '2015-06-09 16:15:00', '2', 'personnel', 'First Name Personnel', 'Last Name', 'personnel@wt.com', '1234567', 'wt', '6541', '21232f297a57a5a743894a0e4a801fc3', '148');
INSERT INTO `user` VALUES ('15', '1', '2015-06-09 16:18:12', '2015-06-09 16:18:12', '3', 'safety', 'First Name Safety', 'Last Name', 'wt', '12345678', 'wt', '6987', '21232f297a57a5a743894a0e4a801fc3', '148');
INSERT INTO `user` VALUES ('16', '1', '2015-06-09 16:22:10', '2015-06-09 16:22:10', '7', 'safetyc', 'Name Safety Contractor', 'Last Name', 'safety_contractor@allied.com', '1324567', 'allied', '1256', '21232f297a57a5a743894a0e4a801fc3', '48');
INSERT INTO `user` VALUES ('17', '1', '2015-06-09 16:24:29', '2015-06-09 16:24:29', '7', 'safetyc2', 'Other Safety Contractor', 'Last Name', 'other_safety_contractor@american.com', '1234567', 'american', '6352', '21232f297a57a5a743894a0e4a801fc3', '44');
INSERT INTO `user` VALUES ('18', '1', '2015-06-09 16:35:14', '2015-06-09 16:35:14', '8', 'client', 'Cliente Manager', 'Last Name', 'cliente_manager@client.com', '123456789', 'client', '7777', '21232f297a57a5a743894a0e4a801fc3', '149');

-- ----------------------------
-- Table structure for user_jobsite
-- ----------------------------
DROP TABLE IF EXISTS `user_jobsite`;
CREATE TABLE `user_jobsite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `jobsite_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `us_jo_us_id` (`user_id`),
  KEY `us_jo_jo_id` (`jobsite_id`),
  CONSTRAINT `us_jo_jo_id` FOREIGN KEY (`jobsite_id`) REFERENCES `jobsite` (`id`),
  CONSTRAINT `us_jo_us_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_jobsite
-- ----------------------------
INSERT INTO `user_jobsite` VALUES ('64', '9', '20');
INSERT INTO `user_jobsite` VALUES ('65', '9', '21');
INSERT INTO `user_jobsite` VALUES ('66', '9', '23');
INSERT INTO `user_jobsite` VALUES ('67', '9', '24');
INSERT INTO `user_jobsite` VALUES ('68', '10', '1');
INSERT INTO `user_jobsite` VALUES ('69', '10', '17');
INSERT INTO `user_jobsite` VALUES ('70', '10', '18');
INSERT INTO `user_jobsite` VALUES ('71', '10', '19');
INSERT INTO `user_jobsite` VALUES ('72', '11', '1');
INSERT INTO `user_jobsite` VALUES ('73', '11', '17');
INSERT INTO `user_jobsite` VALUES ('74', '11', '18');
INSERT INTO `user_jobsite` VALUES ('75', '11', '19');
INSERT INTO `user_jobsite` VALUES ('79', '1', '1');
INSERT INTO `user_jobsite` VALUES ('80', '1', '17');
INSERT INTO `user_jobsite` VALUES ('81', '1', '18');
INSERT INTO `user_jobsite` VALUES ('82', '12', '1');
INSERT INTO `user_jobsite` VALUES ('83', '12', '16');
INSERT INTO `user_jobsite` VALUES ('84', '13', '23');
INSERT INTO `user_jobsite` VALUES ('85', '14', '16');
INSERT INTO `user_jobsite` VALUES ('86', '15', '20');
INSERT INTO `user_jobsite` VALUES ('87', '16', '20');
INSERT INTO `user_jobsite` VALUES ('88', '17', '16');
INSERT INTO `user_jobsite` VALUES ('89', '17', '20');
INSERT INTO `user_jobsite` VALUES ('90', '18', '16');
INSERT INTO `user_jobsite` VALUES ('91', '18', '18');

ALTER TABLE content ADD uploader_id int(11) DEFAULT NULL;
ALTER TABLE content ADD FOREIGN KEY(uploader_id) REFERENCES user(id);
UPDATE content SET `uploader_id` = NULL
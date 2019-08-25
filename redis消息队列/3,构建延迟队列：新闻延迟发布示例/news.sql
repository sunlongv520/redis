/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-09-22 12:36:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `news`
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` varchar(50) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `news_pubtime` datetime DEFAULT NULL,
  `news_ispub` bit(1) DEFAULT b'0',
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('131', 'abc', '0', '2018-09-22 12:13:15', '');
INSERT INTO `news` VALUES ('132', 'bbb', '0', '2018-09-22 12:15:41', '');
INSERT INTO `news` VALUES ('133', 'ccc', '0', '2018-09-22 12:31:09', '');
INSERT INTO `news` VALUES ('134', 'ddd', '0', '2018-09-22 12:17:22', '');
INSERT INTO `news` VALUES ('135', 'hig', '0', '2018-09-22 12:20:49', '');

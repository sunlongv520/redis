/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-09-29 12:17:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `orders`
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) DEFAULT NULL,
  `order_state` tinyint(4) DEFAULT '1',
  `order_time` datetime DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of orders
-- ----------------------------
INSERT INTO `orders` VALUES ('1', '201809291', '2', '2018-09-29 03:54:15');
INSERT INTO `orders` VALUES ('2', '201809292', '2', '2018-09-29 03:55:00');
INSERT INTO `orders` VALUES ('3', '201809293', '2', '2018-09-29 03:55:00');
INSERT INTO `orders` VALUES ('4', '201809294', '2', '2018-09-29 03:55:01');
INSERT INTO `orders` VALUES ('5', '201809295', '3', '2018-09-29 03:55:01');
INSERT INTO `orders` VALUES ('6', '201809296', '4', '2018-09-29 03:55:01');
INSERT INTO `orders` VALUES ('7', '201809297', '2', '2018-09-29 03:55:02');
INSERT INTO `orders` VALUES ('8', '201809298', '2', '2018-09-29 03:55:02');
INSERT INTO `orders` VALUES ('9', '201809299', '2', '2018-09-29 03:55:02');

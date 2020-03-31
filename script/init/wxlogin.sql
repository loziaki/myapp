CREATE DATABASE IF NOT EXISTS `test_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `test_db`;

DROP TABLE IF EXISTS `wxuser`;
CREATE TABLE `wxuser` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `openId` varchar(255) DEFAULT NULL COMMENT '微信用户标识',
  `nickName` varchar(255) CHARACTER SET utf8mloginloginb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `avatarUrl` varchar(255) DEFAULT NULL COMMENT '头像',
  `gender` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别',
  `language` varchar(20) DEFAULT NULL COMMENT '语言',
  `province` varchar(10) DEFAULT NULL COMMENT '省份',
  `city` varchar(10) DEFAULT NULL COMMENT '城市',
  `country` varchar(20) DEFAULT NULL COMMENT '国家',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='微信用户表';

DROP TABLE IF EXISTS `token`;
CREATE TABLE `wxuser` (
  `uid` int(11) NOT NULL COMMENT '用户id',
  `openId` varchar(255) DEFAULT NULL COMMENT '微信用户标识',
  `session_key` varchar(100) DEFAULT NULL COMMENT '缓存key',
  `token` varchar(70) NOT NULL,
  `token_expire` int(11) NOT NULL COMMENT 'token过期时间戳',
  `last_login_ip` varchar(20) DEFAULT  NULL COMMENT '上次登录ip',
  `last_login_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '上次登录时间',
  PRIMARY KEY (`uid`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='登陆token';


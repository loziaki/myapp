CREATE DATABASE IF NOT EXISTS `test_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `test_db`;

DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `uid` int(11) NOT NULL COMMENT '用户id',
  `username` varchar(50) DEFAULT NULL COMMENT '用户名',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `token` varchar(100) NOT NULL,
  `token_expire` int(11) NOT NULL COMMENT 'token过期时间戳',
  `last_login_ip` varchar(20) DEFAULT  NULL COMMENT '上次登录ip',
  `last_login_time` datetime DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB COMMENT='管理后台的界面';

INSERT INTO `admin_user` (`uid`, `username`, `password`, `token`, `token_expire`)
VALUES ('1', 'admin', '48075c885836f0dda4bdb65886bdefdc', '2222333', '0'),('2', 'staff', '48075c885836f0dda4bdb65886bdefdc', '2222333', '0');

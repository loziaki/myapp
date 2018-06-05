CREATE DATABASE IF NOT EXISTS `test_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `test_db`;

DROP TABLE IF EXISTS `user_info`;
CREATE TABLE `user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(50) NOT NULL COMMENT '密码',
  `email` varchar(30) DEFAULT NULL COMMENT '用户邮箱',
  `state` int(11) UNSIGNED DEFAULT 1 COMMENT '0-禁用，1-初注册，2-邮件已确认，3-在用',
  `login_serial` int(11) DEFAULT NULL COMMENT '登录序列号',
  `login_ip` char(15) DEFAULT NULL COMMENT '登录IP地址(IPV4)',
  `login_time` datetime DEFAULT NULL COMMENT '登录时间',
  `create_time` datetime DEFAULT NULL COMMENT '登录时间',
  `description` varchar(50) DEFAULT NULL COMMENT '备注信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='用户信息表';

DROP TABLE IF EXISTS `operation_log`;
CREATE TABLE `operation_log` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `uid` INT(11) NULL COMMENT '用户id',
    `opr` varchar(50) NOT NULL COMMENT '操作描述',
    `note` varchar(50) NULL COMMENT '备注',
    `detailnote` TEXT NULL COMMENT '详细备注',
    `log_time` DATETIME NOT NULL COMMENT '上传时间',
    PRIMARY KEY (`id`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = '操作记录';

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户角色关联表id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `rid` int(11) NOT NULL COMMENT '角色id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='用户角色表';

INSERT INTO `user_info`(`id`,`username`,`password`,`email`,`state`,`description`) VALUES 
('1', 'admin', '293e7f4415a1e21e2eb1e021befc7dfe',null, 3, '管理员，密码123123'),
('2', 'test', 'c7ac1915232b82f8216e289c6164bf2d',null, 3, '外部用户，密码123456');

INSERT INTO `user_role`(`uid`,`rid`) VALUES 
('1', '1'),
('2', '2');

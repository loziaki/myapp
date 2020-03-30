CREATE DATABASE IF NOT EXISTS `test_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `test_db`;

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `rolename` varchar(20) NOT NULL COMMENT '角色名称',
  `description` varchar(50) DEFAULT NULL COMMENT '备注信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rolename` (`rolename`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='角色表';

DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `agid` int(11) NOT NULL COMMENT '权限组id',
  `name` varchar(20) NOT NULL COMMENT '权限名',
  `description` varchar(50) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限表';

DROP TABLE IF EXISTS `authgroup`;
CREATE TABLE `authgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限组表id',
  `groupname` varchar(20) NOT NULL COMMENT '组名',
  `description` varchar(20) DEFAULT NULL COMMENT '组描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限组表';

DROP TABLE IF EXISTS `role_agroup`;
CREATE TABLE `role_agroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关联表id',
  `rid` int(11) NOT NULL COMMENT '角色id',
  `agid` int(11) NOT NULL COMMENT '权限组id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色权限组关联表';

INSERT INTO `role` VALUES 
('1', 'admin', '管理员'),
('2', 'externald', '外部用户'),
('3', 'internal', '内部用户');

INSERT INTO `authgroup` VALUES 
('1', 'admin', '管理员权限组'),
('2', 'output', '导出权限组'),
('3', 'upload', '上传权限组'),
('4', 'modify', '修改权限组'),
('5', 'getinfo', '查看权限组'),
('6', 'general', '通用权限组');

INSERT INTO `role_agroup`(`rid`,`agid`) VALUES 
('1', '1'),
('1', '2'),
('1', '3'),
('1', '4'),
('1', '5'),
('2', '4'),
('2', '5'),
('3', '2'),
('3', '3'),
('3', '4'),
('3', '5');

# 管理员权限组


# 导出权限组

# 上传权限组

# 修改权限组

# 查看权限组

# 通用权限组

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `gl_admin_map`
-- ----------------------------
DROP TABLE IF EXISTS `gl_admin_map`;
CREATE TABLE `gl_admin_map` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL COMMENT '权限组id、用户id',
  `node_id` int(11) unsigned NOT NULL COMMENT '节点id',
  `type` varchar(20) NOT NULL COMMENT '分类，user自定义用户节点，role权限组节点',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gl_admin_map
-- ----------------------------
INSERT INTO `gl_admin_map` VALUES ('57', '3', '1', 'role');
INSERT INTO `gl_admin_map` VALUES ('58', '3', '2', 'role');
INSERT INTO `gl_admin_map` VALUES ('59', '3', '5', 'role');
INSERT INTO `gl_admin_map` VALUES ('60', '3', '6', 'role');
INSERT INTO `gl_admin_map` VALUES ('61', '3', '8', 'role');

-- ----------------------------
-- Table structure for `gl_admin_node`
-- ----------------------------
DROP TABLE IF EXISTS `gl_admin_node`;
CREATE TABLE `gl_admin_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(20) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型，0导航，1子导航，2子导航页面按钮，3子导航数据列表操作',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `auth` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1为所有人可以访问，0为依赖权限组访问',
  `sort` mediumint(3) unsigned NOT NULL DEFAULT '0',
  `pid` int(20) unsigned NOT NULL COMMENT '导航为0，子导航时为父导航id',
  `extra` varchar(50) NOT NULL DEFAULT '' COMMENT '额外属性',
  `icon` varchar(20) NOT NULL DEFAULT '' COMMENT '图标',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of gl_admin_node
-- ----------------------------
INSERT INTO `gl_admin_node` VALUES ('1', '', '系统管理', '0', '1', '0', '20', '0', '', '');
INSERT INTO `gl_admin_node` VALUES ('2', 'node/index', '节点列表', '1', '1', '0', '50', '1', '', '');
INSERT INTO `gl_admin_node` VALUES ('3', 'node/add', '新增模块', '2', '1', '0', '1', '2', '', '');
INSERT INTO `gl_admin_node` VALUES ('4', 'node/addNav', '添加页面', '3', '1', '0', '4', '2', '', '');
INSERT INTO `gl_admin_node` VALUES ('5', 'node/addBtn', '添加按钮', '3', '1', '0', '3', '2', '', '');
INSERT INTO `gl_admin_node` VALUES ('6', 'node/addAct', '添加操作', '3', '1', '0', '3', '2', '', '');
INSERT INTO `gl_admin_node` VALUES ('7', 'node/delete', '删除', '3', '1', '0', '1', '2', 'layui-btn-danger', '');
INSERT INTO `gl_admin_node` VALUES ('8', 'node/edit', '编辑', '3', '1', '0', '3', '2', '', '');
INSERT INTO `gl_admin_node` VALUES ('9', 'admin/index', '首页', '0', '1', '0', '30', '0', '', 'icon-chengzi');
INSERT INTO `gl_admin_node` VALUES ('19', 'role/index', '权限组列表', '1', '1', '0', '50', '1', '', '');
INSERT INTO `gl_admin_node` VALUES ('20', 'role/add', '新增权限组', '2', '1', '0', '50', '19', '', '');
INSERT INTO `gl_admin_node` VALUES ('21', 'role/edit', '编辑', '3', '1', '0', '50', '19', '', '');
INSERT INTO `gl_admin_node` VALUES ('22', 'role/delete', '删除', '3', '1', '0', '30', '19', 'layui-btn-danger', '');
INSERT INTO `gl_admin_node` VALUES ('23', 'role/setMap', '设置权限', '3', '1', '0', '60', '19', '', '');
INSERT INTO `gl_admin_node` VALUES ('24', 'users/admin', '管理员', '1', '1', '0', '50', '1', '', '');
INSERT INTO `gl_admin_node` VALUES ('25', 'users/addAdmin', '新增管理员', '2', '1', '0', '50', '24', '', 'icon-chengzi');
INSERT INTO `gl_admin_node` VALUES ('26', 'users/removeAdmin', '移除', '3', '1', '0', '30', '24', 'layui-btn-danger', 'icon-chengzi');
INSERT INTO `gl_admin_node` VALUES ('27', 'users/setRole', '设置权限组', '3', '1', '0', '50', '24', '', '');

-- ----------------------------
-- Table structure for `gl_admin_role`
-- ----------------------------
DROP TABLE IF EXISTS `gl_admin_role`;
CREATE TABLE `gl_admin_role` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gl_admin_role
-- ----------------------------
INSERT INTO `gl_admin_role` VALUES ('1', '超级管理员', '允许访问所有节点');
INSERT INTO `gl_admin_role` VALUES ('3', '管理员', '12');

-- ----------------------------
-- Table structure for `gl_users`
-- ----------------------------
DROP TABLE IF EXISTS `gl_users`;
CREATE TABLE `gl_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `name` varchar(10) NOT NULL COMMENT '用户名',
  `nickname` varchar(50) NOT NULL COMMENT '用户昵称',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限组id',
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '管理员标识',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 保密 1 男 2 女',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `phone_validated` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否验证手机',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮件',
  `email_validated` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否验证电子邮箱',
  `last_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `status` tinyint(2) unsigned NOT NULL COMMENT '状态，1启用，0禁用，-1删除',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `mobile` (`phone_validated`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gl_users
-- ----------------------------
INSERT INTO `gl_users` VALUES ('1', 'admin', '', '1c3653f708f434ea17cd2a9c22521c04', 'https://mysali.oss-cn-hangzhou.aliyuncs.com/Uploads/Picture/2017-11-23/5a167ace88798.png', '1', '1', '0', '13819398442', '0', '', '0', '127.0.0.1', '1524626236', '0', '1');

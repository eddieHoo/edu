DROP TABLE IF EXISTS `oa_admin`;
CREATE TABLE `oa_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) NOT NULL DEFAULT '' COMMENT '企业微信userid',
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '登录用户名',
  `pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '登录密码',
  `salt` varchar(100) NOT NULL DEFAULT '' COMMENT '密码盐',
  `reg_pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '初始密码',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '人员姓名',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` bigint(11) NOT NULL DEFAULT 0 COMMENT '手机号码',
  `sex` int(1) NOT NULL DEFAULT 0 COMMENT '性别:1男,2女',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '别名',
  `thumb` varchar(255) NOT NULL COMMENT '头像',
  `theme` varchar(50) NOT NULL DEFAULT 'white' COMMENT '系统主题',
  `did` int(11) NOT NULL DEFAULT 0 COMMENT '主部门id',
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '上级主管id',
  `role_ids` varchar(100) NOT NULL DEFAULT '' COMMENT '角色ids',
  `position_id` int(11) NOT NULL DEFAULT 0 COMMENT '职位id',
  `position_name` int(11) NOT NULL DEFAULT 0 COMMENT '职务',
  `position_rank` int(11) NOT NULL DEFAULT 0 COMMENT '职级',
  `type` int(1) NOT NULL DEFAULT 0 COMMENT '人员类型:0未设置,1正式,2试用,3实习',
  `is_staff` int(1) NOT NULL DEFAULT 1 COMMENT '身份类型:1企业人员,2劳务派遣,3兼职人员',
  `job_number` varchar(255) NOT NULL DEFAULT '' COMMENT '工号',
  `birthday` varchar(255) NOT NULL DEFAULT '' COMMENT '生日',
  `age` int(11) NOT NULL DEFAULT 0 COMMENT '年龄',
  `work_date` varchar(255) NOT NULL DEFAULT '' COMMENT '开始工作时间',
  `work_location` int(11) NOT NULL DEFAULT 0 COMMENT '工作地点',
  `native_place` varchar(255) NOT NULL DEFAULT '' COMMENT '籍贯',
  `nation` varchar(255) NOT NULL DEFAULT '' COMMENT '民族',
  `home_address` varchar(255) NOT NULL DEFAULT '' COMMENT '家庭地址',
  `current_address` varchar(255) NOT NULL DEFAULT '' COMMENT '现居地址',
  `contact` varchar(255) NOT NULL DEFAULT '' COMMENT '紧急联系人',
  `contact_mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '紧急联系人电话',
  `resident_type` int(1) NOT NULL DEFAULT 0 COMMENT '户口性质:1农村户口,2城镇户口',
  `resident_place` varchar(255) NOT NULL DEFAULT '' COMMENT '户口所在地',
  `graduate_school` varchar(255) NOT NULL DEFAULT '' COMMENT '毕业学校',
  `graduate_day` varchar(255) NOT NULL DEFAULT '' COMMENT '毕业日期',
  `political` int(1) NOT NULL DEFAULT 1 COMMENT '政治面貌:1中共党员,2团员',
  `marital_status` int(1) NOT NULL DEFAULT 1 COMMENT '婚姻状况:1未婚,2已婚,3离异',
  `idcard` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证',  
  `education` varchar(255) NOT NULL DEFAULT '' COMMENT '学位',
  `speciality` varchar(255) NOT NULL DEFAULT '' COMMENT '专业',
  `social_account` varchar(255) NOT NULL DEFAULT '' COMMENT '社保账号',
  `medical_account` varchar(255) NOT NULL DEFAULT '' COMMENT '医保账号',
  `provident_account` varchar(255) NOT NULL DEFAULT '' COMMENT '公积金账号',
  `bank_account` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `bank_info` varchar(255) NOT NULL DEFAULT '' COMMENT '开户行',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '档案附件',
  `desc` mediumtext  NULL COMMENT '人员个人简介',
  `is_hide` int(1) NOT NULL DEFAULT 0 COMMENT '是否隐藏联系方式:0否,1是',
  `integral` int(11) NOT NULL DEFAULT 0 COMMENT '学分',
  `credit` int(11) NOT NULL DEFAULT 0 COMMENT '积分',
  `entry_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '人员入职日期',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '注册时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新信息时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `last_login_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `login_num` int(11) NOT NULL DEFAULT 0 COMMENT '登录次数',
  `last_login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `is_lock` int(1) NOT NULL DEFAULT 0 COMMENT '是否锁屏:1是0否',
  `auth_dids` varchar(500) NOT NULL DEFAULT '' COMMENT '可见部门数据',
  `son_dids` varchar(500) NOT NULL DEFAULT '' COMMENT '可见子部门数据',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态：-1待入职,0禁止登录,1正常,2离职',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人员表';

-- ----------------------------
-- Table structure for oa_admin_profiles
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_profiles`;
CREATE TABLE `oa_admin_profiles`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '人员ID',
  `types` int(1) NOT NULL DEFAULT 0 COMMENT '类型:1教育经历/2工作经历/3相关证书/4计算机技能/5语言能力',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '院校/培训机构/公司名称/证书名称/技能名称/语言名称/',
  `start_time` varchar(255) NOT NULL DEFAULT '' COMMENT '开始时间',
  `end_time` varchar(255) NOT NULL DEFAULT '' COMMENT '结束时间',
  `speciality` varchar(50) NOT NULL DEFAULT '' COMMENT '所学专业',
  `education` varchar(50) NOT NULL DEFAULT '' COMMENT '所获学历',
  `authority` varchar(50) NOT NULL DEFAULT '' COMMENT '颁发机构',
  `position` varchar(50) NOT NULL DEFAULT '' COMMENT '职位',
  `know` int(11) NOT NULL DEFAULT 0 COMMENT '熟悉程度',
  `remark` mediumtext  NULL COMMENT '备注说明',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人员档案表';

-- ----------------------------
-- Table structure for oa_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_log`;
CREATE TABLE `oa_admin_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` bigint(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  `type` varchar(80) NOT NULL DEFAULT '' COMMENT '操作类型',
  `action` varchar(80) NOT NULL DEFAULT '' COMMENT '操作动作',
  `subject` varchar(80) NOT NULL DEFAULT '' COMMENT '操作主体',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `function` varchar(32) NOT NULL DEFAULT '' COMMENT '方法',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT '登录ip',
  `param_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '操作数据id',
  `param` mediumtext  NULL COMMENT '参数json格式',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人员操作日志表';

-- ----------------------------
-- Table structure for oa_admin_module
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_module`;
CREATE TABLE `oa_admin_module`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '模块名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '模块目录，唯一，字母',
  `desc` varchar(500) NOT NULL DEFAULT '' COMMENT '模块功能描述',
  `type` int(2) NOT NULL DEFAULT 1 COMMENT '状态:1系统模块,2普通模块,3自定义模块',
  `sourse` int(2) NOT NULL DEFAULT 1 COMMENT '来源:1官方,2第三方',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:0禁用,1正常',
  `conf_1` mediumtext  NULL COMMENT '配置字段1，可作为预配置内容',
  `conf_2` mediumtext  NULL COMMENT '配置字段2，可作为预配置内容',
  `conf_3` mediumtext  NULL COMMENT '配置字段3，可作为预配置内容',
  `conf_4` mediumtext  NULL COMMENT '配置字段4，可作为预配置内容',
  `conf_5` mediumtext  NULL COMMENT '配置字段5，可作为预配置内容',
  `conf_6` mediumtext  NULL COMMENT '配置字段6，可作为预配置内容',
  `conf_7` mediumtext  NULL COMMENT '配置字段7，可作为预配置内容',
  `conf_8` mediumtext  NULL COMMENT '配置字段8，可作为预配置内容',
  `conf_9` mediumtext  NULL COMMENT '配置字段9，可作为预配置内容',
  `conf_10` mediumtext  NULL COMMENT '配置字段9，可作为预配置内容',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '功能模块及数据权限配置表';

-- ----------------------------
-- Records of oa_admin_module
-- ----------------------------
INSERT INTO `oa_admin_module` VALUES (1, '系统管理模块', 'system','', 1, 1,1, '', '', '', '', '', '', '', '', '', '', 1656143065, 1724830718);
INSERT INTO `oa_admin_module` VALUES (2, '人事行政模块', 'adm','', 1, 1,1, '', '', '', '', '', '', '', '', '', '', 1656143065, 1724830718);
INSERT INTO `oa_admin_module` VALUES (3, '线上课程模块', 'course','', 2, 1,1, '', '', '', '', '', '', '', '', '', '', 1656143065, 1724830718);
INSERT INTO `oa_admin_module` VALUES (4, '面授培训模块', 'train','', 2, 1,1, '', '', '', '', '', '', '', '', '', '', 1656143065, 1724830718);
INSERT INTO `oa_admin_module` VALUES (5, '在线考试模块', 'exam','', 2, 1,1, '', '', '', '', '', '', '', '', '', '', 1656143065, 1724830718);
INSERT INTO `oa_admin_module` VALUES (6, '荣誉证书模块', 'honor','', 2, 1,1, '', '', '', '', '', '', '', '', '', '', 1656143065, 1724830718);
INSERT INTO `oa_admin_module` VALUES (7, '知识网盘模块', 'disk','', 2, 1,1, '', '', '', '', '', '', '', '', '', '', 1656143065, 1724830718);

-- ----------------------------
-- Table structure for oa_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_rule`;
CREATE TABLE `oa_admin_rule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` bigint(11) NOT NULL DEFAULT 0 COMMENT '父id',
  `src` varchar(255) NOT NULL DEFAULT '' COMMENT 'url链接',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '日志操作名称',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '所属模块',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `menu` int(1) NOT NULL DEFAULT 0 COMMENT '是否是菜单,1是,2不是',
  `sort` int(11) NOT NULL DEFAULT 1 COMMENT '越小越靠前',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态,0禁用,1正常',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '菜单及权限表';

-- ----------------------------
-- Records of oa_admin_rule
-- ----------------------------
INSERT INTO `oa_admin_rule` VALUES (1, 0, '', '系统管理', '系统管理', 'system', 'icon-jichupeizhi', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (2, 0, '', '人事行政', '人事行政', 'adm', 'icon-renshishezhi', 1, 2, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (3, 0, '', '线上课程', '线上课程', 'course', 'icon-banjiguanli', 1, 3, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (4, 0, '', '线下培训', '线下培训', 'train', 'icon-kaoshijihua', 1, 4, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (5, 0, '', '试卷管理', '试卷管理', 'exam', 'icon-yuangongtidian', 1, 5, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (6, 0, '', '荣誉证书', '荣誉证书', 'honor', 'icon-kehuguanli', 1, 6, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (7, 0, '', '学习中心', '学习中心', 'learn', 'icon-xiangmuguanli', 1, 7, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (8, 0, '', '知识网盘', '知识网盘', 'disk', 'icon-xiangmuguanli', 1, 8, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (9, 1, 'system/conf/datalist', '系统配置', '系统配置', 'system', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (10, 9, 'system/conf/add', '新建/编辑', '配置项', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (11, 9, 'system/conf/del', '删除', '配置项', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (12, 9, 'system/conf/edit', '编辑', '配置详情', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (13, 1, 'system/module/datalist', '功能模块', '功能模块', 'system', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (14, 13, 'system/module/add', '新建/编辑', '功能模块', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (15, 13, 'system/module/del', '删除', '功能模块', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (16, 13, 'system/module/auth', '模块数据权限配置', '数据权限配置', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (17, 1, 'system/rule/datalist', '功能节点', '功能节点', 'system', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (18, 17, 'system/rule/add', '新建/编辑', '功能节点', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (19, 17, 'system/rule/del', '删除', '功能节点', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (20, 1, 'system/role/datalist', '角色权限', '角色权限', 'system', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (21, 20, 'system/role/add', '新建/编辑', '角色权限', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (22, 20, 'system/role/del', '删除', '角色权限', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (23, 1, 'system/basic/index', '基础数据', '基础数据', 'system', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (24, 23, 'system/position/datalist', '岗位职称', '岗位职称', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (25, 24, 'system/position/add', '新建/编辑', '岗位职称', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (26, 24, 'system/position/set', '设置', '岗位职称', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (27, 23, 'system/rank/datalist', '职务等级', '职务等级', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (28, 27, 'system/rank/add', '新建/编辑', '职务等级', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (29, 27, 'system/rank/set', '设置', '职务等级', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (30, 23, 'system/notecate/datalist', '公告类型', '公告类型', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (31, 30, 'system/notecate/add', '新建/编辑', '公告类型', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (32, 30, 'system/notecate/set', '设置', '公告类型', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (33, 1, 'system/template/datalist', '消息模板', '消息模板', 'system', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (34, 33, 'system/template/add', '新建/编辑', '消息模板', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (35, 33, 'system/template/set', '设置', '消息模板', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (36, 33, 'system/template/view', '查看', '消息模板', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (37, 1, 'system/files/datalist', '附件管理','附件管理', 'system', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (38, 37, 'system/files/edit', '编辑附件','附件', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (39, 37, 'system/files/move', '移动附件','附件', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (40, 37, 'system/files/del', '删除附件','附件', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (41, 37, 'system/files/get_group', '附件分组','附件分组', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (42, 37, 'system/files/add_group', '新建/编辑','附件分组', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (43, 37, 'system/files/del_group', '删除附件分组','附件分组', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (44, 1, 'system/task/datalist', '定时任务', '定时任务', 'system', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (45, 44, 'system/task/add', '新建/编辑', '定时任务', 'system', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (46, 44, 'system/task/delete', '删除', '定时任务', 'system', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (47, 1, 'system/log/datalist', '操作日志', '操作日志', 'system', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (48, 2, 'adm/department/datalist', '部门架构', '部门', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (49, 48, 'adm/department/add', '新建/编辑', '部门', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (50, 48, 'adm/department/del', '删除', '部门', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (51, 2, 'adm/user/datalist', '人员管理', '人员', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (52, 51, 'adm/user/add', '新建/编辑', '人员', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (53, 51, 'adm/user/view', '查看', '人员信息', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (54, 51, 'adm/user/set', '设置', '人员状态', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (55, 51, 'adm/user/resetpsw', '重设密码', '人员密码', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (56, 51, 'adm/user/del', '删除', '人员', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (57, 2, 'adm/files/datalist', '人员档案', '人员档案', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (58, 57, 'adm/files/add', '编辑', '人员档案', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (59, 57, 'adm/files/view', '查看', '人员档案', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (60, 57, 'adm/files/set', '设置', '人员档案', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (61, 2, 'adm/note/datalist', '公告管理', '公告', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (62, 61, 'adm/note/add', '新建/编辑', '公告', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (63, 61, 'adm/note/del', '删除', '公告', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (64, 61, 'adm/note/view', '查看', '公告', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (65, 2, 'adm/news/datalist', '新闻管理', '新闻', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (66, 65, 'adm/news/add', '新建/编辑', '新闻', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (67, 65, 'adm/news/del', '删除', '新闻', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (68, 65, 'adm/news/view', '查看', '新闻', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (69, 3, 'course/lecturer/datalist', '讲师管理', '讲师', 'train', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (70, 69, 'course/lecturer/add', '新建/编辑', '讲师', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (71, 69, 'course/lecturer/check', '设置', '讲师', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (72, 69, 'course/lecturer/view', '查看', '讲师', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (73, 69, 'course/lecturer/del', '删除', '讲师', 'train', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (74, 3, 'course/cate/datalist', '课程分类', '课程分类', 'train', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (75, 74, 'course/cate/add', '新建/编辑', '课程分类', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (76, 74, 'course/cate/set', '设置', '课程分类', 'train', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (77, 3, 'course/material/datalist', '课程内容', '课程内容', 'course', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (78, 77, 'course/material/add', '新建/编辑', '课程内容', 'course', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (79, 77, 'course/material/del', '删除', '课程内容', 'course', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (80, 77, 'course/material/view', '预览', '课程内容', 'course', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (81, 3, 'course/course/datalist', '课程列表', '课程', 'train', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (82, 81, 'course/course/add', '新建/编辑', '课程', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (83, 81, 'course/course/check', '设置', '课程', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (84, 81, 'course/course/view', '查看', '课程', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (85, 81, 'course/course/del', '删除', '课程', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (86, 81, 'course/course/section', '章节', '课程', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (87, 81, 'course/course/record', '学习记录', '学习记录', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (88, 81, 'course/course/record_del', '删除', '学习记录', 'train', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (89, 81, 'course/course/appraise', '课程评价', '课程评价', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (90, 81, 'course/course/appraise_del', '删除', '课程评价', 'train', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (91, 4, 'train/cate/datalist', '培训分类', '培训分类', 'train', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (92, 91, 'train/cate/add', '新建/编辑', '培训分类', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (93, 91, 'train/cate/set', '设置', '培训分类', 'train', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (94, 4, 'train/train/datalist', '线下培训', '培训', 'train', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (95, 94, 'train/train/add', '新建/编辑', '培训', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (96, 94, 'train/train/view', '查看', '培训', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (97, 94, 'train/train/del', '删除', '培训', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (98, 94, 'train/train/appraise', '评价学员', '评价学员', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (99, 94, 'train/train/appraise_view', '查看评价', '评价学员', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (100, 94, 'train/train/appraise_add', '添加评价', '评价学员', 'train', '', 2, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (101, 5, 'exam/question/cate', '题库分类', '题库分类', 'exam', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (102, 101, 'exam/question/cate_add', '新建/编辑', '题库分类', 'exam', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (103, 101, 'exam/question/cate_set', '设置', '题库分类', 'exam', '', 2, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (104, 5, 'exam/question/datalist', '题库管理', '考题', 'exam', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (105, 104, 'exam/question/add', '新建/编辑', '考题', 'exam', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (106, 104, 'exam/question/view', '查看', '考题', 'exam', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (107, 104, 'exam/question/del', '删除', '考题', 'exam', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (108, 104, 'exam/question/set', '设置', '考题', 'exam', '', 2, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (109, 5, 'exam/cate/datalist', '试卷分类', '试卷分类', 'exam', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (110, 109, 'exam/cate/add', '新建/编辑', '试卷分类', 'exam', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (111, 109, 'exam/cate/set', '设置', '试卷分类', 'exam', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (112, 5, 'exam/paper/datalist', '试卷管理', '试卷', 'exam', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (113, 112, 'exam/paper/add', '新建/编辑', '试卷', 'exam', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (114, 112, 'exam/paper/view', '查看', '试卷', 'exam', '', 2,0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (115, 112, 'exam/paper/del', '删除', '试卷', 'exam', '', 2,0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (116, 112, 'exam/paper/check', '设置', '试卷', 'exam', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (117, 112, 'exam/paper/question', '组卷', '试卷', 'exam', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (118, 112, 'exam/paper/preview', '预览', '试卷', 'exam', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (119, 5, 'exam/record/datalist', '考试记录', '考试记录', 'exam', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (120, 119, 'exam/record/view', '查看', '考试记录', 'exam', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (121, 119, 'exam/record/set', '审阅', '考试记录', 'exam', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (122, 119, 'exam/record/del', '删除', '考试记录', 'exam', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (123, 6, 'honor/statistics/index', '学习统计', '统计', 'honor', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (124, 6, 'honor/statistics/ranking', '学员排名', '排名', 'honor', '', 1, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (125, 6, 'honor/certificate/datalist', '证书管理', '证书', 'honor', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (126, 125, 'honor/certificate/add', '新建/编辑', '证书', 'honor', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (127, 125, 'honor/certificate/check', '设置', '证书', 'honor', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (128, 125, 'honor/certificate/delete', '删除', '证书', 'honor', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (129, 125, 'honor/certificate/users', '授予', '证书', 'honor', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (130, 125, 'honor/certificate/check_user', '设置授予', '证书', 'honor', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (131, 6, 'honor/certificate/honorlist', '学员证书', '证书', 'honor', '', 1, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (132, 7, 'course/learn/datalist', '课程学习', '课程', 'course', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (133, 132, 'course/learn/view', '查看', '课程', 'course', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (134, 132, 'course/learn/learn', '学习', '课程', 'course', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (135, 132, 'course/learn/appraise_add', '评价', '课程', 'course', '', 2, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (136, 7, 'train/learn/datalist', '培训学习', '培训', 'train', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (137, 136, 'train/learn/view', '学习', '培训', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (138, 136, 'train/learn/appraise', '评价讲师', '评价讲师', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (139, 136, 'train/learn/appraise_add', '添加评价', '评价讲师', 'train', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (140, 136, 'train/learn/appraise_view', '查看评价', '评价讲师', 'train', '', 2, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (141, 7, 'exam/exam/datalist', '在线考试', '试卷', 'exam', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (142, 141, 'exam/exam/exam', '考试', '试卷', 'exam', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (143, 141, 'exam/exam/view', '考试结果', '试卷', 'exam', '', 2, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (144, 7, 'honor/certificate/mylist', '我的证书', '证书', 'honor', '', 1, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (145, 8, 'disk/index/datalist', '个人空间', '个人空间', 'disk', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (146, 145, 'disk/index/add_upload', '新增', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (147, 145, 'disk/index/add_folder', '新增', '文件夹', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (148, 145, 'disk/index/add_article', '新增/编辑', '在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (149, 145, 'disk/index/view_article', '查看', '在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (150, 145, 'disk/index/del', '删除', '文件/文件夹/在线文档', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (151, 145, 'disk/index/rename', '重命名', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (152, 145, 'disk/index/move', '移动', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (153, 145, 'disk/index/star', '标星', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (154, 145, 'disk/index/unstar', '取消标星', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (155, 145, 'disk/index/back', '还原', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (156, 145, 'disk/index/clear', '清除', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (157, 145, 'disk/index/share', '分享', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (158, 145, 'disk/index/unshare', '取消分享', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (159, 145, 'disk/index/starlist', '标星文件', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (160, 145, 'disk/index/mysharelist', '我分享的文件', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (161, 145, 'disk/index/tosharelist', '别人分享的文件', '文件', 'disk', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (162, 8, 'disk/index/sharelist', '共享空间', '共享空间', 'disk', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (163, 162, 'disk/index/add_group', '新建/编辑','共享空间', 'disk', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (164, 162, 'disk/index/del_group', '删除','共享空间', 'disk', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (165, 8, 'disk/index/clearlist', '回 收 站', '回收站文件', 'disk', '', 1, 0, 1, 1656143065, 0);

-- ----------------------------
-- Table structure for oa_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_group`;
CREATE TABLE `oa_admin_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT 1,
  `rules` mediumtext  NULL COMMENT '用户组拥有的规则id',
  `layouts` mediumtext  NULL COMMENT '首页展示模块',
  `mobile_bar` mediumtext  NULL COMMENT '手机端Bar',
  `mobile_menu` mediumtext  NULL COMMENT '手机端工作台',
  `desc` mediumtext  NULL COMMENT '备注',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人员权限分组表';

-- ----------------------------
-- Records of oa_admin_group
-- ----------------------------
INSERT INTO `oa_admin_group` VALUES (1, '超级权限角色', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168', '1,2,3,4,5,6,7,8,9,10,11,12','1,2,3,4,5','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16','超级权限角色，拥有系统的最高权限，主要用于系统初始化数据而设，不可修改，不可删除。', 0, 0);
INSERT INTO `oa_admin_group` VALUES (2, '管理岗角色', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166','1,2,3,4,5,6,7,8,9,10,11,12','1,2,3,4,5','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16', '管理岗角色权限，可根据公司的具体需求调整。', 0, 0);
INSERT INTO `oa_admin_group` VALUES (3, '业务岗角色', 1, '4,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16','1,2,3,4,5','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16', '业务岗角色权限，可根据公司的具体需求调整。', 0, 0);

-- ----------------------------
-- Table structure for oa_config
-- ----------------------------
DROP TABLE IF EXISTS `oa_config`;
CREATE TABLE `oa_config`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置标识',
  `content` mediumtext  NULL COMMENT '配置内容',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '系统配置表';

-- ----------------------------
-- Records of oa_config
-- ----------------------------
INSERT INTO `oa_config`VALUES (1, '信息配置', 'web', 'a:10:{s:2:"id";s:1:"1";s:11:"admin_title";s:9:"勾股Edu";s:6:"domain";s:24:"https://www.gougucms.com";s:3:"icp";s:21:"粤ICP备xxxxxxx号-1";s:4:"logo";s:31:"/static/home/images/syslogo.png";s:4:"file";s:0:"";s:10:"small_logo";s:37:"/static/home/images/syslogo_small.png";s:5:"beian";s:27:"粤公网安备xxxxxxx号-1";s:8:"keywords";s:9:"勾股Edu";s:4:"desc";s:537:"勾股Edu是由勾股技术团队打造的一套业内领先的线上学习、培训、考试一体化解决方案。勾股Edu基于ThinkPHP + Layui + MySql开发，系统集成了系统管理、人事行政、线上课程、面授培训、学习中心、在线考试、荣誉证书、知识网盘八大功能模块。系统操作简约，开箱即用，可快捷方便创建学习任务、培训任务、试题和题库，发布试卷，组织考试，自动阅卷等，实现企业内部人员学习、培训、考试一体化管理。";}', 1, 1612514630, 1724823769);
INSERT INTO `oa_config`VALUES (2, '系统配置', 'system', 'a:8:{s:9:"menu_mode";s:9:"classical";s:19:"upload_max_filesize";s:2:"50";s:9:"msg_sound";s:1:"1";s:9:"watermark";s:1:"1";s:6:"qrcode";s:1:"2";s:7:"version";s:5:"1.0.2";s:9:"copyright";s:25:"© 2023-2025 gougucms.com";s:2:"id";s:1:"2";}', 1, 1612514630, 1724824879);
INSERT INTO `oa_config`VALUES (3, '邮箱配置', 'email', 'a:8:{s:2:"id";s:1:"3";s:4:"smtp";s:11:"smtp.qq.com";s:9:"smtp_port";s:3:"465";s:9:"smtp_user";s:15:"gougucms@qq.com";s:8:"smtp_pwd";s:6:"123456";s:4:"from";s:24:"勾股CMS系统管理员";s:5:"email";s:18:"admin@gougucms.com";s:8:"template";s:544:"<p>勾股Edu是由勾股技术团队打造的一套业内领先的线上学习、培训、考试一体化解决方案。勾股Edu基于ThinkPHP + Layui + MySql开发，系统集成了系统管理、人事行政、线上课程、面授培训、在线考试、学习中心、荣誉证书、知识网盘八大功能模块。系统操作简约，开箱即用，可快捷方便创建学习任务、培训任务、试题和题库，发布试卷，组织考试，自动阅卷等，实现企业内部人员学习、培训、考试一体化管理。</p>";}', 1, 1612521657, 1637075205);
INSERT INTO `oa_config`VALUES (4, 'Api Token配置', 'token', 'a:5:{s:2:\"id\";s:1:\"3\";s:3:\"iss\";s:15:\"oa.gougucms.com\";s:3:\"aud\";s:7:\"gouguoa\";s:7:\"secrect\";s:7:\"GOUGUOA\";s:7:\"exptime\";s:4:\"3600\";}', 1, 1627313142, 1638010233);
INSERT INTO `oa_config`VALUES (5, '其他配置', 'other', 'a:3:{s:2:\"id\";s:1:\"5\";s:6:\"author\";s:15:\"勾股工作室\";s:7:\"version\";s:13:\"v1.2024.08.28\";}', 1, 1613725791, 1724824410);

-- ----------------------------
-- Table structure for oa_basic_user
-- ----------------------------
DROP TABLE IF EXISTS `oa_basic_user`;
CREATE TABLE `oa_basic_user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `types` varchar(100) NOT NULL DEFAULT '' COMMENT '数据类型:1职务,2职级,3看后期增加',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人事模块常规数据';

-- ----------------------------
-- Records of oa_basic_user
-- ----------------------------
INSERT INTO `oa_basic_user` VALUES (1, '2', '初级', 1, 1733384661, 0);
INSERT INTO `oa_basic_user` VALUES (2, '2', '中级', 1, 1733384671, 0);
INSERT INTO `oa_basic_user` VALUES (3, '2', '高级', 1, 1733384675, 0);
INSERT INTO `oa_basic_user` VALUES (4, '1', '总经理', 1, 1733384651, 0);
INSERT INTO `oa_basic_user` VALUES (5, '1', '工程师', 1, 1733384651, 0);

-- ----------------------------
-- Table structure for oa_timing_task
-- ----------------------------
DROP TABLE IF EXISTS `oa_timing_task`;
CREATE TABLE `oa_timing_task`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '任务名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '任务标识，唯一',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '执行链接',
  `types` int(2) NOT NULL DEFAULT 3 COMMENT '状态:1系统任务,2普通任务,3自定义任务',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1进行中,2暂停,3已完成',
  `plan_types` tinyint(1) NOT NULL DEFAULT 0 COMMENT '执行周期：0每天,1每N天,2每周,3每月,4每N时,5每N分钟,6每N秒',
  `plan_days` int(3) NOT NULL DEFAULT 0 COMMENT 'N日',
  `plan_week` int(3) NOT NULL DEFAULT 0 COMMENT '周几',
  `plan_day` int(3) NOT NULL DEFAULT 0 COMMENT '日',
  `plan_hour` int(3) NOT NULL DEFAULT 0 COMMENT '小时',
  `plan_min` int(3) NOT NULL DEFAULT 0 COMMENT '分钟',
  `plan_sec` int(3) NOT NULL DEFAULT 0 COMMENT '分钟',
  `start_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '开始执行时间',
  `next_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '下次执行时间',
  `desc` varchar(500) NOT NULL DEFAULT '' COMMENT '任务描述',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '定时任务表';

-- ----------------------------
-- Table structure for oa_department
-- ----------------------------
DROP TABLE IF EXISTS `oa_department`;
CREATE TABLE `oa_department`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '部门名称',
  `pid` bigint(11) NOT NULL DEFAULT 0 COMMENT '上级部门id',
  `leader_ids` varchar(500) NULL DEFAULT '' COMMENT '部门负责人ids',
  `phone` varchar(60) NOT NULL DEFAULT '' COMMENT '部门联系电话',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '部门组织';

-- ----------------------------
-- Records of oa_department
-- ----------------------------
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (1, '董事会', 0);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (2, '人事部', 1);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (3, '财务部', 1);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (4, '市场部', 1);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (5, '销售部', 1);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (6, '技术部', 1);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (7, '客服部', 1);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (8, '销售一部', 5);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (9, '销售二部', 5);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (10, '销售三部', 5);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (11, '产品部', 6);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (12, '设计部', 6);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (13, '研发部', 6);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (14, '客服一部', 7);
INSERT INTO `oa_department`(`id`, `title`, `pid`) VALUES (15, '客服二部', 7);

-- ----------------------------
-- Table structure for oa_department_admin
-- ----------------------------
DROP TABLE IF EXISTS `oa_department_admin`;
CREATE TABLE `oa_department_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '人员ID',
  `department_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '部门ID',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '次要部门人员关联表';

-- ----------------------------
-- Table structure for oa_file_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_file_group`;
CREATE TABLE `oa_file_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分组名',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '文件分组表';

-- ----------------------------
-- Table structure for oa_file
-- ----------------------------
DROP TABLE IF EXISTS `oa_file`;
CREATE TABLE `oa_file`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '所属模块',
  `sha1` varchar(60) NOT NULL COMMENT 'sha1',
  `md5` varchar(60) NOT NULL COMMENT 'md5',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `filepath` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径+文件名',
  `thumbpath` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图路径',
  `filesize` int(10) NOT NULL DEFAULT 0 COMMENT '文件大小',
  `fileext` varchar(10) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mimetype` varchar(100) NOT NULL DEFAULT '' COMMENT '文件类型',
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件分组ID',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传会员ID',
  `uploadip` varchar(64) NOT NULL DEFAULT '' COMMENT '上传IP',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未审核1已审核-1不通过',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `admin_id` int(11) NOT NULL COMMENT '审核者id',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `audit_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '审核时间',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '来源模块功能',
  `use` varchar(255) NULL DEFAULT NULL COMMENT '用处',
  `download` int(11) NOT NULL DEFAULT 0 COMMENT '下载量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '文件表';

-- ----------------------------
-- Records of oa_file
-- ----------------------------
INSERT INTO `oa_file` VALUES (1, 'admin', '5125347886f07f48f7003825660117039eb8784f', '563e5e8f48e607ed54461796b0cb4844', 'f95982689eb222b84e999122a50b3780.jpg.jpg', 'f95982689eb222b84e999122a50b3780.jpg', 'https://blog.gougucms.com/storage/202202/f95982689eb222b84e999122a50b3780.jpg', 62609, 'jpg', 'image/jpeg', 0, 1, '127.0.0.1', 1, 1645057433, 1, 0, 1645057433, 'upload', 'thumb', 0);
INSERT INTO `oa_file` VALUES (2, 'admin', '5125347886f07f48f7003825660117039eb8784f', '563e5e8f48e607ed54461796b0cb4844', 'e729477de18e3be7e7eb4ec7fe2f821e.jpg', 'e729477de18e3be7e7eb4ec7fe2f821e.jpg', 'https://blog.gougucms.com/storage/202202/e729477de18e3be7e7eb4ec7fe2f821e.jpg', 62609, 'jpg', 'image/jpeg', 0, 1, '127.0.0.1', 1, 1645057433, 1, 0, 1645057433, 'upload', 'thumb', 0);
INSERT INTO `oa_file` VALUES (3, 'admin', '5125347886f07f48f7003825660117039eb8784f', '563e5e8f48e607ed54461796b0cb4844', '1193f7a1585b9f6e8a97ae17718018b3.jpg', 'images/1193f7a1585b9f6e8a97ae17718018b3.jpg', 'https://blog.gougucms.com/storage/202204/1193f7a1585b9f6e8a97ae17718018b3.jpg', 62609, 'jpg', 'image/jpeg', 0, 1, '127.0.0.1', 1, 1645057433, 1, 0, 1645057433, 'upload', 'thumb', 0);
INSERT INTO `oa_file` VALUES (4, 'admin', '5125347886f07f48f7003825660117039eb8784f', '563e5e8f48e607ed54461796b0cb4844', '0f22a5ba4797b2fa22049ea73e6f779c.jpg', 'images/0f22a5ba4797b2fa22049ea73e6f779c.jpg', 'https://blog.gougucms.com/storage/202202/0f22a5ba4797b2fa22049ea73e6f779c.jpg', 62609, 'jpg', 'image/jpeg', 0, 1, '127.0.0.1', 1, 1645057433, 1, 0, 1645057433, 'upload', 'thumb', 0);

-- ----------------------------
-- Table structure for oa_template
-- ----------------------------
DROP TABLE IF EXISTS `oa_template`;
CREATE TABLE `oa_template`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '权限标识唯一，字母',
  `types` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型:1普通消息,2审批消息',
  `check_types` int(11) NOT NULL DEFAULT 0 COMMENT '审批类型:0',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注描述，使用场景等',
  `msg_link` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板链接(审批申请)',
  `msg_title_0` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批申请发审批人)',
  `msg_content_0` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批申请发审批人)',
  `msg_title_1` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批通过发申请人)',
  `msg_content_1` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批通过发申请人)',
  `msg_title_2` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批拒绝发申请人)',
  `msg_content_2` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批拒绝发申请人)',
  `msg_title_3` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板标题(审批通过发抄送人)',
  `msg_content_3` varchar(500) NOT NULL DEFAULT '' COMMENT '消息模板内容(审批通过发抄送人)',
  `email_link` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱消息模板链接',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT  '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '消息模板表';

-- ----------------------------
-- Records of oa_template
-- ----------------------------
INSERT INTO `oa_template` VALUES (1, '公告通知', 'note', 1, 0, '', '/home/note/view/id/{action_id}', '{from_user}发了一个新『公告』，请及时查看', '您有一个新公告：{title}。', '', '', '', '', '', '', '', 1, 1, 1733312491, 1733314809, 0);
INSERT INTO `oa_template` VALUES (2, '新闻通知', 'news', 1, 0, '', '/home/news/view/id/{action_id}', '{from_user}发了一个新『公司新闻』，请及时查看', '您有一个新的新闻：{title}。',  '', '', '', '', '', '', '', 1, 1, 1733312491, 1733314809, 0);
INSERT INTO `oa_template` VALUES (3, '课程学习通知', 'course', 1, 0, '', '/course/learn/lview/id/{action_id}', '{from_user}发了一个新『课程学习』，请及时查看', '您有一个新的课程学习：{title}。',  '', '', '', '', '', '', '', 1, 1, 1733312491, 1733314809, 0);
INSERT INTO `oa_template` VALUES (4, '面授培训通知', 'train', 1, 0, '', '/train/learn/lview/id/{action_id}', '{from_user}发了一个新『面授培训』，请及时查看', '您有一个新的面授培训：{title}。', '', '', '', '', '', '', '', 1, 1, 1733312491, 1733314809, 0);
INSERT INTO `oa_template` VALUES (5, '在线考试通知', 'exam', 1, 0, '', '/exam/exam/exam/id/{action_id}', '{from_user}发了一个新『在线考试』，请及时查看', '您有一个新的在线考试：{title}。', '', '', '', '', '', '', '', 1, 1, 1733312491, 1733314809, 0);

-- ----------------------------
-- Table structure for oa_third_message
-- ----------------------------
DROP TABLE IF EXISTS `oa_third_message`;
CREATE TABLE `oa_third_message`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `msgtype` varchar(100) NOT NULL DEFAULT '' COMMENT '消息类型,email,weixin,mobile',
  `types` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '接收人类型：1人员,2部门,3岗位,4全部',
  `uids` varchar(500) NOT NULL DEFAULT '' COMMENT '人员ids',
  `dids` varchar(500) NOT NULL DEFAULT '' COMMENT '部门ids',
  `pids` varchar(500) NOT NULL DEFAULT '' COMMENT '岗位ids',
  `content` mediumtext NULL COMMENT '消息内容',
  `send_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '发送日期',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '第三方发消息表';

-- ----------------------------
-- Table structure for oa_message
-- ----------------------------
DROP TABLE IF EXISTS `oa_message`;
CREATE TABLE `oa_message`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '消息主题',
  `template` varchar(100) NOT NULL DEFAULT '' COMMENT '消息模板,默认是空私人消息,其他则在配置文件查看消息模板',
  `content` mediumtext  NULL COMMENT '消息内容',
  `file_ids` mediumtext  NULL COMMENT '消息附件',
  `from_uid` bigint(11) NOT NULL DEFAULT 0 COMMENT '发送人id，0为系统消息',
  `types` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '接收人类型：1人员,2部门,3岗位,4全部',
  `uids` varchar(500) NOT NULL DEFAULT '' COMMENT '人员ids',
  `dids` varchar(500) NOT NULL DEFAULT '' COMMENT '部门ids',
  `pids` varchar(500) NOT NULL DEFAULT '' COMMENT '岗位ids',
  `copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '操送人员ids',
  `msg_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '转发、回复关联消息id',
  `is_draft` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否是草稿：1正常消息 2草稿消息',
  `send_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '发送日期',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `clear_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '清除时间',
  `action_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '操作模块数据的id（针对系统消息）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '发消息表';

-- ----------------------------
-- Table structure for oa_msg
-- ----------------------------
DROP TABLE IF EXISTS `oa_msg`;
CREATE TABLE `oa_msg`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '消息主题',
  `template` varchar(100) NOT NULL DEFAULT '' COMMENT '消息模板,默认是私人文本消息,其他则在配置文件查看消息模板',
  `content` mediumtext  NULL COMMENT '消息内容',
  `file_ids` mediumtext  NULL COMMENT '消息附件',
  `from_uid` bigint(11) NOT NULL DEFAULT 0 COMMENT '发送人id，0为系统消息',
  `to_uid` bigint(11) NOT NULL DEFAULT 0 COMMENT '接收人id',
  `message_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '来源发件消息id,0为系统消息',
  `msg_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '转发、回复关联消息id',
  `is_star` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否星标信息：1是 0不是',
  `read_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '阅读时间',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `clear_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '清除时间',
  `action_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '操作模块数据的id（针对系统消息）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '消息表';

-- ----------------------------
-- Table structure for oa_position
-- ----------------------------
DROP TABLE IF EXISTS `oa_position`;
CREATE TABLE `oa_position`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '岗位名称',
  `work_price` int(10) NOT NULL DEFAULT 0 COMMENT '工时单价',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '岗位职称';

-- ----------------------------
-- Records of oa_position
-- ----------------------------
INSERT INTO `oa_position` VALUES (1, '董事长', 0, '', 1, 0, 0);
INSERT INTO `oa_position` VALUES (2, '总经理', 0, '', 1, 0, 0);
INSERT INTO `oa_position` VALUES (3, '人事总监', 0, '', 1, 0, 0);
INSERT INTO `oa_position` VALUES (4, '财务总监', 0, '', 1, 0, 0);
INSERT INTO `oa_position` VALUES (5, '普通人员', 0, '', 1, 0, 0);

-- ----------------------------
-- Table structure for oa_rank
-- ----------------------------
DROP TABLE IF EXISTS `oa_rank`;
CREATE TABLE `oa_rank`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '职级名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '岗位职级';

-- ----------------------------
-- Records of oa_rank
-- ----------------------------
INSERT INTO `oa_rank` VALUES (1, '初级',1, 0, 0);
INSERT INTO `oa_rank` VALUES (2, '中级',1, 0, 0);
INSERT INTO `oa_rank` VALUES (3, '高级',1, 0, 0);

-- ----------------------------
-- Table structure for oa_note_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_note_cate`;
CREATE TABLE `oa_note_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父类ID',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '公告分类';

-- ----------------------------
-- Records of oa_note_cate
-- ----------------------------
INSERT INTO `oa_note_cate` VALUES (1, 0, 1, '普通公告',1, 1637984265, 1637984299,0);
INSERT INTO `oa_note_cate` VALUES (2, 0, 2, '紧急公告',1, 1637984283, 1637984310,0);

-- ----------------------------
-- Table structure for oa_note
-- ----------------------------
DROP TABLE IF EXISTS `oa_note`;
CREATE TABLE `oa_note`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '公告分类ID',
  `title` varchar(225) NULL DEFAULT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '公告内容',
  `src` varchar(100) NULL DEFAULT NULL COMMENT '关联链接',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '相关附件',
  `role_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '查看权限，0所有人,1部门,2岗位,3人员',
  `role_dids` varchar(500) NOT NULL DEFAULT '' COMMENT '可查看部门',
  `role_pids` varchar(500) NOT NULL DEFAULT '' COMMENT '可查看岗位',
  `role_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '可查看人员',
  `start_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '展示开始时间',
  `end_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '展示结束时间',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '发布人id',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '公告表';

-- ----------------------------
-- Table structure for oa_news
-- ----------------------------
DROP TABLE IF EXISTS `oa_news`;
CREATE TABLE `oa_news`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(225) NULL DEFAULT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '新闻内容',
  `src` varchar(100) NULL DEFAULT NULL COMMENT '关联链接',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '相关附件',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '发布人id',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '公司新闻表';

-- ----------------------------
-- Table structure for oa_lecturer
-- ----------------------------
DROP TABLE IF EXISTS `oa_lecturer`;
CREATE TABLE `oa_lecturer`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '专家名称',
  `mobile` varchar(200) NOT NULL DEFAULT '' COMMENT '手机号码',
  `sex` tinyint(1) NOT NULL DEFAULT 0 COMMENT '性别 1男 2女',
  `desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '专家简述',
  `thumb` bigint(11) NOT NULL DEFAULT 0 COMMENT '头像',
  `content` text NULL COMMENT '实战经验',
  `sort` bigint(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `education` bigint(11) NOT NULL DEFAULT 0 COMMENT '讲师学历',
  `level` bigint(11) NOT NULL DEFAULT 0 COMMENT '讲师等级',
  `admin_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0下架 1正常',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '讲师表';

-- ----------------------------
-- Table structure for oa_course_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_course_cate`;
CREATE TABLE `oa_course_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `pid` bigint(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间', 
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '课程分类';


-- ----------------------------
-- Table structure for oa_material
-- ----------------------------
DROP TABLE IF EXISTS `oa_material`;
CREATE TABLE `oa_material`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '名称',
  `types` tinyint(1) NOT NULL DEFAULT 0 COMMENT '类型:1视频,2音频,3PDF,4PPT,5WORD,6图文',
  `desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '简述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `cate_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `file_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '文件id',
  `content` text NULL COMMENT '内容',
  `duration` bigint(11) NOT NULL DEFAULT 0 COMMENT '时长',
  `integral` int(11) UNSIGNED NOT NULL DEFAULT 5 COMMENT '学完获得的积分',
  `admin_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '课程内容表';

-- ----------------------------
-- Table structure for oa_course
-- ----------------------------
DROP TABLE IF EXISTS `oa_course`;
CREATE TABLE `oa_course`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '课程标题',
  `types` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型',
  `cate_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `lecturer_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '关联讲师ID',
  `thumb` int(11) NOT NULL DEFAULT 0 COMMENT '缩略图',
  `is_open` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否公开',
  `duration` int(11) NOT NULL DEFAULT 0 COMMENT '总课时',
  `credit` bigint(11) NOT NULL DEFAULT 0 COMMENT '学分',
  `courseware` varchar(1000) NULL DEFAULT '' COMMENT '课件附件',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '简介',
  `content` text NULL COMMENT '描述',
  `learn_types` tinyint(1) NOT NULL DEFAULT 1 COMMENT '学习对象：0所有人,1部门,2岗位,3人员',
  `dids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定部门',
  `pids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定岗位',
  `uids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定学员',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0未发布 1已发布',
  `sort` int(10) NOT NULL DEFAULT 0 COMMENT '排序',
  `view` bigint(11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `admin_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '课程表';

-- ----------------------------
-- Table structure for oa_course_section
-- ----------------------------
DROP TABLE IF EXISTS `oa_course_section`;
CREATE TABLE `oa_course_section`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '章节标题',
  `course_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '课程id',
  `mids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定课程内容',
  `admin_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '课程章节表';

-- ----------------------------
-- Table structure for oa_course_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_course_record`;
CREATE TABLE `oa_course_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `course_id` int(11) NOT NULL DEFAULT 0 COMMENT '课程id',
  `credit` int(11) NOT NULL COMMENT '学分',
  `duration` int(11) NOT NULL DEFAULT 0 COMMENT '已学时长',
  `progress` int(11) NOT NULL DEFAULT 0 COMMENT '学习进度',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '开始学习日期', 
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '课程学习记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for oa_material_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_material_record`;
CREATE TABLE `oa_material_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `course_id` int(11) NOT NULL DEFAULT 0 COMMENT '课程id',
  `material_id` int(11) NOT NULL DEFAULT 0 COMMENT '内容id',
  `integral` int(11) NOT NULL COMMENT '积分',
  `duration` int(11) NOT NULL DEFAULT 0 COMMENT '已学时长',
  `is_finish` int(1) NOT NULL DEFAULT 0 COMMENT '是否学完,0未完成,1已完成',
  `finish_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '学习完成日期',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '开始学习日期', 
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新日期', 
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '内容学习记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for oa_train_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_train_cate`;
CREATE TABLE `oa_train_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `pid` bigint(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '培训分类';

INSERT INTO `oa_train_cate` VALUES (1, '入职培训', 0, 0, 1, 1746505105, 0, 0);
INSERT INTO `oa_train_cate` VALUES (2, '销售培训', 0, 0, 1, 1746505110, 0, 0);
INSERT INTO `oa_train_cate` VALUES (3, '产品培训', 0, 0, 1, 1746524371, 1746543092, 0);
INSERT INTO `oa_train_cate` VALUES (4, '企业文化', 0, 0, 1, 1746524412, 1746524990, 0);

-- ----------------------------
-- Table structure for oa_train
-- ----------------------------
DROP TABLE IF EXISTS `oa_train`;
CREATE TABLE `oa_train`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '培训标题',
  `cate_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `lecturer_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '关联讲师ID',
  `director_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '培训计划负责人',
  `thumb` int(11) NOT NULL DEFAULT 0 COMMENT '缩略图',
  `credit` bigint(11) NOT NULL DEFAULT 0 COMMENT '学分',
  `start_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '培训开始时间',
  `end_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '培训结束时间',
  `tips_time` varchar(200) NOT NULL DEFAULT '' COMMENT '培训时长描述',
  `duration` int(11) NOT NULL DEFAULT 0 COMMENT '培训时长',
  `address` varchar(200) NOT NULL DEFAULT '' COMMENT '培训地址',
  `types` tinyint(1) NOT NULL DEFAULT 1 COMMENT '培训对象：0所有人,1部门,2岗位,3人员',
  `dids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定部门',
  `pids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定岗位',
  `uids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定学员',
  `is_check` tinyint(1) NOT NULL DEFAULT 0 COMMENT '报名是否需要审核',
  `file_ids` varchar(500) NULL DEFAULT '' COMMENT '课件附件',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '简介',
  `content` text NULL COMMENT '简介',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1待发布,2已发布,3培训中,4已培训',
  `sort` int(10) NOT NULL DEFAULT 0 COMMENT '排序',
  `admin_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '培训计划表';

-- ----------------------------
-- Table structure for oa_train_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_train_record`;
CREATE TABLE `oa_train_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `train_id` int(11) NOT NULL DEFAULT 0 COMMENT '培训id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `appraise_admin` text NULL COMMENT '对培训师评价',
  `appraise_admin_score` bigint(11) NOT NULL DEFAULT 0 COMMENT '对培训师评分',
  `appraise_user` text NULL COMMENT '对学员评价',
  `appraise_user_score` bigint(11) NOT NULL DEFAULT 0 COMMENT '对学员评分',
  `appraise_admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '评价人',
  `appraise_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '评价时间',
  `check_status` int(11) NOT NULL DEFAULT 2 COMMENT '审核状态:1未审核,2通过,3拒绝',
  `check_uid` bigint(11) NOT NULL DEFAULT 0 COMMENT '审核人id',
  `check_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '审核时间',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '报名日期', 
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '培训记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for oa_question_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_question_cate`;
CREATE TABLE `oa_question_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `pid` bigint(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '考题分类';

INSERT INTO `oa_question_cate` VALUES (1, '企业文化', 0, 0, 1, 1746525007, 1746630917, 0);
INSERT INTO `oa_question_cate` VALUES (2, '公司制度', 0, 0, 1, 1746525023, 1746630927, 0);
INSERT INTO `oa_question_cate` VALUES (3, '产品', 0, 0, 1, 1746525030, 1746630936, 0);
INSERT INTO `oa_question_cate` VALUES (4, '销售', 0, 0, 1, 1746525038, 1746630943, 0);
INSERT INTO `oa_question_cate` VALUES (5, '销售术语', 0, 0, 1, 1746630948, 0, 0);

-- ----------------------------
-- Table structure for oa_question
-- ----------------------------
DROP TABLE IF EXISTS `oa_question`;
CREATE TABLE `oa_question`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '所属分类',
  `title` varchar(255) NOT NULL COMMENT '题干',
  `thumb` bigint(11) NOT NULL DEFAULT 0 COMMENT '题干图片ID，选填',
  `desc` text NULL COMMENT '题干描述，选填',
  `types` int(1) NOT NULL DEFAULT 0 COMMENT '考题类型,1判断题,2单选题,3多选题,4填空题,5简单题',
  `level` int(1) NOT NULL DEFAULT 0 COMMENT '考题难度,1非常容易,2比较容易,3常规,4较难,5非常难',
  `score` int(3) NOT NULL DEFAULT 1 COMMENT '分数',
  `integral` int(3) NOT NULL DEFAULT 1 COMMENT '积分',
  `questionselect` text NULL COMMENT '选项',
  `maxselect` int(3) NOT NULL DEFAULT 0 COMMENT '最多选择几项,0无限',
  `questionanswer` text NULL COMMENT '答案',
  `answerdescribe` text NULL COMMENT '试题解析',
  `is_important` int(1) NOT NULL DEFAULT 0 COMMENT '是否重点题目',
  `is_wrong` int(1) NOT NULL DEFAULT 0 COMMENT '是否高频错题',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '0下架,1正常',
  `admin_id` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '考题表';

-- ----------------------------
-- Table structure for oa_paper_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_paper_cate`;
CREATE TABLE `oa_paper_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `pid` bigint(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '试卷分类';

INSERT INTO `oa_paper_cate` VALUES (1, '公司制度考试', 0, 0, 1, 1746540957, 0, 0);
INSERT INTO `oa_paper_cate` VALUES (2, '专业知识考试', 0, 0, 1, 1746540966, 0, 0);

-- ----------------------------
-- Table structure for oa_paper
-- ----------------------------
DROP TABLE IF EXISTS `oa_paper`;
CREATE TABLE `oa_paper`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '试卷标题',
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `start_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '开放考试时间',
  `end_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '结束考试时间',
  `duration` int(11) NOT NULL DEFAULT 0 COMMENT '考试时长',
  `exam_times` int(11) NOT NULL DEFAULT 0 COMMENT '考试次数,0不限制',
  `question_type` int(11) NOT NULL DEFAULT 1 COMMENT '组卷模式:1人工组卷,2抽题组卷,3随机组卷',
  `total_score` int(11) NOT NULL DEFAULT 100 COMMENT '试卷总分',
  `good_score` int(11) NOT NULL DEFAULT 80 COMMENT '优秀分数',
  `pass_score` int(11) NOT NULL DEFAULT 60 COMMENT '及格分数',
  `total_num` int(11) NOT NULL DEFAULT 0 COMMENT '题目总数',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '考试说明',
  `rules` varchar(1000) NULL DEFAULT '' COMMENT '答题规则',
  `methods` varchar(1000) NULL DEFAULT '' COMMENT '违规处理办法',
  `types` tinyint(1) NOT NULL DEFAULT 1 COMMENT '考试属性：1常规考试,2课程考试,3培训考试',
  `train_id` int(11) NOT NULL DEFAULT 0 COMMENT '培训id',
  `course_id` int(11) NOT NULL DEFAULT 0 COMMENT '课程id',
  `open_types` tinyint(1) NOT NULL DEFAULT 1 COMMENT '考试对象属性：0全部,2部门,2岗位,3人员',
  `dids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定部门',
  `pids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定岗位',
  `uids` varchar(500) NOT NULL DEFAULT '' COMMENT '指定学员',
  `reviewer` varchar(500) NOT NULL DEFAULT '' COMMENT '指定阅卷人员',
  `paper_qids` varchar(1000) NOT NULL DEFAULT '' COMMENT '指定试题',
  `paper_score` varchar(1000) NOT NULL DEFAULT '' COMMENT '指定试题分数',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0未发布 1已发布',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '试卷表';

-- ----------------------------
-- Table structure for oa_paper_section
-- ----------------------------
DROP TABLE IF EXISTS `oa_paper_section`;
CREATE TABLE `oa_paper_section`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '试卷章节',
  `paper_id` int(11) NOT NULL DEFAULT 0 COMMENT '试卷id',
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `types` int(11) NOT NULL DEFAULT 0 COMMENT '题型',
  `level` int(11) NOT NULL DEFAULT 0 COMMENT '难易度',
  `num` int(11) NOT NULL DEFAULT 0 COMMENT '数量',
  `score` int(11) NOT NULL DEFAULT 1 COMMENT '章节分数',
  `qids` varchar(1000) NOT NULL DEFAULT '' COMMENT '指定试题',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '试卷章节表';

-- ----------------------------
-- Table structure for oa_paper_snapshot
-- ----------------------------
DROP TABLE IF EXISTS `oa_paper_snapshot`;
CREATE TABLE `oa_paper_snapshot`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `paper_id` int(11) NOT NULL DEFAULT 0 COMMENT '试卷id',
  `detail` text NOT NULL COMMENT '试题快照详情',
  `duration` int(11) NOT NULL DEFAULT 0 COMMENT '答题时长',
  `snapshot` text NOT NULL COMMENT '答题快照',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建日期',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '考试考卷快照' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for oa_paper_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_paper_record`;
CREATE TABLE `oa_paper_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `paper_id` int(11) NOT NULL DEFAULT 0 COMMENT '试卷id',
  `snapshot_id` int(11) NOT NULL DEFAULT 0 COMMENT '试卷快照id',
  `score` int(3) NOT NULL DEFAULT 0 COMMENT '考试得分',
  `integral` int(3) NOT NULL DEFAULT 0 COMMENT '考试得积分',
  `duration` int(11) NOT NULL DEFAULT 0 COMMENT '考试时长',
  `snapshot` text NOT NULL COMMENT '答题快照',
  `answer` text NOT NULL COMMENT '考试答案',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0未审批 1审批通过,2审批拒绝',
  `reviewer` int(11) NOT NULL DEFAULT 0 COMMENT '评卷人id',
  `reviewer_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '评卷时间',
  `reviewer_remark` text NOT NULL COMMENT '评卷意见',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '考试日期', 
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '考试答题记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for oa_certificate
-- ----------------------------
DROP TABLE IF EXISTS `oa_certificate`;
CREATE TABLE `oa_certificate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '证书标题',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '证书名称',
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '证书前缀',
  `signer` varchar(100) NOT NULL DEFAULT '' COMMENT '证书签发人',
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '证书类型',
  `content_top` varchar(1000) NULL DEFAULT '' COMMENT '证书内容头部',
  `content_bottom` varchar(1000) NULL DEFAULT '' COMMENT '证书内容底部',
  `relation_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联内容',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0下架 1正常',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '证书表';

-- ----------------------------
-- Table structure for oa_certificate_users
-- ----------------------------
DROP TABLE IF EXISTS `oa_certificate_users`;
CREATE TABLE `oa_certificate_users`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificate_id` int(11) NOT NULL DEFAULT 0 COMMENT '证书id',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联内容',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1001 CHARACTER SET = utf8mb4 COMMENT = '证书授予表';


-- ----------------------------
-- Table structure for oa_credit_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_credit_record`;
CREATE TABLE `oa_credit_record`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `credit` int(11) NOT NULL DEFAULT 0 COMMENT '学分数',
  `course_id` int(11) NOT NULL DEFAULT 0 COMMENT '学分来源课程id',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '操作时间',
  `type` int(1) NOT NULL DEFAULT 1 COMMENT '类型:0减少,1增加',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '学分记录表';

-- ----------------------------
-- Table structure for oa_integral_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_integral_record`;
CREATE TABLE `oa_integral_record`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `integral` int(11) NOT NULL DEFAULT 0 COMMENT '积分数',
  `course_id` int(11) NOT NULL DEFAULT 0 COMMENT '积分来源课程id',
  `material_id` int(11) NOT NULL DEFAULT 0 COMMENT '积分来源内容id',
  `exam_id` int(11) NOT NULL DEFAULT 0 COMMENT '积分来源考试记录id',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '操作时间',
  `type` int(1) NOT NULL DEFAULT 1 COMMENT '类型:0减少,1增加',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '积分记录表';

-- ----------------------------
-- Table structure for oa_disk
-- ----------------------------
DROP TABLE IF EXISTS `oa_disk`;
CREATE TABLE `oa_disk`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在文件夹目录ID',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `types` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型:0文件,1在线文档,2文件夹',
  `action_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联id',
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享空间id',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '文件名称',
  `file_ext` varchar(200) NOT NULL DEFAULT '' COMMENT '文件后缀名称',
  `file_size` bigint(11) NOT NULL DEFAULT 0 COMMENT '文件大小',
  `is_star` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否标星',
  `share_types` tinyint(1) NOT NULL DEFAULT 0 COMMENT '分享对象：0不分享,1所有人,2部门,3岗位,4人员',
  `share_dids` varchar(255) NOT NULL DEFAULT '' COMMENT '分享部门',
  `share_pids` varchar(255) NOT NULL DEFAULT '' COMMENT '分享岗位',
  `share_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '分享学员',
  `share_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '分享截止时间',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `clear_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '清除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '网盘文件表';

-- ----------------------------
-- Table structure for oa_disk_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_disk_group`;
CREATE TABLE `oa_disk_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分享空间名称',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `director_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '管理人员',
  `group_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '群组成员',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '网盘分享空间表';

-- ----------------------------
-- Records of oa_disk_group
-- ----------------------------
INSERT INTO `oa_disk_group` VALUES (1000, '入职培训', 1, '', '', 1749030795, 1749031338, 0);
INSERT INTO `oa_disk_group` VALUES (1001, '项目文件', 1, '', '', 1749030795, 1749031338, 0);
INSERT INTO `oa_disk_group` VALUES (1002, '常用软件', 1, '', '', 1749030795, 1749031338, 0);
INSERT INTO `oa_disk_group` VALUES (1003, '团建照片', 1, '', '', 1749030795, 1749031338, 0);


-- ----------------------------
-- Table structure for oa_article
-- ----------------------------
DROP TABLE IF EXISTS `oa_article`;
CREATE TABLE `oa_article`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文档标题',
  `origin_url` varchar(255) NOT NULL DEFAULT '' COMMENT '来源地址',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '相关附件',
  `content` text NOT NULL COMMENT '文章内容',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '作者',
  `create_time` bigint(11) NOT NULL DEFAULT 0,
  `update_time` bigint(11) NOT NULL DEFAULT 0,
  `delete_time` bigint(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '文档表';

-- ----------------------------
-- Table structure for oa_edit_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_edit_log`;
CREATE TABLE `oa_edit_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '标识',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段',
  `action_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联id',
  `second_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '第二关联id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人',
  `old_content` mediumtext  NULL COMMENT '修改前的内容',
  `new_content` mediumtext  NULL COMMENT '修改后的内容',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '修改记录表';

-- ----------------------------
-- Table structure for oa_comment
-- ----------------------------
DROP TABLE IF EXISTS `oa_comment`;
CREATE TABLE `oa_comment`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT '模块',
  `topic_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联主题id',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复内容id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `to_uids` varchar(100) NOT NULL DEFAULT '' COMMENT '@用户id',
  `content` mediumtext  NULL COMMENT '评论内容',
  `md_content` mediumtext  NULL COMMENT 'markdown评论内容',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '评论表';

-- ----------------------------
-- Table structure for oa_comment_read
-- ----------------------------
DROP TABLE IF EXISTS `oa_comment_read`;
CREATE TABLE `oa_comment_read`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联评论id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '已读人',
  `create_time` bigint(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '评论已读回执表';
需要在根目录下.env 配置数据库日志的连接

使用例子:
MMLog::createMoment('test2', 2);
MMLog::addContent('content1', 'title');
MMLog::addContent(['content2' => [1, 2, 3], 'content3' => [3, 4, 5]], 'title');
MMLog::addContent($oddsTennis, '$oddsTennis');
MMLog::flush();

[DL]
DB_HOST = 127.0.0.1
DB_USER = username
DB_PASS = password
DB_NAME = itd_logger
DB_PORT = 3306
LOG_LEVEL = 1 #1.debug 2.info  3.warning  4.error  5.fatal

手动创建上述的DB_NAME相同名字的数据库(db_logger),再创建以下表

CREATE TABLE `itd_logger`.`_db_log` (
`id` int(11) NOT NULL,
`project` varchar(20) NOT NULL,
`log_type` varchar(20) NOT NULL,
`title` varchar(1000) NOT NULL,
`content` text NOT NULL,
`record_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据库日志表';
ALTER TABLE `_db_log` ADD PRIMARY KEY (`id`);
ALTER TABLE `_db_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT; COMMIT;

CREATE TABLE IF NOT EXISTS `itd_logger`.`_moment_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`project` varchar(20) NOT NULL,
`moment_name` varchar(20) NOT NULL,
`title` varchar(1000) NOT NULL,
`content` text NOT NULL,
`record_date` datetime NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据库瞬间日志表';

CREATE TABLE `itd_logger`.`_moment_interval` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`project` VARCHAR( 100 ) NOT NULL COMMENT '项目',
`moment_name` VARCHAR( 200 ) NOT NULL COMMENT '瞬间的标签',
`update_time` DATETIME NOT NULL COMMENT '更新时间',
INDEX ( `project` , `moment_name` )
) ENGINE = INNODB COMMENT = '瞬间间隔表';


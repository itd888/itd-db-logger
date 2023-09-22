需要在根目录下.env 配置数据库日志的连接

[DL]
DB_HOST = 127.0.0.1
DB_USER = username
DB_PASS = password
DB_NAME = db_logger
DB_PORT = 3306
LOG_LEVEL = 1 #1.debug 2.info  3.warning  4.error  5.fatal

手动创建上述的DB_NAME相同名字的数据库(db_logger),再创建以下表
CREATE TABLE `_db_log` (
`id` int(11) NOT NULL,
`project` varchar(20) NOT NULL,
`log_type` varchar(20) NOT NULL,
`title` varchar(1000) NOT NULL,
`content` text NOT NULL,
`record_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据库日志表';

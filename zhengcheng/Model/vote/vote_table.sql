
create DATABASE vote IF NOT EXISTS;

DROP TABLE `vote_table` IF EXISTS;

create TABLE `vote_table` (
	`id` bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY COMMENT '表id',
	`uid` char(10) NOT NULL COMMENT '用户id',
	`ip`  varchar(32) NOT NULL COMMENT 'ip地址',
	`article_id` int(10) unsigned NOT NULL COMMENT '文章id',
	`vote_time` int(10) unsigned NOT NULL COMMENT '投票时间戳',
	`d_count` tinyint(3) unsigned NOT NULL COMMENT '日投票数',
	index `vote_time`('vote')
);
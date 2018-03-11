
create DATABASE vote IF NOT EXISTS;

DROP TABLE `vote_table` IF EXISTS;

create TABLE `vote_table` (
	`id` bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY COMMENT '��id',
	`uid` char(10) NOT NULL COMMENT '�û�id',
	`ip`  varchar(32) NOT NULL COMMENT 'ip��ַ',
	`article_id` int(10) unsigned NOT NULL COMMENT '����id',
	`vote_time` int(10) unsigned NOT NULL COMMENT 'ͶƱʱ���',
	`d_count` tinyint(3) unsigned NOT NULL COMMENT '��ͶƱ��',
	index `vote_time`('vote')
);
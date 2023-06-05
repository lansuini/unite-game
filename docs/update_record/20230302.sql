ALTER TABLE `account_ext` ADD `lang` VARCHAR(10)  NULL  DEFAULT 'en'  COMMENT '语言'  AFTER `currency`;
ALTER TABLE `account_ext` CHANGE `currency` `currency` VARCHAR(50)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT 'PHP'  COMMENT '货币';


ALTER TABLE `account_ext` CHANGE `client_id_sub` `client_id_sub` INT(11)  NOT NULL  DEFAULT 0  COMMENT '子渠道号';

CREATE TABLE `customer_sub` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(11) unsigned DEFAULT NULL,
  `symbol` varchar(10) DEFAULT NULL,
  `remark` varchar(128) DEFAULT NULL,
  `is_lock` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_customer_id_symbol` (`customer_id`,`symbol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `data_report_sub` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `client_id_sub` int(11) DEFAULT NULL,
  `bet_amount` bigint(20) DEFAULT NULL,
  `count_date` date DEFAULT NULL,
  `transfer_amount` bigint(20) DEFAULT NULL,
  `bet_count` bigint(20) DEFAULT NULL,
  `updated_time` timestamp NULL DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `valid_user_cnt` int(11) DEFAULT '0',
  `tax` int(11) DEFAULT NULL,
  `login_user_cnt` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_client_id_client_id_sub_count_date` (`client_id`,`client_id_sub`,`count_date`,`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `analysis_export_file_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cost_time` int(11) unsigned DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_id` int(11) unsigned DEFAULT '0',
  `admin_username` varchar(32) DEFAULT NULL,
  `key` varchar(50) DEFAULT NULL,
  `errors` varchar(512) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT '0',
  `ext` varchar(20) DEFAULT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `is_success` tinyint(1) DEFAULT '0',
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_key_admin_id` (`key`,`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `merchant_export_file_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cost_time` int(11) unsigned DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_id` int(11) unsigned DEFAULT '0',
  `admin_username` varchar(32) DEFAULT NULL,
  `key` varchar(50) DEFAULT NULL,
  `errors` varchar(512) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT '0',
  `ext` varchar(20) DEFAULT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `is_success` tinyint(1) DEFAULT '0',
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_key_admin_id` (`key`,`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
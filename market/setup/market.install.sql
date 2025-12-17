/**
 * Market module DB installation (file market.install.sql)
 */


-- Таблица товаров
CREATE TABLE IF NOT EXISTS `cot_market` (
  `fieldmrkt_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `fieldmrkt_alias` varchar(255) NOT NULL DEFAULT '',
  `fieldmrkt_state` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `fieldmrkt_cat` varchar(255) NOT NULL,
  `fieldmrkt_title` varchar(255) NOT NULL,
  `fieldmrkt_desc` varchar(255) DEFAULT '',
  `fieldmrkt_metatitle` varchar(255) DEFAULT '',
  `fieldmrkt_metadesc` varchar(255) DEFAULT '',
  `fieldmrkt_text` MEDIUMTEXT DEFAULT NULL,
  `fieldmrkt_costdflt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fieldmrkt_parser` varchar(64) DEFAULT '',
  `fieldmrkt_ownerid` int UNSIGNED NOT NULL DEFAULT '0',
  `fieldmrkt_date` int UNSIGNED NOT NULL DEFAULT '0',
  `fieldmrkt_updated` int UNSIGNED NOT NULL DEFAULT '0',
  `fieldmrkt_count`  mediumint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldmrkt_id`),
  KEY `fieldmrkt_cat` (`fieldmrkt_cat`),
  KEY `fieldmrkt_alias` (`fieldmrkt_alias`),
  KEY `fieldmrkt_date` (`fieldmrkt_date`),
  KEY `fieldmrkt_ownerid` (`fieldmrkt_ownerid`),
  KEY `fieldmrkt_title` (`fieldmrkt_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

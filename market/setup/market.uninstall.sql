/**
 * Удаление модуля market из базы данных
 */

-- Удаление таблицы товаров
DROP TABLE IF EXISTS `cot_market`;

-- Удаление прав доступа market
DELETE FROM `cot_auth` WHERE `auth_code` = 'market';

-- Удаление категорий market
DELETE FROM `cot_structure` WHERE `structure_area` = 'market';
ALTER TABLE `customer` ADD `game_mc` VARCHAR(1024)  NULL  DEFAULT NULL  AFTER `game_oc`;
ALTER TABLE `customer` ADD `configs` VARCHAR(2048)  NULL  AFTER `game_mc`;

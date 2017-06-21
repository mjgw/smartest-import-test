CREATE TABLE IF NOT EXISTS `Blocks` (
  `block_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `block_webid` varchar(64) NOT NULL,
  `block_title` varchar(255) NOT NULL,
  `block_name` varchar(255) NOT NULL,
  `block_draft_asset_id` int(11) NOT NULL,
  `block_live_asset_id` int(11) NOT NULL,
  `block_info` text NOT NULL,
  `block_created` int(11) unsigned NOT NULL,
  `block_modified` int(11) unsigned NOT NULL,
  `block_last_published` int(11) unsigned NOT NULL,
  `block_parent_block_id` int(11) unsigned NOT NULL,
  `block_blocklist_id` int(11) NOT NULL,
  `block_type` varchar(64) NOT NULL,
  `block_status` varchar(64) NOT NULL,
  `block_order_index` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `Items` CHANGE `item_public` `item_public` VARCHAR(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'FALSE';
ALTER TABLE `TextFragments` CHANGE `textfragment_content` `textfragment_content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `Sets` ADD `set_parent_node_id` INT(11) NOT NULL AFTER `set_itemclass_id`;
ALTER TABLE `AssetIdentifiers` ADD `assetidentifier_block_id` INT(11) NOT NULL AFTER `assetidentifier_item_id`;
ALTER TABLE `ItemPropertyValues` CHANGE `itempropertyvalue_draft_content` `itempropertyvalue_draft_content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `ItemPropertyValues` CHANGE `itempropertyvalue_content` `itempropertyvalue_content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `Items` CHANGE `item_name` `item_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
ALTER TABLE `Pages` CHANGE `page_title` `page_title` VARCHAR(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Untitled Page';
ALTER TABLE `Assets` CHANGE `asset_label` `asset_label` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PagePropertyValues` CHANGE `pagepropertyvalue_live_value` `pagepropertyvalue_live_value` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
ALTER TABLE `Tags` ADD `tag_model_id` INT(11) NOT NULL AFTER `tag_site_id`;
ALTER TABLE `Sites` ADD `site_default_blocklist_style_id` INT(11) NOT NULL AFTER `site_primary_text_placeholder_id`;

UPDATE `Settings` SET `setting_value` = '844' WHERE `Settings`.`setting_type` ='SM_SETTINGTYPE_SYSTEM_META' AND `Settings`.`setting_name`='database_minimum_revision' LIMIT 1;
UPDATE `Settings` SET `setting_value` = '24' WHERE `Settings`.`setting_type` ='SM_SETTINGTYPE_SYSTEM_META' AND `Settings`.`setting_name`='database_version' LIMIT 1;
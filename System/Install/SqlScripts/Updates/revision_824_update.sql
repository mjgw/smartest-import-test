ALTER TABLE  `Tags` ADD  `tag_featured` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `tag_label` ;
ALTER TABLE  `Lists` CHANGE  `list_type`  `list_type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'SM_LIST_SIMPLE';
ALTER TABLE  `Lists` DROP  `list_draft_header_template`;
ALTER TABLE  `Lists` DROP  `list_draft_footer_template` ;
ALTER TABLE  `Lists` DROP  `list_live_header_template` ;
ALTER TABLE  `Lists` DROP  `list_live_footer_template` ;
ALTER TABLE  `Lists` ADD  `list_draft_secondary_set_id` INT( 11 ) NULL AFTER  `list_live_set_id`;
ALTER TABLE  `Lists` ADD  `list_live_secondary_set_id` INT( 11 ) NULL AFTER  `list_draft_secondary_set_id` ;
ALTER TABLE  `Lists` ADD  `list_draft_set_filter` VARCHAR( 64 ) NOT NULL AFTER  `list_live_set_id` ;
ALTER TABLE  `Lists` ADD  `list_live_set_filter` VARCHAR( 64 ) NOT NULL AFTER  `list_draft_set_filter` ;
ALTER TABLE  `Lists` ADD  `list_draft_header_image_id` INT( 11 ) NOT NULL AFTER  `list_title` ;
ALTER TABLE  `Lists` ADD  `list_live_header_image_id` INT( 11 ) NOT NULL AFTER  `list_draft_header_image_id` ;
ALTER TABLE  `ItemClasses` ADD `itemclass_default_date_property_id` INT( 11 ) NOT NULL AFTER `itemclass_default_thumbnail_property_id` ;

UPDATE `Settings` SET `setting_value` = '824' WHERE `Settings`.`setting_type` ='SM_SETTINGTYPE_SYSTEM_META' AND `Settings`.`setting_name`='database_minimum_revision' LIMIT 1;
UPDATE `Settings` SET `setting_value` = '23' WHERE `Settings`.`setting_type` ='SM_SETTINGTYPE_SYSTEM_META' AND `Settings`.`setting_name`='database_version' LIMIT 1;
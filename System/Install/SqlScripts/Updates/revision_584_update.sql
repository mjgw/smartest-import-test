ALTER TABLE  `ItemPropertyValues` CHANGE  `itempropertyvalue_id`  `itempropertyvalue_id` INT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `ManyToManyLookups` CHANGE  `mtmlookup_id`  `mtmlookup_id` INT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `SetsItemsLookup` CHANGE  `setlookup_id`  `setlookup_id` INT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `AssetIdentifiers` CHANGE  `assetidentifier_id`  `assetidentifier_id` INT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `TagsObjectsLookup` CHANGE  `taglookup_id`  `taglookup_id` INT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `AssetClasses` DROP INDEX  `assetclass_name`;
ALTER TABLE  `AssetClasses` ADD  `assetclass_is_sitewide` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `assetclass_site_id`;
ALTER TABLE  `AssetIdentifiers` ADD  `assetidentifier_site_id` MEDIUMINT( 9 ) NULL DEFAULT NULL AFTER  `assetidentifier_item_id`;
ALTER TABLE  `PageProperties` ADD  `pageproperty_is_sitewide` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `pageproperty_site_id`;
ALTER TABLE  `PagePropertyValues` ADD  `pagepropertyvalue_site_id` MEDIUMINT( 9 ) NULL DEFAULT NULL AFTER  `pagepropertyvalue_item_id`;
ALTER TABLE  `PagePropertyValues` ADD  `pagepropertyvalue_language` VARCHAR( 3 ) NOT NULL;
ALTER TABLE  `PageUrls` ADD  `pageurl_asset_id` INT( 11 ) NOT NULL AFTER  `pageurl_site_id`;
ALTER TABLE  `PageUrls` ADD  `pageurl_language` VARCHAR( 3 ) NOT NULL;
ALTER TABLE  `PageUrls` ADD  `pageurl_expires` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `PageUrls` ADD  `pageurl_name` VARCHAR( 64 ) NOT NULL AFTER  `pageurl_id`;
ALTER TABLE  `Pages` ADD  `page_pdf_version_asset_id` INT( 11 ) NOT NULL;
ALTER TABLE  `PageLayoutPresetDefinitions` ADD  `plpd_template_id` INT( 11 ) NOT NULL;
ALTER TABLE  `UsersTokensLookup` ADD  `utlookup_granted_by_user_id` INT( 11 ) NOT NULL;
ALTER TABLE  `Assets` ADD  `asset_thumbnail_id` INT( 11 ) NOT NULL AFTER  `asset_model_id`;
ALTER TABLE  `Assets` ADD  `asset_submitted_from_public` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `asset_is_archived`;
ALTER TABLE  `Assets` ADD  `asset_public_status_trusted` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `asset_submitted_from_public`;
ALTER TABLE  `Items` ADD  `item_submitted_from_public` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `item_is_archived`;
ALTER TABLE  `Items` ADD  `item_public_status_trusted` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `item_submitted_from_public`;
ALTER TABLE  `Items` ADD  `item_type` VARCHAR( 64 ) NOT NULL DEFAULT  'SM_ITEMTYPE_NORMAL' AFTER  `item_slug`;
ALTER TABLE  `Tags` ADD  `tag_site_id` INT( 11 ) NOT NULL AFTER  `tag_label`;
ALTER TABLE  `Tags` ADD  `tag_type` VARCHAR( 64 ) NOT NULL DEFAULT  'SM_TAGTYPE_TAG';
ALTER TABLE  `Users` CHANGE  `user_activated`  `user_activated` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `Users` ADD  `user_info` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `Users` ADD  `user_oauth_consumer_token` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `Users` ADD  `user_oauth_consumer_secret` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `Users` ADD  `user_oauth_access_token` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `Users` ADD  `user_oauth_access_token_secret` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `Users` ADD  `user_oauth_service_id` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `Users` ADD  `user_type` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'SM_USERTYPE_SYSTEM_USER';
ALTER TABLE  `Sets` ADD  `set_webid` VARCHAR( 36 ) NOT NULL AFTER  `set_id`;
ALTER TABLE  `Sets` ADD  `set_feed_sort_field` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER  `set_sort_direction`;
ALTER TABLE  `Sets` ADD  `set_feed_sort_direction` VARCHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'DESC' AFTER  `set_feed_sort_field`;
ALTER TABLE  `Sets` ADD  `set_feed_nonce` VARCHAR( 16 ) NOT NULL AFTER  `set_feed_sort_direction`;
ALTER TABLE  `Sets` ADD  `set_cover_asset_id` INT( 11 ) NOT NULL AFTER  `set_label`;
ALTER TABLE  `ItemClasses` ADD  `itemclass_blog_mode` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `itemclass_settings`;
ALTER TABLE  `ItemPropertyValues` ADD  `itempropertyvalue_parent_value_id` INT( 20 ) NOT NULL AFTER  `itempropertyvalue_name`;
ALTER TABLE  `Sites` ADD  `site_url_prefix` VARCHAR( 32 ) NOT NULL AFTER  `site_domain`;
ALTER TABLE  `Sites` DROP  `site_logo_image_file`;
ALTER TABLE  `Sites` DROP  `site_root`;
ALTER TABLE  `Items` CHANGE  `item_webid`  `item_webid` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `Pages` CHANGE  `page_webid`  `page_webid` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `Assets` CHANGE  `asset_webid`  `asset_webid` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `ItemClasses` CHANGE  `itemclass_webid`  `itemclass_webid` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `TextFragments` CHANGE  `textfragment_webid`  `textfragment_webid` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `ItemProperties` CHANGE  `itemproperty_webid`  `itemproperty_webid` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT  '';
UPDATE `Settings` SET `setting_value` = '584' WHERE `Settings`.`setting_type` ='SM_SETTINGTYPE_SYSTEM_META' AND `Settings`.`setting_name`='database_minimum_revision' LIMIT 1;
UPDATE `Settings` SET `setting_value` = '21' WHERE `Settings`.`setting_type` ='SM_SETTINGTYPE_SYSTEM_META' AND `Settings`.`setting_name`='database_version' LIMIT 1;
=== InfiniteWP Client ===
Contributors: infinitewp, amritanandh, rajkuppus
Tags: admin, administration, amazon, api, authentication, automatic, dashboard, dropbox, events, integration, manage, multisite, multiple, notification, performance, s3, security, seo, stats, tracking, infinitewp, updates, backup, restore, iwp, infinite
Requires at least: 3.1
Tested up to: 6.7.2
Stable tag: 1.13.2

Install this plugin on unlimited sites and manage them all from a central dashboard.
This plugin communicates with your InfiniteWP Admin Panel.

== Description ==

[InfiniteWP](https://infinitewp.com/ "Manage Multiple WordPress") allows users to manage unlimited number of WordPress sites from their own server.

Main features:

*   Self-hosted system: Resides on your own server and totally under your control
*   One-click updates for WordPress, plugins and themes across all your sites
*   Instant backup and restore your entire site or just the database
*   One-click access to all WP admin panels
*   Bulk Manage plugins & themes: Activate & Deactive multiple plugins & themes on multiple sites simultaneously
*   Bulk Install plugins & themes in multiple sites at once
*   and more..

Visit us at [InfiniteWP.com](https://infinitewp.com/ "Manage Multiple WordPress").

Check out the [InfiniteWP Overview Video](https://www.youtube.com/watch?v=s35ZoW95cnU) below.

https://www.youtube.com/watch?v=s35ZoW95cnU

Credits: [Vladimir Prelovac](http://prelovac.com/vladimir) for his worker plugin on which the client plugin is being developed.


== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder
2. Go to the Plugins page and activate InfiniteWP Client
3. If you have not yet installed the InfiniteWP Admin Panel, visit [InfiniteWP.com](http://infinitewp.com/ "Manage Multiple WordPress"), download the free InfiniteWP Admin Panel & install on your server.
4. Add your WordPress site to the InfiniteWP Admin Panel and start using it.

== Screenshots ==

1. Sites & Group Management
2. Search WordPress Plugin Repository
3. Bulk Plugin & Theme Management
4. One-click access to WordPress admin panels
5. One-click updates

== Changelog ==

= 1.13.2 - Feb 17th 2025 =
* Fix: MySQL DB Dump was not functioning when the database host was specified as localhost:3306
* Fix: Added MySQL DB Dump path configuration support for Flex servers.
* Fix: Old Dropbox backups were not being deleted as per the retained limit settings.
* Fix: Fixed an intermittent issue where Dropbox backups failed to upload.
* Fix: Addressed a PHP fatal error: Uncaught ValueError: base and exponent overflow in iwp-client/lib/phpseclib/phpseclib/phpseclib/Math/BigInteger.php.
* Fix: Addressed a PHP fatal error:Uncaught TypeError: Cannot access offset of type string on string in /iwp-client/installer.class.php:931

= 1.13.1 - Dec 5th 2024 =
* Fix: Open Admin failed when the Duo Universal plugin was active.
* Fix: An issue causing SFTP backups to repeatedly upload, resulting in a "Multicall call limit reached" error.
* Fix: Old Dropbox backups were not being deleted as per the retained limit settings.
* Fix: WP Rocket clear cache notifications not disappearing in the wp-admin page after clearing the cache via the IWP admin panel.
* Fix: Enhanced the clarity of update error messages for better troubleshooting.

= 1.13.0 - April 29th 2024 =
* Feature: SFTP support for multicall method backups.
* Fix: SFTP key-based backup not working for AWS S3.
* Fix: Openssl verification issue in the red hat server.
* Fix: PHP Fatal error occurred: Uncaught mysqli_sql_exception: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'IF' at line 1.
* Fix: Code snippet empty responses.
* Fix: The PHP dump fails when the table name is in the format 'wp_example-1034'.
* Fix: While running Phoenix backup failed to update the IWP_backup_status option and value 0.
* Fix: PHP Fatal error occurred: Uncaught TypeError: openssl_verify(): Argument #2 ($signature) must be of type string, array given in /wp-content/plugins/iwp-client/helper.class.php:387 – while adding or re-adding a site on a Red hat server with PHP version 8.3.
* Fix: WP Engine updated the API key.
* Fix: The plugin and theme update issue will show as an error (the theme is at the latest version).
* Fix: '_iwp_redirects' logging in a slow query log for certain users.

= 1.12.5 - Jan 3rd 2024 =
* Improvement: Plugin update response improved.
* Fix: PHP Fatal error occurred: Uncaught ArgumentCountError: Too few arguments to function IWP_MMB_S3::abortMultipartUpload(), 1 passed in /iwp-client/lib/amazon/s3IWPBackup.php
* Fix: PHP Fatal error occurred: Uncaught TypeError: fseek(): Argument #1 ($stream) must be of type resource, bool given in /iwp-client/backup.class.multicall.php:4356
* Fix: IWP Client plugin connection error while performing WP fastest plugin cache clear.
* Fix: Backup taken using Phoenix method not satisfying number of backups to keep.
* Fix: php8 related warnings fixed.

= 1.12.3.1 - Dec 8th 2023 =
* Improvement: New constant (IWP_BROKEN_LINK_RESULT_LIMIT) introduced to limit the Broken Linker checker result.
* Improvement: PHP secure library updated.
* Improvement: Better naming convention adopted.

= 1.12.3 - June 30th 2023 =
* Fix: PHP Fatal error occurred: Uncaught TypeError: count(): Argument #1 ($value) must be of type Countable|array, string given in iwp-client/init.php:2122
* Fix: WordPress database error: [Table 'wp_wptc_options' doesn't exist] SELECT 'value' from 'wp_wptc_options' WHERE 'name'='white_lable_details'.
* Fix: Incorrect core updates are displayed on the admin panel for websites with the WPML Multilingual CMS plugin installed.

= 1.12.1 - June 14th 2023 =
* Feature: Added compatibility to show premium plugin/theme change log links.
* Improvement: Added compatibility for WooCommerce High-Performance Order Storage plugin.
* Improvement: Improved the process of adding new sites.
* Improvement: Added new hook to handle "(503) The service is currently unavailable" while backing up the site to Google Drive.
* Improvement: Added a new constant to skip beta WordPress update notification.
* Fix: Windows server not taking backup root folder for certain users.
* Fix: If the root folder doesn’t have permission backup will stop.
* Fix: Backup runs without error if the iwp_processed_iterator table is missing.
* Fix: Deprecated hook used: wpmu_new_blog
* Fix: Open admin after login case handled.
* Fix: "Parse error: syntax error, unexpected '?' in core.class.php on line 1290" on sites installed on a server with PHP version 5.6.

= 1.11.1 - May 4th 2023 =
* Fix: InfiniteWP: Warning: settings for googledrive are empty. A dummy field is usually needed so that something is saved.
* Fix: If the iwp_processed_iterator table is not available the backup runs without error and in PHP 8.0 it throws a fatal error.
* Fix: PHP Deprecated: Using ${var} in strings is deprecated, use {$var} instead in /wp-content/plugins/iwp-client/backup/databaseencrypt.php on line 4.
* Fix: PHP Fatal error: Uncaught TypeError: array_slice(): Argument #1 ($array) must be of type array, bool gave in wp-content/plugins/wp-client/init.php:3283.
* Fix: PHP Fatal error occurred: Uncaught TypeError: array_merge(): Argument #1 must be of type array, string given.
* Fix: Direct post call to any search.php fails if iwp-client plugin activated.
* Fix: Deprecated function get_option('siteurl').

= 1.11.0 - Jan 10th 2023 =
* Improvement: After activating maintenance mode user can see the site from the wp-admin page.
* Improvement: Site LiteSpeed server detection.
* Improvement: Autoload option changed to off for IWP_backup_history in the options table.
* Improvement: Staging Site wp-admin page will have a different colour theme.
* Fix: Phoenix backup repo is not maintaining the limit for certain users.
* Fix: Some users faced the error during reload data (Error message: Cannot redeclare process_file() (previously declared in export/export.php:676)).
* Fix: The user logout option in the All-in-one security plugin causes the IWP client plugin connection errors.
* Fix: cURL Error(92): HTTP/2 stream 0 was not closed cleanly: PROTOCOL_ERROR (err 1).
* Fix: PHP Fatal error occurred: Uncaught ArgumentCountError: array_key_exists() expects exactly 2 arguments, 1 given /usr/www//wp-content/plugins/iwp-client/init.php.
* Fix: MySQL Error: WordPress database error: Processing the value for the following field failed: taskResults. The supplied value may be too long or contains invalid data.

= 1.9.9 - Aug 9th 2022 =
* Fix: Wordfence recent update changed the table prefix from the camel case, which caused the Client report to be unable to fetch Wordfence data.
* Fix: PHP 8.0 fatal error in backup if the site has the smart-slider plugin installed.
* Fix: WP Fastest Cache : Unable to perform WP Fastest cache error fixed.
* Fix: PHP 8.0 fatal and warning.

= 1.9.8 - Mar 18th 2022 =
* Feature: Added customer role to the Manage user addon if Woocommerce plugin is activated on the WordPress site.
* Fix: iThemes 404 logs removed from client reports as the iThemes plugin do not detect 404 errors.
* Fix: Error in query (1064): Syntax error near offset ) values issue fixed it happen some random server.
* Fix: Backing up a WordPress site with wp_ prefix includes other site database tables in the single and multicall method backups.
* Fix: mu-plugin version not shown on the plugin page.
* Fix: Unable to read InfiniteWP must use plugin

= 1.9.6 - Feb 14th 2022 =
* Feature: Added support for Litespeed Cache.
* Improvement: Ensure phpseclib Crypt_Blowfish is loaded over PEAR’s version.
* Improvement: Enabled support for new Dropbox OAuth Authentication.
* Fix: Enabled version number for InfiniteWP MU plugin loader.
* Fix: Undefined array key “hook_suffix” warning.
* Fix: Delete SFTP test backup file on the remote server.
* Fix: PPL addon editor image not uploaded. (issue happening from 1.9.4.9).
* Fix: Fatal error while backing up a 0kb file using the single call method on site installed on site with php8 server.

= 1.9.4.11 - Jun 16th 2021 =
* Improvement: Amazon s3 library updated.
* Improvement: Wordfence data not correctly showing in Client report due to the Wordfence recent release.
* Improvement: wp_new_user_notification function deprecated arg removed.
* Improvement: New constant (IWP_PHP_DB_ROWS) introduced on the WordPress site to take more row in the PHP database dump backup method.
* Improvement: If MySQL Dump fails, the admin panel will automatically retry MySQL Dump with a different 'max_allowed_packet' value.
* Improvement: Added a few locations for the MySQL dump command.
* Improvement: Multicall method backup speed enhanced.
* Improvement: Enabled support for "Exclude file size" in Phoenix method backup.
* Fix: Multiple Deprecated notices and Warnings when backing up a site (installed on a server with PHP8) using all backup methods.
* Fix: Fatal errors due to disabled PHP functions on certain hosting providers.
* Fix: WP Option cache causing issue in Phoenix backup on certain servers.
* Fix: MySQL Dump using 'passthru()' function not working both single and multicall method.
* Fix: MySQL Dump not working if 'max_allowed_packet' size has low value.
* Fix: open_basedir warning fixed.

= 1.9.4.8.2 - Dec 2nd 2020 =
* Improvement: Improved support for the recent WordPress DB update
* Fix: Percentage ( % ) converted to random string after Restore the single call backup in certain databases
* Fix: IWP maintancemode not working if Minimal Coming Soon & Maintenance Mode – Coming Soon Page plugin activated
* Fix: SSL verification disabled during install plugins/themes from the panel.
* Fix: PHP Notice: Trying to get property 'old_version' of non-object in /home/public_html/wp-content/plugins/iwp-client/installer.class.php on line 250

= 1.9.4.7 - Aug 10th 2020 =
* Improvement: Multicall backup iwp_file_list table prefix validation improved.
* Improvement: Improved compatibility for PHP 7.4 or above.
* Improvement: Clone,Staging and restore DB details call improved.
* Improvement: Website URL added in the Activation key section after activating the IWP client plugin.
* Improvement: Redis cache will be cleared while running the Phoenix backup.

= 1.9.4.6 - Apr 24th 2020 =
* Improvement: WPMerge Temp folder Excluded on all backup methods (single call, multicall, Phoenix).
* Improvement: Improved Error messages for failed plugins, themes, translation updates in the process queue, and activity log.
* Improvement: Phoenix backup failure case detection improved.
* Fix: Unable to activate or network activate plugins on multisite.
* Fix: Add site failed for Network sites without open SSL.
* Fix: .rnd files created on the wp-admin folder in a few instances.

= 1.9.4.5 - Jan 8th 2020 =
* Fix: Important security fix.

= 1.9.4.4 - Dec 17th 2019 =
* Improvement: Implemented a new method to fetch database details for the following operations. Restore, same server staging and existing site cloning.
* Improvement: Support for V3 PPL addon.
* Improvement: Now, the WPTC plugin gets a higher priority than the IWP client plugin.
* Fix: File uploader throws a fatal error if the selected folder doesn't have permission to upload.
* Fix: Error: "Failed to upload to Amazon S3. Please check your details and set Managed Policies on your users to AmazonS3FullAccess" while backing up the site to s3 bucket using the single call method.
* Fix: With the Phoenix method, Backups are shown as completed if the backup folder does not have write permission.
* Fix: iwp-restore-log.txt file excluded in the multicall and single call method backups.

= 1.9.4.1 - July 25th 2019 =
* Feature: Full Support for Multisite Installations.
* Feature: SSH support - You can use your SSH keys to backup your WordPress sites.
* Feature: You can Encrypt your DB backups using the Phoenix backup method.
* Feature: Server Side encryption for Amazon S3 backups is enabled for all three backup mechanisms.
* Feature: Notifications for WooCommerce DB updates.
* Improvement: IWP client plugin will add a must-use plugin to WordPress sites.
* Improvement: Support for WPTC backups to include on IWP Client reports.
* Improvement: File Iterator process is improved in the Multicall backup method.
* Improvement: You can now manage the users on your Multisites.
* Improvement: The activation key will not be shown to subscriber users.
* Improvement: Enabled support for Autoptimize plugin.
* Improvement: "site_map.xml" and "virtual files" are excluded from the backup while backing up the site using the single and multicall method.
* Improvement: Phoenix backup failure retry count decreased as the backup is running for a prolonged time.
* Improvement: By default, backup logs will be created for the multicall backup method.
* Improvement: Backup debug chart added.
* Improvement: Added an option to exclude Database tables on backup.
* Improvement: Google drive and Dropbox will respect the multicall loop break time.
* Improvement: Curl version, SSL version are added in server info.
* Improvement: Response header will now have the correct HTTP version.
* Improvement: MySQL dump backup process improved.
* Improvement: Support for manage users, manage comments, broken link checker, iThemes, Wordfence and Malware Scanner addons for v3.
* Fix: Duo security plugin on WordPress site breaks the IWP client plugin connection while performing open admin action.
* Fix: Backup is retried when it failed with error "Failed to connect to content.dropboxapi.com port 443: Connection timed out. and Could not resolve host: api.dropboxapi.com.".
* Fix: Plugin updates count showing in plugin menu on WP admin page while hide plugin updates setting is enabled on InfiniteWP admin panel via Client Plugin Branding.
* Fix: Phoenix backup SFTP or FTP not using the custom port.
* Fix: FTP SSL not working on Phoenix backup.
* Fix: Ithemes security data is not included in the client reports.
* Fix: Backups failed with error "Database backup failed. Try to enable MySQL dump on your server.".
* Fix: Table "w1.wp_iwp_backup_status" doesnot exist , IWP will now create the table automatically instead of throwing an error.
* Fix: Removed FTP credentials from error messages.
* Fix: Few PHP 7.2 warnings are fixed.
* Fix: Backups taken with Phoenix method are not erased from Google drive Storage while deleting the backups from via admin panel.
* Fix: "*Number of Backups to Keep*" setting was not working for the Phoenix method backups.
* Fix: Backup entries are not removed on WordPress database table wp_iwp_backup_status while deleting the backup schedules with phoenix method.
* Fix: S3 verification failed: File may be corrupted.
* Fix: Broken link checker update link and un-dismiss options throw fatal error.
* Fix: Submitted input out of alignment: got [4194300] expected [4194304].
* Fix: Dropbox account/info did not return HTTP 200. (only for specific users).
* Fix: Open admin not working if iThemes hide backend option enabled.
* Fix: PHP Fatal error occurred: Uncaught Guzzle\Service\Exception\ValidationException: Validation errors: [Key] is a required string.
* Fix: If dump fails, the dump .sql file will be deleted.
* Fix: IWP Client Plugin error, Curl 18 and Curl 92 on updates and install option due to Cloud Flare setup on WordPress sites.

= 1.8.6 - Feb 11th 2019 =
* Fix: Duo security plugin on WordPress site breaks the IWP client plugin connection while performing open admin action.
* Fix: Backup is retried when it failed with error "Failed to connect to content.dropboxapi.com port 443: Connection timed out. and Could not resolve host: api.dropboxapi.com.".
* Fix: Plugin updates count showing in plugin menu on WP admin page while hide plugin updates setting is enabled on InfiniteWP admin panel via Client Plugin Branding.
* Fix: Phoenix backup SFTP or FTP not using the custom port.
* Fix: FTP SSL not working on Phoenix backup.
* Fix: Ithemes security data is not included in the client reports.
* Fix: Backups failed with error "Database backup failed. Try to enable MySQL dump on your server.".

= 1.8.5 - Sep 4th 2018 =
* Improvement: Multisite support for broken link checker plugin.
* Improvement: MySQL DB dump process is improved in multicall and single call backup.
* Improvement: Phoenix method now doesn't wait for the wp-cron to start the backup.
* Improvement: Debug log added for few errors.
* Improvement: If your server gets timed out, the files which are already backed up will be skipped while resume/retrying it(Phoenix backups).
* Improvement: Phoenix Backups keep running in the background even when it's stopped from your admin panel.
* Improvement: Phoenix Backup files are not deleted when we kill a backup process manually.
* Fix: Multicall backup files – S3 bucket – Global users permission.
* Fix: Broken link checker plugin unlink and mark not as broken action throws a fatal error.
* Fix: Phoenix backup files on your server is removed when you immediately backup your site using the single/multicall method after a phoenix backup.
* Fix: Piwik warning.
* Fix: A few PHP warnings are fixed.
* Fix: FTP backup keeps running if slash is added at the end of the FTP path.

= 1.8.3 - May 15th 2018 =
* Improvement: Auto cron task is enabled by default for Phoenix method backups.
* Improvement: Calling Next Function failed error when Shell DB dump backup fails error.

= 1.8.2 - Apr 11th 2018 =
* Fix: Itheme security's action not displaying while generating Report.

= 1.8.1 - Apr 3rd 2018 =
* Feature: New backup method introduced named Phoenix.
* Improvement: Multicall method is implemented for Restore process.
* Improvement: Backup Constants added for Phoenix method.
* Improvement: Support for Wordfence Security plugin New version.
* Improvement: Support for purging cache on WordPress sites.
* Improvement: Phoenix backup now supports V4 AWS regions.
* Improvement: WordFence and Itheme Security added for client reporting.
* Improvement: Site FTP details will be used from the panel if any update require FTP details.
* Improvement: Now you can create S3 buckets in the Paris region.
* Fix: PHP fatal error while backing up your site to a SFTP server using the single call backup method.
* Fix: Restore failed if the backup is placed inside a folder on your S3 bucket(Single call and Multicall).
* Fix: Restore failed if backup files are splitted into part files (Single call and Multicall).
* Fix: Undefined index: hook_suffix warning is fixed.
* Fix: Compatibility with All In One Security.
* Fix: PHP fatal errors due to incompatibility with few plugins like Cornerstone, Litespeed cache, Offers for WooCommerce etc.
* Fix: Dropbox storage exceeds limit error showing incorrectly with "path error".
* Fix: A Few security plugins block the calls to IWP Client plugin breaking the panel and site connection.
* Fix: PHP Fatal error while backing up your site to your SFTP server using SIngle call method.
* Fix: PHP Fatal error occurred: Uncaught Guzzle\Service\Exception\ValidationException: Validation errors: [Key] is a required string in while backing up the site using the multicall method.


= 1.6.8.3 - Jan 8th 2018 =
* Improvement: Now IWP Client Plugin is compatible with most of the premium plugin/theme updates.
* Improvement: Now you can update your Child Themes on WP sites from your IWP dashboard.
* Improvement: MySQL dump is enabled for multicall backup method.
* Improvement: Enabled debug logs to sort out any backup issues in future.
* Fix: If any file named api.php called directly they will get an empty response.
* Fix: Premium updates for specific plugins/themes are not fetched on some instances.
* Fix: File list table break time increased and introduced a constant to change the value (IWP_FILE_LIST_BREAK_TIME) on wp-config.php file.
* Fix: Fatal error in Broken Link checker addon.
* Fix: IWP Client Plugin connection error due to the warning thrown by other plugins.
* Fix: "Zip-error: Error while updating the file .. .. .. in the file list table" error while backing up a site using the multicall method.
* Fix: "Zip-error: Unable to zip" error while backing up the site.
* Fix: Update failed for sites installed on a WP Engine server.
* Fix: Unknown error occurred while backing up the site.
* Fix: "Calling Next Function failed – Error while fetching table data" while backing up your sites.
* Fix: cURL operation timeout error while doing reload data.
* Fix: Windows server had issues in using MySQL dump.
* Fix: "Uncaught Exception: Submitted input out of alignment error" while uploading the backup to your Dropbox account.
* Fix: "Please deactivate & activate InfiniteWP Client plugin on your site, then add the site again" error while adding the site to your panel.
* Fix: IWP client plugin connection is broken when NextGEN plugin is installed on WordPress sites.
* Fix: "Empty Response" error from the sites while backing up using the multicall method.

= 1.6.6.3 - Sep 29th 2017 =
* Fix: v1_retired error while backing up the site to Dropbox

= 1.6.5.1 - Sep 9th 2017 =
* Feature: WP Time Capsule support enabled.
* Improvement: Copy Details in client plugin installation has been updated to clipboard.js from flash.
* Improvement: WordPress updates will be fetched every 2 hours instead of 4 hours.
* Improvement: Custom posts will be registered to avoid false alerts from other security plugins.
* Improvement: Sucuri API is removed. IWP will use Sucuri Plugin to scan your sites for malware.
* Improvement: Calling Next Function failed – Error while fetching table data has been fixed.
* Improvement: Multicall backups would not exclude .zip files.
* Improvement: Support for iThemes Security plugin New version.
* Improvement: wp-content/uploads/wpallimport directory will be automatically excluded from backups.
* Improvement: Default file zip split size decreased to 512 MB.
* Fix: wp_iwp_backup_status table column type is changed from var_char to long text.
* Fix: The backup process would generate multiple warnings.

= 1.6.4.2 - Jul 10th 2017 =
* Improvement: Dropbox API V2 has been integrated with InfiniteWP.
* Fix: While uploading the backup to Dropbox some users get Dropbox verification failed: File may be corrupted error.
* Fix: File exceeds 150MB upload limit error while uploading the backups to Dropbox using Single call back method.
* Fix: Path error if the Dropbox folder have trailing spaces.

= 1.6.4 - May 2nd 2017 =
* Improvement: JSON communication implementation between Admin Panel and Client plugin has been completed.
* Improvement: Unwanted files and folders will be automatically excluded from backups.
* Improvement: Few unwanted folders have been excluded from the list of folders to be backed up.
* Fix: When cloud upload fails during backup, the copy is retained on the server instead of being deleted.
* Fix: The backup process would generate the warning "PHP Warning: fclose(): supplied resource is not a valid stream resource in /home/heidihic/public_html/wp-content/plugins/iwp-client/lib/amazon/s3IWPBackup.php on line 310" during multi-call backups.

= 1.6.3.2 - Jan 4th 2017 =
* Improvement: JSON communication between Admin Panel and Client plugin has been implemented.
* Improvement: If the PHP version of WordPress site is less than 5.4.0 then the single call backups will fail with "Fatal error: Cannot use string offset as an array in /home/asogerb6/public_html/wp-content/plugins/iwp-client/backup.class.singlecall.php on line 340".
* Improvement: Debug files DE_clMemoryPeak.php, DE_clMemoryUsage.php and DE_clTimeTaken.php have been removed.
* Improvement: Security patches have been applied for sites backed up to Amazon S3 storage.
* Fix: Backups having size greater than 2GB could not be uploaded to Google Drive in certain scenarios throwing a bad request error.
* Fix: "Failed to zip files. pclZip error (-4): File '/wp-content/plugins/wordfence/tmp/configCache.php' does not exist" has been fixed.
* Fix: Pluggable.php shouldn't be included before loading all plugins.
* Fix: MySQL error wouldn't show accurately during failed table creation.
* Fix: If the default auto_increment_increment value is set to 2 in the user's server the backup will fail for all sites on that server.
* Fix: If the value for integer field is EMPTY then instead of considering default value as NULL, the plugin creates a duplicate entry during cloning.

= 1.6.1.1 - August 12th 2016 =
* Fix: Bug Fix.
* Fix: Plugins were not updated but were showing incorrectly as updated in the Process Queue.
* Fix: Failed to restore: Error performing query "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */; ": Variable 'character_set_client' can't be set to the value of 'NULL' error message would show during restore.
* Fix: PHP Notice: Undefined index error would show while taking backups.

= 1.6.0 - June 27th 2016 =
* Feature: Activity log for updates and backups to be used in new version of client reporting beta will be saved and retrieved from the WP Admin instead of the IWP Admin Panel, provided the client reporting addon is active.
* Improvement: The code in the backup_status_table has been refactored.
* Fix: Failed backups with date “01 Jan 1970” were not cleared from the database.

= 1.5.1.3 - May 24th 2016 =
* Fix: "Unable to update File list table : Can’t DROP ‘thisFileName’; check that column/key exists" error would be thrown while taking Multi-call backups in the Multi-site WordPress environment.

= 1.5.1.2 - May 18th 2016 =
* Fix: If the file path is 192 characters or higher, it would throw a Zip error: unable to update the file list while performing multicall backup.
* Fix: For the first WP core update alone, the From Version was missing in the WP updates section of the Client Reports.

= 1.5.1.1 - Mar 18th 2016 =
* Improvement: Verifying backup uploaded to Amazon S3 utilized higher bandwidth.

= 1.5.1 - Mar 14th 2016 =
* Improvement: Some of the deprecated WP functions are replaced with newer ones.
* Fix: SQL error populated while converting the IWP backup tables to UTF8 or UTF8MB4 in certain WP sites.
* Fix: DB optimization not working on the WP Maintenance addon.
* Fix: Versions prior to WP v3.9 were getting the wpdb::check_connection() fatal error.

= 1.5.0 - Jan 9th 2016 =
* Improvement: Compatibility with PHP7.
* Improvement: Memory usage in Multi call backup now optimized.
* Improvement: Support for new Amazon S3 SDK. Added support for Frankfut bucket which will solve random errors in amazon backup. For php < v5.3.3 will use older S3 library.
* Improvement: Timeout will be reduced in Single call backup Zip Archive.
* Improvement: Client plugin will support MySQLi by using wpdb class.
* Improvement: All tables created by client plugin will use default DB engine.
* Improvement: Maintenance mode status also included in reload data. This will result in the IWP Admin Panel displaying relevant status colours.
* Improvement: Support for WP Maintenance Addon's new options - Clear trash comments, Clear trash posts, Unused posts metadata, Unused comments metadata, Remove pingbacks, Remove trackbacks.
* Improvement: Dedicated cacert.pem file introduced for Dropbox API." client plugin.
* Fix: Issue with IWP DB Table version not updating.
* Fix: Backup DB table now uses WP's charset (default UTF8). This will solve filename issues with foreign (umlaut) characters.
* Fix: Temp files not getting deleted while using single call backup in certain cases.

= 1.4.3 - Nov 18th 2015 =
* Improvement: Maintenance mode status also included in reload data. This will result in the IWP Admin Panel displaying relevant status colours.
* Fix: Maintenance mode shows off even it is in ON mode after the site is reloaded.

= 1.4.2.2 - Sep 24th 2015 =
* Improvement: Translation update support.
* Improvement: All executable files in client plugin should check the running script against the file name to prevent running directly for improved security.
* Improvement: Error message improved for premium plugin/theme when not registered with iwp process.
* Fix: Some admin theme blocks IWP Client from displaying activation key.
* Fix: Fatal error while calling wp_get_translation_updates() in WP versions lower than v3.7.

= 1.4.1 - Aug 31th 2015 =
* Fix: Branding should take effect which we lost in v1.4.0 without making any changes.

= 1.3.16 - Jul 28th 2015 =
* Fix: Dropbox download while restore create memory issue Fatal Error: Allowed Memory Size of __ Bytes Exhausted.

= 1.3.15 - Jul 8th 2015 =
* Improvement: Security improvement.
* Fix: Parent theme update showing as child theme update.
* Fix: Bug fixes.

= 1.3.14 - Jul 3rd 2015 =
* Fix: Bug fix.

= 1.3.13 - May 13th 2015 =
* Fix: In certain cases, a multi-call backup of a large DB missed a few table's data.

= 1.3.12 - Mar 31st 2015 =
* Fix: In a few servers, readdir() was creating "Empty reply from server" error and in WPEngine it was creating 502 error while taking backup
* Fix: .mp4 was excluding by default 

= 1.3.11 - Mar 27th 2015 =
* Improvement: using wp_get_theme() instead of get_current_theme() which is deprecated in WordPress      
* Fix: IWP failed to recognise the error from WP v4.0
* Fix: Restoring backup for second time
* Fix: $HTTP_RAW_POST_DATA is made global, which is conflicting with other plugin
* Fix: Install a plugin/theme from Install > My Computer from panel having IP and different port number
* Fix: Install a plugin/theme from Install > My Computer from panel protected by basic http authentication
* Fix: Google Webmaster Redirection not working with a few themes
* Fix: Bug fixes

= 1.3.10 - Jan 27th 2015 =
* Fix: Bug Fix - This version fixes an Open SSL bug that was introduced in v1.3.9. If you updated to v1.3.9 and are encountering connection errors, update the Client Plugin from your WP dashboards. You don't have to re-add the sites to InfiniteWP.

= 1.3.9 - Jan 26th 2015 =
* Fix: WP Dashboard jQuery conflict issue. 
* Fix: Empty reply from server created by not properly configured OpenSSL functions.
* Fix: Google Drive backup upload timeout issue.

= 1.3.8 - Dec 2nd 2014 =
* Fix: Fixed a security bug that would allow someone to put WP site into maintenance mode if they know the admin username. 

= 1.3.7 - Nov 21st 2014 =
* Fix: Dropbox SSL3 verification issue.

= 1.3.6 - Sep 1st 2014 =
* Fix: IWP's PCLZIP clash with other plugins. PCLZIP constants have been renamed to avoid further conflicts. This will fix empty folder error - "Error creating database backup folder (). Make sure you have correct write permissions."
* Fix: Amazon S3 related - Call to a member function list_parts() on a non-object in wp-content/plugins/iwp-client/backup.class.multicall.php on line 4587.

= 1.3.5 - Aug 19th 2014 =
* Improvement: Support for iThemes Security Pro.
* Fix: IWP's PCLZIP clash with other plugins.

= 1.3.4 - Aug 11th 2014 =
* Feature: Maintenance mode with custom HTML.
* New: WP site's server info can be viewed.
* Improvement: Simplified site adding process - One-click copy & paste.
* Improvement: New addons compatibility.

= 1.3.3 - Jul 28th 2014 =
* Fix: False "FTP verification failed: File may be corrupted" error.

= 1.3.2 - Jul 23rd 2014 =
* Fix: Dropbox backup upload in single call more then 50MB file not uploading issue.

= 1.3.1 - Jul 16th 2014 =
* Fix: "Unable to create a temporary directory" while cloning to exisiting site or restoring.
* Fix: Disabled tracking hit count.

= 1.3.0 - Jul 9th 2014 =
* Improvement: Multi-call backup & upload.
* Fix: Fatal error Call to undefined function get_plugin_data() while client plugin update.
* Fix: Bug fixes.


= 1.2.15 - Jun 23rd 2014 =
* Improvement: Support for backup upload to SFTP repository.
* Fix: Bug fixes.

= 1.2.14 - May 27th 2014 =
* Improvement: SQL dump taken via mysqldump made compatible for clone.

= 1.2.13 - May 14th 2014 =
* Fix: Google library conflict issues are fixed.

= 1.2.12 - May 7th 2014 =
* Improvement: Backup process will only backup WordPress tables which have configured prefix in wp-config.php.
* Improvement: Support for Google Drive for cloud backup addon.
* Improvement: Minor improvements.
* Fix: Bug fixes

= 1.2.11 - Apr 16th 2014 =
* Fix: Bug fixes

= 1.2.10 - Apr 10th 2014 =
* Fix: wp_iwp_redirect sql error is fixed


= 1.2.9 - Apr 9th 2014 =
* Improvement: Support for new addons.
* Fix: Strict Non-static method set_hit_count() and is_bot() fixed.

= 1.2.8 - Jan 21st 2014 =
* Fix: Minor security update

= 1.2.7 - Jan 13th 2014 =
* Fix: Activation failed on multiple plugin installation is fixed
* Fix: Dropbox class name conflit with other plugins is fixed
* Fix: Bug fixes

= 1.2.6 - Nov 18th 2013 =
* Fix: Bug fixes

= 1.2.5 - Oct 30th 2013 =
* Improvement: Compatible with WP updates 3.7+


= 1.2.4 - Oct 16th 2013 =
* Fix: Empty backup list when schedule backup is created/modified

= 1.2.3 - Sep 11th 2013 =
* Fix: Gravity forms update support

= 1.2.2 - Sep 6th 2013 =
* Improvement: Minor improvements for restore/clone
* Fix: Warning errors and bug fixes for restore/clone

= 1.2.1 - Aug 28th 2013 =
* Fix: Fatal error calling prefix method while cloning a fresh package to existing site

= 1.2.0 - Aug 26th 2013 =
* Improvement: Backup fail safe option now uses only php db dump and pclZip
* Improvement: Better feedback regarding completion of backups even in case of error
* Improvement: Restore using file system (better handling of file permissions)
* Fix: Notice issue with unserialise

= 1.1.10 - Apr 5th 2013 =
* Charset issue fixed for restore / clone
* Dropbox improved
* Cloning URL and folder path fixed


= 1.1.9 - Mar 22nd 2013 =
* Better error reporting
* Improved connection reliability

= 1.1.8 - Feb 21st 2013 =
* Minor fixes

= 1.1.7 - Feb 20th 2013 =
* Old backups retained when a site is restored
* Compatible with Better WP Security
* Compatible with WP Engine
* Improved backups
* Bug fixes

= 1.1.6 - Dec 14th 2012 =
* Multisite updates issue fixed

= 1.1.5 - Dec 13th 2012 =
* WP 3.5 compatibility
* Backup system improved
* Dropbox upload 500 error fixed

= 1.1.4 - Dec 7th 2012 =
* Bug in command line backup fixed

= 1.1.3 - Dec 3rd 2012 =
* Backup improved and optimize table while backing up fixed
* Excluding wp-content/cache & wp-content/w3tc/ by default
* Amazon S3 backup improved
* pclZip functions naming problem fixed
* get_themes incompatibility fixed

= 1.1.2 - Oct 5th 2012 =
* Respository issue when openSSL is not available, fixed
* Restore MySQL charset issue fixed
* Backups will not be removed when sites are re-added

= 1.1.1 - Oct 2nd 2012 =
* Improved backups
* Bug fixes

= 1.1.0 - Sep 11th 2012 =
* Premium addons bugs fixed
* Reload data improved

= 1.0.4 - Aug 28th 2012 =
* Premium addons compatibility
* Clearing cache and sending WP data
* Bugs fixed

= 1.0.3 - Jun 11th 2012 =
* WordPress Multisite Backup issue fixed
* Bugs fixed

= 1.0.2 - May 16th 2012 =
* Bugs fixed

= 1.0.1 - May 11th 2012 =
* WordPress Multisite support
* Bugs fixed

= 1.0.0 - Apr 25th 2012 =
* Public release
* Bugs fixed
* Feature Improvements

= 0.1.5 - Apr 18th 2012 =
* Client plugin update support from IWP Admin Panel 
* Backup file size format change


= 0.1.4 - Apr 2nd 2012 =
* Private beta release

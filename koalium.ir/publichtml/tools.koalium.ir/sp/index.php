<?php

//print_r($_SERVER);die();

$sitepad['db_name'] = 'sp745';
$sitepad['db_user'] = 'sp745';
$sitepad['db_pass'] = 'p088KSq4p1';
$sitepad['db_host'] = 'localhost';
$sitepad['db_table_prefix'] = 'spap_';
$sitepad['charset'] = 'utf8mb4';
$sitepad['collate'] = 'utf8mb4_unicode_ci';
$sitepad['serving_url'] = '127.0.0.1/sp';// URL without protocol but with directory as well
$sitepad['url'] = 'http://127.0.0.1/sp';
$sitepad['relativeurl'] = '/sp';
$sitepad['.sitepad'] = 'C:\Program Files\Ampps/private';
$sitepad['sitepad_plugin_path'] = 'C:/Program Files/Ampps/ampps/scripts/sitepad';
$sitepad['editor_path'] = 'C:/Program Files/Ampps/ampps/scripts/sitepad/editor';
$sitepad['path'] = dirname(__FILE__);
$sitepad['AUTH_KEY'] = 'lhozngmyetwetzbz48dlikzpc3iop9ayrcexkigpbfsotyiqjpfbm46id0ajkxef';
$sitepad['SECURE_AUTH_KEY'] = 'n6tye9v60psolrvhsuxrnk8pwbnhsejqa35bjmqpqwrhvrmdmoqwpw1p3evrzm29';
$sitepad['LOGGED_IN_KEY'] = 'ob2ugvyquvo9o332ern1qyk8qm6bykvzqnuwanrox4bcvxxpbawbqdwninjld4ot';
$sitepad['NONCE_KEY'] = 'zouun3ux6qq5aiprpgmb2nxt1my6soirpigjluy1yif2lvxl7fvhmxyexxe1hhin';
$sitepad['AUTH_SALT'] = 'tyox3aar7di33khrdwi72vmvch1lfszbuepinjen546tqakdvd741kli3l1kqwix';
$sitepad['SECURE_AUTH_SALT'] = 'i3yphhroifrtdr15cf2byquonllsdwjiwxyw87mxhdkntslm4sgsmvghlvycxxtd';
$sitepad['LOGGED_IN_SALT'] = 'rv5isbeqrhecj7kn3csvjxho82kluf7npssqoom0frjhxkiikn9hzehuqyadzhkq';
$sitepad['NONCE_SALT'] = '7jn6j4fxxgirqtqgait4wtadluuxyuld8f2amnfeky76bjzighi4uhqpkzzqts4q';

if(!include_once($sitepad['editor_path'].'/site-inc/bootstrap.php')){
	die('Could not include the bootstrap.php. One of the reasons could be open_basedir restriction !');
}


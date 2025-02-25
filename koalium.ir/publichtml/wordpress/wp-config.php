<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'koaliumi_wp' );

/** Database username */
define( 'DB_USER', 'wpadmin' );

/** Database password */
define( 'DB_PASSWORD', 'koala551364' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '`<iwz!&N_|/-&T-pS8H-f:htx(|%LJ3C}$DHShI_XLotKeQ0)&VU{PmBc[$Dt}^T' );
define( 'SECURE_AUTH_KEY',  'p($jtS86pO#wO@,)Z|~a/hUt4eGlT_TBt 5JJ]F?>xK]Qe.c beBGb5(/LQz*al{' );
define( 'LOGGED_IN_KEY',    'DPB.dW#y`BGzbWyR-0uTA8m:X=;pznag#l?AI@`X= <JU/Kgmg 9X}-,`T^nWvmi' );
define( 'NONCE_KEY',        'Ifj/}y7;^9SJ+}p~65<0}Uc/V`GG;}AOeFQ2O~u$xnuo2-[vCftx[Y,|_0+]Ef^B' );
define( 'AUTH_SALT',        '1c3R{pW?CwP}%{LB,X+_m)o~TR#si]YJ],z&<N4*&,%oHK2_*?gb?@##cU=]!xvD' );
define( 'SECURE_AUTH_SALT', 'M@kwM]},Hh`?9L9@(TP65dx:q[<$TW>g)`d>xH:m%^?ufdK2k?%*%Gx.9^vC:w>p' );
define( 'LOGGED_IN_SALT',   'phTlD3]_@=w6u/e>uJeD{bXYj8Qs/_?V( ,r}A),**lO}7AEWLCf.`Tmy&G;{iG0' );
define( 'NONCE_SALT',       'Y]+SdJ<4L|{:YJH*YSJ|Zw>#E,XtD#O3IG2v|nwI|PcEVzT6.y(h)=Fk:wT]@@?_' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress_plugin_development');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('AUTH_KEY',         'YV6p}*_x9+ud@:alvJ*3ECBL~2{3c0[/Uj?SdIoT0qpZtJ%R-BL,sHH_6@!X3Fa|');
define('SECURE_AUTH_KEY',  '*<c!d11~7q,GOiC&F*w!e/]X<:Z6-*UGYkew!6NZ[25TdJi9(Re}ZRXEXTK`/8GB');
define('LOGGED_IN_KEY',    'N##uW9[-LBfsSM c4}PtGl~~KfC<oV]p}0W mnqZ?NnhDf`8Nz<:=P=a-Gg?2x|Q');
define('NONCE_KEY',        '}mY*=+CQ[-1=u/*Px1^dc=|JD1!X.gM-qm^.m_($(F6q;WLJ$/6`fpoZ1$h4AjjC');
define('AUTH_SALT',        'I0~^T|X+76A~@_sfb7u+aT9<;;=-W|+=rvZanZ0|HXfNMBn?yeH`9RGg97Ih7ILp');
define('SECURE_AUTH_SALT', '6/B}:+k#-#Bu.j=+8!T=+On(2)yf+)Fq.y#BMpR4e8Py]@.Hh@XQl5)Y|0ibgIzD');
define('LOGGED_IN_SALT',   '+5#xwU-p5UUFu1EMOSo:UL%hC+,,Qu2t|q>haRm=$+VT?nN8a@C_QV0oxR-|HO:Y');
define('NONCE_SALT',       '#okL|-Zd+;IQ/aSj&P}F|#3Bw~jgV}e32h{,hHE]<E2Hw0OJp-i3+!kmP dM^[*y');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'csclarklaw');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '*7-|Oh_@+|&]7{6W{GOb_~HZybuM[7[;jXoY#*lg7JXjk84T9,NvF^f&}*ZTdhiY');
define('SECURE_AUTH_KEY',  'mupdw2A{T:^h7Mx<zzNA3iQgV^.|Yp^X)-XdR-yU6Nyjqe9R,Oki_;2phzw-|+hb');
define('LOGGED_IN_KEY',    'q0,!z}EHN-,a}7)LrV}2>YqPvQW@{fj,+r]R{2yBNJW][|W:uXpJhFy`f1mrq}k~');
define('NONCE_KEY',        '>4)+hnCCr=s%|)C]|]nkb4vj<[|=DlhehCFQ0E-Fo| w<*imoIJOuE|jl|[x8sN2');
define('AUTH_SALT',        'v%(l:qO|Glc1)-vE3IYOklr&VLfe-1+yJdH(fNDJ2^[[zZvc&M>lt~sYaC&HAsOF');
define('SECURE_AUTH_SALT', 'P2vl<|-`7<.#od_=vDH8hEXPlqAV9HseND:406Q|cYPf4Ks/=K,[fQ6|Qmv+Ru-o');
define('LOGGED_IN_SALT',   'hNdT phbD:K9U+-2SiRuo*KSPWg<4C+9zecx#h?LnOZ$+>uVoOp:@},^&+=}|=,E');
define('NONCE_SALT',       '34N^TP4KhHS[=k 0_&~^qH-P;SI{]2+:Ig]+x|.e];P~F6g J^Ny)^$]FbxylZ|H');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'spk_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'calendarr' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '[{EV(L[~+s*$jcnjo1NCTbQEOiNb@|L~`V7>!J{UunNlt.d,yix`)uMBE7[(bBhm' );
define( 'SECURE_AUTH_KEY',  'rMc^mD(2~%eaz)RZcY$zh!w E%ic5/fd94Lb+9eULl>9nB|QhbDQ<QJG$Aoe}]gK' );
define( 'LOGGED_IN_KEY',    'pnr4MY#Q?a7=eCJEw:MA^JW2y#vy-z}8kStd>9zovCQgio]?D+k$/Iu}tc/iqaU`' );
define( 'NONCE_KEY',        '4A^i%JVnf<kmIC=uGl.ECYCG6q<R(;`C|#M|)kRnVNQ7n{Pgc*iy[;s-O+*RgQ?:' );
define( 'AUTH_SALT',        '!{oNZJhU.jVYg|NLyLaT0{f68y9:F*9O1K$:v))%1!]j`*:Bs%K?.,N;|EF1v3Kd' );
define( 'SECURE_AUTH_SALT', 'n*#b=+Xe^{]H;#/K}mHOFIM^Z!*9oS?x}+0h{pYeHz!-4J!zk!k*>QKPSU-8z[$V' );
define( 'LOGGED_IN_SALT',   '78kHr4l(87,4:Uwqh;4liJG+q&c?u^RJNb>pkY5eyil6`jzj!A}V#FS0=P)F^l.{' );
define( 'NONCE_SALT',       'o9X*KFYgxEyZ|bGE?uvGJI+bj-J jh00OLQ`+TNoSlB#iTc!AMFtn;/a.6Poa8<M' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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

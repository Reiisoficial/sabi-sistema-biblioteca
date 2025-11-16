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
define( 'DB_NAME', 'sabi_academico' );

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
define( 'AUTH_KEY',         'wp5bapt32t5ddjohswq082xrvhyt7rybwbyfxzkiuwlwj9imu8p4z9vfaqcseixz' );
define( 'SECURE_AUTH_KEY',  'npbhxcppfn6j5na4uk0y26gkursc6kqsh9luyzrdetbgla12crmx5svuwvzyyjge' );
define( 'LOGGED_IN_KEY',    'nb0e7l1ud2q4vxqrjvpvldpneyjeu7bctvodqlpxjfzqio2njjyqhp2q8gjcmfm6' );
define( 'NONCE_KEY',        'q4wphbqklvum1dpashv9njohknxm245ltzvdw6h1s3abxsoa31i1arf4rzqsaqpy' );
define( 'AUTH_SALT',        'h56bmeghcfcbqjpabtfkdd6pbzmtowzjtuidtkyrphd0l1m90x0qds3roj3c8v1z' );
define( 'SECURE_AUTH_SALT', 'fw3yw7ucbp15naw5nypnwy9ocrfnajtvcoj2eepiuplhnmohktlwya90dtpuxu62' );
define( 'LOGGED_IN_SALT',   '4tszktujohnc8zilmrgkrl9tymi1ztsxh8qrn8cl5gjr4o5wkkbpdx4ecg0duaxv' );
define( 'NONCE_SALT',       'tp6mb62kwx3hgcntoo0ohmjj6yqf3zc7dvcsryporswts5iaiokn7l6wtx7rbfg2' );

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


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

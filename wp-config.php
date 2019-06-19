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
define( 'DB_NAME', 'woo_xero' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'admin786' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'uf45uQ*z[F^(oH[wNWsF&2Q?AF1{%*NGM<~|U8VlhmYLXmWQX*)_{9u{BU5WJ# L' );
define( 'SECURE_AUTH_KEY',  'WR$2`/0~6p/e:BQ~;i<`U8f~ovR1$NQ.zR;BGd}#qJX`*#:>a{Pf0~9~ETVBa|x`' );
define( 'LOGGED_IN_KEY',    ';ryJPx,7:{kgwZKJxd5JuOGH8c$c :IU!/!?g9{qMVm!J/`{oNzY {l5?n=3$~<8' );
define( 'NONCE_KEY',        'WsGy0hM3y<$RqelkgHvY~gN~w/f/Na&3kUI_h;5blajGJb,j@qltoHC%Q^`P,}-P' );
define( 'AUTH_SALT',        '[?yg4$x:$v9-fZr=CQp[!2d:y2Cu,4zs68us+_I:slShTM]cZORYzJ4j#nC.;UI~' );
define( 'SECURE_AUTH_SALT', 'FEr_}iP7DucQ^AsNVYhlCJ,*`p=?<GmRqm>#nnYuF-QRV aKP#!bk:||a=f4aw+S' );
define( 'LOGGED_IN_SALT',   'F:^kTqZ~ixVX=:pV?-FGK_4_ij`|P*zhvpH8nN*;([OHAe8PW5f)c/m}g0*QcI }' );
define( 'NONCE_SALT',       'y_HwR;8r2|o#AGF0}?_?k=1]Gk{wo=u)%MW[wzJ,Ww@qU9?f#n#_v;7(vt@*,Bon' );

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
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Install plugin ask for ftp solution **/
define('FS_METHOD', 'direct');

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

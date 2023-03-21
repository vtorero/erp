<?php

if ( (!empty( $_SERVER['HTTP_X_FORWARDED_HOST'])) ||
     (!empty( $_SERVER['HTTP_X_FORWARDED_FOR'])) ) {

    // http://wordpress.org/support/topic/wordpress-behind-reverse-proxy-1
   // $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];

    define('WP_HOME', 'https://www.americatv.com.pe/cinescape');
    define('WP_SITEURL', 'https://www.americatv.com.pe/cinescape');

    //$_SERVER['REQUEST_URI'] =  "/cinescape3" . $_SERVER['REQUEST_URI'];

    // http://wordpress.org/support/topic/compatibility-with-wordpress-behind-a-reverse-proxy
    $_SERVER['HTTPS'] = 'on';
}


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
define( 'DB_NAME', 'cinescape');

/** MySQL database username */
define( 'DB_USER', 'cinescape');

/** MySQL database password */
define( 'DB_PASSWORD', 'ksORI8Pe1yyrc64' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY', '36cc85178d39b41896f2a07542762d3de54a07e681f025f41cafe96814cb020b');
define('SECURE_AUTH_KEY', '8bb645f97d2994b53c1c7c7555c0e3a4886254999fc199cc72342876fb45e36d');
define('LOGGED_IN_KEY', 'e2b8d1794710a18f46be644a8b2de77f70e0d491d7653dfb631c6819fca11726');
define('NONCE_KEY', '8bc3d9e5389fb133c87988962b3e675c239e4c848bc66c1c3e4d2ac2172e1483');
define('AUTH_SALT', '1cc55958d634f9d84b77b86d9ce91989902003b0bdc92c525546b675407f48da');
define('SECURE_AUTH_SALT', '56ac28b1eaacdbf0f456172d2026491a3e2f3833eab38c53e555a0f641a1bb98');
define('LOGGED_IN_SALT', 'cf5bf6622c40bb761e1fcae44243b3e2031ea62e4f1bddd153473a9fae997f69');
define('NONCE_SALT', 'dd42ee865ba3239d691a326f93d41dbc11442baee218012041cb7fc805bfb1cc');

/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
$_SERVER['HTTP_HOST'] = 'www.americatv.com.pe';
 
/*Include, ONLY if the main site is SSL forced*/
//define('FORCE_SSL_ADMIN', true);
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)
       $_SERVER['HTTPS']='on';

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
define( 'WP_DEBUG', false );
define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );   // 5.2 and later define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true ); 
#define ('WP_CACHE',true);

/* That's all, stop editing! Happy publishing. */

define('FS_METHOD', 'direct');

/**
 * The WP_SITEURL and WP_HOME options are configured to access from any hostname or IP address.
 * If you want to access only from an specific domain, you can modify them. For example:
 *  define('WP_HOME','http://example.com');
 *  define('WP_SITEURL','http://example.com');
 *
*/

if ( defined( 'WP_CLI' ) ) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

#define('WP_SITEURL','https://www.americatv.com.pe/cinescape');
#define('WP_HOME', 'https://www.americatv.com.pe/cinescape');

//define('WP_SITEURL','https://www.americatv.com.pe/cinescape2');
//define( 'WP_SITEURL', 'https://' . $_SERVER["REQUEST_URI"] . '/cinescape' );
//define( 'WP_SITEURL', str_replace("/wp-admin/", "/cinescape2/wp-admin/",  $_SERVER['REQUEST_URI']) );
//define('WP_SITEURL', 'https://www.americatv.com.pe/cinescape');

#$_SERVER['HTTP_HOST'] = 'www.americatv.com.pe';
 
/*Include, ONLY if the main site is SSL forced*/
define('FORCE_SSL_ADMIN', true);
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)
	       $_SERVER['HTTPS']='on';

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

#define('WP_TEMP_DIR', '/opt/bitnami/apps/wordpress/tmp');




//  Disable pingback.ping xmlrpc method to prevent Wordpress from participating in DDoS attacks
//  More info at: https://docs.bitnami.com/general/apps/wordpress/troubleshooting/xmlrpc-and-pingback/

if ( !defined( 'WP_CLI' ) ) {
    // remove x-pingback HTTP header
    add_filter('wp_headers', function($headers) {
        unset($headers['X-Pingback']);
        return $headers;
    });
    // disable pingbacks
    add_filter( 'xmlrpc_methods', function( $methods ) {
            unset( $methods['pingback.ping'] );
            return $methods;
    });
    add_filter( 'auto_update_translation', '__return_false' );
}



//$_SERVER['REQUEST_URI'] = str_replace("/wp-admin/", "/cinescape2/wp-admin/",  $_SERVER['REQUEST_URI']);

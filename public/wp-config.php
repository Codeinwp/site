<?php
define('WP_CACHE', true); // Added by WP Rocket
ini_set( 'display_errors', 0 );

// ===================================================
// Load database info and local development parameters
// ===================================================
if ( file_exists( dirname( __FILE__ ) . '/../config/live-config.php' ) ) {
	define( 'WP_LOCAL_DEV', false );
	include( dirname( __FILE__ ) . '/../config/live-config.php' );
} else {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/../config/local-config.php' );
}

// ========================
// Custom Content Directory
// ========================
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content' );

// ================================================
// You almost certainly do not want to change these
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ================================
// Language
// Leave blank for American English
// ================================
define( 'WPLANG', '' );

// ======================
// Hide errors by default
// ======================
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG', false );

// ======================================
// Load a Memcached config if we have one
// ======================================
if ( file_exists( dirname( __FILE__ ) . '/memcached.php' ) ) {
	$memcached_servers = include( dirname( __FILE__ ) . '/memcached.php' );
}

// =======================================
// Define our composer path (if it exists)
// =======================================
if ( file_exists( dirname( dirname( __FILE__ ) ) . '/composer.phar' ) ) {
	define( 'COMPOSER_PATH', dirname( dirname( __FILE__ ) ) . '/composer.phar' );
}

// ===================
// Bootstrap WordPress
// ===================
$table_prefix  = 'wpdev7_';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
}
require_once( ABSPATH . 'wp-settings.php' );

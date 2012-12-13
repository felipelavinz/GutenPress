<?php
// ===================================================
// Load database info and local development parameters
// ===================================================
if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/local-config.php' );
} else {
	define( 'WP_LOCAL_DEV', false );
	define( 'DB_NAME', '%%DB_NAME%%' );
	define( 'DB_USER', '%%DB_USER%%' );
	define( 'DB_PASSWORD', '%%DB_PASSWORD%%' );
	define( 'DB_HOST', '%%DB_HOST%%' ); // Probably 'localhost'
}

// =====================================================================
// Custom Content Directory
// Define WP_CONTENT_URL on local-config.php if site's on a subdirectory
// =====================================================================
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
if ( ! defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content' );

// ================================================
// You almost certainly do not want to change these
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ==============================================================
// Salts, for security
// Grab these from: https://api.wordpress.org/secret-key/1.1/salt
// ==============================================================
define('AUTH_KEY',         '0XJ?IG.)3!q3M(wt__Eg9k03G[T&Y^=7qH~7yLN}W2#OkdD(h9(+(FdlN{qGi#Bo');
define('SECURE_AUTH_KEY',  ' E4.=#zXFFLmQ.{kA.(YRLyJ;QA`9N.u,ugR}0=cp5:`ZRm-IHDOF#6~*+#80bG;');
define('LOGGED_IN_KEY',    'y+|p5E:7p(+((wpn/|UmyDi1Z[]wUVW~!L!E<KMA-N*[.HA 9Ftj)^F#h~zc*FVZ');
define('NONCE_KEY',        '4(!}2FwX4ub,oMmN+9#QvA~1MlX-oQ2aT^DWY<tG9]$ SaUQ)%HH=QCBmm?J>+ai');
define('AUTH_SALT',        'D3S,;/Jd?TLkqW6ce/h+>ho7@olew`hXn9^R(VPfh}tJ|KEw<E4#$&gn1=k|Cs5-');
define('SECURE_AUTH_SALT', '[1*KTpr/|pky-|2ak@gV(eu<Bg9;/rV-`(suTx:TKPR{~+B`&y6L~}!)V||~NKx]');
define('LOGGED_IN_SALT',   'b9Z#0]upYTCIG&+KXhieaQhmnl4boVxM1dR@;,J=/{JX^Ec(e=OHEw4t@g^uyt-)');
define('NONCE_SALT',       '{|p4i~!|h;uL;ini3>b7L /I_U-{Q.uGJ)/W|?`Hx!w!ws}rp%qma>A6W4SU<p]t');
// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================
$table_prefix  = 'wp_';

// ===========
// Hide errors
// ===========
ini_set( 'display_errors', 0 );
define( 'WP_DEBUG_DISPLAY', false );

// =================================================================
// Debug mode
// Debugging? Enable these. Can also enable them in local-config.php
// =================================================================
// define( 'SAVEQUERIES', true );
// define( 'WP_DEBUG', true );

// ======================================
// Load a Memcached config if we have one
// ======================================
if ( file_exists( dirname( __FILE__ ) . '/memcached.php' ) )
	$memcached_servers = include( dirname( __FILE__ ) . '/memcached.php' );

// ===========================================================================================
// This can be used to programatically set the stage when deploying (e.g. production, staging)
// ===========================================================================================
define( 'WP_STAGE', '%%WP_STAGE%%' );
define( 'STAGING_DOMAIN', '%%WP_STAGING_DOMAIN%%' ); // Does magic in WP Stack to handle staging domain rewriting

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
require_once( ABSPATH . 'wp-settings.php' );

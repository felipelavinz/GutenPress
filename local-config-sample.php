<?php
/*
This is a sample local-config.php file
In it, you *must* include the four main database defines

You may include other settings here that you only want enabled on your local development checkouts
*/

define( 'DB_NAME', 'local-db-name' );
define( 'DB_USER', 'local-db-user' );
define( 'DB_PASSWORD', 'local-db-password' );
define( 'DB_HOST', 'localhost' ); // Probably 'localhost'

// ==================================================
// Log Errors
// Should be turned on for development
// Also, super-useful when using the Debug Bar plugin
// ==================================================
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

// ================================
// Language
// Leave blank for American English
// ================================
// define( 'WPLANG', '' );

// ======================================================
// Custom Content Directory
// Define accordingly if your site it's on a subdirectory
// ======================================================
// define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/{path-to}/content' );
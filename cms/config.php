<?php
ini_set( "display_errors", true );
date_default_timezone_set( "Europe/Moscow" );
define( "MYSQL_SERVER", "localhost" );
define( "MYSQL_USER", "root" );
define( "MYSQL_PASSWORD", "rootroot" );
define( "MYSQL_DB", "lab4" );
define( "ADMIN_USERNAME", "admin" );
define( "ADMIN_PASSWORD", "admin" );
require( CLASS_PATH . "/Article.php" );
function handleException( $exception ) {
  echo "Sorry, a problem occurred. Please try later.";
  error_log( $exception->getMessage() );
}
set_exception_handler( 'handleException' );
?>

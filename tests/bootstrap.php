<?php

/**
 * PHPUnit bootstrap file
 *
 * @package OWC_SC
 */

//$_tests_dir = getenv( 'WP_TESTS_DIR' );
//if ( ! $_tests_dir ) {
//	$_tests_dir = '/tmp/wordpress-tests-lib';
//}
//
//// Give access to tests_add_filter() function.
//require_once $_tests_dir . '/includes/functions.php';
//
//// Start up the WP testing environment.
//require $_tests_dir . '/includes/bootstrap.php';

/**
 * Load dependencies with Composer autoloader.
 */
require __DIR__ . '/../vendor/autoload.php';

/**
 * Bootstrap WordPress Mock.
 */
\WP_Mock::setUsePatchwork( true );
\WP_Mock::bootstrap();

$GLOBALS['samenwerkende-catalogi'] = array(
	'active_plugins' => array( 'samenwerkende-catalogi/samenwerkende-catalogi.php' ),
);

class WP_CLI {
	public static function add_command() {}
}

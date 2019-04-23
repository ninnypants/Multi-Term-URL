<?php
/**
 * PHPUnit bootstrap for multi-term-url.
 */

namespace Plugin\MultiTermUrl\Tests;

// See https://github.com/petenelson/wp-unit-tests for examples on setting up local unit tests.

/**
 * Bootstrap for all unit tests.
 *
 * @throws Exception For any failures.
 * @return void
 */
function bootstrap() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	$wp_develop_dir = getenv( 'WP_DEVELOP_DIR' );

	if ( empty( $wp_develop_dir ) ) {
		throw new \Exception(
			'ERROR' . PHP_EOL . PHP_EOL .
			'You must define the WP_DEVELOP_DIR environment variable.' . PHP_EOL
		);
	}

	// Load the Composer autoloader.
	$plugin_root = dirname( dirname( __FILE__ ) );
	if ( ! file_exists( $plugin_root . '/vendor/autoload.php' ) ) {
		throw new \Exception(
			'ERROR' . PHP_EOL . PHP_EOL .
			'You must use Composer to install the test suite\'s dependencies.' . PHP_EOL
		);
	}
	require_once $plugin_root . '/vendor/autoload.php';

	// Give access to tests_add_filter() function.
	require_once $wp_develop_dir . '/tests/phpunit/includes/functions.php';

	tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\manually_load_plugins' );

	// Start up the WP testing environment.
	require $wp_develop_dir . '/tests/phpunit/includes/bootstrap.php';
	require_once $plugin_root . '/tests/phpunit/MultiTerm_Url_Tests.php';
}

/**
 * Manuall loads plugins.
 *
 * @return void
 */
function manually_load_plugins() {
	$plugin_root = dirname( dirname( __FILE__ ) );

	$files = [
		'multiterm-url.php',
	];

	foreach ( $files as $file ) {
		require_once $plugin_root . '/' . $file;
	}
}

// Now bootstrap the unit tests.
bootstrap();

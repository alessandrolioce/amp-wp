<?php
/**
 * Class OptionCommand.
 *
 * Commands that deal with the AMP options.
 *
 * @package AmpProject\AmpWP
 */

namespace AmpProject\AmpWP\Cli;

use WP_CLI;
use AmpProject\AmpWP\Option;
use AmpProject\AmpWP\Admin\ReaderThemes;
use AmpProject\AmpWP\Infrastructure\Service;
use AmpProject\AmpWP\Infrastructure\CliCommand;

/**
 * Retrieves and sets AMP plugin options.
 *
 * ## EXAMPLES
 *
 * # Get AMP plugin option.
 * $ wp amp option get theme_support
 * standard
 *
 * # Update AMP plugin option.
 * $ wp amp option update theme_support reader
 * Success: Updated theme_support option.
 *
 * @since 2.4.0
 */
final class OptionCommand implements Service, CliCommand {

	/**
	 * Options endpoint.
	 *
	 * @var string
	 */
	const OPTIONS_ENDPOINT = '/amp/v1/options';

	/**
	 * Allowed options to be managed via the CLI.
	 *
	 * @var string[]
	 */
	const ALLOWED_OPTIONS = [
		Option::READER_THEME,
		Option::THEME_SUPPORT,
		Option::MOBILE_REDIRECT,
	];

	/**
	 * Reader themes key.
	 *
	 * @var string
	 */
	const READER_THEMES = 'reader_themes';

	/**
	 * ReaderThemes instance.
	 *
	 * @var ReaderThemes
	 */
	private $reader_themes;

	/**
	 * Get the name under which to register the CLI command.
	 *
	 * @return string The name under which to register the CLI command.
	 */
	public static function get_command_name() {
		return 'amp option';
	}

	/**
	 * OptionCommand constructor.
	 *
	 * @param ReaderThemes $reader_themes ReaderThemes instance.
	 */
	public function __construct( ReaderThemes $reader_themes ) {
		$this->reader_themes = $reader_themes;
	}

	/**
	 * Gets the value for an option.
	 *
	 * ## OPTIONS
	 *
	 * <key>
	 * : Key for the option.
	 *
	 * [--format=<format>]
	 * : Get value in a particular format.
	 * ---
	 * default: var_export
	 * options:
	 *   - var_export
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * # Get option.
	 * $ wp amp option get theme_support
	 * standard
	 *
	 * # Get option in JSON format.
	 * $ wp amp option get theme_support --format=json
	 *
	 * @param array $args       Array of positional arguments.
	 * @param array $assoc_args Associative array of associative arguments.
	 */
	public function get( $args, $assoc_args ) {
		list( $option_name ) = $args;

		$this->check_user();

		$options = $this->get_options();

		if ( ! isset( $options[ $option_name ] ) ) {
			/* translators: %s: option name */
			WP_CLI::error( sprintf( __( 'Could not get %s option. Does it exist?', 'amp' ), $option_name ) );
		}

		WP_CLI::print_value( $options[ $option_name ], $assoc_args );
	}

	/**
	 * Updates an option value.
	 *
	 * ## OPTIONS
	 *
	 * <key>
	 * : The name of the option to update.
	 *
	 * [<value>]
	 * : The new value.
	 *
	 * ## EXAMPLES
	 *
	 * # Update plugin option.
	 * $ wp amp option update theme_support reader
	 * Success: Updated theme_support option.
	 *
	 * @alias set
	 *
	 * @param array $args       Array of positional arguments.
	 * @param array $assoc_args Associative array of associative arguments.
	 */
	public function update( $args, $assoc_args ) {
		list( $option_name, $option_value ) = $args;

		if ( ! in_array( $option_name, self::ALLOWED_OPTIONS, true ) ) {
			/* translators: %s: option name */
			WP_CLI::error( sprintf( __( 'You are not allowed to update %s option via the CLI.', 'amp' ), $option_name ) );
		}

		$this->check_user();

		$options = $this->get_options();

		if ( ! isset( $options[ $option_name ] ) ) {
			/* translators: %s: option name */
			WP_CLI::error( sprintf( __( 'Could not update %s option. Does it exist?', 'amp' ), $option_name ) );
		}

		$this->update_option( $option_name, $option_value );
	}

	/**
	 * List AMP options.
	 *
	 * @subcommand list
	 */
	public function list_() {
		$this->check_user();

		$options = $this->get_options();

		// Add reader themes to the options.
		$options[ self::READER_THEMES ] = wp_list_pluck( $this->reader_themes->get_themes(), 'slug' );

		WP_CLI::line( WP_CLI::colorize( '%y' . __( 'Available options:', 'amp' ) . '%n' ) );
		WP_CLI\Utils\format_items(
			'table',
			array_map(
				static function ( $option_name, $option_value ) {
					return compact( 'option_name', 'option_value' );
				},
				array_keys( $options ),
				$options
			),
			[ 'option_name', 'option_value' ]
		);

		WP_CLI::line( '' ); // Add a line break for readability.
		WP_CLI::line( WP_CLI::colorize( '%y' . __( 'Allowed options to be managed via CLI:', 'amp' ) . '%n' ) );
		WP_CLI\Utils\format_items(
			'table',
			array_map(
				static function ( $option_name ) {
					return compact( 'option_name' );
				},
				self::ALLOWED_OPTIONS
			),
			[ 'option_name' ]
		);
	}

	/**
	 * Get the options.
	 *
	 * @return array Options.
	 */
	private function get_options() {
		$response = $this->do_request( 'GET', self::OPTIONS_ENDPOINT, [] );

		if ( $response->as_error() ) {
			/* translators: %s: option name */
			WP_CLI::error( sprintf( __( 'Could not get options: %s', 'amp' ), $response->as_error()->get_error_message() ) );
		}

		return $response->get_data();
	}

	/**
	 * Update an option.
	 *
	 * @param string $option_name  Option name.
	 * @param string $option_value Option value.
	 */
	private function update_option( $option_name, $option_value ) {
		$response = $this->do_request(
			'POST',
			self::OPTIONS_ENDPOINT,
			[
				$option_name => $option_value,
			]
		);

		if ( $response->as_error() ) {
			/* translators: %1$s: option name, %2$s: error message */
			WP_CLI::error( sprintf( __( 'Could not update %1$s option: %2$s', 'amp' ), $option_name, $response->as_error()->get_error_message() ) );
		}

		/* translators: %s: option name */
		WP_CLI::success( sprintf( __( 'Updated %s option.', 'amp' ), $option_name ) );
	}

	/**
	 * Check if the user is set up to use the REST API.
	 *
	 * @return bool Whether the user is set up to use the REST API.
	 */
	private function check_user() {
		if ( ! current_user_can( 'manage_options' ) ) {
			WP_CLI::error( __( 'Sorry, you are not allowed to manage options for this site.', 'amp' ), false );
			WP_CLI::line( WP_CLI::colorize( '%y' . __( 'Try using --user=<id|login|email> to set the user context or set it in wp-cli.yml.', 'amp' ) . '%n' ) );
			WP_CLI::halt( 1 );
		}

		return true;
	}

	/**
	 * Do a REST Request
	 *
	 * @param string $method HTTP method.
	 * @param string $route REST route.
	 * @param array  $assoc_args Associative args.
	 *
	 * @return \WP_REST_Response Response object.
	 */
	private function do_request( $method, $route, $assoc_args ) {
		if ( ! defined( 'REST_REQUEST' ) ) {
			define( 'REST_REQUEST', true );
		}

		$request = new \WP_REST_Request( $method, $route );

		if ( in_array( $method, [ 'POST', 'PUT' ] ) ) {
			$request->set_body_params( $assoc_args );
		} else {
			foreach ( $assoc_args as $key => $value ) {
				$request->set_param( $key, $value );
			}
		}

		return rest_do_request( $request );
	}
}

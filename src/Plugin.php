<?php
/**
 * Manages the hook on actions and filters of the plugin.
 *
 * To remove an action or filter added by this class use:
 *
 * ```php
 * <?php
 * remove_action( 'some_action', [ tribe('event-call-links'), 'on_some_action' ] );
 * remove_filter( 'some_value', [ tribe('event-call-links'), 'filter_some_value' ] );
 * ```
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\EventCallLinks
 */

namespace Tribe\Extensions\EventCallLinks;

/**
 * Class Plugin
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\EventCallLinks
 */
class Plugin {

	/**
	 * Plugin constructor.
	 *
	 * @since TBD
	 */
	public static function instance() {
		if ( ! tribe()->isBound( static::class ) ) {
			$instance = new static();
			$instance->register();
		}

		return tribe( static::class );
	}

	/**
	 * Loads the plugin required files and registers the bindings in the `tribe` service locator.
	 *
	 * @since TBD
	 */
	public function register() {
		tribe_singleton( static::class, $this );
		tribe_singleton( 'event-call-links', $this );

		add_filter( 'tribe_addons_tab_fields', [ $this, 'filter_addons_tab_fields' ] );
	}

	/**
	 * Filters the fields in the Events > Settings > APIs tab to add the ones provided by the extension.
	 *
	 * @since TBD
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function filter_addons_tab_fields( $fields ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}

		// Require Zoom class files.
		require_once __DIR__ . '/Zoom/Settings.php';
		require_once __DIR__ . '/Zoom/Api.php';
		require_once __DIR__ . '/Zoom/Url.php';

		// Hook the Zoom settings section.
		return tribe( Zoom\Settings::class )->add_fields( $fields );
	}
}

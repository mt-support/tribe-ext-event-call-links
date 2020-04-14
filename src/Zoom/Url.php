<?php
/**
 * Manages the Zoom URLs for the plugin.
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\EventCallLinks\Zoom
 */

namespace Tribe\Extensions\EventCallLinks\Zoom;

/**
 * Class Url
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\EventCallLinks\Zoom
 */
class Url {

	/**
	 * Returns the URL to disconnect from the Zoom API.
	 *
	 * @since TBD
	 *
	 * @param string $current_url The URL to return to after a successful disconnection.
	 *
	 * @return string The URL to disconnect from the Zoom API.
	 */
	public function to_disconnect( $current_url = null ) {
		return '';
	}

	/**
	 * Returns the URL to authorize the use of the Zoom API.
	 *
	 * @since TBD
	 *
	 * @param array<string,string> $query_args A list of query vars that should be appended to the request URL.
	 *
	 * @return string The request URL.
	 */
	public function to_authorize( array $query_args = [] ) {
		return '';
	}

	public function get_authorized_url() {
		return '';
	}
}

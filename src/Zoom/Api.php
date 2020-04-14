<?php
/**
 * Handles the interaction w/ Zoom API.
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\EventCallLinks\Zoom
 */

namespace Tribe\Extensions\EventCallLinks\Zoom;

/**
 * Class Api
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\EventCallLinks\Zoom
 */
class Api {
	/**
	 * Checks whether the minimum set of fields and values required to interact w/ the Zoom API are set or not.
	 *
	 * @since TBD
	 *
	 * @return bool Whether the minimum set of fields and values required to interact w/ the Zoom API are set or not.
	 */
	public function has_required_fields() {
	}

	public function is_authorized() {
		return false;
	}
}

<?php
/**
 * Plugin Name:       The Events Calendar Extension: Events Call Links
 * Plugin URI:        https://theeventscalendar.com/extensions/event-call-links/
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-event-call-links
 * Description:       Automatically generate conference call meeting links for your events.
 * Version:           1.0.0
 * Extension Class:   \Tribe\Extensions\EventCallLinks\Plugin
 * Author:            Modern Tribe, Inc.
 * Author URI:        http://m.tri.be/1971
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tribe-ext-event-call-links
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

namespace Tribe\Extensions\EventCallLinks;

// Do not load unless Tribe Common is fully loaded and our class does not yet exist.
if ( ! class_exists( 'Tribe__Extension' ) ) {
	return;
}

require_once __DIR__ . '/src/Plugin.php';

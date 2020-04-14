<?php
/**
 * Manages the Zoom settings for the extension.
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\EventCallLinks\Zoom
 */

namespace Tribe\Extensions\EventCallLinks\Zoom;

/**
 * Class Settings
 *
 * @since   TBD
 *
 * @package Tribe\Extensions\EventCallLinks\Zoom
 */
class Settings {
	/**
	 * The prefix, in the context of tribe options, of each setting for this extension.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $option_prefix = 'tribe_zooom_';

	/**
	 * The URL handler instance.
	 *
	 * @since TBD
	 *
	 * @var Url
	 */
	protected $url;

	/**
	 * An instance of the Zoom API handler.
	 *
	 * @since TBD
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * Settings constructor.
	 *
	 * @since TBD
	 *
	 * @param Url $url An instance of the URL handler.
	 */
	public function __construct( Api $api, Url $url ) {
		$this->url = $url;
		$this->api = $api;
	}

	/**
	 * Adds the Zoom API ones to the fields in the Events > Settings > APIs tab.
	 *
	 * @since TBD
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function add_fields( array $fields = [] ) {
		$zoom_fields = [
			static::$option_prefix . 'zoom_header'    => [
				'type' => 'html',
				'html' => $this->get_intro_text(),
			],
			static::$option_prefix . 'zoom_authorize' => [
				'type' => 'html',
				'html' => $this->get_authorize_fields(),
			],
			static::$option_prefix . 'app_id'         => [
				'type'            => 'text',
				'label'           => esc_html__( 'APP ID', 'tribe-ext-event-call-links' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the App ID from the application created in Zoom',
					'tribe-ext-event-call-links' ) ),
				'validation_type' => 'html',
			],
			static::$option_prefix . 'client_id'      => [
				'type'            => 'text',
				'label'           => esc_html__( 'Client ID', 'tribe-ext-event-call-links' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the Client ID from the application created in Zoom',
					'tribe-ext-event-call-links' ) ),
				'validation_type' => 'html',
			],
			static::$option_prefix . 'client_secret'  => [
				'type'            => 'text',
				'label'           => esc_html__( 'Client Secret', 'tribe-ext-event-call-links' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the Client Secret from the application created in Zoom',
					'tribe-ext-event-call-links' ) ),
				'validation_type' => 'html',
			],
			static::$option_prefix . 'user_id'        => [
				'type'            => 'text',
				'label'           => esc_html__( 'User ID', 'tribe-ext-event-call-links' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the Developer Account User ID for Zoom',
					'tribe-ext-event-call-links' ) ),
				'validation_type' => 'html',
			],
			static::$option_prefix . 'api_key'        => [
				'type'            => 'text',
				'label'           => esc_html__( 'API Key', 'tribe-ext-event-call-links' ),
				'tooltip'         => sprintf( esc_html__( 'Enter the Developer Account API Key from the Developer Account in Zoom',
					'tribe-ext-event-call-links' ) ),
				'validation_type' => 'html',
			]
		];

		// Insert the link after the other APIs and before the Google Maps API ones.
		$gmaps_fields = array_splice( $fields, array_search( 'gmaps-js-api-start', array_keys( $fields ) ) );

		$fields = array_merge( $fields, $zoom_fields, $gmaps_fields );

		return $fields;
	}

	/**
	 * Provides the introductory text to the set up and configuration of the Zoom API integration.
	 *
	 * @since TBD
	 *
	 * @return string The introductory text to the the set up and configuration of the Zoom API integration.
	 */
	protected function get_intro_text() {
		$guide = sprintf( '%s <a href="%s" target="_blank">%s</a> %s',
			esc_html_x( 'Review our', 'Intro text to the setup guide link.', 'tribe-ext-event-call-links' ),
			esc_url( 'http://m.tri.be/ext-event-call-links-kb' ),
			esc_html_x( 'setup guide', 'Link text to the setup guide.', 'tribe-ext-event-call-links' ),
			esc_html_x( 'to help you get started.', 'Ending text to the setup guide link.',
				'tribe-ext-event-call-links' )
		);

		ob_start();
		?>
		<h3 id="tribe-zoom-application-credientials">
			<?php echo esc_html_x( 'Zoom', 'API connection header', 'tribe-ext-event-call-links' ) ?>
		</h3>
		<div style="margin-left: 20px;">
			<p>
				<?php echo esc_html_x( 'You need to connect to your Zoom account to be able to create Zoom meeting links.',
					'Settings Description', 'tribe-ext-event-call-links' ); ?>
			</p>
			<p>
				<?php echo $guide; ?>
			</p>
		</div>
		<?php

		echo $this->get_status_table();

		return ob_get_clean();
	}

	/**
	 * Returns the Zoom API integration status, in the form of a human-readable table.
	 *
	 * @since TBD
	 *
	 * @return string The current Zoom API connection, in table format.
	 */
	protected function get_status_table() {
		$options         = $this->get_all_options();
		$indicator_icons = [
			'good'    => 'marker',
			'warning' => 'warning',
			'bad'     => 'dismiss',
		];

		ob_start();
		?>
		<table class="zoom-status event-aggregator-status">
			<thead>
			<tr class="table-heading">
				<th colspan="4"><?php esc_html_e( 'Zoom Services', 'tribe-ext-event-call-links' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php

			// Connection Status ( checks for access token, refresh token, and expires site options )
			$indicator = 'warning';
			$notes     = '&nbsp;';
			$label     = _x( 'Zoom Connection', 'Status label for Zoom API connection.',
				'tribe-ext-event-call-links' );
			$text      = _x( 'Not connected.', 'Status for Zoom API connection.', 'tribe-ext-event-call-links' );

			if ( ! empty( $options['access_token'] ) && ! empty( $options['refresh_token'] ) && ! empty( $options['access_token'] ) ) {
				$indicator = 'good';
				$text      = _x( 'Connected!', 'Status for Zoom API connection.', 'tribe-ext-event-call-links' );
			}

			?>
			<tr>
				<td class="label"><?php echo esc_html( $label ); ?></td>
				<td class="indicator <?php echo esc_attr( $indicator ); ?>">
					<span class="dashicons dashicons-<?php echo esc_attr( $indicator_icons[ $indicator ] ); ?>"></span>
				</td>
				<td><?php echo esc_html( $text ); ?></td>
				<td><?php echo esc_html( $notes ); ?></td>
			</tr>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}

	/**
	 * Returns the tribe options for the Zoom integration with the prefix removed from the keys.
	 *
	 * @since TBD
	 *
	 * @return array<string,mixed> A map of the Zoom API integration options; keys do not include the prefix.
	 */
	protected function get_all_options() {
		$raw_options = $this->get_all_raw_options();

		$result = [];

		foreach ( $raw_options as $key => $value ) {
			$abbr_key            = str_replace( static::$option_prefix, '', $key );
			$result[ $abbr_key ] = $value;
		}

		return $result;
	}

	/**
	 * Returns the tribe options for the Zoom integration with full sub-option name.
	 *
	 * @since TBD
	 *
	 * @return array<string,mixed> A map of the Zoom API integration options; keys include the prefix.
	 */
	public function get_all_raw_options() {
		$tribe_options = \Tribe__Settings_Manager::get_options();

		if ( ! is_array( $tribe_options ) ) {
			return [];
		}

		$result = [];

		foreach ( $tribe_options as $key => $value ) {
			if ( 0 === strpos( $key, static::$option_prefix ) ) {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	protected function get_authorize_fields() {
		if ( $this->api->has_required_fields() ) {
			return '';
		}

		// Zoom requires a SSL, check and display an error message if SSL not detected
		if ( ! is_ssl() ) {
			ob_start();
			?>
			<fieldset id="tribe-field-zoom_token" class="tribe-field tribe-field-text tribe-size-medium">
				<legend class="tribe-field-label"><?php esc_html_e( 'Zoom Connection',
						'tribe-ext-event-call-links' ) ?></legend>
				<div class="tribe-field-wrap tribe-error">
					<?php esc_html_e( 'An SSL is required to connect to Zoom, please enable it on your site.',
						'tribe-ext-event-call-links' ) ?>
				</div>
			</fieldset>
			<div class="clear"></div>
			<?php

			return ob_get_clean();
		}

		$missing_credentials = ! $this->api->is_authorized();

		ob_start();

		?>
		<fieldset id="tribe-field-zoom_token" class="tribe-field tribe-field-text tribe-size-medium">
			<legend class="tribe-field-label"><?php esc_html_e( 'Zoom Connection',
					'tribe-ext-event-call-links' ) ?></legend>
			<div class="tribe-field-wrap">
				<?php
				$authorize_link = $this->url->get_authorized_url();

				if ( $missing_credentials ) {
					echo '<p>' . esc_html__( 'You need to connect to Zoom.', 'tribe-ext-event-call-links' ) . '</p>';
					$connect_label = __( 'Connect to Zoom', 'tribe-ext-event-call-links' );
				} else {
					$connect_label    = __( 'Refresh your connection to Zoom', 'tribe-ext-event-call-links' );
					$disconnect_label = __( 'Disconnect', 'tribe-ext-event-call-links' );
					$current_url      = \Tribe__Settings::instance()->get_url( [ 'tab' => 'addons' ] );
					$disconnect_url   = $this->url->to_disconnect( $current_url );
				}
				?>
				<a target="_blank" class="tribe-button tribe-zoom-button"
				   href="<?php echo esc_url( $authorize_link ); ?>"><?php esc_html_e( $connect_label ); ?></a>
				<?php if ( ! $missing_credentials ) : ?>
					<a href="<?php echo esc_url( $disconnect_url ); ?>"
					   class="tribe-zoom-disconnect"><?php echo esc_html( $disconnect_label ); ?></a>
				<?php endif; ?>
			</div>
		</fieldset>

		<!-- Uses style guide colors https://www.zoom.com/style-guide -->
		<style>
			.tribe-zoom-button {
				background: #00A4BD;
				border-radius: 3px;
				color: #fff;
				display: inline-block;
				padding: .5rem 1.5rem;
				text-decoration: none;
				-webkit-transition: all 0.5s ease;
				transition: all 0.5s ease;
			}

			.tribe-zoom-button:active,
			.tribe-zoom-button:hover,
			.tribe-zoom-button:focus {
				background: #FF7A59;
				color: #253342;
			}
		</style>
		<div class="clear"></div>
		<?php

		return ob_get_clean();
	}
}

<?php

/**
 * Fired during plugin uninstall
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's uninstall.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class If_So_Uninstall {

	/**
	 * Cleanup upon uninstall of the plugin
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-if-so-license.php';

		// retrieve our license key & item name from the DB
		$license = get_option('edd_ifso_license_key');
		$item_name = get_option('edd_ifso_license_item_name');

		if ($license !== false &&
			$item_name !== false) {
			$license = trim( $license );

			// Deactivate the license
			If_So_License::deactivate_license($license, $item_name);
		}

		// Remove all the options related to If-So
		delete_option('edd_ifso_license_key');
		delete_option('edd_ifso_license_item_name');
		delete_option('edd_ifso_license_status');

		// Remove all transients in use
		delete_transient('ifso_transient_license_validation');
	}

}

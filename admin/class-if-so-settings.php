<?php

/**
 * The settings of the plugin.
 *
 * @link       http://devinvinson.com
 * @since      1.0.0
 *
 * @package    Wppb_Demo_Plugin
 * @subpackage Wppb_Demo_Plugin/admin
 */

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class If_So_Admin_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	public $triggers_obj;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plans = array(
				"If>So Dynamic WordPress Content – Lifetime License",
				"If>So Dynamic WordPress Content – Yearly Subscription",
				"If>So Dynamic WordPress Content – Monthly Subscription",
				"Free Trial"
			);
	}
	
	public function plugin_settings_page(){
		return false; // main display will be overriden by a custom post type
	}
	
	public function ifso_trigger_settings_metabox( $post ){
		require_once('partials/ifso_trigger_settings_metabox.php');
		return false;
	}
	
	public function ifso_shortcode_display_metabox( $post ){
		require_once('partials/ifso_shortcode_display_metabox.php');
		return false;
	}

	// The page for license activation
	public function display_admin_menu_license( $post ){
		$license = get_option( 'edd_ifso_license_key' );
		$status  = get_option( 'edd_ifso_license_status' );
		$expires = get_option( 'edd_ifso_license_expires' );
		//die("A".$license."AA");
		?>
		<div class="wrap">
			<h2><?php _e('If>So License'); ?></h2>
			<form method="post" action="options.php">

				<?php settings_fields('edd_ifso_license'); ?>

				<?php if (!( $status !== false && $status == 'valid' )): ?>
				<div class="no_license_message">Enter your license key to enable all features. If you do not have license key you can <a href="https://goo.gl/Y3FzAu" target="_blank">get one for free here</a>.</div>
				<?php endif; ?>

				<table class="form-table license-tbl">
					<tbody>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('License Key'); ?>
							</th>
							<td>
								<input id="edd_ifso_license_key" name="edd_ifso_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
								<label class="description" for="edd_ifso_license_key"><?php _e('Enter your license key'); ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active'); ?></span>
									<?php wp_nonce_field( 'edd_ifso_nonce', 'edd_ifso_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
								<?php } else {
									wp_nonce_field( 'edd_ifso_nonce', 'edd_ifso_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_ifso_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>

				<!-- License key expiratiaton date -->
				<?php if ( $status !== false && $status == 'valid' && $expires !== false ): ?>
				<div class="license_expires_message">Your license key expires on <span class="expire_date"><?php _e(date_i18n( get_option( 'date_format' ), strtotime( $expires, current_time( 'timestamp' ) ) )); ?>.</span></div>
				<?php endif; ?>

				<?php //submit_button(); ?>

			</form>
		</div>
		<?php
	}

	/*
	 *	this function registers 'edd_ifso_license_key' option
	 *	registered via 'admin_init'
	 */
	public function edd_ifso_register_option() {
		// creates our settings in the options table
		//register_setting('edd_ifso_license', 'edd_ifso_license_key', 'edd_ifso_sanitize_license' );
	}

	/*
	 * sanitizes the value of the option 'edd_ifso_license_key'
	 */
	public function edd_ifso_sanitize_license( $new ) {
		$old = get_option( 'edd_ifso_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'edd_ifso_license_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	// Helper function that returns the proper messages to the client
	// according to the result received from the API (resides in $license_data)
	public function edd_api_get_error_message($license_data) {
		$message = false;

		if ( false === $license_data->success ) {

			switch( $license_data->error ) {

				case 'expired' :

					$message = sprintf(
						__( 'Your license key expired on %s.' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked' :

					$message = __( 'Your license key has been disabled.' );
					break;

				case 'missing' :

					$message = __( 'Invalid license.' );
					break;

				case 'invalid' :
				case 'site_inactive' :

					$message = __( 'Your license is not active for this URL.' );
					break;

				case 'item_name_mismatch' :

					$message = __( 'This appears to be an invalid license key for the selected item.' );
					break;

				case 'no_activations_left':

					$message = __( 'Your license key has reached its activation limit.' );
					break;

				default :

					$message = __( 'An error occurred, please try again.' );
					break;
			}
		}

		return $message;
	}

	private function _query_ifso_api($edd_action, $license, $item_name) {
			// data to send in our API request
			$api_params = array(
				'edd_action' => $edd_action, //'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( $item_name ), // the name of our product in EDD
				'url'        => home_url()
			);

			$message = false;
			$license_data = false;

			// Call the custom API.
			$response = wp_remote_post( EDD_IFSO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			}

			if (!$license_data) return $message;
			return $license_data;
	}

	private function edd_api_activate_item($license, $item_name) {

			return $this->_query_ifso_api('activate_license', $license, $item_name);

			// Check if anything passed on a message constituting a failure
			// if ( ! empty( $message ) && !$isPresentedInTheAPI ) {
			// 	$base_url = admin_url( 'admin.php?page=' . EDD_IFSO_PLUGIN_LICENSE_PAGE );
			// 	$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			// 	wp_redirect( $redirect );
			// 	exit();
			// }
	}

	private function edd_api_deactivate_item($license, $item_name) {
		return $this->_query_ifso_api('deactivate_license', $license, $item_name);
	}

	/*
	 *	Runs when the user clicks on "Activate License" button
	 *	registered via 'admin_init'
	 */
	public function edd_ifso_activate_license() {
		// listen for our activate button to be clicked
		if( isset( $_POST['edd_ifso_license_activate'] ) ) {
			// run a quick security check
		 	if( ! check_admin_referer( 'edd_ifso_nonce', 'edd_ifso_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			$db_license = trim( get_option('edd_ifso_license_key') );
			$license = trim( $_POST["edd_ifso_license_key"] );

			if ($db_license != $license)
				delete_option('edd_ifso_license_status');

			// save the license in the database
			update_option('edd_ifso_license_key', $license);

			//die($license);

			// data to send in our API request
			// $api_params = array(
			// 	'edd_action' => 'activate_license',
			// 	'license'    => $license,
			// 	'item_name'  => urlencode( $plan ), // the name of our product in EDD
			// 	'url'        => home_url()
			// );

			// // Call the custom API.
			// $response = wp_remote_post( EDD_IFSO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// // make sure the response came back okay
			// if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			// 	if ( is_wp_error( $response ) ) {
			// 		$message = $response->get_error_message();
			// 	} else {
			// 		$message = __( 'An error occurred, please try again.' );
			// 	}

			// } else {

			// 	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// 	if ( false === $license_data->success ) {

			// 		switch( $license_data->error ) {

			// 			case 'expired' :

			// 				$message = sprintf(
			// 					__( 'Your license key expired on %s.' ),
			// 					date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
			// 				);
			// 				break;

			// 			case 'revoked' :

			// 				$message = __( 'Your license key has been disabled.' );
			// 				break;

			// 			case 'missing' :

			// 				$message = __( 'Invalid license.' );
			// 				break;

			// 			case 'invalid' :
			// 			case 'site_inactive' :

			// 				$message = __( 'Your license is not active for this URL.' );
			// 				break;

			// 			case 'item_name_mismatch' :

			// 				$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $plan );
			// 				break;

			// 			case 'no_activations_left':

			// 				$message = __( 'Your license key has reached its activation limit.' );
			// 				break;

			// 			default :

			// 				$message = __( 'An error occurred, please try again.' );
			// 				break;
			// 		}

			// 	}

			// }

			// Check if anything passed on a message constituting a failure
			// if ( ! empty( $message ) ) {
			// 	$base_url = admin_url( 'admin.php?page=' . EDD_IFSO_PLUGIN_LICENSE_PAGE );
			// 	$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			// 	wp_redirect( $redirect );
			// 	exit();
			// }

			// Iterating over each plan and trying to activate it
			$last_plan = NULL;
			foreach ($this->plans as $key => $plan) {
				$last_plan = $plan;
				$license_data = $this->edd_api_activate_item($license, $plan);

				if ($license_data instanceof stdClass &&
					$license_data->error == 'item_name_mismatch') {
					// Ok, keep going. it's another item
					continue;
				}

				break;
			}

			if ($license_data instanceof stdClass)
				$message = $this->edd_api_get_error_message($license_data);
			else if ($license_data) // check for not false
				// license_data might be the message if something went wrong
				$message = $license_data;

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = admin_url( 'admin.php?page=' . EDD_IFSO_PLUGIN_LICENSE_PAGE );
				$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				wp_redirect( $redirect );
				exit();
			}

			// die($this->plans[$last_plan_indx]);
			update_option('edd_ifso_license_item_name', $last_plan);
			// $license_data->license will be either "valid" or "invalid"
			update_option('edd_ifso_license_expires', $license_data->expires);
			update_option( 'edd_ifso_license_status', $license_data->license );
			wp_redirect( admin_url( 'admin.php?page=' . EDD_IFSO_PLUGIN_LICENSE_PAGE ) );
			exit();
		}
	}

	/***********************************************
	* Illustrates how to deactivate a license key.
	* This will decrease the site count
	***********************************************/

	public function edd_ifso_deactivate_license() {

		// listen for our activate button to be clicked
		if( isset( $_POST['edd_license_deactivate'] ) ) {

			// run a quick security check
		 	if( ! check_admin_referer( 'edd_ifso_nonce', 'edd_ifso_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			//$license = trim( get_option( 'edd_ifso_license_key' ) );
			$license = trim( $_POST['edd_ifso_license_key'] );

			// // data to send in our API request
			// $api_params = array(
			// 	'edd_action' => 'deactivate_license',
			// 	'license'    => $license,
			// 	'item_name'  => urlencode( EDD_IFSO_ITEM_NAME ), // the name of our product in EDD
			// 	'url'        => home_url()
			// );

			// // Call the custom API.
			// $response = wp_remote_post( EDD_IFSO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// // make sure the response came back okay
			// if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			// 	if ( is_wp_error( $response ) ) {
			// 		$message = $response->get_error_message();
			// 	} else {
			// 		$message = __( 'An error occurred, please try again.' );
			// 	}

			// 	$base_url = admin_url( 'admin.php?page=' . EDD_IFSO_PLUGIN_LICENSE_PAGE );
			// 	$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			// 	wp_redirect( $redirect );
			// 	exit();
			// }

			// // decode the license data
			// $license_data = json_decode( wp_remote_retrieve_body( $response ) );

			foreach ($this->plans as $key => $plan) {
				$license_data = $this->edd_api_deactivate_item($license, $plan);

				if ($license_data instanceof stdClass &&
					!$license_data->success) 
				{
					continue;
				}

				// $license_data->license will be either "deactivated" or "failed"
				if( $license_data->license == 'deactivated' ) {
					delete_option( 'edd_ifso_license_status' );
					delete_option( 'edd_ifso_license_item_name' );
				}

				wp_redirect( admin_url( 'admin.php?page=' . EDD_IFSO_PLUGIN_LICENSE_PAGE ) );
				exit();
			}

			if (!($license_data instanceof stdClass))
				$message = $license_data;

			$base_url = admin_url( 'admin.php?page=' . EDD_IFSO_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}
	}

	/*
	 *	IfSo Edd Updater 
	 */
	public function edd_sl_ifso_plugin_updater() {
		// retrieve our license key & item name from the DB
		$license = trim( get_option('edd_ifso_license_key') );
		$item_name = get_option('edd_ifso_license_item_name');

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( EDD_IFSO_STORE_URL, __FILE__, array(
				'version' 	=> '1.0', 				// current version number
				'license' 	=> $license, 		// license key (used get_option above to retrieve from DB)
				'item_name' => $item_name, 	// name of this plugin
				'author' 	=> '',  // author of this plugin
				'beta'		=> false
			)
		);

	}

	/* Responsible to check if the license is still valid */
	public function edd_ifso_is_license_valid() {
		if (!get_transient('ifso_transient_license_validation') &&
			get_option( 'edd_ifso_license_key' ) !== false) {
			set_transient( 'ifso_transient_license_validation', true, 300);
			// retrieve our license key & item name from the DB
			$license = trim( get_option('edd_ifso_license_key') );
			$item_name = get_option('edd_ifso_license_item_name');
			// Check if the license is still valid
			if($this->edd_ifso_check_license($license, $item_name)) {
				// Valid!
				// Do nothing...
			} else {
				// Not valid!
				// Remove the appropriate stuff
				delete_option( 'edd_ifso_license_status' );
				delete_option( 'edd_ifso_license_item_name' );
			}
		}
	}

	private function edd_ifso_check_license($license, $item_name) {
		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $license,
			'item_name' => urlencode( $item_name ),
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_IFSO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// print_r($license_data);
		if( $license_data->license == 'valid' ) {
			return true;
			// echo 'valid'; exit;
			// this license is still valid
		} else {
			return false;
			// echo 'invalid'; exit;
			// this license is no longer valid
		}
	}

	/**
	 * This is a means of catching errors from the activation method above and displaying it to the customer
	 */
	public function edd_ifso_admin_notices() {
		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch( $_GET['sl_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo $message; ?></p>
					</div>
					<?php
					break;

				case 'true':
				default:
					// Developers can put a custom success message here for when activation is successful if they way.
					break;

			}
		}
	}

	/*
	 *	add plaugin menu items
	 */
	public function add_plugin_menu_items() {
		
		add_menu_page(
			__( 'If So', 'if-so' ), // The title to be displayed on this menu's corresponding page
			__( 'IfSo', 'if-so' ), // The text to be displayed for this actual menu item
			'manage_options', // Which type of users can see this menu
			'if-so', // The unique ID - that is, the slug - for this menu item
			array($this, 'plugin_settings_page'), // The name of the function to call when rendering this menu's page
			plugin_dir_url( __FILE__ ) . 'images/logo-256x256.png', // icon url
			90 // position
		);
		
		global $submenu;
		$permalink = admin_url( 'post-new.php' ).'?post_type=ifso_triggers';
		// $submenu['if-so'][] = array( __('Add New Trigger', 'if-so'), 'manage_options', $permalink );
		
		$saveAsideAllTriggers = $submenu['if-so'][0];

		$submenu['if-so'][0] = array( __('Add New Trigger', 'if-so'), 'manage_options', $permalink );
		$submenu['if-so'][] = $saveAsideAllTriggers;


		add_submenu_page(
			'if-so',
			'License',
			'License',
			'manage_options',
			'wpcdd_admin_menu_license',
			array( $this, 'display_admin_menu_license' )
		);

		/*add_submenu_page(
			'if-so',
			'Add New',
			'Add New',
			'manage_options',
			'edit.php?post_type=ifso_triggers',
			array($this, 'plugin_settings_page')
		);*/
		
		/*add_submenu_page(
			'if-so',
			'Instructions',
			'Instructions',
			'manage_options',
			'wpcdd_admin_menu_instruction',
			array( $this, 'display_admin_menu_instruction' )
		);*/
	}
	
	// Create custom column to display shortcode
	public function ifso_add_custom_column_title( $prev_columns ){
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'title'    => __( 'Title', 'if-so' ),
			'trigger' => __( 'Triggers', 'if-so' ),
			'shortcode' => __( 'Shortcode', 'if-so' )
		);
		
		// add custom columns except yoast seo to the end of the table
		foreach($prev_columns as $col_index => $col_title) {
			if(strpos($col_index, 'wpseo') !== false) continue;
			if(array_key_exists($col_index, $columns)) continue;
			$columns[$col_index] = $col_title;
		}
		
		// set date column at the end of the table
		$columns['date'] = __( 'Date', 'if-so' );
		
		return $columns;
	}
	
	public function ifso_add_custom_column_data( $column, $post_id ){
		switch( $column ){
			case 'trigger' :
				$data = array();
				$triggers = '';
				
				$data_json = get_post_meta( $post_id, 'ifso_trigger_rules', true );
				if(!empty($data_json)) $data = json_decode($data_json, true);
				if(empty($data)) return false;
				$triggers_array = array();
				$query_strings_used = array();
				foreach($data as $rule) {
					if($rule['trigger_type'] == 'url' && !empty($rule['compare'])) $query_strings_used[] = "{$rule['compare']}";
					else if(!in_array($rule['trigger_type'], $triggers_array)) $triggers_array[] = $rule['trigger_type'];
				}
				
				// add all query strings selected to the triggers array
				if(!empty($query_strings_used)) {
					$triggers_array[] = 'Custom URL (?ifso='.implode(', ', $query_strings_used).')';
				}
				
				if(!empty($triggers_array)) $triggers = implode('<br/>', $triggers_array);
				echo $triggers;
				break;
			case 'shortcode' :
				$shortcode = sprintf( '[ifso id="%1$d"]', $post_id);
				echo "<span class='shortcode'><input type='text' onfocus='this.select();' readonly='readonly' value='". $shortcode ."' class='large-text code'></span>";
				break;
		}
	}

	public function custom_triggers_template ($content) {
    	global $wp_query, $post;
    	return "HI";
	    /* Checks for single template by post type */
	    if ($post->post_type == "ifso_triggers"){
	    	die(PLUGIN_PATH);
	        if(file_exists(PLUGIN_PATH . '/Custom_File.php'))
	            return PLUGIN_PATH . '/Custom_File.php';
	    }

		return $single;
	}
	
	public function ifso_add_meta_boxes( $post_type ){
		
		add_filter( 'wpseo_metabox_prio', function() { return 'low'; } );
		
		add_meta_box(
			'ifso_triggers_metabox', 
			__('Trigger settings', 'if-so'), 
			array($this, 'ifso_trigger_settings_metabox'),
			'ifso_triggers',
			'normal',
			'high'
		);

		add_meta_box(
			'ifso_shortcode_display',
			__('Shortcode', 'if-so'),
			array( $this, 'ifso_shortcode_display_metabox' ),
			'ifso_triggers',
			'side',
			'default'
		);
		
		/*
		// in case that priority manipulation doesnt work
		function do_something_after_title() {
			$scr = get_current_screen();
			if ( ( $scr->base !== 'post' && $scr->base !== 'page' ) || $scr->action === 'add' )
				return;
			echo '<h2>After title only for post or page edit screen</h2>';
		}

		add_action( 'edit_form_after_title', 'do_something_after_title' );
		*/
	}
	
	public function move_yoast_metabox_down( $priority ){
		return 'low';
	}
	
	public function load_tinymce() {
		check_ajax_referer( 'my-nonce-string', 'nonce' );
		$editor_id = intval( $_POST['editor_id'] );
		
		wp_editor( '', 'repeatable_editor_content'.$editor_id, array(
			'wpautop'       => true,
			'textarea_name' => 'repeater['.$editor_id.'][repeatable_editor_content]',
			'textarea_class' => 'cloned-textarea',
			'textarea_rows' => 20,
		));
		wp_die();
	}
	
	public function ifso_save_post_type ( $post_id ){
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			die(__( 'You do not have sufficient previlliege to edit the post', 'if-so' ).'.');
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		// Prevent quick edit from clearing custom fields
		if (defined('DOING_AJAX') && DOING_AJAX) {
			return $post_id;
		}
		
		$trigger_data = array();
		//if(empty($_POST['default'])) return $post_id;
		$trigger_data['default'] = (!empty($_POST['ifso_default'])) ? $_POST['ifso_default'] : '';
		//echo "<pre>".print_r($_POST['repeater'], true)."</pre>";
		if(empty($_POST['repeater'])) return $post_id;
		
		// Counting the number of repeaters!
		$repeaters_counter = 1;

		foreach($_POST['repeater'] as $index => $group_item) {
			if(empty($group_item['trigger_type'])) continue;

			$repeaters_counter += 1;
		}

		// die("<h1>".strval($repeaters_counter)."</h1>");

		$testing_mode = (is_numeric($_POST['testing-mode']) &&
						  intval($_POST['testing-mode']) <= $repeaters_counter) ? $_POST['testing-mode'] : "";

		foreach($_POST['repeater'] as $index => $group_item) {
			
			if($index === 'index_placeholder') continue;
			// if(empty($group_item['trigger_type']) || empty($group_item['repeatable_editor_content'])) continue; // was removed in order to allow saving of an empty content
			if(empty($group_item['trigger_type'])) continue;
			
			$compare = '';
			if(!empty($group_item['compare_referrer'])) $compare = $group_item['compare_referrer'];
			if(!empty($group_item['compare_url'])) $compare = $group_item['compare_url'];
			
			/* Begin Sessions */

			$ab_testing_no_sessions = '';
			if(!empty($group_item['ab-testing-sessions']))
				$ab_testing_no_sessions = $group_item['ab-testing-sessions'];

			$ab_testing_custom_sessions = '';
			if (!empty($group_item['ab-testing-custom-no-sessions'])) 
				$ab_testing_custom_sessions = $group_item['ab-testing-custom-no-sessions'];

			/* End Sessions */

			/* Begin User Behavior */

			$user_behavior_loggedinout = '';
			if (!empty($group_item['user-behavior-loggedinout'])) 
				$user_behavior_loggedinout = $group_item['user-behavior-loggedinout'];

			$user_behavior_returning = '';
			if (!empty($group_item['user-behavior-returning'])) 
				$user_behavior_returning = $group_item['user-behavior-returning'];

			$user_behavior_retn_custom = '';
			if (!empty($group_item['user-behavior-retn-custom'])) 
				$user_behavior_retn_custom = $group_item['user-behavior-retn-custom'];

			$user_behavior_browser_language = '';
			if (!empty($group_item['user-behavior-browser-language'])) 
				$user_behavior_browser_language = $group_item['user-behavior-browser-language'];

			/* End User Behavior */

			$numberOfViews = 0;
			if (!empty($group_item['saved_number_of_views']))
				$numberOfViews = $group_item['saved_number_of_views'];

			$user_behavior_device_mobile = false;
			$user_behavior_device_tablet = false;
			$user_behavior_device_desktop = false;
			
			if ($group_item['user-behavior-device-mobile'] == "on")
				$user_behavior_device_mobile = true;

			if ($group_item['user-behavior-device-tablet'] == "on")
				$user_behavior_device_tablet = true;

			if ($group_item['user-behavior-device-desktop'] == "on")
				$user_behavior_device_desktop = true;

			$trigger_data['rules'][] = array(
				'trigger_type' => $group_item['trigger_type'],
				'AB-Testing' => $group_item['AB-Testing'],
				'User-Behavior' => $group_item['User-Behavior'],
				'user-behavior-loggedinout' => $user_behavior_loggedinout,
				'user-behavior-returning' => $user_behavior_returning,
				'user-behavior-retn-custom' => $user_behavior_retn_custom,
				'user-behavior-loggedinout' => $user_behavior_loggedinout,
				'user-behavior-browser-language' => $user_behavior_browser_language,
				'user-behavior-device-mobile' => $user_behavior_device_mobile,
				'user-behavior-device-tablet' => $user_behavior_device_tablet,
				'user-behavior-device-desktop' => $user_behavior_device_desktop,
				'user-behavior-logged' => $group_item['user-behavior-logged'],
				'ab-testing-custom-no-sessions' => $ab_testing_custom_sessions,
				'time-date-start-date' => $group_item['time-date-start-date'],
				'time-date-end-date' => $group_item['time-date-end-date'],
				'Time-Date-Start' => $group_item['Time-Date-Start'],
				'Time-Date-End' => $group_item['Time-Date-End'],
				'Time-Date-Schedule-Selection' => $group_item['Time-Date-Schedule-Selection'],
				'Date-Time-Schedule' => $group_item['Date-Time-Schedule'],
				'testing-mode' => $testing_mode,
				'freeze-mode' => $group_item['freeze-mode'],
				'ab-testing-sessions' => $ab_testing_no_sessions,
				'number_of_views' => $numberOfViews,
				'trigger' => $group_item['trigger'],
				'chosen-common-referrers' => $group_item['chosen-common-referrers'],
				'custom' => $group_item['custom'],
				'page' => $group_item['page'],
				'operator' => $group_item['operator'],
				'compare' => strtolower($compare),
				'advertising_platforms' => strtolower($group_item['advertising_platforms']),
				'advertising_platforms_option' => $group_item['advertising_platforms_option']
			);
			// echo "<pre>".print_r($group_item, true)."</pre>";
			$trigger_data['vesrions'][] = $group_item['repeatable_editor_content'];
		}
		
		/*echo "<pre>".print_r($trigger_data, true)."</pre>";
		echo "<pre>".print_r($_POST['repeater'], true)."</pre>";
		die('died in save');*/
		
		// update default content
		update_post_meta( $post_id, 'ifso_trigger_default', $trigger_data['default']);

		// update rules
		update_post_meta( $post_id, 'ifso_trigger_rules', json_encode($trigger_data['rules']));

		// delete all previous versions
		delete_post_meta($post_id, 'ifso_trigger_version');
		
		if(!empty($trigger_data['vesrions'])) {
			foreach($trigger_data['vesrions'] as $version_content) {
				// add saved versions
				add_post_meta( $post_id, 'ifso_trigger_version', $version_content );
			}
		}


		// print_r(json_encode($trigger_data['rules']));
		// die();

		// update_post_meta( $post_id, 'ifso_trigger_rules', json_encode($trigger_data['rules']));

		// echo $post_id;
		// $data_rules_json = get_post_meta( $post_id, 'ifso_trigger_rules', true );
		// print_r($data_rules_json);
		// die();
		
		
		//die('died in save');
		//update_post_meta( $post_id, 'ifso_trigger_rules', htmlspecialchars(json_encode($trigger_data), ENT_QUOTES, 'UTF-8') );
	}
}
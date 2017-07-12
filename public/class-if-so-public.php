<?php

require_once(__DIR__ ."/funcs.php");

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class If_So_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/* Responsible to check if the license is still valid */
	private function _edd_ifso_is_license_valid() {
		if (!get_transient('ifso_transient_license_validation') &&
			get_option( 'edd_ifso_license_key' ) !== false) {
			set_transient( 'ifso_transient_license_validation', true, 300);
			// retrieve our license key & item name from the DB
			$license = trim( get_option('edd_ifso_license_key') );
			$item_name = get_option('edd_ifso_license_item_name');
			// Check if the license is still valid
			if($this->_edd_ifso_check_license($license, $item_name)) {
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

	private function _edd_ifso_check_license($license, $item_name) {
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
	
	/*
	 *	Create shortcode
	 */
	public function add_if_so_shortcode( $atts ) {
		$this->_edd_ifso_is_license_valid(); // Validates the license

		// get post id from shortcode
		if(empty($atts['id'])) return '';
		$trigger_id = $atts['id'];
		
		$data = array();
		$data_default = get_post_meta( $trigger_id, 'ifso_trigger_default', true );
		$data_rules_json = get_post_meta( $trigger_id, 'ifso_trigger_rules', true );
		$data_rules = json_decode($data_rules_json, true);
		$data_versions = get_post_meta( $trigger_id, 'ifso_trigger_version', false );
		// echo "<pre>".print_r($_SERVER, true)."</pre>";
		$referrer = trim($_SERVER['HTTP_REFERER'], '/');
		$referrer = str_replace('https://', '', $referrer);
		$referrer = str_replace('http://', '', $referrer);
		$referrer = str_replace('www.', '', $referrer);
		$current_url = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";//http://
		
		// echo "<script>console.log(document.referrer);</script>";

		// Begin Testing Mode Check
		if (!empty($data_rules) && 
			isset($data_rules[0]['testing-mode']) &&
			$data_rules[0]['testing-mode'] != "") {

			if (!is_numeric($data_rules[0]['testing-mode'])) return 'NOT NUMERIC!';

			//declare testing mode var
			$testingModeIndex = intval($data_rules[0]['testing-mode']);

			if (0 == $testingModeIndex) {
				// the default content is the testing mode, so we return it
				return (!empty($data_default)) ? apply_filters('the_content', $data_default) : '';
			}

			$testingModeIndex -= 2; // the default content takes one and it keeps counting from 2

			// otherwise just return the index's version content if it's not in the upper limit
			if (sizeof($data_rules) >= $testingModeIndex &&
				$testingModeIndex >= 0) {
				return $data_versions[$testingModeIndex];
			}
		}
		// End Testing Mode Check

		if(empty($data_rules)) {
			// return default content if rules are empty
			return (!empty($data_default)) ? apply_filters('the_content', $data_default) : '';
		}

		/* Begin Cookies Handle */

		$cookie_name = 'ifso_visit_counts';

		$isNewUser = $_COOKIE[$cookie_name] == '';
		$numOfVisits = 0;
		$isUserLoggedIn = is_user_logged_in();

		if (!$isNewUser)
			$numOfVisits = $_COOKIE[$cookie_name] + 1;
			
		setcookie($cookie_name, $numOfVisits, time() + (86400 * 30), "/"); // 86400 = 1 day

		/* End Cookies Handle */

		/* Impressions */

		$options = get_option("ifso");

		$ifso_sessions = $options["framed_sessions"];
		$ifso_activated = $options["activated"];
		$ifso_level = $options["level"];

		// Check if we exceeds our sessions
		// TODO: Somehow detect when user changes his LEVEL
		// 		 to obtain more sessions
		// if ($options["monthly_sesssions_count"] >= $options["level_sessions"]) {
			/* Reset "framed_sessions" option? */
			// return (!empty($data_default)) ? apply_filters('the_content', $data_default) : '';
		// }

		if ($ifso_sessions <= 0) { // START HANDLING 0 FRAMED SESSIONS
			/* No sessions left */

			// echo "LESS THAN 0";

			$response = wp_remote_get( 'http://www.if-so.com/wp-content/themes/Avada-child/ifso/ifso_updater.php', 
				array('body' => 
						array('uid' => '1',
							  'domain' => $_SERVER['HTTP_HOST'],
							  'level' => $ifso_level || 0)) );

			if( is_array($response) ) {
			  	$header = $response['headers']; // array of http header lines
			  	$body = $response['body']; // use the content

				$data = json_decode( $body, true );
				// Check if error
				if ($data['error']) {
					// return (!empty($data_default)) ? apply_filters('the_content', $data_default) : '';
				}

				if (isset($data['abuse']) &&
					$data['abuse'] == true) {
					// Abusement of the system,
					// thus returning default content
					// not before changing the options

					$options["level_sessions"] = $data["level_sessions"];
					$options["monthly_sesssions_count"] = $data["level_sessions"];
					update_option("ifso", $options); 

					return (!empty($data_default)) ? apply_filters('the_content', $data_default) : '';
				}

				$options["framed_sessions"] = $data['framed_sessions'];
				$options["level_sessions"] = $data['level_sessions'];
				$options["monthly_sesssions_count"] = $data["monthly_sesssions_count"];
				
				if ($data["activated"]) {
					$options["level"] = $data["level"];
					$options["activated"] = true;

					// domain exceeds
					if ($data["framed_sessions"] == 0) {
						$options["framed_sessions"] = 15; // NUM OF RETRIES
						
						update_option("ifso", $options);
						
						// Not left sessions!
						return (!empty($data_default)) ? apply_filters('the_content', $data_default) : '';
					}
				}
				// return $data['domain'];
			} else {
				/* SILENT */
				// return "ERROR";
				// error?
				// keep going with the code. something went bad
				// in the server side. DO NOT BREAK USERS SITE
			}
		} /* END HANDLING 0 FRAMED SESSIONS */

		$options["framed_sessions"] -= 1;
		$options["monthly_sesssions_count"] += 1;

		update_option("ifso", $options);
		
		// echo "<pre>".print_r($data_rules, true)."</pre>";

		/* Begin Handle Closed Features */

		$status  = get_option( 'edd_ifso_license_status' );
		$isLicenseValid = ($status !== false && $status == 'valid') ? true : false;

		$free_features = array("Device", "User-Behavior");

		/* End Handle Closed Features */

		foreach($data_rules as $index => $rule) {
			
			if(empty($rule['trigger_type'])) continue;
			if($rule['freeze-mode'] == "true") continue; // skip freezed version
			// License no valid
			if (!$isLicenseValid && !in_array($rule['trigger_type'], $free_features)) continue;
			// Sub child features
			if (!$isLicenseValid && 
				$rule['trigger_type'] == "User-Behavior" &&
				!in_array($rule['User-Behavior'], array("LoggedIn", "LoggedOut", "Logged"))) continue;			

			if($rule['trigger_type'] == 'referrer') {
				// handle referrer
				if ($rule['trigger'] == 'common-referrers') {
					$chose_common_referrers = $rule['chosen-common-referrers'];

					if($chose_common_referrers == 'facebook') {
						if(strpos($referrer, 'facebook.com') !== false) return apply_filters('the_content', $data_versions[$index]);
					}
					else if($chose_common_referrers == 'google') {
						if(strpos($referrer, 'google.') !== false) return apply_filters('the_content', $data_versions[$index]);
					}
					// TODO - check twitter referrer not working ($_SERVER['HTTP_REFERER'] is empty)
					else if($chose_common_referrers == 'twitter') {
						if(strpos($referrer, 'twitter.') !== false) return apply_filters('the_content', $data_versions[$index]);
					}
					else if($chose_common_referrers == 'youtube') {
						if(strpos($referrer, 'youtube.') !== false) return apply_filters('the_content', $data_versions[$index]);
					}
				}
				else if($rule['trigger'] == 'page-on-website' && $rule['page']) {
					
					$page_id = (int)$rule['page'];
					$page_link = get_permalink($page_id);
					$page_link = trim($page_link, '/');
					$page_link = str_replace('https://', '', $page_link);
					$page_link = str_replace('http://', '', $page_link);
					$page_link = str_replace('www.', '', $page_link);
					// echo "<pre>".print_r($page_link, true)."</pre>";
					// var_dump($referrer);
					// $page = get_page($page_id);
					if($referrer == $page_link) return apply_filters('the_content', $data_versions[$index]);
				}
				else {
					// custom referrer
					// handle url custom referrer - currently the only one
					if($rule['custom'] == 'url') {
						
						if($rule['operator'] == 'is' || $rule['operator'] == 'is-not') {
							// remove trailing slashes and http from comparition when exact match is requested
							$rule['compare'] = trim($rule['compare'], '/');
							$rule['compare'] = str_replace('https://', '', $rule['compare']);
							$rule['compare'] = str_replace('http://', '', $rule['compare']);
							$rule['compare'] = str_replace('www.', '', $rule['compare']);
						}
						
						if($rule['operator'] == 'contains' && (strpos($referrer, $rule['compare']) !== false)) return apply_filters('the_content', $data_versions[$index]); // match wildcards
						else if($rule['operator'] == 'is' && $referrer == $rule['compare']) return apply_filters('the_content', $data_versions[$index]); // exact match
						else if($rule['operator'] == 'is-not' && $referrer != $rule['compare']) return apply_filters('the_content', $data_versions[$index]); // not exact match
						else if($rule['operator'] == 'not-containes' && (strpos($referrer, $rule['compare']) === false)) return apply_filters('the_content', $data_versions[$index]); // does'nt match wildcards
					}
				}
			}
			else if($rule['trigger_type'] == 'url' || $rule['trigger_type'] == 'advertising-platforms') {

				$compare = '';
				if ($rule['trigger_type'] == 'url') {
					$compare = $rule['compare'];
				} else if ($rule['trigger_type'] == 'advertising-platforms') {
					$compare = $rule['advertising_platforms'];
				}

				if(!empty($_GET['ifso']) && $_GET['ifso'] == $compare) return apply_filters('the_content', $data_versions[$index]);
			}
			else if($rule['trigger_type'] == 'AB-Testing') {
				if (!empty($rule['AB-Testing'])) {

					$perc 			= $rule['AB-Testing'];
					// $gCounter 		= $rule['a-b-counter'];
					$numberOfViews 	= $rule['number_of_views'];
					$sessionsBound 	= $rule['ab-testing-sessions'];

					if ($sessionsBound == 'Custom') {
						if (empty($rule['ab-testing-custom-no-sessions'])) break;

						$sessionsBound = $rule['ab-testing-custom-no-sessions'];
					}

					// Check if we passed the number of sessions
					// dedicated to that post
					if ($sessionsBound != 'Unlimited' &&
						$numberOfViews >= (int)$sessionsBound) break;

					$factors = array("25%" => 4,
									 "33%" => 3,
									 "50%" => 2,
									 "75%" => 4);

					$factor = $factors[$perc];

					$factRemainder = $numberOfViews % $factor;

					// Sets new a-b-counter
					// $gCounter += 1;
					$numberOfViews += 1;

					// $data_rules[$index]['a-b-counter'] = $gCounter % $factor;
					$data_rules[$index]['number_of_views'] = $numberOfViews;

					$data_rules_cleaned = str_replace("\\", "\\\\\\", json_encode($data_rules));

					update_post_meta( $trigger_id, 'ifso_trigger_rules', $data_rules_cleaned);

					if ($perc == "25%" && $factRemainder == 0) {
							return apply_filters('the_content', $data_versions[$index]);
					} else if ($perc == "33%" && $factRemainder == 0) {
							return apply_filters('the_content', $data_versions[$index]);
					} else if ($perc == "50%" && $factRemainder == 0) {
							return apply_filters('the_content', $data_versions[$index]);
					} else if ($perc == "75%" &&
							   in_array($factRemainder, array(0, 1, 2))) {
							return apply_filters('the_content', $data_versions[$index]);
					}
				} 
			} else if ($rule['trigger_type'] == 'User-Behavior') {
					/*
						Helpers:
							$isNewUser = TRUE/FALSE
							$numOfVisits = NUMBER
							$isUserLoggedIn = TRUE/FALSE
					*/

					$user_behavior = $rule['User-Behavior'];


					if ($user_behavior == "NewUser") {
						// Check if new user

						if ($isNewUser) {
							// Yes he is a new user!
							return apply_filters('the_content', $data_versions[$index]);
						}

					} else if ($user_behavior == "Returning") {
						// Check if the user is returning based on 
						// 'user-behavior-returning' OR 'user-behavior-retn-custom'
						// incase 'user-behavior-returning' is CUSTOM.

						// Check if it's custom thus use 'user-behavior-retn-custom'

						$numOfReturns = 0;

						if ($rule['user-behavior-returning'] == "custom") {
							$numOfReturns = intval($rule['user-behavior-retn-custom']);
						} else {
								$returnsOptions = array("first-visit" => 1,
									 					"second-visit" => 2,
												 		"three-visit" => 3);

								$numOfReturns = $returnsOptions[$rule['user-behavior-returning']];
						}

						// In here, $numOfReturns hold the number of returns we desire

						if ($numOfVisits >= $numOfReturns) {
							// We have a match! :)
							return apply_filters('the_content', $data_versions[$index]);
						}

					} else if ($user_behavior == "LoggedIn") {
						// Check if the user is Logged in
						if ($isUserLoggedIn) {
							// Yes, he is!
							return apply_filters('the_content', $data_versions[$index]);
						}
					} else if ($user_behavior == "LoggedOut") {
							// Check if the user is Logged out
							if (!$isUserLoggedIn) {
								// Yes, he isn't!
								return apply_filters('the_content', $data_versions[$index]);
							}
					} else if ($user_behavior == "Logged") {
						// New Version of Logged In Out.
						// Keeping the previous one for backward compatibility.
						$loggedInOut = $rule['user-behavior-logged'];
						// return $loggedInOut;

						if ($loggedInOut == "logged-in" && $isUserLoggedIn) {
							// Yes! he is logged in!
							return apply_filters('the_content', $data_versions[$index]);
						} else if ($loggedInOut == "logged-out" && !$isUserLoggedIn) {
							// Yes! he is logged off
							return apply_filters('the_content', $data_versions[$index]);
						}
					} else if ($user_behavior == "BrowserLanguage") {
						// grab user's language
						$user_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

						// check if user's language is in match with the 
						// user behavior selected language
						if (strpos($user_lang, $rule['user-behavior-browser-language']) !== false) {
							// Yes! User's language is the same as the ifso setting
							return apply_filters('the_content', $data_versions[$index]);
						}
					}
			} else if ($rule['trigger_type'] == "Device") {
				if ($rule["user-behavior-device-mobile"] && my_wp_is_mobile()) {
					// User is on Mobile
					return apply_filters('the_content', $data_versions[$index]);
				} else if ($rule["user-behavior-device-tablet"] && my_wp_is_tablet()) {
					// User is on Tablet
					return apply_filters('the_content', $data_versions[$index]);
				} else if ($rule["user-behavior-device-desktop"] && (!my_wp_is_mobile() && !my_wp_is_tablet())) {
					// User is on Desktop
					return apply_filters('the_content', $data_versions[$index]);
				}
			} else if ($rule['trigger_type'] == "Time-Date") {

				// Check if the selection is "Start/End Date"

				$format = "Y/m/d H:i";
				$currDate = DateTime::createFromFormat($format, current_time($format));

				if ($rule["Time-Date-Schedule-Selection"] == "Start-End-Date") {
					if ($rule['Time-Date-Start'] == "None" &&
						$rule['Time-Date-End'] == "None") {
						return apply_filters('the_content', $data_versions[$index]);
					}

					if ($rule['Time-Date-Start'] == "None") {
						// No start date
						$endDate = DateTime::createFromFormat($format, $rule['time-date-end-date']);

						if ($currDate <= $endDate) {
							// Yes! we are in the right time frame
							return apply_filters('the_content', $data_versions[$index]);
						}

					} else if ($rule['Time-Date-End'] == "None") {
						// No end date
						$startDate = DateTime::createFromFormat($format, $rule['time-date-start-date']);

						if ($currDate >= $startDate) {
							// Yes! we are in the right time frame
							return apply_filters('the_content', $data_versions[$index]);
						}
					} else {
						// Both have dates
						$startDate = DateTime::createFromFormat($format, $rule['time-date-start-date']);
						$endDate = DateTime::createFromFormat($format, $rule['time-date-end-date']);

						if ($currDate >= $startDate &&
							$currDate <= $endDate) {
							// Yes! we are in the right time frame
							return apply_filters('the_content', $data_versions[$index]);
						}
					}
				} else {
					// Otherwise the selection is "Schedule-Date"
					$schedule = json_decode($rule['Date-Time-Schedule']);
					$currTime = current_time($format);
					$currDay = date('w');
					$selectedHours = $schedule->$currDay;
					$dayYearMonth = split(" ", $currTime)[0];

					if (!empty($selectedHours)) {
						foreach ($selectedHours as $hoursKey => $hoursPair) {
							$startHour = $dayYearMonth." ".$hoursPair[0];
							$endHour = $dayYearMonth." ".$hoursPair[1];
							
							$startDate = DateTime::createFromFormat($format, $startHour);
							$endDate = DateTime::createFromFormat($format, $endHour);

							// Check if in between
							// if so we display this version's content

							if ($currDate >= $startDate &&
								$currDate <= $endDate) {
								return apply_filters('the_content', $data_versions[$index]);
							}
						}
					}
				}
			}
		}
			//echo "<pre>".print_r($rule, true)."</pre>";
		
		// return default content if nothing match
		return (!empty($data_default)) ? apply_filters('the_content', $data_default) : '';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/if-so-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/if-so-public.js', array( 'jquery' ), $this->version, false );

	}

}

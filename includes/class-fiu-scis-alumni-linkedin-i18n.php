<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.cis.fiu.edu
 * @since      1.0.0
 *
 * @package    Fiu_Scis_Alumni_Linkedin
 * @subpackage Fiu_Scis_Alumni_Linkedin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fiu_Scis_Alumni_Linkedin
 * @subpackage Fiu_Scis_Alumni_Linkedin/includes
 * @author     https://www.cis.fiu.edu <webmaster@cis.fiu.edu>
 */
class Fiu_Scis_Alumni_Linkedin_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fiu-scis-alumni-linkedin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

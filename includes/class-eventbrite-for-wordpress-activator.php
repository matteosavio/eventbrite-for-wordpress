<?php

/**
 * Fired during plugin activation
 *
 * @link       https://digitalideas.io/
 * @since      1.0.0
 *
 * @package    Eventbrite_For_Wordpress
 * @subpackage Eventbrite_For_Wordpress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Eventbrite_For_Wordpress
 * @subpackage Eventbrite_For_Wordpress/includes
 * @author     Digital Ideas <matteo@digitalideas.io>
 */
class Eventbrite_For_Wordpress_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        $timestamp = wp_next_scheduled( 'eventbrite_for_wordpress_getevents' );

        if($timestamp == false) {
            wp_schedule_event( time(), 'hourly', 'eventbrite_for_wordpress_getevents' );
        }
	}

}

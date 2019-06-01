<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://digitalideas.io/
 * @since      1.0.0
 *
 * @package    Eventbrite_For_Wordpress
 * @subpackage Eventbrite_For_Wordpress/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Eventbrite_For_Wordpress
 * @subpackage Eventbrite_For_Wordpress/public
 * @author     Digital Ideas <matteo@digitalideas.io>
 */
class Eventbrite_For_Wordpress_Public {

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
		 * defined in Eventbrite_For_Wordpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Eventbrite_For_Wordpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/eventbrite-for-wordpress-public.css', array(), $this->version, 'all' );

	}
	
	public function register_shortcodes() {
		add_shortcode('eventbrite_list', array($this,'eventbrite_list'));
	}
	
	public function register_posttype() {
    	$args = [
            'labels' => ['name' => __( 'Events' ), 'singular_name' => __( 'Event' )],
            'public' => false, 
            'rewrite' => array('slug' => 'event'),
            'supports' => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'public' => true,
            'has_archive' => true,
            'menu_position'      => null,
		    'capability_type'    => 'post',
        ];
        register_post_type( 'eventbritelist_event', $args); 
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
		 * defined in Eventbrite_For_Wordpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Eventbrite_For_Wordpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/eventbrite-for-wordpress-public.js', array( 'jquery' ), $this->version, false );
    }
	
	public function eventbrite_list() {
    	$atts = [];
    	
        $status = 'future';
        if(isset($atts['status'])) {
            $status = $atts['status'];
        }
        
        $showExcerpt = false;
        $showDescription = false;
        if(isset($atts['show_excerpt'])) {
            $showDescription = true;
        }
            
        /*if($showDescription == "false") {
            $showDescription = false;
        }
        else {
            $showDescription = true;
        }
        
        if($showHiddenTickets == "true") {
            $showHiddenTickets = true;
        }
        else {
            $showHiddenTickets = false;
        }*/
            $showHiddenTickets = false;
        
        $showMoreLinkIfThereAreHiddenEvents = true;
        $hiddenEvents = 0;
        
        $limitEventsToShow = 3;
        if(isset($atts['limit_events_to_show'])) {
            $limitEventsToShow = intval($atts['limit_events_to_show']);
        }
        
        $args = [
        	'orderby'          => 'date',
        	'order'            => 'ASC',
        	'post_type'        => 'eventbritelist_event',
            'post_status'      => $status,
        	'suppress_filters' => true,
            'posts_per_page'   => 100,
        ];
        $events = get_posts($args);
        $content .= '<div class="eventbritelist">'."\n";
        $content .= '<div class="list">'."\n";
        
        foreach($events as $event) {
            $freeTicketAvailability =  get_post_meta($event->ID, 'eventbritelist_eventbrite_free_tickets_availability', true);
            $eventUrl = get_post_meta($event->ID, 'eventbritelist_eventbrite_link', true);
            
            if($freeTicketAvailability == 'AVAILABLE_NOW') {
                $freeTicketsAvailable = (int)get_post_meta($event->ID, 'eventbritelist_eventbrite_free_tickets_availability_count', true);
                if($freeTicketsAvailable < 1) {
                    $ticketsAvailableString = 'ticket availability could not be determined';
                }
                if($freeTicketsAvailable == 1) {
                    $ticketsAvailableString = '<a href="' . $eventUrl . '" class="button oneleft" title="Updated: ' .  get_post_modified_time("l, j. F Y H:i", false, $event->ID ) . '">last ticket available &#8811;</a>';
                }
                else if($freeTicketsAvailable <= 3) {
                    $ticketsAvailableString = '<a href="' . $eventUrl . '" class="button limited" title="Updated: ' .  get_post_modified_time("l, j. F Y H:i", false, $event->ID ) . '">just ' . $freeTicketsAvailable . ' free tickets left &#8811;</a>';
                }
                else {
                    $ticketsAvailableString = '<a href="' . $eventUrl . '" class="button available" title="Updated: ' .  get_post_modified_time("l, j. F Y H:i", false, $event->ID ) . '">' . $freeTicketsAvailable . ' free tickets &#8811;</a>';
                }
            }
            else if($freeTicketAvailability == 'AVAILABLE_IN_THE_FUTURE') {
                $freeTicketsAvailabilityDate = new \DateTime(get_post_meta($event->ID, 'eventbritelist_eventbrite_free_tickets_availability_date', true));
                $ticketsAvailableString = '<a href="' . $eventUrl . '" class="button future" title="Updated: ' .  get_post_modified_time("l, j. F Y H:i", false, $event->ID ) . '">free tickets available ' . $freeTicketsAvailabilityDate->format('F j H:i') . '</a>';
            }
            else if($freeTicketAvailability == 'SOLD_OUT') {
                $ticketsAvailableString = '<a href="' . $eventUrl . '" class="button soldout" title="Updated: ' .  get_post_modified_time("l, j. F Y H:i", false, $event->ID ) . '">no tickets left</a>';
            }
            /*else if($freeTicketAvailability == 'NOT_AVAILABLE') {
                $ticketsAvailableString = '<a href="' . $eventUrl . '" class="button available" title="Updated: ' .  get_post_modified_time("l, j. F Y H:i", false, $event->ID ) . '">tickets not available</a>';
            }*/
            else {
                $ticketsAvailableString = '<a href="' . $eventUrl . '" class="button available" title="Updated: ' .  get_post_modified_time("l, j. F Y H:i", false, $event->ID ) . '">Tickets &raquo;</a>';
            }
            
            if($limitEventsToShow > 0) {
                $content .= '<div class="event">';
            }
            else {
                $content .= '<div class="hidden event">';
                $hiddenEvents++;
            }
            
            $content .= '<div class="image">';
            if(!empty(get_post_meta($event->ID, 'eventbritelist_eventbrite_image', true))) {
                $content .= '<img src="' . get_post_meta($event->ID, 'eventbritelist_eventbrite_image', true) . '">';
            }
            $content .= '</div>';
            $content .= '<div class="time"><i class="fal fa-calendar"></i> ' . get_the_time( "l, j. F Y H:i", $event->ID ) . '</div>';
            $content .= '<div class="title">
                <a href="' . $eventUrl  . '">' . $event->post_title . '</a>';
            if(!empty(get_post_meta($event->ID, 'eventbritelist_eventbrite_organizer_name', true))) {
                $content .= ' by <a href="' . get_post_meta($event->ID, 'eventbritelist_eventbrite_organizer_url', true) . '">' . get_post_meta($event->ID, 'eventbritelist_eventbrite_organizer_name', true) . '</a>
                </div>';
            }
            if(!empty(get_post_meta($event->ID, 'eventbritelist_eventbrite_location', true))) {
                $content .= '<div class="location"><i class="fal fa-thumbtack"></i> <a href="http://www.google.com/maps/place/' . get_post_meta($event->ID, 'eventbritelist_eventbrite_location_latitude', true) . ',' . get_post_meta($event->ID, 'eventbritelist_eventbrite_location_longitude', true) . '" target="_blank">' . get_post_meta($event->ID, 'eventbritelist_eventbrite_location', true) . '</a></div>';
            }
            $content .= '<div class="description">';
            if($showDescription) {
                $content .= get_the_excerpt($event->ID) . '<br />';
            }
            $content .= $ticketsAvailableString . '</div>';
            $content .= '</div>';
            $limitEventsToShow--;
        }
        $content .= '</div>'."\n";
        if($hiddenEvents > 0) {
            $content .= "<p><button class=\"moreevents\">Show more ($hiddenEvents) events</button></p>";
        }
        $content .= '</div>'."\n";
        return $content;	
	}
}

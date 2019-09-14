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
class Eventbrite_Connector {
    const EVENTBRITELIST_EVENT_KEY = 'eventbritelist_eventbrite_id';
    const EVENTBRITE_APIv3_BASE = 'https://www.eventbriteapi.com/v3';

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function updateEvents() {
        if (!defined('EVENTBRITELIST_CONFIG')) {
            wp_die('Please define EVENTBRITELIST_CONFIG in wp-config.php with your app token');
        }
        
        $events = $this->eventbritelist_getEventsForProfiles(EVENTBRITELIST_CONFIG);
        /* MISSING: WALK THROUGH ALL FUTURE + 1hr EVENTS WHERE the CUSTOM FIELD IS SET AND CHECK IF THE ID ISN'T IN THE EVENT_LIST */

        foreach($events as $event) {
            if( ($event['event']['status'] == 'live') ||
                ($event['event']['status'] == 'started') ||
                ($event['event']['status'] == 'ended') ||
                ($event['event']['status'] == 'completed')
               ) {
                $eventBeginDate = new \DateTime($event['event']['start']['local']);
                $gmEventBeginDate = new \DateTime($event['event']['start']['local'], new \DateTimeZone('GMT'));
                $eventData = [
                  'post_title'    => wp_strip_all_tags($event['event']['name']['text']),
                  'post_content'  => '',
                  'post_type' => 'eventbritelist_event',
                  'post_author'   => 1,
                  'post_date' => $eventBeginDate->format('Y-m-d H:i:s'),
                  'post_date_gmt' => $gmEventBeginDate->format('Y-m-d H:i:s'),
                  'post_content' => $event['event']['description']['html'],
                  'post_excerpt'  => mb_strimwidth($event['event']['description']['text'], 0, 160, "..."),
                ];
                
                $customFields = [
                    'eventbritelist_eventbrite_link' => $event['event']['url'],
                    'eventbritelist_organizer_id' => $event['event']['organizer_id']
                ];
                
                if(!empty($event['event']['logo']['url'])) {
                    $customFields['eventbritelist_eventbrite_image'] = $event['event']['logo']['url'];
                }
                else {
                    $customFields['eventbritelist_eventbrite_image'] = '';
                }
    
                if(!empty($event['venue'])) {
                    $customFields['eventbritelist_eventbrite_location'] = $event['venue']['name'] . ', ' . $event['venue']['address']['city'] . ', ' . $event['venue']['address']['country'];
                    $customFields['eventbritelist_eventbrite_location_longitude'] = $event['venue']['longitude'];
                    $customFields['eventbritelist_eventbrite_location_latitude'] = $event['venue']['latitude'];
                }
                else {
                    $customFields['eventbritelist_eventbrite_location'] = '';
                    $customFields['eventbritelist_eventbrite_location_longitude'] = 0;
                    $customFields['eventbritelist_eventbrite_location_latitude'] = 0;
                }
    
                if(!empty($event['organizer'])) {
                    $customFields['eventbritelist_eventbrite_organizer_name'] = $event['organizer']['name'];
                    $customFields['eventbritelist_eventbrite_organizer_url'] = isset($event['organizer']['website'])?$event['organizer']['website']:$event['organizer']['url'];
                }
                else {
                    $customFields['eventbritelist_eventbrite_organizer_name'] = '';
                    $customFields['eventbritelist_eventbrite_organizer_url'] = '';
                }
                
                if($event['event']['status'] == 'live') {
                    $eventData['post_status'] = 'future';
                    $areOrWereFreeNonHiddenTicketsAvailable = false;
                    $freeNonHiddenTicketsAvailableNow = 0;
                    $freeNonHiddenTicketsAvailableInTheFuture = 0;
                    $freeNonHiddenTicketsTotal = 0;
                    $freeNonHiddenTicketsAvailableInTheFutureDate = null;
                    
                    foreach($event['ticketClass'] as $ticketClass) {
                        if($ticketClass['free'] && !$ticketClass['hidden']) {
                            $freeNonHiddenTicketsTotal += $ticketClass['quantity_total'];
                            
                            if($ticketClass['on_sale_status'] == 'AVAILABLE') {
                                $areOrWereFreeNonHiddenTicketsAvailable = true;
                                $freeNonHiddenTicketsAvailableNow += $ticketClass['quantity_total'] - $ticketClass['quantity_sold'];
                            }
                            else if($ticketClass['on_sale_status'] == 'NOT_YET_ON_SALE') {
                                $freeNonHiddenTicketsAvailableInTheFuture += $ticketClass['quantity_total'] ;
                                if(is_null($freeNonHiddenTicketsAvailableInTheFutureDate)) {
                                    $freeNonHiddenTicketsAvailableInTheFutureDate = new \DateTime($ticketClass['sales_start'], new \DateTimeZone('UTC'));
                                }
                                else {
                                    $anotherTicketClassSalesStart = new \DateTime($ticketClass['sales_start'], new \DateTimeZone('UTC'));
                                    if($anotherTicketClassSalesStart < $freeNonHiddenTicketsAvailableInTheFutureDate) {
                                        $freeNonHiddenTicketsAvailableInTheFutureDate = $anotherTicketClassSalesStart;
                                    }
                                }
                            }
                            
                            /*
                                It's also possible that ticket sale of all tickets has stopped. That case is detected as "Sold out".
                            */
                        }
                        else {
                            // PAID TICKETS COUNT TO BE IMPLEMENTED YET!!!
                        }
                    }
                    
                    if($freeNonHiddenTicketsTotal > 0) {
                        if($freeNonHiddenTicketsAvailableNow > 0) { // FREE TICKETS AVAILABLE_NOW
                            $customFields['eventbritelist_eventbrite_free_tickets_availability'] = 'AVAILABLE_NOW';
                            $customFields['eventbritelist_eventbrite_free_tickets_availability_count'] = $freeNonHiddenTicketsAvailableNow; 
                        }
                        else if($freeNonHiddenTicketsAvailableInTheFuture > 0) { // FREE TICKETS AVAILABLE_IN_THE_FUTURE
                            $customFields['eventbritelist_eventbrite_free_tickets_availability'] = 'AVAILABLE_IN_THE_FUTURE';
                            $customFields['eventbritelist_eventbrite_free_tickets_availability_date'] = $freeNonHiddenTicketsAvailableInTheFutureDate->format(DATE_ATOM);
                        }
                        else { // FREE TICKETS SOLD_OUT
                            $customFields['eventbritelist_eventbrite_free_tickets_availability'] = 'SOLD_OUT';
                        }
                    }
                    else { // FREE TICKETS NOT_AVAILABLE
                        $customFields['eventbritelist_eventbrite_free_tickets_availability'] = 'NOT_AVAILABLE';
                    }
                    
                    // PAID TICKETS COUNT TO BE IMPLEMENTED YET!!!
                    $customFields['eventbritelist_eventbrite_paid_tickets_available'] = 'NOT_AVAILABLE';
                }
                else { // EVENT IS IN THE PAST OR HAS STARTED/ENDED
                    $eventData['post_status'] = 'publish';
                    $customFields['eventbritelist_eventbrite_free_tickets_availability'] = 'NOT_AVAILABLE';
                    $customFields['eventbritelist_eventbrite_paid_tickets_availability'] = 'NOT_AVAILABLE';
                }
                $this->insertOrUpdateEvent($event['event']['id'], $eventData, $customFields);
            }
            else {
                $this->unpublishEventIfExists($event['event']['id']);
            }
        }
        // check if any future event got deleted on Eventbrite and delete them if so
        $args = [
        	'orderby'          => 'date',
        	'order'            => 'ASC',
        	'post_type'        => 'eventbritelist_event',
            'post_status'      => 'future',
        	'suppress_filters' => true,
            'posts_per_page'   => 100,
        ];
        $futureEvents = get_posts($args);
        
        foreach($futureEvents as $futureEvent) {
            $eventbriteEventId = get_post_meta($futureEvent->ID, self::EVENTBRITELIST_EVENT_KEY, true);
            $eventbriteOrganizerId = get_post_meta($futureEvent->ID, 'eventbritelist_organizer_id', true);
            var_dump($eventbriteEventId);
            $apiToken = NULL;
            foreach(EVENTBRITELIST_CONFIG as $key => $organizerIds) {
                if(in_array($eventbriteOrganizerId, $organizerIds)) {
                    $apiToken = $key;
                }
            }

            if(!is_null($apiToken)) {
                if(!$this->eventbritelist_checkIfEventExists($apiToken, $eventbriteEventId)) {
                    $this->unpublishEventIfExists($futureEvent->ID);
                }
            }
            else { // in this case the orgnizer id is either not present at all or is wrong, so the event needs to be removed
                wp_delete_post($futureEvent->ID, false);
            }
        }
    }
    
    public function testCall($appProfileKeys) {
        var_dump($this->eventbritelist_getEventsForProfiles($appProfileKeys));
        wp_die();
    }

    private function eventbritelist_getEventsForProfiles($appProfileKeys) {
        $eventbriteEvents = [];
        
        foreach($appProfileKeys as $appKey => $profiles) {
            foreach($profiles as $profile) {
                $returnedEvents = $this->eventbritelist_eventbriteGetAllEventsStarting48hrsAgoForOrganizer($appKey, $profile);
                
                if($returnedEvents !== false) {
                    foreach($returnedEvents as $returnedEvent) {
                        
                        $eventbriteEvent = array();
                        $eventbriteEvent['event'] = $returnedEvent;
                        $eventbriteEvent['venue'] = $this->eventbritelist_eventbriteGetVenue($appKey, $returnedEvent['venue_id']);
                        $eventbriteEvent['organizer'] = $this->eventbritelist_eventbriteGetOrganizer($appKey, $returnedEvent['organizer_id']);
                        $eventbriteEvent['ticketClass'] = $this->eventbritelist_eventbriteGetTicketClasses($appKey, $returnedEvent['id']);
                        $eventbriteEvents[] = $eventbriteEvent;
                    }
                }
            }
            unset($eventbriteClient);
        }
        return $eventbriteEvents;
    }
    
    private function eventbritelist_eventbriteGetVenue($tokenId, $venueId) {
        if(!empty($venueId)) {
            $answer = $this->eventbritelist_eventbriteCall($tokenId, '/venues/' . $venueId . "/", [], []);
            return $answer;
        }
        return [];
    }
    
    private function eventbritelist_eventbriteGetOrganizer($tokenId, $organizerId) {
        $answer = $this->eventbritelist_eventbriteCall($tokenId, '/organizers/' . $organizerId . "/", [], []);
        return $answer;
    }
    
    private function eventbritelist_eventbriteGetTicketClasses($tokenId, $eventId) {
        $ticketClasses = [];
        $pageToQuery = 1;
        do {
            $answer = $this->eventbritelist_eventbriteCall($tokenId, '/events/' . $eventId . "/ticket_classes/", [], ['page=' . $pageToQuery]);
            if($answer === false)
                return false;
            $pageToQuery++;
            $ticketClasses = array_merge($ticketClasses, $answer['ticket_classes']);
        } while($answer['pagination']['has_more_items']);
    
        return $ticketClasses;
    }
    
    private function eventbritelist_eventbriteGetAllEventsStarting48hrsAgoForOrganizer($tokenId, $organizerId) {
        $events = [];
        $pageToQuery = 1;
        $startDate = new \DateTime();
        $startDate->modify('-48 hours');
        do {
            $answer = $this->eventbritelist_eventbriteCall($tokenId, '/organizers/' . $organizerId . '/events/', [], ['status=all','start_date.range_start='.$startDate->format('Y-m-d\TH:i:s'), 'page=' . $pageToQuery]);
            if($answer === false)
                return false;
            $pageToQuery++;
            $events = array_merge($events, $answer['events']);
        } while($answer['pagination']['has_more_items']);
    
        return $events;
    }
    
    function eventbritelist_checkIfEventExists($token, $eventId) {
        $answer = $this->eventbritelist_eventbriteCall($token, '/events/' . $eventId . "/", [], []);
        if($answer === false)
            return false;
        return true;
    }
    
    function eventbritelist_eventbriteCall($token, $path, $body, $expand, $httpMethod = 'GET') {
        if(!ini_get('allow_url_fopen') ) {
            return new WP_Error( 'url_open', __( "Please set allow_url_fopen to true to allow opening the Eventbrite API!", "eventbrite-for-wordpress" ) );
        } 
        
        $data = json_encode($body);
        
        $options = array(
            'http'=>array(
                'method' => $httpMethod,
                'header' => "content-type: application/json\r\n",
                'ignore_errors' => true,
                'header' => "User-Agent: Website " . site_url()
            )
        );
    
        if ($httpMethod == 'POST') {
            $options['http']['content'] = $data;
        }
    
        $url = self::EVENTBRITE_APIv3_BASE . $path . '?token=' . $token;
    
        if (!empty($expand)) {
            $expand_str = implode('&', $expand);
            $url = $url . '&' . $expand_str;
        }
        
        $context  = stream_context_create($options);
        
        $result = file_get_contents($url, false, $context);

        if($result === false) {
            return false;
        }
        
        $response = json_decode($result, true);
        
        if (empty($response)) {
            $response = array();
        }
        else if(isset($response['status_code'])) {
            if(isset($response['error'])) {
                if($response['error'] == 'NOT_FOUND') {
                    return false;
                }
                else {
                    wp_die($response['error'] . ": " . $response['error_description'] . ' (Line ' . __LINE__ . ')');
                }
            }
        }
        
        $response['response_headers'] = $http_response_header;
        return $response;
    }
    
    private function insertOrUpdateEvent($eventbriteEventId, $eventData, $customFields) {
        if(empty($eventbriteEventId)) {
            return false;
        }
        $args = [
        	'meta_key'         => self::EVENTBRITELIST_EVENT_KEY,
        	'meta_value'       => $eventbriteEventId,
        	'post_type'        => 'eventbritelist_event',
        	'post_status'      => 'publish,future',
        	'suppress_filters' => true 
        ];
        $existingEvents = get_posts($args);
        if($existingEvents) {
            if(count($existingEvents) == 1) {
                $existingEvent = array_shift($existingEvents);
                
                $eventData['ID'] = $existingEvent->ID;
                if($postId = wp_update_post($eventData)) {
                    foreach($customFields as $customFieldName => $customFieldValue) {
                        if (!add_post_meta($postId, $customFieldName, $customFieldValue, true )) { 
                            update_post_meta($postId, $customFieldName, $customFieldValue);
                        }
                    }
                }
            }
            else {
                wp_die("There were multiple instances of the same event with the ID $eventId found. Please delete the ones you don't want to keep, until then synching of this event is paused.");
            }
        }
        else {
            if($postId = wp_insert_post( $eventData )) {
                if (!add_post_meta($postId, self::EVENTBRITELIST_EVENT_KEY, $eventbriteEventId, true ) ) { 
                    update_post_meta($postId, self::EVENTBRITELIST_EVENT_KEY, $eventbriteEventId);
                }
                foreach($customFields as $customFieldName => $customFieldValue) {
                    if ( ! add_post_meta($postId, $customFieldName, $customFieldValue, true ) ) { 
                        update_post_meta($postId, $customFieldName, $customFieldValue);
                    }
                }
            }
        }  
    }
}

<?php

class UBEP_Schema {
    
	public function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}	

	public function __construct() {
		$this->post_type = 'ubep_simple_event';
        $this->date_meta = 'ubep_event_date';
        add_action( 'init', array( $this, 'register_feed_post_type' ) );
    }
    
	/**
	 * All we need is a basic custom post type. Let's not get crazy.
	 */
	public function register_feed_post_type() {
		$labels = array(
			'name'               => __( 'Events', 'ubep' ),
			'singular_name'      => __( 'Event', 'ubep' ),
			'add_new'            => _x( 'Add New', 'ubep', 'add new event' ),
			'all_items'          => __( 'All Events', 'ubep' ),
			'add_new_item'       => __( 'Add New Event', 'ubep' ),
			'edit_item'          => __( 'Edit Event', 'ubep' ),
			'new_item'           => __( 'New Event', 'ubep' ),
			'view_item'          => __( 'View Event', 'ubep' ),
			'search_items'       => __( 'Search Events', 'ubep' ),
			'not_found'          => __( 'No events found', 'ubep' ),
			'not_found_in_trash' => __( 'No events found in trash', 'ubep' ),
		);

		register_post_type( $this->post_type, apply_filters( 'ubep_register_feed_post_type_args', array(
			'label'       => $labels['name'],
			'labels'      => $labels,
			'description' => __( 'Events created by the Ultra Basic Events plugin', 'ubep' ),
			'public'      => true,
			'hierarchical' => false,
            'publicly_queryable' => true,
            'exclude_from_search' => true,
			'supports' 	=> array('title','editor','author','thumbnail','excerpt','custom-fields','page-attributes'),
			'taxonomies' => array('category', 'post_tag'),
            'capabilities' => array('edit_posts')
			#'show_in_menu' => ubep()->menu_slug
			#'menu_position' => 100
			#'show_ui'     => true, // for testing only
		) ) );

		do_action( 'ubep_feed_post_type_registered' );
	}
    
    public function date_box_schema(){
        $args = ubep()->util->build_meta_box_argument('event_date', 'Event Date', 'event_date', 'date');    
        return $args;
    }
    
    public function date_box(){
        $args = self::date_box_schema();
        
        add_meta_box( $this->slug.'_event_date_box', 'Event Date', array($this, 'meta_box_maker'), $this->post_type, 'side', 'high', $args );
        
    }
    
    public function meta_box_maker($args){
        
        ubep()->util->meta_box_maker($args);
        
    }
        
    
}
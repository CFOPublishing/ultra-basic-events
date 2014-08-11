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
        add_action( 'add_meta_boxes', array($this, 'date_box') );
        add_action( 'save_post', array($this, 'meta_box_checker') );
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
			'taxonomies' => array('category', 'post_tag')
			#'show_in_menu' => ubep()->menu_slug
			#'menu_position' => 100
			#'show_ui'     => true, // for testing only
		) ) );

		do_action( 'ubep_feed_post_type_registered' );
	}

    public function date_box_schema($key = false){
        $args = ubep()->util->build_meta_box_argument('event_date', 'Event Date', 'event_date', 'date', 'Field for saving when the event will occur');
        if (!$key){
          return $args;
        }
        return $args[$key];
    }

    public function date_box(){
        $args = self::date_box_schema();
        #var_dump('<pre>');
        #var_dump($args);
        #die();
        add_meta_box( ubep()->slug.'_event_date_box', 'Event Information', array($this, 'meta_box_maker'), $this->post_type, 'side', 'high', $args );

    }

    public function meta_box_maker($post, $args){
        #var_dump('<pre>');
        #var_dump($args);
        #var_dump('bob');
        #die();
        ubep()->util->meta_box_maker($post, $args);

    }

    public function meta_box_checker($post_id){
        $args = self::date_box_schema();
        #var_dump($post_id);
        #var_dump($_POST); die();
        ubep()->util->meta_box_checker($post_id, $args);
    }


}

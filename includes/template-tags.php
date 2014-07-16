<?php

class UBEP_Template_Tags {

	public function get_event_posts_by_date($args = array()){

		$default = array(
					'post_type'			=> ubep()->schema->post_type,
					'meta_key'			=> ubep()->schema->date_box_schema('field_name'),
					'orderby'       => 'meta_value',
					'order'					=> 'DESC',
					'post_status'		=> 'publish'

		);
		#var_dump($args); die(0);
		$args = wp_parse_args( $args, $default );

		$query = new WP_Query($args);

		return $query;

	}

}

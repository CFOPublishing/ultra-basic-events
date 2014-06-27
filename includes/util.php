<?php
class UBEP_Util {
    
	public function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}	

	public function __construct() {
		
        #Stuff
        
    }
    
    
    /**
     *
     * Takes
     * meta_slug    - string, slug to save post_meta with
     * label        - the user-visible label attached to the entry field
     * field_name   - the HTML for= and name= value. 
     * input        - field type
     * post_type    - qualified post type, string, only takes one atm.
     * size         - field size
     * the_field    - pass HTML if no input template is useful. 
     *
     */
    public function build_meta_box_argument($meta_slug = false, $label = false, $field_name = false, $input = 'text', $descript="", $size = 25, $post_type = false, $the_field = false){
        if (!$post_type) { $post_type = ubep()->schema->post_type; }
        $args = array(
            'meta_slug'     => $meta_slug,
            'label'         => $label,
            'field_name'    => $field_name,
            'input'         => $input,
			'descript'		=> $descript,
            'post_type'     => $post_type,
            'size'          => $size,
            'the_field'     => $the_field
        );
        foreach ($args as $key=>$arg){
            if (!$arg){
                self::logger('Ultra Basic Event meta box field ' . $key . ' was left unset.');    
            }
        }
        return $args;
    }
    
            
    public function meta_box_default_parser($args){
    
         $default = array(
            'input'     => 'text',
            'size'      => 25,
            'label'     => ubep()->title . ' Meta Field',
            'post_type' => ubep()->schema->post_type
             
        );
        
        $args = wp_parse_args( $args, $defaults );
        $args['field_name'] = ubep()->slug . $args['field_name'];

        return $args;
        
    }
    
    public function meta_slug($args){
        
        return ubep()->slug . '_' . $args['meta_slug'];
        
    }
    
    public function meta_box_box_name($args){
        
        return ubep()->slug . '_' . $args['meta_slug'] . '_box';
        
    }
    
    public function meta_box_nonce_name($args){
     
        return ubep()->slug . '_' . $args['meta_slug'] . '_box_nonce';
        
    }
    
	public function meta_box_maker($args){
        global $post;
        
        $args = ubep()->util->meta_box_default_parser($args);
        
        if ($post->post_type == $args['post_type']){
        
            $current_metadata = get_post_meta( $post->ID, self::meta_slug($args), true );

            wp_nonce_field( self::meta_box_box_name($args), self::meta_box_nonce_name($args) );

            _printf('<label for="%1$s">%2$s</label>', $args['field_name'], $args['label']);
           switch ($args['input']){
			   case 'text':
					_printf('<input type="text" id="%1$s" name="%1$s" value="%2$s" size="%3$u" />', 
							$args['field_name'], 
							esc_attr($current_metadata),
							$args['size']
						   );
			   		break;
			   case 'date':
			   		# from: https://github.com/Automattic/Edit-Flow/blob/master/modules/editorial-metadata/editorial-metadata.php
						// TODO: Move this to a function
						if ( !empty( $current_metadata ) ) {
							// Turn timestamp into a human-readable date
							$current_metadata = $this->show_date_or_datetime( intval( $current_metadata ) );	
						}
						echo '<label for="'.$args['field_name'].'">'.$args['label'].'</label>';
						if ( !empty($args['descript']) )
							echo '<label for="'.$args['field_name'].'">'.$args['descript'].'</label>';
						echo '<input id="'.$args['field_name'].'" name="'.$args['field_name'].'" type="text" class="date-time-pick" value="'.$current_metadata.'" />';
						break;			   		
				default:
					echo $args['the_field'];

            }
        }
        
        
    }
	
	private function show_date_or_datetime( $current_date ) {

		if( date( 'Hi', $current_date ) == '0000')
			return date( 'M d Y', $current_date );
		else
			return date( 'M d Y H:i', $current_date );
	}	
    
    public function meta_box_checker($post_id, $args){
        
        $args = ubep()->util->meta_box_default_parser($args);
        
        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST[self::meta_box_nonce_name($args)] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( self::meta_box_nonce_name($args), self::meta_box_box_name($args) ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && $args['post_type'] == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }

        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
        if ( ! isset( $_POST[$args['field_name']] ) ) {
            return;
        }
        
        $data = $_POST[$args['field_name']];
        
        if ('text' == $args['input']){
            // Sanitize user input.
            $data = sanitize_text_field( $_POST[$args['field_name']] );
        }

        // Update the meta field in the database.
        update_post_meta( $post_id, self::meta_slug($args), $data );        
        
    }
    
    # via http://www.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
    public function logger($message){
    
        if (WP_DEBUG === true) {
            if (is_array($message) || is_object($message)) {
                error_log(print_r($message, true));
            } else {
                error_log($message);
            }
        }    
        
    }
    
}
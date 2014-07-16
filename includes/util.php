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
        add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts' ));
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
			      'descript'		  => $descript,
            'post_type'     => $post_type,
            'size'          => $size,
            'the_field'     => $the_field
        );
        foreach ($args as $key=>$arg){
            if (!$arg && ('the_field' != $key)){
                self::logger('Ultra Basic Event meta box field ' . $key . ' was left unset.');
            }
        }
        $args['field_name'] = ubep()->slug . '_' . $args['field_name'];
        return $args;
    }


    public function meta_box_default_parser($args){
         $default = array(
                'meta_slug'     => false,
                'label'         => ubep()->title . ' Meta Field',
                'field_name'    => false,
                'input'         => 'text',
                'descript'      => "",
                'post_type'     => ubep()->schema->post_type,
                'size'          => 25,
                'the_field'     => ''

        );
        #var_dump($args); die(0);
        $args = wp_parse_args( $args, $default );

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

	public function meta_box_maker($post, $metabox){
        global $post;
        $theseArgs = ubep()->util->meta_box_default_parser($metabox['args']);
        #var_dump($theseArgs);
        if ($post->post_type == $theseArgs['post_type']){

            $current_metadata = get_post_meta( $post->ID, self::meta_slug($theseArgs), true );

            wp_nonce_field( self::meta_box_box_name($theseArgs), self::meta_box_nonce_name($theseArgs) );

           printf('<label for="%1$s">%2$s</label>', $theseArgs['field_name'], $theseArgs['label']);
           switch ($theseArgs['input']){
			   case 'text':
					printf('<input type="text" id="%1$s" name="%1$s" value="%2$s" size="%3$u" />',
							$theseArgs['field_name'],
							esc_attr($current_metadata),
							$theseArgs['size']
						   );
			   		break;
			   case 'date':
			   		# from: https://github.com/Automattic/Edit-Flow/blob/master/modules/editorial-metadata/editorial-metadata.php
						// TODO: Move this to a function
						if ( !empty( $current_metadata ) ) {
							// Turn timestamp into a human-readable date
							$current_metadata = $this->show_date( intval( $current_metadata ) );
						}
						if ( !empty($theseArgs['descript']) )
				            echo '<label for="'.$theseArgs['field_name'].'"></label>';
						echo '<input id="'.$theseArgs['field_name'].'" class="'.$theseArgs['input'].' date-time-pick-zs-util" name="'.$theseArgs['field_name'].'" type="text" value="'.$current_metadata.'" />';
                        echo '<br />'.$theseArgs['descript'];
						break;
				default:
					echo $theseArgs['the_field'];

            }
        }


    }

	private function show_date( $current_date ) {

		  return date('m/d/Y', $current_date);

	}

    public function meta_box_checker($post_id, $args){
        #var_dump($post_id); die();
        $args = ubep()->util->meta_box_default_parser($args);

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST[self::meta_box_nonce_name($args)] ) ) {
            ubep()->util->logger('Nonce is not set.');
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST[self::meta_box_nonce_name($args)], self::meta_box_box_name($args) ) ) {
            ubep()->util->logger('Nonce is not valid.');
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && $args['post_type'] == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                ubep()->util->logger('User cannot edit_page.');
                return;
            }

        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                ubep()->util->logger('User cannot edit_post.');
                return;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
        if ( ! isset( $_POST[$args['field_name']] ) ) {
            ubep()->util->logger('field_name is not set.');
            return;
        }

        $data = $_POST[$args['field_name']];
        #var_dump($data); die();
        if ('text' == $args['input']){
            // Sanitize user input.
            $data = sanitize_text_field( $data );
        }

        if ('date' == $args['input']){
            $unix_date = strtotime($data);
            $data = $unix_date;
        }

        // Update the meta field in the database.
        update_post_meta( $post_id, self::meta_slug($args), $data );

        return $data;

    }

    public function admin_scripts(){
        $screen = get_current_screen();
        #var_dump($screen); die();
        wp_register_script('ubep-datepicker', ubep()->url . 'assets/js/datepicker-imp.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'));
        wp_register_style('jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css');
        if (('post' || 'edit' ) == $screen->base){
            wp_enqueue_style('jquery-ui-style');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('ubep-datepicker');

        }

    }

    # via http://www.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
    public function logger($message){
        #var_dump($message); die();
        if (WP_DEBUG === true) {
            if (is_array($message) || is_object($message)) {
                error_log(print_r($message, true));
            } else {
                error_log($message);
            }
        }

    }

}

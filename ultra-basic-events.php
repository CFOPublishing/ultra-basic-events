<?php

class Ultra_Basic_Events_Plugin {

    var $slug;
    var $title;
    var $menu_slug;
    var $root;
    var $file_path;
    var $url;
    var $version;
    var $schema;
    var $admin;
    var $template_tags;
    
    public static function init() {
		static $instance;

		if ( ! is_a( $instance, 'Ultra_Basic_Events_Plugin' ) ) {
			$instance = new self();
		}

		return $instance;
	}

	// See http://php.net/manual/en/language.oop5.decon.php to get a better understanding of what's going on here.
	private function __construct() {
        
        $this->$slug = 'ubep';
        $this->$title = 'Ultra Basic Events Plugin';
        $this->$menu_slug = $this->$slug . '-menu';
        $this->$root = dirname(__FILE__);
        $this->$file_path = $this->$root . '/' . basename(__FILE__);
        $this->$url = plugins_url('/', __FILE__);
        $this->$version = '0.0.1';       
        
        $this->includes();
        
        $this->schema();
        
        $this->admin();
        
        $this->template_tags();
    }

	/**
	 * Include necessary files
	 *
	 * @since 0.0.1
	 */
	function includes() {
        require_once( $this->root . '/includes/schema.php' );
        require_once( $this->root . '/includes/admin.php' );
        require_once( $this->root . '/includes/template-tags.php' );
       
    }
    
    function schema(){
 		if ( empty( $this->schema ) ) {
			$this->schema = new UBEP_Schema;
		}       
    }
    
    function admin(){
 		if ( empty( $this->admin ) ) {
			$this->schema = new UBEP_Admin;
		}       
    }
    
    function template_tags(){
 		if ( empty( $this->template_tags ) ) {
			$this->schema = new UBEP_Template_Tags;
		}       
    }    

}


/**
 * Bootstrap
 *
 * You can also use this to get a value out of the global, eg
 *
 *    $foo = ubep()->bar;
 *
 * @since 0.0.1
 */
function ubep() {
	return Ultra_Basic_Events_Plugin::init();
}

// Start me up!
ubep();
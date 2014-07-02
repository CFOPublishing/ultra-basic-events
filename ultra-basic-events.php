<?php

/*
Plugin Name: Ultra Basic Events Plugin
Plugin URI: http://cfo.com/
Description: Because all you want is a list of events and descriptions ordered by when they will happen, and not that other junk.
Version: 0.0.1
Author: Aram Zucker-Scharff
Author URI: http://aramzs.me/
License: GPL2
*/

/*  Developed for the CFO Publishing LLC

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class Ultra_Basic_Events_Plugin {

    var $slug;
    var $title;
    var $menu_slug;
    var $root;
    var $content_root;
    var $file_path;
    var $url;
    var $version;
    var $util;
    var $schema;
    var $admin;
    var $templates;
    
    public static function init() {
		static $instance;

		if ( ! is_a( $instance, 'Ultra_Basic_Events_Plugin' ) ) {
			$instance = new self();
		}

		return $instance;
	}

	// See http://php.net/manual/en/language.oop5.decon.php to get a better understanding of what's going on here.
	private function __construct() {
        
        $this->slug = 'ubep';
        $this->title = 'Ultra Basic Events Plugin';
        $this->menu_slug = $this->slug . '-menu';
        $this->root = dirname(__FILE__);
        $this->content_root = dirname(dirname(dirname(__FILE__)));
        $this->file_path = $this->root . '/' . basename(__FILE__);
        $this->url = plugins_url('/', __FILE__);
        $this->version = '0.0.1';       
        
        $this->includes();
        
        $this->util();
        
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
        
        require_once( $this->root . '/includes/util.php' );
        require_once( $this->root . '/includes/schema.php' );
        require_once( $this->root . '/includes/admin.php' );
        require_once( $this->root . '/includes/template-tags.php' );
        if (!file_exists($this->content_root . '/plugins-mu/custom-meta-boxes/custom-meta-boxes.php') && !file_exists($this->content_root . 'plugins-mu/Custom-Meta-Boxes/custom-meta-boxes.php') && !class_exists('CMB_Meta_Box')){
            require_once($this->root . '/lib/custom-meta-boxes/custom-meta-boxes.php');    
        }
       
    }
    
    function util(){
 		if ( empty( $this->util ) ) {
			$this->util = new UBEP_Util;
		}       
    }    
    
    function schema(){
 		if ( empty( $this->schema ) ) {
			$this->schema = new UBEP_Schema;
		}       
    }
    
    function admin(){
 		if ( empty( $this->admin ) ) {
			$this->admin = new UBEP_Admin;
		}       
    }
    
    function template_tags(){
 		if ( empty( $this->templates ) ) {
			$this->templates= new UBEP_Template_Tags;
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
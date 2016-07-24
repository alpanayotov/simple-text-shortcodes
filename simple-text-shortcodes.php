<?php 

/**
 * Plugin Name: Simple Text Shortcodes
 * Description: Simple plugin for creating and managing text shortcodes
 * Version: 0.2
 * Author: Alexander Panayotov
 * Author URI: https://github.com/alpanayotov
 * License: GPL2
 * Requires at least: 4.5.3
 * Tested up to: 4.5.3
 */

class Shortcodes_Manager {

	function __construct() {

		add_action( 'init', array( $this, 'create_post_type') );
		add_action( 'after_setup_theme', array( $this, 'register_shortcode') );

		add_action( 'manage_alp_shortcode_posts_custom_column', array( __CLASS__ , 'set_admin_column_content' ), 10, 2 );
		add_filter( 'manage_alp_shortcode_posts_columns', array( __CLASS__ , 'set_admin_column_head' ) );
	}

	function create_post_type(){

		$arguments = array(
			'labels' => array(
				'name'               => __('Shortcodes', 'alp'),
				'singular_name'      => __('Custom Type', 'alp'),
				'add_new'            => __('Add New', 'alp'),
				'add_new_item'       => __('Add new Custom Type', 'alp'),
				'view_item'          => __('View Custom Type', 'alp'),
				'edit_item'          => __('Edit Custom Type', 'alp'),
				'new_item'           => __('New Custom Type', 'alp'),
				'view_item'          => __('View Custom Type', 'alp'),
				'search_items'       => __('Search Shortcodes', 'alp'),
				'not_found'          => __('No Shortcodes found', 'alp'),
				'not_found_in_trash' => __('No Shortcodes found in trash', 'alp'),
			),
			'menu_icon'           => 'dashicons-list-view',
			'public'              => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'_edit_link'          => 'post.php?post=%d',
			'rewrite'             => false,
			'query_var'           => true,
			'supports'            => array('title', 'editor'),
		);

		$arguments = apply_filters( 'alp_posttype_args', $arguments );

		register_post_type( 'alp_shortcode', $arguments );
	}

	public function register_shortcode(){
		add_shortcode( 'text_block', function( $atts, $content ){

			$atts = shortcode_atts( array(
				'id'          => '',
				'description' => '',
			), $atts );

			if ( ! $atts['id'] ) {
				return; 
			}

			$shortcode_post = get_post( $atts['id'] );

			if ( empty( $shortcode_post ) ) {
				return;
			}

			return apply_filters( 'the_content', $shortcode_post->post_content );

		});
		
	}

	public static function set_admin_column_head( $columns ) {
		unset( $columns['date'] );
		
		$columns['shortcode'] = __( 'Shortcode', 'alp' );
		$columns['date']      = __( 'Date', 'alp' );

		return $columns;
	}

	public static function set_admin_column_content( $column_name, $post_id ) {
		if ( 'shortcode' === $column_name ) {
			echo '<code>[text_block id='. $post_id .' description="' . get_the_title( $post_id ) . '"]</code>';
		}
	}
}

$shortcodes_manager = new Shortcodes_Manager();
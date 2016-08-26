<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Inex_Add_Page class.
 *
 * @package inex ticket
 *
 * @since version 1.0
 *
 */
class Inex_Add_Page {


	/**
	 * pages_array
	 *
	 * (default value: null)
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @var mixed
	 * @access private
	 */
	private $pages_array = null;


	/**
	 * Inex_Add_Page::__construct()
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @param array $args various params some overidden by default
	 *
	 * @return
	 */
	public function __construct( $pages ) {

		if ( is_array( $pages ) ){

		$this->pages_array = $pages;

		$this->add_page();

		} else {

			return false;
		}

	}



	/**
	 * add_page function.
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @access public
	 * @return void
	 */
	public function add_page() {

		// Do checks only in backend
		if ( is_admin() ) {

			if ( empty( $GLOBALS['wp_rewrite'] ) )
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();

				global $wpdb;

				foreach ( $this->pages_array as $page ) {

				//if page exists $pagina->ID is the id of the page
				$pagina = get_page_by_path( $page['post_name'] );

				//Create the array for insert post
				$arg = array (
					'ID'			=> '',
	    			'post_title'	=> $page['post_title'],
	    			'post_name'		=> $page['post_name'],
	    			'post_content'	=> ( ! empty ( $page['post_content'] ) ? $page['post_content'] : $pagina->post_content) ,
	    			'post_type'		=> $page['post_type'],
	    			'post_status'	=> $page['post_status'],
	    			'post_author'	=> $page['post_author'],
 				);


				//if page doesn't exist I create it
				if ( empty( $pagina ) ) {
				    $post_id = wp_insert_post( $arg );
			    } else {
				    //if page exist add the page ID to the array to modify the right page
				    $arg['ID'] .= $pagina->ID;
				    $page_data = get_post( $pagina->ID );
				    $arg['post_content'] = $page_data->post_content;

				    //echo '<pre>ARRAY MODPAGE ::  ' . print_r( $arg, 1 ) . ' FINE ARRAY </pre>';
				    $post_id = wp_insert_post( $arg  );

			    }

			    //Verify if in data/data.php exist _wp_page_template and if exist value insert it
			    if (isset ($page[ '_wp_page_template' ]) && ! empty($page[ '_wp_page_template' ])) {
				    update_post_meta ( $post_id , '_wp_page_template' ,$page['_wp_page_template'] );
			    }
			} //end foreach

			// Use a static front page
			if ( isset($front_page) && ! empty($front_page) ) {

				$frontpage = get_page_by_path( $front_page['page-slug'] );
				if ( ! empty($frontpage) ) {
					update_option( 'page_on_front', $frontpage->ID );
					update_option( 'show_on_front', 'page' );
				}
			}

		} //end if only in the admin

	}

}// chiudo la classe

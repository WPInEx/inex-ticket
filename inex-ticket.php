<?php
/**
 * @package inex ticket
 * @author Paolo Valenti & Michele Cipriani
 * @version 1.0 first release
 */
/*
Plugin Name: INEX Ticket
Plugin URI: http://wpinex.com
Description: Ticket system by INEX
Author: Paolo Valenti & Michele Cipriani
Version: 1.0
Author URI: http://www.inex.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: inex-ticket
Domain Path: /languages
*/
/*
	Copyright 2016  Paolo Valenti & Michele Cipriani  (email : wolly66@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define ( 'INEX_TICKET_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define ( 'INEX_TICKET_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
define ( 'INEX_TICKET_PLUGIN_SLUG', basename( dirname( __FILE__ ) ) );
define ( 'INEX_TICKET_PLUGIN_VERSION', '1.0' );
define ( 'INEX_TICKET_PLUGIN_VERSION_NAME', 'inex_ticket_version' );


function inex_ticket_load_plugin_textdomain() {
    load_plugin_textdomain( 'inex-ticket', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'inex_ticket_load_plugin_textdomain' );

/* Load inex-ticket-load.php*/
try {

	$inex_ticket_load = INEX_TICKET_PLUGIN_PATH . '/inex-ticket-load.php';

	if( ! file_exists( $inex_ticket_load ) ) {

    	throw new Exception( __( 'Distributive is broken. inex-ticket-load.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

  	} else {

	  	require_once( INEX_TICKET_PLUGIN_PATH . '/inex-ticket-load.php' );

  	}

	} catch( Exception $e ) {

 		echo __( 'Caught exception: ', 'inex-ticket' ),  $e->getMessage(), "\n";
	}


/* Add add_new_ticket caps to standard WordPress roles */

register_activation_hook( __FILE__, array( 'Inex_Ticket_Base', 'add_new_ticket_caps' ) );


class Inex_Ticket_Base {

	private $options;
	/**
	 * Inex_ticket_Base::__construct()
	 *
	 *
	 * @param array $args various params some overidden by default
	 *
	 * @return
	 */

	public function __construct() {

		$this->options =  get_option( 'inex-ticket' );

		if ( empty( $this->options ) && empty( get_option( INEX_TICKET_PLUGIN_VERSION_NAME ) ) ){

			add_action( 'init', array( $this, 'first_install' ) );

		}

		//check for plugin update
		add_action( 'init', array( $this, 'update_check' ) );

		// ! TODO DEBUG DA RIMUOVERE
		//delete_option( 'inex-ticket' );

		//enqueue scripts

		add_action( 'show_user_profile', array( $this, 'promote_to_agent' ) );
		add_action( 'edit_user_profile', array( $this, 'promote_to_agent' ) );
		add_action( 'personal_options_update', array( $this, 'save_custom_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_custom_user_profile_fields' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'ticket_css_frontend' ) );

		if ( 0 == $this->options['bootstrap'] ){

			add_action( 'wp_enqueue_scripts', array( $this, 'bootstrap_frontend' ) );
			add_action( 'wp_head', array( $this, 'bootstrap_meta' ) );

		}

	}

	/**
	 * first_install function.
	 *
	 * @access public
	 * @return void
	 */
	public function first_install(){

		$status_new = wp_insert_term( __( 'New', 'inex-ticket' ), 'inex_ticket_status' );
		$status_close = wp_insert_term( __( 'Closed', 'inex-ticket' ), 'inex_ticket_status' );
		$priority = wp_insert_term( __( 'Normal', 'inex-ticket' ), 'inex_ticket_priority' );
		$type = wp_insert_term( __( 'Question', 'inex-ticket' ), 'inex_ticket_type' );

		$args =	array(
		  	'pubblica' => 'pubblico',
		  	'single_template' => 'yes',
		  	'status' => (int)$status_close['term_id'],
		  	'opening_status' => (int)$status_new['term_id'],
		  	'listing-type' => 0,
		  	'listing-priority' => 0,
		  	'listing-status-operator' => 1,
		  	'listing-status' => (int)$status_close['term_id'],
		  	'listing-tickets-per-page' => 20,
		  	'listing-ticket-replies-per-page' => 20,
		  	'sorting' => 'DESC',
		  	'background' => Array(
		  	        $status_close['term_id'] => '#81d742', //close
		  	        $status_new['term_id'] => '#dd3333', //new
		  	    ),

		  	'bootstrap' => 0,
		  	);
		 add_option('inex-ticket', $args );

		 $this->options =  get_option( 'inex-ticket' );

		 $this->add_pages();

	}

	/**
	 * update_UTILITY_check function.
	 *
	 * @access public
	 * @return void
	 */
	 public function update_check() {
	 // Do checks only in backend
	    if ( is_admin() ) {

	    	if ( version_compare( get_site_option( INEX_TICKET_PLUGIN_VERSION_NAME ), INEX_TICKET_PLUGIN_VERSION ) != 0  ) {

	    	$this->do_update();

	    	}

	 	} //end if only in the admin
	 }

	/**
	 * do_update function.
	 *
	 * @access private
	 *
	 */
	 public function do_update(){

	   	//Update option
	    update_option( INEX_TICKET_PLUGIN_VERSION_NAME , INEX_TICKET_PLUGIN_VERSION );
	 }


	public static function add_new_ticket_caps(){

		//add new caps to new roles

		// gets the administrator role
		$role = get_role( 'administrator' );
		$role->add_cap( 'add_new_ticket' );

		// gets the editor role
		$role = get_role( 'editor' );
		$role->add_cap( 'add_new_ticket' );

		// gets the author role
		$role = get_role( 'author' );
		$role->add_cap( 'add_new_ticket' );

		// gets the collaborator role
		$role = get_role( 'collaborator' );
		$role->add_cap( 'add_new_ticket' );


		// gets the subscriber role
		$role = get_role( 'subscriber' );
		$role->add_cap( 'add_new_ticket' );


	}

	public function promote_to_agent( $user ){

		if ( current_user_can( 'create_users' ) ) {

		$selected = get_user_meta( $user->ID, 'ownticket', true );


		?>

		<h3><?php _e( 'Ticket Agents', 'inex-ticket' ); ?></h3>

			<?php _e( 'This user is a Ticket Agent', 'inex-ticket' ); ?> <input type="checkbox" name="ownticket" value="1" <?php checked( $selected, 1 ); ?> />


		<?php	}

	}

	public function save_custom_user_profile_fields( $user ) {


		if ( isset( $_POST['ownticket'] ) && 1 == $_POST['ownticket'] ) {

			update_user_meta( $user, 'ownticket', $_POST['ownticket'] );

		} else {

			delete_user_meta( $user, 'ownticket' );
		}
	}

	/**
     * Enqueue plugin style-file
     */
    function ticket_css_frontend() {
        // Respects SSL, Style.css is relative to the current file

        //wp_register_style( 'inex_gant_screen', plugins_url('styles/css/screen.css' , __FILE__ ));
        wp_register_style( 'ticket_css_frontend', plugins_url('/css/ticket.css' , __FILE__ ));
        //wp_enqueue_style( 'inex_gant_screen' );
        wp_enqueue_style( 'ticket_css_frontend' );
    }

    function bootstrap_frontend() {
    // Check if Bootstrap is loaded, if not queue up script
		if ( ! is_admin() ){
		if( ! wp_script_is( 'bootstrap', 'enqueued' ) && ! wp_style_is( 'bootstrap', 'queue' ) && ! wp_style_is( 'bootstrap', 'done' ) ) {
			wp_register_style( 'bootstrap', plugins_url('/libs/bootstrap/css/bootstrap.min.css' , __FILE__ ) );
	        wp_enqueue_style( 'bootstrap' );
	        wp_register_style( 'bootstrap-theme', plugins_url('/libs/bootstrap/css/bootstrap-theme.min.css' , __FILE__ ) );
	       	wp_enqueue_style( 'bootstrap-theme' );

	        wp_enqueue_script( 'bootstrap','' . INEX_TICKET_PLUGIN_DIR . 'libs/bootstrap/js/bootstrap.min.js', array('jquery'), time(), true );
		}
		}
	}

	function bootstrap_meta(){
			/*echo '
				<!-- Start INEX Ticket -->
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<!-- End INEX Ticket -->
			';*/
	}

	/**
	 * add_pages function.
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 * @access private
	 * @return void
	 */
	private function add_pages(){

		$user_ID = get_current_user_id();

	    //Sample Pages array

		//****************************************************************************
		// Array completo con tutti i dati, shortcode in post_content
		// Eliminare i campi che non servono
		// array(
		//		'post_title' => 'Post title',
		//		'post_name' => 'Post slug',
		//		'post_content' => 'content',
		//		'post_type' => 'page',
		//		'post_status' => 'publish',
		//		'post_author' => 1,
		//		'_wp_page_template' => 'template.php'
		// ),
		//
		//*****************************************************************************

		$pages =  array(
			array(
				'post_title' => __( 'New Ticket', 'inex-ticket' ),
				/* translators: this is a slug, no space between words, use - */
				'post_name' => __( 'new-ticket', 'inex-ticket' ),
				'post_content' => '[inex-ticket]',
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_author' => $user_ID,
				'_wp_page_template' => ''
			),

			array(
				'post_title' => __( 'List Tickets', 'inex-ticket' ),
				/* translators: this is a slug, no space between words, use - */
				'post_name' => __( 'list-tickets', 'inex-ticket' ),
				'post_content' => '[inex-list-ticket]',
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_author' => $user_ID,
				'_wp_page_template' => ''
			),

		);

		$add_pages = new Inex_Add_Page( $pages );

	}

}// close the class

//class instance
$inex_ticket_base = new Inex_Ticket_Base();
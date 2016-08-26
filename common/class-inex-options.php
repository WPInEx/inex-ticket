<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Inex_Options class.
 *
 * @package inex ticket
 *
 * @since version 1.0
 *
 */
class Inex_Options{
    /**
     * Holds the values to be used in the fields callbacks
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     */
    private $options;

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct(){

        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_menu', array( $this, 'register_inex_menu_page' ) );

    }


	/**
	 * register_inex_menu_page function.
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @access public
	 * @return void
	 */
	public function register_inex_menu_page(){

		add_menu_page( 'INEX Settings', 'INEX Settings', 'manage_options', 'inex-settings', array( $this, 'create_admin_page' ) );

	}

    /**
     * create_admin_page function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function create_admin_page(){

        $this->options = get_option( 'inex-settings' );
        ?>
        <div class="wrap">
            <h2><?php _e( 'Inex Settings', 'inexticket' ) ?></h2>


            <form method="post" action="options.php">
            <?php

                // This prints out all hidden setting fields
                settings_fields( 'inex_general_option_group' );
                do_settings_sections( 'inex-settings' );
                //submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * page_init function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function page_init(){

        register_setting(
            'inex_general_option_group', // Option group
            'inex-settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'inex-01', // ID
            __( 'General Settings', 'inexticket' ) , // Title
            array( $this, 'print_section_inex_callback' ), // Callback
            'inex-settings' // Page
        );

        add_settings_field(
            'description',
            __( 'Inex', 'inexticket' ),
            array( $this, 'inex_callback' ),
            'inex-settings',
            'inex-01'
        );

        	}

    /**
     * Sanitize each setting field as needed
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ){

        if( isset( $input['pubblica'] ) ){

	        if ( 'pubblico' == $input['pubblica'] || 'privato' == $input['pubblica'] ){

            	$new_input['pubblica'] = $input['pubblica'] ;

            }
        }


        return $new_input;
    }

    /**
     * print_section_pubblica_privata function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function print_section_inex_callback(){

        print _e( 'InEx Settings:', 'inexticket' );
    }



    /**
     * inex_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function inex_callback(){

	    print _e( 'We are InEx - Intranet Extranet experience - resistence is futile!', 'inexticket' );

    }




}// close the class

if( is_admin() ){

    	$inex_options = new Inex_Options();

}
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Inex_Ticket_Options class.
 *
 * @package inex ticket
 *
 * @since version 1.0
 *
 */
class Inex_Ticket_Options
{
    /**
     * Holds the values to be used in the fields callbacks
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     */
    private $options;
    private $options_add_pages;

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct(){

        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_menu', array( $this, 'register_inex_ticket_menu_page' ) );
		//
		//
        add_action( 'admin_enqueue_scripts', array( $this, 'add_color_picker' ) );
    }


	/**
	 * register_inex_ticket_menu_page function.
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @access public
	 * @return void
	 */
	public function register_inex_ticket_menu_page(){

		add_submenu_page( 'inex-settings', 'INEX Tickets Settings', 'INEX Tickets Settings', 'manage_options', 'inex-ticket', array( $this, 'create_admin_page' ) );

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

        $this->options = get_option( 'inex-ticket' );
        ?>
        <div class="wrap">
            <h2><?php _e( 'Tickets Settings', 'inex-ticket' ) ?></h2>


            <form method="post" action="options.php">
            <?php

                // This prints out all hidden setting fields
                settings_fields( 'inex_option_group' );
                do_settings_sections( 'inex-ticket' );
                submit_button();
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
            'inex_option_group', // Option group
            'inex-ticket', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'inex-01', // ID
            __( 'General Settings', 'inex-ticket' ) , // Title
            array( $this, 'print_section_pubblica_privata' ), // Callback
            'inex-ticket' // Page
        );

        add_settings_field(
            'pubblica',
            __( 'Set if Private or public', 'inex-ticket' ),
            array( $this, 'pubblica_privata_callback' ),
            'inex-ticket',
            'inex-01'
        );

        add_settings_field(
            'single_template',
            __( 'Use embedded template', 'inex-ticket' ),
            array( $this, 'single_template_callback' ),
            'inex-ticket',
            'inex-01'
        );

        add_settings_section(
            'inex-02', // ID
            __( 'Set opening and closing status', 'inex-ticket' ), // Title
            array( $this, 'print_section_opening_closing_status' ), // Callback
            'inex-ticket' // Page
        );

		add_settings_field(
            'opening',
            __( 'Opening status', 'inex-ticket' ),
            array( $this, 'opening_status_callback' ),
            'inex-ticket',
            'inex-02'
        );

        add_settings_field(
            'closing',
            __( 'Closing status', 'inex-ticket' ),
            array( $this, 'closing_status_callback' ),
            'inex-ticket',
            'inex-02'
        );

        add_settings_section(
            'inex-03', // ID
            __( 'Set standard listing', 'inex-ticket' ), // Title
            array( $this, 'print_section_standard_listing' ), // Callback
            'inex-ticket' // Page
        );

        add_settings_field(
            'listing-type',
            __( 'Listing type', 'inex-ticket' ),
            array( $this, 'listing_type_callback' ),
            'inex-ticket',
            'inex-03'
        );
        add_settings_field(
            'listing-priority',
            __( 'Listing priority', 'inex-ticket' ),
            array( $this, 'listing_priority_callback' ),
            'inex-ticket',
            'inex-03'
        );
        add_settings_field(
            'listing-status',
            __( 'Listing status', 'inex-ticket' ),
            array( $this, 'listing_status_callback' ),
            'inex-ticket',
            'inex-03'
        );
        add_settings_field(
            'listing-tickets-per-page',
            __( 'Listing tickets per page', 'inex-ticket' ),
            array( $this, 'listing_tickets_per_page_callback' ),
            'inex-ticket',
            'inex-03'
        );

		 add_settings_field(
            'listing-ticket-replies-per-page',
            __( 'Listing tickets replies per page', 'inex-ticket' ),
            array( $this, 'listing_ticket_replies_per_page_callback' ),
            'inex-ticket',
            'inex-03'
        );

		add_settings_field(
            'listing-status-bg-color',
            __( 'Choose color for each status', 'inex-ticket' ),
            array( $this, 'listing_status_bg_color_callback' ),
            'inex-ticket',
            'inex-03'
        );

        add_settings_section(
            'inex-04', // ID
            __( 'Layout', 'inex-ticket' ), // Title
            array( $this, 'print_section_layout' ), // Callback
            'inex-ticket' // Page
        );

        add_settings_field(
            'import-bootstrap',
            __( 'Import Bootstrap', 'inex-ticket' ),
            array( $this, 'layout_bootstrap_callback' ),
            'inex-ticket',
            'inex-04'
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

        if( isset( $input['single_template'] ) ){

	        if ( 'yes' == $input['single_template'] || 'no' == $input['single_template'] ){

            	$new_input['single_template'] = $input['single_template'] ;

            }

        }

        if( isset( $input['status'] ) ){

            $new_input['status'] = absint( $input['status'] );

        }

        if( isset( $input['opening_status'] ) ){

            $new_input['opening_status'] = absint( $input['opening_status'] );

        }

        if( isset( $input['listing-type'] ) ){

            $new_input['listing-type'] = absint( $input['listing-type'] );

        }

        if( isset( $input['listing-priority'] ) ){

            $new_input['listing-priority'] = absint( $input['listing-priority'] );

        }

        if( isset( $input['listing-status-operator'] ) ){

	         if ( 0 == $input['listing-status-operator'] || 1 == $input['listing-status-operator'] ){

			 	$new_input['listing-status-operator'] = $input['listing-status-operator'];

            }
        }

		if( isset( $input['listing-status'] ) ){

            $new_input['listing-status'] = absint( $input['listing-status'] );

        }

		if( isset( $input['listing-tickets-per-page'] ) ){

            $new_input['listing-tickets-per-page'] = absint( $input['listing-tickets-per-page'] );

        }

        if( isset( $input['listing-ticket-replies-per-page'] ) ){

            $new_input['listing-ticket-replies-per-page'] = absint( $input['listing-ticket-replies-per-page'] );

        }

        if( isset( $input['background'] ) ){

	        foreach ( $input['background'] as $key => $in ){

		        $result = $this->check_color( $in );

		        if ( true == $result ){

			        $new_input['background'][$key] = $in;
		        }


	        }
        }

        if( isset( $input['bootstrap'] ) ){

	        if ( 0 == $input['bootstrap'] || 1 == $input['bootstrap'] ){

            	$new_input['bootstrap'] = $input['bootstrap'];

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
    public function print_section_pubblica_privata(){

        print _e( 'Settings:', 'inex-ticket' );
    }


    /**
     * print_section_opening_closing_status function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function print_section_opening_closing_status(){

        print _e( 'Opening and Closing Status Settings:', 'inex-ticket' );
    }


    /**
     * print_section_standard_listing function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function print_section_standard_listing(){

        print _e( 'Set standard Settings for Tickets listing: ', 'inex-ticket' );
    }


    /**
     * print_section_layout function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function print_section_layout(){

        print _e( 'Layout options:', 'inex-ticket' );
    }

    /**
     * pubblica_privata_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function pubblica_privata_callback(){

	    print '<ul>
	    		<li><input type="radio" name="inex-ticket[pubblica]" value="pubblico" ' . checked( $this->options["pubblica"], "pubblico", false ) . '/> ' . __( 'Public platform', 'inex-ticket' ) . '</li>
	    		<li><input type="radio" name="inex-ticket[pubblica]" value="privato" ' . checked( $this->options["pubblica"], "privato", false ) . '/> ' . __( 'Private platform', 'inex-ticket' ) . '</li>
				</ul>';

    }

    /**
     * single_template_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function single_template_callback(){

	    print '<ul><li><input type="radio" name="inex-ticket[single_template]" value="yes" ' . checked( $this->options["single_template"], "yes", false ) . '/> ' . __( 'Use single template embedded in inex plugin', 'inex-ticket' ) . '</li>
	         <li><input type="radio" name="inex-ticket[single_template]" value="no" ' . checked( $this->options["single_template"], "no", false ) . '/> ' . __( 'Use your theme single template', 'inex-ticket' ) . '</li></ul>';

    }

    /**
     * opening_status_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function opening_status_callback(){

	    // no default values. using these as examples
		$taxonomies = array(
						'inex_ticket_status',
						);

		$args = array(
		    'orderby'           => 'name',
		    'order'             => 'ASC',
		    'hide_empty'        => false,

		);

		$status = get_terms($taxonomies, $args);

		if ( ! empty( $status ) ){
			print '<select name="inex-ticket[opening_status]">';

			print '<option value="-1" ' . selected( $this->options['opening_status'], -1 ) . '>' . __( 'Select tickets opened Status', 'inex-ticket' ) . '</option>';



			foreach ( $status as $st ){



				print '<option value="' . $st->term_id . '" ' . selected( $this->options['opening_status'], $st->term_id ) . '>' . $st->name . '</option>';
			}
			print '</select>';
			}
    }

    /**
     * closing_status_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function closing_status_callback(){

	    // no default values. using these as examples
		$taxonomies = array(
						'inex_ticket_status',
						);

		$args = array(
		    'orderby'           => 'name',
		    'order'             => 'ASC',
		    'hide_empty'        => false,

		);

		$status = get_terms($taxonomies, $args);

		if ( ! empty( $status ) ){
			print '<select name="inex-ticket[status]">';

			print '<option value="-1" ' . selected( $this->options['status'], -1 ) . '>' . __( 'Select tickets closed Status', 'inex-ticket' ) . '</option>';



			foreach ( $status as $st ){



				print '<option value="' . $st->term_id . '" ' . selected( $this->options['status'], $st->term_id ) . '>' . $st->name . '</option>';
			}
			print '</select>';
			}
    }

    /**
     * listing_type_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function listing_type_callback(){

	    // no default values. using these as examples
		$taxonomies = array(
						'inex_ticket_type',
						);

		$args = array(
		    'orderby'           => 'name',
		    'order'             => 'ASC',
		    'hide_empty'        => false,

		);

		$status = get_terms($taxonomies, $args);

		if ( ! empty( $status ) ){
			print '<select name="inex-ticket[listing-type]">';

			print '<option value="0" ' . selected( $this->options['listing-type'], 0 ) . '>' . __( 'All Types', 'inex-ticket' ) . '</option>';



			foreach ( $status as $st ){



				print '<option value="' . $st->term_id . '" ' . selected( $this->options['listing-type'], $st->term_id ) . '>' . $st->name . '</option>';
			}
			print '</select>';
			}
    }

    /**
     * listing_priority_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function listing_priority_callback(){

	    $taxonomies = array(
						'inex_ticket_priority',
						);

		$args = array(
		    'orderby'           => 'name',
		    'order'             => 'ASC',
		    'hide_empty'        => false,

		);

		$status = get_terms($taxonomies, $args);

		if ( ! empty( $status ) ){
			print '<select name="inex-ticket[listing-priority]">';

			print '<option value="0" ' . selected( $this->options['listing-priority'], 0 ) . '>' . __( 'All Priority', 'inex-ticket' ) . '</option>';



			foreach ( $status as $st ){



				print '<option value="' . $st->term_id . '" ' . selected( $this->options['listing-priority'], $st->term_id ) . '>' . $st->name . '</option>';
			}
			print '</select>';
		}
    }

    /**
     * listing_status_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function listing_status_callback(){

		$status = $this->get_all_status_terms();

		if ( ! empty( $status ) ){

            if(isset($this->options["listing-status-operator"])) $listing_status_operator=$this->options["listing-status-operator"];
            else $listing_status_operator=0;

			print '
                <ul>
                    <li>
                        <input type="radio" name="inex-ticket[listing-status-operator]" value="1" '.
                            checked( $listing_status_operator, 1, false ).
                        '/>'.
                        __( 'Is Not', 'inex-ticket' ).'
                    </li>
                    <li>
                        <input type="radio" name="inex-ticket[listing-status-operator]" value="0" '.
                            checked( $listing_status_operator, 0, false ).
                        '/>'.
                        __( 'Is', 'inex-ticket' ).'
                    </li>
                </ul>
            ';

			print '<select name="inex-ticket[listing-status]">';

			print '<option value="0" ' . selected( $this->options['listing-status'], 0 ) . '>' . __( 'All Status', 'inex-ticket' ) . '</option>';



			foreach ( $status as $st ){



				print '<option value="' . $st->term_id . '" ' . selected( $this->options['listing-status'], $st->term_id ) . '>' . $st->name . '</option>';
			}
			print '</select>';
		}
    }

	/**
	 * listing_tickets_per_page_callback function.
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @access public
	 * @return void
	 */
	public function listing_tickets_per_page_callback(){

	    printf(
            '<input type="text" id="listing-tickets-per-page" name="inex-ticket[listing-tickets-per-page]" value="%s" />',
            isset( $this->options['listing-tickets-per-page'] ) ? esc_attr( $this->options['listing-tickets-per-page']) : ''
        );

    }

    /**
     * listing_ticket_replies_per_page_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function listing_ticket_replies_per_page_callback(){

	    printf(
            '<input type="text" id="listing-ticket-per-page" name="inex-ticket[listing-ticket-replies-per-page]" value="%s" />',
            isset( $this->options['listing-ticket-replies-per-page'] ) ? esc_attr( $this->options['listing-ticket-replies-per-page']) : ''
        );

    }

    /**
     * listing_status_bg_color_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function listing_status_bg_color_callback(){

	    $status = $this->get_all_status_terms();

	    echo '<table>';

	    foreach ( $status as $st ){
		echo '<tr>';
		echo '<td>' . $st->name . '</td>';
		echo '<td>';
		printf(
            '<input type="text"  name="inex-ticket[background][' . $st->term_id . ']" value="%s"  class="ticket-color-picker"/>',
            isset( $this->options['background'][$st->term_id] ) ? $this->options['background'][$st->term_id] : ''
        );
		echo '</td>';
		echo '</tr>';
		}

		echo '</table>';
    }

    /**
     * layout_bootstrap_callback function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @return void
     */
    public function layout_bootstrap_callback(){
        if(isset($this->options["bootstrap"])) $bootstrap=$this->options["bootstrap"];
        else $bootstrap=0;

        print '
            <p>'.__( 'INEX Ticket needs <a target="_BLANK" href="http://getbootstrap.com/" >Bootstrap</a>, you may disable it if your theme already imports it', 'inex-ticket' ).'</p>
            <ul>
                <li>
                    <input type="radio" name="inex-ticket[bootstrap]" value="1" '.
                        checked( $bootstrap, 1, false ).
                    '/>'.
                    __( 'Do not import Bootstrap', 'inex-ticket' ).'
                </li>
                <li>
                    <input type="radio" name="inex-ticket[bootstrap]" value="0" '.
                        checked( $bootstrap, 0, false ).
                    '/>'.
                    __( 'Import Bootstrap', 'inex-ticket' ).'
                </li>
            </ul>
        ';
    }

    /**
     * add_color_picker function.
     *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
     *
     * @access public
     * @param mixed $hook
     * @return void
     */
    public function add_color_picker( $hook ){

    if( is_admin() ){

        // Add the color picker css file
        wp_enqueue_style( 'wp-color-picker' );

        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'ticket-color-picker', INEX_TICKET_PLUGIN_DIR . '/js/ticket-color-picker.js', array( 'wp-color-picker' ), false, true );
    	}
	}

	/**
	 * check_color function.
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function check_color( $value ){

    	if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #

        	return true;

    	}

		return false;
	}

	/**
	 * get_all_status_terms function.
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 *
	 * @access private
	 * @return void
	 */
	private function get_all_status_terms(){

		// no default values. using these as examples
		$taxonomies = array(
						'inex_ticket_status',
						);

		$args = array(
		    'orderby'           => 'name',
		    'order'             => 'ASC',
		    'hide_empty'        => false,

		);

		$status = get_terms($taxonomies, $args);

		return $status;

	}

}// close the class

if( is_admin() ){
    $inex_ticket_options = new Inex_Ticket_Options();
    }
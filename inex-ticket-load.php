<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
	/**
	 * @package inex ticket load
	 * @author Wolly
	 * @version 1.0
	 * @date 27/05/2016
	 * Load classes and dependencies
	 */
$options = get_option( 'inex-ticket' );

try {

	// Common files and classes

    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/common/class-inex-add-pages.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-add-pages.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/common/class-inex-add-pages.php' );

    }

    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/common/class-inex-options.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-ticket-options.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/common/class-inex-options.php' );

    }

    if ( 'yes' == $options['single_template'] ){

	    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-single-ticket-template.php' ) ) {

        	throw new Exception( __( 'Distributive is broken. class-inex-single-ticket-template.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    	} else {

	    	require_once( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-single-ticket-template.php' );

	    	$inex_single_ticket_template = new Inex_Single_Ticket_Template( 'inex-ticket' );

    }
    }


	// InEx ticket files
	if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/inex-ticket-cpt.php' ) ) {

        throw new Exception( __( 'Distributive is broken. inex-ticket-cpt.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/inc/inex-ticket-cpt.php' );

    }



	if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-ticket-options.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-ticket-options.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-ticket-options.php' );

    }

    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-insert-ticket-front-end.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-insert-ticket-front-end.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-insert-ticket-front-end.php' );

    }

    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-ticket-functions.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-ticket-functions.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-ticket-functions.php' );

    }

    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-ticket-reply-loop.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-ticket-reply-loop.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-ticket-reply-loop.php' );

    }

    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-check.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-check.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-check.php' );

    }

    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-ticket-meta.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-ticket-meta.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-ticket-meta.php' );

    }

    if( ! file_exists( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-sendmail.php' ) ) {

        throw new Exception( __( 'Distributive is broken. class-inex-sendmail.php is missed. Try to remove and upload plugin again.', 'inex-ticket' ) );

    } else {

	    require_once( INEX_TICKET_PLUGIN_PATH . '/inc/class-inex-sendmail.php' );

    }




	} catch( Exception $e ) {

     	echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}



/**
 * Inex_Check class.
 *
 * @package inex ticket
 *
 * @since version 1.0
 *
 */
class Inex_Check {

	var $id_user = '';
	var $ticket_status = '';
	var $id_ticket = '';
	var $options = '';
	var $pubblico = '';
	var $read = '';
	var $write = '';
	var $company_id = '';
	var $view_only_your_tickets = '';

	/**
	 * Inex_Check::__construct()
	 *
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

	public function __construct( $args = array() ) {

		$this->id_user = absint( $args['user_id'] );
		$this->ticket_status = absint( $args['status'] );
		$this->id_ticket = absint( $args['ticket_id'] );
		$this->pubblico = $args['pubblico'];
		$this->read = $args['read'];

		if ( false == $args['write'] || true == $args['write'] ){

			$this->write = $args['write'];

		} else {

			$this->write = false;
		}

		$this->company_id = $args['company_id'];

		if ( false == $args['view_only_your_tickets'] || true == $args['view_only_your_tickets'] ){

			$this->view_only_your_tickets = $args['view_only_your_tickets'];

		} else {

			$this->view_only_your_tickets = false;
		}


		$this->options = get_option( 'inex-ticket' );

	}

	/**
	 * check_user_permissions function.
	 *
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 * @access public
	 * @return void
	 */
	public function check_user_permissions(){

		if ( false == $this->write ){

			$user_can_write = false;

		} else {

			$user_can_write = true;
		}

		return $user_can_write;
	}

	/**
	 * check_ticket_status function.
	 *
	 *
	 * @package inex ticket
	 *
	 * @since version 1.0
	 *
	 * @access public
	 * @return void
	 */
	public function check_ticket_status(){

			if ( (int)$this->options['status'] == (int)$this->ticket_status ){

				$this->ticked_is_open = false;

			} else {

				$this->ticked_is_open = true;

			}

		return $this->ticked_is_open;

	}

}// chiudo la classe


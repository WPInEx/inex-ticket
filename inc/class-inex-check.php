<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
		$this->write = $args['write'];
		$this->company_id = $args['company_id'];
		$this->view_only_your_tickets = $args['view_only_your_tickets'];


		$this->options = get_option( 'inex-ticket' );

	}

	public function check_user_permissions(){


		$author_id = get_the_author_meta( 'ID' );


		if ( $this->id_user != $author_id ){

		$company_author = get_user_meta( $author_id, 'company_associata', true );
		$company_user = get_user_meta( $this->id_user, 'company_associata', true );



		if ( $company_author == $company_user || current_user_can( 'manage_options' ) ){

			//echo 'Ciao Autore: ' . $company_author . ' - User: ' . $company_user;

		//wp_die();
		switch ( $this->ticket_status ) {

			case 'write_comment':
				if ( user_can( $this->id_user, 'company_tickets_others_view_post_comment' ) || current_user_can( 'manage_options' ) ){

					$write = 'write';

				return $write;

			} else {

				return false;
			}

			break;

			case 'view_comment':

				if ( user_can( $this->id_user, 'company_tickets_others_view_comments' ) ||current_user_can( 'manage_options' ) ){

					$read = 'read';
				return $read;

			} else {

				return false;
			}
			break;

			default:
				return false;
			break;
		}

		}//end check company

		} else {//end if different users

			switch ( $this->ticket_status ) {

				case 'write_comment':
					$write = 'write';
					return $write;
				break;
				case 'view_comment':
					$read = 'read';
					return $read;
				break;

			}

		}

	}

	public function check_ticket_status(){

		$term_status = get_the_terms( $this->id_ticket, 'inex_ticket_status' );

			if ( (int)$this->options['status'] == (int)$term_status[0]->term_id ){

				$this->ticked_is_open = false;

			} else {

				$this->ticked_is_open = true;

			}

		return $this->ticked_is_open;

	}

	private function sanitize_args(){


	}



}// chiudo la classe


<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

	Class Inex_SendMail {

		var $ticket_data = array();
		var $ticket_type = '';
		var $ticket_owners;

		public function __construct( $args = '', $type = '' ) {

		if ( is_array( $args ) ){

			$this->ticket_data = $args;
			$this->ticket_type = $type;

			$this->get_all_ticket_owners();
			}

		}

		private function get_all_ticket_owners(){


			$args = array(

				'meta_key'     => 'ownticket',
				'meta_value'   => 1,
				'meta_compare' => '=',
				'orderby'      => 'login',
				'order'        => 'ASC',
				'fields'       => 'all'
				);
			$owners = get_users( $args );

			$this->ticket_owners = $owners;
		}

		public function send_mail(){

			if ( 'new' == $this->ticket_type ){

				foreach ( $this->ticket_owners as $ow ){
					$subject = '';
					$message = '';

					$to = $ow->user_email;

					$subject .= __( 'New Ticket: ', 'inex-ticket' );
					$subject .= $this->ticket_data['ticket_title'];

					$message .= __( 'Ticket Title: ', 'inex-ticket' );
					$message .=  $this->ticket_data['ticket_title'];
					$message .= "\r\n\r\n";
					$message .= __( 'Ticket Content: ', 'inex-ticket' );
					$message .=  $this->ticket_data['description'];
					$message .= "\r\n\r\n";
					$message .= __( ' permalink: ', 'inex-ticket' );
					$message .= $this->ticket_data['ticket_permalink'];
					$message .= "\r\n\r\n";

					wp_mail( $to, $subject, $message );
				}

			} else {

				foreach ( $this->ticket_owners as $ow ){

					$subject = '';
					$message = '';

					$to = $ow->user_email;

					$subject .= __( 'New Ticket Reply: ', 'inex-ticket' );
					$subject .= $this->ticket_data['ticket_title'];

					$message .= __( 'Ticket Title: ', 'inex-ticket' );
					$message .=  $this->ticket_data['ticket_title'];
					$message .= "\r\n\r\n";
					$message .= __( 'Ticket Content: ', 'inex-ticket' );
					$message .=  $this->ticket_data['description'];
					$message .= "\r\n\r\n";
					$message .= __( ' permalink: ', 'inex-ticket' );
					$message .= $this->ticket_data['ticket_permalink'];
					$message .= "\r\n\r\n";

					wp_mail( $to, $subject, $message );
				}

				//send email to ticket reply author

				$reply_author_user_data = get_userdata( $this->ticket_data['reply_author'] );
				$to = $reply_author_user_data->user_email;

				wp_mail( $to, $subject, $message );

				//send email to ticket owner

				if (  array_key_exists( 'ticket_owner', $this->ticket_data ) && -1 != $this->ticket_data['ticket_owner']  ){

				$reply_author_user_data = get_userdata( $this->ticket_data['ticket_owner'] );
				$to = $reply_author_user_data->user_email;

				wp_mail( $to, $subject, $message );
				}



			}
		}

	}
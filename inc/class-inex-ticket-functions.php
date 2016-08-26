<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


	/**
	 * inex_check_capabilities function.
	 *
	 * @access public
	 * @param mixed $user_id
	 * @param mixed $status (default: null)
	 * @return void
	 */
	function inex_check_capabilities( $user_id, $status = null ){


		$author_id = get_the_author_meta( 'ID' );


		if ( $user_id != $author_id ){

		$company_author = get_user_meta( $author_id, 'company_associata', true );
		$company_user = get_user_meta( $user_id, 'company_associata', true );



		if ( $company_author == $company_user || current_user_can( 'manage_options' ) ){

			//echo 'Ciao Autore: ' . $company_author . ' - User: ' . $company_user;

		//wp_die();
		switch ( $status ) {

			case 'write_comment':
				if ( user_can( $user_id, 'company_tickets_others_view_post_comment' ) || current_user_can( 'manage_options' ) ){

					$write = 'write';

				return $write;

			} else {

				return false;
			}

			break;

			case 'view_comment':

				if ( user_can( $user_id, 'company_tickets_others_view_comments' ) ||current_user_can( 'manage_options' ) ){

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

			switch ( $status ) {

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



	/**
	 * Inex_Ticket_Functions class.
	 */
	class Inex_Ticket_Functions {

		//A static member variable representing the class instance
		private static $_instance = null;

		var $options = null;
		var $company_user = null;
		var $ticked_opened_age = null;
		var $ticket_last_comment = null;
		var $ticket_id = null;
		var $ticked_is_open = null;
		var $meta_or_update = null;
		var $priority = array();
		var $status = array();
		var $type = array();
		var $type_args = '';
		var $status_args = '';
		var $priority_args = '';

		/**
		 * Inex_Ticket_Functions::__construct()
		 * Locked down the constructor, therefore the class cannot be externally instantiated
		 *
		 * @param array $args various params some overidden by default
		 *
		 * @return
		 */

		private function __construct() {

			$this->options = get_option( 'inex-ticket' );

			add_filter( 'the_title', array( $this, 'add_numerator_to_ticket_title' ), 10, 2 );

			add_shortcode('inex-list-ticket', array( $this, 'list_all_tickets' ) );

			add_action( 'comment_post', array( $this, 'ticket_save_comment' ) );

			add_action( 'comment_form_top', array( $this, 'modify_ticket_data') );


		}

		/**
		 * Inex_Ticket_Functions::__clone()
		 * Prevent any object or instance of that class to be cloned
		 *
		 * @return
		 */
		public function __clone() {
			trigger_error( "Cannot clone instance of Singleton pattern ...", E_USER_ERROR );
		}

		/**
		 * Inex_Ticket_Functions::__wakeup()
		 * Prevent any object or instance to be deserialized
		 *
		 * @return
		 */
		public function __wakeup() {
			trigger_error( 'Cannot deserialize instance of Singleton pattern ...', E_USER_ERROR );
		}

		/**
		 * Inex_Ticket_Functions::getInstance()
		 * Have a single globally accessible static method
		 *
		 * @param mixed $args
		 *
		 * @return
		 */
		public static function getInstance( $args = array() ) {
			if ( ! is_object( self::$_instance ) )
				self::$_instance = new self( $args );

			return self::$_instance;


		}


		/**
		 * add_numerator_to_ticket_title function.
		 *
		 * @access public
		 * @param mixed $title
		 * @param mixed $id (default: null)
		 * @return $title
		 */
		public function add_numerator_to_ticket_title( $title, $id = null ){

			if ( is_singular( 'inex-ticket' ) ){

				$numerator = get_post_meta( $id, 'inex_ticket_number', true);

				$title =  $numerator . ' ' . $title;

			}

			return $title;
		}

		/**
		 * list_all_tickets function.
		 *
		 * The starting point
		 *
		 * @access public
		 * @return $html
		 */
		public function list_all_tickets(){


			//New ticket meta instance
			$inex_ticket_meta = new Inex_Ticket_Meta();

			//get standard ticket listing
			$all_args = $inex_ticket_meta->std_list();

			$this->type_args = $all_args['type'];
			$this->status_args = $all_args['status'];
			$this->priority_args = $all_args['priority'];

			if ( is_user_logged_in() ){

				$id_user = get_current_user_id();
			} else {

				$id_user = '';
			}

			$standard_permissions = array(
									'read' => true,
									'write' => true,
									'view_only_your_tickets' => false,
									'pubblico' => $this->options['pubblica'],
									'user_id' => (int)$id_user,
									'status' => null,
									'ticket_id' => null,
									'company_id' => null,
									);

			$filtered_permissions = apply_filters( 'inex_permissions', $standard_permissions );


			$html = '';

			if ( true == $filtered_permissions[ 'view_only_your_tickets' ] ){

				$author = $filtered_permissions['user_id'];
			} else {

				$author = '';

			}
			// check lists is public or private and check  if user is logged in
			if ( 'pubblico' == $filtered_permissions['pubblico'] || ( 'privato' == $filtered_permissions['pubblico'] && is_user_logged_in() ) ){



			$paged = ( get_query_var( 'paged' ) && ! isset ( $_GET['filter'] ) ) ? get_query_var( 'paged' ) : 1;

			//standard query arguments
			$type_return = $this->type_array( $this->options['listing-type'] );
			$type_array = $type_return['query'];
			$type_selected = $type_return['selected'];

			$priority_return = $this->priority_array( $this->options['listing-priority'] );
			$priority_array = $priority_return['query'];
			$priority_selected = $priority_return['selected'];

			$status_return = $this->status_array( $this->options['listing-status'], $this->options['listing-status-operator'] );
			$status_array = $status_return['query'];
			$status_selected = $status_return['selected'];
			$status_operator_selected = $this->options['listing-status-operator'];

			$posts_per_page = $this->options['listing-tickets-per-page'];

			if ( $_POST || $_GET ){

				//$paged = 1;

				$user_caps = get_userdata( get_current_user_id() );

				$type_return = $this->type_array( $_REQUEST['type'] );
				$type_array = $type_return['query'];
				$type_selected = $type_return['selected'];
				$this->type_args['selected'] = $type_selected;

				$priority_return = $this->priority_array( $_REQUEST['priority'] );
				$priority_array = $priority_return['query'];
				$priority_selected = $priority_return['selected'];
				$this->priority_args['selected'] = $priority_selected;

				$status_return = $this->status_array( $_REQUEST['status'], $_REQUEST['status_operator'] );
				$status_array = $status_return['query'];
				$status_selected = $status_return['selected'];
				$this->status_args['selected'] = $status_selected;

				$status_operator_selected = $_REQUEST['status_operator'];

				}//end if $_POST

			$args = array(
			'post_type'	=>	'inex-ticket',
			'author'	=>	$author,
			'tax_query'	=> array(
			  		'relation' => 'AND',
			  			$status_array,
			  			$priority_array,
			  			$type_array,
			  			),
			'posts_per_page' => $posts_per_page,
			'paged'         => $paged
			);



			$list_ticket = new WP_Query( $args );

			$html .= '
			<section class="inexticket">
				<form id="inex_frontend_ticket_listing_form" action="' . get_the_permalink() . '" method="post">' .
					wp_nonce_field( 'list_ticket_nonce_name', 'list_ticket_nonce_field', true, false ) . '
					'./*<div class="riga list_intestazione">*/'
					<div class="row input-group">
						<div class="col-md-3 col-sm-6 col-sx-12">
							<label for="type" class="little-title">' . __( 'Ticket Type', 'inextickt' ) . '</label><br />' . wp_dropdown_categories( $this->type_args ) . '
						</div>
						<div class="col-md-3 col-sm-6 col-sx-12">
							<label for="priority" class="little-title">' . __( 'Ticket Priority', 'inextickt' ) . '</label><br />
							' . wp_dropdown_categories( $this->priority_args ) . '
						</div>
						<div class="col-md-3 col-sm-6 col-sx-12">
						<label for="status" class="little-title">' . __( 'Ticket Status', 'inextickt' ) . '</label><br />
							<select name="status_operator" id="status_operator" class="form-control">
								<option value="0" ' . selected( (int)$status_operator_selected, 0, false ) . '>' . __( 'Is', 'inex-ticket' ) . '</option>
								<option value="1" ' . selected( (int)$status_operator_selected, 1, false ) . '>' . __( 'Is NOT', 'inex-ticket' ) . '</option>
							</select>'
							. wp_dropdown_categories( $this->status_args ) . '
						</div>
						<div class="col-md-3 col-sm-6 col-sx-12">
						<label for="author" class="little-title">' . __( 'Ticket Author', 'inextickt' ) . '</label><br />
							<input type="hidden" value="" name="author" />
						</div>
					</div>

					<div class="row top-buffer">
						<div class="col-md-12 text-center">
							<div class="wats_submit_tl">
								<input class="button-primary btn" type="submit" id="filter" name="filter" value="' . __( 'Filter', 'inextickt' ) . '" />
								<input class="button-primary btn" type="submit" id="inex_export" name="inex_export" value="' . __( 'Export', 'inextickt' ) . '" />
							</div>
							<input class="button-primary" type="hidden" id="categoryfilter" name="categoryfilter" value="0" />
							<input class="button-primary" type="hidden" id="categorylistfilter" name="categorylistfilter" value="" />
							<div id="resultticketlist"></div>
						</div>
					</div>
				</form>
			';

			if ( $list_ticket->have_posts() ) :
			// the  Loop

			$i = 1;

			$html .= '
				<div class="inexticket-list">
					<div class="list-navigation">';

			$big = 999999999; // need an unlikely integer

			// TO DO args pagination

			if ( isset ( $_REQUEST['filter']) ) {

				$args_pagination = array(
					'type'							=> $_REQUEST['type'],
    				'priority'						=> $_REQUEST['priority'],
    				'status_operator'				=> $_REQUEST['status_operator'],
    				'status' 						=> $_REQUEST['status'],
    				'author'						=> $_REQUEST['author'],
 				);

			} else {

				$args_pagination = false;

			}

			$html .= paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, get_query_var('paged') ),
				'total' => $list_ticket->max_num_pages,
				'add_args' => $args_pagination
				) );
			$html .= '</div>';

			while ( $list_ticket->have_posts() ) :

			if ($i % 2 != 0){ # An odd row
				$odd_or_even = 'odd';
				$odd_or_even_title = 'even';
  				} else { # An even row
	  				$odd_or_even = 'even';
	  				$odd_or_even_title = 'odd';
    		}


			$list_ticket->the_post();
			global $post;
			$status = strip_tags( get_the_term_list( $post->ID, 'inex_ticket_status' ) );
			$ticket_number = get_post_meta( $list_ticket->post->ID, 'inex_ticket_number', true );

			$status_id = get_term_by('name', $status, 'inex_ticket_status', ARRAY_A );

			if ( ! empty( $this->options['background'][(int)$status_id['term_id']] ) ){

				$status_color = $this->options['background'][(int)$status_id['term_id']];

				} else {

				$status_color = '#e7e7e7';
			}

			//find ticket owner
			$ticket_owner_assigned = get_post_meta( $list_ticket->post->ID, 'ticket_owner', true );

			if ( ! empty( $ticket_owner_assigned ) && -1 !=  $ticket_owner_assigned ){

				$ticket_owner_data = get_userdata( $ticket_owner_assigned );

				$ticket_owner = $ticket_owner_data->display_name;
			} else {

				$ticket_owner = __( 'Not yet assigned', 'inex-ticket' );
			}

			//find latest comment date and author
			$args = array(
				'posts_per_page'   => -1,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'inex-ticket-reply',
				'post_parent'      => $list_ticket->post->ID,
				'post_status'      => 'publish',
				'suppress_filters' => true
				);

			$ticket_reply  = get_posts( $args );


			$comment_number = (int) count( $ticket_reply );

			if ( ! empty( $ticket_reply ) ){

			$last_ticket_reply = $ticket_reply[0];

			} else {
				$last_ticket_reply = '';

			}

			if ( is_object( $last_ticket_reply ) ){

			$get_last_ticket_author_data = get_userdata( $last_ticket_reply->post_author );

			$last_ticket_author = $get_last_ticket_author_data->display_name;

			} else {

				$last_ticket_author = '';
			}

			//


			if ( (int)0 == $comment_number ){

				$last_ticket_date = false;

			} else {

			$last_ticket_date = $last_ticket_reply->post_date;

			}

			if ( empty( $last_ticket_author ) ){

				$last_ticket_author = __('No reply yet', 'inex-ticket' );
			}

			if ( empty( $last_ticket_date ) ){
				//$last_ticket_date = __('No reply yet', 'inex-ticket' );
			}

			$this->ticked_opened_age = get_the_date( 'd M Y');
			$this->ticket_last_comment = $last_ticket_date;
			$this->ticket_id = $list_ticket->post->ID;


			$term_status = get_the_terms( $list_ticket->post->ID, 'inex_ticket_status' );

			if ( (int)$this->options['status'] == (int)$term_status[0]->term_id ){

				$this->ticked_is_open = false;

			} else {

				$this->ticked_is_open = true;

			}


			$ticket_age = $this->ticket_age();


			$html .= '
					<div class="row row-eq-height top-buffer ' . $odd_or_even . '" >
						<div class="col-md-2 col-sm-2 col-xs-12 ticket-status-list">
							<span class="label label-default" style="background-color: ' .  $status_color . ';">
							' . $status . '
							</span>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12 ticket-title-list">
							<a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a>
						</div>
						<div class="col-md-4 ticket-author-list">
							' . __( 'Author: ', 'inex-ticket' ) . '
							' . get_the_author() . ' - ' . __( 'on', 'inex-ticket' ) . ' '  . get_the_date( 'd M Y') . '
						</div>
					</div>

					<div class="row ' . $odd_or_even . '">
						<div class="col-md-1 col-sm-2 col-xs-2">
							<span class="little-title">' . __( 'Nr', 'inex-ticket' ) . '</span><br />
								#' . $ticket_number . '
						</div>
						<div class="col-md-2 col-sm-2 col-xs-12">
							<span class="little-title">' . __( 'Owner', 'inex-ticket' ) . '</span><br />
								' . $ticket_owner . '
						</div>
						<div class="col-md-2 col-sm-2 col-xs-12">
							<span class="little-title">' . __( 'Modified on', 'inex-ticket' ) . '</span><br />
								' . $last_ticket_date . '
						</div>
						<div class="col-md-2 col-sm-2 col-xs-12">
							<span class="little-title">' . __( 'By', 'inex-ticket' ) . '</span><br />
								' . $last_ticket_author . '
						</div>
						<div class="col-md-2 col-sm-2 col-xs-12">
							<span class="little-title">' . __( 'Age', 'inex-ticket' ) . '</span><br />
								' . $ticket_age . '
						</div>
						<div class="col-md-1 col-sm-2 col-xs-12">
							<span class="little-title">' . __( 'Nr. Replies', 'inex-ticket' ) . '</span><br />
								' . $comment_number . '
						</div>
						<div class="col-md-1 col-sm-2 col-xs-12">
							<span class="little-title">' . __( 'Type', 'inex-ticket' ) . '</span><br />
								' . strip_tags( get_the_term_list( $post->ID, 'inex_ticket_type' ) ) . '
						</div>
						<div class="col-md-1 col-sm-2 col-xs-12">
							<span class="little-title">' . __( 'Priority', 'inex-ticket' ) . '</span><br />
								' . strip_tags( get_the_term_list( $post->ID, 'inex_ticket_priority' ) ) . '
						</div>
					</div>';

			$i++; # Increment our row counter
			endwhile;
			$html .= '<div class="list-navigation">';

			$big = 999999999; // need an unlikely integer

			$html .= paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, get_query_var('paged') ),
				'total' => $list_ticket->max_num_pages,
				'add_args' => $args_pagination
				) );
			$html .= '</div>
				</div>';

			endif;

			// Ripristina Query & Post Data originali
			wp_reset_query();
			wp_reset_postdata();

			} else {

				$html = __( 'Access denied, please login or register', 'inex-ticket' );
			}//end if user is logged in


			$html.='</section>';
			return $html;
	}//end list_all_tickets


	public function ticket_age(){



		$age = array();

			if ( false == $this->ticket_last_comment ){

				$this->ticket_last_comment =  get_post_time( 'Y-m-d  h:i:s A', true, $this->ticket_id, false ) ;
			}

			if ( false == $this->ticked_is_open ){

				$end_date = strtotime( $this->ticket_last_comment );

			} else {

				$end_date = strtotime( date( 'Y-m-d  h:i:s A' ) );

			}

			$total = $end_date - strtotime( get_post_time( 'Y-m-d  h:i:s A', true, $this->ticket_id, false ) );
			$age['days'] = floor( $total / ( 60 * 60 * 24 ) );
			$age['hours'] = floor( ( $total - ( $age['days'] * 24 * 60 * 60 ) ) / ( 60 * 60 ) );
			$age['minutes'] = floor( ( $total - ( $age['days'] * 24 * 60 * 60 ) - ( $age['hours'] * 60 * 60 ) ) / 60 );

			$ticket_age = '';
			if ( $age['days'] > 1 ){

				$ticket_age .= $age['days'].' '.__( 'days','inex-ticket' );

				} else {

					//$ticket_age .= $age['days'].' '.__( 'day','WATS' );
					$ticket_age .= ' '. $age['hours']. ' ' .__( 'h','inex-ticket' );
					$ticket_age .= ' '. $age['minutes'] .' ' .__( 'm','inex-ticket' );
				}


		return $ticket_age;
	}

	/**
	 * get_all_company_users function.
	 *
	 * This function check if a user is associated with a company
	 * if is not he will read only his ticket
	 * if he is, check if he can read other company or not
	 * and pass the id, or ids
	 *
	 * @access private
	 * @return new_user_array
	 */
	public function get_all_company_users(){

		$company_author = get_user_meta( $this->company_user, 'company_associata', true );

		if ( $company_author && user_can( $this->company_user, 'company_tickets_others_view_comments' ) ){

		$args = array(
			'meta_key'     => 'company_associata',
			'meta_value'   => $company_author,
			'meta_compare' => '=',
			);
		$user_array = get_users( $args );

			$new_user_array = array();

			foreach ( $user_array as $ua ){

				$new_user_array[] = $ua->ID;
			}

		} else {

			$new_user_array = array();
			$new_user_array[] = $this->company_user;
		}

		return array_unique( $new_user_array );

	}


	public function ticket_save_comment( $comment_ID ) {


	    	if ( ! empty( $_POST['owner_list'] ) ){

		 	update_post_meta( $_POST['comment_post_ID'], 'ticket_owner', $_POST['owner_list'] );

		 	if ( 0 != $_POST['priority'] )
		 	wp_set_object_terms( $_POST['comment_post_ID'], array( (int)$_POST['priority'], ), 'inex_ticket_priority', false );

		 	if ( 0 != $_POST['status'] )
		 	wp_set_object_terms( $_POST['comment_post_ID'], array( (int)$_POST['status'] ), 'inex_ticket_status',false );

		 	if ( 0 != $_POST['type'] )
		 	wp_set_object_terms( $_POST['comment_post_ID'], array( (int)$_POST['type'] ), 'inex_ticket_type', false );

	    	}

	    	//send mail

	    	$args = array(

			'meta_key'     => 'ownticket',
			'meta_value'   => 1,
			'meta_compare' => '=',
			'orderby'      => 'login',
			'order'        => 'ASC',
			'fields'       => 'all'
			);
			$owner = get_users( $args );

			$ticket_title = get_the_title( $_POST['comment_post_ID'] );
			$ticket_permalink = get_the_permalink($ticket_permalink );
			$comment_author_mail = get_comment_author_email( $comment_ID );
			$comment_author_name = get_comment_author( $comment_ID );

			$original_ticket = get_post( $_POST['comment_post_ID'] );

			$ticket_originator = get_the_author_meta( 'user_email', $original_ticket->post_author );


			foreach ( $owner as $ow ){

				$to = $ow->user_email;

				$subject = $comment_author_name . ' ha inserito una nuova risposta al ticket: ' . $ticket_title;

				$message = 'Testo della risposta al ticket: ' .  $_POST['comment'] . ' permalink: ' . $ticket_permalink;

				wp_mail( $to, $subject, $message );
			}


	    	$to = $comment_author_mail;

			$subject = 'Hai inserito una nuova risposta al ticket: ' . $ticket_title;

				$message = 'Testo della risposta al ticket: ' .  $_POST['comment'] . ' permalink: ' . $ticket_permalink;

			wp_mail( $to, $subject, $message );

			$to = $ticket_originator;

			$subject = 'È stata inserita una nuova risposta al ticket che hai aperto: ' . $ticket_title;

				$message = 'Testo della risposta al ticket: ' .  $_POST['comment'] . ' permalink: ' . $ticket_permalink;

			wp_mail( $to, $subject, $message );
     }

	/**
	 * status_array function.
	 *
	 * @access private
	 * @param mixed $status_selection
	 * @param mixed $status_operator_selection
	 * @return $status_array
	 */
	private function status_array( $status_selection, $status_operator_selection ){

		if ( 0 == $status_operator_selection ){

					$status_operator = 'IN';

				} else {

					$status_operator = 'NOT IN';
				}

		if ( 0 == $status_selection ){

			$status_array['query'] = '';
			$status_array['selected'] = 0;

			} else {

			$status_array['query'] = array(
					'taxonomy'  => 'inex_ticket_status',
					'field'     => 'term_id',
					'terms'     => $status_selection,
					'operator'  => $status_operator
					);
			$status_array['selected'] = (int)$status_selection;

				}

		return $status_array;

	}


	/**
	 * priority_array function.
	 *
	 * @access private
	 * @param mixed $priority_selection
	 * @return $priority_array
	 */
	private function priority_array( $priority_selection ){

		if ( 0 == $priority_selection ){

			$priority_array['query'] = '';
			$priority_array['selected'] = 0;

			} else {

			$priority_array['query'] = array(
					'taxonomy'  => 'inex_ticket_priority',
					'field'     => 'term_id',
					'terms'     => $priority_selection,
					'operator'  => 'IN'
					);
			$priority_array['selected'] = $priority_selection;

				}



		return $priority_array;

	}


	/**
	 * type_array function.
	 *
	 * @access private
	 * @param mixed $type_selection
	 * @return $type_array
	 */
	private function type_array( $type_selection ){

		if ( 0 == $type_selection ){

			$type_array['query'] = '';
			$type_array['selected'] = 0;

			} else {

			$type_array['query'] = array(
					'taxonomy'  => 'inex_ticket_type',
					'field'     => 'term_id',
					'terms'     => $type_selection,
					'operator'  => 'IN'
					);
			$type_array['selected'] = $type_selection;

				}


		return $type_array;

	}


	}// chiudo la classe

	//istanzio la classe

	$inex_ticket_functions = Inex_Ticket_Functions::getInstance();
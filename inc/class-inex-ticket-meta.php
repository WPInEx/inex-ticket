<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
class Inex_Ticket_Meta {

	var $id_ticket = '';
	var $ticket_user = '';
	var $options = '';
	var $priority = '';
	var $status = '';
	var $type = '';
	var $priority_selected = '';
	var $status_selected = '';
	var $type_selected = '';
	var $owner_assigned = '';

	/**
	 * Inex_Ticket_Meta::__construct()
	 * Locked down the constructor, therefore the class cannot be externally instantiated
	 *
	 * @param array $args various params some overidden by default
	 *
	 * @return
	 */

	public function __construct(  $ticket_id = null, $id_user = null ) {

		$this->id_ticket = absint( $ticket_id );
		$this->ticket_user = absint( $id_user );
		$this->options = get_option( 'inex-ticket' );
	}

		/**
	 * inex_show_ticket_data function.
	 *
	 * @access public
	 * @return void
	 */
	public function show_ticket_data(){



		$render_ticket_data = '';
		$can_own_ticket ='';
		//$owner_list = $this->owner_list();
		$this->owner_assigned = get_post_meta( $this->id_ticket, 'ticket_owner', true );
		$owner_data =  get_userdata( $this->owner_assigned );
		if ( empty( $owner_data ) ){

			$owner_display_name = __( 'Owner not yet assigned', 'inex-ticket');

		} else {

			$owner_display_name = $owner_data->display_name;
		}


		if ( null != $this->ticket_user ){

			$can_own_ticket_meta_data = get_user_meta( $this->ticket_user, 'ownticket', true );

			if ( 1 == $can_own_ticket_meta_data ){

				$can_own_ticket = 'can';

			} else {

				$can_own_ticket = 'cannot';
			}
		}

		?>

				<?php

					$this->get_type_priority_status();
				?>

				<?php if ( 'can' == $can_own_ticket && 'update' == $this->meta_or_update ){



					$priority_render = wp_dropdown_categories( $this->priority_args() );
					$status_render = wp_dropdown_categories( $this->status_args() );

					$type_render = wp_dropdown_categories( $this->type_args() );

					$ticket_owner_list = $this->owner_list();

					} else {


					$priority_render = $this->priority[0]->name . '<input type="hidden" name="priority" id="priority" value="' . $this->priority_selected . '" />';
					$status_render = $this->status[0]->name . '<input type="hidden" name="status" id="status" value="' . $this->status_selected . '" />';
					$type_render = $this->type[0]->name . '<input type="hidden" name="type" id="type" value="' . $this->type_selected . '" />';

					$ticket_owner_list = $owner_display_name . '<input type="hidden" name="owner_list" id="owner_list" value="' . $this->owner_assigned . '" />';
				}



				$render_ticket_data .= '<div class="riga">
					<div class="colonna-1-3">
						<strong>' . __('Current priority:','inex-ticket') . '</strong> '
						.  $priority_render . '
					</div>
					<div class="colonna-1-3"><strong>' . __('Current status:','inex-ticket') . '</strong> '
						. $status_render .
					'</div>
					<div class="colonna-1-3"><strong>' . __('Ticket type:','inex-ticket') . '</strong> '
						. $type_render .
					'</div>
				</div><!-- end riga -->

				<div class="riga">
					<div class="colonna-1-3">
						<strong>' . __('Ticket owner:','inex-ticket') . '</strong> '
						. $ticket_owner_list .
					'</div>
					<div class="colonna-1-3">
					</div>
					<div class="colonna-1-3">

					</div>
				</div><!-- end riga -->';

				//$render_ticket_data .= '<script>
				//	jQuery(document).ready(function($) {
				//	$('#status').on('change', function() {
				//	alert( this.value ); // or $(this).val()
				//	});
				//	});
				//</script>';

		return $render_ticket_data;

	}

	public function modify_ticket_data(){

		$this->meta_or_update = 'update';
		$modify_status = $this->show_ticket_data();

		return $modify_status;


	}

	public function get_type_priority_status(){

		$this->priority = get_the_terms( $this->id_ticket, 'inex_ticket_priority' );

		$this->status = get_the_terms( $this->id_ticket, 'inex_ticket_status' );

		$this->type = get_the_terms( $this->id_ticket, 'inex_ticket_type' );

		$this->priority_selected = (int)$this->priority[0]->term_id;
		$this->status_selected = (int)$this->status[0]->term_id;
		$this->type_selected = (int)$this->type[0]->term_id;



	}

	/**
	 * status_args function.
	 *
	 * @access public
	 * @param mixed $selected (default: null)
	 * @return $args
	 */
	public function status_args(){

		$args = array(
			'show_option_all'    => 'All',
			//'show_option_none'   => '',
			//'option_none_value'  => '-1',
			//'orderby'            => 'ID',
			//'order'              => 'ASC',
			//'show_count'         => 0,
			'hide_empty'         => 0,
			//'child_of'           => 0,
			//'exclude'            => '',
			'echo'               => 0,
			'selected'           => $this->status_selected,
			//'hierarchical'       => 0,
			'name'               => 'status',
			//'id'                 => '',
			'class'              => 'ticket-dd form-control',
			//'depth'              => 0,
			'tab_index'          => 30,
			'taxonomy'           => 'inex_ticket_status',
			'hide_if_empty'      => false,
			'value_field'	     => 'term_id',
			);
		return $args;
	}


	/**
	 * priority_args function.
	 *
	 * @access public
	 * @param mixed $selected (default: null)
	 * @return $args
	 */
	public function priority_args(){

		$args = array(
			'show_option_all'    => 'All',
			//'show_option_none'   => '',
			//'option_none_value'  => '-1',
			//'orderby'            => 'ID',
			//'order'              => 'ASC',
			//'show_count'         => 0,
			'hide_empty'         => 0,
			//'child_of'           => 0,
			//'exclude'            => '',
			'echo'               => 0,
			'selected'           => $this->priority_selected,
			//'hierarchical'       => 0,
			'name'               => 'priority',
			//'id'                 => '',
			'class'              => 'ticket-dd form-control',
			//'depth'              => 0,
			'tab_index'          => 20,
			'taxonomy'           => 'inex_ticket_priority',
			'hide_if_empty'      => false,
			'value_field'	     => 'term_id',
			);
		return $args;
	}


	/**
	 * type_args function.
	 *
	 * @access public
	 * @param mixed $selected (default: null)
	 * @return $args
	 */
	public function type_args(){

		$args = array(
			'show_option_all'    => 'All',
			//'show_option_none'   => '',
			//'option_none_value'  => '-1',
			//'orderby'            => 'ID',
			//'order'              => 'ASC',
			//'show_count'         => 0,
			'hide_empty'         => 0,
			//'child_of'           => 0,
			//'exclude'            => '',
			'echo'               => 0,
			'selected'           => $this->type_selected,
			//'hierarchical'       => 0,
			'name'               => 'type',
			//'id'                 => '',
			'class'              => 'ticket-dd form-control',
			//'depth'              => 0,
			'tab_index'          => 10,
			'taxonomy'           => 'inex_ticket_type',
			'hide_if_empty'      => false,
			'value_field'	     => 'term_id',
			);
		return $args;
	}

	/**
	 * owner_list function.
	 *
	 * @access public
	 * @param mixed $selected (default: null)
	 * @return $html
	 */
	public function owner_list(){

	$args = array(

	'meta_key'     => 'ownticket',
	'meta_value'   => 1,
	'meta_compare' => '=',
	'orderby'      => 'login',
	'order'        => 'ASC',
	'fields'       => 'all'
	);
	$owner = get_users( $args );
	$html ='';
	$html .= '<select id="owner_list" name="owner_list" class="ticket-dd">
		<option value="-1">' . __( 'Select ticket owner', 'inex-ticket' ) . '</option>';
	foreach ( $owner as $ow ){
		$html .= '<option value="' . $ow->ID . '" ' . selected( $this->owner_assigned, $ow->ID, false ) . '>' . $ow->display_name . '</option>';
	}

	$html .= '</select>';


	return $html;
	}


	public function std_list(){

		$this->priority_selected = (int)$this->options['listing-priority'];
		$this->status_selected = (int)$this->options['listing-status'];
		$this->type_selected = (int)$this->options['listing-type'];

		$all_args = array();

		$all_args['type'] = $this->type_args();
		$all_args['priority'] = $this->priority_args();
		$all_args['status'] = $this->status_args();

		return $all_args;



	}

}// chiudo la classe


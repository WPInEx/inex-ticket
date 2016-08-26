<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Inex_Ticket_Reply_Loop {

	var $ticket_id = '';
	var $id_user = '';

	/**
	 * Inex_Check_ticket_Permissions::__construct()
	 * Locked down the constructor, therefore the class cannot be externally instantiated
	 *
	 * @param array $args various params some overidden by default
	 *
	 * @return
	 */

	public function __construct( $id_ticket, $user_id ) {

		$this->id_user = absint( $user_id );
		$this->ticket_id = absint( $id_ticket );

	}


	/**
	 * reply_loop function.
	 *
	 * @access public
	 * @return $reply_loop_render
	 */
	public function reply_loop(){

		$reply_loop_render = '';

		//check if a reply is submitted
		if ( $_POST	){

			$reply_loop_render .= $this->save_reply();

			}

		//custom wp_query for ticket reply
		$args = array(

			'post_type' => 'inex-ticket-reply',
			'post_parent' => $this->ticket_id,
			'orderby' => 'ID',
			'order' => 'ASC',
			'posts_per_page' => -1,
		);

		$ticket_replies = new WP_Query( $args );


		//if there are replies I loop
		if ( $ticket_replies->have_posts() ) :
				// Start the Loop.
				while ( $ticket_replies->have_posts() ) : $ticket_replies->the_post();

            $reply_loop_render .= '<div id="post-' . get_the_ID() . '">
                     <div class="blog-post-tags">
                    <ul class="list-unstyled list-inline blog-info">
                        <li><i class="icon-calendar"></i> Risposta inviata il ' . get_the_time(__('j M y', 'inex-ticket')) . ' alle ' . get_the_time() . '</li>
                        <li><i class="icon-pencil"></i> da ' . get_the_author() . '</li>
					</ul>

                </div>

                <div class="blog-body">'
                	. get_the_content() . '

                </div>
             </div>

			<hr>';

				endwhile;
				endif;
				wp_reset_postdata();

			$standard_single_permissions = array(
									'read' => true,
									'write' => true,
									'view_only_your_tickets' => false,
									'pubblico' => null,
									'user_id' => (int)$this->id_user,
									'status' => null,
									'ticket_id' => (int)$this->ticket_id,
									'company_id' => null,
									);

			$filtered_single_permissions = apply_filters( 'inex_single_permissions', $standard_single_permissions );

			$ticket_status = new Inex_Check( $filtered_single_permissions );

			$user_can_write = $ticket_status->check_user_permissions();

			$ticket_is_open = $ticket_status->check_ticket_status();

			if ( true == $ticket_is_open && 'write' == $user_can_write ){

				$ticket_meta = new Inex_Ticket_Meta( $filtered_single_permissions['ticket_id'], $filtered_single_permissions['user_id'] );

				$dropdowns = $ticket_meta->modify_ticket_data();

				$reply_title = __( 'Reply to: ', 'inex-ticket' ) . get_the_title( $this->ticket_id );

				//editor

				// default settings - Kv_front_editor.php
				$content = __( 'Insert your reply', 'inex-ticket' );
				$editor_id = 'inex_description';
				$settings =   array(
				    'wpautop' => true, // use wpautop?
				    'media_buttons' => true, // show insert/upload button(s)
				    'textarea_name' => 'inex_description', // set the textarea name to something different, square brackets [] can be used here
				    'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
				    'tabindex' => '',
				    'editor_css' => '', //  extra styles for both visual and HTML editors buttons,
				    'editor_class' => '', // add extra class(es) to the editor textarea
				    'teeny' => false, // output the minimal editor config used in Press This
				    'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
				    'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
				    'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
				);
				// Turn on the output buffer
				ob_start();

				// Echo the editor to the buffer
				wp_editor( $content, $editor_id, $settings );

				// Store the contents of the buffer in a variable
				$editor_contents = ob_get_clean();

				$reply_loop_render .= '

				<form id="inex-ticket-reply" name="inex-ticket-reply" method="post" action="">
				<p>' . $dropdowns . '</p>
				<p><label for="description">' . __( 'Ticket reply', 'inex-ticket' ) . '</label><br />' .
				 $editor_contents . '


				</p>

				<p align="right"><input type="submit" value="Publish" tabindex="6" id="inex_submit" name="inex_submit" /></p>

				<input type="hidden" name="post-type" id="post-type" value="inex-ticket-reply" />

				<input type="hidden" name="parent-ticket" id="parent-ticket" value="' . $this->ticket_id . '" />

				<input type="hidden" name="reply-title" id="reply-title" value="' . $reply_title . '" />

				<input type="hidden" name="reply-author" id="reply-author" value="' . $this->id_user . '" />

				<input type="hidden" name="action" value="inex-ticket-reply" />'

				. wp_nonce_field( 'new_ticket_reply_action','new_ticket_reply_nonce_field' ) . '

				</form>';


			}


			return $reply_loop_render;
	}


	/**
	 * save_reply function.
	 *
	 * @access private
	 * @return void
	 */
	private function save_reply(){

		$allowedtags = array(
			'img' => array(
		        'src' => true,
		    ),
		    'a' => array(
		        'href' => true,
		        'title' => true,
		    ),
		    'abbr' => array(
		        'title' => true,
		    ),
		    'acronym' => array(
		        'title' => true,
		    ),
		    'b' => array(),
		    'blockquote' => array(
		        'cite' => true,
		    ),
		    'cite' => array(),
		    'code' => array(),
		    'del' => array(
		        'datetime' => true,
		    ),
		    'em' => array(),
		    'i' => array(),
		    'q' => array(
		        'cite' => true,
		    ),
		    'strike' => array(),
		    'strong' => array()
		);

		if ( empty( $_POST ) || !wp_verify_nonce( $_POST['new_ticket_reply_nonce_field'], 'new_ticket_reply_action' ) ){

			print 'Sorry, your nonce did not verify.';
			exit;

			} else {

				if ( isset ( $_POST['reply-title'] ) ) {

				$title = ( $_POST['reply-title'] );

				} else {

					echo 'Please enter a title';
					exit;
				}

				if ( isset ( $_POST['inex_description'] ) ) {

					$description = wp_kses( $_POST['inex_description'], $allowedtags ); //esc_textarea( $_POST['inex_description'] );

				} else {

					echo 'Please enter the content';
					exit;
				}

				if ( isset ( $_POST['parent-ticket'] ) ) {

					$parent_ticket = absint( $_POST['parent-ticket'] );

				} else {

					echo 'Please enter the content';
					exit;
				}

				if ( isset ( $_POST['reply-author'] ) ) {

					$reply_author = absint( $_POST['reply-author'] );

				} else {

					echo 'Please enter the content';
					exit;
				}

				if ( -1 != $_POST['owner_list'] ) {

					$ticket_owner = absint( $_POST['owner_list'] );

				} else {

					$ticket_owner = -1;
				}



			$args = array(

				'post_title'    => $title,
				'post_content'  => $description,
				'post_status'   => 'publish',
				'post_author'   => $reply_author,
				'post_parent'	=> $parent_ticket,
				'post_type'		=> 'inex-ticket-reply'
				);

			$cpt_id = wp_insert_post( $args );

			//inizio invio email
			$ticket_permalink = get_the_permalink( $parent_ticket );

			$args = array(

			'ticket_title' => wp_strip_all_tags( $title ),
			'description' => $description,
			'ticket_permalink' => $ticket_permalink,
			'reply_author' => $reply_author,
			'ticket_owner' => $ticket_owner,

			);

			$inex_send_mail = new Inex_SendMail( $args, 'reply');
			$inex_send_mail->send_mail();

			//update ticket owner

			update_post_meta( $parent_ticket, 'ticket_owner', $ticket_owner );

			// update terms
			wp_set_object_terms( $parent_ticket, array( (int)$_POST['priority'] ), 'inex_ticket_priority' );
			wp_set_object_terms( $parent_ticket, array( (int)$_POST['category'] ), 'inex-ticket' );
			wp_set_object_terms( $parent_ticket, array( (int)$_POST['status'] ), 'inex_ticket_status' );
			wp_set_object_terms( $parent_ticket, array( (int)$_POST['type'] ), 'inex_ticket_type' );

			// ! TODO DEBUG DA RIMUOVERE
			// perché c'era questa roba qui che non esiste più????
			// [wolly] Sono rincoglionito
			//global $inex_ticket_functions;
			//$inex_ticket_functions->get_type_priority_status();
		}// end if empty $_POST

	}

}// close the class


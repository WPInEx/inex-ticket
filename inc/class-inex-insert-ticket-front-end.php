<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Inex_New_Ticket {

	private $options;




	/**
	 * Inex_New_Ticket::__construct()
	 *
	 *
	 * @param array $args various params some overidden by default
	 *
	 * @return
	 */

	public function __construct() {

		 $this->options = get_option( 'inex-ticket' );

		 add_shortcode('inex-ticket', array( $this, 'ticket_front_end_form' ) );


	}


	/**
	 * ticket_front_end_form function.
	 *
	 * @access public
	 * @return void
	 */
	public function ticket_front_end_form() {

		if ( is_user_logged_in() && ( current_user_can( 'add_new_ticket' ) ) ){

	?>

	<?php $user_data = get_userdata( get_current_user_id() ); ?>
	<?php $company = get_user_meta( $user_data->ID, 'company_associata', true ); ?>

	<div class="row">
		<div class="col-md-12 inex-user-name">
			<p><?php echo _e( 'Welcome', 'inex-ticket' ) . ' ' . $user_data->display_name ?></p>
		</div>
	</div>
	<form id="inex-ticket" name="inex-ticket" method="post" action="" class="inexticket">
		<?php
		//TO DO
		// Creare pannello di controllo per scegliere lo stato da utilizzare in apertura ticket
		?>
		<div class="row">
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label for="category"><?php echo _e( 'Ticket Category', 'inex-ticket' ) ?></label><br />
				<?php wp_dropdown_categories( 'tab_index=1&taxonomy=inex-ticket&hide_empty=0&name=category&class=ticket-dd form-control' ); ?>
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label for="type"><?php echo _e( 'Ticket Type', 'inex-ticket' ) ?></label><br />
				<?php wp_dropdown_categories( 'tab_index=10&taxonomy=inex_ticket_type&hide_empty=0&name=type&class=ticket-dd form-control' ); ?>
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<label for="priority"><?php echo _e( 'Ticket Priority', 'inex-ticket' ) ?></label><br />
				<?php wp_dropdown_categories( 'tab_index=20&taxonomy=inex_ticket_priority&hide_empty=0&name=priority&class=ticket-dd form-control' ); ?>
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<?php
					//check if opening status is set

					if ( empty( $this->options['opening_status'] ) || -1 == $this->options['opening_status'] ){

						$include = '';
					} else {

						$include = '&include=' . (int)$this->options['opening_status'];

					}
				?>
				<label for="staus"><?php echo _e( 'Ticket Status', 'inex-ticket' ) ?></label><br />
				<?php wp_dropdown_categories( 'tab_index=30&taxonomy=inex_ticket_status' . $include . '&hide_empty=0&name=status&class=ticket-dd form-control' ); ?>
			</div>
		</div>
		<div class="row top-buffer">
			<div class="col-md-12">
				<p><label for="title"><?php echo _e( 'Ticket Title', 'inex-ticket' ) ?></label><br />

					<input type="text" id="inex_title" value="" tabindex="40" size="138" name="inex_title" class="form-control" />

				</p>

				<p>
					<label for="description"><?php echo _e( 'Ticket Description', 'inex-ticket' ) ?></label>
					<?php
					/*
					<textarea id="inex_description" tabindex="50" name="inex_description" cols="50" rows="15" class="form-control"></textarea>
					*/
					$content = '';
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
					echo $editor_contents;
					?>
				</p>

				<p align="right"><input type="submit" value="Publish" tabindex="6" id="inex_submit" name="inex_submit" class="btn" /></p>

				<input type="hidden" name="post-type" id="post-type" value="inex-ticket" class="form-control" />

				<input type="hidden" name="action" value="inex-ticket" />

				<?php wp_nonce_field( 'new_ticket_action','new_ticket_nonce_field' ); ?>
			</div>
		</div>
	</form>
<?php

	if(	isset( $_POST['inex_submit'] ) ){

		$this->ticket_save_data();

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
	}



	} else {

	echo '<p>'; _e('Devi fare login per inserire un ticket'); echo '</p>';

	$args = array(
        'echo'           => true,
        'redirect'       => site_url( $_SERVER['REQUEST_URI'] ),
        'form_id'        => 'loginform',
        'label_username' => __( 'Username' ),
        'label_password' => __( 'Password' ),
        'label_remember' => __( 'Remember Me' ),
        'label_log_in'   => __( 'Log In' ),
        'id_username'    => 'user_login',
        'id_password'    => 'user_pass',
        'id_remember'    => 'rememberme',
        'id_submit'      => 'wp-submit',
        'remember'       => true,
        'value_username' => NULL,
        'value_remember' => false
		);
	wp_login_form( $args );

	}

	}

	private function ticket_save_data() {

		if ( empty( $_POST ) || !wp_verify_nonce( $_POST['new_ticket_nonce_field'], 'new_ticket_action' ) ){

			print 'Sorry, your nonce did not verify.';
			exit;

		} else {


			// Do some minor form validation to make sure there is content
			if ( isset ( $_POST['inex_title'] ) ) {

				$title = $_POST['inex_title'];

			} else {

				echo 'Please enter a title';
				exit;
			}

		if ( isset ( $_POST['inex_description'] ) ) {

			$description = wp_kses( $_POST['inex_description'], $allowedtags );

		} else {

			echo 'Please enter the content';
			exit;
		}


		// Add the content of the form to $post as an array
		$post = array(
		'post_title' => wp_strip_all_tags( $title ),
		'post_content' => $description,
		'post_status' => 'publish',           // Choose: publish, preview, future, etc.
		'post_type' => $_POST['post-type'],  // Use a custom post type if you want to
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		);

		$new_ticket_id = wp_insert_post( $post );  // http://codex.wordpress.org/Function_Reference/wp_insert_post

		$ticket_permalink = get_the_permalink( $new_ticket_id );

		$ticket_title = get_the_title( $new_ticket_id );

		//inizio invio email


		$args = array(

				'ticket_title' => wp_strip_all_tags( $title ),
				'description' => $description,
				'ticket_permalink' => $ticket_permalink,
				);

		$inex_send_mail = new Inex_SendMail( $args, 'new');
		$inex_send_mail->send_mail();

		//Add ticket number
		$inex_ticket_numerator = get_option('inex-ticket-numerator');

		if ( ! $inex_ticket_numerator ){

			$inex_ticket_numerator = 0;
		}

		$inex_ticket_numerator = $inex_ticket_numerator + 1;

		update_option('inex-ticket-numerator', $inex_ticket_numerator );

		//$new_ticket_number = date( 'ymd', time() ) . '-' .  str_pad( $inex_ticket_numerator, 6, "0", STR_PAD_LEFT );
		$new_ticket_number = $inex_ticket_numerator;
		update_post_meta( $new_ticket_id, 'inex_ticket_number', $new_ticket_number );
		// end add tickt number

		if ( isset( $_POST['priority'] ) ){

			wp_set_object_terms( $new_ticket_id, array( (int)$_POST['priority'] ), 'inex_ticket_priority' );
			}

		if ( isset( $_POST['category'] ) ){

			wp_set_object_terms( $new_ticket_id, array( (int)$_POST['category'] ), 'inex-ticket' );

			}

		if ( isset( $_POST['status'] ) ){

			wp_set_object_terms( $new_ticket_id, array( (int)$_POST['status'] ), 'inex_ticket_status' );

			}

		if ( isset( $_POST['type'] ) ){

			wp_set_object_terms( $new_ticket_id, array( (int)$_POST['type'] ), 'inex_ticket_type' );

			}

		//$location = home_url(); // redirect location, should be login page
		$location = $ticket_permalink;

		echo "<meta http-equiv='refresh' content='0;url=$location' />"; exit;
		} // end IF

	}

}// chiudo la classe

//istanzio la classe

$inex_new_ticket = new Inex_New_Ticket;
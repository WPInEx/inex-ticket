<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php


		// Start the loop.
		while ( have_posts() ) : the_post();

			/*
			 * 	27/6/2016
			 * wolly
			 * start add ticket metadata
			 */
			?>
			<div class="blog-post-tags">
                    <ul class="list-unstyled list-inline blog-info">
                        <li><i class="icon-calendar"></i> Ticket creato il <?php echo get_the_time(__('j M y', 'wpitticket')); ?> alle <?php echo get_the_time(); ?></li>
                        <li><i class="icon-pencil"></i> da <?php the_author(); ?></li>
					</ul>
					<?php

						$ticket_meta = new Inex_Ticket_Meta( get_the_id()  );

						echo $ticket_meta->show_ticket_data();
						?>
                </div>
			<?php

			/*
			 * 	27/6/2016
			 * wolly
			 * end add ticket metadata
			 */


/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php twentysixteen_excerpt(); ?>

	<?php twentysixteen_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentysixteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

			if ( '' !== get_the_author_meta( 'description' ) ) {
				get_template_part( 'template-parts/biography' );
			}
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php twentysixteen_entry_meta(); ?>
		<?php
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'twentysixteen' ),
					get_the_title()
				),
				'<span class="edit-link">',
				'</span>'
			);
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
<?php


		// End of the loop.
		endwhile;

			/*
			 * 	27/6/2016
			 * wolly
			 * start add ticket reply loop
			 */

			$ticket_id = get_the_id();
			$user_id = get_current_user_id();

			$ticket_reply_loop = new Inex_Ticket_Reply_Loop( $ticket_id, $user_id );

			echo $ticket_reply_loop->reply_loop();


            ?>


	</main><!-- .site-main -->

	<?php //get_sidebar( 'content-bottom' ); ?>

</div><!-- .content-area -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
<?php

/**
 * Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php get_header(); ?>

		<div id="container">
			<div id="content" role="main">

				<?php do_action( 'bbp_template_notices' ); ?>

				<?php if ( bbp_user_can_view_forum( array( 'forum_id' => bbp_get_reply_forum_id() ) ) ) : ?>

					<?php while ( have_posts() ) : the_post(); ?>

						<div id="bbp-reply-wrapper-<?php bbp_reply_id(); ?>" class="bbp-reply-wrapper">
							<h1 class="entry-title"><?php bbp_reply_title(); ?></h1>
							<div class="entry-content">

								<?php bbp_get_template_part( 'bbpress/content', 'single-reply' ); ?>

							</div><!-- .entry-content -->
						</div><!-- #bbp-reply-wrapper-<?php bbp_reply_id(); ?> -->

					<?php endwhile; ?>

				<?php elseif ( bbp_is_forum_private( bbp_get_reply_forum_id(), false ) ) : ?>

					<?php bbp_get_template_part( 'bbpress/feedback', 'no-access' ); ?>

				<?php endif; ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

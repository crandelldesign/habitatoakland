<?php
/*
 Template Name: Template Restore
 *
 * This is the template for the full width Restore page.
 *
 * For more info: http://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>

<div id="content">
	<div class="row">
		<div class="col-sm-12">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/BlogPosting">
					<?php if( get_field('header') ): ?>
						<div class="row">
							<div class="col-sm-6">
								<h1 class="page-title"><?php the_title(); ?></h1>
							</div>
							<div class="col-sm-6">
								<?php the_field('header'); ?>
							</div>
						</div>
					<?php else: ?>
						<h1 class="page-title"><?php the_title(); ?></h1>
					<?php endif; ?>
					<section itemprop="articleBody">
						<?php
							// the content (pretty self explanatory huh)
							the_content();

							/*
							 * Link Pages is used in case you have posts that are set to break into
							 * multiple pages. You can remove this if you don't plan on doing that.
							 *
							 * Also, breaking content up into multiple pages is a horrible experience,
							 * so don't do it. While there are SOME edge cases where this is useful, it's
							 * mostly used for people to get more ad views. It's up to you but if you want
							 * to do it, you're wrong and I hate you. (Ok, I still love you but just not as much)
							 *
							 * http://gizmodo.com/5841121/google-wants-to-help-you-avoid-stupid-annoying-multiple-page-articles
							 *
							*/
							wp_link_pages( array(
								'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'bonestheme' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
							) );
						?>
					</section>
				</article>
			<?php endwhile; else : ?>

				<article id="post-not-found" class="hentry cf">
						<header class="article-header">
							<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
					</header>
						<section class="entry-content">
							<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
					</section>
					<footer class="article-footer">
							<p><?php _e( 'This is the error message in the page-custom.php template.', 'bonestheme' ); ?></p>
					</footer>
				</article>

			<?php endif; ?>

			<?php
				$womenbuildIdObj = get_category_by_slug('women-build');
				$womenbuildId = $womenbuildIdObj->term_id;
				$eventsIdObj = get_category_by_slug('event');
				$eventsId = $eventsIdObj->term_id;
			?>

			<!-- Get Events -->
			<?php query_posts( array( 'category__and' => array( $womenbuildId, $eventsId ) ) ); ?>
    		<div class="row margin-bottom-10">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php if ( has_post_thumbnail() ) { ?>
				<div class="col-sm-6 col-md-4 margin-bottom-15 text-center">
					<a class="btn-img btn btn-block" href="<?php echo get_permalink(); ?>" target="_blank">
						<span class="title"><?php the_title() ;?></span>
						<div class="img-holder" style="background-image:url(<?php echo the_post_thumbnail_url('full') ?>)"></div>
						<span class="learn-more">See More <i class="fa fa-angle-right"></i></span>
					</a>
			   	</div>
			   	<?php } ?>
			<?php endwhile; endif; ?>
			<?php wp_reset_query(); ?>
			</div>

		</div>
	</div>
</div>

<?php get_footer(); ?>

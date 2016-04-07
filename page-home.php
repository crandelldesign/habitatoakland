<?php
/*
 Template Name: Home Page
 *
 * This is the template for the home page.
 *
 * For more info: http://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>

	<div id="content">

		<div class="row">
    		<div class="col-sm-8">


    		<div class="main-buttons-desktop visible-xs-block">
                    <a href="#" class="btn btn-lg btn-block btn-lightblue margin-bottom-10">Donate</a>
                    <a href="#" class="btn btn-lg btn-block btn-darkblue margin-bottom-10">Volunteer</a>
                    <a href="#" class="btn btn-lg btn-block btn-green margin-bottom-10">ReStore</a>
                </div>

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<section>
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
					$eventsIdObj = get_category_by_slug('event');
					$eventsId = $eventsIdObj->term_id;
				?>

				<hr>

				<!-- Get Events -->
				<?php query_posts('cat='.$eventsId.'&posts_per_page=3'); ?>
				<h2>Events</h2>
        		<div class="row">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<div class="col-md-4">
						<h3><?php the_title() ;?></h3>
					   	<p><?php my_excerpt(30); ?></p>
					   	<p><a class="btn btn-sm btn-gray" href="<?php echo wp_get_shortlink(); ?>" role="button">View details &raquo;</a></p>
				   	</div>
				<?php endwhile; endif; ?>
				<?php wp_reset_query(); ?>
				</div>

    		</div>
    		<div class="col-sm-4">
    			<div class="main-buttons-desktop hidden-xs">
                    <a href="#" class="btn btn-lg btn-block btn-lightblue margin-bottom-10">Donate</a>
                    <a href="#" class="btn btn-lg btn-block btn-darkblue margin-bottom-10">Volunteer</a>
                    <a href="#" class="btn btn-lg btn-block btn-green margin-bottom-10">ReStore</a>
                </div>

                <div class="recent-news margin-bottom-35">
            		<h2>Recent News</h2>
                	<?php query_posts(array('category__not_in' => array($eventsId))); ?>
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<h3><?php the_title(); ?></h3>
						<p><?php my_excerpt(30); ?></p>
					   	<p><a class="btn btn-sm btn-lightblue" href="<?php echo wp_get_shortlink(); ?>" role="button">View details &raquo;</a></p>
					<?php endwhile; endif; ?>
					<?php wp_reset_query(); ?>
				</div>
    		</div>
    	</div>

	</div>


<?php get_footer(); ?>

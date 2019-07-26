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


                <div class="main-buttons-mobile visible-xs-block">
                    <a href="<?php echo home_url(); ?>/donate/" class="btn btn-lg btn-block btn-lightblue margin-bottom-10">Donate</a>
                    <a href="<?php echo home_url(); ?>/volunteer/" class="btn btn-lg btn-block btn-darkblue margin-bottom-10">Volunteer</a>
                    <a href="<?php echo home_url(); ?>/restore/" class="btn btn-lg btn-block btn-green margin-bottom-10">ReStore</a>
                    <a href="<?php echo home_url(); ?>/housingprograms-information/rock-the-block/" class="btn btn-lg btn-block btn-blue margin-bottom-10 btn-rock-the-block">Rock the Block - APPLY NOW</a>
                </div>
                <div class="main-search-mobile margin-bottom-10 visible-xs-block">
                    <?php get_search_form(); ?>
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

                    // Now, the custom query:

                    $args = array(
                        'posts_per_page' => 3,
                        'meta_query' => array(
                            array(
                                'key' => '_mem_start_date',
                                'value' => array( date('Y-m-d', strtotime('-10 years')), date('Y-m-d', strtotime('+2 years')) ),
                                'compare' => 'BETWEEN'
                            )
                        ),
                        'orderby'  => 'meta_value',
                        'order'  => 'DESC', // DESC = newest first, ASC = oldest first
                        'ignore_sticky_posts' => true,
                        'cat' => $eventsId,
                        'paged' => $paged
                    );

                    $the_query = new WP_Query( $args );
				?>

				<!--<hr>-->

				<!-- Get Events -->
				<?php //query_posts('cat='.$eventsId.'&posts_per_page=3'); ?>
				<!--<h2>Events &nbsp;&nbsp;&nbsp;<a href="<?php echo home_url(); ?>/events/" class="btn btn-gray">View All Events &raquo;</a></h2>
        		<div class="row margin-bottom-10">
				<?php if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>
					<div class="col-md-4 event">
						<h3 class="post-title"><a href="<?php echo get_permalink(); ?>"><?php the_title() ;?></a></h3>
					   	<?php //my_excerpt(30); ?>
                        <p class="event-info">
                            <?php
                            // First, check if date fields are present.
                            // This will return an array with formatted dates.
                            $mem_date = mem_date_processing(
                                get_post_meta($post->ID, '_mem_start_date', true) ,
                                get_post_meta($post->ID, '_mem_end_date', true)
                            );
                            
                            // Second step: display the date
                            if ($mem_date["start-iso"] !=="") { // show the event date
                                echo '<span class="event-date">When: ';
                                $start_date = strtotime(get_post_meta($post->ID, '_mem_start_date', true));
                                echo date('l, F jS, Y g:i a',$start_date);
                                $end_date = get_post_meta($post->ID, '_mem_end_date', true);
                                if ($end_date) {
                                  if (date('Y-m-d',$start_date) == date('Y-m-d',strtotime($end_date))) {
                                    echo ' &mdash; ' . date('g:i a',strtotime($end_date));
                                  } else {
                                    echo ' through ' . date('l, F jS, Y g:i a',strtotime($end_date));
                                  }
                                }
                                echo '</span>';
                            }
                      
                            // Get Repeat Dates
                            $mem_repeats = get_post_meta($post->ID, '_mem_repeat_date', false);
                            if ($mem_repeats) {
                                ?><br><span class="event-date date-repeats">
                                &nbsp;&nbsp;&nbsp;<?php
                      
                                  $nr_of_repeats = count($mem_repeats);
                                  $repeat_counter = 1;
                                  sort($mem_repeats);
                      
                                  foreach($mem_repeats as $date_repeat) {
                      
                                    if ($nr_of_repeats == 1) {
                                      //echo 'on: ';
                                    } else if ($nr_of_repeats > 1) {
                      
                                      if ($repeat_counter == 1) {
                                        // the first item
                                        //echo 'on: ';
                                      } else if ($repeat_counter == $nr_of_repeats ) {
                                        // the last item
                                        echo '<br>&nbsp;&nbsp;&nbsp;';
                                      } else {
                                        echo '<br>&nbsp;&nbsp;&nbsp;';
                                      }
                                    }
                      
                                    $date = strtotime($date_repeat);
                      
                                    echo date('l, F jS, Y h:i a',$date);
                      
                                    $repeat_counter++; // increment by one
                                    }
                                ?></span><?php
                            }
                            ?>
                        </p>
					   	<p><a class="btn btn-sm btn-gray" href="<?php echo get_permalink(); ?>" role="button">View details &raquo;</a></p>
				   	</div>
				<?php endwhile; else: ?>
                    <div class="col-xs-12">
                        <p>There aren't any events planned at this time.</p>
                    </div>
                <?php endif; ?>
				<?php wp_reset_query(); ?>
				</div> -->

    		</div>
    		<div class="col-sm-4">
                <div class="main-search-desktop margin-bottom-10 hidden-xs">
                    <?php get_search_form(); ?>
                </div>
    			<div class="main-buttons-desktop hidden-xs">
                    <a href="<?php echo home_url(); ?>/donate/" class="btn btn-block btn-lightblue margin-bottom-10">Donate</a>
                    <a href="<?php echo home_url(); ?>/volunteer/" class="btn btn-block btn-darkblue margin-bottom-10">Volunteer</a>
                    <a href="<?php echo home_url(); ?>/restore/" class="btn btn-block btn-green margin-bottom-10">ReStore<br><small>Donate/Pick-Up Call: 248-365-4090</small></a>
                    <a href="<?php echo home_url(); ?>/housingprograms-information/rock-the-block/" class="btn btn-lg btn-block btn-blue margin-bottom-10 btn-rock-the-block">Rock the Block - APPLY NOW</a>
                </div>

                <div class="recent-news margin-bottom-35">
            		<h2>Recent News &nbsp;&nbsp;&nbsp;<a href="<?php echo get_permalink( get_option( 'page_for_posts' ) ) ?>" class="btn btn-blue">View All News &raquo;</a></h2>
                	<?php query_posts(array('category__not_in' => array($eventsId),'posts_per_page' => 4)); ?>
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<h3 class="post-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php my_excerpt(30); ?>
					   	<p><a class="btn btn-sm btn-lightblue" href="<?php echo get_permalink(); ?>" role="button">View details &raquo;</a></p>
					<?php endwhile; endif; ?>
					<?php wp_reset_query(); ?>
				</div>
    		</div>
    	</div>

	</div>


<?php get_footer(); ?>

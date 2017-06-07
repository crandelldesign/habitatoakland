<?php
/*
 Template Name: Events Page
 *
 * This is the template for the events page.
 *
 * For more info: http://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>
	<div id="content">
		<div id="inner-content" class="row">
            <main id="main" class="col-sm-8" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<header class="article-header">
					<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
				</header> <?php // end article header ?>

                <?php get_template_part('breadcrumbs'); ?>

				<?php the_content(); ?>

				<?php endwhile; endif; ?>

                <?php
                // Paged
                $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
                if ($paged == 1):
                ?>

                <h2>Upcoming</h2>

                <?php
                    $eventsIdObj = get_category_by_slug('event');
                    $eventsId = $eventsIdObj->term_id;

                    // Now, the custom query:

                    $args = array(
                        'posts_per_page' => 50,
                        'meta_query' => array(
                            array(
                                'key' => '_mem_start_date',
                                'value' => array( date('Y-m-d'), date('Y-m-d', strtotime('+2 years')) ),
                                'compare' => 'BETWEEN'
                            )
                        ),
                        'orderby'  => 'meta_value',
                        'order'  => 'ASC', // DESC = newest first, ASC = oldest first
                        'ignore_sticky_posts' => true,
                        'cat' => $eventsId,
                        'paged' => $paged
                    );

                    $the_query = new WP_Query( $args );
                ?>

                <?php //query_posts('cat='.$eventsId); ?>
                <?php if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

                        <header class="article-header">
                            <h1 class="h2 entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                            <p class="byline entry-meta vcard">
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
                                      <?php

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
                                            echo '<br>';
                                          } else {
                                            echo '<br>';
                                          }
                                        }

                                        $date = strtotime($date_repeat);

                                        echo date('l, F jS, Y g:i a',$date);

                                        $repeat_counter++; // increment by one
                                        }
                                      ?></span><?php
                                }
                                ?>
                                <!--<?php printf( __( 'Posted', 'bonestheme' ).' %1$s %2$s',
                                    /* the time the post was published */
                                    '<time class="updated entry-time" datetime="' . get_the_time('Y-m-d') . '" itemprop="datePublished">' . get_the_time(get_option('date_format')) . '</time>',
                                    /* the author of the post */
                                    '<span class="by">'.__( 'by', 'bonestheme').'</span> <span class="entry-author author" itemprop="author" itemscope itemptype="http://schema.org/Person">' . get_the_author_link( get_the_author_meta( 'ID' ) ) . '</span>'
                                ); ?>-->
                            </p>
                        </header>

                        <section class="entry-content cf">
                            <?php // the_content(); ?>
                            <?php if ( has_post_thumbnail() ) { ?>
                                <div class="blog-list-thumbnail">
                                <?php the_post_thumbnail('thumbnail'); ?>
                                </div>
                            <?php } ?>
                            <?php the_excerpt(); ?>
                        </section>

                        <footer class="article-footer cf">
                            <p class="footer-comment-count">
                                <?php comments_number( __( '<span>No</span> Comments', 'bonestheme' ), __( '<span>One</span> Comment', 'bonestheme' ), __( '<span>%</span> Comments', 'bonestheme' ) );?>
                            </p>
                            <?php printf( '<p class="footer-category">' . __('filed under', 'bonestheme' ) . ': %1$s</p>' , get_the_category_list(', ') ); ?>
                            <?php the_tags( '<p class="footer-tags tags"><span class="tags-title">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '</p>' ); ?>
                        </footer>

                    </article>

                <?php endwhile; ?>

                    <?php
                        if (function_exists(custom_pagination)) {
                            custom_pagination($the_query->max_num_pages,"",$paged);
                        }
                    ?>

                <?php else : ?>

                    <article id="post-not-found" class="hentry cf">
                        <section class="entry-content">
                            <p>There are no upcoming events posted at this time. Please check back later.</p>
                        </section>
                    </article>

                <?php endif; ?>

                <?php endif; ?>

                <h2>Completed</h2>

				<?php
					$eventsIdObj = get_category_by_slug('event');
					$eventsId = $eventsIdObj->term_id;

					// We define the current date, using the included function.
					$mem_today = mem_date_of_today();

					// We set a limit for past events:
					$mem_date_expiration = ( 730 * DAY_IN_SECONDS );
					// Here we will display them up to 2 days after they occurred.
					// Change that number according to your requirements.

					$mem_unix_limit = ( $mem_today["unix"] - $mem_date_expiration );
					$mem_age_limit = date_i18n( "Y-m-d", $mem_unix_limit);

					// Now, the custom query:
					$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
					$args = array(
                        'posts_per_page' => 10,
                        'meta_query' => array(
                            array(
                                'key' => '_mem_start_date',
                                'value' => array( date('Y-m-d', strtotime('-10 years')), date('Y-m-d') ),
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

				<?php //query_posts('cat='.$eventsId); ?>
				<?php if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

						<header class="article-header">
							<h1 class="h2 entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
							<p class="byline entry-meta vcard">
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
                                      <?php

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
                                            echo '<br>';
                                          } else {
                                            echo '<br>';
                                          }
                                        }

                                        $date = strtotime($date_repeat);

                                        echo date('l, F jS, Y g:i a',$date);

                                        $repeat_counter++; // increment by one
                                        }
                                      ?></span><?php
                                }
								?>
                                <!--<?php printf( __( 'Posted', 'bonestheme' ).' %1$s %2$s',
       								/* the time the post was published */
       								'<time class="updated entry-time" datetime="' . get_the_time('Y-m-d') . '" itemprop="datePublished">' . get_the_time(get_option('date_format')) . '</time>',
       								/* the author of the post */
       								'<span class="by">'.__( 'by', 'bonestheme').'</span> <span class="entry-author author" itemprop="author" itemscope itemptype="http://schema.org/Person">' . get_the_author_link( get_the_author_meta( 'ID' ) ) . '</span>'
    							); ?>-->
							</p>
						</header>

						<section class="entry-content cf">
							<?php // the_content(); ?>
							<?php if ( has_post_thumbnail() ) { ?>
								<div class="blog-list-thumbnail">
								<?php the_post_thumbnail('thumbnail'); ?>
								</div>
							<?php } ?>
							<?php the_excerpt(); ?>
						</section>

						<footer class="article-footer cf">
							<p class="footer-comment-count">
								<?php comments_number( __( '<span>No</span> Comments', 'bonestheme' ), __( '<span>One</span> Comment', 'bonestheme' ), __( '<span>%</span> Comments', 'bonestheme' ) );?>
							</p>
                 			<?php printf( '<p class="footer-category">' . __('filed under', 'bonestheme' ) . ': %1$s</p>' , get_the_category_list(', ') ); ?>
                  			<?php the_tags( '<p class="footer-tags tags"><span class="tags-title">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '</p>' ); ?>
						</footer>

					</article>

				<?php endwhile; ?>

					<?php
                        if (function_exists(custom_pagination)) {
                            custom_pagination($the_query->max_num_pages,"",$paged);
                        }
                    ?>

				<?php else : ?>

					<article id="post-not-found" class="hentry cf">
						<section class="entry-content">
                            <p>There are no past events posted at this time. Please check back later.</p>
						</section>
					</article>

				<?php endif; ?>

			</main>

			<?php get_sidebar(); ?>

		</div>

	</div>
<?php get_footer(); ?>

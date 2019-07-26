<?php get_header(); ?>
    <div id="content">

        <div id="inner-content" class="row">
            <main id="main" class="col-sm-8" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<?php if( is_home() && get_option( 'page_for_posts' ) ) { ?>
			            <header class="article-header">
			                <h1 class="entry-title"><?php $posts_page = get_post( get_option( 'page_for_posts' ) ); echo apply_filters( 'the_title', $posts_page->post_title ); ?></h1>
			            </header>
			        <?php } ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

                        <header class="article-header">
                            <h1 class="h2 entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                            <p class="byline entry-meta vcard">
                                    <?php
                                        /*if (in_category('event') || in_category('class')) {*/
                                        if (in_category('event')) {
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
                                        } else {
                                            echo 'Posted: ';
                                            echo the_time('l, F jS, Y');
                                        }
                                    ?>
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

                    <?php bones_page_navi(); ?>

                <?php else : ?>

                    <article id="post-not-found" class="hentry cf">
                            <header class="article-header">
                                <h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
                        </header>
                            <section class="entry-content">
                                <p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
                        </section>
                        <footer class="article-footer">
                                <p><?php _e( 'This is the error message in the index.php template.', 'bonestheme' ); ?></p>
                        </footer>
                    </article>

                <?php endif; ?>

            </main>

            <div class="hidden-xs">
            <?php get_sidebar(); ?>
            </div>

        </div>

    </div>
<?php get_footer(); ?>

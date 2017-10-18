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

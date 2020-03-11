<span class="post-date">
  <i class="fa fa-calendar fa-fw" aria-hidden="true"></i>&nbsp;
  <?php // If an event
  if (get_post_type() == 'tribe_events'): ?>
    <?= (tribe_event_is_all_day())?tribe_get_start_date(null, true, 'l, F jS, Y'):tribe_get_start_date(null, true, 'l, F jS, Y g:i a') ?>
  <?php else: // If not an event ?>
    <?php printf( '<time class="updated" datetime="%1$s" itemprop="datePublished">%2$s</time> ', get_the_time('Y-m-j', get_the_ID()), get_the_time(get_option('date_format'), get_the_ID())) ?>
  <?php endif; ?>
</span>
<span class="categories">
<?php $categories = get_the_category(get_the_ID()) ?>
<?php if(!empty($categories)): ?>
  <i class="fa fa-bookmark fa-fw" aria-hidden="true"></i>&nbsp;
  <a href="<?= esc_url( get_category_link( $categories[0]->term_id ) ) ?>"><?= $categories[0]->name ?></a>
<?php endif; ?>
</span>
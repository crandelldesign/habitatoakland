<?php
  // This will return an array with formatted dates.
  $mem_date = mem_date_processing(
    get_post_meta(get_the_ID(), '_mem_start_date', true) ,
    get_post_meta(get_the_ID(), '_mem_end_date', true)
  );
?>

<span class="post-date">
  <i class="fa fa-calendar fa-fw" aria-hidden="true"></i>&nbsp;
  When: <time datetime="<?= date('DATE_ATOM',$mem_date['start-unix']) ?>" itemprop="startDate"><?= ( date('g:i a',$mem_date['start-unix']) == '12:00 am' ) ? date('l, F jS, Y',$mem_date['start-unix']) : date('l, F jS, Y g:i a',$mem_date['start-unix']) ?></time>
  @if ($mem_date['start-unix'] != $mem_date['end-unix'])
    @if (date('Y-m-d',$mem_date['start-unix']) == date('Y-m-d',$mem_date['end-unix']))
    &mdash; <?= date('g:i a',$mem_date['end-unix']); ?>
    @else
    through <time datetime="<?= date('DATE_ATOM',$mem_date['end-unix']) ?>" itemprop="endDate"><?= ( date('g:i a',$mem_date['end-unix']) == '12:00 am' ) ? date('l, F jS, Y',$mem_date['end-unix']) : date('l, F jS, Y g:i a',$mem_date['end-unix']) ?></time>
    @endif
  @endif
</span>
<span class="categories">
<?php $categories = get_the_category(get_the_ID()) ?>
<?php if(!empty($categories)): ?>
  <i class="fa fa-bookmark fa-fw" aria-hidden="true"></i>
  <a href="<?= esc_url( get_category_link( $categories[0]->term_id ) ) ?>"><?= $categories[0]->name ?></a>
<?php endif; ?>
</span>
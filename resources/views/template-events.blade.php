{{--
  Template Name: Events Page
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php the_post() @endphp
    @include('partials.content-page')
  @endwhile

  <?php
  $eventsIdObj = get_category_by_slug('event');
  $eventsId = $eventsIdObj->term_id;
  // Page Number
  $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  $page = (is_int(basename(dirname($url))))?basename(dirname($url)):1;
  ?>

  <ul class="nav nav-tabs justify-content-center" id="event-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="upcoming-tab" data-toggle="tab" href="#upcoming" role="tab" aria-controls="upcoming" aria-selected="true">Upcoming Events</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="past-tab" data-toggle="tab" href="#past" role="tab" aria-controls="past" aria-selected="false">Past Events</a>
    </li>
  </ul>
  <div class="tab-content" id="event-tab-content">
    <div class="tab-pane fade show active pt-3" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
    <?php
    wp_reset_postdata();
    global $post;
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
      'paged' => $page
    );
    ?>
    @php
      $loop = new WP_Query($args);
    @endphp
    <div class="row">
      @foreach ($loop->posts as $post)
        <?php
          setup_postdata($post);
          // This will return an array with formatted dates.
          $mem_date = mem_date_processing(
            get_post_meta($post->ID, '_mem_start_date', true) ,
            get_post_meta($post->ID, '_mem_end_date', true)
          );
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
          @include('partials.content')
        </div>
      @endforeach
    </div>
    @php wp_reset_postdata(); @endphp
    </div>
    <div class="tab-pane fade pt-3" id="past" role="tabpanel" aria-labelledby="past-tab">
      <?php
        // We define the current date, using the included function.
        $mem_today = mem_date_of_today();
        // We set a limit for past events:
        $mem_date_expiration = ( 730 * DAY_IN_SECONDS );
        // Here we will display them up to 2 days after they occurred.
        // Change that number according to your requirements.
        $mem_unix_limit = ( $mem_today["unix"] - $mem_date_expiration );
        $mem_age_limit = date_i18n( "Y-m-d", $mem_unix_limit);
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
          'paged' => $page
        );
      ?>
      @php
      $loop = new WP_Query($args);
    @endphp
    <div class="row">
      @foreach ($loop->posts as $post)
        <?php
          setup_postdata($post);
          // This will return an array with formatted dates.
          $mem_date = mem_date_processing(
            get_post_meta($post->ID, '_mem_start_date', true) ,
            get_post_meta($post->ID, '_mem_end_date', true)
          );
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
          @include('partials.content')
        </div>
      @endforeach
    </div>
    @php wp_reset_postdata(); @endphp
    </div>
  </div>

  
@endsection

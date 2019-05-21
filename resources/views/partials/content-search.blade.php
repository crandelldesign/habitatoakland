<article @php post_class() @endphp itemscope itemtype="http://schema.org/Article">
  @php $url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()), 'medium' ) @endphp
  @if ($url)
    <a href="{{ get_permalink() }}" class="featured-img">
      <img src="{{ $url }}" alt="{{ get_the_title() }}" />
    </a>
  @endif
  <div class="article-content">
    <header class="card-header"> 
      <h2 class="entry-title"><a href="{{ get_permalink() }}">{!! get_the_title() !!}</a></h2>
    </header>
    <div class="card-body entry-summary">
      @php
      $content = get_the_content();
      @endphp
      <p>{!! wp_trim_words( $content , '25' ) !!}</p>
      <div class="btn-container"><a class="btn btn-primary" href="{{ get_permalink() }}">Read More</a></div>
    </div>
    <div class="card-footer">
      @if (get_post_type() === 'post')
      <div class="text-muted">
        @if (!in_category('event'))
        @include('partials/entry-meta')
        @else
        @include('partials/event-meta')
        @endif
      </div>
      @endif
    </div>
  </div>
</article>


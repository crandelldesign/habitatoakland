<article @php post_class() @endphp itemscope itemtype="http://schema.org/Article">
  @php $url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()), 'medium' ) @endphp
  @if ($url)
    <div class="featured-img">
      <img src="{{ $url }}" alt="{{ get_the_title() }}" />
    </div>
  @endif
  <header class="article-header">
    <h1 class="entry-title">{!! get_the_title() !!}</h1>
    <div class="article-meta">
      @include('partials/entry-meta')
    </div>
  </header>
  <div class="entry-content">
    @php the_content() @endphp
  </div>
  <footer>
    {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
  </footer>
  @php comments_template('/partials/comments.blade.php') @endphp
</article>

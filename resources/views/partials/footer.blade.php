<footer class="footer content-info">
  <div class="container-fluid widget-area">
    @php dynamic_sidebar('sidebar-footer') @endphp
  </div>
  <div class="copyright-area">
    <div class="copyright">
      <p>&copy; {{date('Y')}} Habitat for Humanity of Oakland County</p>
    </div>
    <div class="social-media">
      {!! do_shortcode("[social_media]"); !!}
    </div>
  </div>
</footer>

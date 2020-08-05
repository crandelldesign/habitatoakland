<header class="header">
  <div class="header-bar">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8">
          <div class="contact-info">
            150 Osmun St Pontiac, MI 48342 | 248-338-1843
          </div>
          <div class="header-search">
            <button type="button" class="btn btn-sm btn-invisible" data-toggle="modal" data-target="#search-modal">
              <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search</span>
            </button>
            <form method="get" action="{{esc_url( home_url( '/' ) )}}">
              <span class="icon"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search</span></span>
              <input type="search" id="search" name="s" placeholder="Search..." />
            </form>
          </div>
        </div>
        <div class="col-md-4">
          <div class="social-media">
            {!! do_shortcode("[social_media]"); !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  
</header>
<nav id="main-nav" class="main-nav navbar sticky-top navbar-expand-lg">
    <div class="container-fluid">
      <a class="brand" href="{{ home_url('/') }}">
        @include('partials/logo-full')
        @include('partials/logo')
      </a>
      <!--<button type="button" class="navbar-toggler collapsed">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar top-bar"></span>
        <span class="icon-bar middle-bar"></span>
        <span class="icon-bar bottom-bar"></span>
      </button>-->
      <button class="navbar-toggler navbar-toggler-right collapsed" type="button" data-toggle="collapse"
        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar top-bar"></span>
        <span class="icon-bar middle-bar"></span>
        <span class="icon-bar bottom-bar"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
          @if (has_nav_menu('primary_navigation'))
            {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'navbar-nav ml-auto']) !!}
          @endif
        </div>
    </div>
  </nav>

<!-- Modal -->
<div class="modal fade" id="search-modal" tabindex="-1" role="dialog" aria-labelledby="search-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="search-modal-label">Search</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @php get_search_form(); @endphp
      </div>
    </div>
  </div>
</div>
@extends('layouts.app')

@section('content')
  <!-- Alternative for no results only: Use confused boy as left image with blue on right -->
    @if (!have_posts())
      <div class="page-title-block bg-primary search-title-block">
        <div class="row no-gutters">
          <div class="col-sm-8">
          <img src="<?= \App\asset_path('images/not-found.jpg'); ?>" alt="{{ App::title() }}" class="d-none">
          </div>
          <div class="col-sm-4">
            @include('partials.page-header')
          </div>
        </div>
        
        <div class="alert alert-primary">
          {{ __('Sorry, Your search did not match any results.', 'sage') }}
        </div>
      </div>
      {!! get_search_form(false) !!}
    @else
      @include('partials.page-header')
    @endif

  <div class="row">
    @while (have_posts()) @php the_post() @endphp
      <div class="col-md-6 col-lg-4 mb-4">
        @include('partials.content-search')
      </div>
    @endwhile
  </div>

  {!! get_the_posts_navigation() !!}
@endsection

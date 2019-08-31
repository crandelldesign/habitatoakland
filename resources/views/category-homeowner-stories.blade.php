{{--
  Category Name: Homeowner Stories Page
--}}

@extends('layouts.app')

@section('content')

  @include('partials.page-header')

  @if(category_description())
  <div class="archive-meta">
     {!! category_description() !!}
  </div>
  @endif

  @if (!have_posts())
    <div class="alert alert-warning">
      {{ __('Sorry, no results were found.', 'sage') }}
    </div>
    {!! get_search_form(false) !!}
  @endif

  <div class="row">
    @while (have_posts()) @php the_post() @endphp
      <div class="col-md-6 col-lg-4 mb-4">
        @include('partials.content-'.get_post_type())
      </div>
    @endwhile
  </div>

  {{ App\page_navi() }}
  
@endsection

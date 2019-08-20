@extends('layouts.app')

@section('content')
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
      {{ __('We\'re sorry, but we couldn\'t find that page.', 'sage') }}
    </div>
  </div>
  {!! get_search_form(false) !!}
  
@endsection

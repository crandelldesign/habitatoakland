{{--
  Template Name: Style Guide
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php the_post() @endphp
    @include('partials.page-header')
    <div class="row">
            <div class="col-sm-6">
                <h1>Color Palette</h1>
                <div class="row">
                    <div class="col-md-4">
                        <div class="color-swatch swatch1">
                            Bright Blue<br>
                            #00AFD7
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch2">
                            Bright Green<br>
                            #C4D600
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch3">
                            Gray<br>
                            #888B8D
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch4">
                            Habitat Blue<br>
                            #385988
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch5">
                            Habitat Green<br>
                            #43B02A
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch6">
                            Orange<br>
                            #FF671F
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch7">
                            Brick<br>
                            #A4343A
                        </div>
                    </div>
                </div>
                <hr>
                <h1>Buttons</h1>

                <button class="btn btn-lg btn-primary">This is a large button</button>&nbsp;

                <div class="visible-xs-block margin-bottom-10"></div>

                <button class="btn btn-secondary">This is another smaller button</button>

                <hr class="visible-xs-block">

            </div>
            <div class="col-sm-6">
                <h1>Typography</h1>

                <h1>Heading 1</h1>
                <p class="details">Font: Roboto Regular / Bahama Blue #005596</p>

                <hr>

                <h2>Heading 2</h2>
                <p class="details">Font: Roboto Bold / Apple #51b948</p>

                <hr>

                <h3>Heading 3</h3>
                <p class="details">Font: Roboto Medium / Bahama Blue #005596</p>

                <hr>

                <p>Body Text</p>
                <p>Lorem ipsum dolor sit amet, quis quam, fusce duis. Montes vestibulum esse, tristique dui lorem. Wisi cubilia. Nonummy justo, eros aliquet elit, nulla sollicitudin ut. Iaculis sit lacus, nisi orci nunc, pede convallis vestibulum.</p>
                <p>Sed tellus. Posuere est quis, lacus sit nec. Ultricies vehicula arcu, nunc nonummy id. Vivamus odio neque, faucibus duis. Non diam amet, elit nec semper.</p>

                <p><a href="#">This is a link</a> - <a href="#" class="hover">This is a hover link</a></p>

                <p class="details">Font: Crimson Text / Black #252525</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h1>Form Elements</h1>
                <form>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>First name</label>
                            <input type="text" class="form-control" placeholder="First name" value="Mark" required>
                        </div>
                        <div class="form-group">
                            <label>Last name</label>
                            <input type="text" class="form-control is-valid" placeholder="Last name" value="Otto" required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" class="form-control is-invalid" placeholder="City" required>
                            <div class="invalid-feedback">
                                Please provide a valid city.
                            </div>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" class="form-control is-invalid" placeholder="State" required>
                            <div class="invalid-feedback">
                                Please provide a valid state.
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Zip</label>
                            <input type="text" class="form-control is-invalid" placeholder="Zip" required>
                            <div class="invalid-feedback">
                                Please provide a valid zip.
                            </div>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit">Submit form</button>
                    </div>
                </form>
            </div>
        </div>
  @endwhile
@endsection

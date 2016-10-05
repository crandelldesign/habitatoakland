<?php
/*
 Template Name: Style Guide
 *
 * This is the template for the style guide.
 *
 * For more info: http://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>
	<div id="content">

		<div class="row">
            <div class="col-sm-6">
                <h1>Color Palette</h1>

                <div class="row">
                    <div class="col-md-4">
                        <div class="color-swatch swatch1">
                            Bahama Blue<br>
                            #005596
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch2">
                            Apple<br>
                            #51b948
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch3">
                            Jumbo Gray<br>
                            #8b8c84
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch4">
                            Cornflower<br>
                            #8fc3e9
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="color-swatch swatch5">
                            Deep Cerulean<br>
                            #007cb1
                        </div>
                    </div>
                    <!--<div class="col-md-4">
                        <div class="color-swatch swatch6">
                            Salem<br>
                            #077135
                        </div>
                    </div>-->
                </div>
                <hr>
                <h1>Buttons</h1>

                <button class="btn btn-lg btn-blue">This is a large button</button>&nbsp;

                <div class="visible-xs-block margin-bottom-10"></div>

                <button class="btn btn-gray">This is another smaller button</button>

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

	</div>


<?php get_footer(); ?>

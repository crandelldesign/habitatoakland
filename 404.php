<?php get_header(); ?>

	<div id="content">

		<article id="post-not-found">

			<header class="article-header">

				<h1><?php _e( '404 Error - Page Not Found', 'bonestheme' ); ?></h1>

			</header>

			<section class="entry-content">

				<p><?php _e( 'The page or post you were looking for was not found, but maybe try looking again!', 'bonestheme' ); ?></p>

				<div class="search">
					<p><?php get_search_form(); ?></p>
				</div>

			</section>

			<!--<footer class="article-footer">

					<p><?php /*_e( 'This is the 404.php template.', 'bonestheme' );*/ ?></p>

			</footer>-->

		</article>

	</div>

<?php get_footer(); ?>

		</div>

    	<footer class="footer" itemscope itemtype="http://schema.org/WPFooter">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-2 col-md-1 hidden-xs">
                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/eho-logo.jpg" class="img-responsive" alt="EHO">
                    </div>

                    <div class="col-sm-3 margin-bottom-10">
                        <p><strong>ReStore Pontiac</strong></p>
                        <p>Hours:<br>
                        Thurs - Sat: 10 a.m. to 5:30 p.m.<br>
                        Sun: 12 p.m. to 5 p.m.</p>
                        <meta itemprop="openingHours" content="Mo-Sa 10:00-17:30"/>
                        <meta itemprop="openingHours" content="Su 12:00-5:30"/>

                        <address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="streetAddress">150 Osmun Street</span><br>
                        <span itemprop="addressLocality">Pontiac</span>, <span itemprop="addressRegion">MI</span> <span itemprop="postalCode">48342</span><br>
                        <a href="tel:1-248-338-8392" itemprop="telephone">248-338-8392</a><br>
                        Donate/Pick-Up Call <a href="tel:1-248-365-4090" itemprop="telephone">248-365-4090</a><br>
                        <i class="fa fa-cc-visa fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-mastercard fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-discover fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-amex fa-2x" aria-hidden="true"></i></address>
                    </div>
                    <div class="col-sm-3 margin-bottom-10">
                        <p><strong>ReStore Farmington</strong></p>
                        <p>Hours:<br>
                        Tues - Sat: 10 a.m. to 5:30 p.m.<br>
                        Sun: 12 p.m. to 5 p.m.</p>
                        <meta itemprop="openingHours" content="Mo-Sa 10:00-17:30"/>
                        <meta itemprop="openingHours" content="Su 12:00-5:30"/>

                        <address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="streetAddress">28575 Grand River Ave.</span><br>
                        <span itemprop="addressLocality">Farmington</span>, <span itemprop="addressRegion">MI</span> <span itemprop="postalCode">48336</span><br>
                        <a href="tel:1-248-442-2267" itemprop="telephone">248-442-2267</a><br>
                        Donate/Pick-Up Call <a href="tel:1-248-365-4090" itemprop="telephone">248-365-4090</a><br>
                        <i class="fa fa-cc-visa fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-mastercard fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-discover fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-amex fa-2x" aria-hidden="true"></i></address>
                    </div>
                    <div class="col-sm-2 margin-bottom-10">
                        150 Osmun<br>
                        Pontiac, MI 48342<br>
                        248-338-1843
                    </div>
                    <div class="col-sm-2 margin-bottom-10">
                        Office Hours:<br>
                        Mon. - Fri.<br>
                        9:00 AM- 5:00 PM
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted">&copy; 2016 <?php bloginfo( 'name' ); ?>.</p>
                    </div>
                    <div class="col-md-6 social-icons">
                        <?php echo do_shortcode('[social_media]'); ?>
                    </div>
                </div>
                <div class="visible-xs-block row">
                    <div class="col-xs-6 col-xs-offset-3"><img src="<?php echo get_template_directory_uri(); ?>/library/images/ehl-logo.gif" class="img-responsive" alt="EHL"></div>
                </div>
            </div>
        </footer>


		<?php // all js scripts are loaded in library/bones.php ?>
		<?php wp_footer(); ?>

	</body>

</html> <!-- end of site. what a ride! -->

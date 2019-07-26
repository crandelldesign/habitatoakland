		</div>

    	<footer class="footer" itemscope itemtype="http://schema.org/WPFooter">
            <div class="container-fluid">
                <div class="row">
                    <!--<div class="col-sm-2 col-md-1 hidden-xs hidden">
                        <img src="<?php echo get_template_directory_uri(); ?>/library/images/eho-logo.jpg" class="img-responsive" alt="EHO">
                    </div>-->

                    <div class="col-sm-6 col-lg-3 margin-bottom-10">
                        <h3>ReStore Waterford</h3>
                        <h4>Hours</h4>
                        <p><strong>Mon - Sat</strong> 10 AM to 6 PM<br>
                        <strong>Sun</strong> 12 PM to 5 PM</p>
                        <meta itemprop="openingHours" content="Mo-Sa 10:00-18:00"/>
                        <meta itemprop="openingHours" content="Su 12:00-17:00"/>

                        <address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="streetAddress">3653 Highland Rd</span><br>
                        <span itemprop="addressLocality">Waterford</span>, <span itemprop="addressRegion">MI</span> <span itemprop="postalCode">48328</span><br>
                        <a href="tel:1-248-338-8392" itemprop="telephone">248-338-8392</a><br>
                        Donate/Pick-Up Call <a href="tel:1-248-365-4090" itemprop="telephone">248-365-4090</a></address>
                        
                        <p><i class="fa fa-cc-visa fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-mastercard fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-discover fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-amex fa-2x" aria-hidden="true"></i></p>
                    </div>
                    <div class="col-sm-6 col-lg-3 margin-bottom-10">
                        <h3>ReStore Farmington</h3>
                        <h4>Hours</h4>
                        <p><strong>Mon - Sat</strong> 10 AM to 6 PM.<br>
                        <strong>Sun</strong> 12 PM to 5 PM</p>
                        <meta itemprop="openingHours" content="Mo-Sa 10:00-18:00"/>
                        <meta itemprop="openingHours" content="Su 12:00-17:00"/>

                        <address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="streetAddress">28575 Grand River Ave.</span><br>
                        <span itemprop="addressLocality">Farmington</span>, <span itemprop="addressRegion">MI</span> <span itemprop="postalCode">48336</span><br>
                        <a href="tel:1-248-442-2267" itemprop="telephone">248-442-2267</a><br>
                        Donate/Pick-Up Call <a href="tel:1-248-365-4090" itemprop="telephone">248-365-4090</a></address>
                        <p><i class="fa fa-cc-visa fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-mastercard fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-discover fa-2x" aria-hidden="true"></i> <i class="fa fa-cc-amex fa-2x" aria-hidden="true"></i></p>
                    </div>
                    <div class="col-sm-6 col-lg-3 margin-bottom-10">
                        <h3>Office</h3>
                        <h4>Office Hours</h4>
                        <p>Mon. - Fri.<br>
                        9:00 AM- 5:00 PM</p>
                        <address>150 Osmun<br>
                        Pontiac, MI 48342<br>
                        248-338-1843</address>
                        
                    </div>
                    <div class="col-sm-6 col-lg-3 margin-bottom-10">
                        <h3>Quick Links</h3>
                        <?php
                            wp_nav_menu( array(
                                'theme_location'    => 'footer-links',
                                'depth'             => 2,
                                'container'         => 'footer-links-container',
                                'container_class'   => 'footer-links-container',
                                'container_id'      => 'footer-links-container',
                                'menu_class'        => 'footer-links'
                            ));
                        ?>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted">&copy; <?= date('Y') ?> <?php bloginfo( 'name' ); ?>.</p>
                    </div>
                    <div class="col-md-6 social-icons">
                        <?php echo do_shortcode('[social_media]'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12"><img src="<?php echo get_template_directory_uri(); ?>/library/images/eho-logo.png" class="img-responsive center-block" alt="Equal Housing Opportunity"></div>
                </div>
            </div>
        </footer>


		<?php // all js scripts are loaded in library/bones.php ?>
		<?php wp_footer(); ?>

	</body>

</html> <!-- end of site. what a ride! -->

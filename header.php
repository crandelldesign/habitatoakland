<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<title><?php wp_title(''); ?></title>

		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>

		<?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
            <meta name="theme-color" content="#121212">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

		<?php // drop Google Analytics Here ?>
		<?php // end analytics ?>

	</head>

	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

        <nav class="navbar" itemscope itemtype="http://schema.org/WPHeader">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo home_url(); ?>" itemscope itemtype="http://schema.org/Organization"><img src="<?php echo get_template_directory_uri(); ?>/library/images/habitatoakland-logo.png" class="img-responsive logo" alt="<?php bloginfo( 'name' ); ?>"></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Donate <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Mailed Check</a></li>
                                <li><a href="#">Online</a></li>
                                <li><a href="#">Cars For Homes</a></li>
                                <li><a href="#">ReStore</a></li>
                                <li><a href="#">Payroll Deductions/Matching Gifts</a></li>
                                <li><a href="#">Third-Party Event Fundraising</a></li>
                                <li><a href="#">Bequests and Estate Planning</a></li>
                                <li><a href="#">Gift-in-kind</a></li>
                                <li><a href="#">Donor Stories</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Volunteer <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Volunteer Calendar</a></li>
                                <li><a href="#">Get Invloved</a></li>
                            </ul>
                        </li>
                        <li><a href="#">ReStore</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">About Us <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">History and Our Future</a></li>
                                <li><a href="#">Vision</a></li>
                                <li><a href="#">Mission</a></li>
                                <li><a href="#">Executive Director Message</a></li>
                                <li><a href="#">Where We Work</a></li>
                                <li><a href="#">General Explanation</a></li>
                                <li><a href="#">Board of Directors</a></li>
                                <li><a href="#">Staff</a></li>
                                <li><a href="#">Employment</a></li>
                                <li><a href="#">Contact Us</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Homeownership &amp; Home Repair <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Homeownership Program</a></li>
                                <li><a href="#">Critical Home Repair Program</a></li>
                                <li><a href="#">Partnerships</a></li>
                                <li><a href="#">Partner Homeowner Stories</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>

        <div class="container-fluid page-content">

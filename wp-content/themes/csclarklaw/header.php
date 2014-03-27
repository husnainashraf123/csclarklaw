<?php
global $data;
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php wp_title('|', true, 'right'); ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
        <?php echo $data['style_head']; ?>
        <?php get_template_part('googlefonts'); ?>
        <!--[if gte IE 9]><!-->
        <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
        <!--<![endif]-->

        <?php wp_head(); ?>
        <?php echo $data['script_head']; ?>
    </head>

    <body <?php 
   
    body_class(); ?>>
        <?php echo $data['code_afterbody']; ?>
        <header id="masthead " class="site-header <?php
        if (!is_front_page()) {
            echo 'in-page-header';
        }
        ?>"  role="banner">
            <div class="header-outer-div">
                <div class="top-header-outer-div">
                    <div class="container  header-color">
                        <div class="logo" data-original-title="logo" title="">
                            <a href="<?php bloginfo('url'); ?>" ><img src="<?php echo $data['media_logo'] ?>" alt="logo"/></a>
                        </div>
                        <div class="address" data-original-title="address" title="">
                            <div class="visible-phone visible-tablet top-mar-30">
                                <a href="http://maps.google.com/?q=<?php echo strip_tags($data['address1']) ?>" >
                                    <i class="icon-map-marker" ></i><br />
                                    Find Us
                                </a>

                            </div>
                            <p class="hidden-phone hidden-tablet"><?php echo $data['address1'] ?></p>
                            <p class="hidden-phone hidden-tablet"><?php echo $data['address2'] ?></p>
                        </div>
                        <div class="phone" data-original-title="phone" title="">
                            <div class="visible-phone visible-tablet top-mar-30">
                                <a  href="tel:<?php echo str_replace(' ', '', $data['phone1']); ?>" >
                                    <i class="icon-phone" ></i><br />
                                    Call Us
                                </a>
                                <p></p>
                            </div>
                            <p class="hidden-phone hidden-tablet"><?php echo $data['phone_heading'] ?></p>
                            <h3 class="hidden-phone hidden-tablet"><?php echo $data['phone1'] ?></h3>
                            <p class="hidden-phone hidden-tablet num-header">Call Anytime - 24 hour&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Se Habla Español</p>
                            <h3 class="hidden-phone hidden-tablet"><?php echo $data['phone2'] ?></h3>
                        </div>
                    </div>
                </div>
                <div class="navbar ">
                    <div class="sticky">
                        <div class="navbar-top ">
                            <div class="container">
                                <div class="">
                                    <?php
                                    
                                    $args = array(
                                        'theme_location' => 'primary',
                                        'depth' => 4,
                                        'container' => false,
                                        'menu_class' => 'nav',
                                        'walker' => new BootstrapNavMenuWalker()
                                    );
                                    wp_nav_menu($args);

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </header><!-- #masthead -->


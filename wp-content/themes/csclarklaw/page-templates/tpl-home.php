<?php
/**
 * Template Name: Home
 *
 */
get_header();
global $data;
?>
<div id="slider">
    <div  class="container slider-bg" > 
<!--        <img id="Image-Maps-Com-image-maps-2014-01-31-003300" src="<?php echo get_template_directory_uri(); ?>/images/slider_img.png" border="0" usemap="#image-maps-2014-01-31-003300" alt="" />
<map name="image-maps-2014-01-31-003300" id="ImageMapsCom-image-maps-2014-01-31-003300">
<area shape="rect" coords="978,295,980,297" alt="Image Map" title="Image Map" href="http://www.brentbowen.com/brent-bowen/" />
<area  shape="poly" coords="735,291,734,274,734,265,724,261,717,246,714,237,712,231,709,225,707,217,706,212,703,207,701,202,701,198,699,189,699,183,699,177,699,172,699,166,700,160,700,154,701,150,703,144,704,136,707,129,708,125,708,120,712,109,715,105,722,99,724,94,728,90,732,87,738,84,752,81,756,76,760,69,764,65,767,63,772,59,773,57,770,54,768,51,767,48,767,45,767,43,767,39,770,38,773,34,773,22,776,17,783,10,790,8,797,11,802,10,810,14,816,23,818,31,816,40,818,48,816,58,811,73,810,81,812,89,821,93,829,97,834,107,838,116,845,126,844,137,843,145,846,154,845,161,843,174,841,187,840,200,837,210,835,222,836,236,831,249,826,261,821,268,817,277,809,284,801,287,797,292,794,295,777,296,764,296,754,296,746,295" alt="" title="" target="_self" href="http://www.brentbowen.com/brent-bowen/"     />
</map>-->
        <?php echo do_shortcode($data['home_slider']); ?>
    </div>
</div>

<div id="feature-items">
    <div id="books-wraper" class="container">
   
        <div class="span4 books_list_check grow border-dive-round">
            <div class="book-cont ">
                <div class="book_innercent">
                <h3 class="title-box"><?php echo $data['book_title_1']; ?></h3>
                <div class="clearfix full-width pull-left">
                    <span class="book-img"><img src="<?php echo $data['book1_img']; ?>" /></span>
                    <p><?php echo $data['book_subtitle_1']; ?></p>
                    <a href="#book1" role="button" class="btn-yellow pull-left" data-toggle="modal"> <?php echo $data['book_btn_text']; ?> <i class="icon-download-alt "></i></a>
                </div>
                </div>

            </div>
        </div>
      
        <div class="span4 books_list_check grow border-dive-round">
            <div class="book-cont">
                <div class="book_innercent">
                <h3 class="title-box-2"><?php echo $data['book_title_2']; ?></h3>
                <div class="clearfix full-width pull-left">
                    <span class="book-img"><img src="<?php echo $data['book2_img']; ?>" /></span>
                    <p><?php echo $data['book_subtitle_2']; ?></p>
                    <a href="#book2" role="button" class="btn-yellow pull-left" data-toggle="modal"> <?php echo $data['book_btn_text']; ?> <i class="icon-download-alt "></i></a>
                </div>
               </div>
            </div>
        </div>
        <div class="span4 zero-mar hidden-phone hidden-tablet hidden_all grow border-dive-round">
            <div class="book-cont">
                <h3 class="title-box">Client Testimonials</h3>
<!--                <p class="home-testimonial"><?php //echo $data['home_testimonial']; ?>... <a href="<?php //echo get_bloginfo('url'); ?>/testimonials/" ><?php //echo $data['testimonial_link_text']; ?></a></p>-->
                <div class="ticker_testmonial">
                <?php if(function_exists('ditty_news_ticker')){ditty_news_ticker(778);} ?>
                <a href="<?php echo get_bloginfo('url'); ?>/testimonials/" ><?php echo $data['testimonial_link_text']; ?></a>
                </div>
            </div>
        </div>

    </div>
</div>
<div  class="container">
    <div  class="inner-contents">
        <div  id="content-main">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php the_content(); ?>
                </article><!-- #post -->
            <?php endwhile; ?>

        </div>
        <?php get_sidebar(); ?>
    </div>
    <?php /* The loop */ ?>
</div><!-- .inner-contents -->
</div><!-- .container -->
<?php get_footer(); ?>
<?php
//$cFormObj = new cForm;
global $data;
?>
<div class="pull-right  hidden-phone hidden-tablet" id="sidebar">
    <div class="box full-width margin_top10 box-bg">
        <h3 class="title-box brown_bg">Free Initial Consultation </h3>
        <?php echo do_shortcode('[cForm]'); ?>
    </div>

    <div class="box full-width margin_top10">
        <h3 class="title-box-form brown_bg"><a href="http://www.csclarklaw.com/know-your-legal-rights/" class="white-color-text">Know Your Legal Rights</a></h3>
    </div>    
        <div class="box-bg-white full-width margin_top10">
            <h3 class="title-box-form brown_bg" style="padding-left: 11px !important">About the NJ <img class="back-image-hammer" src="<?php bloginfo('template_directory'); ?>/images/gavel_icon.png" ></br>Criminal Court System</h3>
        <ul class="sidebar-links sidebar-links-padding">
            <p> Here are some things you should know about our criminal courts and which one will handle your case.</p>
            
        </ul>
            <h2> <a href="http://www.csclarklaw.com/new-jersey-criminal-court-system" class="btn-yellow learn-more btn-pad-learn-more"/>Learn More</a></h2>
    </div>
    
    <div class="box-bg-white full-width margin_top10">
        <h3 class="title-box-form brown_bg">Recent Posts</h3>
        <ul class="sidebar-links">
            <?php
            query_posts('post_type=page&posts_per_page=3');

// The Loop
            while (have_posts()) : the_post();
                ?>
                <li><a href="<?php the_permalink(); ?>" title="Click to view details of '<?php the_title(); ?>'"><?php the_title(); ?></a></li>
               
   <?php
            endwhile;
            // Reset Query
            wp_reset_query();
            ?>
          
        </ul>
    </div>
    <div class="full-width margin_top10">
        <div class="facebook_link">
            <a href="https://www.facebook.com/pages/Clark-Clark-LLC/189599417718397" target="_blank"><div class="face-book"></div></a>
   
            <a href="https://twitter.com/NJDefense" target="_blank" ><div class="twitter"></div></a>   
               
        </div>
    </div>
</div>
</div>
<div class="well well-large hidden-desktop margin_top10 pull-left full-width">
    <h2>Contact Us  <a href="<?php bloginfo('url') ?>/contact-us/" class="btn  btn-success white-font"/>Get Help Now <i class="icon-envelope"></i></a></h2>
</div>

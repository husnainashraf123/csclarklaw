<?php
//$cFormObj = new cForm;
global $data;
?>
<div class="pull-right  hidden-phone hidden-tablet" id="sidebar">
    
        <div class="box full-width margin_top10 box-bg">
    <?php $post_id = 1166;
$queried_post = get_post($post_id);

echo $queried_post->post_content; 
?>
        
    </div>
    <div class="box full-width margin_top10 box-bg">
        <h3 class="title-box-form brown_bg">Free Initial Consultation </h3>
        <?php echo do_shortcode('[cForm]'); ?>
    </div>

    <div class="box full-width margin_top10">
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
            <a href="3" target="_blank"><img src="<?php echo get_template_directory_uri()?>/images/fb.png" /></a>
        <a href="#" target="_blank"><img src="<?php echo get_template_directory_uri()?>/images/in.png" /></a>
        <a href="#" target="_blank"><img src="<?php echo get_template_directory_uri()?>/images/net.png" /></a>
        <a href="#" target="_blank"><img src="<?php echo get_template_directory_uri()?>/images/tweet.png" /></a>
        </div>
    </div>
</div>
</div>
<div class="well well-large hidden-desktop margin_top10 pull-left full-width">
    <h2>Contact Us  <a href="<?php bloginfo('url') ?>/contact-us/" class="btn  btn-success white-font"/>Get Help Now <i class="icon-envelope"></i></a></h2>
</div>

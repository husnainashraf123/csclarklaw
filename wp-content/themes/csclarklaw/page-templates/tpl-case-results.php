<?php
/**
 * Template Name: Case results
 *
 */
get_header();
global $data;
?>
<div class="clearfix inner-page"></div>
<div  class="container" >
    <div class="inner-contents " >
        <div id="content-main" >
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </article><!-- #post -->
            <?php endwhile; ?>
                
                <div class="blog_category">
                    <form action="<?php echo site_url()?>/case-results" method="post" id="post_form">
                <div class="blog_dropdownlist">
                <?php 
                $category_id = get_cat_ID('Case Results');
                 if(isset($_POST['id'])){
                    $CAT_id    =   @$_REQUEST['id'];
           
                }else{
                     $CAT_id    =   125;
                }
                ?>
                <select name="id" class="blog_drop_downget"> 
                    <option value="<?php echo $category_id;?>"><?php echo esc_attr(__('Select Categories')); ?></option> 
                    <?php 
                    $categories = get_categories('child_of='.$category_id); 
                    foreach ($categories as $category) {
                      ?>
                    <option value="<?php echo $category->term_id;?>" <?php if($CAT_id==$category->term_id){?>selected="selected" <?php } ?>><?php echo $category->cat_name;?></option> 

                    <?php  } ?>
                </select>
                
                <div class="blog_page_new">
            <?php
          
           //echo $CAT_id;exit;
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => $data['blog_post'],
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
                'paged' => $paged,
                'cat'   =>$CAT_id    
            );
            $wp_query = new WP_Query($args);
            while ($wp_query->have_posts()) : $wp_query->the_post();
                $numcomms = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");
                if (0 < $numcomms) $numcomms = number_format($numcomms);
            ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h2><?php the_title(); ?></h2>
                   <?php the_content(); ?>
                    <span class="meta-author">
                        <i class="icon-user"></i>
                        <a href="<?php echo $data['icon_google'];?>" title="<?php _e('View all posts by', 'law-firm'); ?> <?php the_author(); ?>"> <?php the_author(); ?></a>
                    </span>
                </article><!-- #post -->
            <?php endwhile; ?>
                <br>
                <div style="clear: both;"></div>
            <div class="paginate_wordpress">
                <?php if(function_exists('wp_paginate')) {
                        wp_paginate();
                } ?>
            </div>
            </div>
            </div>
            
                
            </form>
                </div>
                
                    
        </div>
        <script type="text/javascript">
    jQuery(".blog_drop_downget").change(function(){
        var catid   =   jQuery(this).val();
        //jQuery("#post_change_value").val(catid);
         jQuery( "#post_form" ).submit();
        //alert(linkcat);
        //window.location.replace(catid);
    });
    </script>
        <?php get_sidebar('inner'); ?>
    </div>
</div><!-- .inner-contents -->
<?php get_footer(); ?>
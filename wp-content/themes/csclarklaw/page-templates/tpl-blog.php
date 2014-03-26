<?php
/**
 * Template Name: Blog
 *
 */
get_header();
global $data;
?>
<div class="clearfix inner-page"></div>
<div  class="container" >
    <div  class="inner-contents inner-page">
        <div  id="content-main">
            <?php
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => $data['blog_post'],
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
                'paged' => $paged
            );
            $wp_query = new WP_Query($args);
            while ($wp_query->have_posts()) : $wp_query->the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h2><i class="icon-pencil icon-larg"></i> <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'law-firm'), the_title_attribute('echo=0') ); ?>" rel="bookmark"> <?php the_title(); ?></a></h2>
                   <?php the_excerpt(); ?>
                    <span class="meta-date">
                        <i class="icon-calendar"></i>
                        <time datetime="<?php echo date(DATE_W3C); ?>" class="updated">
                             <?php the_time(get_option('date_format')); ?>
                        </time>
                    </span>
                    <span class="meta-author">
                        <i class="icon-user"></i>
                        <a href="<?php echo $data['icon_google'];?>" title="<?php _e('View all posts by', 'law-firm'); ?> <?php the_author(); ?>"> <?php the_author(); ?></a>
                    </span>
                                    </article><!-- #post -->
            <?php endwhile; ?>
            <div class="blog_pagination">
                <?php if(function_exists('wp_paginate')) {
                wp_paginate();
                } ?>
            </div>
        </div>
        <?php get_sidebar('inner'); ?>
    </div>
    <?php /* The loop */ ?>

</div><!-- .inner-contents -->
</div><!-- .container -->
<?php get_footer(); ?>
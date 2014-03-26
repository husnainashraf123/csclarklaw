<?php
/**
 * Template Name: case evaluation form
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
                <div style="clear: both;"></div>
                <div style="margin-left: 20px;">

                <?php echo do_shortcode('[caseForm]'); ?>

            </div>
        </div>
        
        <?php get_sidebar('inner'); ?>
    </div>
</div><!-- .inner-contents -->
<?php get_footer(); ?>
<?php
/**
 * Template Name: Template Tesmonial New
 *
 */
get_header();
global $data;
?>
<div class="clearfix inner-page"></div>
<div  class="container" >
    <div class="inner-contents " >
        <div class="sidebar_new_page">
            <div class="video-div">
                <div class="hidden-desktop inner-page-mobile-video">
                    <iframe id="inner-page-mobile-video" src="http://www.youtube.com/embed/<?php echo $youtube_id; ?>?autoplay=1" frameborder="0" allowfullscreen></iframe>
                </div>
                <a href="#" class="basic visible-desktop" rel="<?php echo $post->ID; ?>">
                    <?php
                    $youtube_id = get_post_meta($post->ID, 'Youtube_Video_ID', 'true');
                    if (empty($youtube_id)) {
                        $youtube_id = '#';
                    }
                    ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/images/video-pic.jpg" />
                </a>
            </div>
        </div>
        <div id="content-main" class="middel_div_client-review" >
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1 id="main-heading-new-page"><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </article><!-- #post -->
            <?php endwhile; ?>
        </div>

    </div>
</div><!-- .inner-contents -->
<?php get_footer(); ?>
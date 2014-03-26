<?php
get_header();
global $data;
?>
<div  class="container" id="mov-top">
    <div  class="inner-contents">
        <div  id="content-main">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h2><?php the_title(); ?></h2>
                    <?php the_content(); ?>
                    <?php if ($data['check_sharebox'] == true) { ?>
                        <?php get_template_part('sharebox'); ?>
                    <?php } ?>
                    <?php if ($data['check_authorinfo'] == true) { ?>
                        <div id="author-info" class=" well clearfix">
                            <div class="author-image">
                                <a href="<?php echo $data['icon_google'];?>"><?php echo get_avatar(get_the_author_meta('user_email'), '35', ''); ?></a>
                            </div>   
                            <div class="author-bio">
                                <h4><?php _e('About the Author', 'law-firm'); ?></h4>
                                <?php the_author_meta('description'); ?>
                            </div>
                        </div>
                    <?php } ?>

                </article><!-- #post -->
            <?php endwhile; ?>
        </div>
        <?php get_sidebar('inner'); ?>
    </div>
    <?php /* The loop */ ?>

</div><!-- .inner-contents -->
</div><!-- .container -->
<?php get_footer(); ?>
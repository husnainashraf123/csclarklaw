<?php
/* Template name: New page Template */
get_header();
global $data;
?>
<div class="clearfix inner-page"></div>
<div  class="container" >
    <div class="inner-contents" >
        <div class="sidebar_new_page">
            <div class="video-div">
                <h1>To Learn More About <br />
                    <?php echo the_title(); ?> <br />
                    Watch This Important<br />
                    <?php
                    $video_duration = get_post_meta($post->ID, 'enter_number_of_minutes', 'true');
                    if (empty($video_duration)) {
                        $video_duration = 10;
                    }
                    echo $video_duration;
                    ?> Minute Video<br /><span></span></h1>
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
        <div id="content-main" class="new-page-content-div" >
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1 id="main-heading-new-page"><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </article><!-- #post -->
            <?php endwhile; ?>
            <div class="attorney-mini-bio-page">
                <div class="page-bio-left-div">
                    <a href="<?php echo $data['link_new_page_text']; ?>" onClick="_gaq.push(['_trackEvent', 'ProfilePageOpen', 'Url', '<?php echo the_permalink(); ?>']);">
                        <img src="<?php echo get_template_directory_uri() ?>/images/profile_img.png"/>
                        <span></span>
                        <h2><?php echo $data['review_profile_page']; ?> Reviews</h2>
                    </a>
                </div>
                <div class="page-bio-right-div">
                    <h1><?php echo $data['attorney_name_profile_page'] ?></h1>
                    <?php echo $data['bio_new_page_text']; ?>
                    <a href="<?php echo $data['link_new_page_text']; ?>" onClick="_gaq.push(['_trackEvent', 'ProfilePageOpen', 'Url', '<?php echo the_permalink(); ?>']);">View Full Profile</a>
                </div>
            </div>
            <?php if (function_exists('get_related_links')) : ?>
                <?php if (get_related_links()) : ?>   
                    <div class="related-links-page">
                        <h1>Related Articles</h1>
                        <div class="reccent_post_bg" style="width:100% !important;">
                            <ul class="list-style">
                                <?php $related_links = get_related_links(); ?>
                                <?php foreach ($related_links as $link): ?>
                                    <li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
                                <?php endforeach; ?>	
                            </ul>
                        </div>
                        <div class="reccent_post_btm_cornr"></div>
                    </div><!--reccent_post -->
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div><!-- .inner-contents -->
<?php get_footer(); ?>

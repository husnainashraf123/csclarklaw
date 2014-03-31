<?php
/* template name: Attorney Profile Template */
get_header();
?>
<div class="container">
    <div class="attorney-profile-outer-div">
        <div class="right-sidebar-attorney-profile">
            <h1><?php echo $data['attorney_name_profile_page']; ?></h1>
            <div class="video-iframe">
                <?php echo $data['video_profile_page']; ?>
            </div>
            <h2><?php echo $data['review_profile_page']; ?> Reviews<span><img src="<?php echo get_template_directory_uri(); ?>/images/big_star.png"></span></h2>

            <div class="tabs-div hidden-600">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a href="#why-client" data-toggle="tab"><?php echo $data['tab1_profile_page']; ?></a></li>
                    <li><a href="#other-attorney" data-toggle="tab"><?php echo $data['tab2_profile_page']; ?></a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade in active" id="why-client">
                        <?php echo $data['tab1_text_profile_page']; ?>
                        <span class="more-link"><a href="<?php echo $data['more_link_profile_page']; ?>">(More)</a></span>
                        <div class="bio-bottom-area">
                            <div class="star-pic"><img src="<?php echo get_template_directory_uri(); ?>/images/small_stars.png"></div>
                            <h4>32 Reviews</h4>
                        </div> 
                    </div>
                    <div class="tab-pane fade" id="other-attorney">
                        <?php echo $data['tab2_text_profile_page']; ?>
                        <span class="more-link"><a href="<?php echo $data['more_link_profile_page']; ?>">(More)</a></span>
                        <div class="bio-bottom-area">
                            <div class="star-pic"><img src="<?php echo get_template_directory_uri(); ?>/images/small_stars.png"></div>
                            <h4><?php echo $data['review_profile_page']; ?>  Reviews</h4>
                        </div> 
                    </div>
                </div>
                <script>
                    $(function() {
                        $('#myTab a:first').tab('show')
                    })
                </script>
            </div>

            <?php /* ..........Mobile............................ */ ?>
            <div class="panel-group hidden-desktop" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                <?php echo $data['tab1_profile_page']; ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <?php echo $data['tab1_text_profile_page']; ?>
                            <span class="more-link"><a href="<?php echo $data['more_link_profile_page']; ?>">(More)</a></span>
                            <div class="bio-bottom-area">
                                <div class="star-pic"><img src="<?php echo get_template_directory_uri(); ?>/images/small_stars.png"></div>
                                <h4><?php echo $data['review_profile_page']; ?>  Reviews</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                <?php echo $data['tab2_profile_page']; ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php echo $data['tab2_text_profile_page']; ?>
                            <span class="more-link"><a href="<?php echo $data['more_link_profile_page']; ?>">(More)</a></span>
                            <div class="bio-bottom-area">
                                <div class="star-pic"><img src="<?php echo get_template_directory_uri(); ?>/images/small_stars.png"></div>
                                <h4><?php echo $data['review_profile_page']; ?>  Reviews</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php /* ..........Mobile............................ */ ?>
        </div>
        <div class="left-div-attorney-profile">
            <?php while (have_posts()) : the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>

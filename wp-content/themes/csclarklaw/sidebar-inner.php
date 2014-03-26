<?php
global $data;
?>
<div class="pull-right  hidden-phone hidden-tablet" id="sidebar">

    <?php //if(in_category('family')){
    $first_cat  =   $data['select_pageCategory'];
    $categories = get_the_category( $id ) ;
    if($categories){
        foreach($categories as $category) { 
          $category_name    =   $category->name;
        }
        
    }
    //echo get_category(get_query_var('cat'))->name;
    ?>
     <?php
    if(in_category('bio-show') ){
            ?>
    <div class="box-bg-white full-width margin_top10">
        <h3 class="title-box-form brown_bg"><?php echo $data['bio_name']; ?></h3>
        <div class="line_seperator"></div>
        <?php if($data['bio_img']){ ?><span class="bio-img"><img src="<?php echo $data['bio_img']; ?>" alt="<?php echo $data['bio_name']; ?>" /></span><?php }?>
        <ul class="bio">
            <?php echo $data['bio_list']; ?>
        </ul>
    </div>
    <?php } ?>
    
    
    
    <?php if (function_exists('get_related_links')) : ?>
        <?php if (get_related_links()) : ?>   
            <div class="box-bg-white full-width margin_top10 related_article">
                <h3 class="title-box-form brown_bg">Related Articles</h3>
                <ul class="sidebar-links">
                    <?php $related_links = get_related_links(); ?>
                    <?php foreach ($related_links as $link): ?>
                        <li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
                    <?php endforeach; ?>	
                </ul>
            </div> 
        <?php endif; ?>
    <?php endif; ?>
    <?php
    if(in_category('crime') ){
            ?>
    <div class="box-bg-white full-width margin_top10 inner-book">
        <h3 class="title-box-form brown_bg"><?php echo $data['book_title_1']; ?></h3>
        <span class="book-img-inner"><img src="<?php echo $data['book1_img']; ?>" /></span>
        <p><?php echo $data['book_subtitle_1']; ?></p>
        <a href="#book1" role="button" class="btn-yellow pull-left zero-mar" data-toggle="modal"><?php echo $data['book_btn_text']; ?> <i class="icon-download-alt "></i></a>
    </div>
   
    <?php }?>
     <?php
    if(in_category('dwi') ){
            ?>
    <div class="box-bg-white full-width margin_top10 inner-book">
        <h3 class="title-box-form brown_bg"><?php echo $data['book_title_2']; ?></h3>
        <span class="book-img-inner"><img src="<?php echo $data['book2_img']; ?>" /></span>
        <p><?php echo $data['book_subtitle_2']; ?></p>
        <a href="#book2" role="button" class="btn-yellow pull-left zero-mar" data-toggle="modal"><?php echo $data['book_btn_text']; ?> <i class="icon-download-alt"></i></a>
    </div>
    <?php }?>
   
    <?php if ( (!is_page(1266))) { 
        if( (!is_page(1268))){
        ?>
        <div class="box full-width margin_top10 box-bg">
             <h3 class="title-box brown_bg">Free Initial Consultation</h3>
            <?php echo do_shortcode('[cForm]'); ?>
        </div>
    <?php } }
    ?>
</div>
<?php if (!is_page(72)) { ?>
    <div class="well well-large hidden-desktop margin_top10 pull-left full-width">
        <h2>Contact Us <a href="<?php bloginfo('url') ?>/contact-us/" class="btn  btn-success white-font"/>Get Help Now <i class="icon-envelope"></i></a></h2>
    </div>
<?php } ?>
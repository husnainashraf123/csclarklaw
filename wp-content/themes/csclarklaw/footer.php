<?php
global $data;
?>
 
<div id="book1" class="modal hide fade" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <?php if($data['book1_pdf']!=""){ ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-larg white-font"></i></button>
        <h3 id="book-title-1" class="hidden-phone hidden-tablet"><?php echo $data['book_title_1']; ?></h3>
    </div>
    <div class="modal-body popup-form">
        <p class="white-font hidden-phone hidden-tablet"><?php echo $data['book_subtitle_1']; ?></p>
        <form class="book form-horizontal" id="book" name="book" method="post" onsubmit="return false;" action="<?php echo get_template_directory_uri() ?>/ajax/book-request.php" >
            <input type="hidden" name="book" value="Crime" >
            <input type="hidden" name="bookid" value="1" >
            <input type="hidden" name="pdfurl"  value="<?php echo $data['book1_pdf']; ?>" >
            <div id="ErrorMsg"> </div>
            <div class="hidden-phone popup-img ">  <img src="<?php echo $data['book1_img']; ?>" /></div>
            <div class="span3">
                <p class="book-labels">Your Name *</p>
                <input type="text" name="name1" id="name" placeholder="Full Name">
                <p class="book-labels">Email *</p>
                <input type="text" name="email1" id="email" placeholder="Valid Email">
                <p class="book-labels">Phone *</p>
                <input type="text" name="phone1" class="phone-formate" placeholder="10 Digit Number" >
            </div>
            <div id="respons"></div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger hidden-phone hidden-tablet" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-success" id="btn-download" type="submit">Download Guide Now <i class="icon-download-alt"></i></button>
    </div>

</form>
       <?php }else{?>
 <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-larg white-font"></i></button>
        <h3 id="book-title-1" class="hidden-phone hidden-tablet"><?php echo $data['book_title_1']; ?></h3>
</div>   
<div class="modal-body popup-form">
        <p class="white-font hidden-phone hidden-tablet"><?php echo $data['book_subtitle_1']; ?></p>
        <div id="ErrorMsg"> </div>
            <div class="hidden-phone popup-img ">  <img src="<?php echo $data['book1_img']; ?>" /></div>
            <div class="span3">
                <p class="bokk_download_txt">Download Coming Soon<br> Check Back Shortly</p> 
            </div>
            <div id="respons"></div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger hidden-phone hidden-tablet" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>



<?php }?>
</div>

<div id="book2" class="modal hide fade" tabindex="1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
    <?php if($data['book2_pdf']!=""){ ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-larg white-font"></i></button>
        <h3 id="book-title-2"><?php echo $data['book_title_2']; ?></h3>
    </div>
    <div class="modal-body popup-form-2">
        <p class="white-font "><?php echo $data['book_subtitle_2']; ?></p>
        <form class="book form-horizontal " id="book-2" name="book-2" method="post" onsubmit="return false;" action="<?php echo get_template_directory_uri() ?>/ajax/book-request.php" >
            <input type="hidden" name="book" value="DWI" >
            <input type="hidden" name="bookid" value="2" >
            <input type="hidden" name="pdfurl" value="<?php echo $data['book2_pdf']; ?>" >
            <div id="ErrorMsg-2"> </div>
            <div class="hidden-phone popup-img">  <img src="<?php echo $data['book2_img']; ?>" /></div>
            <div class="span3">
                <p class="book-labels">Your Name *</p>
                <input type="text" name="name2" id="name" placeholder="Full Name">
                <p class="book-labels">Email *</p>
                <input type="text" name="email2" id="email" placeholder="Valid Email">
                <p class="book-labels">Phone *</p>
                <input type="text" name="phone2" class="phone-formate" placeholder="10 Digit Number" >
            </div>
            <div id="respons"></div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-success" id="btn-download-2" type="submit">Download Guide Now <i class="icon-download-alt"></i></button>
    </div>
   
</form>
        <?php }else{?>
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-larg white-font"></i></button>
        <h3 id="book-title-2" class="hidden-phone hidden-tablet"><?php echo $data['book_title_2']; ?></h3>
</div>   
<div class="modal-body popup-form">
        <p class="white-font hidden-phone hidden-tablet"><?php echo $data['book_subtitle_2']; ?></p>
        <div id="ErrorMsg"> </div>
            <div class="hidden-phone popup-img ">  <img src="<?php echo $data['book2_img']; ?>" /></div>
            <div class="span3">
                <p class="bokk_download_txt">Download Coming Soon<br> Check Back Shortly</p> 
            </div>
            <div id="respons"></div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger hidden-phone hidden-tablet" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
        <?php }?>
</div>
<footer id="colophon"  role="contentinfo">
<!--    <div class="footer_service_div container footer-color-text">
        Disclaimer:<br>
        This website is intended to provide general information regarding KEVIN J. ROACH, Attorney at Law and does not constitute legal advice. While the internet crosses State and National boundaries, this site is for informational purposes only and does not establish an attorney-client relationship in any way. Do not rely on the information in this site in lieu of consulting an attorney. Communication through e-mail or this site is not a secure method of communication. If you send e-mail to Kevin J. Roach, you will receive a reply, but the sending of e-mail or receipt of a response does not constitute the formation of a contract or create any obligation on the part of the sender or the recipient. A contract of representation can only be created with the Law Firm of Kevin J. Roach after you have signed a written contract with his office. If your matter has a time deadline that requires an urgent response please call us toll free at 1-866-519-0085.
</div>   -->
    <div class="container footer-border site-footer">
        <div id="footer-nav">
            <?php
            $args = array(
                'theme_location' => 'secondary',
                'depth' => 1,
                'container' => false,
                'menu_class' => '',
                'walker' => ''
            );
            wp_nav_menu($args);
            ?>
        </div>
        <div class="clearfix copyright-text"><p ><?php echo $data['text_copyright']; ?></p></div>
<!--                <div id="social_icons">
                    <a href="<?php echo $data['icon_facebook']; ?>" ><img src="<?php echo get_template_directory_uri() ?>/images/fb.png" alt="fb" /></a>
                    <a href="<?php echo $data['icon_twitter']; ?>" ><img src="<?php echo get_template_directory_uri() ?>/images/twitter.png" alt="twitter" /></a>
                    <a href="<?php echo $data['icon_linkedin']; ?>" ><img src="<?php echo get_template_directory_uri() ?>/images/linkedin.png" alt="linkedin" /></a>
                    <a href="<?php echo $data['icon_pinterest']; ?>" ><img src="<?php echo get_template_directory_uri() ?>/images/pinterest.png" alt="icon_pinterest" /></a>
                    <a href="<?php echo $data['icon_google']; ?>" ><img src="<?php echo get_template_directory_uri() ?>/images/google.png" alt="Google +" /></a>
        
                </div>-->      
    </div><!-- .site-info -->
</footer><!-- #colophon -->
</div><!-- #page -->
 
<?php wp_footer(); ?>

</body>
</html>
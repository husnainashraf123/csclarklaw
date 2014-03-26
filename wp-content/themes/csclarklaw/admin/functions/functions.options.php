<?php

add_action('init', 'of_options');

if (!function_exists('of_options')) {

    function of_options() {


        /* ----------------------------------------------------------------------------------- */
        /* TO DO: Add options/functions that use these */
        /* ----------------------------------------------------------------------------------- */

        //More Options
        $uploads_arr = wp_upload_dir();
        $all_uploads_path = $uploads_arr['path'];
        $all_uploads = get_option('of_uploads');
        $other_entries = array("Select a number:", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19");
        $body_repeat = array("no-repeat", "repeat-x", "repeat-y", "repeat");
        $body_pos = array("top left", "top center", "top right", "center left", "center center", "center right", "bottom left", "bottom center", "bottom right");

        $of_options_select = array("one", "two", "three", "four", "five");
        $args = array(
	'type'                     => array('page'),
	'child_of'                 => 0,
	'parent'                   => '',
	'orderby'                  => 'name',
	'order'                    => 'ASC',
	'hide_empty'               => 0,
	'hierarchical'             => 1,
	'exclude'                  => '',
	'include'                  => '',
	'number'                   => '',
	'taxonomy'                 => 'category',
	'pad_counts'               => false );
        $cagt   =   '';
       $categories = get_categories( $args );
       foreach($categories as $cate){
           $cagt[$cate->slug]  =  $cate->name ;
       }
       $cagtgory    = $cagt;  

        // Image Alignment radio box
        $of_options_thumb_align = array("alignleft" => "Left", "alignright" => "Right", "aligncenter" => "Center");

        // Image Links to Options
        $of_options_image_link_to = array("image" => "The Image", "post" => "The Post");

        $functionof_pages = array();
        $of_pages_obj = get_pages('sort_column=post_parent,menu_order');
        if ($of_pages_obj):
            foreach ($of_pages_obj as $of_page) {
                $of_pages[$of_page->ID] = $of_page->post_name;
            }
        endif;
        //print_r($of_pages);
        //echo implode("','", );
        //exit();

        /* -----------------------------The Theme Options Array-------------------------- */

        global $of_options;
        $of_options = array();

        $url = ADMIN_DIR . 'assets/images/';

        /* -----------General Settings------------------- */
        $of_options[] = array("name" => "General",
            "type" => "heading");

        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "External Script",
            "icon" => false,
            "type" => "info");

        $of_options[] = array("name" => "External Style Sheet",
            "desc" => "External Style Sheet or  your Google Fonts",
            "id" => "style_head",
            "std" => "",
            "type" => "textarea");

        $of_options[] = array("name" => "&lt;head&gt; Code",
            "desc" => "Paste your Google Analytics Code (or other) here.In head section",
            "id" => "script_head",
            "std" => "",
            "type" => "textarea");

        $of_options[] = array("name" => "&lt;body&gt; Code",
            "desc" => "Paste your code after &lt;body&gt; tag",
            "id" => "code_afterbody",
            "std" => "",
            "type" => "textarea");
        /* -----------Email Tempalte Settings------------------- */
        $of_options[] = array("name" => "Email Alerts",
            "type" => "heading");

        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Email Alerts Settings",
            "icon" => false,
            "type" => "info");

        $of_options[] = array("name" => "Enable SmartSheet Propogation ",
            "desc" => "Check to enable smartsheet alerts",
            "id" => "check_smartsheet",
            "std" => 1,
            "type" => "checkbox");

        $of_options[] = array("name" => "Lawyer Name:",
            "desc" => "will used for alerts",
            "id" => "lawyer_name",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Alert Emails",
            "desc" => "will used for alerts. Seprate with  comma.",
            "id" => "alert_emails",
            "std" => "seolawyers2012@gmail.com",
            "type" => "textarea");

        $of_options[] = array("name" => "Book Download Subject",
            "desc" => "Will used for alerts",
            "id" => "book_email_subject",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Phone Call Subject",
            "desc" => "Will used for alerts",
            "id" => "call_email_subject",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Books Donwload Email Template",
            "desc" => "You can use bellow shortcode in your template.<br /> <cod>[lawyername] <br />[name] <br />[email] <br />[phone]</code> ",
            "id" => "book_email_temp",
            "std" => "",
            "type" => "textarea");

        $of_options[] = array("name" => "Phone Email Template",
            "desc" => "You can use bellow shortcode in your template. <cod><br />[lawyername] <br />[caller-id]<br /> [duration]<br /> [timedate] <br />[dailled-number]</code> ",
            "id" => "phone_email_temp",
            "std" => "",
            "type" => "textarea");


        /* --------------------------Authoer Bio Settings---------------------------------------------- */
        $of_options[] = array("name" => "Author Bio",
            "type" => "heading");
        
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Author Bio Settings",
            "icon" => false,
            "type" => "info");
       
        $of_options[] = array("name" => "Lawyer Name",
            "desc" => "Display on author bio widget",
            "id" => "bio_name",
            "std" => "",
            "type" => "text");
        
        $of_options[] = array("name" => "Details Text",
            "desc" => "Please insert in formate of list",
            "id" => "bio_list",
            "std" => "",
            "type" => "textarea");
        
        $of_options[] = array("name" => "lawyer  Image",
            "desc" => "Upload bio image. <br />Note: Only PNG File",
            "id" => "bio_img",
            "std" => "",
            "mod" => "min",
            "type" => "media");
        
        // 2ND author
        
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "2nd Author Bio Settings",
            "icon" => false,
            "type" => "info");
         $of_options[] = array("name" => "Lawyer Name",
            "desc" => "Display on author bio widget",
            "id" => "bio_name_2",
            "std" => "",
            "type" => "text");
        
        $of_options[] = array("name" => "Details Text",
            "desc" => "Please insert in formate of list",
            "id" => "bio_list_2",
            "std" => "",
            "type" => "textarea");
        
        $of_options[] = array("name" => "lawyer  Image",
            "desc" => "Upload bio image. <br />Note: Only PNG File",
            "id" => "bio_img_2",
            "std" => "",
            "mod" => "min",
            "type" => "media");
        
        
        
        /* --------------------------Home Page Settings---------------------------------------------- */
        $of_options[] = array("name" => "Home",
            "type" => "heading");

        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Home General",
            "icon" => false,
            "type" => "info");
        $of_options[] = array("name" => "Slider Shotcode",
            "desc" => "Display on home page. e.g [rev_slider home]",
            "id" => "home_slider",
            "std" => "[rev_slider home]",
            "type" => "text");
        $of_options[] = array("name" => "Home Testimonial",
            "desc" => "Display on home page.",
            "id" => "home_testimonial",
            "std" => "",
            "type" => "textarea");
        $of_options[] = array("name" => "Testimonial Link Text ",
            "desc" => "Display on home page.<br /> e.g <strong>Read All Testimonials</strong>",
            "id" => "testimonial_link_text",
            "std" => "Read More >>",
            "type" => "text");
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Books #1 Settings",
            "icon" => false,
            "type" => "info");

        $of_options[] = array("name" => "Book  Title",
            "desc" => "It will be used for home page and inner pages.<br />For e.g <strong>Charged with a DUI?</strong>",
            "id" => "book_title_1",
            "std" => "Charged with a DUI?",
            "type" => "text");
        $of_options[] = array("name" => "Category",
         "desc" => "Select Page category name ",
         "id" => "book_category_1",
         "std" => "",
         "type" => "select",
         "options" => $cagtgory
            );
        $of_options[] = array("name" => "Book Sub-title",
            "desc" => "It will be used for home page and inner pages.<br />For e.g <br /><strong>(Helpful Guide to Navigate Your DUI case)</strong>",
            "id" => "book_subtitle_1",
            "std" => "(Helpful Guide to Navigate Your DUI case)",
            "type" => "text");
        $of_options[] = array("name" => "Book  Image",
            "desc" => "Upload book image. <br />Note: Only PNG File",
            "id" => "book1_img",
            "std" => "",
            "mod" => "min",
            "type" => "media");
        $of_options[] = array("name" => "PDF Book ",
            "desc" => "Upload book PDF.",
            "id" => "book1_pdf",
            "std" => "",
            "mod" => "min",
            "type" => "upload");
        ///////////////////////////////////////////
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Books # 2 Settings",
            "icon" => false,
            "type" => "info");
        $of_options[] = array("name" => "Book  Title",
            "desc" => "It will be used for home page and inner pages.<br />For e.g <strong>Charged with a Crime?</strong>",
            "id" => "book_title_2",
            "std" => "Charged with a DUI?",
            "type" => "text");
        $of_options[] = array("name" => "Category",
         "desc" => "Select Page category name ",
         "id" => "book_category_2",
         "std" => "",
         "type" => "select",
         "options" => $cagtgory
            );
        $of_options[] = array("name" => "Book  Sub-title",
            "desc" => "It will be used for home page and inner pages.<br />For e.g <br /><strong>(Helpful Guide to Navigate Your Criminal case)</strong>",
            "id" => "book_subtitle_2",
            "std" => "(Helpful Guide to Navigate Your Crime case)",
            "type" => "text");
        $of_options[] = array("name" => "Book  Image",
            "desc" => "Upload book image. <br />Note: Only PNG File",
            "id" => "book2_img",
            "std" => "",
            "mod" => "min",
            "type" => "media");

        $of_options[] = array("name" => "PDF Book ",
            "desc" => "Upload book PDF.",
            "id" => "book2_pdf",
            "std" => "",
            "mod" => "min",
            "type" => "upload");

        

        ////////////////3rd book/////////////////////
        ///////////////////////////////////////////
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Books # 3 Settings",
            "icon" => false,
            "type" => "info");
        $of_options[] = array("name" => "Book  Title",
            "desc" => "It will be used for home page and inner pages.<br />For e.g <strong>Charged with a Crime?</strong>",
            "id" => "book_title_3",
            "std" => "Charged with a DUI?",
            "type" => "text");
        $of_options[] = array("name" => "Category",
         "desc" => "Select Page category name ",
         "id" => "book_category_3",
         "std" => "",
         "type" => "select",
         "options" => $cagtgory
            );
        $of_options[] = array("name" => "Book  Sub-title",
            "desc" => "It will be used for home page and inner pages.<br />For e.g <br /><strong>(Helpful Guide to Navigate Your Criminal case)</strong>",
            "id" => "book_subtitle_3",
            "std" => "(Helpful Guide to Navigate Your Crime case)",
            "type" => "text");
        $of_options[] = array("name" => "Book  Image",
            "desc" => "Upload book image. <br />Note: Only PNG File",
            "id" => "book3_img",
            "std" => "",
            "mod" => "min",
            "type" => "media");

        $of_options[] = array("name" => "PDF Book ",
            "desc" => "Upload book PDF.",
            "id" => "book3_pdf",
            "std" => "",
            "mod" => "min",
            "type" => "upload");

        $of_options[] = array("name" => "Botton Text",
            "desc" => "It will be used for download button.<br />For e.g <strong>FREE DOWNLOAD</strong>",
            "id" => "book_btn_text",
            "std" => "FREE DOWNLOAD",
            "type" => "text");



        /* --------------------------Header Settings---------------------------------------------- */
        $of_options[] = array("name" => "Header",
            "type" => "heading");

        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Logo & Navigations",
            "icon" => false,
            "type" => "info");

        $of_options[] = array("name" => "Logo Upload",
            "desc" => "Upload your Logo",
            "id" => "media_logo",
            "std" => "",
            "mod" => "min",
            "type" => "media");

        $of_options[] = array("name" => "Address 1",
            "desc" => "Isert your address.It will be used for google map & header",
            "id" => "address1",
            "std" => "",
            "type" => "text");
              $of_options[] = array("name" => "Address 1 latitude",
            "desc" => "Isert your address.It will be used for google map & header",
            "id" => "latitude_address1",
            "std" => "",
            "type" => "text");
        $of_options[] = array("name" => "Address 1 longitude",
            "desc" => "Isert your address.It will be used for google map & header",
            "id" => "longitude_address1",
            "std" => "",
            "type" => "text");
        $of_options[] = array("name" => "Address 2",
            "desc" => "Isert your address.It will be used for google map & header",
            "id" => "address2",
            "std" => "",
            "type" => "text");
     
        $of_options[] = array("name" => "Address 2 latitude",
            "desc" => "Isert your address.It will be used for google map & header",
            "id" => "latitude_address2",
            "std" => "",
            "type" => "text");
        $of_options[] = array("name" => "Address 2 longitude",
            "desc" => "Isert your address.It will be used for google map & header",
            "id" => "longitude_address2",
            "std" => "",
            "type" => "text");
        $of_options[] = array("name" => "Phone Heading",
            "desc" => "Dispaly above the phone numbers",
            "id" => "phone_heading",
            "std" => "Call for Free Consultaion",
            "type" => "text");

        $of_options[] = array("name" => "Phone # 1",
            "desc" => "Used for click to call and header <br />Formate = (999) 999-9999",
            "id" => "phone1",
            "std" => "(999) 999-9999",
            "type" => "text");
        $of_options[] = array("name" => "Phone # 2",
            "desc" => "Used for click to call and header <br /> Formate = (999) 999-9999",
            "id" => "phone2",
            "std" => "",
            "type" => "text");

        /* ------------------------------------------------------------------------ */
        /* Footer
          /* ------------------------------------------------------------------------ */
        $of_options[] = array("name" => "Footer",
            "type" => "heading");
        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Footer Settings",
            "icon" => false,
            "type" => "info");
        $of_options[] = array("name" => "Footer Widget?",
            "desc" => "Select how many widgets you want in the footer section. <br /> Note: This will use to divide space.",
            "id" => "select_footerwidgets",
            "std" => "4",
            "type" => "select",
            "options" => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4'
                ));


        $of_options[] = array("name" => "Copyright Text",
            "desc" => "Enter your Copyright Text",
            "id" => "text_copyright",
            "std" => "Copyright &COPY; 2013 " . get_bloginfo('url') . "",
            "type" => "textarea");


        $of_options[] = array("name" => "Social Icons",
            "desc" => "Inser complete URL or leave blank to disable.",
            "id" => "introduction",
            "std" => "Social Media Icons",
            "icon" => true,
            "type" => "info");

        $of_options[] = array("name" => "Twitter Username",
            "desc" => "Enter your Twitter username",
            "id" => "icon_twitter",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Dribbble URL",
            "desc" => "Insert complete URL of  Dribbble Account",
            "id" => "icon_dribbble",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Flickr URL",
            "desc" => "Insert complete URL of  Flickr Account",
            "id" => "icon_flickr",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Facebook URL",
            "desc" => "Insert complete URL of  Facebook Account",
            "id" => "icon_facebook",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Skype URL",
            "desc" => "Insert complete URL of  Skype Account",
            "id" => "icon_skype",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Google+ URL",
            "desc" => "Insert complete URL of  Google+ Account",
            "id" => "icon_google",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "LinkedIn URL",
            "desc" => "Insert complete URL of  LinkedIn Account",
            "id" => "icon_linkedin",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Vimeo URL",
            "desc" => "Insert complete URL of  Vimeo Account",
            "id" => "icon_vimeo",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Tumblr URL",
            "desc" => "Insert complete URL of  Tumblr Account",
            "id" => "icon_tumblr",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "YouTube URL",
            "desc" => "Insert complete URL of  YouTube Account",
            "id" => "icon_youtube",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Picasa URL",
            "desc" => "Insert complete URL of  Picasa Account",
            "id" => "icon_picasa",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "DeviantArt URL",
            "desc" => "Insert complete URL of  DeviantArt Account",
            "id" => "icon_deviantart",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Behance URL",
            "desc" => "Insert complete URL of  Behance Account",
            "id" => "icon_behance",
            "std" => "",
            "type" => "text");

        $of_options[] = array("name" => "Pinterest URL",
            "desc" => "Insert complete URL of  Pinterest Account",
            "id" => "icon_pinterest",
            "std" => "",
            "type" => "text");

        /* ------------------------------------------------------------------------ */
        /* Typography
          /* ------------------------------------------------------------------------ */
        $of_options[] = array("name" => "Typography",
            "type" => "heading");

        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Body",
            "icon" => false,
            "type" => "info");

        $of_options[] = array("name" => "Body Text Font",
            "desc" => "Specify the Body font properties",
            "id" => "font_body",
            "std" => array('size' => '13px', 'face' => 'Helvetica', 'style' => 'normal', 'color' => '#000'),
            "type" => "typography");

        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Headlines",
            "icon" => false,
            "type" => "info");

        $of_options[] = array("name" => "H1 - Headline Font",
            "desc" => "Specify the H1 Headline font properties",
            "id" => "font_h1",
            "std" => array('size' => '28px', 'face' => 'Helvetica', 'style' => 'normal', 'color' => '#000'),
            "type" => "typography");

        $of_options[] = array("name" => "H2 - Headline Font",
            "desc" => "Specify the H2 Headline font properties",
            "id" => "font_h2",
            "std" => array('size' => '23px', 'face' => 'Helvetica', 'style' => 'normal', 'color' => '#000'),
            "type" => "typography");

        $of_options[] = array("name" => "H3 - Headline Font",
            "desc" => "Specify the H3 Headline font properties",
            "id" => "font_h3",
            "std" => array('size' => '18px', 'face' => 'Helvetica', 'style' => 'normal', 'color' => '#000'),
            "type" => "typography");

        $of_options[] = array("name" => "H4 - Headline Font",
            "desc" => "Specify the H4 Headline font properties",
            "id" => "font_h4",
            "std" => array('size' => '16px', 'face' => 'Helvetica', 'style' => 'normal', 'color' => '#000'),
            "type" => "typography");

        $of_options[] = array("name" => "H5 - Headline Font",
            "desc" => "Specify the H5 Headline font properties",
            "id" => "font_h5",
            "std" => array('size' => '15px', 'face' => 'Helvetica', 'style' => 'normal', 'color' => '#000'),
            "type" => "typography");

        $of_options[] = array("name" => "H6 - Headline Font",
            "desc" => "Specify the H6 Headline font properties",
            "id" => "font_h6",
            "std" => array('size' => '14px', 'face' => 'Helvetica', 'style' => 'normal', 'color' => '#000'),
            "type" => "typography");

        /* ------------------------------------------------------------------------ */
        /* Blog
          /* ------------------------------------------------------------------------ */
        $of_options[] = array("name" => "Blog",
            "type" => "heading");

        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Blog Options",
            "icon" => false,
            "type" => "info");

        $of_options[] = array("name" => "Show Posts",
            "desc" => "How many post you want to show on blog page",
            "id" => "blog_post",
            "std" => 10,
            "type" => "text");

        $of_options[] = array("name" => "Excerpt Length",
            "desc" => "How many word you want to show on blog page for each post",
            "id" => "blog_excerptlength",
            "std" => 30,
            "type" => "text");

        $of_options[] = array("name" => "Enable Share-Box on Post Detail",
            "desc" => "Check to enable Share-Box",
            "id" => "check_sharebox",
            "std" => 1,
            "type" => "checkbox");

        $of_options[] = array("name" => "Enable Author Info on Post Detail",
            "desc" => "Check to enable Author Info",
            "id" => "check_authorinfo",
            "std" => 1,
            "type" => "checkbox");

        $of_options[] = array("name" => "Enable Related Posts on Post Detail",
            "desc" => "Check to enable Related Posts",
            "id" => "check_relatedposts",
            "std" => 1,
            "type" => "checkbox");

        $of_options[] = array("name" => "Blog Excerpt Length",
            "desc" => "Default: 30. Used for blog page, archives & search results.",
            "id" => "text_excerptlength",
            "std" => "30",
            "type" => "text");

        $of_options[] = array("name" => "",
            "desc" => "",
            "id" => "general_heading",
            "std" => "Social Sharing Box Icons",
            "icon" => false,
            "type" => "info");

        $of_options[] = array("name" => "Enable Facebook in Social Sharing Box",
            "desc" => "Check to enable Facebook in Social Sharing Box",
            "id" => "check_sharingboxfacebook",
            "std" => 1,
            "type" => "checkbox");

        $of_options[] = array("name" => "Enable Twitter in Social Sharing Box",
            "desc" => "Check to enable Twitter in Social Sharing Box",
            "id" => "check_sharingboxtwitter",
            "std" => 1,
            "type" => "checkbox");

        $of_options[] = array("name" => "Enable LinkedIn in Social Sharing Box",
            "desc" => "Check to enable LinkedIn in Social Sharing Box",
            "id" => "check_sharingboxlinkedin",
            "std" => 1,
            "type" => "checkbox");

        $of_options[] = array("name" => "Enable Google in Social Sharing Box",
            "desc" => "Check to enable Google in Social Sharing Box",
            "id" => "check_sharingboxgoogle",
            "std" => 1,
            "type" => "checkbox");

        $of_options[] = array("name" => "Enable E-Mail in Social Sharing Box",
            "desc" => "Check to enable Google in E-Mail Sharing Box",
            "id" => "check_sharingboxemail",
            "std" => 1,
            "type" => "checkbox");

        /* ------------------------------------------------------------------------ */
        /* Backup
          /* ------------------------------------------------------------------------ */
        $of_options[] = array("name" => "Backup Options",
            "type" => "heading");

        $of_options[] = array("name" => "Backup and Restore Options",
            "id" => "of_backup",
            "std" => "",
            "type" => "backup",
            "desc" => 'You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back.',
        );

        $of_options[] = array("name" => "Transfer Theme Options Data",
            "id" => "of_transfer",
            "std" => "",
            "type" => "transfer",
            "desc" => 'You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click "Import Options".
						',
        );
    }

}
?>

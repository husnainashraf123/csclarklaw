<?php
/**
 * @package WordPress
 * @subpackage Speakeasy Theme
 * @since law Firm 1.0
 */
global $data;
require_once('admin/index.php'); // To add custom Theme Options Framework
require_once('css/custom-css.php'); // To add custom Style
require_once('framework/shortcodes.php'); // To add custom Style

function theme_setup() {

    add_theme_support('automatic-feed-links');
// Switches default core markup for search form, comment form, and comments
// to output valid HTML5.
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));

// This theme uses wp_nav_menu() in one location.
    register_nav_menu('primary', __('Top Menu', 'law-firm'));
    register_nav_menu('secondary', __('Footer Menu', 'law-firm'));

    /*
     * This theme uses a custom image size for featured images, displayed on
     * "standard" posts and pages.
     */
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(604, 270, true);

// This theme uses its own gallery styles.
    add_filter('use_default_gallery_style', '__return_false');
}

add_action('after_setup_theme', 'theme_setup');

/**
 * Enqueues scripts and styles for front end.
 *

 *
 * @return void
 */
function theme_scripts_styles() {
// Adds JavaScript to pages with the comment form to support sites with
// threaded comments (when in use).
    if (is_singular() && comments_open() && get_option('thread_comments'))
        wp_enqueue_script('comment-reply');
// Adds Masonry to handle vertical alignment of footer widgets.
    if (is_active_sidebar('sidebar-1'))
        wp_enqueue_script('jquery-masonry');
// Loads JavaScript file with functionality specific to Twenty Thirteen.
    wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/functions.js', array('jquery'), '1.0', true);
    wp_enqueue_script('waypoints-sticky', get_template_directory_uri() . '/js/waypoints-sticky.js', array('jquery'), '1.2', true);
    wp_enqueue_script('bootstrap-script', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('maskedinput', get_template_directory_uri() . '/js/jquery.maskedinput.js', array('jquery'), '1.3', true);
    wp_enqueue_script('jquery-ddslick', get_template_directory_uri() . '/js/jquery.ddslick.min.js', array('jquery'), '1.0', true);
// wp_enqueue_script('validate', get_template_directory_uri() . '/js/jquery.validate.js', array('jquery'), '1.2', true);
    wp_enqueue_script('validator', get_template_directory_uri() . '/js/validator.js', array('jquery'), '1.2', true);
    wp_enqueue_script('fileDownload', get_template_directory_uri() . '/js/jquery.fileDownload.js', array('jquery'), '1.2', true);


// Loads our main stylesheet.
    wp_enqueue_style('theme-style', get_stylesheet_uri(), array(), '1.0');

// Loads the stylesheet.

    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array('theme-style'), '1.0');
    wp_enqueue_style('bootstrap-responsive', get_template_directory_uri() . '/css/bootstrap-responsive.css', array('theme-style'), '1.0');
    wp_enqueue_style('google-fonts', get_template_directory_uri() . '/googlefonts.php', array('theme-style'), '1.1');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css', array('theme-style'), '1.0');
// Loads the Internet Explorer specific stylesheet.
    wp_enqueue_style('theme-ie', get_template_directory_uri() . '/css/ie.css', array('theme-style'), '1.0');
    wp_style_add_data('theme-ie', 'conditional', 'lt IE 9');
}

add_action('wp_enqueue_scripts', 'theme_scripts_styles');

/* Custom Excerpt Length */

function excerpt_length($length) {
    global $data;
    return $data['blog_excerptlength'];
}

add_filter('excerpt_length', 'excerpt_length');

function excerpt_more($more) {
    global $post;
    return '… <a href="' . get_permalink($post->ID) . '" class="read-more-link">' . '' . __('Read More', 'law-firm') . ' &rarr;' . '</a>';
}

add_filter('excerpt_more', 'excerpt_more');

/**
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function theme_wp_title($title, $sep) {
    global $paged, $page;

    if (is_feed())
        return $title;

// Add the site name.
    $title .= get_bloginfo('name');

// Add the site description for the home/front page.
    $site_description = get_bloginfo('description', 'display');
    if ($site_description && ( is_home() || is_front_page() ))
        $title = "$title $sep $site_description";

// Add a page number if necessary.
    if ($paged >= 2 || $page >= 2)
        $title = "$title $sep " . sprintf(__('Page %s', 'theme'), max($paged, $page));

    return $title;
}

add_filter('wp_title', 'theme_wp_title', 10, 2);

/**
 * Registers two widget areas.
 *

 *
 * @return void
 */
function theme_widgets_init() {
    register_sidebar(array(
        'name' => __('Main Widget Area', 'theme'),
        'id' => 'sidebar-1',
        'description' => __('Appears in the footer section of the site.', 'law-firm'),
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '<',
    ));

    register_sidebar(array(
        'name' => __('Secondary Widget Area', 'theme'),
        'id' => 'sidebar-2',
        'description' => __('Appears on posts and pages in the sidebar.', 'law-firm'),
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
    ));
}

add_action('widgets_init', 'theme_widgets_init');

if (!function_exists('theme_paging_nav')) :

    /**
     * Displays navigation to next/previous set of posts when applicable.
     *
     * @return void
     */
    function theme_paging_nav() {
        global $wp_query;

// Don't print empty markup if there's only one page.
        if ($wp_query->max_num_pages < 2)
            return;
        ?>
        <nav class="navigation paging-navigation" role="navigation">
            <h1 class="screen-reader-text"><?php _e('Posts navigation', 'law-firm'); ?></h1>
            <div class="nav-links">

                <?php if (get_next_posts_link()) : ?>
                    <div class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', 'law-firm')); ?></div>
                <?php endif; ?>

                <?php if (get_previous_posts_link()) : ?>
                    <div class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&rarr;</span>', 'law-firm')); ?></div>
                <?php endif; ?>

            </div><!-- .nav-links -->
        </nav><!-- .navigation -->
        <?php
    }

endif;

if (!function_exists('theme_post_nav')) :

    /**
     * Displays navigation to next/previous post when applicable.
     *
     * @return void
     */
    function theme_post_nav() {
        global $post;

// Don't print empty markup if there's nowhere to navigate.
        $previous = ( is_attachment() ) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
        $next = get_adjacent_post(false, '', false);

        if (!$next && !$previous)
            return;
        ?>
        <nav class="navigation post-navigation" role="navigation">
            <h1 class="screen-reader-text"><?php _e('Post navigation', 'theme'); ?></h1>
            <div class="nav-links">

                <?php previous_post_link('%link', _x('<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'theme')); ?>
                <?php next_post_link('%link', _x('%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'theme')); ?>

            </div><!-- .nav-links -->
        </nav><!-- .navigation -->
        <?php
    }

endif;

/**
 * Returns the URL from the post.
 *
 * @uses get_url_in_content() to get the URL in the post meta (if it exists) or
 * the first link found in the post content.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 *
 * @return string The Link format URL.
 */
function theme_get_link_url() {
    $content = get_the_content();
    $has_url = get_url_in_content($content);

    return ( $has_url ) ? $has_url : apply_filters('the_permalink', get_permalink());
}

/**
 * Extends the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Active widgets in the sidebar to change the layout and spacing.
 * 3. When avatars are disabled in discussion settings.
 *
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function theme_body_class($classes) {
    if (!is_multi_author())
        $classes[] = 'single-author';

    if (is_active_sidebar('sidebar-2') && !is_attachment() && !is_404())
        $classes[] = 'sidebar';

    if (!get_option('show_avatars'))
        $classes[] = 'no-avatars';

    return $classes;
}

add_filter('body_class', 'theme_body_class');

/**
 * Adjusts content_width value for video post formats and attachment templates.
 * @return void
 */
function theme_content_width() {
    global $content_width;

    if (is_attachment())
        $content_width = 724;
    elseif (has_post_format('audio'))
        $content_width = 484;
}

add_action('template_redirect', 'theme_content_width');

/**
 * Extended Walker class for use with the
 * Twitter Bootstrap toolkit Dropdown menus in Wordpress.
 * Edited to support n-levels submenu.
 * @author johnmegahan https://gist.github.com/1597994, Emanuele 'Tex' Tessore https://gist.github.com/3765640
 */
class BootstrapNavMenuWalker extends Walker_Nav_Menu {

    function start_lvl(&$output, $depth) {
        $indent = str_repeat("\t", $depth);
        $submenu = ($depth > 0) ? ' sub-menu' : '';
        $output .= "\n$indent<ul class=\"dropdown-menu$submenu depth_$depth\">\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';
        $li_attributes = '';
        $class_names = $value = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
// managing divider: add divider class to an element to get a divider before it.
        $divider_class_position = array_search('divider', $classes);
        if ($divider_class_position !== false) {
            $output .= "<li class=\"divider\"></li>\n";
            unset($classes[$divider_class_position]);
        }
        $classes[] = ($args->has_children) ? 'dropdown' : '';
        $classes[] = ($item->current || $item->current_item_ancestor) ? 'active' : '';
        $classes[] = 'menu-item-' . $item->ID;
        if ($depth && $args->has_children) {
            $classes[] = 'dropdown-submenu';
        }
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = ' class="' . esc_attr($class_names) . '"';
        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';
        $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';
        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .=!empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= ($args->has_children) ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= ($depth == 0 && $args->has_children) ? ' <b class="caret"></b></a>' : '</a>';
        $item_output .= $args->after;
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
//v($element);
        if (!$element)
            return;
        $id_field = $this->db_fields['id'];
//display this element
        if (is_array($args[0]))
            $args[0]['has_children'] = !empty($children_elements[$element->$id_field]);
        else if (is_object($args[0]))
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        $cb_args = array_merge(array(&$output, $element, $depth), $args);
        call_user_func_array(array(&$this, 'start_el'), $cb_args);
        $id = $element->$id_field;
// descend only when the depth is right and there are childrens for this element
        if (($max_depth == 0 || $max_depth > $depth + 1 ) && isset($children_elements[$id])) {
            foreach ($children_elements[$id] as $child) {
                if (!isset($newlevel)) {
                    $newlevel = true;
//start the child delimiter
                    $cb_args = array_merge(array(&$output, $depth), $args);
                    call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
                }
                $this->display_element($child, $children_elements, $max_depth, $depth + 1, $args, $output);
            }
            unset($children_elements[$id]);
        }
        if (isset($newlevel) && $newlevel) {
//end the child delimiter
            $cb_args = array_merge(array(&$output, $depth), $args);
            call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
        }
//end this element
        $cb_args = array_merge(array(&$output, $element, $depth), $args);
        call_user_func_array(array(&$this, 'end_el'), $cb_args);
    }

}

// send ifbyphone email
if ($_REQUEST['action'] != '' and $_REQUEST['action'] == 'ifbyphone') {
// Remove spaces from phone number
    $number_1 = str_replace(' ', '', trim($data['phone1']));
    $number_1 = str_replace('-', '', trim($data['phone1']));
    $number_2 = str_replace(' ', '', trim($data['phone2']));
    $number_2 = str_replace('-', '', trim($data['phone2']));
// Remove Special Characters
    $number_1 = preg_replace('/[^0-9\-]/', '', $number_1);
    $number_2 = preg_replace('/[^0-9\-]/', '', $number_2);
//check if number is not empty
    if ($_REQUEST['phone-number'] == $number_1 || $_REQUEST['phone-number'] == $number_2) {
        $subject = "Incoming Call Using Your Website's Tracking#";
        $lead_source = 'Website-TX';

        /* ............................................. */
        if (empty($_REQUEST['caller-id'])) {
            $caller_id = 'Caller ID Blocked';
            $not_send = 0;
        } else {
            $caller_id = formatPhone($_REQUEST['caller-id']);
            $not_send = 1;
        }
        /* ............................................. */

        sendmail($subject, $lead_source, $caller_id, $not_send);
    }
}

function sendmail($subject, $lead_source, $caller_id, $not_send) {
    global $data;
    $admin_name = $data['lawyer_name'];
    $site_url = substr(get_bloginfo('url'), 7);
    $ifbyphone_number = $caller_id;

    if ($not_send == 1) {
        ini_set('display_error', 0);
        $email = $data['alert_emails'];

        $reply_to = 'info@' . substr(get_bloginfo('url'), 11);
        $email_temp = $data['phone_email_temp'];
        $email_temp = @str_replace("[caller-id]", $ifbyphone_number, $email_temp);
        $email_temp = @str_replace("[duration]", $_REQUEST['duration'], $email_temp);
        $email_temp = @str_replace("[timedate]", date('m/d/Y'), $email_temp);
        $email_temp = @str_replace("[lawyername]", $admin_name, $email_temp);
        $email_temp = @str_replace("[dailled-number]", formatPhone($_REQUEST['phone-number']), $email_temp);
        // To send HTML mail, the Content-type header must be set
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: $site_url " . "\r\n";
        $headers .= 'Reply-To: ' . $reply_to . "\r\n";
        // Mail it
        $respons = mail($email, $subject, $email_temp, $headers);
    }

    if ($data['check_smartsheet'] == 1) {
        /* Smart Sheet papulate */
        date_default_timezone_set("America/New_York");
        $lead_data2 = array();
        $main_sheet_id = '8eda7e4a0e5548fda3cc511af3e4e729';
        $lead_data2['ctlForm'] = 'ctlForm';
        $lead_data2['formName'] = 'fn_row';
        $lead_data2["formAction"] = "fa_insertRow";
        $lead_data2["parm1"] = $main_sheet_id;
        $lead_data2["parm2"] = "";
        $lead_data2["parm3"] = "";
        $lead_data2["parm4"] = "";
        $lead_data2["sk"] = "";
        $lead_data2["SFReturnURL"] = "";
        $lead_data2["SFImageURL"] = "";
        $lead_data2["SFImage2URL"] = "";
        $lead_data2["SFTaskNumber"] = "";
        $lead_data2["SFTaskID"] = "";
        $lead_data2["SFInstructions"] = "";
        $lead_data2["EQBCT"] = $main_sheet_id;
        $lead_data2["29239059"] = date('m/d/Y'); /* Date */
        $lead_data2["29239060"] = 'Phone Call'; /* Phone call */
        $lead_data2["29239061"] = "[$admin_name] - $site_url "; /* Client Name */
        $lead_data2["29239058"] = $ifbyphone_number; /* Caller id */
        $lead_data2["29241150"] = $_REQUEST['duration']; /* Call duration */
        $lead_data2["30451525"] = $lead_source; /* Lead Type */
        $lead_data2["29239062"] = ' '; /* Caller name */
        $lead_data2["29239063"] = ' '; /* Caller Email */
        $lead_data2["29241135"] = ' '; /* Book Type */
        $lead_data2["29241136"] = ' '; /* Office Location */
//create the final string to be posted using implode()
        $post_items_2 = array();
        foreach ($lead_data2 as $key => $value) {
            $post_items_2[] = $key . '=' . $value;
        }
        $post_string_2 = implode('&', $post_items_2);
//create cURL connection
        $url_connection_2 = curl_init('https://www.smartsheet.com/b/publish');
//set options
        curl_setopt($url_connection_2, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($url_connection_2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/18.6.872.0 Safari/535.2 UNTRUSTED/1.0 3gpp-gba UNTRUSTED/1.0");
        curl_setopt($url_connection_2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url_connection_2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($url_connection_2, CURLOPT_FOLLOWLOCATION, 1);
//set data to be posted
        curl_setopt($url_connection_2, CURLOPT_POSTFIELDS, $post_string_2);
//perform our request
        $result_2 = curl_exec($url_connection_2);
//show information regarding the request
        curl_getinfo($url_connection_2);
        curl_errno($url_connection_2) . '-' . curl_error($url_connection_2);
//close the connection
        curl_close($url_connection_2);
    } // EOF Smartsheet check
}

function formatPhone($num) {
    $num = preg_replace('/[^0-9]/', '', $num);
    $len = strlen($num);

    if ($len == 10) {
        $num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1)  $2 - $3 ', $num);
        return $num;
    } else {
        return '0';
    }
}
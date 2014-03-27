<?php
/*

  Plugin Name: Case Evalutation form

  Plugin URI: www.speakeasymarketinginc.com

  Description: Attoryney Consultant form with custom option of CSS.Very easy to install and theme otptions

  Version: 1.0

  Author: Muhammad Hafeez

  Author URI: masifm88@gmail.com

  License: GPL v2 or higher

  License URI: License URI: http://www.gnu.org/licenses/gpl-2.0.html

 * 



 * Installation:

 * 

 * 1. Download and unzip the latest release zip file.

 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.

 * 3. Upload the entire plugin directory to your '/wp-content/plugins/' directory.

 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.

 * 

 * Usage:

 * 

 * 1. Navigate to the new caseForm menu in the Wordpress Administration Panel.

 *

 */

/* Add Menu in dashboard sidebar */

if (!class_exists("caseForm")) {



    class caseForm {

        var $_version = '1.0';
        var $_name = 'caseForm';
        var $_usedInputs = array();
        var $_pluginPath = '';
        var $_general = 'caseForm';
        var $_pluginRelativePath = '';
        var $_pluginURL = '';
        var $_selfLink = '';
        var $_supportURL = 'mailto:masifm88@gmail.com';

        function caseForm() {

            $this->_pluginPath = dirname(__FILE__);

            $this->_pluginRelativePath = ltrim(str_replace('\\', '/', str_replace(rtrim(ABSPATH, '\\\/'), '', $this->_pluginPath)), '\\\/');

            $this->_pluginURL = site_url() . '/' . $this->_pluginRelativePath;

            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {

                $this->_pluginURL = str_replace('http://', 'https://', $this->_pluginURL);
            }

            $this->_selfLink = array_shift(explode('?', $_SERVER['REQUEST_URI'])) . '?page=' . $this->_var;



            // Admin.

            if (is_admin()) {

                add_action('admin_menu', array($this, 'caseform_link')); // Add menu in admin.
                // add_action('wp_dashboard_setup', array( &$this, 'add_dashboard_widgets' ) );

                add_action('admin_init', array(&$this, 'init_admin')); // Run on admin initialization.

                register_activation_hook(__FILE__, array(&$this, 'caseform_table'));
            }

            add_action('init', array(&$this, 'get_form_case_parameters')); // Run on admin initialization.

            add_shortcode('caseForm', array(&$this, 'get_caseForm'));
        }

        function caseform_table() {

            require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

            global $wpdb, $table_prefix;

            $table_name = $wpdb->prefix . "caseformleads";

            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (

  `lead_id` int(255) NOT NULL AUTO_INCREMENT,

  `lead_string` text,

  `lead_time` datetime DEFAULT NULL,

  PRIMARY KEY (`lead_id`)

) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

            dbDelta($sql);
        }

        function init_admin() {

            register_setting('caseform_options', 'caseform_setting_options');



            add_filter('theme_head', array(&$this, 'get_caseForm')); // Run on admin initialization.
        }

        function caseform_validate($input) {

            // Say our text option must be safe text with no HTML tags

            $input['namelabel'] = wp_filter_nohtml_kses($input['namelabel']);

            $input['namereq'] = wp_filter_nohtml_kses($input['namereq']);

            $input['emaillabel'] = wp_filter_nohtml_kses($input['emaillabel']);

            $input['emailreq'] = wp_filter_nohtml_kses($input['emailreq']);

            $input['phonelabel'] = wp_filter_nohtml_kses($input['phonelabel']);

            $input['phonereq'] = wp_filter_nohtml_kses($input['phonereq']);

            $input['textarealabel'] = wp_filter_nohtml_kses($input['textarealabel']);

            $input['btnclass'] = wp_filter_nohtml_kses($input['btnclass']);

            return $input;
        }

        function caseform_link() {

            // Add main menu (default when clicking top of menu)

            add_menu_page('caseForm Options', $this->_name, 'administrator', $this->_general, array($this, 'caseForm_case_evaluation_options'), $this->_pluginURL . '/images/case_evaluation-icon.png');

            //Sub pages

            add_submenu_page($this->_general, 'caseForm Options with ' . $this->_name, 'caseForm Options', 'administrator', $this->_general, array(&$this, 'caseForm_case_evaluation_options'));

            //add_submenu_page($this->_general, 'caseForm Options', 'caseForm Options',  'administrator', 'edit-options', array($this, 'sem_case_evaluation_options'));

            add_submenu_page($this->_general, 'Email Template', 'Email Settings', 'administrator', 'case_email_settings', array($this, 'case_email_template'));

            //add_submenu_page($this->_general, 'Support', 'Support', 'administrator', 'caseform_support', array($this, 'caseform_support'));
        }

        function add_dashboard_widgets() {

            wp_add_dashboard_widget('pb_caseForm', 'caseForm', array(&$this, 'dashboard_stats'));
        }

        function caseForm_case_evaluation_options() {



            if (!isset($_REQUEST['settings-updated']))
                $_REQUEST['settings-updated'] = false;

            wp_enqueue_style('dashboard');

            wp_print_styles('dashboard');

            wp_enqueue_script('dashboard');

            wp_print_scripts('dashboard');

            // Load scripts and CSS used on this page.

            $this->admin_scripts();
            ?>

            <div class="wrap">

                <h2>Getting Started with  <?php echo $this->_name; ?> v<?php echo $this->_version; ?></h2>

                <div class="postbox-container"  style="width:70%;">

                    <div id="breadcrumbssupport" class="postbox">
                    <div class="inside">

                            <?php if (false !== $_REQUEST['settings-updated']) : ?>

                                <div class="updated fade"><p><strong><?php _e('caseForm settings are saved successfully.', 'caseform'); ?></strong></p></div>

                            <?php endif; ?>

                            <h2>How to Setup?</h2>

                            <ol>

                                <li><?php echo $this->_name; ?>  is very easy to integration, just choose label names and check checkboxes for required fields.</li>

                                <li>Assign any class name to submit button.</li>

                                <li>Just Use shortcode <code>[caseForm]</code> or PHP function <code><?php highlight_string("<?php echo  get_caseForm() ;  ?>"); ?></code> to insert form anywhere in your theme templates files.</li>

                                <li>You done it. Enjoy</li>

                            </ol>    

<form method="post" action="options.php">
    <table class="form-table" id="form-settings">
        <?php settings_fields('caseform_options'); ?>
        <?php
        $caseForm_options = get_option('caseform_setting_options');
        ?>
        <tr>
            <th scope="row">
                <label for="namelabel">Name Field Label</label>
            </th>
            <td>
                <input name="caseform_setting_options[namelabel]"  type="text" class="admin-text-field" id="caseform_setting_options[namelabel]" value=" <?php $this->EmptyFieldCheck("Your Name", $caseForm_options['namelabel']) ?>"/>
            </td>
            <td>
                <input name="caseform_setting_options[namereq]"  type="checkbox"   id="namereq" <?php checked($caseForm_options['namereq'] == 'on'); ?>/>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="emaillabel">Email Field Label</label>
            </th>
            <td>
                <input name="caseform_setting_options[emaillabel]" type="text" class="admin-text-field" id="caseform_setting_options[emaillabel]" value="<?php $this->EmptyFieldCheck("Your Email", $caseForm_options['emaillabel']) ?>"/>
            </td>
            <td>
                <input name="caseform_setting_options[emailreq]" type="checkbox"   id="emailreq" <?php checked($caseForm_options['emailreq'] == 'on'); ?> />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="Streetlabel">Street Address</label>
            </th>
            <td>
                <input name="caseform_setting_options[Streetlabel]"  type="text" class="admin-text-field" id="caseform_setting_options[Streetlabel]" value="<?php $this->EmptyFieldCheck("Street Address", $caseForm_options['Streetlabel']) ?>"/>
            </td>
            <td>
                <input name="caseform_setting_options[streetreq]" type="checkbox"   id="streetreq" <?php checked($caseForm_options['streetreq'] == 'off'); ?>/>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="citylabel">City</label>
            </th>
            <td>
                <input name="caseform_setting_options[citylabel]"  type="text" class="admin-text-field" id="caseform_setting_options[citylabel]" value="<?php $this->EmptyFieldCheck("City ", $caseForm_options['citylabel']) ?>"/>
            </td>
            <td>
                <input name="caseform_setting_options[cityreq]" type="checkbox"   id="cityreq" <?php checked($caseForm_options['cityreq'] == 'off'); ?>/>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="phonelabel">Phone Field Label</label>
            </th>
            <td>
                <input name="caseform_setting_options[phonelabel]"  type="text" class="admin-text-field" id="caseform_setting_options[phonelabel]" value="<?php $this->EmptyFieldCheck("Phone #", $caseForm_options['phonelabel']) ?>"/>
            </td>
            <td>
                <input name="caseform_setting_options[phonereq]" type="checkbox"   id="phonereq" <?php checked($caseForm_options['phonereq'] == 'on'); ?>/>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="textarealabel">Textarea Label</label>
            </th>
            <td>
                <input name="caseform_setting_options[textarealabel]"  type="text" class="admin-text-field" id="textarealabel"  value="<?php $this->EmptyFieldCheck("Tell us what happend", $caseForm_options['textarealabel']) ?>"/>
            </td>
            <td>
                <input name="caseform_setting_options[textareareq]" type="checkbox" id="textareareq" <?php checked($caseForm_options['textareareq'] == 'on'); ?> />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="btnclass">Button Class</label>
            </th>
            <td>
                <input name="caseform_setting_options[btnclass]"  type="text" class="admin-text-field" id="btnclass" value="<?php $this->EmptyFieldCheck("btn-case_evaluation", $caseForm_options['btnclass']) ?>"/>
            </td>
        </tr>
    </table>

    <p class="submit">

        <input type="submit" class="button-primary" value="<?php _e('Save Settings', 'caseform'); ?>" />

    </p>
</form> 



                        </div>

                    </div>



                </div>

                <?php include'inc/about-authors.php'; ?>





            </div>

            <?php
        }

        function case_email_template() {



            if (!isset($_REQUEST['settings-updated']))
                $_REQUEST['settings-updated'] = false;

            wp_enqueue_style('dashboard');

            wp_print_styles('dashboard');

            wp_enqueue_script('dashboard');

            wp_print_scripts('dashboard');

            //wp_enqueue_script('tiny_mce');
            // Load scripts and CSS used on this page.

            wp_enqueue_script('common');

            wp_enqueue_script('jquery-color');

            wp_print_scripts('editor');

            if (function_exists('add_thickbox'))
                add_thickbox();

            wp_print_scripts('media-upload');

            if (function_exists('wp_tiny_mce'))
                wp_tiny_mce();

            wp_admin_css();

            wp_enqueue_script('utils');

            do_action("admin_print_styles-post-php");

            do_action('admin_print_styles');

            $this->admin_scripts();
            ?>

            <div class="wrap">

                <h2>Update your email preferences </h2>



                <div class="postbox-container"  style="width:70%;">

                    <div id="breadcrumbssupport" class="postbox">



                        <div class="inside">

                            <?php if (false !== $_REQUEST['settings-updated']) : ?>

                                <div class="updated fade"><p><strong><?php _e('Email alert settings have been saved.', 'caseform'); ?></strong></p></div>

                            <?php endif; ?>

                            <form method="post" action="options.php">

                                <table class="form-table" id="email-settings">

                                    <?php settings_fields('caseform_options'); ?>

                                    <?php
                                    $caseForm_options = get_option('caseform_setting_options');
                                    ?>

                                    <tr>

                                        <th scope="row">

                                            <label for="namelabel">Email ID:</label>

                                        </th>

                                        <td>

                                            <textarea name="caseform_setting_options[emails]" class="admin-textarea"  id="caseform_setting_options[emails]"><?php $this->EmptyFieldCheck(get_bloginfo('admin_email'), $caseForm_options['emails']) ?></textarea>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th scope="row">

                                            <label for="namelabel">Email Template:</label>

                                        </th>

                                        <td>



                                            <?php
                                            $settings = array(
                                                'textarea_name' => 'caseform_setting_options[emailtemp]', //name you want for the textarea
                                                'quicktags' => true,
                                                'tinymce' => true
                                            );

                                            if (empty($caseForm_options['emailtemp'])) {

                                                $txtcontents = file_get_contents($this->_pluginURL . '/inc/email-temp.txt');
                                            } else {

                                                $txtcontents = $caseForm_options['emailtemp'];
                                            }

                                            the_editor($txtcontents, 'caseform_setting_options[emailtemp]', $settings);
                                            ?>



                                        </td>



                                    </tr>

                                    <tr>

                                        <td></td>

                                        <td >

                                            <p class="submit">

                                                <input type="submit" class="button-primary" value="<?php _e('Save Settings', 'caseform'); ?>" />

                                            </p>

                                        </td>

                                    </tr>



                                </table>



                            </form> 



                        </div>

                    </div>



                </div>

                <?php include'inc/about-authors.php'; ?>





            </div>

            <?php
        }

        public function EmptyFieldCheck($DefaultValue, $DefinedValue) {

            if ($DefinedValue == '') {

                echo $DefaultValue;
            } else {

                echo $DefinedValue;
            }
        }

        public function get_caseForm() {

            $caseForm_options = get_option('caseform_setting_options');

            if (!is_admin()) {

                wp_enqueue_script('the_js', plugins_url('js/jquery.validate.js', __FILE__));

                wp_enqueue_script('the_js2', plugins_url('js/validate.js', __FILE__));

                wp_enqueue_script('the_js3', plugins_url('js/jquery.maskedinput-1.3.js', __FILE__));

                session_start();
            }
            ?>

    <!--<form action = "<?php bloginfo('url') ?>?action=caseform&redirect_url=case-thank-you" method = "post" name = "caseform" id = "caseform">-->

            <div class="caseForm_widget">            

                <form action = "" method = "post" name = "caseform" id = "caseform">
                    <input type="text" name="caseformrobot" style="display: none;">
                    <input type="hidden" value="case_evaluation" name="caseform_type">
                    <input id="caseformlink" type="hidden" name="caseformlink">
                    <p><?php
                        $this->EmptyFieldCheck("Your Name", $caseForm_options['namelabel']);
                        $this->get_required('namereq')
                        ?> <br>
                        <span>
                <input type="text" class = "cfom-text" id="cfom-text" name = "caseformname"></span>
                    </p>
                       <p><?php
                        $this->EmptyFieldCheck("Your Email", $caseForm_options['emaillabel']);

                        $this->get_required('emailreq')
                        ?> <br>

                        <span>

                            <input type = "text" class = "cfom-text" id="caseformemail" name = "caseformemail">

                        </span>
                    </p>
                    <p><?php
                        $this->EmptyFieldCheck("Street Address", $caseForm_options['streetlabel']);
                        $this->get_required('streetreq')
                        ?> <br>
                        <span>
                <input type="text" class = "cfom-text" id="cfom-text" name="caseformstreet"></span>
                    </p>
                     <p><?php
                        $this->EmptyFieldCheck("City", $caseForm_options['citylabel']);
                        $this->get_required('cityreq')
                        ?> <br>
                        <span>
                <input type="text" class = "cfom-text" id="cfom-text" name="caseformcity"></span>
                    </p>
                     <p><?php
                        $this->EmptyFieldCheck("State", $caseForm_options['statelabel']);
                        $this->get_required('statereq')
                        ?> <br>
                        <span>
                <input type="text" class = "cfom-text" id="cfom-text" name="caseformstate"></span>
                    </p>
                     <p><?php
                        $this->EmptyFieldCheck("Zip Code", $caseForm_options['ziplabel']);
                        $this->get_required('zipreq')
                        ?> <br>
                        <span>
                <input type="text" class = "cfom-text" id="cfom-text" name="caseformzip"></span>
                    </p>
                     <p>
                         <?php
                        $this->EmptyFieldCheck("Phone Number", $caseForm_options['phonelabel']);
                        $this->get_required('casephonereq')
                        ?> <br>
                        <span>
                        <input type = "text" autocomplete = "off" id = "caseformphone" name = "caseformphone"></span>
                     </p>
                      <p><?php
                        $this->EmptyFieldCheck("How do you find Us?", $caseForm_options['findlabel']);
                        $this->get_required('findreq')
                        ?> <br>
                        <span>
                <input type="text" class = "cfom-text" id="cfom-text" name="caseformfind"></span>
                    </p>

                                        <p><?php
                        $this->EmptyFieldCheck("Statement of Case ", $caseForm_options['textarealabel']);

                        $this->get_required('textareareq')
                        ?> <br>

                        <span>

                            <textarea rows = "4" cols = "20" class="caseform-textarea" id = "caseformmessage" name = "caseformstatement"></textarea>
                        </span>

                    </p>

                                                            <p><?php
                        $this->EmptyFieldCheck("Additional Comments  ", $caseForm_options['textarealabel']);

                        $this->get_required('textareareq')
                        ?> <br>

                        <span>

                            <textarea rows = "4" cols = "20" class="caseform-textarea" id = "caseformmessage" name = "caseformcomments"></textarea>
                        </span>

                    </p>
                    <p><button class="btn-yellow" class="<?php $this->EmptyFieldCheck("btn-case_evaluation", $caseForm_options['btnclass']) ?>" id = "cformsubmit" > <i class="icon-share"></i> Get Help Now</button> </p>
                </form>

            </div>

            <?php
        }

        function get_captcha() {

            session_start();

            $_SESSION['answer'] = NULL;

            unset($_SESSION['answer']);

            $random1 = rand(0, 10);

            $random2 = rand(11, 20);

            $_SESSION['answer'] = $random2;

            return $random1 . ' or ' . $random2;
        }

        function get_required($field) {

            $caseForm_options = get_option('caseform_setting_options');

            if ($caseForm_options[$field] == 'on') {

                echo ' *';
            }
        }

        function admin_scripts() {



            //wp_enqueue_script( 'jquery' );

            wp_enqueue_script('jquery-ui-core');

            wp_print_scripts('jquery-ui-core');

            wp_enqueue_script('caseForm-custom-ui-js', $this->_pluginURL . '/js/jquery.custom-ui.js');

            wp_print_scripts('caseForm-custom-ui-js');

            echo '<link rel="stylesheet" href="' . $this->_pluginURL . '/css/ui-lightness/jquery-ui-1.7.2.custom.css" type="text/css" media="all" />';

            // For Tool Tip

            wp_enqueue_script('caseForm-tooltip-js', $this->_pluginURL . '/js/tooltip.js');

            wp_print_scripts('caseForm-tooltip-js');

            // For popups

            wp_enqueue_script('caseForm-swiftpopup-js', $this->_pluginURL . '/js/swiftpopup.js');

            wp_print_scripts('caseForm-swiftpopup-js');

            // For 

            wp_enqueue_script('caseForm-admin-js', $this->_pluginURL . '/js/caseform.js');

            wp_print_scripts('caseForm-admin-js');

            echo '<link rel="stylesheet" href="' . $this->_pluginURL . '/css/caseform.css" type="text/css" media="all" />';
        }

        //check if message contain html

        function isHtml($string) {

            preg_match("/<\/?\w+((\s+\w+(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/", $string, $matches);

            if (count($matches) == 0) {

                return FALSE;
            } else {

                return TRUE;
            }
        }

        //check URl in String



        function checkurl($string_value) {
           

            $var = '/((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)/i';

            $var1 = '/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/';

            $words = array('.com', '.org', '.in', '.pk', '.eu', '.net', '.my', '.edu', '.info', '.biz', '.pro', '.jobs', '.cc');

            $matches_words = 0;

            foreach ($words as $word) {

                if (strpos($string_value, $word) !== false) {

                    $matches_words = 1;
                }
            }

            if ($matches_words == '1') {

                header('Location:/case-case-thank-you');

                exit();
            }

            preg_match($var1, $string_value, $matches);

            preg_match($var, $string_value, $matchess);

            if (count($matches) == 0 && count($matchess) == 0) {

                return FALSE;
            } else {

                return TRUE;
            }
        }

        //Get posted form values

        function get_form_case_parameters() {

            global $_POST;

            session_start();


           
            if (isset($_POST['caseform_type'])) {
                //echo "enter herere";exit;
                if ($_POST['caseformlink'] == '1') {

                    header('Location:/case-thank-you');

                    exit();
                }

                if (!empty($_POST['caseformrobot'])) {

                    header('Location:/case-thank-you');

                    exit();
                }

                if ($this->isHtml($_POST['caseformmessage']) == TRUE) {

                    header('Location:/case-thank-you');

                    exit();
                }

                if ($this->checkurl($_POST['caseformmessage']) == TRUE) {

                    header('Location:/case-thank-you');

                    exit();
                }

                $caseformValue = array();

                $email_address = $_POST['caseformemail'];
                $caseformValue['caseformname'] = $_POST['caseformname'];
                $caseformValue['caseformemail'] = $_POST['caseformemail'];
                $caseformValue['caseformphone'] = $_POST['caseformphone'];
                $caseformValue['caseformstreet'] = $_POST['caseformstreet'];
                $caseformValue['caseformcity'] = $_POST['caseformcity'];
                $caseformValue['caseformstate'] = $_POST['caseformstate'];
                $caseformValue['caseformzip'] = $_POST['caseformzip'];
                $caseformValue['caseformfind'] = $_POST['caseformfind'];
                $caseformValue['caseformstatement'] = $_POST['caseformstatement'];
                $caseformValue['caseformcomments'] = $_POST['caseformcomments'];
                $phone = preg_replace("/[^0-9,.]/", "", $caseformValue['caseformphone']);
                    print_r($caseformValue);exit;
                //$phone_len = strlen($phone);

                if (filter_var($email_address, FILTER_VALIDATE_EMAIL) && $phone != '' && strlen($phone) == 10 && !empty($caseformValue['caseformname']) && !empty($caseformValue['caseformemail'])) {
                    //echo "enbter eherlkj l ";exit;
                    if (substr($phone, 0, 1) == "0") {
                        header("Location:" . get_bloginfo('url') . "/case-thank-you");
                        exit();
                    } else {
                        $this->save_send_mail($caseformValue);
                    }
                } else {
                   //echo "enter herer";exit;
                    header('Location:' . $_SERVER['HTTP_REFERER']);
                }
            } elseif (isset($_POST['caseform_type']) && $_POST['caseform_type'] == 'case_evaluation') {

                $captcha_error = 1;
                   // echo "enter herer last";exit;
                header('Location:' . $_SERVER['HTTP_REFERER']);

                exit();
            }
        }

        function save_send_mail($formvalues) {
                //echo "enter herer";exit;
            global $wpdb;

            $caseForm_options = get_option('caseform_setting_options');

            $site_url = substr(get_bloginfo('url'), 7);

            $subject = 'Lead From ' . $site_url;

            $to = $caseForm_options['emails'];

            $admin_name = get_bloginfo('name');

            $email_temp = $caseForm_options['emailtemp'];

            $email_temp = @str_replace("[site_url]", $site_url, $email_temp);

            $email_temp = @str_replace("[name]", $formvalues['caseformname'], $email_temp);

            $email_temp = @str_replace("[phone]", $formvalues['caseformphone'], $email_temp);

            $email_temp = @str_replace("[email]", $formvalues['caseformemail'], $email_temp);

            $email_temp = @str_replace("[street]", $formvalues['caseformstreet'], $email_temp);
            $email_temp = @str_replace("[city]", $formvalues['caseformcity'], $email_temp);
            $email_temp = @str_replace("[state]", $formvalues['caseformstate'], $email_temp);
            $email_temp = @str_replace("[zip]", $formvalues['caseformzip'], $email_temp);
            $email_temp = @str_replace("[work]", $formvalues['caseformwork'], $email_temp);
            $email_temp = @str_replace("[notes]", $formvalues['caseformnotes'], $email_temp);
            $email_temp = @str_replace("[find]", $formvalues['caseformfind'], $email_temp);
            $email_temp = @str_replace("[clientname]", $formvalues['caseformclientname'], $email_temp);
            $email_temp = @str_replace("[dateofbirth]", $formvalues['caseformdateofbirth'], $email_temp);
            $email_temp = @str_replace("[license]", $formvalues['caseformlicense'], $email_temp);
            $email_temp = @str_replace("[statement]", $formvalues['caseformstatement'], $email_temp);
            $email_temp = @str_replace("[goals]", $formvalues['caseformgoals'], $email_temp);
            $email_temp = @str_replace("[comments]", $formvalues['caseformcomments'], $email_temp);
            
               // print_r($email_temp);exit;
            $headers = 'MIME-Version: 1.0' . "\r\n";

            $headers.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            $headers.= "From: $site_url <" . $formvalues['caseformemail'] . ">" . "\r\n";

            $headers.= 'Reply-To: ' . $formvalues['caseformemail'] . "\r\n";
            
            $status = mail($to, $subject, $email_temp, $headers);
            if ($status) {
                $table_name = $wpdb->prefix . "caseformleads";

                $wpdb->insert($table_name, array('lead_time' => current_time('mysql'), 'lead_string' => implode(',', $formvalues)));

                //smartsheet integration

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

                $lead_data2["29239060"] = 'Case Evaluation Form'; /* Phone call */

                $lead_data2["29239061"] = "[$admin_name] - $site_url "; /* Client Name */

                $lead_data2["29239058"] = $formvalues['caseformphone']; /* Caller id */

                $lead_data2["30451525"] = "Case Evaluation Form"; /* Lead Type */

                $lead_data2["29241150"] = ' '; /* Call duration */

                $lead_data2["29239062"] = $formvalues['caseformname']; /* Caller name */

                $lead_data2["29239063"] = $formvalues['caseformemail']; /* Caller Email */

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

                header("Location:" . get_bloginfo('url') . "/case-thank-you");

                exit();
            }
        }

    }

    $caseFormObj = new caseForm(); // Create instance
    //include'inc/caseform-email.php';
}




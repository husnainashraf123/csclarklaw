<?php
/*
 * @package WordPress
 * @subpackage Xpert
 * @since Xpert 1.0
 * 
 * Define action for icons
 * 
 */
require_once("../../../../../wp-load.php");
?>
jQuery(document).ready(function(){
var url = "<?php echo get_template_directory_uri(); ?>/framework/js/admin-popup.html";
jQuery.get(url, 
function(data) {
jQuery(data).hide().appendTo('body');
});

});


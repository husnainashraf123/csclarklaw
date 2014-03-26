<?php
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'add_option_page');
}


function add_option_page() {
    // Add a new submenu under Options:
    add_options_page('Map Categories to Pages Options', 'Map Categories to pages', 'administrator', 'mcpoptions', 'mcp_option_page');

}




function mcp_option_page() {
global $kCmcp_incponc, $kCmcp_showponp, $kCmcp_showponp_arg;
?>
<div class="wrap">
<h2>Map Categories to Pages</h2>

<form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <table class="form-table">
        <tr valign="top">
        <td style="width:200px;"><b>Show the Pages on Category pages</b></td>
        <td><input type="checkbox" name="<?php echo $kCmcp_incponc; ?>" value="1" <?php if(get_option($kCmcp_incponc)==1) echo 'checked="checked"';  ?> /></td>
        </tr>
        <tr valign="top">
        <td style="width:200px;"><b>Show Posts on the Pages tagged under the same category</b></td>
        <td>
        	<input type="checkbox" name="<?php echo $kCmcp_showponp; ?>" value="1" <?php if(get_option($kCmcp_showponp)==1) echo 'checked="checked"';  ?> />
            &nbsp; Options <input type="text" name="<?php echo $kCmcp_showponp_arg;?>" value="<?php echo get_option($kCmcp_showponp_arg); ?>"/> e.g. header=Posts&before=&lt;h3&gt;&after=&lt;/h3&gt;
        </td>
        </tr>
    </table>
    <input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="<?php echo $kCmcp_incponc.','.$kCmcp_showponp.','.$kCmcp_showponp_arg;  ?>" />

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>
<?php } 

add_filter('manage_pages_columns', 'add_category_to_page_edit');
add_action('manage_pages_custom_column', 'display_category_on_page_edit');

function add_category_to_page_edit($page_columns){
	$page_columns["categories"]=__('Categories');
	return $page_columns;
}

function display_category_on_page_edit($page_columns){
	global $post;
	switch ($page_columns)
    {
		case 'categories':
			
			?>
			<?php
				$categories = get_the_category($post_id);
				if ( !empty( $categories ) ) {
					$out = array();
					foreach ( $categories as $c )
						$out[] = "<a href='edit.php?post_type={$post->post_type}&amp;category_name={$c->slug}'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . "</a>";
						echo join( ', ', $out );
				} 
			?>
			<?php
			break;
	}
}
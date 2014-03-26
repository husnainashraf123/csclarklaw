<?php
global $wpdb, $table_prefix;
if (!isset($wpdb)) {
    require_once('../../../../wp-config.php');
    require_once('../../../../wp-includes/wp-db.php');
}
/*
 * define custom style from theme options
 */
function custom_style() {
global $data;
?>
<style>
body{ 
    font-family: <?php echo $data['font_body']['face']; ?>, Arial, Helvetica, sans-serif; 
    font-size: <?php echo $data['font_body']['size']; ?>; 
    font-weight: <?php echo $data['font_body']['style']; ?>; 
    color: <?php echo $data['font_body']['color']; ?>;
}
h1{ 
    font-family: <?php echo $data['font_h1']['face']; ?>, Arial, Helvetica, sans-serif;
    font-size: <?php echo $data['font_h1']['size']; ?>; 
    font-weight: <?php echo $data['font_h1']['style']; ?>; 
    color: <?php echo $data['font_h1']['color']; ?>; 
}
h2{ 
    font-family: <?php echo $data['font_h2']['face']; ?>, Arial, Helvetica, sans-serif;
    font-size: <?php echo $data['font_h2']['size']; ?>; 
    font-weight: <?php echo $data['font_h2']['style']; ?>; 
    color: <?php echo $data['font_h2']['color']; ?>; 
}
h3{ 
    font-family: <?php echo $data['font_h3']['face']; ?>, Arial, Helvetica, sans-serif; 
    font-size: <?php echo $data['font_h3']['size']; ?>; 
    font-weight: <?php echo $data['font_h3']['style']; ?>; 
    color: <?php echo $data['font_h3']['color']; ?>; 
}
h4{ 
    font-family: <?php echo $data['font_h4']['face']; ?>, Arial, Helvetica, sans-serif; 
    font-size: <?php echo $data['font_h4']['size']; ?>; 
    font-weight: <?php echo $data['font_h4']['style']; ?>; 
    color: <?php echo $data['font_h4']['color']; ?>;
}
h5{ 
    font-family: <?php echo $data['font_h5']['face']; ?>, Arial, Helvetica, sans-serif; 
    font-size: <?php echo $data['font_h5']['size']; ?>; 
    font-weight: <?php echo $data['font_h5']['style']; ?>; 
    color: <?php echo $data['font_h5']['color']; ?>; 
}
h6{ 
    font-family: <?php echo $data['font_h6']['face']; ?>, Arial, Helvetica, sans-serif; 
    font-size: <?php echo $data['font_h6']['size']; ?>; 
    font-weight: <?php echo $data['font_h6']['style']; ?>; 
    color: <?php echo $data['font_h6']['color']; ?>; 
}
</style>
<?php 

} // EOF Custom style
add_action( 'wp_head', 'custom_style', 100 );
?>
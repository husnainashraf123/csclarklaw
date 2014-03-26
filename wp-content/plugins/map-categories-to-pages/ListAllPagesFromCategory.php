<?php


// We're putting the plugin's functions in one big function we then
// call at 'plugins_loaded' (add_action() at bottom) to ensure the
// required Sidebar Widget functions are available.
function mcp_widget_ListAllPagesFromCat_init() {
 	

	// Check to see required Widget API functions are defined...
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return; // ...and if not, exit gracefully from the script.

	// This function prints the sidebar widget--the cool stuff!
	function mcp_widget_ListAllPagesFromCat($args) {

		// $args is an array of strings which help your widget
		// conform to the active theme: before_widget, before_title,
		// after_widget, and after_title are the array keys.
		extract($args);

		// Collect our widget's options, or define their defaults.
		$options = get_option('mcp_widget_ListAllPagesFromCat');
		$title = empty($options['title']) ? '' : $options['title'];
		$excludecat = empty($options['excludecat']) ? '' : explode(',', $options['excludecat']);
		//$text = empty($options['text']) ? 'Hello World!' : $options['text'];

 		
		?>
        
		 <?php
        global $post;
		 $categories = get_the_category();
		 $cats=array();
		 $i=0;
		 foreach($categories as $category) {
			if ( !in_array($category->cat_ID, $excludecat) ) {
				$cats[]=$category->cat_ID;
			}

			$i+=1;
		 }

		if( count($cats) == 0 ){
			return;
		}
         $myposts = get_posts('numberposts=-1&category='.implode(',',$cats).'&orderby=title&order=ASC&post_type=page&exclude='.$post->ID);
		 if(count($myposts)==0){
			 return;
		 }
		 // It's important to use the $before_widget, $before_title,
 		// $after_title and $after_widget variables in your output.
		echo $before_widget;
		echo $before_title;
		echo $title;
		echo $after_title;
		 ?>
         <ul>
         <?php
         foreach($myposts as $post) :
           setup_postdata($post);
         ?>
            <li><a href="<?php the_permalink(); ?>"><?php if ( get_post_meta($post->ID, 'page_thumb', true) ) : ?><img src="<?php echo get_post_meta($post->ID, 'page_thumb', true) ?>" alt="<?php the_title(); ?>" /> <?php endif; ?><?php the_title(); ?></a></li>
         <?php endforeach; ?>
         </ul> 
        <?php
		echo $after_widget;
	}
	
	// This is the function that outputs the form to let users edit
	function mcp_widget_ListAllPagesFromCat_control() {

		// Collect our widget's options.

		$options = get_option('mcp_widget_ListAllPagesFromCat');

		$newoptions = get_option('mcp_widget_ListAllPagesFromCat');

		// This is for handing the control form submission.

		if ( $_POST['mcp_ListAllPagesFromCat-submit'] ) {

			// Clean up control form submission options
			$newoptions['title'] = strip_tags(stripslashes($_POST['mcp_ListAllPagesFromCat-title']));
			$newoptions['excludecat'] = strip_tags(stripslashes($_POST['mcp_ListAllPagesFromCat-excludecat']));
		}

		// If original widget options do not match control form

		// submission options, update them.

		if ( $options != $newoptions ) {

			$options = $newoptions;

			update_option('mcp_widget_ListAllPagesFromCat', $options);

		}

		// Format options as valid HTML. Hey, why not.

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$excludecat = $options['excludecat'];
// The HTML below is the control form for editing options.

?>
		<div>
            <label for="ListAllPagesFromCat-title" style="line-height:35px;display:block;">Title: <input type="text" id="mcp_ListAllPagesFromCat-title" name="mcp_ListAllPagesFromCat-title" value="<?php echo $title; ?>" style="width:100%" /></label>
    <br />
    		<label for="ListAllPagesFromCat-excludecat" style="line-height:35px;display:block;">Exclude Category IDs <small>(e.g., 4,8)</small>: <input type="text" id="mcp_ListAllPagesFromCat-excludecat" name="mcp_ListAllPagesFromCat-excludecat" value="<?php echo $excludecat; ?>" style="width:100%" /></label><br />
            <input type="hidden" name="mcp_ListAllPagesFromCat-submit" id="mcp_ListAllPagesFromCat-submit" value="1" />
		</div>

	<?php
	}

	// This registers the widget. About time.
	wp_register_sidebar_widget('mcp_listallpagesfromcat', 'Display All Pages from Current Category', 'mcp_widget_ListAllPagesFromCat');

	// This registers the (optional!) widget control form.
	wp_register_widget_control('mcp_listallpagesfromcat', 'Display All Pages from Current Category', 'mcp_widget_ListAllPagesFromCat_control');
}


	// Delays plugin execution until Dynamic Sidebar has loaded first.
	add_action('plugins_loaded', 'mcp_widget_ListAllPagesFromCat_init');
	
	

?>
<?php

/*
 * Plugin Name: Related Links
 * Plugin URI: http://wordpress.org/extend/plugins/related-links/
 * Description: Allows to easily access links to your other posts and pages through a widget.
 * Version: 1.5.7
 * Author: Triggvy Gunderson
 * Author URI: http://wordpress.org/extend/plugins/related-links/
 * License: GPLv3
 *
 * Copyright (C) 2012 Triggvy Gunderson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
 
// ------------------------------------------
// backend
// ------------------------------------------


include_once('include/class-related-links-settings.php');
include_once('include/class-related-links-box.php');

// initialize objects
$Related_Links_Settings = new Related_Links_Settings();
$Related_Links_Box = new Related_Links_Box();

// add or remove default settings
register_activation_hook(__FILE__, array($Related_Links_Settings, 'add_default_settings'));
register_deactivation_hook(__FILE__, array($Related_Links_Settings, 'remove_default_settings'));


// ------------------------------------------
// frontend
// ------------------------------------------


/**
 * Get a list of related links.
 *
 * @param $post_id int (optional) The post id for which you want to retreive the links, if null it will pull from global $post.
 * @param $post_type string (optional) Filter by all registered post types, if null it will pull all post types.
 * @return array The array is keyed.
 */
if ( !function_exists( 'get_related_links' ) ) 
{
function get_related_links( $post_type = null, $post_id = null )
{
	global $post;
	
	if ( empty( $post_id ) ) 
	{
		$post_id = $post->ID;
	}
	
	// Get the meta information	
	$meta = get_post_meta( $post_id, '_related_links', true );
	$values = array();

	// Parse it
	if( !empty( $meta['posts'] ) )
	{
		foreach( $meta['posts'] as $id ) 
		{	
			$is_custom = strrpos( $id, 'custom_' );
			
			if( $is_custom !== false )
			{
				// Read the custom meta data
				$custom_meta = $meta['custom'][$id];
				$custom_meta[1] = ($custom_meta[1] == '') ? null : $custom_meta[1];
				
				// Push custom values
				if( $post_type == 'custom' || empty( $post_type ) )
				{		
					$values[] = array('id' => null, 'title' => $custom_meta[0], 'url' => $custom_meta[1], 'type' => null);
				}
			}
			else
			{
				// Check if the post exists
				$found_post = get_post( $id );
				
				if( !empty( $found_post ) && $found_post->post_status != 'trash' && $found_post->post_status != 'draft' )
				{
					// Push posts values
					if( $post_type == get_post_type($id) || empty( $post_type ) )
					{
						$values[] = array('id' => $id, 'title' => $found_post->post_title, 'url' => get_permalink($id), 'type' => $found_post->post_type);
					}
				}
			}		
		}
	}

	return $values;
}
}

/**
 * Get a unordered list of related links for the current post.
 */
if ( !function_exists( 'related_links' ) ) 
{
function related_links()
{
	$related_links = get_related_links(); ?>
	<ul>
	<?php foreach ($related_links as $link): ?>
		<li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
	<?php
}
}

?>

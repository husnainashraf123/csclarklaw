jQuery(document).ready(function(jQuery) {

    // The number of the next page to load (/page/x/).
    var pageNum = parseInt(xpert_more.startPage) + 1;
	
    // The maximum number of pages the current query can return.
    var max = parseInt(xpert_more.maxPages);
	
    // The link of the next page of posts.
    var nextLink = xpert_more.nextLink;
	
    /**
	 * Replace the traditional navigation with our own,
	 * but only if there is at least one page of new posts to load.
	 */
    if(pageNum <= max) {
        // Insert the "More Posts" link.
        jQuery('#load-more').append('<p id="xpert-load-posts"><a href="#" class="btn btn-info">Load More </a></p>');
        jQuery('.project-container').append('<div class="xpert-post-'+ pageNum +'"></div>')
        
			
    // Remove the traditional navigation.
    //jQuery('.navigation').remove();
    }
    /**
	 * Load new posts when the link is clicked.
	 */
    jQuery('#xpert-load-posts a').click(function() {
	
        // Are there more posts to load?
        if(pageNum <= max) {
            // Show that we're working.
            jQuery(this).text('Loading posts...');
            //alert(jQuery('.xpert-post-'+ pageNum).load(nextLink + ' .POST'));
            jQuery('.xpert-post-'+ pageNum).load(nextLink,
                function(response, status) {
                    alert(response);
                    if (status == "success") {
                        jQuery('.portfolio article').hover(function () {
                            jQuery(this).find('.image-overlay-bg').stop(true, true).animate({
                                opacity: 1
                            }, 200 ).css({
                                'display': 'block'
                            });
                        }, function () {
                            jQuery(this).find('.image-overlay-bg').stop(true, true).animate({
                                opacity: 0
                            }, 200 );
                        }
                        );
			
                        jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({
                            overlay_gallery: false
                        });
                    }         
                    // Update page number and nextLink.
                    pageNum++;
                    nextLink = nextLink.replace(/\/page\/[0-9]?/, '/page/'+ pageNum);
                  
                    // Add a new placeholder, for when user clicks again.
                    jQuery('#xpert-load-posts')
                    .before('<div class="xpert-post-'+ pageNum +'"></div>')
					
                    // Update the button message.
                    if(pageNum <= max) {
                        jQuery('#xpert-load-posts a').text('Load More');
                    } else {
                        jQuery('#xpert-load-posts a').text('No more posts to load.');
                    }
                }
                );
            
        } else {
            jQuery('#xpert-load-posts a').append('.');
        }	
		
        return false;
    });
});
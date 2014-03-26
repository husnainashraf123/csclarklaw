<?php global $data; ?>
<div class="sharebox clearfix">
	<h4><?php _e('Share this Article', 'law-firm'); ?></h4>
	<div class="social-icons clearfix">
		<ul>
			<?php if($data['check_sharingboxfacebook'] == true) { ?>	
			<li class="social-facebook">
                            <a data-toggle="tooltip" title="Facebook" href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>&t=<?php the_title(); ?>" target="_blank"><i class="icon-facebook-sign icon-2x"></i></a>
			</li>
			<?php } ?>
			<?php if($data['check_sharingboxtwitter'] == true) { ?>	
			<li class="social-twitter">
				<a href="http://twitter.com/home?status=<?php the_title(); ?> <?php the_permalink(); ?>" title="<?php _e( 'Twitter', 'law-firm' ) ?>" target="_blank"><i class="icon-twitter icon-2x"></i></a>
			</li>
			<?php } ?>
			<?php if($data['check_sharingboxlinkedin'] == true) { ?>	
			<li class="social-linkedin">
				<a href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink();?>&amp;title=<?php the_title();?>" title="<?php _e( 'LinkedIn', 'law-firm' ) ?>" target="_blank"><i class="icon-linkedin-sign icon-2x"></i></a>
			</li>
			<?php } ?>
			<?php if($data['check_sharingboxgoogle'] == true) { ?>	
			<li class="social-googleplus">
				<a href="http://google.com/bookmarks/mark?op=edit&amp;bkmk=<?php the_permalink() ?>&amp;title=<?php echo urlencode(the_title('', '', false)) ?>" title="<?php _e( 'Google+', 'law-firm' ) ?>" target="_blank"><i class="icon-google-plus icon-2x"></i></a>
			</li>
			<?php } ?>
			<?php if($data['check_sharingboxemail'] == true) { ?>	
			<li class="social-email">
				<a href="mailto:?subject=<?php the_title();?>&amp;body=<?php the_permalink() ?>" title="<?php _e( 'E-Mail', 'law-firm' ) ?>" target="_blank"><i class="icon-envelope icon-2x"></i></a>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
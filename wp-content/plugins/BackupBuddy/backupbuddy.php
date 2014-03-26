<?php
/**
 *
 * Plugin Name: BackupBuddy
 * Plugin URI: http://anonym.to/?http://pluginbuddy.com/
 * Description: BackupBuddy Backup, Restoration, & Migration Tool
 * Version: 1.3.10
 * Author: Dustin Bolton
 * Author URI: http://anonym.to/?http://dustinbolton.com
 *
 * Installation:
 * 
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire Backup directory to your '/wp-content/plugins/' directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 * 
 * Usage:
 * 
 * 1. Navigate to the new Backup menu in the Wordpress Administration Panel.
 *
 */
 
if (!class_exists("iThemesBackupBuddy")) {
	class iThemesBackupBuddy {
		
		var $_version = '1.3.10';
		
		var $_wp_minimum = '2.9.0';
		var $_php_minimum = '5.1';
		var $_debug = true;						// Set true to enable debug messages.
		var $_var = 'ithemes-backupbuddy';
		var $_name = 'BackupBuddy';
		var $_url = 'http://anonym.to/?http://pluginbuddy.com/purchase/backupbuddy/';
		var $_timeformat = '%b %e, %Y, %l:%i%p';	// mysql time format
		var $_timestamp = 'M j, Y, g:iA';			// php timestamp format
		var $_usedInputs = array();
		var $_pluginPath = '';
		var $_pluginRelativePath = '';
		var $_pluginURL = '';
		var $_selfLink = '';
		var $_defaults = array(
			'password'				=>		'#PASSWORD#',
			'ftp_server'			=>		'',
			'ftp_user'				=>		'',
			'ftp_pass'				=>		'',
			'ftp_path'				=>		'',
			'ftp_type'				=>		'ftp',
			'last_run'				=>		0,
			'compression'			=>		1,
			'force_compatibility'	=>		0,
			'email_notify_scheduled' =>		1,
			'email_notify_manual'	=>		0,
			'backup_nonwp_tables'	=>		0,
			'integrity_check'		=>		1,
			'aws_ssl'				=>		1,
			'aws_directory'			=>		'backupbuddy',
			'schedules'				=>		array(),
			'log_level'				=>		'1',			// Valid options: 0 = none, 1 = errors only, 2 = errors + warnings, 3 = debugging (all kinds of actions)
			'email'					=>		'',
			'aws_accesskey'			=>		'',
			'aws_secretkey'			=>		'',
			'aws_bucket'			=>		'',
			'excludes'				=>		'',
			'zip_limit'				=>		'0',
		);
		var $_options = array();
		var $_sql_files = 0;
		var $_warning_time = 300;					// Minutes within which to warn user when beginning backup that one may already be running.
		var $_errors;
		
		/**
		 * iThemesBackupBuddy()
		 *
		 * Default Constructor
		 *
		 */
		function iThemesBackupBuddy() {
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = site_url() . '/' . $this->_pluginRelativePath;
			if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
				$this->_pluginURL = str_replace( 'http://', 'https://', $this->_pluginURL );
			}
			$this->_selfLink = array_shift( explode( '?', $_SERVER['REQUEST_URI'] ) ) . '?page=' . $this->_var;
			
			// Admin.
			if ( is_admin() ) {
				add_action('admin_menu', array(&$this, 'admin_menu')); // Add menu in admin.
				add_action('admin_init', array(&$this, 'init_admin' )); // Run on admin initialization.
				// When user activates plugin in plugin menu.
				//register_activation_hook(__FILE__, array(&$this, '_activate'));
				// Direct downloading of importbuddy.php using ajax portal.
				add_action('wp_ajax_backupbuddy_importbuddy', array(&$this, 'ajax_importbuddy') );
				// Test FTP
				add_action('wp_ajax_backupbuddy_ftptest', array(&$this, 'ajax_ftptest') );
				// Test Amazon S3
				add_action('wp_ajax_backupbuddy_awstest', array(&$this, 'ajax_awstest') );
				// Directory listing for exluding
				add_action('wp_ajax_backupbuddy_dirlist', array(&$this, 'ajax_dirlist') );
				
				// Dashboard Stats
				add_action('wp_dashboard_setup', array( &$this, 'add_dashboard_widgets' ) );
				
				//require_once(dirname( __FILE__ ).'/lib/updater/updater.php');
			} else { // Non-Admin.
				//add_action('template_redirect', array(&$this, 'init_public'));
			}
			
			add_action( $this->_var.'-cron_email', array( &$this, 'cron_email' ), '', 4 );
			add_action( $this->_var.'-cron_ftp', array( &$this, 'cron_ftp' ), '', 6 );
			add_action( $this->_var.'-cron_aws', array( &$this, 'cron_aws' ), '', 6 );
			
			add_filter( 'cron_schedules', array( &$this, 'cron_add_schedules' ) );
			add_action( $this->_var . '-cron_schedule', array( &$this, 'cron_schedule' ), '', 5 );
		}
		
		/**
		 *	alert()
		 *
		 *	Displays a message to the user at the top of the page when in the dashboard.
		 *
		 *	$message		string		Message you want to display to the user.
		 *	$error			boolean		OPTIONAL! true indicates this alert is an error and displays as red. Default: false
		 *	$error_code		int			OPTIONAL! Error code number to use in linking in the wiki for easy reference.
		 */
		function alert( $message, $error = false, $error_code = '' ) {
			$log_error = false;
			
			echo '<div id="message" class="';
			if ( $error == false ) {
				echo 'updated fade';
			} else {
				echo 'error';
				$log_error = true;
			}
			if ( $error_code != '' ) {
				$message .= '<p><a href="http://anonym.to/?http://ithemes.com/codex/page/' . $this->_name . ':_Error_Codes#' . $error_code . '" target="_new"><i>' . $this->_name . ' Error Code ' . $error_code . ' - Click for more details.</i></a></p>';
				$log_error = true;
			}
			if ( $log_error === true ) {
				$this->log( $message . ' Error Code: ' . $error_code, 'error' );
			}
			echo '"><p><strong>'.$message.'</strong></p></div>';
		}
		
		
		/**
		 *	tip()
		 *
		 *	Displays a message to the user when they hover over the question mark. Gracefully falls back to normal tooltip.
		 *	HTML is supposed within tooltips.
		 *
		 *	$message		string		Actual message to show to user.
		 *	$title			string		Title of message to show to user. This is displayed at top of tip in bigger letters. Default is blank. (optional)
		 *	$echo_tip		boolean		Whether to echo the tip (default; true), or return the tip (false). (optional)
		 */
		function tip( $message, $title = '', $echo_tip = true ) {
			$tip = ' <a class="pluginbuddy_tip" title="' . $title . ' - ' . $message . '"><img src="' . $this->_pluginURL . '/images/pluginbuddy_tip.png" alt="(?)" /></a>';
			if ( $echo_tip === true ) {
				echo $tip;
			} else {
				return $tip;
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::init_admin()
		 *
		 * Initialize for admins.
		 *
		 */
		function init_admin() {
			// TODO: MAKE THIS ONLY RUN ON BACKUPBUDDY PAGES!
			//header('Keep-Alive: 7200');
			//header('Connection: keep-alive');
		}
		
		// PAGES //////////////////////////////
		
		function add_dashboard_widgets() {
			wp_add_dashboard_widget('pb_backupbuddy', 'BackupBuddy',  array( &$this, 'dashboard_stats' ) );
		}
		function dashboard_stats() {
			echo '<style type="text/css">';
			echo '	.pb_fancy {';
			echo '		font-family: Georgia, "Times New Roman", "Bitstream Charter", Times, serif;';
			echo '		font-size: 18px;';
			echo '		color: #21759B;';
			echo '	}';
			echo '</style>';
			
			echo '<div>';
			
			$files = (array) glob( $this->_options['backup_directory'] . 'backup*.zip' );
			array_multisort( array_map( 'filemtime', $files ), SORT_NUMERIC, SORT_DESC, $files );
			/*
			foreach ( $files as $file_id => $file ) {
				echo human_time_diff( filemtime( $files[0] ) + ( ( get_option( 'gmt_offset' ) * 3600 ) +86400), time() );
			}
			*/
			
			
			echo 'You currently have <span class="pb_fancy"><a href="admin.php?page=ithemes-backupbuddy-backup">' . count( $files ) . '</a></span> stored backups.';
			if ( count( $files ) > 0 ) {
				echo ' Your most recent backup was <span class="pb_fancy"><a href="admin.php?page=ithemes-backupbuddy-backup">' . human_time_diff( filemtime( $files[0] ) + ( ( get_option( 'gmt_offset' ) * 3600 ) +86400), time() + ( ( get_option( 'gmt_offset' ) * 3600 ) +86400) ) . ' ago</a></span>.';
			} else {
				echo ' <span class="pb_fancy"><a href="admin.php?page=ithemes-backupbuddy-backup">Go create a backup!</a></span>';
			}
			
			echo '</div>';
		}
		
		function view_gettingstarted() {
			// Needed for fancy boxes...
			wp_enqueue_style('dashboard');
			wp_print_styles('dashboard');
			wp_enqueue_script('dashboard');
			wp_print_scripts('dashboard');
			// Load scripts and CSS used on this page.
			$this->admin_scripts();
			
			// If they clicked the button to reset plugin defaults...
			if (!empty($_POST['reset_defaults'])) {
				$this->_options = $this->_defaults;
				$this->save();
				$this->_showStatusMessage( 'Plugin settings have been reset to defaults.' );
			}
			?>
			
			<div class="wrap">
				<div class="postbox-container" style="width:70%;">
					<h2>Getting Started with <?php echo $this->_name; ?> v<?php echo $this->_version; ?></h2>
					
					
					
					
					
					BackupBuddy is an all-in-one solution for backups, restoration, and migration.  The single backup ZIP file created by the plugin
					can be used with the importer PHP script to quickly and easily restore your site on the same server or even migrate to a new host
					with different settings.  Whether you're an end user or a developer, this plugin is sure to bring you peace of mind and added safety
					in the event of data loss.  Our goal is to keep the backup, restoration, and migration processes easy, fast, and reliable.
					
					<br /><br />
					
					A full backup is required to fully restore your site or migrate.  However, a Database Only backup may be created as a faster, more
					regular backup solution.  When restoring your site or migrating, simply restore the latest Database Only backup (if newer than the
					full backup) followed by the Full Backup.
					<br /><br />
					
					Your backups are stored in <?php echo $this->_options['backup_directory']; ?> <?php $this->tip(' This is the local directory that backups are stored in. Backup files include random characters in their name for increased security. Verify that write permissions are available for this directory.' ); ?>
					<br />
					<br />
					<p>
						<h3>Backup</h3>
						<ol>
							<!-- <li>Set a password in the <a href="<?php echo admin_url( "admin.php?page={$this->_var}-settings" ); ?>">Settings</a> section if you have not done so.</li> -->
							<li>Perform a full backup by clicking Begin Backup on the <a href="<?php echo admin_url( "admin.php?page={$this->_var}-backup" ); ?>">Backups</a> page. This may take several minutes.</li>
							<li>Perform database backups regularly to complement the full backup.<br />The database changes with every post & is much smaller than files so may be backed up more often.</li>
							<li>Download the backup importer, <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a>, for use later when restoring or migrating.
						</ol>
					</p>
					<br />
					<p>
						<h3>Restoring, Migrating</h3>
						<ol>
							<li>Upload the backup file & <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a> to the root web directory of the destination server.<br />
								<b>Do not install WordPress</b> on the destination server. The importbuddy.php script will restore all files, including WordPress.</li>
							<li>Navigate to <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a> in your web browser on the destination server.</li>
							<li>Follow the importing instructions on screen. You will be asked whether you are restoring or migrating.<br />If applicable, you may restore an older Full Backup followed by a newer Database Only backup.</li>
						</ol>
					</p>
					
					<?php if ( stristr( PHP_OS, 'WIN' ) ) { ?>
						<br />
						<h3>Windows Server Performance Boost</h3>
						Windows servers may be able to significantly boost performance, IF the server allows executing .exe files, by adding native Zip compatibility executable files <a href="http://anonym.to/?http://pluginbuddy.com/wp-content/uploads/2010/05/backupbuddy_windows_unzip.zip">available for download here</a>.
						Instructions are provided within the readme.txt in the package.  This package prevents Windows from falling back to Zip compatiblity mode and works for both BackupBuddy and importbuddy.php. This is particularly useful for local development on a Windows machine using a system like <a href="http://www.apachefriends.org/en/xampp.html">XAMPP</a>.
					<?php } ?>
					
					<br /><br />
					
					
					
					
					
					
					<h3>Version History</h3>
					<textarea rows="7" cols="65"><?php readfile( $this->_pluginPath . '/history.txt' ); ?></textarea>
					<br /><br />
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery("#pluginbuddy_debugtoggle").click(function() {
								jQuery("#pluginbuddy_debugtoggle_div").slideToggle();
							});
						});
					</script>
					
					<a id="pluginbuddy_debugtoggle" class="button secondary-button">Debugging Information</a>
					<div id="pluginbuddy_debugtoggle_div" style="display: none;">
						<h3>Debugging Information</h3>
						<?php
						echo '<textarea rows="7" cols="65">';
						echo 'Plugin Version = '.$this->_name.' '.$this->_version.' ('.$this->_var.')'."\n";
						echo 'WordPress Version = '.get_bloginfo("version")."\n";
						echo 'PHP Version = '.phpversion()."\n";
						global $wpdb;
						echo 'DB Version = '.$wpdb->db_version()."\n";
						echo "\n".serialize($this->_options);
						echo '</textarea>';
						
						echo '<h3>Debug Log</h3><textarea rows="7" cols="65">';
						if ( file_exists( WP_CONTENT_DIR . '/uploads/' . $this->_var . '.txt' ) ) {
							echo readfile( WP_CONTENT_DIR . '/uploads/' . $this->_var . '.txt' );
						} else {
							echo 'Currently no debugging logs exist.';
						}
						echo '</textarea>';
						
						
						?>
						<p>
						<form method="post" action="<?php echo $this->_selfLink; ?>">
							<input type="hidden" name="reset_defaults" value="true" />
							<input type="submit" name="submit" value="Reset Plugin Settings & Defaults" id="reset_defaults" class="button secondary-button" onclick="if ( !confirm('WARNING: This will reset all settings associated with this plugin to their defaults. Are you sure you want to do this?') ) { return false; }" />
						</form>
						</p>
					</div>
					<br /><br /><br />
					<a href="http://anonym.to/?http://pluginbuddy.com" style="text-decoration: none;"><img src="<?php echo $this->_pluginURL; ?>/images/pluginbuddy.png" style="vertical-align: -3px;" /> PluginBuddy.com</a><br /><br />
				</div>
				<div class="postbox-container" style="width:20%; margin-top: 35px; margin-left: 15px;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">

							<div id="breadcrumbssupport" class="postbox">
								<div class="handlediv" title="Click to toggle"><br /></div>
								<h3 class="hndle"><span>Tutorials & Support</span></h3>
								<div class="inside">
									<p>See our <a href="http://anonym.to/?http://pluginbuddy.com/tutorials/backupbuddy/">tutorials & videos</a> or visit our <a href="http://anonym.to/?http://ithemes.com/support/backupbuddy/">support forum</a> for additional information and help.</p>
								</div>
							</div>
						
							<div id="breadcrumbslike" class="postbox">
								<div class="handlediv" title="Click to toggle"><br /></div>
								<h3 class="hndle"><span>Things to do...</span></h3>
								<div class="inside">
									<ul class="pluginbuddy-nodecor">
										<li>- <a href="http://twitter.com/home?status=<?php echo urlencode('Check out this awesome plugin, ' . $this->_name . '! ' . $this->_url . ' @pluginbuddy'); ?>" title="Share on Twitter" onClick="window.open(jQuery(this).attr('href'),'ithemes_popup','toolbar=0,status=0,width=820,height=500,scrollbars=1'); return false;">Tweet about this plugin.</a></li>
										<li>- <a href="http://anonym.to/?http://pluginbuddy.com/purchase/">Check out PluginBuddy plugins.</a></li>
										<li>- <a href="http://anonym.to/?http://pluginbuddy.com/purchase/">Check out iThemes themes.</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		
		function get_feed( $feed, $limit, $append = '', $replace = '' ) {
			require_once(ABSPATH.WPINC.'/feed.php');  
			$rss = fetch_feed( $feed );
			if (!is_wp_error( $rss ) ) {
				$maxitems = $rss->get_item_quantity( $limit ); // Limit 
				$rss_items = $rss->get_items(0, $maxitems); 
				
				echo '<ul class="pluginbuddy-nodecor">';

				$feed_html = get_transient( md5( $feed ) );
				if ( $feed_html == '' ) {
					foreach ( (array) $rss_items as $item ) {
						$feed_html .= '<li>- <a href="' . $item->get_permalink() . '">';
						$title =  $item->get_title(); //, ENT_NOQUOTES, 'UTF-8');
						if ( $replace != '' ) {
							$title = str_replace( $replace, '', $title );
						}
						if ( strlen( $title ) < 30 ) {
							$feed_html .= $title;
						} else {
							$feed_html .= substr( $title, 0, 32 ) . ' ...';
						}
						$feed_html .= '</a></li>';
					}
					set_transient( md5( $feed ), $feed_html, 300 ); // expires in 300secs aka 5min
				}
				echo $feed_html;
				
				echo $append;
				echo '</ul>';
			} else {
				echo 'Temporarily unable to load feed...';
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::view_backup()
		 *
		 * Displays backup plugin page.
		 *
		 */
		function view_backup() {
			$this->check_versions();
			$this->load();
			
			// Load scripts and CSS used on this page.
			$this->admin_scripts();
			?>
			<div class="wrap">
				<h2>Create Backup</h2><br />
			<?php
			if (!empty($_GET['backup_step'])) {
				echo 'This may take several minutes. Do NOT use your back button or it may corrupt the backup. Please wait for this page to finish loading...<br /><br />';
				echo 'Having trouble? Check out our <a target="_blank" href="http://anonym.to/?http://ithemes.com/codex/page/BackupBuddy">BackupBuddy Wiki for comprehensive troubleshooting information</a><br />';
				?>
				<h3>Backup Status:</h3>
				<div class="updated">
				<table style="width: 100%; border-collapse: collapse;">
					<tr>
						<td id="backupbuddy_step1" style="border-right: 1px solid #E6DB55; padding: 8px; font: italic 17px Georgia;">Starting Backup ...</td>
						<td id="backupbuddy_step2" style="color: #9F9F9F; background-color: #FFFFEF; border-right: 1px solid #E6DB55; padding: 8px; font: italic 17px Georgia;">Exporting Settings ...</td>
						<td id="backupbuddy_step3" style="color: #9F9F9F; background-color: #FFFFEF; border-right: 1px solid #E6DB55; padding: 8px; font: italic 17px Georgia;">Exporting Database ...</td>
						<td id="backupbuddy_step4" style="color: #9F9F9F; background-color: #FFFFEF; border-right: 1px solid #E6DB55; padding: 8px; font: italic 17px Georgia;">Saving Data to File ...</td>
						<td id="backupbuddy_step5" style="color: #9F9F9F; background-color: #FFFFEF; padding: 8px; font: italic 17px Georgia;">Finishing ...</td>
					</tr>
				</table>
			</div>
				<?php
				echo '<div style="background: #FFFFFF; border: 1px dashed #E3E3E3; padding: 6px;">';
				flush();
				
				$this->_backup( $_GET['type'] );
				echo '</div>';
				
				$this->show_backup_files();
				
			} else {
				
			?>
				Backups stored in <?php echo $this->_options['backup_directory']; ?> <?php $this->tip( 'This is the local directory that backups are stored in. Backup files include random characters in their name for increased security. Verify that write permissions are available for this directory.' ); ?><br />
				<p>
					<ol>
						<li>Perform a full backup by clicking the button below. This may take several minutes.</li>
						<li>Download the backup importer: <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a>
						<li>Upload the resulting backup zip file and <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a> to the root web directory of the destination server.
						<li>If you performed database backups after the full backup, upload the latest database backup zip and import after the full backup.
						<li>Navigate to <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a> in your webbrowser on the destination server.</li>
					</ol>
				</p>
				
				<?php
				/*
				if ($this->_options['password']=='#PASSWORD#') {
					echo '<br /><b>IMPORTANT:</b> Backing up disabled until a password is set for BackupBuddy. Set a password in the <a href="'.admin_url( "admin.php?page={$this->_var}-settings" ).'">Settings</a> section before you may make a backup.<br />';
					$this->_showErrorMessage( __( 'IMPORTANT: You have not set a password for BackupBuddy. You must set a password in the <a href="'.admin_url( "admin.php?page={$this->_var}-settings" ).'">Settings</a> section before you may make a backup.', $this->_var ) );
				} else {
				*/
				if ( !file_exists( $this->_options['backup_directory'] ) ) {
					$this->mkdir_recursive($this->_options['backup_directory']);
				}
				if ( !file_exists( $this->_options['backup_directory'] ) ) {
					$this->_showErrorMessage( __( 'Unable to create backup storage directory.', $this->_var ) );
				}
				if (is_writable($this->_options['backup_directory'])) {
					?>
					<br />
					<h3>Create Backups</h3>
					<table width="400">
						<tr>
							<td valign="top">
								<form method="post" action="<?php echo $this->_selfLink; ?>-backup&backup_step=1&type=full">
									<?php $this->_addSubmit( 'backup', 'Full Backup' ); ?> <?php $this->tip( 'Initial and occassional backup to store everything, including database and all files.' ); ?>
								</form>
							</td>
							<td valign="top">
								<form method="post" action="<?php echo $this->_selfLink; ?>-backup&backup_step=1&type=db">
									<?php $this->_addSubmit( 'db_backup', 'Database Only' ); ?> <?php $this->tip( 'Perform this backup often to backup the database and all posts and settings. It is recommended that you create a Full Backup first and Database backups secondly on a more regular basis.  Upload the Database Backup ZIP in addition to the other files when restoring or migrating.' ); ?></p>
								</form>
							</td>
						</tr>
					</table>
					<?php
				} else {
					echo 'ERROR: The backup directory is not writable. Please verify the directory has write permissions.';
				}
				?>
				
				<h3></h3>
				<?php $this->show_backup_files(); ?>
			</div>
			<?php
			}
		}
		
		/**
		 * iThemesBackupBuddy::admin_scripts()
		 *
		 * Load scripts and styling for admin pages.
		 *
		 */
		function admin_scripts() {
			//wp_enqueue_script( 'jquery' );
			
			wp_enqueue_script( 'jquery-ui-core' );
			wp_print_scripts( 'jquery-ui-core' );
			
			wp_enqueue_script( 'ithemes-custom-ui-js', $this->_pluginURL . '/js/jquery.custom-ui.js' );
			wp_print_scripts( 'ithemes-custom-ui-js' );
			
			wp_enqueue_script( 'ithemes-timepicker-js', $this->_pluginURL . '/js/timepicker.js' );
			wp_print_scripts( 'ithemes-timepicker-js' );
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/ui-lightness/jquery-ui-1.7.2.custom.css" type="text/css" media="all" />';
			
			wp_enqueue_script( 'ithemes-filetree-js', $this->_pluginURL . '/js/filetree.js' );
			wp_print_scripts( 'ithemes-filetree-js' );
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/filetree.css" type="text/css" media="all" />';
			
			wp_enqueue_script( 'ithemes-tooltip-js', $this->_pluginURL . '/js/tooltip.js' );
			wp_print_scripts( 'ithemes-tooltip-js' );
			wp_enqueue_script( 'ithemes-swiftpopup-js', $this->_pluginURL . '/js/swiftpopup.js' );
			wp_print_scripts( 'ithemes-swiftpopup-js' );
			wp_enqueue_script( 'ithemes-'.$this->_var.'-admin-js', $this->_pluginURL . '/js/admin.js' );
			wp_print_scripts( 'ithemes-'.$this->_var.'-admin-js' );
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/admin.css" type="text/css" media="all" />';
		}
		
		function check_versions() {
			global $wp_version;
			if ( version_compare( $wp_version, $this->_wp_minimum, '<' ) ) {
				$this->alert( 'ERROR: ' . $this->_name . ' requires WordPress version ' . $this->_wp_minimum . ' or higher. You may experience unexpected behavior or complete failure in this environment. Please consider upgrading WordPress.' );
				$this->log( 'Unsupported WordPress Version: ' . $wp_version , 'error' );
			}
			if ( version_compare( PHP_VERSION, $this->_php_minimum, '<' ) ) {
				$this->alert( 'ERROR: ' . $this->_name . ' requires PHP version ' . PHP_VERSION . ' or higher. You may experience unexpected behavior or complete failure in this environment. Please consider upgrading PHP.' );
				$this->log( 'Unsupported PHP Version: ' . PHP_VERSION , 'error' );
			}
		}
		
		/**
		 * iThemesBackupBuddy::view_settings()
		 *
		 * Displays settings form and values for viewing & editing.
		 *
		 */
		function view_settings() {
			$this->check_versions();
			require_once( dirname( __FILE__ ) . '/classes/view_settings.php' );
		}
		
		
		/**
		 * iThemesBackupBuddy::view_scheduling()
		 *
		 * Displays settings form and values for viewing & editing.
		 *
		 */
		function view_scheduling() {
			$this->check_versions();
			require_once( dirname( __FILE__ ) . '/classes/view_scheduling.php' );
		}
		
		
		/**
		 * iThemesBackupBuddy::pretty_schedule_type()
		 *
		 * Make this look pretty.
		 *
		 */
		function pretty_schedule_type($val) {
			if ($val == 'db') {
				return 'Database';
			} elseif ($val == 'full') {
				return 'Full';
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::pretty_schedule_interval()
		 *
		 * Make this look pretty.
		 *
		 */
		function pretty_schedule_interval($val) {
			if ($val == 'monthly') {
				return 'Monthly';
			} elseif ($val == 'twicemonthly') {
				return 'Twice Monthly';
			} elseif ($val == 'weekly') {
				return 'Weekly';
			} elseif ($val == 'daily') {
				return 'Daily';
			} elseif ($val == 'hourly') {
				return 'Hourly';
			} else {
				return $val;
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::pretty_schedule_firstrun()
		 *
		 * Make this look pretty.
		 *
		 */			
		function pretty_schedule_firstrun($val) {
			return date('m/d/Y h:i a',$val);
		}
		
		
		/**
		 * iThemesBackupBuddy::delete_schedule()
		 *
		 * Delete one or more scheduled backups
		 *
		 */			
		function delete_schedule() {
			$cron = get_option('cron');
			if ( ! empty( $_POST['schedules'] ) && is_array( $_POST['schedules'] ) ) {
				foreach ( (array) $_POST['schedules'] as $id_full ) {
					$id = explode('-', $id_full);
					// Remove from backupbuddy database.
					unset( $this->_options['schedules'][$cron[$id[0]]['ithemes-backupbuddy-cron_schedule'][$id[1]]['args'][0]] );
					$this->save();
					// Remote from CRON system in database.
					unset( $cron[$id[0]]['ithemes-backupbuddy-cron_schedule'][$id[1]] );
					update_option('cron', $cron); // Save Cron changes.
				}
			}
		}
		
		
		function add_schedule() {
			if ( empty( $_POST[$this->_var.'-name'] ) ) {
				$this->_errors[] = 'name';
				$this->_showErrorMessage( 'A name is required to create a new schedule.' );
			}
			/*elseif ( is_array( $this->_options['schedules'] ) ) {
				foreach ( (array) $this->_options['schedules'] as $id => $group ) {
					if ( $group['name'] == $name ) {
						$this->_errors[] = 'name';
						$this->_showErrorMessage( 'A schedule with that name already exists.' );
						break;
					}
				}
			} */
			
			//wp_schedule_event(time(), 'hourly', 'my_schedule_hook');
			//echo 'type: '.$_POST[$this->_var.'-type'];
			
			if ( isset( $this->_errors ) )
				$this->_showErrorMessage( 'Error: '.$this->_errors );
			else {
				if ( is_array( $this->_options['schedules'] ) && ! empty( $this->_options['schedules'] ) ) {
					$newID = max( array_keys( $this->_options['schedules'] ) ) + 1;
				} else {
					$newID = 0;
				}
				
				$first_run = strtotime($_POST['ithemes_datetime']);
				
				$this->_options['schedules'][$newID]['first_run'] = $first_run - get_option( 'gmt_offset' )*3600;
				$this->_options['schedules'][$newID]['name'] = $_POST[$this->_var . '-name'];
				$this->_options['schedules'][$newID]['interval'] = $_POST[$this->_var . '-interval'];
				$this->_options['schedules'][$newID]['type'] = $_POST[$this->_var . '-type'];
				
				if (!empty($_POST[$this->_var . '-delete_after'])) {
					$this->_options['schedules'][$newID]['delete_after'] = $_POST[$this->_var . '-delete_after'];
					$delete_after = 1;
				} else {
					$delete_after = 0;
				}
				
				// DEPRECATED at v1.1.38
										if (!empty($_POST[$this->_var . '-send_ftp'])) {
											$this->_options['schedules'][$newID]['send_ftp'] = $_POST[$this->_var . '-send_ftp'];
											$delete_after++;
										}
										if (!empty($_POST[$this->_var . '-send_email'])) {
											$this->_options['schedules'][$newID]['send_email'] = $_POST[$this->_var . '-send_email'];
											$delete_after++;
										}
				// END DEPRECATED
				
				$this->_options['schedules'][$newID]['remote_send'] = $_POST[$this->_var . '-remote_send'];
				if ( $_POST[$this->_var . '-remote_send'] != 'none' ) {
					$this->_options['schedules'][$newID]['delete_after'] = $_POST[$this->_var . '-delete_after'];
				}
				
				// If deleting after, change value to number of things that need sent before its triggered. FTP send or email send will decrement this number. Once number reaches 1, it deletes.
				if ( isset( $this->_options['schedules'][$newID]['delete_after'] ) && ($this->_options['schedules'][$newID]['delete_after'] == '1') ) {
					$this->_options['schedules'][$newID]['delete_after'] = $delete_after;
				}
				
				$this->save();
				
				wp_schedule_event($first_run, $_POST[$this->_var . '-interval'], $this->_var.'-cron_schedule', array($newID) );
			}
		}
		
		/**
		 * iThemesBackupBuddy::delete_files()
		 *
		 * Delete selected backup zip files(s).
		 *
		 */		
		function delete_files() {
			if ( ! empty( $_POST['files'] ) && is_array( $_POST['files'] ) ) {
				foreach ( (array) $_POST['files'] as $id ) {
					if ($id != '') {
						if ( file_exists( $this->_options['backup_directory'] . $id ) ) {
							unlink( $this->_options['backup_directory'] . $id );
							if ( file_exists( $this->_options['backup_directory'] . $id ) ) {
								$this->_errors[] = 'Deletion operation files on ' . $id . '.';
							}
						} else {
							//$this->_errors[] = 'File not found on ' . $id . '.';
						}
					}
				}
			}
			if ( is_array( $this->_errors ) ) {
				$this->_showErrorMessage( implode( '<br />', $this->_errors ) );
			} else {
				$this->_showStatusMessage( __( 'Selected files deleted.', $this->_var ) );
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::show_backup_files()
		 *
		 * Displays listing of all backup files.
		 *
		 */		
		function show_backup_files() {
		
			if ( ! empty( $_POST['delete_file'] ) ) {
				$this->delete_files();
			} elseif ( ! empty( $_POST['email_zip'] ) ) {
				$attachments = array(WP_CONTENT_DIR . '/uploads/backupbuddy_backups/'.$_POST['file']);
				wp_schedule_single_event(time(), $this->_var.'-cron_email', array($_POST['email'], 'BackupBuddy ZIP File', 'Attached is the BackupBuddy generated ZIP file that you requested on the site '.site_url(), $attachments));
				spawn_cron();
				$this->log( 'Scheduled to run Email cron now.' );
				$this->_showStatusMessage('The file has been queued for emailing to '.htmlentities($_POST['email']).'.  You should receive it shortly.');
			} elseif ( ! empty( $_POST['ftp_zip'] ) ) {
				$attachments = array(WP_CONTENT_DIR . '/uploads/backupbuddy_backups/'.$_POST['file']);
				
				wp_schedule_single_event( time(), $this->_var.'-cron_ftp', array( $_POST['ftp_server'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_path'], $this->_options['backup_directory'] . $_POST['file'], $_POST['ftp_type'] ) );
				spawn_cron();
				//$this->cron_ftp( $_POST['ftp_server'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_path'], $_POST['file'] );
				$this->log( 'Scheduled to run FTP cron now.' );
				$this->_showStatusMessage('The file has been queued for uploading to '.htmlentities($_POST['ftp_server']).' by ' . htmlentities( $_POST['ftp_type'] ) . '.  It should be uploaded shortly.');
			} elseif ( ! empty( $_POST['aws_zip'] ) ) {
				$attachments = array(WP_CONTENT_DIR . '/uploads/backupbuddy_backups/'.$_POST['file']);
				wp_schedule_single_event( time(), $this->_var.'-cron_aws', array( $_POST['aws_accesskey'], $_POST['aws_secretkey'], $_POST['aws_bucket'], $_POST['aws_directory'], $this->_options['backup_directory'] . $_POST['file'] ) );
				spawn_cron();
				//$this->cron_aws( $_POST['aws_accesskey'], $_POST['aws_secretkey'], $_POST['aws_bucket'], $_POST['aws_directory'], $this->_options['backup_directory'] . $_POST['file'] );
				$this->log( 'Scheduled to run Amazon S3 cron now.' );
				$this->_showStatusMessage('The file has been queued for uploading to '.htmlentities($_POST['ftp_server']).' by Amazon S3.  It should be uploaded shortly.');
			}
			?>
			<br />
			
			
			
			
			
			<?php
			if ( !file_exists($this->_options['backup_directory']) ) {
				$this->mkdir_recursive($this->_options['backup_directory']);
				
				if (is_writable($this->_options['backup_directory'])) {
					
					// Dummy files to prevent directory listings.
					
				} else {
					echo 'ERROR: The backup directory is not writable. Please verify the directory has write permissions.';
				}
			}
			
			
			
			
			
			
			if ( !file_exists($this->_options['backup_directory'] . '/index.php' ) ) {
				$fh = fopen($this->_options['backup_directory'].'/index.php', 'a');
				fwrite($fh, '<html></html><?php die(); ?>');
				fclose($fh);
				unset($fh);
			}
			if ( !file_exists($this->_options['backup_directory'] . '/index.htm' ) ) {
				$fh = fopen($this->_options['backup_directory'].'/index.htm', 'a');
				fwrite($fh, '<html></html>');
				fclose($fh);
				unset($fh);
			}
			if ( !file_exists($this->_options['backup_directory'] . '/.htaccess' ) ) { // Prevent directory listing.
				$fh = fopen($this->_options['backup_directory'].'/.htaccess', 'a');
				fwrite( $fh, "IndexIgnore *\n" );
				fclose( $fh );
				unset( $fh );
			}
			if ( file_exists($this->_options['backup_directory'] . '/robots.txt' ) ) {
				/*
				$fh = fopen($this->_options['backup_directory'].'/robots.txt', 'a');
				fwrite( $fh, "User-agent: *\n" );
				fwrite( $fh, "Disallow: /\n" );
				fclose( $fh );
				unset( $fh );
				*/
				@unlink( $this->_options['backup_directory'] . '/robots.txt' );
			}
			
			
			
			
			
			$this->trim_zips();
			
			
			
			$this->_set_greedy_script_limits();
			
			$found_file = false;
			
			$files = (array) glob( $this->_options['backup_directory'] . 'backup*.zip' );
			array_multisort( array_map( 'filemtime', $files ), SORT_NUMERIC, SORT_DESC, $files );
			
			//$handler = opendir($this->_options['backup_directory']);
			$file_i=0;
			$file_type = 'unknown';
			foreach ( $files as $file_id => $file ) {
			//while ($file = readdir($handler)) {
				//if ($file != '.' && $file != '..' && substr($file, 0, 6) == 'backup' ) {
					unset( $this->_errors );
					$found_file = true;
					
					if ( $this->_options['integrity_check'] != '1' ) {
						$this->_errors[] = 'unknown ' . $this->tip( 'Backup File integrity checking is disabled on the Settings page.  Backup file status and type are unavailable while this option is disabled.', true );
					} else {
						// Verify backup file integrity.
						if ( substr( $file, -1 ) != '-' ) {
							require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
							$zip = new PclZip( $file );
							if (($list = $zip->listContent()) == 0) { // CORRUPT ZIP
								//die("Error : ".$zip->errorInfo(true));
								//$this->_showErrorMessage( __( 'Zip file error for '.$file.'. The backup may still be in progress for this file:<br />'. $zip->errorInfo(true), $this->_var ) );
								$this->_errors[] = 'Invalid Zip Format or In Progress ' . $this->tip( 'This backup ZIP file was not able to be successfully read to check its integrity.  The backup may still be in process or the backup failed without completing the file. Please manually verify this file before relying on it.' );
							} else {
								$found_dat = false;
								$found_sql = false;
								$found_wpc = false;
								$file_type = '';
								
								$describe_dat = 'The BackupBuddy configuration file that is stored in the ZIP backup has a problem. The backup may have failed or is still backing up.';
								$describe_sql = 'The database file that is stored within this ZIP backup has a problem. This backup will not restore your database which includes post content, settings, etc.';
								$describe_wpc = 'The main WordPress configuration file, wp-config.php, stored within this ZIP backup has a problem. This backup may be missing required WordPress files and be highly unreliable for file restoration.';
								
								// TODO: After old zip file names are phased out then we can remove checking for posb. This is set to handle both old filenames and the new one where dashes separate sections for the url.
								$posa = strrpos($file,'_')+1;
								$posb = strrpos($file,'-')+1;
								if ( $posa < $posb ) {
									$zip_id = $posb;
									$zip_id = strrpos($file,'-')+1;
								} else {
									$zip_id = $posa;
									$zip_id = strrpos($file,'_')+1;
								}
								
								
								$zip_id = substr( $file, $zip_id, strlen($file)-$zip_id-4 );
								for ($i=0; $i<sizeof($list); $i++) {
									//echo 'loop';
									if ( $list[$i]['filename'] == 'wp-content/uploads/temp_'.$zip_id.'/backupbuddy_dat.php' ) {
										$found_dat = true;
										$file_type = 'full';
										if ( $list[$i]['size'] == 0 ) {
											$this->_errors[] = 'BackupBuddy DAT file is empty. (Err 2234534)<br />';
										}
									} elseif ( $list[$i]['filename'] == 'backupbuddy_dat.php' ) {
										$found_dat = true;
										$file_type = 'db';
										if ( $list[$i]['size'] == 0 ) {
											$this->_errors[] = 'BackupBuddy DAT file is empty. (Err 16892534)<br />';
										}
									} elseif ( $list[$i]['filename'] == 'wp-content/uploads/temp_'.$zip_id.'/db.sql' ) {
										$found_sql = true;
										$file_type = 'full';
										if ( $list[$i]['size'] == 0 ) {
											$this->_errors[] = 'Database SQL file is empty. (Err 9882534)<br />';
										}
									} elseif ( $list[$i]['filename'] == 'db.sql' ) {
										$found_sql = true;
										$file_type = 'db';
										if ( $list[$i]['size'] == 0 ) {
											$this->_errors[] = 'Database SQL file is empty. (Err 5492534)<br />';
										}
									} elseif ( $list[$i]['filename'] == 'wp-config.php' ) {
										$found_wpc = true;
										$file_type = 'full';
										if ( $list[$i]['size'] == 0 ) {
											$this->_errors[] = 'WordPress config file (wp-config.php) is empty. (Err 5492534)<br />';
										}
									}
								}
								if ( $found_dat == false ) { $this->_errors[] = 'BackupBuddy DAT file is missing. ' . $this->tip( $describe_dat, false ) . '<br />'; }
								if ( $found_sql == false ) { $this->_errors[] = 'Database SQL file is missing.' . $this->tip( $describe_sql, false ) . '<br />'; }
								if ( $file_type == 'full' ) {
									if ( $found_wpc == false ) { $this->_errors[] = 'WordPress config file, wp-config.php, missing. ' . $this->tip( $describe_wpc, false ) . '<br />'; }
								}
							}
							unset( $zip );
							//print_r( $this->_errors );
						}
						if ( $file_type == 'full' ) {
							$file_type = 'Full';
						} elseif ( $file_type == 'db' ) {
							$file_type = 'Database';
						} else {
							$file_type = 'Unknown';
						}
						// End file integrity check
					}
					
					
					// 0	=>	File modified time
					// 1	=>	Filename
					// 2	=>	Backup status
					// 3	=>	Backup type
					// 4	=>	Filesize
					$file_mod_time = filemtime( $file ) + ( ( get_option( 'gmt_offset' ) * 3600 ) +86400);
					$files_list[ $file_mod_time ] = array(
						//date( $this->_timestamp, $file_mod_time ),
						$file_mod_time,
						$file,
						$this->_pretty_backup_status(),
						$file_type,
						number_format( ( filesize( $file ) / 1048576 ), 2 ),
					);
					
					
				//}
			}
			//closedir($handler);
			//unset($handler);
			
			if ($found_file) {
				//sort($files, SORT_NUMERIC);
				?>
				
				
				<form id="posts-filter" enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>-backup">
				<div class="tablenav">
					<div class="alignleft actions">
						<?php $this->_addSubmit( 'delete_file', array( 'value' => 'Delete', 'class' => 'button-secondary delete' ) ); ?>
					</div>
					<br class="clear" />
				</div>
				
				<table class="widefat" style="min-width: 600px;">
				<thead>
					<tr class="thead">
						<th scope="col" class="check-column"><input type="checkbox" id="check-all-groups" /></th>
						<th style="white-space: nowrap">Backup File</th>
						<th style="white-space: nowrap">Last Modified &darr;</th>
						<th style="white-space: nowrap">File Size</th>
						<th style="white-space: nowrap">Status</th>
						<th style="white-space: nowrap">Type</th>
						<th style="white-space: nowrap">Send<?php $this->tip( 'Large files are not compatible with all email servers. Low PHP server configuration time limits can cause remote transfers to FTP or Amazon S3 to time out.' ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr class="thead">
						<th scope="col" class="check-column"><input type="checkbox" id="check-all-groups" /></th>
						<th>Backup File</th>
						<th>Last Modified</th>
						<th>File Size</th>
						<th>Status</th>
						<th>Type</th>
						<th>Send<?php $this->tip( 'Large files are not compatible with all email servers. Low PHP server configuration time limits can cause remote transfers to FTP or Amazon S3 to time out.' ); ?></th>
					</tr>
				</tfoot>
				<tbody>
					<tr style="background-color: #ffd000;">
					<?php
				
				foreach ($files_list as $file) {
					$file_i++;
					echo '<tr><th scope="row" class="check-column" style="padding-bottom: 5px;"><input type="checkbox" name="files[]" class="files" value="'. str_replace( $this->_options['backup_directory'], '', $file[1] ) .'" id="file_name_'.$file_i.'" /></th><td style="white-space: nowrap"><a href="'. site_url() . '/wp-content/uploads/backupbuddy_backups/' . str_replace( $this->_options['backup_directory'], '', $file[1] ) . '">' . str_replace( $this->_options['backup_directory'], '', $file[1] ) . '</a></td><td style="white-space: nowrap">' . date( $this->_timestamp, $file[0] ) . ' <span style="color: #AFAFAF;">(' . human_time_diff( $file[0], time() + ( ( get_option( 'gmt_offset' ) * 3600 ) +86400) ) . ' ago)</span></td><td>'. $file[4] .' MB</td><td>' . $file[2] . '</td><td>' . $file[3] . '</td><td  style="white-space: nowrap"><a href="ithemes-ftp_pop" id="ftp_pop_'.$file_i.'" class="ithemes_pop ithemes_ftp_pop"><img src="'.$this->_pluginURL.'/images/server_go.png" style="vertical-align: -3px;" title="Send file by FTP" /></a> <a href="ithemes-aws_pop" id="aws_pop_'.$file_i.'" class="ithemes_pop ithemes_aws_pop"><img src="'.$this->_pluginURL.'/images/aws.gif" style="vertical-align: -3px;" title="Send file by Amazon S3" /></a> <a href="ithemes-email_pop" id="email_pop_'.$file_i.'" class="ithemes_pop ithemes_email_pop"><img src="'.$this->_pluginURL.'/images/email_go.png" style="vertical-align: -3px;" title="Send file by Email" /></a></td></tr>';
				}
				
				
				?>
							</tr>
						</tbody>
					</table>
					<div class="tablenav">
						<div class="alignleft actions">
							<?php $this->_addSubmit( 'delete_file', array( 'value' => 'Delete', 'class' => 'button-secondary delete' ) ); ?>
						</div>
						<br class="clear" />
					</div>
					</form><br />
				<?php
			} else {
				echo '<i>You do not have any backup archives stored yet.</i><br /><br /><br />';
			}
			?>
			
			<br />
			<a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>" class="button-secondary">Download Import/Migration Script (importbuddy.php)</a>
			
			<div id="ithemes-email_pop" class="ithemes-popup" style="text-align: center;">
				<center><h3>Email Address:</h3></center>
				<form method="post" action="admin.php?page=ithemes-backupbuddy-backup">
					<input type="hidden" name="email_zip" value="true" />
					<input type="hidden" name="file" value="NULL" id="email_file_name" />
					<input type="text" name="email" value="<?php echo $this->_options['email']; ?>" style="width: 200px;" />
					<p class="submit"><input value="Send File" type="submit" name="backup" class="button-primary" /></p>
				</form>
			</div>
			<div id="ithemes-ftp_pop" class="ithemes-popup" style="text-align: center;">
				<center><h3>FTP Connection:</h3></center>
				<form method="post" action="admin.php?page=ithemes-backupbuddy-backup">
					<input type="hidden" name="ftp_zip" value="true" />
					<input type="hidden" name="file" value="NULL" id="ftp_file_name" />
					
					<table class="form-table">
						<tr><td><label for="ftp_server">FTP Server:</label></td><td><input type="text" name="ftp_server" value="<?php echo $this->_options['ftp_server']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="ftp_user">FTP User:</label></td><td><input type="text" name="ftp_user" value="<?php echo $this->_options['ftp_user']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="ftp_pass">FTP Pass:</label></td><td><input type="password" name="ftp_pass" value="<?php echo $this->_options['ftp_pass']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="ftp_path">FTP Path:</label></td><td><input type="text" name="ftp_path" value="<?php echo $this->_options['ftp_path']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="ftp_type">FTP Type:</label></td><td>
							<select name="ftp_type" style="width: 200px;" />
								<option value="ftp" <?php if ( isset( $this->_options['ftp_type'] ) && $this->_options['ftp_type'] == 'ftp' ) { echo 'selected'; } ?>>FTP</option>
								<option value="ftps" <?php if ( isset( $this->_options['ftp_type'] ) && $this->_options['ftp_type'] == 'ftps' ) { echo 'selected'; } ?>>FTPs (SSL)</option>
							</select>
						</td></tr>
					</table>
					
					<p class="submit"><input value="Send File to FTP" type="submit" name="backup" class="button-primary" /></p>
				</form>
			</div>
			<div id="ithemes-aws_pop" class="ithemes-popup" style="text-align: center;">
				<center><h3>Amazon S3 Connection:</h3></center>
				<form method="post" action="admin.php?page=ithemes-backupbuddy-backup">
					<input type="hidden" name="aws_zip" value="true" />
					<input type="hidden" name="file" value="NULL" id="aws_file_name" />
					
					<table class="form-table">
						<tr><td><label for="aws_accesskey">AWS Access Key:</label></td><td><input type="text" name="aws_accesskey" value="<?php echo $this->_options['aws_accesskey']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="aws_secretkey">AWS Secret Key:</label></td><td><input type="password" name="aws_secretkey" value="<?php echo $this->_options['aws_secretkey']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="aws_bucket">Bucket Name:</label></td><td><input type="text" name="aws_bucket" value="<?php echo $this->_options['aws_bucket']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="aws_directory">Directory Name:</label></td><td><input type="text" name="aws_directory" value="<?php echo $this->_options['aws_directory']; ?>" style="width: 200px;" /></td></tr>
					</table>
					
					<p class="submit"><input value="Send File to S3" type="submit" name="backup" class="button-primary" /></p>
				</form>
			</div>
			
			<?php
		}
		
		
		function trim_zips() {
			if ( ( !empty( $this->_options['zip_limit'] ) ) && ( $this->_options['zip_limit'] > 0 ) ) {
				$files = glob( $this->_options['backup_directory'] . 'backup*.zip' );
				array_multisort( array_map( 'filemtime', $files ), SORT_NUMERIC, SORT_DESC, $files );
				
				$file_count = 0;
				foreach( $files as $id => $file ) {
					//echo $file . '<br />';
					$file_count++;
					if ( $file_count > $this->_options['zip_limit'] ) {
						echo 'Maximum number of archived zips exceeded. Deleted extra backup.';
						$this->tip( 'Deleted backup due to archive limit set on the settings page: ' . $file );
						echo '<br /><br />';
						unlink( $file );
					}
				}
			}
		}
		
		
		function _pretty_backup_status() {
			$string = '';
			if ( isset( $this->_errors ) ) {
				foreach( $this->_errors as $error ) {
					$string .= $error;
				}
				$string = '<span style="color: #DE4E21;">' . $string . '</span>';
			} else {
				$string = 'Good';
			}
			return $string;
			
			//return print_r($this->_errors);
		}
		
		/**
		 * iThemesBackupBuddy::cron_email()
		 *
		 * Begin process of backing up.
		 *
		 * @param	$to				string		Email address to send email to.
		 * @param	$subject		string		Subject of email.
		 * @param	$message		string		Email message body.
		 * @param	$attachments	string[]	Array of file paths of attachments to send.
		 * DEPRICATED:	@param	$from			string		Optional return email address.
		 *
		 */
		function cron_email($to, $subject, $message, $attachments, $delete_after_int = 0) {
			$headers = 'From: BackupBuddy <' . get_option('admin_email') . '>' . "\r\n\\";
			wp_mail($to, $subject, $message, $headers, $attachments);
			
			// Decrement this each sending operation. If we reach 1 then time to delete file since last send is done.
			$delete_after_int = $delete_after_int - 1;
			if ( $delete_after_int == 1 ) {
				unlink( $attachments );
			}
		}
		
		/**
		 * iThemesBackupBuddy::cron_add_schedules()
		 *
		 * Add additional scheduling intervals to WordPress cron system.
		 *
		 */
		function cron_add_schedules( $schedules = array() ) {
			$schedules['weekly'] = array( 'interval' => 604800, 'display' => 'Once Weekly' );
			$schedules['twicemonthly'] = array( 'interval' => 604800, 'display' => 'Twice Monthly' );
			$schedules['monthly'] = array( 'interval' => 2592000, 'display' => 'Once Monthly' );
			return $schedules;
		}
		
		
		/**
		 * iThemesBackupBuddy::cron_schedule()
		 *
		 * Handle scheduled jobs.
		 *
		 */
		function cron_schedule($item) {
			$this->load();
			
			if ( is_array($this->_options['schedules'][$item]) ) {
				$scheduled = $this->_options['schedules'][$item];
				$this->_isScheduled = true; // Set this variable so manual email notification doesnt get sent.
				
				$this->_options['last_backup'] = 0; // Clear this so scheduled backups dont get blocked.
				
				$this->_backup( $scheduled['type'], true ); // second parameter tells it this is a cron spawn so it wont print out listing.
				
				if ( ( isset( $this->_options['email_notify_scheduled'] ) ) && ( $this->_options['email_notify_scheduled'] ) ) {
					//if ( ( !isset( $this->_options['email_notify_manual'] ) ) || ( !$this->_options['email_notify_manual'] ) ) { // If manual backups isnt set OR manual backups are off then send schedule email.  Manual emailing is always triggered so only send schedule email if manual emails is not also checked.
					$this->mail_notice('Scheduled backup "'.$scheduled['name'].'" completed.');
					//}
				}
				
				if ( isset( $scheduled['delete_after'] ) ) {
					$delete_after_int = $scheduled['delete_after'];
				} else {
					$delete_after_int = 0; // dont delete file
				}
				
				
				// NEW AT v1.1.38
						$got_ftp = false;
						$got_email = false;
						$got_aws = false;
						
						// DEPRECATED at v1.1.38
													if ( array_key_exists('send_ftp', $scheduled) && ($scheduled['send_ftp'] == '1') ) {
														$got_ftp = true;
													}
													if ( array_key_exists('send_email', $scheduled) && ($scheduled['send_email'] == '1') ) {
														if ($got_ftp == true) { echo ', '; }
														$got_email = true;
													}
						// END DEPRECATED
						
						if ( array_key_exists('remote_send', $scheduled) ) {
							if ( $scheduled['remote_send'] == 'ftp' ) {
								$got_ftp = true;
							} elseif ( $scheduled['remote_send'] == 'aws' ) {
								$got_aws = true;
							} elseif ( $scheduled['remote_send'] == 'email' ) {
								$got_email = true;
							} else {
								echo 'None';
							}
						}
				// END NEW
				
				
				
				if ( $got_email ) {
					//function cron_email($to, $subject, $message, $attachments) {
					$this->cron_email($this->_options['email'],'Scheduled backup file.','Attached to this email is the backup file for the scheduled backup "'.$scheduled['name'].'"' . "\n\nFile: ".$this->zip_file."\n\nBackupBuddy by http://anonym.to/?http://pluginbuddy.com", $this->zip_file, $scheduled['delete_after']);
				}
				if ( $got_ftp ) {
					if ( array_key_exists('ftp_type', $scheduled) ) {
						$ftp_type = $this->_options['ftp_type'];
					} else {
						$ftp_type = 'ftp'; // handle deprecated old schedules
					}
					if ( $scheduled['delete_after'] > 0 ) {
						$delete_after = true;
					} else {
						$delete_after = true;
					}
					$this->cron_ftp( $this->_options['ftp_server'], $this->_options['ftp_user'], $this->_options['ftp_pass'], $this->_options['ftp_path'], $this->zip_file, $ftp_type, $delete_after );
				}
				if ( $got_aws ) {
					//cron_aws($aws_accesskey, $aws_secretkey, $aws_bucket, $file, $delete_after_int = 0)
					$this->cron_aws($this->_options['aws_accesskey'], $this->_options['aws_secretkey'], $this->_options['aws_bucket'], $this->_options['aws_directory'], $this->zip_file, $scheduled['delete_after']);
				}
			}
		}
		
		
		function _generate_serial() {
			return $this->rand_string( 10 );
		}
		
		
		function _get_zip_name( $serial ) {
			$siteurl = site_url(); //get_bloginfo("url");
			$siteurl = str_replace( 'http://', '', $siteurl );
			$siteurl = str_replace( 'https://', '', $siteurl );
			$siteurl = str_replace( '/', '_', $siteurl );
			$siteurl = str_replace( '\\', '_', $siteurl );
			$siteurl = str_replace( '.', '_', $siteurl );
			//echo 'url:'. $siteurl . '!<br />';
			return $this->_options['backup_directory'] . 'backup-' . $siteurl . '-' . str_replace( '-', '_', date( 'Y-m-d' ) ) . "-$serial.zip";
		}
		
		
		function _get_storage_directory( $serial, $create_new = false ) {
			//$upload_dir = wp_upload_dir();
			//$upload_path = $upload_dir['basedir'];
			
			// Sadly we currently must use this location for uploads and ignore custom uploads folders due to importbuddy limitations.
			$upload_path = ABSPATH . 'wp-content/uploads';
			
			$storage_directory = "$upload_path/temp_$serial";
			
			
			if ( ! file_exists( $storage_directory ) ) {
				if ( false === $this->mkdir_recursive( $storage_directory ) ) {
					$this->alert('Unable to create temporary storage directory (' . $storage_directory . ').', true, '9002');
					return new WP_Error( 'write_failure', 'Unable to create temporary storage directory' );
				}
			}
			
			// Secure the temp directory against prying eyes just in case!
			if ( !file_exists($storage_directory . '/index.php' ) ) {
				$fh = fopen($storage_directory.'/index.php', 'a');
				fwrite($fh, '<html></html><?php die(); ?>');
				fclose($fh);
				unset($fh);
			}
			if ( !file_exists($storage_directory . '/index.htm' ) ) {
				$fh = fopen($storage_directory.'/index.htm', 'a');
				fwrite($fh, '<html></html>');
				fclose($fh);
				unset($fh);
			}
			if ( !file_exists($storage_directory . '/.htaccess' ) ) { // Prevent directory listing.
				$fh = fopen($storage_directory.'/.htaccess', 'a');
				fwrite( $fh, "IndexIgnore *\n" );
				fclose( $fh );
				unset( $fh );
			}
			if ( file_exists($storage_directory . '/robots.txt' ) ) {
				/*
				$fh = fopen($storage_directory.'/robots.txt', 'a');
				fwrite( $fh, "User-agent: *\n" );
				fwrite( $fh, "Disallow: /\n" );
				fclose( $fh );
				unset( $fh );
				*/
				@unlink( file_exists($storage_directory . '/robots.txt' ) );
			}
			
			return $storage_directory;
		}
		
		
		function _pre_backup( $type ) {
		
			// Give the script enough memory and time to work properly
			$this->_set_greedy_script_limits();
			
			// Set up backup variables
			$serial = $this->_generate_serial();
			$zip_file = $this->_get_zip_name( $serial );
			$storage_directory = $this->_get_storage_directory( $serial, true );
			
			// Verify that the temporary storage directory exists
			if ( is_wp_error( $storage_directory ) ) {
				// Without this, this just fails with all kinds of errors.  Added this alert as a quick fix for this issue.
				// TODO: Better error handling here.
				echo 'Unknown Error #35565654. Contact PluginBuddy support.';
				die();
				return $storage_directory;
			} elseif ( ! file_exists( $storage_directory ) ) {
				return new WP_Error( 'write_failure', 'Unable to create temporary storage directory' );
			}
			
			// Create the backup directory if it doesn't exist
			if ( ! file_exists( $this->_options['backup_directory'] ) ) {
				if ( false === $this->mkdir_recursive( $this->_options['backup_directory'] ) )
					return new WP_Error( 'write_failure', 'Unable to create backup directory' );
			}
			
			// Verify that the backup zip file can be written to
			if ( false === ( $file_handle = fopen( $zip_file, 'w' ) ) )
				return new WP_Error( 'write_failure', 'Unable to create backup file' );
			fclose( $file_handle );
			
			
			// TODO: Add code to handle incomplete processes
			unset( $this->_options['backup_status'] );
			
			
			// Set up the backup status information
			if ( empty( $this->_options['backup_status'] ) ) {
				$this->_options['backup_status'] = array(
					'type'				=> $type,
					'serial'			=> $serial,
					'zip_file'			=> $zip_file,
					'storage_directory'	=> $storage_directory,
					'processes'			=> array(),
					'start_time'		=> time(),
					'last_update_time'	=> time(),
					'last_process'		=> '',
					'errors'			=> array(),
				);
				
				$this->save();
			}
			
			// Set up the processes that will be run based on type
			$this->_processes = array(
				'settings'	=> array(
					'name'				=> 'Settings Backup',
					'description'		=> 'Creating site settings backup...',
					'function'			=> '_create_settings_file',
					'time_limit'		=> 5 * 60, // 5 minutes
					'number'			=> 2,
				),
				'database'	=> array(
					'name'				=> 'Database Backup',
					'description'		=> 'Creating database backup...',
					'function'			=> '_create_database_backup_file',
					'time_limit'		=> 3 * 60 * 60, // 3 hours
					'number'			=> 3,
				),
			);
			
			if ( 'db' === $type ) {
				$this->_processes['archive'] = array(
					'name'				=> 'Database Backup',
					'description'		=> 'Storing database backup to a single archive...',
					'function'			=> '_create_database_backup',
					'time_limit'		=> 4 * 60 * 60, // 4 hours
					'number'			=> 4,
				);
			}
			else {
				$this->_processes['archive'] = array(
					'name'				=> 'Full Backup',
					'description'		=> 'Storing full backup to a single archive...',
					'function'			=> '_create_full_backup',
					'time_limit'		=> 8 * 60 * 60, // 8 hours
					'number'			=> 4,
				);
			}
			
			// Set up the process statuses
			$this->_statuses = array(
				'pre_run'		=> array(
					'description'	=> 'Process has not started yet',
					'error'			=> false,
				),
				'running'		=> array(
					'description'	=> 'Process is still running',
					'error'			=> false,
				),
				'complete'		=> array(
					'description'	=> 'Process completed successfully',
					'error'			=> false,
				),
				'timed_out'		=> array(
					'description'	=> 'Process took too long to complete',
					'error'			=> true,
				),
				'failed'		=> array( // This is a temp status message and should be replaced by more specific status codes and messages
					'description'	=> 'Process failed to complete successfully',
					'error'			=> true,
				),
			);
		}
		
		
		function _post_backup( $type ) {
			unlink( $this->_options['backup_status']['storage_directory'] . '/.htaccess' );
			sleep(1);
		
			// Remove the temporary storage directory
			if ( ! empty( $this->_options['backup_status']['storage_directory'] ) && file_exists( $this->_options['backup_status']['storage_directory'] ) ) {
				if ( false === $this->_delete_directory( $this->_options['backup_status']['storage_directory'] ) ) {
					echo "<p class='bb-error'>Unable to remove temporary storage directory</p>\n";
					echo "<p class='bb-error'>The following directory should be manually removed: <strong><code>{$this->_options['backup_status']['storage_directory']}</code></strong></p>\n";
				}
			}
			
			// Remove the backup status so that another backup can run
			if ( isset( $this->_options['backup_status'] ) ) {
				$this->zip_file = $this->_options['backup_status']['zip_file']; // Set last filename for use for cron mailing, etc.
				unset( $this->_options['backup_status'] );
				
				$this->save();
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::backup()
		 *
		 * Begin process of backing up.
		 *
		 *	@param	$step	integer	Numeric step number.
		 *	@param	$type	string	Optional backup type for CRON jobs. Use querystring for http based backups.
		 *	@param	$get_zip_id	int	ID number if passed in querystring.
		 *
		 */
		function _backup( $type, $cronning = false ) {
			if ( ! empty( $_GET['reset_last_backup'] ) ) {
				$this->_options['last_backup'] = 0; // Reset last backup time to allow it to run.
			}
			
			// Set up everything needed for the backup processes
			$this->_pre_backup( $type );
			
			// Run backup processes
			$this->_run_backup_processes();
			
			// Clean up after the backup processes
			$this->_post_backup( $type );
			
			// Show backed-up files ONLY if this is NOT a cron backup!
			/*
			if ( !defined('DOING_CRON') && !isset($_GET['doing_wp_cron']) && ( $cronning === false ) ) {
				$this->show_backup_files();
			}
			*/
		}
		
		function _run_backup_processes() {
//			$this->_start_time = time();
			
			foreach ( (array) $this->_processes as $process => $process_options ) {
				if ( empty( $this->_options['backup_status']['processes'][$process] ) ) {
					$this->_options['backup_status']['processes'][$process] = array(
						'status'	=> 'pre_start',
					);
				}
			}
			
			?>
			<script type="text/javascript">
				backupbuddy_timer = window.setTimeout('backupbuddy_timeout()', 1800000);
				function backupbuddy_timeout() { alert('WARNING! The backup is taking an excessive amount of time (30+ minutes).  Verify your browser is still actively loading the page since the backup may have failed.'); }
			</script>
			<?php
			
			
			foreach ( (array) $this->_processes as $process => $process_options ) {
				$current_time = time();
				
				$this->_options['backup_status']['last_update_time'] = $current_time;
				$this->_options['backup_status']['last_process'] = $process;
				
				if ( 'pre_start' === $this->_options['backup_status']['processes'][$process]['status'] ) {
					$this->_options['backup_status']['processes'][$process] = array(
						'start_time'		=> $current_time,
						'last_update_time'	=> $current_time,
						'status'			=> 'running',
					);
					
				}
				
				// If process status is not 'running', skip to next process
				if ( 'running' !== $this->_options['backup_status']['processes'][$process]['status'] ) {
					$this->save();
					
					continue;
				}
				
				$this->_options['backup_status']['processes'][$process]['last_update_time'] = $current_time;
				
				$total_time = $current_time - $this->_options['backup_status']['processes'][$process]['start_time'];
				
				// Don't permit the process to exceed its time limit
				if ( isset( $process_options['time_limit'] ) && ( $process_options['time_limit'] > 0 ) && ( $total_time > $process_options['time_limit'] ) ) {
					$this->_options['backup_status']['processes'][$process]['status'] = 'timed_out';
					$this->save();
					
					continue;
				}
				
				
				//echo 'num:'.$process_options['number'];
				echo '<script type="text/javascript">';
				// Starting this step, color its background and text
				echo "	jQuery('#backupbuddy_step".$process_options['number']."').css({'color':'#000000','background-color':'transparent'});";
				echo "	jQuery('#backupbuddy_step".$process_options['number']."').append('&nbsp;<img id=\"backupbuddy_loading".$process_options['number']."\" src=\"".$this->_pluginURL."/images/loading.gif\" style=\"vertical-align: 0px;\" />');";
				echo "\n";
				// Finished previous step, add checkmark.
				echo "	jQuery('#backupbuddy_loading". ($process_options['number']-1) ."').hide();";
				echo "	jQuery('#backupbuddy_step". ($process_options['number']-1) ."').append('&nbsp;<img src=\"".$this->_pluginURL."/images/tick.png\" style=\"vertical-align: -3px;\" />');";
				
				
				
				echo '</script>';
				
				echo "<p>{$process_options['description']}</p>\n";
				flush();
				
				// Run the process
				call_user_func_array( array( &$this, $process_options['function'] ), array( $process ) );
				
				// Save any changes added by process
				$this->save();
			}
			
			echo '<script type="text/javascript">';
			// finished. Color done step.
			echo "	jQuery('#backupbuddy_step5').css({'color':'#000000','background-color':'transparent'});";
			echo "\n";
			// Finished. Checkmark previous and done step.
			echo "	jQuery('#backupbuddy_loading4').hide();";
			echo "	jQuery('#backupbuddy_step4').append('&nbsp;<img src=\"".$this->_pluginURL."/images/tick.png\" style=\"vertical-align: -3px;\" />');";
			echo "	jQuery('#backupbuddy_step5').append('&nbsp;<img src=\"".$this->_pluginURL."/images/tick.png\" style=\"vertical-align: -3px;\" />');";
			
			echo '	clearTimeout(backupbuddy_timer);';
			
			echo '</script>';
			
			$this->trim_zips();
			
			echo '<span class="updated" style="padding: 6px;"><b>Backup Complete.</b></span><br /><br />';
			if ( !isset( $this->_isScheduled ) ) {
				if ( ( isset( $this->_options['email_notify_manual'] ) ) && ( $this->_options['email_notify_manual'] ) ) {
					$this->mail_notice('Manual backup completed on site ' . site_url() . '.');
				}
			}
		}
		
		/**
		 * iThemesBackupBuddy::_create_settings_file()
		 *
		 * Create .dat (now .php for security) file holding meta information.
		 *
		 */
		function _create_settings_file( $process ) {
			global $wpdb;
			
			
			$storage_directory = $this->_options['backup_status']['storage_directory'];
			$settings_file = "$storage_directory/backupbuddy_dat.php";
			
			
			$settings = array(
				// Store information about the plugin version and time
				'backupbuddy_version'		=> $this->_version,
				'backup_time'				=> date( 'Y-m-d H:i:s' ),
				'backup_type'				=> $_GET['type'],
				
				// Save details about site's WordPress setup
				'abspath'					=> ABSPATH,
				'siteurl'					=> site_url(),
				'home'						=> get_option( 'home' ),
				'blogname'					=> get_option( 'blogname' ),
				'blogdescription'			=> get_option( 'blogdescription' ),
				
				// Add the database details
				'db_name'					=> DB_NAME,
				'db_user'					=> DB_USER,
				'db_prefix'					=> $wpdb->prefix,
				'db_server'					=> DB_HOST,
				'db_password'				=> DB_PASSWORD,		// TODO: If mcrypt is installed, then encrypt this
			);
			
			// If currently using SSL or forcing admin SSL then we will check the hardcoded defined URL to make sure it matches.
			if ( is_ssl() OR ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN == true ) ) {
				$settings['siteurl'] = get_option('siteurl');
			}
			
			
			if ( false === ( $file_handle = fopen( $settings_file, 'w' ) ) ) {
				$this->_options['backup_status']['processes'][$process]['status'] = 'failed';
				
				$error =& new WP_Error( 'write_failure', 'Unable to write Settings File' );
				
				echo '<div class="bb-error">' . $error->get_error_message() . "</div>\n";
				return $error;
			}
			
			fwrite( $file_handle, serialize( $settings ) );
			fclose( $file_handle );
			
			
			$this->_options['backup_status']['processes'][$process]['status'] = 'complete';
			
			return true;
		}
		
		
		/**
		 * iThemesBackupBuddy::backup_db_create_database_backup_file()
		 *
		 * Backup database.
		 *
		 */
		function _create_database_backup_file( $process ) {
			global $wpdb;
			
			
			$storage_directory = $this->_options['backup_status']['storage_directory'];
			$database_file = "$storage_directory/db.sql";
			
			
			if ( false === ( $file_handle = fopen( $database_file, 'w' ) ) ) {
				$this->_options['backup_status']['processes'][$process]['status'] = 'failed';
				
				$error =& new WP_Error( 'write_failure', 'Unable to write Database File' );
				
				echo '<div class="bb-error">' . $error->get_error_message() . "</div>\n";
				return $error;
			}
			
			
			flush();
			$server = DB_HOST;
			$user = DB_USER;
			$pass = DB_PASSWORD;
			$db = DB_NAME;
			
			$_count = 0;
			global $wpdb;
			
			
			mysql_connect($server, $user, $pass);
			mysql_select_db($db);
			$tables = mysql_list_tables($db);
			$insert_sql = "";
			$_char_count = 0;
			
			if ( (isset($this->_options['backup_nonwp_tables'])) && ($this->_options['backup_nonwp_tables'] == 1) ) {
				$backup_nonwp_tables = 1;
				echo 'Including non-WordPress database tables in backup...<br />';
			} else {
				$backup_nonwp_tables = 0;
			}
			
			while ($td = mysql_fetch_array($tables)) {
				$table = $td[0];
				if ( (substr($table, 0, strlen($wpdb->prefix)) == $wpdb->prefix) || ( $backup_nonwp_tables == 1 ) ) { // Only backup this wordpress installations database.
					$r = mysql_query("SHOW CREATE TABLE `$table`");
					if ($r) {
						
						$d = mysql_fetch_array($r);
						$d[1] .= ";";
						$insert_sql .= str_replace("\n", "", $d[1]) . "\n";
						
						$table_query = mysql_query("SELECT * FROM `$table`") or $this->alert('Unable to read database table ' . $table . '. Your backup will not include data from this table (you may ignore this warning if you do not need this specific data). This is due to the following error: ' . mysql_error(), true, '9001');
						$num_fields = mysql_num_fields($table_query);
						while ($fetch_row = mysql_fetch_array($table_query)) {
							$insert_sql .= "INSERT INTO $table VALUES(";
							for ($n=1;$n<=$num_fields;$n++) {
								$m = $n - 1;
								$insert_sql .= "'".mysql_real_escape_string($fetch_row[$m])."', ";
							}
							$insert_sql = substr($insert_sql,0,-2);
							$insert_sql .= ");\n";
							
							fwrite($file_handle, $insert_sql);
							unset($insert_sql);
							$insert_sql = "";
							
							$_count++;
							if ($_count >= 200) {
								echo '.';
								flush();
								$_count = 0;
								$_char_count++;
								if ($_char_count >= 60) {
									echo '<br />';
									$_char_count = 0;
								}
							}
						}
						echo '.';
					}
				}
			}
			
			fclose( $file_handle );
			unset( $file_handle );
			
			
			return true;
		}
		
		
		/**
		 * iThemesBackupBuddy::_create_database_backup()
		 *
		 * Backup web files.
		 *
		 */
		function _create_database_backup( $process ) {
			$storage_directory = $this->_options['backup_status']['storage_directory'];
			
			$options = array(
				'remove_path'	=> $storage_directory,
				'overwrite'		=> true,
				'append'		=> false,
			);
			
			/*
			if ( isset( $this->_options['compression'] ) && ( $this->_options['compression'] != 1 ) ) {
				// Compression specifically disabled.
				$options['no_compress'] = true;
			}
			*/
			
			require_once( dirname( __FILE__ ) . '/lib/zip/zip.php' );
			$pluginBuddyZip = new PluginBuddyZip( $this );
			
			if ($this->_options['compression'] == 0) {
				$disable_compression = true;
			} else {
				$disable_compression = false;
			}
			if ( isset( $this->_options['force_compatibility'] ) && ( $this->_options['force_compatibility'] == 1) ) {
				$options['force_compatibility'] = true;
			} else {
				$options['force_compatibility'] = false;
			}
			$result = $pluginBuddyZip->add_directory_to_zip( $this->_options['backup_status']['zip_file'], $storage_directory, $options, $disable_compression );
			
			if ( true !== $result ) {
				$this->_options['backup_status']['processes'][$process]['status'] = 'failed';
				
				$error =& new WP_Error( 'archive_failure', 'Unable to create backup: ' . $result['error'] );
				
				echo '<div class="bb-error">' . $error->get_error_message() . "</div>\n";
				return $error;
			}
			
			
			$this->_options['backup_status']['processes'][$process]['status'] = 'complete';
			
			return true;
		}
		
		
		/**
		 * iThemesBackupBuddy::_create_full_backup()
		 *
		 * Backup web files.
		 *
		 */
		function _create_full_backup( $process ) {
			$exclude = ltrim( str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_options['backup_directory'] ), ' \\/' );
			if ( is_array( $this->_options['excludes'] ) ) {
				$exclude = array_merge( (array)$exclude, $this->_options['excludes'] );
				//echo 'gotexcludes';
			} else {
				$exclude = (array)$exclude;
			}
			//echo $exclude;
			// Exclude dir format: wp-content/uploads/backupbuddy_backups/
			$options = array(
				'remove_path'	=> ABSPATH,
				'excludes'		=> $exclude,
				'overwrite'		=> true,
				'append'		=> false,
			);
			
			/*
			if ( isset( $this->_options['compression'] ) && ( $this->_options['compression'] != 1 ) ) {
				// Compression specifically disabled.
				$options['no_compress'] = true;
			}
			*/
			
			require_once( dirname( __FILE__ ) . '/lib/zip/zip.php' );
			$pluginBuddyZip = new PluginBuddyZip( $this );
			
			//echo 'yo'.$this->_options['backup_directory'].'!';
			
			if ($this->_options['compression'] == 0) {
				$disable_compression = true;
			} else {
				$disable_compression = false;
			}
			if ( isset( $this->_options['force_compatibility'] ) && ( $this->_options['force_compatibility'] == 1) ) {
				$options['force_compatibility'] = true;
			} else {
				$options['force_compatibility'] = false;
			}
			$result = $pluginBuddyZip->add_directory_to_zip( $this->_options['backup_status']['zip_file'], ABSPATH, $options, $disable_compression );
			
			if ( true !== $result ) {
				$this->_options['backup_status']['processes'][$process]['status'] = 'failed';
				
				$error =& new WP_Error( 'archive_failure', 'Unable to create backup: ' . $result['error'] );
				
				echo '<div class="bb-error">' . $error->get_error_message() . "</div>\n";
				return $error;
			}
			
			
			$this->_options['backup_status']['processes'][$process]['status'] = 'complete';
			
			return true;
		}
		
		function ajax_importbuddy() {
			if ($_GET['pass'] == '') {
				echo 'ERROR #6612: Missing password.';
			} else {
				$output = file_get_contents( dirname( __FILE__ ) . '/importbuddy.php' );
				$output = preg_replace('/#PASSWORD#/', $_GET['pass'], $output, 1 ); // Only replaces first instance due to last parameter.
				$output = preg_replace('/#VERSION#/', $this->_version, $output, 1 ); // Only replaces first instance due to last parameter.

				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: text/plain; name=importbuddy.php' );
				header( 'Content-Disposition: attachment; filename=importbuddy.php' );
				header( 'Expires: 0' );
				header( 'Content-Length: ' . strlen( $output ) );

				//ob_clean();
				flush();

				//str_replace( readfile( dirname( __FILE__ ) . '/importbuddy.php' ) );

				echo $output;
			}
			die();
		}
		
		function ajax_dirlist() {
			$root = ABSPATH;
			$_POST['dir'] = urldecode($_POST['dir']);
			if( file_exists($root . $_POST['dir']) ) {
				$files = scandir($root . $_POST['dir']);
				natcasesort($files);
				if( count($files) > 2 ) { /* The 2 accounts for . and .. */
					echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
					// All dirs
					foreach( $files as $file ) {
						if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
							echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . " <img src=\"" . $this->_pluginURL . "/images/bullet_delete.png\" style=\"vertical-align: -3px;\" /></a></li>";
						}
					}
					// All files
					/*
					foreach( $files as $file ) {
						if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
							$ext = preg_replace('/^.*\./', '', $file);
							echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
						}
					}
					*/
					echo "</ul>";
				} else {
					echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
					echo "<li><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . 'NONE') . "\"><i>Empty Directory ...</i></a></li>";
					echo '</ul>';
				}
			} else {
				echo 'Unable to read site root.';
			}
			
			die();
		}
		
		
		function ajax_ftptest() {
			$ftp_server = $_POST['server'];
			$ftp_user = $_POST['user'];
			$ftp_pass = $_POST['pass'];
			$ftp_path = $_POST['path'];
			
			// can remove this check once ftps implemented...
			if ( isset( $_POST['type'] ) ) {
				if ( is_array( $_POST['type'] ) ) { // IE nonsense...
					$ftp_type = $_POST['type'][0];
				} else {
					$ftp_type = $_POST['type'];
				}
			} else {
				$ftp_type = 'ftp';
			}
			
			if ( ( $ftp_server == '' ) || ( $ftp_user == '' ) || ( $ftp_pass == '' ) ) {
				die('Missing required input.');
			}
			
			
			if ( $ftp_type == 'ftp' ) {
				$conn_id = ftp_connect( $ftp_server ) or die( 'Unable to connect to FTP (check address).' );
			} elseif ( $ftp_type == 'ftps' ) {
				if ( function_exists( 'ftp_ssl_connect' ) ) {
					$conn_id = ftp_ssl_connect( $ftp_server ) or die('Unable to connect to FTPS  (check address/FTPS support).'); 
					if ( $conn_id === false ) {
						die( 'Destination server does not support FTPS?' );
					}
				} else {
					die( 'Your web server doesnt support FTPS.' );
				}
			}
			
			//echo 'user'.$ftp_user;
			$login_result = @ftp_login($conn_id, $ftp_user, $ftp_pass);
			if ((!$conn_id) || (!$login_result)) {
			   echo 'Unable to login. Bad user/pass.';
			} else {
				$tmp = tmpfile(); // Write tempory text file to stream.
				fwrite($tmp, 'Upload test for BackupBuddy');
				rewind($tmp);
				$upload = @ftp_fput($conn_id, $ftp_path.'/backupbuddy.txt', $tmp, FTP_BINARY);
				fclose($tmp);
				if (!$upload) {
					echo 'Failure uploading. Check path & permissions.';
				} else {
					echo 'Success testing ' . $ftp_type . '!';
					ftp_delete($conn_id, $ftp_path.'/backupbuddy.txt');
				}
			}
			@ftp_close($conn_id);
			
			die();
		}
		
		
		function ajax_awstest() {
			$aws_accesskey = $_POST['aws_accesskey'];
			$aws_secretkey = $_POST['aws_secretkey'];
			$aws_bucket = $_POST['aws_bucket'];
			$aws_directory = $_POST['aws_directory'];
			if ( !empty( $_POST['aws_ssl'] ) ) {
				$aws_ssl = $_POST['aws_ssl'];
			}
			
			if ( empty( $aws_accesskey ) || empty( $aws_secretkey ) || empty( $aws_bucket ) ) {
				die( 'Missing Amazon S3 settings input.' );
			}
			
			require_once(dirname( __FILE__ ).'/lib/s3/s3.php');
			$s3 = new S3( $aws_accesskey, $aws_secretkey);
			
			if ( $this->_options['aws_ssl'] != '1' ) {
				S3::$useSSL = false;
			}
			
			$s3->putBucket( $aws_bucket, S3::ACL_PUBLIC_READ );
			
			if ( $s3->putObject( 'Upload test for BackupBuddy for Amazon S3', $aws_bucket, $aws_directory . '/backupbuddy.txt', S3::ACL_PRIVATE) ) {
				// Success... just delete temp test file now...
			} else {
				die( 'Unable to upload. Check bucket & permissions.' );
			}

			if ( S3::deleteObject( $aws_bucket, $aws_directory . '/backupbuddy.txt' ) ) {
				die( 'Success!' );
			} else {
				die( 'Partial success. Could not delete temp file.' );
			}
			
			die();
		}
		
		// Recursively make directory. If a parent directory is missing, create it until we can create the final deepest dir.
		function mkdir_recursive($pathname) {
			is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname));
			return is_dir($pathname) || mkdir($pathname);
		}		
		
		/**
		 * iThemesBackupBuddy::mail_user()
		 *
		 * Send an email to a specified user, no matter what.
		 *
		 * @param	$user			string / int	Name or uid number of user to email.
		 * @param	$subject		string			Email subject.
		 * @param	$body			string			Email body.
		 *
		 */			
		function mail_user($user, $subject, $body) {
			if ( is_numeric($user) ) {
				$sqlwhere="ID='".$user."'";
			} else { // Already numeric.
				$sqlwhere="user_login='".$user."'";
			}
			global $wpdb;
			
			$query = $wpdb->get_results("SELECT user_email FROM ".$wpdb->prefix."users WHERE ".$sqlwhere." LIMIT 1");
			if ( empty($query) ) {
				echo 'ERROR #45445454543: Unable to find specified user for email.';
				return 0;
			} else {
				// Cannot use wp_mail because it will not let us alter the return address (bug?).
				mail($query[0]->user_email, $subject, $body, 'From: '.$this->_options['email_reply']."\r\n".'Reply-To: '.$this->_options['email_reply']."\r\n");
			}
			unset($query);
		}
		
		
		/**
		 * iThemesBackupBuddy::mail_notice()
		 *
		 * Send an email to the admin of the site with notice information.
		 *
		 * @param	$user			string / int	Name or uid number of user to email.
		 * @param	$subject		string			Email subject.
		 * @param	$body			string			Email body.
		 *
		 */			
		function mail_notice($message) {
			//wp_mail(get_option('admin_email'), "BackupBuddy Status", "An action occurred with BackupBuddy on " . date(DATE_RFC822) . " for the site ". site_url() . ".  The notice is displayed below:\n\n".$message, 'From: '.get_option('admin_email')."\r\n".'Reply-To: '.get_option('admin_email')."\r\n");
			
			$message .= "\r\n\r\n";
			
			$this->load();
			if ( isset( $this->_options['email'] ) && ( $this->_options['email'] != '' ) ) {
				$email = $this->_options['email'];
				
				$message .= 'Settings email: ' . $email;
			} else {
				$email = get_option('admin_email');
				
				$message .= 'Admin email: ' . $email;
			}
			wp_mail( $email, "BackupBuddy Status", "An action occurred with BackupBuddy on " . date(DATE_RFC822) . " for the site ". site_url() . ".  The notice is displayed below:\r\n\r\n".$message, 'From: '.$email."\r\n".'Reply-To: '.get_option('admin_email')."\r\n");
		}
		
		// OPTIONS STORAGE //////////////////////
		
		
		function save() {
			add_option($this->_var, $this->_options, '', 'no'); // 'No' prevents autoload if we wont always need the data loaded.
			update_option($this->_var, $this->_options);
			return true;
		}
		
		
		function load() {
			$this->_options=get_option($this->_var);
			$options = array_merge( $this->_defaults, (array)$this->_options );
			
			if ( $options !== $this->_options ) {
				// Defaults existed that werent already in the options so we need to update their settings to include some new options.
				$this->_options = $options;
				$this->save();
			}
			
			$this->_options['backup_directory'] = WP_CONTENT_DIR . '/uploads/backupbuddy_backups/';
			
			return true;
		}
		
		// ADMIN MENU FUNCTIONS /////////////////
		
		
		/** admin_menu()
		 *
		 * Initialize menu for admin section.
		 *
		 */
		function admin_menu() {
			// Add main menu (default when clicking top of menu)
			add_menu_page('Getting Started', $this->_name, 'administrator', $this->_var, array(&$this, 'view_gettingstarted'), $this->_pluginURL.'/images/pluginbuddy.png');
			// Add sub-menu items (first should match default page above)
			add_submenu_page($this->_var, 'Getting Started with '.$this->_name, 'Getting Started', 'administrator', $this->_var, array(&$this, 'view_gettingstarted'));
			add_submenu_page($this->_var, $this->_name.' Backups', 'Backups', 'administrator', $this->_var.'-backup', array(&$this, 'view_backup'));
			//add_submenu_page($this->_var, $this->_name.' Importing', 'Importing', 'administrator', $this->_var.'-importing', array(&$this, 'view_importing'));
			add_submenu_page($this->_var, $this->_name.' Scheduling', 'Scheduling', 'administrator', $this->_var.'-scheduling', array(&$this, 'view_scheduling'));
			add_submenu_page($this->_var, $this->_name.' Settings', 'Settings', 'administrator', $this->_var.'-settings', array(&$this, 'view_settings'));
		}
		
		
		// MISC DUSTIN FUNCTIONS ////////////////
		
		function rand_string($length = 32, $chars = 'abcdefghijkmnopqrstuvwxyz1234567890') {
			$chars_length = (strlen($chars) - 1);
			$string = $chars{rand(0, $chars_length)};
			for ($i = 1; $i < $length; $i = strlen($string)) {
				$r = $chars{rand(0, $chars_length)};
				if ($r != $string{$i - 1}) $string .=  $r;
			}
			return $string;
		}
		
		
		
		function cron_ftp( $ftp_server, $ftp_user, $ftp_pass, $ftp_path, $file, $ftp_type, $delete_after = false ) {
			$this->load();
			
			//$full_file = $this->_options['backup_directory'] . basename( $file );
			
			$details = '';
			$details .= 'Server: '.$ftp_server."\n";
			$details .= 'User: '.$ftp_user."\n";
			$details .= 'Pass: '.$ftp_pass."\n";
			$details .= 'Remote Path: '.$ftp_path."\n";
			$details .= 'Local File & Path: ' . $file . "\n";
			$details .= 'Filename: ' . basename($file) . "\n";
			$details .= 'FTP Type: ' . $ftp_type . "\n";
			$details .= 'Delete after?: ' . $delete_after . "\n";
			
			$this->log( 'Starting CRON FTP. Details: ' . $details );
			
			if ( $ftp_type == 'ftp' ) {
				if ( function_exists( 'ftp_connect' ) ) {
					$conn_id = ftp_connect( $ftp_server );
					if ( $conn_id === false ) {
						$this->log( 'ERROR: Unable to connect to FTP (check address).', 'error' );
						die( 'Unable to connect to FTP (check address).' );
					} else {
						$this->log( 'Connected to FTP.' );
					}
				} else {
					$this->log( 'Your web server doesnt support FTP in PHP.', 'error' );
					die( 'Your web server doesnt support FTP in PHP.' );
				}
			} elseif ( $ftp_type == 'ftps' ) {
				if ( function_exists( 'ftp_ssl_connect' ) ) {
					$conn_id = ftp_ssl_connect( $ftp_server );
					if ( $conn_id === false ) {
						$this->log( 'Unable to connect to FTPS  (check address/FTPS support).', 'error' );
						die( 'Unable to connect to FTPS  (check address/FTPS support).' );
					} else {
						$this->log( 'Connected to FTPs.' );
					}
				} else {
					$this->log( 'Your web server doesnt support FTPS in PHP.', 'error' );
					die( 'Your web server doesnt support FTPS in PHP.' );
				}
			}
			
			$login_result = @ftp_login( $conn_id, $ftp_user, $ftp_pass );
			if ( $login_result === false ) {
				$this->alert( 'ERROR #9011.  FTP/FTPs login failed on scheduled FTP. Credentials: ' . $ftp_user . ':' . $ftp_pass . '@' . $ftp_server . '.', true, '9011' );
				$this->mail_notice( 'ERROR #9011 ( http://anonym.to/?http://ithemes.com/codex/page/BackupBuddy:_Error_Codes#9011 ).  FTP/FTPs login failed on scheduled FTP. Details: ' . $details );
				die();
			} else {
				$this->log( 'Logged in. Sending backup via FTP/FTPs ...' );
				echo "Logged in. Sending backup via FTP/FTPs ... ";
			}
			
			$upload = ftp_put($conn_id, $ftp_path . '/' . basename($file), $file, FTP_BINARY);
			if ( $upload === false ) {
				$this->alert( 'ERROR #9012.  FTP/FTPs file upload failed. Credentials: ' . $ftp_user . ':' . $ftp_pass . '@' . $ftp_server . '.', true, '9011' );
				$this->mail_notice( 'ERROR #9012 ( http://anonym.to/?http://ithemes.com/codex/page/BackupBuddy:_Error_Codes#9012 ).  FTP/FTPs file upload failed. Check file permissions & disk quota. Details: ' . $details );
			} else {
				$this->log( 'Done uploading backup file to FTP/FTPs.' );
				echo 'Done uploading backup to FTP/FTPs server.<br />';
			}
			ftp_close($conn_id);
			
			if ( $delete_after === true ) {
				$this->log( 'Deleting just uploaded FTP file: ' . $file . '.' );
				unlink( $file );
				$this->log( 'Done deleting ftp file ' . $file . '.' );
			}
		}
		
		
		function cron_aws($aws_accesskey, $aws_secretkey, $aws_bucket, $aws_directory, $file, $delete_after_int = 0) {
			$details = '';
			$details .= "AWS Access Key: ".$aws_accesskey."\n";
			if ($this->_debug) {
				$details .= "AWS Secret Key: ".$aws_secretkey."\n";
			} else {
				$details .= "AWS Secret Key: *hidden*\n";
			}
			$details .= "AWS Bucket: ".$aws_bucket."\n";
			$details .= "AWS Directory: ".$aws_directory."\n";
			$details .= "Local File & Path: ".$this->_options['backup_directory'].'/'.basename($file)."\n";
			$details .= "Filename: ".basename($file)."\n";
			
			$this->log( 'Starting Amazon S3 cron. Details: ' . $details );
			
			require_once(dirname( __FILE__ ).'/lib/s3/s3.php');
			$s3 = new S3( $aws_accesskey, $aws_secretkey);
			
			if ( $this->_options['aws_ssl'] != '1' ) {
				S3::$useSSL = false;
			}
			
			$this->log( 'About to put bucket to Amazon S3 cron.' );
			$s3->putBucket( $aws_bucket, S3::ACL_PUBLIC_READ );
			$this->log( 'About to put object (the file) to Amazon S3 cron.' );
			if ( $s3->putObject( S3::inputFile( $file ), $aws_bucket, $aws_directory . '/' . basename($file), S3::ACL_PRIVATE) ) {
				// success
				$this->log( 'SUCCESS sending to Amazon S3!' );
			} else {
				$this->mail_notice('ERROR #9002! Failed sending file to Amazon S3. Details:' . "\n\n" . $details);
				$this->log( 'FAILURE sending to Amazon S3! Details: ' . $details, 'error' );
			}
			
			if ( $delete_after_int == 1 ) {
				$this->log( 'Deleting backup file after Amazon S3 cron.' );
				unlink( $file );
				$this->log( 'Done deleting backup file after Amazon S3 cron.' );
			}
		}
		
		/**
		 * iThemesBackupBuddy::_set_greedy_script_limits()
		 *
		 * Set up PHP parameters to allow for extended time limits
		 *
		 */
		function _set_greedy_script_limits() {
			// Don't abort script if the client connection is lost/closed
			@ignore_user_abort( true );
			
			// 2 hour execution time limits
			@ini_set( 'default_socket_timeout', 60 * 60 * 2 );
			@set_time_limit( 60 * 60 * 2 );
			
			// Increase the memory limit
			$current_memory_limit = trim( @ini_get( 'memory_limit' ) );
			
			if ( preg_match( '/(\d+)(\w*)/', $current_memory_limit, $matches ) ) {
				$current_memory_limit = $matches[1];
				$unit = $matches[2];
				
				// Up memory limit if currently lower than 256M
				if ( 'g' !== strtolower( $unit ) ) {
					if ( ( $current_memory_limit < 256 ) || ( 'm' !== strtolower( $unit ) ) )
						@ini_set('memory_limit', '256M');
				}
			}
			else {
				// Couldn't determine current limit, set to 256M to be safe
				@ini_set('memory_limit', '256M');
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::_delete_directory()
		 *
		 * Delete a directory and all its contents
		 *
		 */
		function _delete_directory( $directory ) {
			$directory = preg_replace( '|[/\\\\]+$|', '', $directory );
			
			$files = glob( $directory . '/*', GLOB_MARK );
			
			foreach( $files as $file ) {
				if( '/' === substr( $file, -1 ) )
					$this->_delete_directory( $file );
				else
					unlink( $file );
			}
			
			if ( is_dir( $directory ) ) rmdir( $directory );
			
			if ( is_dir( $directory ) )
				return false;
			return true;
		}
		
		
		/////////////////////////////////////////////
		// CHRIS' FORM CREATION FUNCTIONS: //////////
		/////////////////////////////////////////////
		
		
		function _saveSettings() {
		
			$errorCount = 0;
		
			check_admin_referer( $this->_var . '-nonce' );
			
			foreach ( (array) explode( ',', $_POST['used-inputs'] ) as $name ) {
				$is_array = ( preg_match( '/\[\]$/', $name ) ) ? true : false;
				
				$name = str_replace( '[]', '', $name );
				$var_name = preg_replace( '/^' . $this->_var . '-/', '', $name );
				
				if ( $is_array && empty( $_POST[$name] ) )
					$_POST[$name] = array();
				
				if ( isset( $_POST[$name] ) && ! is_array( $_POST[$name] ) )
					$this->_options[$var_name] = stripslashes( $_POST[$name] );
				else if ( isset( $_POST[$name] ) )
					$this->_options[$var_name] = $_POST[$name];
				else
					$this->_options[$var_name] = '';
			}
			
			// Strip protocol prefix in case user enters it.
			if ( isset( $_POST[$this->_var.'-ftp_server'] ) ) {
				$this->_options['ftp_server'] = str_replace( 'http://', '', $this->_options['ftp_server'] );
				$this->_options['ftp_server'] = str_replace( 'ftp://', '', $this->_options['ftp_server'] );
			}
			// Convert excluded directories into format we like
			if ( isset( $_POST[$this->_var.'-exclude_dirs'] ) ) {
			
				if ( strstr( $_POST[$this->_var.'-exclude_dirs'], '/wp-content/' . "\r\n" ) || strstr( $_POST[$this->_var.'-exclude_dirs'], '/wp-content/uploads/' . "\r\n" ) ) {
					$this->_showErrorMessage( 'You may not exclude the /wp-content/ or /wp-content/uploads/ directories as they are needed by BackupBuddy. You may exclude other subdirectories within these however.' );
					$errorCount++;
				} else {
			
					$_POST[$this->_var.'-exclude_dirs'] = explode( "\n", trim( $_POST[$this->_var.'-exclude_dirs'] ) );
					$this->_options['excludes'] = $_POST[$this->_var.'-exclude_dirs'];
			
					unset( $_POST[$this->_var.'-exclude_dirs'] );
				}
			}
			
			
			
			// ERROR CHECKING OF INPUT
			if ( $errorCount < 1 ) {
				if ( $this->save() )
					$this->_showStatusMessage( __( 'Settings updated', $this->_var ) );
				else
					$this->_showErrorMessage( __( 'Error while updating settings', $this->_var ) );
			}
			else {
				$this->_showErrorMessage( 'Your settings have NOT been updated. Please correct any errors listed.' );
			}
		}
		
		function _newForm() {
			$this->_usedInputs = array();
		}
		
		function _addSubmit( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'submit';
			$options['name'] = $var;
			$options['class'] = ( empty( $options['class'] ) ) ? 'button-primary' : $options['class'];
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addButton( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'button';
			$options['name'] = $var;
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addPassBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'password';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'text';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextArea( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'textarea';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addFileUpload( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'file';
			$options['name'] = $var;
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addCheckBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'checkbox';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addMultiCheckBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'checkbox';
			$var = $var . '[]';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addRadio( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'radio';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addDropDown( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array();
			else if ( ! isset( $options['value'] ) || ! is_array( $options['value'] ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'dropdown';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHidden( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'hidden';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHiddenNoSave( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['name'] = $var;
			$this->_addHidden( $var, $options, $override_value );
		}
		
		function _addDefaultHidden( $var ) {
			$options = array();
			$options['value'] = $this->defaults[$var];
			
			$var = "default_option_$var";
			$this->_addHiddenNoSave( $var, $options );
		}
		
		function _addUsedInputs() {
			$options['type'] = 'hidden';
			$options['value'] = implode( ',', $this->_usedInputs );
			$options['name'] = 'used-inputs';
			$this->_addSimpleInput( 'used-inputs', $options, true );
		}
		
		function _addSimpleInput( $var, $options = false, $override_value = false ) {
			if ( empty( $options['type'] ) ) {
				echo "<!-- _addSimpleInput called without a type option set. -->\n";
				return false;
			}
			
			$scrublist['textarea']['value'] = true;
			$scrublist['file']['value'] = true;
			$scrublist['dropdown']['value'] = true;
			$defaults = array();
			$defaults['name'] = $this->_var . '-' . $var;
			$var = str_replace( '[]', '', $var );
			
			if ( 'checkbox' === $options['type'] )
				$defaults['class'] = $var;
			else
				$defaults['id'] = $var;
			
			$options = $this->_merge_defaults( $options, $defaults );
			
			if ( ( false === $override_value ) && isset( $this->_options[$var] ) ) {
				if ( 'checkbox' === $options['type'] ) {
					if ( $this->_options[$var] == $options['value'] )
						$options['checked'] = 'checked';
				}
				elseif ( 'dropdown' !== $options['type'] )
					$options['value'] = $this->_options[$var];
			}
			
			if ( ( preg_match( '/^' . $this->_var . '/', $options['name'] ) ) && ( ! in_array( $options['name'], $this->_usedInputs ) ) )
				$this->_usedInputs[] = $options['name'];
			
			$attributes = '';
			
			if ( false !== $options )
				foreach ( (array) $options as $name => $val )
					if ( ! is_array( $val ) && ( ! isset( $scrublist[$options['type']][$name] ) || ( true !== $scrublist[$options['type']][$name] ) ) )
						if ( ( 'submit' === $options['type'] ) || ( 'button' === $options['type'] ) )
							$attributes .= "$name=\"$val\" ";
						else
							$attributes .= "$name=\"" . htmlspecialchars( $val ) . '" ';
			
			if ( 'textarea' === $options['type'] )
				echo '<textarea ' . $attributes . '>' . $options['value'] . '</textarea>';
			elseif ( 'dropdown' === $options['type'] ) {
				echo "<select  $attributes>\n";
				foreach ( (array) $options['value'] as $val => $name ) {
				
					$selected = ( $this->_options[$var] == $val ) ? ' selected="selected"' : '';
					echo "<option value=\"$val\"$selected>$name</option>\n";
				}
				
				echo "</select>\n";
			}
			else
				echo '<input ' . $attributes . '/>';
		}
		
		function _merge_defaults( $values, $defaults, $force = false ) {
			if ( ! $this->_is_associative_array( $defaults ) ) {
				if ( ! isset( $values ) ) {
					return $defaults;
				}
				if ( false === $force ) {
					return $values;
				}
				if ( isset( $values ) || is_array( $values ) )
					return $values;
				return $defaults;
			}
			
			foreach ( (array) $defaults as $key => $val ) {
				if ( ! isset( $values[$key] ) ) {
					$values[$key] = null;
				}
				$values[$key] = $this->_merge_defaults($values[$key], $val, $force );
			}
			return $values;
		}
		
		function _is_associative_array( &$array ) {
			if ( ! is_array( $array ) || empty( $array ) ) {
				return false;
			}
			$next = 0;
			foreach ( $array as $k => $v ) {
				if ( $k !== $next++ ) {
					return true;
				}
			}
			return false;
		}
		
		// PUBLIC DISPLAY OF MESSAGES ////////////////////////
		
		function _showStatusMessage( $message ) {
			echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';			
		}
		function _showErrorMessage( $message ) {
			echo '<div id="message" class="error"><p><strong>'.$message.'</strong></p></div>';
		}
		
		// SORTING FUNCTION(S) //////////////////////////////////
		
		function _sortGroupsByName( $a, $b ) {
			if ( $this->_options['groups'][$a]['name'] < $this->_options['groups'][$b]['name'] )
				return -1;
			
			return 1;
		}
		
		
		
		/**
		 *	log()
		 *
		 *	Logs to a text file depending on settings.
		 *	0 = none, 1 = errors only, 2 = errors + warnings, 3 = debugging (all kinds of actions)
		 *
		 *	$text	string			Text to log.
		 *	$log_type	string		Valid options: error, warning, all (default so may be omitted).
		 *
		 */
		function log( $text, $log_type = 'all' ) {
			$write = false;
			
			if ( !isset( $this->_options['log_level'] ) ) {
				$this->load();
			}
			
			if ( $this->_options['log_level'] == 0 ) { // No logging.
				return;
			} elseif ( $this->_options['log_level'] == 1 ) { // Errors only.
				if ( $log_type == 'error' ) {
					$write = true;
				}
			} elseif ( $this->_options['log_level'] == 2 ) { // Errors and warnings only.
				if ( ( $log_type == 'error' ) || ( $log_type == 'warning' ) ) {
					$write = true;
				}
			} elseif ( $this->_options['log_level'] == 3 ) { // Log all; Errors, warnings, actions, notes, etc.
				$write = true;
			}
			
			if ( $write === true ) {
				$fh = fopen( WP_CONTENT_DIR . '/uploads/' . $this->_var . '.txt', 'a');
				fwrite( $fh, '[' . date( $this->_timestamp . ' ' . get_option( 'gmt_offset' ), time() + (get_option( 'gmt_offset' )*3600) ) . '-' . $log_type . '] ' . $text . "\n" );
				fclose( $fh );
			}
		}
		
	} // End class
	
	
	
	$iThemesBackupBuddy = new iThemesBackupBuddy(); // Create instance
}
?>
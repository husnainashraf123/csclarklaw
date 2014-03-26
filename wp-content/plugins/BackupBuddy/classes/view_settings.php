<?php
$this->load();

if (!empty($_POST['save'])) {
	$this->_saveSettings();
}

// Load scripts and CSS used on this page.
$this->admin_scripts();

echo '<div class="wrap">';
?>
	<h2><?php echo $this->_name; ?> Settings</h2>
	
	
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#exlude_dirs').fileTree({ root: '/', multiFolder: false, script: '<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_dirlist'; ?>' }, function(file) {
				//alert('file:'+file);
			}, function(directory) {
				if ( ( directory == '/wp-content/' ) || ( directory == '/wp-content/uploads/' ) || ( directory == '/wp-content/uploads/backupbuddy_backups/' ) ) {
					alert( 'You cannot exclude /wp-content/ or /wp-content/uploads/.  However, you may exclude subdirectories within these. The backupbuddy_backups directory is also automatically excluded and cannot be added to exclusion list.' );
				} else {
					jQuery('#exclude_dirs').val( directory + "\n" + jQuery('#exclude_dirs').val() );
				}
				
			});
		});
	</script>
	<style type="text/css">
		/* Core Styles */
		.jqueryFileTree LI.directory { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/directory.png') left top no-repeat; }
		.jqueryFileTree LI.expanded { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/folder_open.png') left top no-repeat; }
		.jqueryFileTree LI.file { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/file.png') left top no-repeat; }
		.jqueryFileTree LI.wait { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/spinner.gif') left top no-repeat; }
		/* File Extensions*/
		.jqueryFileTree LI.ext_3gp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
		.jqueryFileTree LI.ext_afp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_afpa { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_asp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_aspx { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_avi { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
		.jqueryFileTree LI.ext_bat { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/application.png') left top no-repeat; }
		.jqueryFileTree LI.ext_bmp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
		.jqueryFileTree LI.ext_c { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_cfm { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_cgi { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_com { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/application.png') left top no-repeat; }
		.jqueryFileTree LI.ext_cpp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_css { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/css.png') left top no-repeat; }
		.jqueryFileTree LI.ext_doc { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/doc.png') left top no-repeat; }
		.jqueryFileTree LI.ext_exe { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/application.png') left top no-repeat; }
		.jqueryFileTree LI.ext_gif { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
		.jqueryFileTree LI.ext_fla { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/flash.png') left top no-repeat; }
		.jqueryFileTree LI.ext_h { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_htm { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/html.png') left top no-repeat; }
		.jqueryFileTree LI.ext_html { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/html.png') left top no-repeat; }
		.jqueryFileTree LI.ext_jar { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/java.png') left top no-repeat; }
		.jqueryFileTree LI.ext_jpg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
		.jqueryFileTree LI.ext_jpeg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
		.jqueryFileTree LI.ext_js { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/script.png') left top no-repeat; }
		.jqueryFileTree LI.ext_lasso { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_log { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/txt.png') left top no-repeat; }
		.jqueryFileTree LI.ext_m4p { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/music.png') left top no-repeat; }
		.jqueryFileTree LI.ext_mov { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
		.jqueryFileTree LI.ext_mp3 { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/music.png') left top no-repeat; }
		.jqueryFileTree LI.ext_mp4 { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
		.jqueryFileTree LI.ext_mpg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
		.jqueryFileTree LI.ext_mpeg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
		.jqueryFileTree LI.ext_ogg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/music.png') left top no-repeat; }
		.jqueryFileTree LI.ext_pcx { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
		.jqueryFileTree LI.ext_pdf { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/pdf.png') left top no-repeat; }
		.jqueryFileTree LI.ext_php { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/php.png') left top no-repeat; }
		.jqueryFileTree LI.ext_png { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
		.jqueryFileTree LI.ext_ppt { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ppt.png') left top no-repeat; }
		.jqueryFileTree LI.ext_psd { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/psd.png') left top no-repeat; }
		.jqueryFileTree LI.ext_pl { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/script.png') left top no-repeat; }
		.jqueryFileTree LI.ext_py { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/script.png') left top no-repeat; }
		.jqueryFileTree LI.ext_rb { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ruby.png') left top no-repeat; }
		.jqueryFileTree LI.ext_rbx { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ruby.png') left top no-repeat; }
		.jqueryFileTree LI.ext_rhtml { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ruby.png') left top no-repeat; }
		.jqueryFileTree LI.ext_rpm { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/linux.png') left top no-repeat; }
		.jqueryFileTree LI.ext_ruby { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ruby.png') left top no-repeat; }
		.jqueryFileTree LI.ext_sql { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/db.png') left top no-repeat; }
		.jqueryFileTree LI.ext_swf { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/flash.png') left top no-repeat; }
		.jqueryFileTree LI.ext_tif { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
		.jqueryFileTree LI.ext_tiff { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
		.jqueryFileTree LI.ext_txt { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/txt.png') left top no-repeat; }
		.jqueryFileTree LI.ext_vb { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_wav { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/music.png') left top no-repeat; }
		.jqueryFileTree LI.ext_wmv { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
		.jqueryFileTree LI.ext_xls { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/xls.png') left top no-repeat; }
		.jqueryFileTree LI.ext_xml { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
		.jqueryFileTree LI.ext_zip { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/zip.png') left top no-repeat; }
	</style>
	
	
	<?php $this->_usedInputs=array(); ?>
	<form method="post" action="<?php echo $this->_selfLink; ?>-settings">
		<table class="form-table" style="max-width: 900px;">
		

			<tr><td colspan="3"><b>Notifications</b></td></tr>
			<tr>
				<td><label for="email">Notification Email Address <?php $this->tip( '[Example: foo@bar.com] - Email address to sent notifications and optional ZIP files to.' ); ?></label></td>
				<td><?php $this->_addTextBox('email', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['email'] ) ); ?></td>
			</tr>
			<tr>
				<td><label for="email_notify_manual">Email on manual backup completion <?php $this->tip( '[Default: disabled] - Receive an email notification on the completion of manual backups.' ); ?></label></td>
				<td><?php $this->_addCheckBox('email_notify_manual', array( 'value' => 1, 'id' => 'email_notify_manual' ) ); ?> <label for="email_notify_manual">Enable manual notifications</label></td>						
			</tr>
			<tr>
				<td><label for="email_notify_scheduled">Email on scheduled backup completion <?php $this->tip( '[Default: enabled] - Receive an email notification on the completion of scheduled backups. This is the default.' ); ?></label></td>
				<td><?php $this->_addCheckBox('email_notify_scheduled', array( 'value' => 1, 'id' => 'email_notify_scheduled' ) ); ?> <label for="email_notify_scheduled">Enable scheduled notifications</label></td>						
			</tr>
			

			



			


			<tr><td colspan="3"><br /><b>Remote FTP / FTPs (BETA) Backup (optional)</b></td></tr>
			<tr>
				<td><label for="ftp_server">FTP Server Address <?php $this->tip( '[Example: ftp.yoursite.com] - Host / IP address of the FTP server to backup to.' ); ?></label></td>
				<td><?php $this->_addTextBox('ftp_server', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['ftp_server'] ) ); ?></td>
			</tr>
			<tr>
				<td><label for="ftp_user">FTP Username <?php $this->tip( '[Example: foo] - Username to use when connecting to the FTP server.' ); ?></label></td>
				<td><?php $this->_addTextBox('ftp_user', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['ftp_user'] ) ); ?></td>
			</tr>
			<tr>
				<td><label for="ftp_pass">FTP Password <?php $this->tip( '[Example: 1234] - Password to use when connecting to the FTP server.' ); ?></label></td>
				<td><?php $this->_addPassBox('ftp_pass', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['ftp_pass'] ) ); ?></td>
			</tr>
			<tr>
				<td><label for="ftp_path">FTP Upload Path <?php $this->tip( '[Example: /public_html/backups/] - Remote path to place uploaded files into on the destination FTP server. Make sure this path is correct and that the directory already exists.' ); ?></label></td>
				<td><?php $this->_addTextBox('ftp_path', array( 'size' => '45', 'maxlength' => '245', 'value' => $this->_options['ftp_path'] ) ); ?></td>
			</tr>
			<tr>
				<td><label for="ftp_path">FTP Connection Type <?php $this->tip( '[Default: FTP] - Select whether this connection is for FTP or FTPs (FTP over SSL). Note that FTPs is NOT the same as sFTP (FTP over SSH) and is not compatible with that.' ); ?></label></td>
				<td><?php $this->_addDropDown( 'ftp_type', array( 'ftp' => 'FTP', 'ftps' => 'FTPs (SSL)' ) ); ?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input value="Test FTP Settings" class="button-secondary" type="submit" id="ithemes_backupbuddy_ftptest" alt="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_ftptest'; ?>" />
					<span id="ithemes_backupbuddy_ftpresponse"></span>
				</td>
			</tr>

			
			
			
			<tr><td colspan="3"><b>Remote Amazon S3 Backup (optional)</b></td></tr>
			<tr>
				<td><label for="aws_accesskey">AWS Access Key <?php $this->tip( '[Example: BSEGHGSDEUOXSQOPGSBE] - Log in to your Amazon S3 AWS Account and navigate to Account: Access Credentials: Security Credentials.' ); ?></label></td>
				<td><?php $this->_addTextBox('aws_accesskey', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_accesskey'] ) ); ?> <a href="https://aws-portal.amazon.com/gp/aws/developer/account/index.html?ie=UTF8&action=access-key" target="_blank"><small>Get Key</small></a></td>
			</tr>
			<tr>
				<td><label for="aws_secretkey">AWS Secret Key <?php $this->tip( '[Example: GHOIDDWE56SDSAZXMOPR] - Log in to your Amazon S3 AWS Account and navigate to Account: Access Credentials: Security Credentials.' ); ?></label></td>
				<td><?php $this->_addPassBox('aws_secretkey', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_secretkey'] ) ); ?></td>
			</tr>
			<tr>
				<td><label for="aws_bucket">Bucket Name <?php $this->tip( '[Example: wordpress_backups] - This bucket will be created for your automatically if it does not already exist.' ); ?></label></td>
				<td><?php $this->_addTextBox('aws_bucket', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_bucket'] ) ); ?></td>
			</tr>
			<tr>
				<td><label for="aws_directory">Directory Name <?php $this->tip( '[Example: backupbuddy] - Directory name to place the backup in within the bucket.' ); ?></label></td>
				<td><?php $this->_addTextBox('aws_directory', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_directory'] ) ); ?></td>
			</tr>
			<tr>
				<td><label for="aws_ssl">Use SSL Encryption <?php $this->tip( '[Default: enabled] - When enabled, all transfers will be encrypted with SSL encryption. Please note that encryption introduces overhead and may slow down the transfer. If Amazon S3 sends are failing try disabling this feature to speed up the process.  Note that 32-bit servers cannot encrypt transfers of 2GB or larger with SSL, causing large file transfers to fail.' ); ?></label></td>
				<td><?php $this->_addCheckBox('aws_ssl', array( 'size' => '45', 'maxlength' => '45', 'value' => 1 ) ); ?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input value="Test S3 Settings" class="button-secondary" type="submit" id="ithemes_backupbuddy_awstest" alt="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_awstest'; ?>" />
					<span id="ithemes_backupbuddy_awsresponse"></span>
				</td>
			</tr>
			
			
			
			
			
			<tr><td colspan="3"><b>Misc. Options</b></td></tr>
			<tr>
				<td><label for="zip_limit">Maximum number of archived backups <?php $this->tip( '[Example: 10] - Maximum number of archived backups to store. The oldest backup will be removed if this limit is exceeded. Set to zero for no limit.' ); ?></label></td>
				<td><?php $this->_addTextBox('zip_limit', array( 'size' => '5', 'maxlength' => '45', 'value' => $this->_options['zip_limit'] ) ); ?> (0 = no limit)</td>
			</tr>
			<!--
			<tr>
				<td><label for="first_name">Restore / Migrate Password <?php $this->tip( '[Example: 1234] - Enter the password you wish to use when restoring your site or migrating.' ); ?></label></td>
				<td><?php $this->_addPassBox('password', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['password'] ) ); ?></td>
			</tr>
			-->
			<tr>
				<td><label for="compression">Use Zip Compression <?php $this->tip( '[Default: enabled] - ZIP compression decreases file sizes of stored backups. If you are encountering timeouts due to the script running too long, disabling compression may allow the process to complete faster.' ); ?></label></td>
				<td><?php $this->_addCheckBox('compression', array( 'value' => 1, 'id' => 'compression' ) ); ?> <label for="compression">Enable ZIP compression</label></td>
			</tr>
			<tr>
				<td><label for="force_compatibility">Force Compatibility Mode <?php $this->tip( '[Default: disabled] - Under normal circumstances compatibility mode is automatically entered as needed without user intervention. However under some server configurations the native backup system is unavailable but is incorrectly reported as functioning by the server.  Forcing compatibility may fix problems in this situation by bypassing the native backup system check entirely.' ); ?></label></td>
				<td><?php $this->_addCheckBox('force_compatibility', array( 'value' => 1, 'id' => 'force_compatibility' ) ); ?> <label for="force_compatibility">Enable forced compatibility for backups (slow)</label></td>
			</tr>
			<tr>
				<td><label for="backup_nonwp_tables">Backup non-WordPress database tables <?php $this->tip( '[Default: disabled] - Checking this box will result in ALL tables and data in the database being backed up, even database content not related to WordPress, its content, or plugins.  This is useful if you have other software installed on your hosting that stores data in your database.  This may also be useful for WordPress MU.' ); ?></label></td>
				<td><?php $this->_addCheckBox('backup_nonwp_tables', array( 'value' => 1, 'id' => 'backup_nonwp_tables' ) ); ?> <label for="backup_nonwp_tables">Enable backing up non-WordPress database data</label></td>						
			</tr>
			<tr>
				<td><label for="integrity_check">Perform integrity check on backup files <?php $this->tip( '[Default: enabled] - By default each backup file is checked for integrity and completion the first time it is viewed on the Backup page.  On some server configurations this may cause memory problems as the integrity checking process is intensive.  If you are experiencing out of memory errors on the Backup file listing, you can uncheck this to disable this feature.' ); ?></label></td>
				<td><?php $this->_addCheckBox('integrity_check', array( 'value' => 1, 'id' => 'integrity_check' ) ); ?> <label for="integrity_check">Enable integrity checking</label></td>						
			</tr>
			
			<tr>
				<td><label for="log_level">Logging Level <?php $this->tip( '[Default: Errors Only] - This option controls how much activity is logged for records or debugging. Logs saved in ' . WP_CONTENT_DIR . '/uploads/backupbuddy.txt' ); ?></label></td>
				<td>
					<?php $this->_addDropDown( 'log_level', array( '0' => 'None', '1' => 'Errors Only', '2' => 'Errors & Warnings', '3' => 'Everything (debug mode)' ) ); ?>
				</td>
			</tr>
			
			
			<tr><td colspan="3"><br /><b>Exclude Directories from Backup</b> <?php $this->tip( 'If you would like specific directories excluded from your backups you may add them to the exclusion list.  This feature is only available in non-compatibility mode.' ); ?></td></tr>
			<tr>
				<td>
					Click directories to navigate, <img src="<?php echo $this->_pluginURL; ?>/images/bullet_delete.png" style="vertical-align: -3px;" />to exclude directory. <?php $this->tip( 'Click on a directory normally to navigate directories. Hold the control (Ctrl) button on your keyboard while clicking on a directory with your mouse to select a directory to exclude from being included in backups.  The selected directory will be added to the list to the right at the top of the list. /wp-content/ and /wp-content/uploads/ cannot be excluded.' ); ?><br />
					<div id="exlude_dirs" class="jQueryOuterTree">
					</div>
					<small>Only available if your server doesn't require compatibility mode. <?php $this->tip( 'If you receive notifications that your server is entering compatibility mode or that native zip functionality is unavailable then this feature will not be available due to technical limitations of the compatibility mode.  Ask your host to correct the problems causing compatibility mode or move to a new server.' ); ?></small>
				</td>
				<td>
					Excluded directories (path relative to root <?php $this->tip( 'List paths relative to root to be excluded from backups.  You may use the directory selector to the left to easily exclude directories by ctrl+clicking them.  Paths are relative to root. Ex: /wp-content/uploads/junk/' ); ?>)<br />
					<?php
					if ( is_array( $this->_options['excludes'] ) ) {
						$exclude_dirs = implode( "\n", $this->_options['excludes'] );
					} else {
						$exclude_dirs = '';
					}
					$this->_addTextArea('exclude_dirs', array( 'wrap' => 'off', 'rows' => '4', 'cols' => '35', 'maxlength' => '9000', 'value' => $exclude_dirs ), true );
					?>
					<br /><small>List one path per line. Remove a line to remove exclusion.</small>
				</td>
			</tr>
			
			<tr>
				<td colspan="2" align="center">
					<p class="submit"><?php $this->_addSubmit( 'save', 'Save Settings' ); ?></p>
				</td>
			</tr>
									
			
		</table><br />
		
		<?php $this->_addUsedInputs(); ?>
		<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
	</form>
<?php
echo '</div>';
?>
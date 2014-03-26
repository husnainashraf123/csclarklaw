<?php
$this->load();
			
if (!empty($_POST['add_schedule'])) {
	$this->add_schedule();
} elseif ( !empty( $_POST['delete_schedules'] ) ) {
	$this->delete_schedule();
}

// Load scripts and CSS used on this page.
$this->admin_scripts();

echo '<div class="wrap">';
echo '<h2>Scheduling</h2>';

$class = 'alternate';
?>
<br />
<form id="posts-filter" enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>-scheduling">
<?php $this->_addSubmit( 'delete_schedules', array( 'value' => 'Delete', 'class' => 'button-secondary delete' ) ); ?>

<br /><br />
<table class="widefat">
	<thead>
		<tr class="thead">
			<th scope="col" class="check-column"><input type="checkbox" class="check-all-entries" /></th>
			<th>Name</th>
			<th>Type</th>
			<th>Interval</th>
			<th>First Run</th>
			<th>Send File</th>
		</tr>
	</thead>
	<tfoot>
		<tr class="thead">
			<th scope="col" class="check-column"><input type="checkbox" class="check-all-entries" /></th>
			<th>Name</th>
			<th>Type</th>
			<th>Interval</th>
			<th>First Run</th>
			<th>Send File</th>
		</tr>
	</tfoot>
	<tbody>

<?php

$found_schedule = false;
foreach ( (array) get_option('cron') as $id => $group ) {
	if (is_array($group) && array_key_exists('ithemes-backupbuddy-cron_schedule', $group)) { //( is_array($group['ithemes-backupbuddy-cron_schedule']) ) {
		foreach ( (array) $group['ithemes-backupbuddy-cron_schedule'] as $id2 => $group2 ) {
			$found_schedule = true;
			$scheduled = $this->_options['schedules'][$group2['args'][0]]; // This schedule item.
			?>
			<tr class="entry-row <?php echo $class; ?>" id="entry-<?php echo $id.'-'.$id2; ?>">
				<th scope="row" class="check-column">
					<input type="checkbox" name="schedules[]" class="entries" value="<?php echo $id.'-'.$id2; ?>" />
				</th>
				<td><?php echo $scheduled['name']; ?></td><td><?php echo $this->pretty_schedule_type($scheduled['type']); ?></td><td><?php echo $this->pretty_schedule_interval($scheduled['interval']); ?></td><td><?php echo $this->pretty_schedule_firstrun($scheduled['first_run'] + (get_option( 'gmt_offset' )*3600) ); ?></td>
				<td>
				<?php
					$got_ftp = false;
					$got_email = false;
					$got_aws = false;
					
					// DEPRECATED at v1.1.38
												if ( array_key_exists('send_ftp', $scheduled) && ($scheduled['send_ftp'] == '1') ) {
													echo 'FTP';
													$got_ftp = true;
												}
												if ( array_key_exists('send_email', $scheduled) && ($scheduled['send_email'] == '1') ) {
													if ($got_ftp == true) { echo ', '; }
													echo 'Email';
													$got_email = true;
												}
					// END DEPRECATED

					if ( array_key_exists('remote_send', $scheduled) ) {
						if ( $scheduled['remote_send'] == 'ftp' ) {
							echo 'FTP';
							$got_ftp = true;
						} elseif ( $scheduled['remote_send'] == 'aws' ) {
							echo 'Amazon S3';
							$got_aws = true;
						} elseif ( $scheduled['remote_send'] == 'email' ) {
							echo 'Email';
							$got_email = true;
						} else {
							echo 'None';
						}
					}
					
					
					if ( ($got_email != true) && ($got_ftp != true) && ($got_aws != true) ) {
						echo '<i>None</i>';
					}
					
					/*
					echo '<pre>';
					print_r( $scheduled );
					echo '</pre>';
					*/
				?>
				</td>
			</tr>
			<?php
			$class = ( $class === '' ) ? 'alternate' : '';
		}
	}
}
if ($found_schedule != true) {
	echo '<td><td colspan="6" align="center"><br /><i>There are currently no scheduled backups.</i><br /><br /></td></tr>';
}
echo '</table><br />';
$this->_addSubmit( 'delete_schedules', array( 'value' => 'Delete', 'class' => 'button-secondary delete' ) );
echo '</form>';
?>

<br />
<h2>Add New Scheduled Backup</h2>
	<img src="<?php echo $this->_pluginURL.'/images/bullet_error.png'; ?>" style="vertical-align: -3px;" /> IMPORTANT: For scheduled events to be occur someone (or you) must visit this site on or after the scheduled time.<br /><br />
	<form enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>-scheduling">
		<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
		<table class="form-table">
			<tr><th scope="row"><label for="name">Name for Backup Schedule <?php $this->tip( 'This is a name for your reference.' ); ?></label></th>
				<td><?php $this->_addTextBox( 'name' ); ?></td>
			</tr>
			<tr><th scope="row"><label for="type">Backup Type <?php $this->tip( 'Full backups contain all files (except exclusions) and your database. Database only backups contain a dump of your mySQL database contents only; no WordPress files or media. Database backups are typically much smaller and faster to perform.' ); ?></label></th>
				<td><?php $this->_addDropDown( 'type', array( 'db' => 'Database Only (default)', 'full' => 'Full Backup (Files + Database)' ) ); ?></td>
			</tr>
			<tr><th scope="row"><label for="interval">Backup Interval <?php $this->tip( 'Time period between backups.' ); ?></label></th>
				<td><?php $this->_addDropDown( 'interval', array( 'monthly' => 'Monthly', 'twicemonthly' => 'Twice Monthly', 'weekly' => 'Weekly', 'daily' => 'Daily', 'hourly' => 'Hourly' ) ); ?></td>
			</tr>
			<tr><th scope="row"><label for="name">Date/Time of First Run <?php $this->tip( 'IMPORTANT: For scheduled events to be occur someone (or you) must visit this site on or after the scheduled time. If no one visits your site for a long period of time some backup events may not be triggered.' ); ?></label></th>
				<td>						
					<input type="text" name="ithemes_datetime" id="ithemes_datetime" value="<?php echo date('m/d/Y h:i a', time() + ( ( get_option( 'gmt_offset' ) * 3600 ) +86400)); ?>"> Currently <code><?php echo date( 'm/d/Y h:i a ' . get_option( 'gmt_offset' ), time() + ( get_option( 'gmt_offset' ) * 3600 ) ); ?> UTC</code> based on <a href="<?php echo admin_url( 'options-general.php' ); ?>">WordPress settings</a>.
					<br />
					<small>mm/dd/yyyy hh:mm [am/pm]</small>
				</td>
			</tr>
			<tr><th scope="row"><label for="send_ftp">Send file after backup <?php $this->tip( 'Configure email and FTP server information in the Settings section. Note that large files may not have time to send based on your server timeout settings. Most email servers will reject files over 10MB.  Most full backups WILL exceed this and not be sendable via email.' ); ?></label></th>
				<td>
					<?php $this->_addDropDown( 'remote_send', array( 'none' => 'None', 'ftp' => 'FTP / FTPs', 'aws' => 'Amazon S3', 'email' => 'Email **' ) ); ?>
					<div id="ithemes-backupbuddy-deleteafter" style="display: none; background-color: #EAF2FA; border: 1px solid #E3E3E3; width: 250px; padding: 10px; margin: 5px; margin-left: 22px;">
						<?php $this->_addCheckBox( 'delete_after', '1' ); ?> Delete local backup file after sending.<br />
					</div>
				</td>
			</tr>
			

			
		</table>

		<p class="submit">
			<?php $this->_addSubmit( 'add_schedule', 'Add Schedule' ); ?>
		</p>
		
	</form>

<?php
echo '</div>';
?>
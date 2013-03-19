<?php

    	//  If the user does not have the required permissions...
    if (!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    	// Get GroupDocs plug-in options from database.
    $userId     = get_option('userId');
    $privateKey = get_option('privateKey');

    //  If data was posted to the page...
    if( isset($_POST['grpdocs_signature_submit_hidden']) && $_POST['grpdocs_signature_submit_hidden'] == 1) {
        //  Save the API key to the Options table.
		$userId     = trim($_POST['userId']);
		$privateKey = trim($_POST['privateKey']);

		update_option( 'userId', $userId);
		update_option( 'privateKey', $privateKey);

        // Display an 'updated' message.
		?>
		<div class="updated"><p><strong><?php _e('Settings saved!', 'menu-test' ); ?></strong></p></div>
		<?php
    }
?>

<div>

	<h2>GroupDocs Options</h2>

	<form name="form" method="post" action="">

		<input type="hidden" name="grpdocs_signature_submit_hidden" value="1">

		<h3>API Settings</h3>
		<table>
		<tr><td>User Id:</td>
		<td><input type="text" name="userId" value="<?php echo $userId; ?>"></td></tr>
		<tr><td>Private Key:</td>
		<td><input type="text" name="privateKey" value="<?php echo $privateKey; ?>"></td></tr>
		</table>

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>

	</form>

</div>

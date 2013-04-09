<?php

/*
Plugin Name: GroupDocs Signature Embedder
Plugin URI: http://www.groupdocs.com/
Description: Lets you embed PPT, PPTX, XLS, XLSX, DOC, DOCX, PDF and many other formats from your GroupDocs acount in a web page using the GroupDocs Embedded Viewer (no Flash or PDF browser plug-ins required).
Author: GroupDocs Team <support@groupdocs.com>
Author URI: http://www.groupdocs.com/
Version: 1.0.0
License: GPLv2
*/

include_once('grpdocs-functions.php');


function grpdocs_signature_getdocument($atts) {

	extract(shortcode_atts(array(
		'form' => '',
		'width' => '',
		'height' => '',
		'page' => 0,
		'version' => 1,
	), $atts));

	$guid = grpdocs_signature_getGuid(urlencode($form));

	$code = '<iframe src="https://apps.groupdocs.com/signature2/forms/SignEmbed/'. $guid .'?referer=wordpress/1.0" frameborder="0" width="'. $width .'" height="'. $height .'"></iframe>';

	$code = str_replace("%W%", $width, $code);
	$code = str_replace("%H%", $height, $code);
	$code = str_replace("%P%", $page, $code);
	$code = str_replace("%V%", $version, $code);
	$code = str_replace("%A%", '', $code);
	$code = str_replace("%B%", $download, $code);
	$code = str_replace("%GUID%", $guid, $code);

	return $code;

}

//activate shortcode
add_shortcode('grpdocssignature', 'grpdocs_signature_getdocument');


// editor integration

// add quicktag
add_action( 'admin_print_scripts', 'grpdocs_signature_admin_print_scripts' );

// add tinymce button
add_action('admin_init','grpdocs_signature_mce_addbuttons');

// add an option page
add_action('admin_menu', 'grpdocs_signature_option_page');
function grpdocs_signature_option_page() {
	global $grpdocs_signature_settings_page;

	$grpdocs_signature_settings_page = add_options_page('GroupDocs Signature', 'GroupDocs Signature', 'manage_options', basename(__FILE__), 'grpdocs_signature_options');

}
function grpdocs_signature_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(t('An error occurred.'));
	if (! user_can_access_admin_page()) wp_die('You do not have sufficient permissions to access this page');

	require(ABSPATH. 'wp-content/plugins/groupdocs-signature/options.php');
}

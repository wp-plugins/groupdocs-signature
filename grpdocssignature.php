<?php

/*
Plugin Name: GroupDocs Signature Embedder
Plugin URI: http://www.groupdocs.com/
Description: With this plugin you and your partners/clients will be able to sign documents online, without the need of printing, scanning, faxing and mailing them. The plugin allows you to embed electronic documents into web-pages on your WordPress website and then invite users to sign the documents right there.
Author: GroupDocs Team <support@groupdocs.com>
Author URI: http://www.groupdocs.com/
Version: 1.2.5
License: GPLv2
*/

include_once('grpdocs-functions.php');


function grpdocs_signature_getdocument($atts) {

	extract(shortcode_atts(array(
		'form' => '',
		'file' => '',
        'width' => '',
		'height' => '',
		'page' => 0,
		'version' => 1,
	), $atts));

    $signer = '';

   if(class_exists('GroupDocsRequestSigner')){
       $signer = new GroupDocsRequestSigner(get_option('signature_userId'));
   }else{
       include_once(dirname(__FILE__) . '/lib/groupdocs-php/APIClient.php');
       include_once(dirname(__FILE__) . '/lib/groupdocs-php/StorageApi.php');
       include_once(dirname(__FILE__) . '/lib/groupdocs-php/GroupDocsRequestSigner.php');
       include_once(dirname(__FILE__) . '/lib/groupdocs-php/FileStream.php');
       $signer = new GroupDocsRequestSigner(get_option('signature_userId'));
   }

    if ($form !==''){

        $no_iframe = 'If you can see this text, your browser does not support iframes. Please enable iframe support in your browser or use the latest version of any popular web browser such as Mozilla Firefox or Google Chrome. For more help, please check our documentation Wiki: <a href="http://groupdocs.com/docs/display/signature/GroupDocs+Signature+Integration+with+3rd+Party+Platforms">http://groupdocs.com/docs/display/signature/GroupDocs+Signature+Integration+with+3rd+Party+Platforms</a>';

        $url = 'https://apps.groupdocs.com/signature2/forms/SignEmbed/'. $form;

    }
    if($file !== '') {
        $no_iframe = 'If you can see this text, your browser does not support iframes. Please enable iframe support in your browser or use the latest version of any popular web browser such as Mozilla Firefox or Google Chrome. For more help, please check our documentation Wiki: <a href="http://groupdocs.com/docs/display/signature/GroupDocs+Signature+Integration+with+3rd+Party+Platforms">http://groupdocs.com/docs/display/signature/GroupDocs+Signature+Integration+with+3rd+Party+Platforms</a>';

        $url = 'https://apps.groupdocs.com/signature2/signembed/'. $file;
    }

    $code_url = $signer->signUrl($url);

    $code =  "<iframe src='{$code_url}&referer=wordpress-signature/1.2.5' frameborder='0' width='{$width}' height='{$height}'>{$no_iframe}</iframe>";

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

register_uninstall_hook( __FILE__, 'groupdocs_signature_deactivate' );

function groupdocs_signature_deactivate()
{
	delete_option('signature_userId');
	delete_option('signature_privateKey');	

}
function grpdocs_signature_option_page() {
	global $grpdocs_signature_settings_page;

	$grpdocs_signature_settings_page = add_options_page('GroupDocs Signature', 'GroupDocs Signature', 'manage_options', basename(__FILE__), 'grpdocs_signature_options');

}
function grpdocs_signature_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(t('An error occurred.'));
	if (! user_can_access_admin_page()) wp_die('You do not have sufficient permissions to access this page');

	require(ABSPATH. 'wp-content/plugins/groupdocs-signature/options.php');
}

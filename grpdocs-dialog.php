<?php
// access wp functions externally
require_once('bootstrap.php');

ini_set('display_errors', '0');
error_reporting(E_ALL | E_STRICT);
if (file_exists('../../../wp-includes/js/tinymce/tiny_mce_popup.js')){
    $tiny = '<script type="text/javascript" src="../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>';
}else{
    $tiny = '<script type="text/javascript" src="js/tiny_mce_popup.js"></script>';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>GroupDocs Signature</title>
    <script type="text/javascript" src="js/jquery-1.5.min.js"></script>
    <?php echo $tiny ?>
	<script type="text/javascript" src="js/grpdocs-dialog.js"></script>

	<link href="css/grpdocs-dialog.css" type="text/css" rel="stylesheet" />

</head>
<body>
<form id='form' onsubmit="" method="post" action="" enctype="multipart/form-data">

    <table>
        <tr>
            <td align="right" class="gray dwl_gray"><strong>Client Id</strong><br /></td>
            <td valign="top"><input name="userId" type="text" class="opt dwl" id="userId" style="width:200px;" value="<?php echo get_option('signature_userId'); ?>" /><br/>
                <span id="uri-note"></span></td>
        </tr>
        <tr>
            <td align="right" class="gray dwl_gray"><strong>API Key</strong><br /></td>
            <td valign="top"><input name="privateKey" type="text" class="opt dwl" id="privateKey" style="width:200px;" value="<?php echo get_option('signature_privateKey'); ?>" /><br/>
                <span id="uri-note"></span></td>
        </tr>
        <tr>
            <td align="right" class="gray dwl_gray"><strong>Height</strong></td>
            <td valign="top" style="width:200px;"><input name="height" type="text" class="opt dwl" id="height" size="6"
                                                         style="text-align:right" value="700"/>px
            </td>
        </tr>
        <tr>
            <td align="right" class="gray dwl_gray"><strong>Width</strong></td>
            <td valign="top"><input name="width" type="text" class="opt dwl" id="width" size="6"
                                    style="text-align:right" value="600"/>px
            </td>
        </tr>
        <tr>
            <div id=first_name>
            <td align="right" class="gray dwl_gray"><strong>First Name</strong></td>
            <td valign="top"><input name="first_name" type="text" class="opt dwl" id="first_name" size="15"
                                    style="text-align:left" />
            </td>
            </div>
        </tr>
        <tr>
            <td align="right" class="gray dwl_gray"><strong>Last Name</strong></td>
            <td valign="top"><input name="last_name" type="text" class="opt dwl" id="last_name" size="15"
                                    style="text-align:left" />
            </td>
        </tr>
        <tr>
            <td align="right" class="gray dwl_gray"><strong>Email</strong></td>
            <td valign="top"><input name="email" type="text" class="opt dwl" id="email" size="15"
                                    style="text-align:left" />
            </td>
        </tr>
    </table>


<div class="section">

<ul class="tabs">
    <li class="current">Upload &amp; Embed</li>
    <li >Paste GUID</li>
</ul>

<div class="box visible">
    <strong>Upload Document</strong><br />
    <input name="file" type="file" class="opt dwl" id="file" style="width:200px;" /><br/>
    <span id="uri-note"></span>
</div>

<div class="box">
    <strong>Form Id (GUID)</strong><br />
    <input name="url" type="text" class="opt dwl" id="url" style="width:200px;" /><br/>
    <span id="uri-note"></span>
</div>
</div><!-- .section -->

<fieldset>
   <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
    <td colspan="2">
    <br />
    Shortcode Preview
    <textarea name="shortcode" cols="72" rows="2" id="shortcode"></textarea>
    </td>
	</tr>
   </table>
</fieldset>
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="insert" name="insert" value="Insert" onclick="GrpdocsInsertDialog.insert();" />

		</div>

		<div style="float: right">
			<input type="button"  id="cancel" name="cancel" value="Cancel" onclick="tinyMCEPopup.close();"/>
		</div>
	</div>
</form>

</body>
</html>
<?php
if (!empty($_POST) && !empty($_FILES)) {

    if (!empty($_POST['email']) and !empty($_POST['first_name']) and !empty($_POST['last_name'])) {


        $file = $_FILES['file'];
        $error_text = true; // Show text or number
        define("UPLOAD_ERR_EMPTY", 5);
        if ($file['size'] == 0 && $file['error'] == 0) {
            $file['error'] = 5;
        }
        $upload_errors = array(
            UPLOAD_ERR_OK => "No errors.",
            UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize.",
            UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE.",
            UPLOAD_ERR_PARTIAL => "Partial upload.",
            UPLOAD_ERR_NO_FILE => "No file.",
            UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
            UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
            UPLOAD_ERR_EXTENSION => "File upload stopped by extension.",
            UPLOAD_ERR_EMPTY => "File is empty." // add this to avoid an offset
        );
// error: report what PHP says went wrong
        $err = ($error_text) ? $upload_errors[$file['error']] : $file['error'];

        if ($file['error'] !== 0) {
            echo "<div class='red'>" . $err . "</div>";
        } else {


            include_once(dirname(__FILE__) . '/lib/groupdocs-php/APIClient.php');
            include_once(dirname(__FILE__) . '/lib/groupdocs-php/StorageApi.php');
            include_once(dirname(__FILE__) . '/lib/groupdocs-php/SignatureApi.php');
            include_once(dirname(__FILE__) . '/lib/groupdocs-php/GroupDocsRequestSigner.php');
            include_once(dirname(__FILE__) . '/lib/groupdocs-php/FileStream.php');

            $uploads_dir = dirname(__FILE__);
            $email = strip_tags(trim($_POST['email']));
            $signName = strip_tags(trim($_POST['first_name']));
            $lastName = strip_tags(trim($_POST['last_name']));
            $tmp_name = $_FILES["file"]["tmp_name"];
            $name = $_FILES["file"]["name"];
            $user_id = strip_tags(trim($_POST['userId']));

            $fs = FileStream::fromFile($tmp_name);


            $signer = new GroupDocsRequestSigner(strip_tags(trim($_POST['privateKey'])));
            $apiClient = new APIClient($signer);
            $api = new StorageApi($apiClient);

            $result = $api->Upload($user_id, $name, 'uploaded', null, null, $fs);

            $guid = $result->result->guid;
            $signature = new SignatureApi($apiClient);
            //Create envilope using user id and entered by user name
            $envelop = $signature->CreateSignatureEnvelope($user_id, $name);
            //Add uploaded document to envelope
            $addDocument = $signature->AddSignatureEnvelopeDocument($user_id, $envelop->result->envelope->id, $guid, null, true);
            //Get role list for curent user
            $recipient = $signature->GetRolesList($user_id);
            //Get id of role which can sign
            for($i = 0; $i < count($recipient->result->roles); $i++) {
                if($recipient->result->roles[$i]->name == "Signer") {
                    $roleId = $recipient->result->roles[$i]->id;
                }
            }

            //Add recipient to envelope
            $addRecipient = $signature->AddSignatureEnvelopeRecipient($user_id, $envelop->result->envelope->id, $email, $signName, $lastName, $roleId, null);
            //Get recipient id
            $getRecipient = $signature->GetSignatureEnvelopeRecipients($user_id, $envelop->result->envelope->id);
            $recipientId = $getRecipient->result->recipients[0]->id;

            $getDocuments = $signature->GetSignatureEnvelopeDocuments($user_id, $envelop->result->envelope->id);
            $signFieldEnvelopSettings = new SignatureEnvelopeFieldSettingsInfo();
            $signFieldEnvelopSettings->locationX = "0.15";
            $signFieldEnvelopSettings->locationY = "0.73";
            $signFieldEnvelopSettings->locationWidth = "150";
            $signFieldEnvelopSettings->locationHeight = "50";
            $signFieldEnvelopSettings->name = $name;
            $signFieldEnvelopSettings->forceNewField = true;
            $signFieldEnvelopSettings->page = "1";
            $addSignField = $signature->AddSignatureEnvelopeField($user_id, $envelop->result->envelope->id, $getDocuments->result->documents[0]->documentId, $recipientId, "0545e589fb3e27c9bb7a1f59d0e3fcb9", $signFieldEnvelopSettings);
            $callBack = ''; //$GLOBALS['base_url'] . "/groupdocs_signature/signature_callback";
            //Send envelop with callback url
            $send = $signature->SignatureEnvelopeSend($user_id, $envelop->result->envelope->id, $callBack); //Url for callback

            $result = array();
            //Make iframe
            $result = $envelop->result->envelope->id .'/'. $recipientId;
            $height = (int) $_POST['height'];
            $width = (int) $_POST['width'];
            echo "<script>
			tinyMCEPopup.editor.execCommand('mceInsertContent', false, '[grpdocssignature file=\"" . @$result . "\" height=\"{$height}\" width=\"{$width}\"]');
			tinyMCEPopup.close();</script>";
            die;
        }
    }else{
        echo 'Please fill first name, last name and email';
    }
}


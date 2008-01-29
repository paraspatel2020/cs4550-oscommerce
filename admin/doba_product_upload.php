<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
/*
  $Id: doba_configure.php,v 1.0 2008/01/17 14:40:27 hpdl Exp $

  Doba     Product Sourcing Simplified
  http://www.doba.com

  Copyright (c) 2008 Doba

  Released under the GNU General Public License
*/

require('includes/application_top.php');

$msg = '';
if (isset($_POST['submit'])) {
	require_once('doba/DobaProductFile.php');
	require_once('doba/DobaInteraction.php');
	
	$filename = isset($_FILES['product_file']['name']) ? $_FILES['product_file']['name'] : '';
	$tmpfile = isset($_FILES['product_file']['tmp_name']) ? $_FILES['product_file']['tmp_name'] : '';
	$file_type = isset($_POST['file_type']) ? $_POST['file_type'] : '';
	$file_action = isset($_POST['file_action']) ? $_POST['file_action'] : '';

	$objDobaProducts = DobaProductFile::processFile($tmpfile, $file_type);
	if (is_a($objDobaProducts, 'DobaProducts') && DobaInteraction::loadDobaProductsIntoDB( $objDobaProducts, $file_action)) {
		$msg = $filename.UPLOAD_SUCCESS_MSG;
	} else {
		$MSG = UPLOAD_FAILURE_MSG;
	}
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
        	<?php
				if ($msg != '') {
					echo '<p><em>'.$msg.'</em></p>';
				}
			?>
			<form enctype="multipart/form-data" action="doba_product_upload.php" method="POST">
				<table>
					<tr>
						<th><?php echo FORM_PRODUCT_FILE; ?>:</th>
						<td colspan="2"><input name="product_file" type="file"></td>
					</tr>
					<tr>
						<th><?php echo FORM_FILE_TYPE; ?>:</th>
						<td colspan="2"><select name="file_type">
							<option value="tab"><?php echo FORM_FILE_TYPE_TAB; ?></option>
							<option value="csv"><?php echo FORM_FILE_TYPE_CSV; ?></option>
						</select></td>
					</tr>
					<tr>
						<th rowspan="2"><?php echo FORM_FILE_ACTION; ?>:</th>
						<td>
							<label for="file_action_update">
								<input type="radio" name="file_action" id="file_action_update" value="update" checked=true> 
								<?php echo FORM_FILE_ACTION_UPDATE; ?>
							</label>
						</td>
						<td style="padding: 2px 0 0 20px; font-size: 10px; color: #999; text-align: bottom;">
							<label for="file_action_update">
								(<?php echo FORM_FILE_ACTION_UPDATE_EXPLAIN; ?>)
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<label for="file_action_replace">
								<input type="radio" name="file_action" id="file_action_replace" value="replace"> 
								<?php echo FORM_FILE_ACTION_REPLACE; ?>
							</label>
						</td>
						<td style="padding: 2px 0 0 20px; font-size: 10px; color: #999; text-align: bottom;">
							<label for="file_action_replace">
								(<?php echo FORM_FILE_ACTION_REPLACE_EXPLAIN; ?>)
							</label>
						</td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<td colspan="2"><input type="submit" name="submit" value="<?php echo FORM_SUBMIT_FILE; ?>"></td>
					</tr>
				</table>
			</form>
		
		</td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

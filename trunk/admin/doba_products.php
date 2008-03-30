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

require_once('doba/DobaProductFile.php');
require_once('doba/DobaProducts.php');
require_once('doba/DobaLog.php');
include_once('doba/DobaApi.php');

$msg = '';
if (isset($_POST['submit'])) {
	require_once('doba/DobaInteraction.php');
	
	$filename = isset($_FILES['product_file']['name']) ? $_FILES['product_file']['name'] : '';
	$tmpfile = isset($_FILES['product_file']['tmp_name']) ? $_FILES['product_file']['tmp_name'] : '';
	$file_type = isset($_POST['file_type']) ? $_POST['file_type'] : '';
	
	$objDobaProducts = DobaProductFile::processFile($tmpfile, $file_type);
	if (is_a($objDobaProducts, 'DobaProducts') && DobaInteraction::loadDobaProductsIntoDB( $objDobaProducts )) {
		DobaLog::logProductUpload($objDobaProducts, $filename);
		$msg = $filename.UPLOAD_SUCCESS_MSG;
	} else {
		$MSG = UPLOAD_FAILURE_MSG;
	}
} else if (isset($_GET['api'])) {
	if ($_GET['api'] == 'success') {
		$msg = 'Your products were successfully loaded from Doba';
	} else {
		$msg = 'There were problems loading your products from Doba.  Please try again later.';
	}
} 

$upload_history = DobaLog::getLogHistorySummary('product');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/doba.css">
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
			<form enctype="multipart/form-data" action="doba_products.php" method="POST">
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
						<th>&nbsp;</th>
						<td colspan="2"><input type="submit" name="submit" value="<?php echo FORM_SUBMIT_FILE; ?>"></td>
					</tr>
				</table>
			</form>
			
			<?php if (DOBA_API_ENABLED === true) { ?>
				<form action="<?php echo FILENAME_DOBA_API_PRODUCTS; ?>">
					<p><input type="submit" value="<?php echo LINK_PRODUCT_API; ?>"></p>
				</form>
			<?php } ?>
			
			<p><strong><?php echo UPLOAD_HISTORY; ?></strong> (last 10)</p>
			<table class="data">
				<thead>
					<tr>
						<th><?php echo TABLE_HEAD_DATE; ?></th>
						<th><?php echo TABLE_HEAD_XFER_METHOD; ?></th>
						<th><?php echo TABLE_HEAD_FILENAME; ?></th>
						<th><?php echo TABLE_HEAD_API_RESPONSE; ?></th>
						<th><?php echo TABLE_HEAD_PRODUCT_CNT; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
						if (count($upload_history) > 0) { 
							foreach ($upload_history as $uh) { 
					?>
							<tr>
								<td><?php echo $uh['ymdt']; ?></td>
								<td><?php echo $uh['xfer_method']; ?></td>
								<td><?php echo $uh['filename']; ?></td>
								<td><?php echo $uh['api_response']; ?></td>
								<td><?php echo $uh['cnt']; ?></td>							
							</tr>
					<?php 
							} 
						} else {
					?>
						<tr>
							<td colspan="5" style="color: #999;">
								<?php echo MSG_NO_HISTORY; ?>
							</td>
						</tr>
					<?php		
						}
					?>					
				</tbody>
			</table>
		
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

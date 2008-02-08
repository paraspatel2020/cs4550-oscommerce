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
require_once('doba/DobaLog.php');

$download_history = DobaLog::getLogHistorySummary('order');
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
        	
			<p><strong><?php echo UPLOAD_HISTORY; ?></strong></p>
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
								<?php echo MSG_NO_PRODUCT_HISTORY; ?>
							</td>
						</tr>
					<?php		
						}
					?>					
				</tbody>
			</table>
			
			<p><strong><?php echo DOWNLOAD_HISTORY; ?></strong></p>
			<table class="data">
				<thead>
					<tr>
						<th><?php echo TABLE_HEAD_DATE; ?></th>
						<th><?php echo TABLE_HEAD_XFER_METHOD; ?></th>
						<th><?php echo TABLE_HEAD_FILENAME; ?></th>
						<th><?php echo TABLE_HEAD_API_RESPONSE; ?></th>
						<th><?php echo TABLE_HEAD_ORDER_CNT; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
						if (count($download_history) > 0) { 
							foreach ($download_history as $dh) { 
					?>
							<tr>
								<td><?php echo $dh['ymdt']; ?></td>
								<td><?php echo $dh['xfer_method']; ?></td>
								<td><?php echo $dh['filename']; ?></td>
								<td><?php echo $dh['api_response']; ?></td>
								<td><?php echo $dh['cnt']; ?></td>							
							</tr>
					<?php 
							} 
						} else {
					?>
						<tr>
							<td colspan="5" style="color: #999;">
								<?php echo MSG_NO_ORDER_HISTORY; ?>
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

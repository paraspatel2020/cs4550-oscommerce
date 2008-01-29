<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
/*
  $Id: doba_configure.php,v 1.0 2008/01/17 14:40:27 hpdl Exp $

  Doba     Product Sourcing Simplified
  http://www.doba.com

  Copyright (c) 2008 Doba

  Released under the GNU General Public License
*/

$downloaded = true;
if (isset($_POST['ordergroup'])) {
	require_once('doba/DobaOrders.php');
	require_once('doba/DobaOrderFile.php');
	
	$ordergroup = trim($_POST['ordergroup']);
	$objDobaOrders = new DobaOrders();
	if ($objDobaOrders->loadOrders($ordergroup)) {
		$filename = 'orders_'.date('YmdHis').'.tab';
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; '
       				.'filename="'.$filename.'"');
		$objDobaOrderFile = new DobaOrderFile();
		$objDobaOrderFile->processData($objDobaOrders);
		exit();
	}
	$downloaded = false;
}

require('includes/application_top.php');

$msg = '';
if (!$downloaded) {
	$msg = FILE_DOWNLOAD_ERROR;
}

$cnt_new_orders = 0;
$cnt_all_orders = 0;
$cnt_submitted_orders = 0;
$cnt_unsubmitted_orders = 0;

$download_history = array();
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
			<form action="doba_order_download.php" method="post">
				<table>
					<tr>
						<td>
							<label for="ordergroup_new">
								<input type="radio" name="ordergroup" id="ordergroup_new" value="new" checked="true">
							</label>
						</td>
						<td>
							<label for="ordergroup_new"><?php echo ORDERGROUP_NEW; ?></label>
						</td>
						<td>(<?php echo $cnt_new_orders.' '.AVAILABLE; ?>)</td>
					</tr>
					<tr>
						<td>
							<label for="ordergroup_all">
								<input type="radio" name="ordergroup" id="ordergroup_all" value="all">
							</label>
						</td>
						<td>
							<label for="ordergroup_all"><?php echo ORDERGROUP_ALL; ?></label>
						</td>
						<td>(<?php echo $cnt_all_orders.' '.AVAILABLE; ?>)</td>
					</tr>
					<tr>
						<td>
							<label for="ordergroup_submitted">
								<input type="radio" name="ordergroup" id="ordergroup_submitted" value="submitted">
							</label>
						</td>
						<td>
							<label for="ordergroup_submitted"><?php echo ORDERGROUP_SUBMITTED; ?></label>
						</td>
						<td>(<?php echo $cnt_submitted_orders.' '.AVAILABLE; ?>)</td>
					</tr>
					<tr>
						<td>
							<label for="ordergroup_unsubmitted">
								<input type="radio" name="ordergroup" id="ordergroup_unsubmitted" value="unsubmitted">
							</label>
						</td>
						<td>
							<label for="ordergroup_unsubmitted"><?php echo ORDERGROUP_UNSUBMITTED; ?></label>
						</td>
						<td>(<?php echo $cnt_unsubmitted_orders.' '.AVAILABLE; ?>)</td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" name="submit" value="<?php echo FORM_DOWNLOAD_FILE; ?>"></td>
					</tr>					
				</table>
			</form>
		
			<p><strong><?php echo DOWNLOAD_HISTORY; ?></strong></p>
			<table>
				<thead>
					<tr>
						<th><?php echo TABLE_HEAD_DATE; ?></th>
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
								<td><?php echo $dh['cnt']; ?></td>							
							</tr>
					<?php 
							} 
						} else {
					?>
						<tr>
							<td colspan="2" style="color: #999;">
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
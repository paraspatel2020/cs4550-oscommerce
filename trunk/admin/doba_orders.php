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

$downloaded = true;
require_once('doba/DobaOrders.php');
require_once('doba/DobaInteraction.php');	
require_once('doba/DobaLog.php');
include_once('doba/DobaApi.php');

$api = new DobaApi();
$msg = '';
		
if (isset($_POST['ordergroup'])) {
	require_once('doba/DobaOrderFile.php');
	
	$ordergroup = trim($_POST['ordergroup']);
	$objDobaOrders = DobaInteraction::loadOrders($ordergroup);
	$now = time();
	
	if ($_POST['act'] == 'download') {
		if (is_a($objDobaOrders, 'DobaOrders')) {
			$filename = 'orders_'.date('YmdHis', $now).'.tab';
			// make this header replace previous headers
			header('Content-Type: application/octet-stream', true);
			header('Content-Disposition: attachment; '
	       				.'filename="'.$filename.'"');
			$objDobaOrderFile = new DobaOrderFile();
			$objDobaOrderFile->processData($objDobaOrders);
			
			if ($ordergroup == 'new') {
				DobaLog::logOrderDownload($objDobaOrders, $filename, $now);
			}
			
			exit();
		}
		$downloaded = false;
	} else if ($_POST['act'] == 'api_send') {
		include_once('doba/DobaOrdersAPI.php');
		
		$action = DOBA_API_ACTION_CREATEORDER;
		$o_data = DobaOrdersAPI::prepOrdersForSubmission($objDobaOrders);
		$success_ids = $failure_ids = array();
		
		foreach ($o_data as $id => $data) {
			if ($api->compileRequestXml($action, $data) && $api->sendRequest()) {
				$cor = DobaOrdersAPI::parseCreateOrderResponse($api->getResponseXml());
				if ($cor === 'success') {
					$success_ids[] = $id;
				} else {
					$failure_ids[] = $id;
				}				
			} else if ($api->hasErrors()) {
				$failure_ids[] = $id;
			} else {	
				$failure_ids[] = $id;
			}
		}

		if (count($success_ids) > 0) {
			DobaLog::logOrderApiSend($objDobaOrders, 'submitted order(s): ' . implode(', ', $success_ids), null, $success_ids);
		}
		if (count($success_ids) > 0) {
			if (strlen($msg) > 0) {
				$msg .= '<br>';
			}
			if (count($success_ids) == 1) {
				$msg .= 'Order ' . implode(', ', $success_ids) . ' was successfully submitted to Doba.';
			} else {
				$msg .= 'Orders ' . implode(', ', $success_ids) . ' were successfully submitted to Doba.';
			}
			 
		}
		if (count($failure_ids) > 0) {
			if (strlen($msg) > 0) {
				$msg .= '<br>';
			}
			$msg .= (count($failure_ids) == 1) ? 'Order ' : 'Orders ';
			$msg .= implode(', ', $failure_ids) . ' could not be submitted to Doba for one or more of the following reasons:<br>
					- it has already been submitted<br>
					- the product doesn\'t exist on Doba<br>
					- the product does not have enough stock to complete the order<br>
					Please log into Doba to verify.'; 
		}
	} else {
		// this was an unrecognized action
	}
}

if (!$downloaded) {
	$msg = FILE_DOWNLOAD_ERROR;
}

$order_cnt_new = DobaInteraction::getOrderCount('new');
$order_cnt_all = DobaInteraction::getOrderCount('all');
$order_cnt_submitted = DobaInteraction::getOrderCount('submitted');
$order_cnt_unsubmitted = DobaInteraction::getOrderCount('unsubmitted');

$download_history = DobaLog::getLogHistorySummary('order');
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
			<form action="doba_orders.php" method="post">
				<input type="hidden" name="act" id="act" value="download">
				<table class="data">
					<tr <?php if ($order_cnt_new == 0) { echo ' class="disabled"'; } ?>>
						<td>
							<label for="ordergroup_new">
								<input type="radio" name="ordergroup" id="ordergroup_new" value="new" checked="true" <?php if ($order_cnt_new == 0) { echo ' disabled'; } ?>>
							</label>
						</td>
						<td>
							<label for="ordergroup_new"><?php echo ORDERGROUP_NEW; ?></label>
						</td>
						<td>(<?php echo $order_cnt_new.' '.AVAILABLE; ?>)</td>
					</tr>
					<tr <?php if ($order_cnt_all == 0) { echo ' class="disabled"'; } ?>>
						<td>
							<label for="ordergroup_all">
								<input type="radio" name="ordergroup" id="ordergroup_all" value="all" <?php if ($order_cnt_all == 0) { echo ' disabled'; } ?>>
							</label>
						</td>
						<td>
							<label for="ordergroup_all"><?php echo ORDERGROUP_ALL; ?></label>
						</td>
						<td>(<?php echo $order_cnt_all.' '.AVAILABLE; ?>)</td>
					</tr>
					<tr <?php if ($order_cnt_submitted == 0) { echo ' class="disabled"'; } ?>>
						<td>
							<label for="ordergroup_submitted">
								<input type="radio" name="ordergroup" id="ordergroup_submitted" value="submitted" <?php if ($order_cnt_submitted == 0) { echo ' disabled'; } ?>>
							</label>
						</td>
						<td>
							<label for="ordergroup_submitted"><?php echo ORDERGROUP_SUBMITTED; ?></label>
						</td>
						<td>(<?php echo $order_cnt_submitted.' '.AVAILABLE; ?>)</td>
					</tr>
					<tr <?php if ($order_cnt_unsubmitted == 0) { echo ' class="disabled"'; } ?>>
						<td>
							<label for="ordergroup_unsubmitted">
								<input type="radio" name="ordergroup" id="ordergroup_unsubmitted" value="unsubmitted" <?php if ($order_cnt_unsubmitted == 0) { echo ' disabled'; } ?>>
							</label>
						</td>
						<td>
							<label for="ordergroup_unsubmitted"><?php echo ORDERGROUP_UNSUBMITTED; ?></label>
						</td>
						<td>(<?php echo $order_cnt_unsubmitted.' '.AVAILABLE; ?>)</td>
					</tr>
					<tr class="noborder">
						<td colspan="3">
								<input type="submit" name="submit" value="<?php echo FORM_DOWNLOAD_FILE; ?>" onClick="document.getElementById('act').value='download';">
								<?php if (DOBA_API_ENABLED === true) { ?>
									<input type="submit" name="submit" value="<?php echo FORM_SEND_FILE_TO_DOBA; ?>" onClick="document.getElementById('act').value='api_send';" style="margin-left: 10px;">
								<?php } ?>
							</td>
					</tr>					
				</table>
			</form>
		
			<p><strong><?php echo ORDER_HISTORY; ?></strong> (last 10)</p>
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

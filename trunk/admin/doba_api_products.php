<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
/*
  $Id: doba_configure.php,v 1.0 2008/01/17 14:40:27 hpdl Exp $

  Doba     Inventory on Demand
  http://www.doba.com

  Copyright (c) 2008 Doba

  Released under the GNU General Public License
*/

require('includes/application_top.php');

require_once('doba/DobaProducts.php');
include_once('doba/DobaApi.php');
include_once('doba/DobaProductAPI.php');

$msg = '';
$api = new DobaApi();

if (isset($_POST['submit'])) {
	require_once('doba/DobaInteraction.php');
	
	$watchlist_id = isset($_POST['watchlist_id']) ? intval($_POST['watchlist_id']) : 0;
	if ($watchlist_id > 0) {
		$action = DOBA_API_ACTION_GETPRODUCTDETAIL;
		$data = array('watchlist_id' => $watchlist_id);

		if ($api->compileRequestXml($action, $data) && $api->sendRequest()) {
			$objDobaProducts = DobaProductAPI::parseProductDetails($api->getResponseXml());
			
			if (is_a($objDobaProducts, 'DobaProducts') && DobaInteraction::loadDobaProductsIntoDB( $objDobaProducts )) {
				require_once('doba/DobaLog.php');
				DobaLog::logProductApiLoad($objDobaProducts, 'success');
				
				header('Location: ' . FILENAME_DOBA_PRODUCTS . '?api=success');
				exit();
			} else {
				$msg = 'Your request could not be completed at this time.  Please try again later.';
			}
		} else if ($api->hasErrors()) {
			$msg = 'Your request could not be completed due to the following errors:<br>- ' . implode('<br>- ', $api->getErrors());
		} else {
			$msg = 'Your request could not be completed at this time.  Please try again later.';
		}
	} else {
		$msg = 'Please select a watchlist to use.';
	}
}

$watchlists = array();
if (DOBA_API_ENABLED !== true) {
	$msg = 'You must set up your Doba API authentication before you can use this tool.  Find out more by sending an email to support@doba.com.';
} else {
	// load up the watchlists set up under the current api auth
	$action = DOBA_API_ACTION_GETWATCHLISTS;
	$data = array();

	if ($api->compileRequestXml($action, $data) && $api->sendRequest()) {
		$res = DobaProductAPI::parseWatchlistResponse($api->getResponseXml());
		$watchlists = $res['data'];
		if ($res['response'] !== 'success') {
			$msg = ERROR_WATCHLIST_REQUEST_NOT_SUCCESS; 
		}
	} else if ($api->hasErrors()) {
		$msg = 'Your request could not be completed due to the following errors:<br>- ' . implode('<br>- ', $api->getErrors());
	} else {	
		$msg = ERROR_WATCHLIST_REQUEST_NOT_SUCCESS;
	}
}
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
			<?php if (DOBA_API_ENABLED === true) { ?>
				<?php if (count($watchlists) > 0) { ?>
					<p><strong><?php echo TITLE_SELECT_WATCHLIST; ?></strong></p>
					
					<form action="doba_api_products.php" method="POST">
						<table class="data">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th><?php echo TABLE_HEAD_NAME; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($watchlists as $w) { ?>
									<tr>
										<td><input type="radio" id="watchlist_id_<?php echo $w['watchlist_id']; ?>" name="watchlist_id" value="<?php echo $w['watchlist_id']; ?>"></td>
										<td><label for="watchlist_id_<?php echo $w['watchlist_id']; ?>"><?php echo $w['name']; ?></label></td>
									</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr class="noborder">
									<td colspan="2"><input type="submit" name="submit" value="<?php echo FORM_GET_PRODUCTS; ?>"></td>
								</tr>
							</tfoot>
						</table>
					</form>
				<?php } else { ?>
					<p>You do not have any watch lists available.  Please log into <a href="<?php echo $api->site_url; ?>" target="doba" style="font-size: 12px; font-weight: normal; text-decoration: underline;">Doba</a> and create one or more.</p>
				<?php  } ?>
			<?php } ?>
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

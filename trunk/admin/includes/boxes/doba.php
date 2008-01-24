<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- doba //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_DOBA,
                     'link'  => tep_href_link(FILENAME_DOBA_CONFIG, 'selected_box=doba_config'));

  if ($selected_box == 'doba_config') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_DOBA_CONFIG, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_DOBA_CONFIG . '</a><br>' . 
								   '<a href="' . tep_href_link(FILENAME_DOBA_PRODUCT_UPLOAD, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_DOBA_UPLOAD_PRODUCT_DATA . '</a><br>' . 
								   '<a href="' . tep_href_link(FILENAME_DOBA_ORDER_DOWNLOAD, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_DOBA_DOWNLOAD_ORDER_DATA . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- doba_eof //-->

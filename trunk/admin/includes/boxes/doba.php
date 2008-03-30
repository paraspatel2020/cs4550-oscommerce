<?php
/*
  $Id: $

  Doba     Inventory on Demand
  http://www.doba.com

  Copyright (c) 2008 Doba

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
                     'link'  => tep_href_link(FILENAME_DOBA_STATS, 'selected_box=doba_config'));

  if ($selected_box == 'doba_config') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_DOBA_STATS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_DOBA_STATS . '</a><br>' . 
								   '<a href="' . tep_href_link(FILENAME_DOBA_PRODUCTS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_DOBA_PRODUCTS . '</a><br>' . 
								   '<a href="' . tep_href_link(FILENAME_DOBA_ORDERS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_DOBA_ORDERS . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- doba_eof //-->

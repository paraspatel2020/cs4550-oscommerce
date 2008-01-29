<?php
/*
  $Id: $

  Doba     Product Sourcing Simplified
  http://www.doba.com

  Copyright (c) 2008 Doba

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Doba Product Upload');

define('FORM_PRODUCT_FILE', 'Product File');
define('FORM_FILE_TYPE', 'File Type');
define('FORM_FILE_TYPE_TAB', 'tab-delimited');
define('FORM_FILE_TYPE_CSV', 'comma-separated');
define('FORM_FILE_ACTION', 'File Action');
define('FORM_FILE_ACTION_UPDATE', 'update');
define('FORM_FILE_ACTION_REPLACE', 'replace');
define('FORM_SUBMIT_FILE', 'Process File');

define('FORM_FILE_ACTION_UPDATE_EXPLAIN', 'add the supplied products to the current database and update any duplicates');
define('FORM_FILE_ACTION_REPLACE_EXPLAIN', 'remove all products currently in the database and insert the supplied products');

define('UPLOAD_SUCCESS_MSG', ' was properly uploaded and processed.  Please view your products in the catalog.');
define('UPLOAD_FAILURE_MSG', 'There were problems uploading and processing your file.  Please check your file and ty again.');
?>
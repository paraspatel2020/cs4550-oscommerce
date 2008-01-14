<?php
  define('HTTP_SERVER', 'http://oscommerce.tefo.ath.cx');
  define('HTTP_CATALOG_SERVER', 'http://oscommerce.tefo.ath.cx');
  define('HTTPS_CATALOG_SERVER', 'http://oscommerce.tefo.ath.cx');
  define('ENABLE_SSL_CATALOG', 'false');
  define('DIR_FS_DOCUMENT_ROOT', '/var/www/html/oscommerce/');
  define('DIR_WS_ADMIN', '/admin/');
  define('DIR_FS_ADMIN', '/var/www/html/oscommerce/admin/');
  define('DIR_WS_CATALOG', '/');
  define('DIR_FS_CATALOG', '/var/www/html/oscommerce/');
  define('DIR_WS_IMAGES', 'images/');
  define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
  define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/');
  define('DIR_WS_INCLUDES', 'includes/');
  define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');
  define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
  define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
  define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
  define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');
  define('DIR_WS_CATALOG_LANGUAGES', DIR_WS_CATALOG . 'includes/languages/');
  define('DIR_FS_CATALOG_LANGUAGES', DIR_FS_CATALOG . 'includes/languages/');
  define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/');
  define('DIR_FS_CATALOG_MODULES', DIR_FS_CATALOG . 'includes/modules/');
  define('DIR_FS_BACKUP', DIR_FS_ADMIN . 'backups/');

  define('DB_SERVER', 'localhost');
  define('DB_SERVER_USERNAME', 'oscommerce');
  define('DB_SERVER_PASSWORD', '1234567890');
  define('DB_DATABASE', 'oscommerce');
  define('USE_PCONNECT', 'false');
  define('STORE_SESSIONS', 'mysql');
?>
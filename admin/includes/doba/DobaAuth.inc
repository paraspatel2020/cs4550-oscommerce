<?php
// Set this to "true" if you have access to the Doba API and plan to use it, false if otherwise
define('DOBA_API_ENABLED', true);

// Set this to your Doba API username.  It is not used if "DOBA_API_ENABLED" is false.
define('DOBA_USERNAME', 'code_bandits');

// Set this to your Doba API password.  It is not in use if "DOBA_API_ENABLED" is false.
define('DOBA_PASSWORD', 'password');

// Set this to your Doba Retailer ID.  It is not in use if "DOBA_API_ENABLED" is false.
define('DOBA_RETAILER_ID', '1223414');

// Set this to "true" if you wish to only run test transactions against the testing sandbox.
// Set this to "false" if this is a "live" implementation.
define('DOBA_TEST_MODE_ENABLED', true);

// Set this to the default category name you wish to use.
// This will be used if a category has not been specified by a custom field in your watchlist or data file.
// The expected header for the custom field is: OSC_CATEGORY
define('PRODUCT_DEFAULT_CATEGORY_NAME', 'New Products');
?>
<?php
// Set this to "true" if you have access to the Doba API and plan to use it, false if otherwise
define('DOBA_API_ENABLED', false);

// Set this to your Doba API username.  It is not used if "DOBA_API_ENABLED" is false.
define('DOBA_USERNAME', '');

// Set this to your Doba API password.  It is not in use if "DOBA_API_ENABLED" is false.
define('DOBA_PASSWORD', '');

// Set this to "true" if you wish to only run test transactions against the testing sandbox.
// Set this to "false" if this is a "live" implementation.
define('DOBA_TEST_MODE_ENABLED', true);
?>
<?php

/*
|--------------------------------------------------------------------------
| Firewall Settings
|--------------------------------------------------------------------------
*/

// Turn firewall ON or OFF
define('FIREWALL_ON', TRUE);
// Turn whitelisted IP's ON or OFF
define('ENABLE_WHITELISTED_IP', FALSE);
// List of allowed IP addresses in an array
define('ALLOWED_IP_ADDR', ['::1']);
// Turn whitelisted URI characters ON or OFF
define('ENABLE_WHITELISTED_URI', FALSE);
// Set URI whitelisted Characters
define('URI_WHITELISTED', '\w\/\.\-\_\?\=\&\%');
// Turn blacklisted post characters ON or OFF
define('ENABLE_BLACKLISTED_POST', FALSE);
// Blacklisted $_POST and post body characters.
define('POST_BLACKLISTED', '\<\>\;\#\\$');
<?php

/*
|--------------------------------------------------------------------------
| Firewall Settings
|--------------------------------------------------------------------------
*/

// Turn firewall ON or OFF as TRUE or FALSE.
define('FIREWALL_ON', TRUE);
// List of allowed IP addresses in an array
define('ALLOWED_IP_ADDR', ['::1']);
// Set URI Whitelisted Characters
define('URI_WHITELISTED', '\w\/\.\-\_\?\=\&');
// Blacklisted $_POST and post body characters.
define('POST_BLACKLISTED', '\<\>\;\#\\$');
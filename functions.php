<?php

/*
|--------------------------------------------------------------------------
| Helper Functions Library
|--------------------------------------------------------------------------
*/

/**
 * Web Application Firewall
 */

function rja_firewall()
{

	if (FIREWALL_ON == TRUE) {

		// Allow only access from whitelisted IP addresses
		// if (! in_array($_SERVER['REMOTE_ADDR'], ALLOWED_IP_ADDR)) {

		// 	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
		// 	exit('<p>You are not allowed to access the application using your IP address.</p>');

		// }

		// Allow only URI_WHITELISTED characters on the Request URI.
		if (! empty(URI_WHITELISTED)) {

			$regex_array = str_replace('w', 'alphanumeric', URI_WHITELISTED);
			$regex_array = explode('\\', $regex_array);

			if (isset($_SERVER['REQUEST_URI']) && preg_match('/[^' . URI_WHITELISTED . ']/i', $_SERVER['REQUEST_URI'])) {

				header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
				exit('<p>The URI should only contain alphanumeric and GET request characters:</p><p><ul>' . implode('<li>', $regex_array) . '</ul></p>');
				
			}

		}

		// Deny POST_BLACKLISTED characters in $_POST and post body.
		if (! empty(POST_BLACKLISTED)) {

			$regex_array = explode('\\', POST_BLACKLISTED);

			if (isset($_POST) && preg_match('/[' . POST_BLACKLISTED . ']/i', implode('/', $_POST)) ) {

				header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
				exit('<p>Submitted data should NOT contain the following characters:</p><p><ul>' . implode('<li>', $regex_array) . '</ul></p>');
				
			}

			$post_data = file_get_contents('php://input');

			if (isset($post_data) && preg_match('/[' . POST_BLACKLISTED . ']/i', $post_data) ) {

				header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
				exit('<p>Submitted data should NOT contain the following characters:</p><p><ul>' . implode('<li>', $regex_array) . '</ul></p>');
				
			}

		}

	}

}

/**
 * Encrypt data using AES-CBC-HMAC
 *
 * @param string $plaintext - Plaintext to be encrypted
 */

function rja_encrypt($plaintext)
{

	$cipher = 'aes-256-cbc';
	$key = hash('sha256', AUTH_KEY);
	$key_hmac = hash( 'sha256', md5(AUTH_KEY) );
	$iv = random_bytes(16);

	$ciphertext = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);
	$hash = hash_hmac('sha256', $ciphertext, $key_hmac);

	return base64_encode($ciphertext . '::' . $hash . '::' . $iv);

}

/**
 * Decrypt data using AES-CBC-HMAC
 *
 * @param string $encypted - base64_encoded ciphertext, hash and iv
 */

function rja_decrypt($encrypted)
{

	if (! isset($encrypted) || empty($encrypted)) { return ''; }
	
	$cipher = 'aes-256-cbc';
	$key = hash('sha256', AUTH_KEY);
	$key_hmac = hash( 'sha256', md5(AUTH_KEY) );

	list($ciphertext, $hash, $iv) = explode( '::', base64_decode($encrypted) );
	$digest = hash_hmac('sha256', $ciphertext, $key_hmac);

	if ( hash_equals($hash, $digest) ) {
		return openssl_decrypt($ciphertext, $cipher, $key, 0, $iv);
	} else {
		return 'Ciphertext has been compromised.';
	}

}
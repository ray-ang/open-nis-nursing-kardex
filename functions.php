<?php

/**
 * Helper Functions Library
 */

// Encrypt data using AES-CBC-HMAC
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

// Decrypt data using AES-CBC-HMAC
function rja_decrypt($encrypted)
{

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
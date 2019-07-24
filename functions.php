<?php

/**
 * Helper Functions Library
 */

// Encrypt data using AES-CBC-HMAC
function rja_encrypt($plaintext)
{

	$cipher = 'aes-256-cbc';
	$key = hash('sha256', PASS_CODE);
	$iv = random_bytes(16);

	$ciphertext = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);
	$hash = hash_hmac('sha256', $ciphertext, $key);

	return base64_encode($ciphertext . '::' . $hash . '::' . $iv);

}

// Decrypt data using AES-CBC-HMAC
function rja_decrypt($encrypted)
{

	$cipher = 'aes-256-cbc';
	$key = hash('sha256', PASS_CODE);

	list($ciphertext, $hash, $iv) = explode( '::', base64_decode($encrypted) );
	$calc_hash = hash_hmac('sha256', $ciphertext, $key);

	if ( hash_equals($hash, $calc_hash) ) {
		return openssl_decrypt($ciphertext, $cipher, $key, 0, $iv);
	} else {
		return 'Ciphertext has been compromised.';
	}

}
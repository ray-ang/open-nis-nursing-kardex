<?php

/**
 * Core Functions
 */

// Encrypt data
function rja_encrypt($plaintext)
{

	$cipher = 'aes-256-cbc';
	$key = hash('sha256', PASS_CODE);
	$iv = random_bytes(16);

	$ciphertext = openssl_encrypt( $plaintext, $cipher, $key, 0, $iv );
	return base64_encode($ciphertext . '::' . $iv);

}

// Decrypt data
function rja_decrypt($encrypted)
{

	$cipher = 'aes-256-cbc';
	$key = hash('sha256', PASS_CODE);

	list($ciphertext, $iv) = explode('::', base64_decode($encrypted));
	return openssl_decrypt($ciphertext, $cipher, $key, 0, $iv);

}
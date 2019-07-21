<?php

/**
 * Core Functions
 */

// Encrypt data
function rja_encrypt($data)
{

	$cipher = 'aes-256-cbc';
	$key = PASS_CODE;
	$iv = random_bytes(16);

	$encrypted = openssl_encrypt( $data, $cipher, $key, 0, $iv );
	return base64_encode($encrypted . '::' . $iv);

}

// Decrypt data
function rja_decrypt($data)
{

	$cipher = 'aes-256-cbc';
	$key = PASS_CODE;

	list($encrypted, $iv) = explode('::', base64_decode($data));
	return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);

}
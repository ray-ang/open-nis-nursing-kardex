<?php
/*
Plugin Name: ClassicPress Encryption
Plugin URI: https://github.com/ClassicPress-research/encryption-functions/
Description: Encryption and decryption features for regulatory compliance.
Author: Raymund John Ang
Author URI: https://open-nis.org/
Text Domain: open-nis
Version: 0.9
*/

// PASS_PHRASE will be used to derive the encryption and HMAC keys.
define( 'PASS_PHRASE', AUTH_KEY );

/**
 * Encrypt data using AES-CBC-HMAC
 *
 * @param string $plaintext - Plaintext to be encrypted
 */

function encrypt( $plaintext )
{

    // Cipher method to CBC with 256-bit key
    $cipher = 'aes-256-cbc';
    // Derive encryption key
    $key = hash( 'sha256', PASS_PHRASE . md5(PASS_PHRASE) );
    // Derive HMAC key
    $key_hmac = hash( 'sha256', md5(PASS_PHRASE) );
    // Initialization vector
    $iv = random_bytes(16);

    $ciphertext = openssl_encrypt( $plaintext, $cipher, $key, 0, $iv );
    $hash = hash_hmac( 'sha256', $ciphertext, $key_hmac );

    return base64_encode( $ciphertext . '::' . $hash . '::' . $iv );

}

/**
 * Decrypt data using AES-CBC-HMAC
 *
 * @param string $encypted - base64_encoded ciphertext, hash and iv
 */

function decrypt( $encrypted )
{

    // Return empty if $encrypted is not set or empty.
    if ( ! isset($encrypted) || empty($encrypted) ) { return ''; }
	
    // Cipher method to CBC with 256-bit key
    $cipher = 'aes-256-cbc';
    // Derive encryption key
    $key = hash( 'sha256', PASS_PHRASE . md5(PASS_PHRASE) );
    // Derive HMAC key
    $key_hmac = hash( 'sha256', md5(PASS_PHRASE) );

    list( $ciphertext, $hash, $iv ) = explode( '::', base64_decode($encrypted) );
    $digest = hash_hmac( 'sha256', $ciphertext, $key_hmac );

    // HMAC authentication
    if ( hash_equals($hash, $digest) ) {
        return openssl_decrypt( $ciphertext, $cipher, $key, 0, $iv );
        }
    else {
        return 'Please verify authenticity of ciphertext.';
    }

}
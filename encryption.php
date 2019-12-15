<?php
/*
Plugin Name: ClassicPress Encryption
Plugin URI: https://github.com/ClassicPress-research/encryption-functions/
Description: Encryption and decryption features for regulatory compliance.
Author: Raymund John Ang
Author URI: https://open-nis.org/
Text Domain: classicpress-encryption
Version: 0.9
*/

/** Show warning if CP_PASS_PHRASE is not defined */
add_filter( 'the_content', 'check_pass_phrase' );
function check_pass_phrase() {

    if ( ! defined( 'CP_PASS_PHRASE' ) ) {
        exit ('<strong>Warning: </strong>Constant CP_PASS_PHRASE is not defined in wp-config.php.');
    }

}

/**
 * Encrypt Data
 *
 * @param string $plaintext - Plaintext to be encrypted
 */

function cp_encrypt( $plaintext ) {

    // Default encryption function
    return cp_encrypt_v1( $plaintext );

}

/**
 * Decrypt Data
 *
 * @param string $encrypted - Data to be decrypted
 */

function cp_decrypt( $encrypted ) {

    $version = explode( '::', $encrypted )[0];

    // Version-based decryption
    switch ( $version ) {
        case 'enc-v1':
            return cp_decrypt_v1( $encrypted );
            break;
        default:
            return $encrypted;
    }

}

/**
 * Version 1 - Encrypt data using AES-CBC-HMAC
 *
 * @param string $plaintext - Plaintext to be encrypted
 */

function cp_encrypt_v1( $plaintext )
{

    // Version
    $version = 'enc-v1';

    // Cipher method to CBC with 256-bit key
    $cipher = 'aes-256-cbc';
    // Salt for encryption key
    $salt_key = random_bytes(16);
    // Derive encryption key
    $key = hash_pbkdf2( 'sha256', CP_PASS_PHRASE, $salt_key, 10000 );
    // Salt for HMAC key
    $salt_hmac = random_bytes(16);
    // Derive HMAC key
    $key_hmac = hash_pbkdf2( 'sha256', CP_PASS_PHRASE, $salt_hmac, 10000 );
    // Initialization vector
    $iv = random_bytes(16);

    $ciphertext = openssl_encrypt( $plaintext, $cipher, $key, 0, $iv );
    $hash = hash_hmac( 'sha256', $ciphertext, $key_hmac );

    return $version . '::' . base64_encode( $ciphertext ) . '::' . base64_encode( $hash ) . '::' . base64_encode( $iv ) . '::' . base64_encode( $salt_key ) . '::' . base64_encode( $salt_hmac );

}

/**
 * Version 1 - Decrypt data using AES-CBC-HMAC
 *
 * @param string $encypted - base64_encoded version, ciphertext, hash,
 *                         - iv, salt_key, and salt_hmac
 */

function cp_decrypt_v1( $encrypted )
{

    // Return empty if $encrypted is not set or empty.
    if ( ! isset( $encrypted ) || empty( $encrypted ) ) { return ''; }
	
    // Cipher method to CBC with 256-bit key
    $cipher = 'aes-256-cbc';

    list( $version, $ciphertext, $hash, $iv, $salt_key, $salt_hmac ) = explode( '::', $encrypted );
    $version = base64_decode( $version );
    $ciphertext = base64_decode( $ciphertext );
    $hash = base64_decode( $hash );
    $iv = base64_decode( $iv );
    $salt_key = base64_decode( $salt_key );
    $salt_hmac = base64_decode( $salt_hmac );

    // Derive encryption key
    $key = hash_pbkdf2( 'sha256', CP_PASS_PHRASE, $salt_key, 10000 );
    // Derive HMAC key
    $key_hmac = hash_pbkdf2( 'sha256', CP_PASS_PHRASE, $salt_hmac, 10000 );
    
    $digest = hash_hmac( 'sha256', $ciphertext, $key_hmac );

    // HMAC authentication
    if ( hash_equals( $hash, $digest ) ) {
        return openssl_decrypt( $ciphertext, $cipher, $key, 0, $iv );
        }
    else {
        exit ('<strong>Warning: </strong>Please verify authenticity of ciphertext.');
    }

}
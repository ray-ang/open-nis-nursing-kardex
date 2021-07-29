<?php
/*
Plugin Name: Open-NIS Electronic Nursing Kardex
Plugin URI: https://github.com/ray-ang/open-nis-nursing-kardex
Description: A WordPress-based electronic nursing kardex
Version: 0.9.8
Author: Raymund John Ang
License: GPL v2 or later
Text Domain: open-nis
Copyright 2019 Raymund John Ang (email: raymund@open-nis.org)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA
*/

if (! class_exists('Basic')) require_once __DIR__ . '/libs/basic/Basic.php'; // BasicPHP class library

add_action( 'admin_init', 'rja_admin_front' ); // Admin - before headers sent
add_action( 'wp', 'rja_admin_front' ); // Frontend - before headers sent

function rja_admin_front() {

	if ( ! is_admin() && ! wp_doing_ajax() && ! empty($_POST) ) {
		foreach ( $_POST as $key => $value ) {
			$_POST[$key] = stripslashes($value); // Remove '\' (i.e. when saving " ' ")
		}
	}

}

// Require only after '\' is removed from $_POST
if (class_exists('Basic')) require_once __DIR__ . '/room.php'; // Room custom post type and template
if (class_exists('Basic')) require_once __DIR__ . '/shortcodes.php'; // Shortcodes

add_action( 'admin_init', 'rja_admin_encrypt_btn' ); // Encrypt and decrypt buttons

function rja_admin_encrypt_btn() {

	if( is_admin() && ! wp_doing_ajax() && isset($_POST['encrypt']) && $_POST['encrypt'] === 'Encrypt' && ! empty($_POST) ) {

		foreach ( $_POST['meta'] as $meta ) {
			if ( ! stristr($meta['value'], 'encv') ) {
				$index = array_search($meta, $_POST['meta']);
				$_POST['meta'][$index]['value'] = Basic::encrypt($meta['value'], KARDEX_PASS);
			}
		}

	}

	if( is_admin() && ! wp_doing_ajax() && isset($_POST['decrypt']) && $_POST['decrypt'] === 'Decrypt' && ! empty($_POST) ) {

		foreach ( $_POST['meta'] as $meta ) {
			if ( stristr($meta['value'], 'encv') ) {
				$index = array_search($meta, $_POST['meta']);
				$_POST['meta'][$index]['value'] = Basic::decrypt($meta['value'], KARDEX_PASS);
			}
		}

	}

}

add_action( 'admin_footer', 'rja_admin_footer' ); // Admin - footer

function rja_admin_footer() {

	if ( is_admin() && ! wp_doing_ajax() && ( stristr($_SERVER['REQUEST_URI'], 'post.php') || stristr($_SERVER['REQUEST_URI'], 'post-new.php') ) ) {
		?>
		<script>
			// Render encrypt and decrypt buttons
			const encryptBtn = document.createElement('input'); // Encrypt button
			encryptBtn.classList.add('button', 'button-info', 'button-large');
			encryptBtn.type = 'submit';
			encryptBtn.name = 'encrypt';
			encryptBtn.value = 'Encrypt';
			encryptBtn.style.marginRight = '3px';
			document.querySelector('#publishing-action').appendChild(encryptBtn);

			const decryptBtn = document.createElement('input'); // Decrypt button
			decryptBtn.classList.add('button', 'button-info', 'button-large');
			decryptBtn.type = 'submit';
			decryptBtn.name = 'decrypt';
			decryptBtn.value = 'Decrypt';
			document.querySelector('#publishing-action').appendChild(decryptBtn);
		</script>
		<?php
	}

}

register_activation_hook( __FILE__, 'rja_add_nurse_roles' ); // Add nurse roles on activation

function rja_add_nurse_roles()
{
	add_role( 'nurse', 'Nurse', array( 'read' => true ) );
	add_role( 'nurse_admin', 'Nurse Admin', array( 'read' => true ) );
}

add_action( 'admin_init', 'add_room_caps'); // Room capabilities

function add_room_caps() {
	$role = get_role( 'administrator' ); // Administrator
	$role->add_cap( 'read_room' );
	$role->add_cap( 'read_private_rooms' );
	$role->add_cap( 'publish_room' );
	$role->add_cap( 'publish_rooms' );
	$role->add_cap( 'edit_room' ); 
	$role->add_cap( 'edit_rooms' ); 
	$role->add_cap( 'edit_others_room' );
	$role->add_cap( 'edit_others_rooms' );   
	$role->add_cap( 'delete_room' );
	$role->add_cap( 'delete_rooms' ); 
	$role->add_cap( 'delete_others_rooms' );
	$role->add_cap( 'edit_published_rooms' );
	$role->add_cap( 'delete_published_rooms' );

	$role = get_role( 'nurse_admin' ); // Nurse Admin
	$role->add_cap( 'read_room' );
	$role->add_cap( 'read_private_rooms' );
	$role->add_cap( 'publish_room' ); 
	$role->add_cap( 'publish_rooms' ); 
	$role->add_cap( 'edit_room' ); 
	$role->add_cap( 'edit_rooms' ); 
	$role->add_cap( 'edit_others_room' );
	$role->add_cap( 'edit_others_rooms' );
	$role->add_cap( 'delete_room' );
	$role->add_cap( 'delete_rooms' ); 
	$role->add_cap( 'delete_others_rooms' );
	$role->add_cap( 'edit_published_rooms' );
	$role->add_cap( 'delete_published_rooms' );
}

register_deactivation_hook( __FILE__, 'rja_remove_nurse_roles' ); // Remove nurse roles on deactivation

function rja_remove_nurse_roles()
{
	remove_role( 'nurse' );
	remove_role( 'nurse_admin' );
}

add_action( 'rest_api_init', 'api_room_meta_fields' ); // Expose room meta fields to REST API

function api_room_meta_fields() {
	if ( is_numeric(Basic::segment(5)) ) {
		register_rest_field( 'room', 'meta', array(
			'get_callback' => 'get_room_meta_api',
			'schema'       => null,
			)
		);
	}
}

function get_room_meta_api( $room ) {
	$room_id = $room['id']; // Room ID
	$room_meta = get_post_meta( $room_id ); // Room meta array

	$decrypted = array();
	foreach ($room_meta as $key => $value) {
		$decrypted[$key] = Basic::decrypt($value[0], KARDEX_PASS); // Decrypt meta values
	}

	return $decrypted; // Decrypted room meta array
}

add_filter( 'login_redirect', 'redirect_after_login' ); // Redirect after login

function redirect_after_login() {
	return home_url(); // Homepage
}
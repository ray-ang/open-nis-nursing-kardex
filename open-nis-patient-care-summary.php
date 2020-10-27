<?php
/*
Plugin Name: Open-NIS Patient Care Summary
Plugin URI: https://github.com/ray-ang/open-nis-patient-care-summary
Description: A WordPress-based electronic patient care summary, or electronic nurse kardex
Version: 0.9.6
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

add_action( 'admin_init', 'rja_admin_front' ); // Admin - before headers sent
add_action( 'wp', 'rja_admin_front' ); // Frontend - before headers sent

function rja_admin_front() {

	if ( ! class_exists('Basic') ) {
		require_once 'Basic.php'; // BasicPHP class library
	}

	if ( defined('KARDEX_PASS') ) {
		Basic::encryption(KARDEX_PASS); // 'KARDEX_PASS' as passphrase
	}

}

add_action( 'admin_init', 'rja_admin_encrypt_btn' ); // Encrypt and decrypt buttons

function rja_admin_encrypt_btn() {

	if( isset($_POST['encrypt']) && $_POST['encrypt'] === 'Encrypt' && ! empty($_POST) ) {

		foreach ( $_POST['meta'] as $meta ) {

			if ( ! stristr($meta['value'], 'enc-v') ) {
				$index = array_search($meta, $_POST['meta']);
				$_POST['meta'][$index]['value'] = Basic::encrypt($meta['value']);
			}

		}

	}

	if( isset($_POST['decrypt']) && $_POST['decrypt'] === 'Decrypt' && ! empty($_POST) ) {

		foreach ( $_POST['meta'] as $meta ) {

			if ( stristr($meta['value'], 'enc-v') ) {
				$index = array_search($meta, $_POST['meta']);
				$_POST['meta'][$index]['value'] = Basic::decrypt($meta['value']);
			}

		}

	}

}

add_action( 'admin_footer', 'rja_admin_footer' ); // Admin - footer

function rja_admin_footer() {

	if ( is_admin() && ( stristr($_SERVER['REQUEST_URI'], 'post.php') || stristr($_SERVER['REQUEST_URI'], 'post-new.php') ) ) {
		?>
		<script>
			// Render encrypt and decrypt buttons
			const encryptBtn = document.createElement('input'); // Encrypt button
			encryptBtn.classList.add('button', 'button-info', 'button-large');
			encryptBtn.type = 'submit';
			encryptBtn.name = 'encrypt';
			encryptBtn.value = 'Encrypt';
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

register_activation_hook( __FILE__, 'rja_add_nurse_role' ); // Add "Nurse" role on activation

function rja_add_nurse_role()
{
	add_role( 'nurse', 'Nurse', array( 'read' => true ) );
}

register_deactivation_hook( __FILE__, 'rja_remove_nurse_role' ); // Remove "Nurse" role on deactivation

function rja_remove_nurse_role()
{
	remove_role( 'nurse' );
}

require_once 'room.php'; // Room custom post type and template
require_once 'shortcodes.php'; // Shortcodes
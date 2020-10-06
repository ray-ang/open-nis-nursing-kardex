<?php
/*
Plugin Name: Open-NIS Patient Care Summary
Plugin URI: https://open-nis.org/
Description: A WordPress-based electronic patient care summary, or electronic nursing kardex
Version: 0.9.5
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

// /** Show warning if ClassicPress Encryption plugin is not activated */
// add_filter( 'the_content', 'check_classicpress_encryption' );
// function check_classicpress_encryption() {

// 	if ( ! function_exists( 'cp_encrypt' ) ) {
// 		exit ('<strong>Warning: </strong>You need to install and activate <a href="https://github.com/ClassicPress-research/encryption-functions">ClassicPress Encryption</a> plugin.');
// 	}

// }

require_once 'Basic.php'; // BasicPHP
require_once 'patient.php'; // Patient custom post type and template
require_once 'shortcodes.php'; // Shortcodes

register_activation_hook( __FILE__, 'rja_add_nurse_role' ); // Add "Nurse" role on activation

function rja_add_nurse_role()
{
	add_role( 'nurse', 'Nurse', array( 'read' => TRUE ) );
}

register_deactivation_hook( __FILE__, 'rja_remove_nurse_role' ); // Remove "Nurse" role on deactivation

function rja_remove_nurse_role()
{
	remove_role( 'nurse' );
}
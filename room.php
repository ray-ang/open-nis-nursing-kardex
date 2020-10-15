<?php

/*
|--------------------------------------------------------------------------
| Room Custom Post Type
|--------------------------------------------------------------------------
*/

// Register Room Custom Post Type and Configuration
add_action( 'init', 'rja_register_room_cpt' );

function rja_register_room_cpt()
{	
	$labels = array(
	    'name'				=> _x( 'Rooms', 'Post type general name', 'open-nis' ),
	    'singular_name'		=> _x( 'Room', 'Post type singular name', 'open-nis' ),
	    'menu_name'			=> _x( 'Rooms', 'Admin Menu text', 'open-nis' ),
	    'name_admin_bar'	=> _x( 'Room', 'Add New on Toolbar', 'open-nis' ),
	    'add_new'			=> __( 'Add New', 'open-nis' ),
	    'add_new_item'		=> __( 'Add New Room', 'open-nis' ),
	    'new_item'			=> __( 'New Room', 'open-nis' ),
	    'edit_item'			=> __( 'Edit Room', 'open-nis' ),
	    'view_item'			=> __( 'View Room', 'open-nis' ),
	    'all_items'			=> __( 'All Rooms', 'open-nis' ),
	    'search_items'		=> __( 'Search Rooms', 'open-nis' ),
	    'not_found'			=> __( 'No Rooms found.', 'open-nis' )
	);

	$args = array(
	    'labels'             => $labels,
	    'public'             => true,
	    'publicly_queryable' => true,
	    'show_ui'            => true,
	    'show_in_menu'       => true,
	    'query_var'          => true,
	    'rewrite'            => array( 'slug' => 'room' ),
	    'capability_type'    => 'post',
	    'has_archive'        => true,
	    'hierarchical'       => true,
	    'menu_position'      => null,
	    'supports'           => array( 'title', /*'editor',*/ 'author', 'custom-fields' )
	);

	register_post_type( 'room', $args );
}

// Single Room - Before Header
add_action( 'wp', 'rja_single_room_content_header' );

function rja_single_room_content_header()
{

	if ( isset($_POST['edit-room']) && wp_verify_nonce($_POST['token'], 'token') ) {	

		$room = array(
			'ID' => $_POST['pid'],
			'post_type' => 'room',
			'post_status' => 'Publish'
			);	

		$pid = wp_update_post($room);

		update_metadata( 'post', $pid, 'room_name', Basic::encrypt($_POST['room-name']) );
		update_metadata( 'post', $pid, 'room_age', Basic::encrypt($_POST['age']) );
		update_metadata( 'post', $pid, 'room_sex', Basic::encrypt($_POST['sex']) );
		update_metadata( 'post', $pid, 'room_date_admission', Basic::encrypt($_POST['admission-date']) );
		update_metadata( 'post', $pid, 'room_doctor', Basic::encrypt($_POST['doctor']) );
		update_metadata( 'post', $pid, 'room_reason', Basic::encrypt($_POST['reason']) );
		update_metadata( 'post', $pid, 'room_allergy', Basic::encrypt($_POST['allergy']) );
		update_metadata( 'post', $pid, 'room_diet', Basic::encrypt($_POST['diet']) );
		update_metadata( 'post', $pid, 'room_iv_access', Basic::encrypt($_POST['iv-access']) );
		update_metadata( 'post', $pid, 'room_monitoring', Basic::encrypt($_POST['monitoring']) );
		update_metadata( 'post', $pid, 'room_urine', Basic::encrypt($_POST['urine']) );
		update_metadata( 'post', $pid, 'room_bowel', Basic::encrypt($_POST['bowel']) );
		update_metadata( 'post', $pid, 'room_history', Basic::encrypt($_POST['history']) );
		update_metadata( 'post', $pid, 'room_medical_notes', Basic::encrypt($_POST['medical-notes']) );
		update_metadata( 'post', $pid, 'room_nursing_plan', Basic::encrypt($_POST['nursing-plan']) );

		$link = get_permalink($pid);
		wp_redirect($link);

	}

	if ( isset($_POST['delete-room']) && isset($_POST['pid']) ) {

		wp_delete_post( $_POST['pid'], true );
		wp_redirect( home_url() );

	}

}

// Custom JavaScript
add_action( 'wp_footer', 'rja_single_room_header_script' );

function rja_single_room_header_script()
{
	?>
	<script>
		function toggleRoomForm() {
			const form = document.getElementById("editroom");
			if (form.style.display == "none") {
				form.style.display = "block";
			} else {
				form.style.display = "none";
			}
		}

		function printKardex() {
			const kardex = document.querySelector('#kardex').innerHTML;
			document.body.innerHTML = kardex;
			document.body.style.backgroundColor = 'white';
			document.body.style.margin = '30px';
			document.querySelector('#no-print').innerHTML = '';
			window.print();
		}
	</script>
	<?php
}

// Single Room
add_filter( 'the_content', 'rja_single_room_content' );

function rja_single_room_content()
{

	if ( get_post_type() == 'room' ) {

		if ( current_user_can('administrator') || current_user_can('nurse') ) {

			$room = get_metadata( 'post', get_the_ID() );

			$room_name = Basic::decrypt($room['room_name'][0]);
			$room_age = Basic::decrypt($room['room_age'][0]);
			if ( Basic::decrypt($room['room_sex'][0]) == 'M' ) { $room_sex = 'Male'; } else { $room_sex = 'Female'; }
			$room_date_admission = Basic::decrypt($room['room_date_admission'][0]);
			$room_doctor = Basic::decrypt($room['room_doctor'][0]);
			$room_reason = Basic::decrypt($room['room_reason'][0]);
			$room_allergy = Basic::decrypt($room['room_allergy'][0]);
			$room_diet = Basic::decrypt($room['room_diet'][0]);
			$room_iv_access = Basic::decrypt($room['room_iv_access'][0]);
			$room_monitoring = Basic::decrypt($room['room_monitoring'][0]);
			$room_urine = Basic::decrypt($room['room_urine'][0]);
			$room_bowel = Basic::decrypt($room['room_bowel'][0]);
			$room_history = Basic::decrypt($room['room_history'][0]);
			$room_medical_notes = Basic::decrypt($room['room_medical_notes'][0]);
			$room_nursing_plan = Basic::decrypt($room['room_nursing_plan'][0]);

			?>
			<div id="kardex">
				<h3>Room: <?php esc_html(the_title()); ?></h3>
				<p>Name: <?= esc_html($room_name); ?><br />
				Age: <?= esc_html($room_age); ?><br />
				Sex: <?= esc_html($room_sex); ?></p>
				<p>Admission Date: <?= esc_html($room_date_admission); ?><br />
				Doctor: <?= esc_html($room_doctor); ?><br />
				Reason: <?= esc_html($room_reason); ?><br />
				Allergy: <?= esc_html($room_allergy); ?><br />
				Diet: <?= esc_html($room_diet); ?></p>
				<p>IV Access: <?= esc_html($room_iv_access); ?><br />
				Monitoring: <?= esc_html($room_monitoring); ?><br />
				Urine: <?= esc_html($room_urine); ?><br />
				Bowel Movement: <?= esc_html($room_bowel); ?></p>
				<p><em>History:</em><br /><?= nl2br(esc_html($room_history)); ?></p>
				<p><em>Medical Notes:</em><br /><?= nl2br(esc_html($room_medical_notes)); ?></p>
				<p><em>Nursing Plan of Care:</em><br /><?= nl2br(esc_html($room_nursing_plan)); ?></p>
				<div id="no-print">
					<h2>Edit Information</h2>
					<button onclick="toggleRoomForm()">Edit Form</button>
					<button onclick="printKardex()">Print</button>
					<div id="editroom" style="display: none;">
						<p></p>
						<form method="post">
							<p>
								<label for="room">Room</label><br />
								<input type="text" id="room" name="room" value="<?php esc_html(the_title()); ?>" readonly />
								<input type="hidden" id="pid" name="pid" value="<?php the_ID(); ?>" readonly />
							</p>
							<p>
								<label for="name">Name</label><br />
								<input type="text" id="room-name" name="room-name" value="<?= esc_html($room_name); ?>" required />
							</p>
							<p>
								<label for="age">Age</label><br />
								<input type="number" id="age" name="age" value="<?= esc_html($room_age); ?>" required />
							</p>
							<p>
								<label for="sex">Sex</label><br />
								<select id="sex" name="sex" size="2" required>
									<option value="M" <?php if (Basic::decrypt($room['room_sex'][0])=='M') echo 'selected="selected"'; ?>>Male</option>
									<option value="F" <?php if (Basic::decrypt($room['room_sex'][0])=='F') echo 'selected="selected"'; ?>>Female</option>
								</select>
							</p>
							<p>
								<label for="admission-date">Date of Admission</label><br />
								<input type="date" id="admission-date" name="admission-date" value="<?= esc_html($room_date_admission); ?>" required />
							</p>
							<p>
								<label for="doctor">Doctor</label><br />
								<input type="text" id="doctor" name="doctor" value="<?= esc_html($room_doctor);?>" required /><br />
							</p>
							<p>
								<label for="reason">Reason for Admission</label><br />
								<input type="text" id="reason" name="reason" value="<?= esc_html($room_reason);?>" required /><br />
							</p>
							<p>
								<label for="allergy">Allergy</label><br />
								<input type="text" id="allergy" name="allergy" value="<?= esc_html($room_allergy);?>" required /><br />
							</p>
							<p>
								<label for="diet">Diet</label><br />
								<input type="text" id="diet" name="diet" value="<?= esc_html($room_diet);?>" required /><br />
							</p>
							<p>
								<label for="iv-access">IV Access</label><br />
								<input type="text" id="iv-access" name="iv-access" value="<?= esc_html($room_iv_access);?>" required /><br />
							</p>
							<p>
								<label for="monitoring">Monitoring</label><br />
								<input type="text" id="monitoring" name="monitoring" value="<?= esc_html($room_monitoring);?>" required /><br />
							</p>
							<p>
								<label for="urine">Urine</label><br />
								<input type="text" id="urine" name="urine" value="<?= esc_html($room_urine);?>" required /><br />
							</p>
							<p>
								<label for="bowel">Bowel Movement</label><br />
								<input type="text" id="bowel" name="bowel" value="<?= esc_html($room_bowel);?>" required /><br />
							</p>
							<p>
								<label for="history">History</label><br />
								<textarea id="history" name="history" required><?= esc_html($room_history); ?></textarea>
							</p>
							<p>
								<label for="medical-notes">Medical Notes</label><br />
								<textarea id="medical-notes" name="medical-notes" required><?= esc_html($room_medical_notes); ?></textarea>
							</p>
							<p>
								<label for="nursing-plan">Nursing Plan of Care</label><br />
								<textarea id="nursing-plan" name="nursing-plan" required><?= esc_html($room_nursing_plan); ?></textarea>
							</p>
							<p>
								<input type="submit" value="Edit Patient" id="edit-room" name="edit-room" />
							</p>
							<!-- <p><input type="reset" value="Clear Details" id="reset-form" name="reset-form" /></p> -->
							<?php if (current_user_can('administrator')) : ?>
								<p><input type="submit" value="Delete Room" id="delete-room" name="delete-room" onclick="return confirm('Are you sure you want to delete this room?');" /></p>
							<?php endif ?>
							<?php wp_nonce_field( 'token', 'token' ); ?>
						</form>
						<button onclick="toggleRoomForm()">Close Form</button>
					</div>
				</div>
			</div>
			<?php			
		} else {
			?>
			<p>You do not have permission to view patient information.</p>
			<?php
		}

	} else {

		return get_the_content();

	}

}
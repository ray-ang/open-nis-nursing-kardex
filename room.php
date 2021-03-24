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
	    'name'           => _x( 'Rooms', 'Post type general name', 'open-nis' ),
	    'singular_name'  => _x( 'Room', 'Post type singular name', 'open-nis' ),
	    'menu_name'      => _x( 'Rooms', 'Admin Menu text', 'open-nis' ),
	    'name_admin_bar' => _x( 'Room', 'Add New on Toolbar', 'open-nis' ),
	    'add_new'        => __( 'Add New', 'open-nis' ),
	    'add_new_item'   => __( 'Add New Room', 'open-nis' ),
	    'new_item'       => __( 'New Room', 'open-nis' ),
	    'edit_item'      => __( 'Edit Room', 'open-nis' ),
	    'view_item'      => __( 'View Room', 'open-nis' ),
	    'all_items'      => __( 'All Rooms', 'open-nis' ),
	    'search_items'   => __( 'Search Rooms', 'open-nis' ),
	    'not_found'      => __( 'No Rooms found.', 'open-nis' )
	);

	$args = array(
	    'labels'             => $labels,
	    'public'             => true,
	    'publicly_queryable' => true,
	    'show_ui'            => true,
	    'show_in_menu'       => true,
	    'query_var'          => true,
	    'rewrite'            => array( 'slug' => 'room' ),
	    'map_meta_cap'       => true,
	    'capability_type'    => array( 'room', 'rooms' ),
	    'capabilities'       => array(
	    	'read_post'                 => 'read_room',
	    	'read_private_posts'        => 'read_private_rooms',
	    	'publish_post'              => 'publish_room',
	    	'publish_posts'             => 'publish_rooms',
	    	'edit_post'                 => 'edit_room',
	    	'edit_posts'                => 'edit_rooms',
	    	'edit_published_posts'      => 'edit_published_rooms',
	    	'edit_others_post'          => 'edit_others_room',
	    	'edit_others_posts'         => 'edit_others_rooms',
	    	'delete_post'               => 'delete_room',
	    	'delete_posts'              => 'delete_rooms',
	    	'delete_published_posts'    => 'delete_published_rooms',
	    	'delete_others_posts'       => 'delete_others_rooms'
	    ),
	    'has_archive'        => true,
	    'hierarchical'       => true,
	    'menu_position'      => null,
	    'supports'           => array( 'title', /*'editor',*/ 'author', 'custom-fields' )
	);

	register_post_type( 'room', $args );
}

// Single Room - before header
add_action( 'wp', 'rja_single_room_content_header' );

function rja_single_room_content_header()
{

	if ( isset($_POST['edit-room']) && wp_verify_nonce($_POST['token'], 'token') && ( current_user_can('administrator') || current_user_can('nurse') ) ) {	

		$room = array(
			'ID' => $_POST['pid'],
			'post_type' => 'room',
			'post_status' => 'Publish'
			);	

		$pid = wp_update_post($room);
		$current_user = wp_get_current_user();

		update_post_meta( $pid, 'room_last_edit_user', Basic::encrypt($current_user->display_name) );
		update_post_meta( $pid, 'room_last_edit_date', Basic::encrypt(date("Y-m-d")) );
		update_post_meta( $pid, 'room_last_edit_time', Basic::encrypt(date(TIME_HISA)) );
		update_post_meta( $pid, 'room_name', Basic::encrypt($_POST['room-name']) );
		update_post_meta( $pid, 'room_age', Basic::encrypt($_POST['age']) );
		update_post_meta( $pid, 'room_sex', Basic::encrypt($_POST['sex']) );
		update_post_meta( $pid, 'room_date_admission', Basic::encrypt($_POST['admission-date']) );
		update_post_meta( $pid, 'room_doctor', Basic::encrypt($_POST['doctor']) );
		update_post_meta( $pid, 'room_reason', Basic::encrypt($_POST['reason']) );
		update_post_meta( $pid, 'room_allergy', Basic::encrypt($_POST['allergy']) );
		update_post_meta( $pid, 'room_diet', Basic::encrypt($_POST['diet']) );
		update_post_meta( $pid, 'room_iv_access', Basic::encrypt($_POST['iv-access']) );
		update_post_meta( $pid, 'room_monitoring', Basic::encrypt($_POST['monitoring']) );
		update_post_meta( $pid, 'room_urine', Basic::encrypt($_POST['urine']) );
		update_post_meta( $pid, 'room_bowel', Basic::encrypt($_POST['bowel']) );
		update_post_meta( $pid, 'room_history', Basic::encrypt($_POST['history']) );
		update_post_meta( $pid, 'room_medical_notes', Basic::encrypt($_POST['medical-notes']) );
		update_post_meta( $pid, 'room_diagnostics', Basic::encrypt($_POST['diagnostics']) );
		update_post_meta( $pid, 'room_nursing_plan', Basic::encrypt($_POST['nursing-plan']) );

		$link = get_permalink($pid);
		wp_redirect($link);

	}

	if ( isset($_POST['delete-room']) && isset($_POST['pid']) && wp_verify_nonce($_POST['token'], 'token') && current_user_can('administrator') ) {

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
			const kardex = document.querySelector('#kardex');
			kardex.firstElementChild.style.float = 'left';
			kardex.firstElementChild.style.width = '220px';
			kardex.firstElementChild.style.marginRight = '120px';
			document.body.innerHTML = kardex.innerHTML;
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

			$room_last_edit_user = Basic::decrypt($room['room_last_edit_user'][0]);
			$room_last_edit_date = Basic::decrypt($room['room_last_edit_date'][0]);
			$room_last_edit_time = Basic::decrypt($room['room_last_edit_time'][0]);
			$room_name = Basic::decrypt($room['room_name'][0]);
			$room_age = Basic::decrypt($room['room_age'][0]);
			if ( Basic::decrypt($room['room_sex'][0]) == 'M' ) { $room_sex = 'Male'; }
			if ( Basic::decrypt($room['room_sex'][0]) == 'F' ) { $room_sex = 'Female'; }
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
			$room_diagnostics = Basic::decrypt($room['room_diagnostics'][0]);
			$room_nursing_plan = Basic::decrypt($room['room_nursing_plan'][0]);

			?>
			<div id="kardex">
				<div>
					<h3>Room: <?php esc_html(the_title()); ?></h3>
					<p>Name: <?= esc_html($room_name); ?><br />
					Age: <?= esc_html($room_age); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sex: <?= esc_html($room_sex); ?></p>
					<p>Admission Date: <?= esc_html($room_date_admission); ?><br />
					Doctor: <?= esc_html($room_doctor); ?><br />
					Reason: <?= esc_html($room_reason); ?><br />
					Allergy: <?= esc_html($room_allergy); ?><br />
					Diet: <?= esc_html($room_diet); ?></p>
					<p>IV Access:<br />
					<?= nl2br(esc_html($room_iv_access)); ?><br />
					<br />
					Monitoring: <?= esc_html($room_monitoring); ?><br />
					Urine: <?= esc_html($room_urine); ?><br />
					Bowel Movement: <?= esc_html($room_bowel); ?></p>
				</div>
				<div>
					<p><em>History:</em><br /><?= nl2br(esc_html($room_history)); ?></p>
					<p><em>Medical Notes:</em><br /><?= nl2br(esc_html($room_medical_notes)); ?></p>
					<p><em>Laboratory & Diagnostics:</em><br /><?= nl2br(esc_html($room_diagnostics)); ?></p>
					<p><em>Nursing Plan of Care & Reminders:</em><br /><?= nl2br(esc_html($room_nursing_plan)); ?></p>
				</div>
				<small style="display: block; clear: both;">Kardex was last edited by <?= $room_last_edit_user ?> on <?= $room_last_edit_date ?> at <?= $room_last_edit_time ?>.</small>
				<small style="display: block; clear: both;">Kardex report generated by <?php $current_user = wp_get_current_user(); echo $current_user->display_name; ?> on <?php echo date("Y-m-d"); ?> at <?php echo date(TIME_HISA); ?>.</small>
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
								<input type="text" id="room-name" name="room-name" value="<?= esc_html($room_name); ?>" pattern="[A-Za-z '-]+" title="Patient name" <?php echo ( ($room_name == '') ? 'required' : 'readonly' ); ?> />
							</p>
							<p>
								<label for="age">Age </label><br />
								<small>(In years.)</small><br />
								<input type="number" id="age" name="age" value="<?= esc_html($room_age); ?>" <?php echo ( ($room_age == '') ? 'required' : 'readonly' ); ?> />
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
								<label for="doctor">Doctor </label><br />
								<small>(Attending physician and consultants.)</small><br />
								<input type="text" id="doctor" name="doctor" value="<?= esc_html($room_doctor);?>" required /><br />
							</p>
							<p>
								<label for="reason">Reason for Admission </label><br />
								<small>(Signs, symptoms or brief history.)</small><br />
								<input type="text" id="reason" name="reason" value="<?= esc_html($room_reason);?>" required /><br />
							</p>
							<p>
								<label for="allergy">Allergy </label><br />
								<small>(Food, drug and environmental allergies.)</small><br />
								<input type="text" id="allergy" name="allergy" value="<?= esc_html($room_allergy);?>" required /><br />
							</p>
							<p>
								<label for="diet">Diet </label><br />
								<small>(Standing diet order and special instructions.)</small><br />
								<input type="text" id="diet" name="diet" value="<?= esc_html($room_diet);?>" required /><br />
							</p>
							<p>
								<label for="iv-access">IV Access </label><br />
								<small>(Central and/or peripheral. Size, location, fluids or locked. New line each site.)</small><br />
								<textarea id="iv-access" name="iv-access" required><?= esc_html($room_iv_access); ?></textarea><br />
							</p>
							<p>
								<label for="monitoring">Monitoring </label><br />
								<small>(Vital signs, neurological, vascular checks, etc. and frequency.)</small><br />
								<input type="text" id="monitoring" name="monitoring" value="<?= esc_html($room_monitoring);?>" required /><br />
							</p>
							<p>
								<label for="urine">Urine </label><br />
								<small>(Description - i.e. color, transparency.)</small><br />
								<input type="text" id="urine" name="urine" value="<?= esc_html($room_urine);?>" required /><br />
							</p>
							<p>
								<label for="bowel">Bowel Movement </label><br />
								<small>(Last BM, description.)</small><br />
								<input type="text" id="bowel" name="bowel" value="<?= esc_html($room_bowel);?>" required /><br />
							</p>
							<p>
								<label for="history">History</label><br />
								<textarea id="history" name="history" required><?= esc_html($room_history); ?></textarea>
							</p>
							<p>
								<label for="medical-notes">Medical Notes </label><br />
								<small>(Pertinent medical notes and plan of care.)</small><br />
								<textarea id="medical-notes" name="medical-notes" required><?= esc_html($room_medical_notes); ?></textarea><br />
							</p>
							<p>
								<label for="diagnostics">Laboratory and Diagnostics </label><br />
								<small>(Blood works, imaging and other diagnostics. Indicate if pending or done.)</small><br />
								<textarea id="diagnostics" name="diagnostics" required><?= esc_html($room_diagnostics); ?></textarea><br />
							</p>
							<p>
								<label for="nursing-plan">Nursing Plan of Care & Reminders </label><br />
								<small>(Nursing notes, plan of care & reminders.)</small><br />
								<textarea id="nursing-plan" name="nursing-plan" required><?= esc_html($room_nursing_plan); ?></textarea><br />
							</p>
							<p>
								<input type="submit" value="Edit Patient" id="edit-room" name="edit-room" />
							</p>
							<!-- <p><input type="reset" value="Clear Details" id="reset-form" name="reset-form" /></p> -->
							<?php wp_nonce_field( 'token', 'token' ); ?>
						</form>
							<?php if (current_user_can('administrator')) : ?>
								<form method="post">
									<p>
										<input type="submit" value="Delete Room" id="delete-room" name="delete-room" onclick="return confirm('Are you sure you want to delete this room?');" />
									</p>
									<input type="hidden" id="pid" name="pid" value="<?php the_ID(); ?>" readonly />
									<?php wp_nonce_field( 'token', 'token' ); ?>
								</form>
							<?php endif ?>
						<button onclick="toggleRoomForm()">Close Form</button>
					</div>
				</div>
			</div>
			<?php			
		} elseif ( current_user_can('nurse_admin') ) {

			$room = get_metadata( 'post', get_the_ID() );

			$room_last_edit_user = Basic::decrypt($room['room_last_edit_user'][0]);
			$room_last_edit_date = Basic::decrypt($room['room_last_edit_date'][0]);
			$room_last_edit_time = Basic::decrypt($room['room_last_edit_time'][0]);
			$room_name = Basic::decrypt($room['room_name'][0]);
			$room_age = Basic::decrypt($room['room_age'][0]);
			if ( Basic::decrypt($room['room_sex'][0]) == 'M' ) { $room_sex = 'Male'; }
			if ( Basic::decrypt($room['room_sex'][0]) == 'F' ) { $room_sex = 'Female'; }
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
			$room_diagnostics = Basic::decrypt($room['room_diagnostics'][0]);
			$room_nursing_plan = Basic::decrypt($room['room_nursing_plan'][0]);
			
			?>
			<div id="kardex">
				<div style="float: left; width: 220px; margin-right: 120px;">
					<h3>Room: <?php esc_html(the_title()); ?></h3>
					<p>Name: <?= esc_html($room_name); ?><br />
					Age: <?= esc_html($room_age); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sex: <?= esc_html($room_sex); ?></p>
					<p>Admission Date: <?= esc_html($room_date_admission); ?><br />
					Doctor: <?= esc_html($room_doctor); ?><br />
					Reason: <?= esc_html($room_reason); ?><br />
					Allergy: <?= esc_html($room_allergy); ?><br />
					Diet: <?= esc_html($room_diet); ?></p>
					<p>IV Access:<br />
					<?= nl2br(esc_html($room_iv_access)); ?><br />
					<br />
					Monitoring: <?= esc_html($room_monitoring); ?><br />
					Urine: <?= esc_html($room_urine); ?><br />
					Bowel Movement: <?= esc_html($room_bowel); ?></p>
				</div>
				<div style="float: left; width: 340px;">
					<p><em>History:</em><br /><?= nl2br(esc_html($room_history)); ?></p>
					<p><em>Medical Notes:</em><br /><?= nl2br(esc_html($room_medical_notes)); ?></p>
					<p><em>Laboratory & Diagnostics:</em><br /><?= nl2br(esc_html($room_diagnostics)); ?></p>
					<p><em>Nursing Plan of Care & Reminders:</em><br /><?= nl2br(esc_html($room_nursing_plan)); ?></p>
				</div>
				<small style="display: block; clear: both;">Kardex was last edited by <?= $room_last_edit_user ?> on <?= $room_last_edit_date ?> at <?= $room_last_edit_time ?>.</small>
				<small style="display: block; clear: both;">Kardex report generated by <?php $current_user = wp_get_current_user(); echo $current_user->display_name; ?> on <?php echo date("Y-m-d"); ?> at <?php echo date(TIME_HISA); ?>.</small>
				<div id="no-print">
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
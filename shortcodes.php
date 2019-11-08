<?php

/*
|--------------------------------------------------------------------------
| Open-NIS Patient Care Summary Shortcodes
|--------------------------------------------------------------------------
*/

// Add Patient Page - Before Header
add_action( 'template_redirect', 'rja_page_add_patient_header' );

function rja_page_add_patient_header()
{

	if (isset($_POST['add-patient']) && wp_verify_nonce($_POST['token'], 'token')) {

        $patient = array(
            'post_title' => $_POST['room'],
            'post_type' => 'patient',
            'post_status' => 'Publish'
            );  

        $pid = wp_insert_post($patient);

        add_metadata( 'post', $pid, 'patient_name', rja_encrypt($_POST['patient-name']) );
        add_metadata( 'post', $pid, 'patient_age', rja_encrypt($_POST['age']) );
        add_metadata( 'post', $pid, 'patient_sex', rja_encrypt($_POST['sex']) );
        add_metadata( 'post', $pid, 'patient_date_admission', rja_encrypt($_POST['admission-date']) );
        add_metadata( 'post', $pid, 'patient_doctor', rja_encrypt($_POST['doctor']) );
        add_metadata( 'post', $pid, 'patient_reason', rja_encrypt($_POST['reason']) );
        add_metadata( 'post', $pid, 'patient_allergy', rja_encrypt($_POST['allergy']) );
        add_metadata( 'post', $pid, 'patient_diet', rja_encrypt($_POST['diet']) );
        add_metadata( 'post', $pid, 'patient_iv_access', rja_encrypt($_POST['iv-access']) );
        add_metadata( 'post', $pid, 'patient_urine', rja_encrypt($_POST['urine']) );
        add_metadata( 'post', $pid, 'patient_bowel', rja_encrypt($_POST['bowel']) );
        add_metadata( 'post', $pid, 'patient_history', rja_encrypt($_POST['history']) );
        add_metadata( 'post', $pid, 'patient_medical_notes', rja_encrypt($_POST['medical-notes']) );
        add_metadata( 'post', $pid, 'patient_nursing_plan', rja_encrypt($_POST['nursing-plan']) );

        $link = get_permalink($pid);
        wp_redirect($link);

    }

}

// Add Patient Page
add_shortcode( 'open-nis-add-patient', 'rja_page_add_patient' );

function rja_page_add_patient()
{
    ?>
    <?php if ( current_user_can('administrator') || current_user_can('nurse') ): ?>
    <div>
        <form method="post">
        	<p><label for="room">Room</label><br />
           		<input type="text" id="room" name="room" value="<?php if (isset($_POST['room'])) echo $_POST['room']; ?>" required /><br />
            </p>
            <p><label for="name">Name</label><br />
            	<input type="text" id="patient-name" name="patient-name" value="<?php if (isset($_POST['patient-name'])) echo $_POST['patient-name']; ?>" required /><br />
            </p>
            <p><label for="age">Age</label><br />
            	<input type="number" id="age" name="age" value="<?php if (isset($_POST['age'])) echo $_POST['age']; ?>" required />
            </p>
            <p><label for="sex">Sex</label><br />
                <select id="sex" name="sex" size="2" required />
                    <option value="M" <?php if ( isset($_POST['sex']) && $_POST['sex'] == 'M' ) echo 'selected="selected"'; ?>>Male</option>
                    <option value="F" <?php if ( isset($_POST['sex']) && $_POST['sex'] == 'F' ) echo 'selected="selected"'; ?>>Female</option>
                </select>
            </p>
            <p><label for="admission-date">Date of Admission</label><br />
        		<input type="date" id="admission-date" name="admission-date" value="<?php if (isset($_POST['admission-date'])) echo $_POST['admission-date']; ?>" required />
            </p>
            <p><label for="doctor">Doctor</label><br />
                <input type="text" id="doctor" name="doctor" value="<?php if (isset($_POST['doctor'])) echo $_POST['doctor']; ?>" required /><br />
            </p>
            <p><label for="reason">Reason for Admission</label><br />
                <input type="text" id="reason" name="reason" value="<?php if (isset($_POST['reason'])) echo $_POST['reason']; ?>" required /><br />
            </p>
            <p><label for="allergy">Allergy</label><br />
                <input type="text" id="allergy" name="allergy" value="<?php if (isset($_POST['allergy'])) echo $_POST['allergy']; ?>" required /><br />
            </p>
            <p><label for="diet">Diet</label><br />
                <input type="text" id="diet" name="diet" value="<?php if (isset($_POST['diet'])) echo $_POST['diet']; ?>" required /><br />
            </p>
            <p><label for="iv-access">IV Access</label><br />
                <input type="text" id="iv-access" name="iv-access" value="<?php if (isset($_POST['iv-access'])) echo $_POST['iv-access']; ?>" required /><br />
            </p>
            <p><label for="urine">Urine</label><br />
                <input type="text" id="urine" name="urine" value="<?php if (isset($_POST['urine'])) echo $_POST['urine']; ?>" required /><br />
            </p>
            <p><label for="bowel">Bowel Movement</label><br />
                <input type="text" id="bowel" name="bowel" value="<?php if (isset($_POST['bowel'])) echo $_POST['bowel']; ?>" required /><br />
            </p>
            <p><label for="history">History</label><br />
                <textarea id="history" name="history" required><?php if (isset($_POST['history'])) echo $_POST['history']; ?></textarea>
            </p>
            <p><label for="medical-notes">Medical Notes</label><br />
                <textarea id="medical-notes" name="medical-notes" required><?php if (isset($_POST['medical-notes'])) echo $_POST['medical-notes']; ?></textarea>
            </p>
            <p><label for="nursing-plan">Nursing Plan of Care</label><br />
                <textarea id="nursing-plan" name="nursing-plan" required><?php if (isset($_POST['nursing-plan'])) echo $_POST['nursing-plan']; ?></textarea>
            </p>
            <div style="float: left; margin-right: 20px;"><input type="submit" value="Add Patient" id="add-patient" name="add-patient" /></div>
            <div style="float: left;"><input type="reset" value="Reset Form" id="reset-form" name="reset-form" /></div>
            <?php wp_nonce_field( 'token', 'token' ); ?>
        </form>
    </div>
    <?php else: ?>
        <style>
            .entry-header {display: none;}
        </style>
        <p>You do not have permission to add a patient.</p>
    <?php endif ?>
    <?php
}

// Search Patient Page
add_shortcode( 'open-nis-search-patient', 'rja_page_search_patient' );

function rja_page_search_patient()
{

    // Search by Room
    if ( isset($_POST['search-room']) && ! empty($_POST['patient-room']) && preg_match( '/[a-zA-Z0-9 _#-]/i', $_POST['patient-room'] ) ) {

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $args = array(
            'posts_per_page'    => 3,
            'offset'            => 0,
            'paged'             => $paged,
            's'                 => $_POST['patient-room'],
            'orderby'           => 'post_title',
            'order'             => 'ASC',
            'include'           => array(),
            'exclude'           => array(),
            'post_type'         => 'patient',
            'suppress_filters'  => true
        );

        $patient = get_posts($args);
        
    }

    // Search by Name
    if ( isset($_POST['search-name']) && ! empty($_POST['patient-name']) && stristr( $_POST['patient-name'], ' ' ) == false && preg_match( '/[a-zA-Z]/i', $_POST['patient-name'] ) ) {

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $args = array(
            'posts_per_page'    => 3,
            'offset'            => 0,
            'paged'             => $paged,
            'orderby'           => 'patient_name',
            'order'             => 'ASC',
            'include'           => array(),
            'exclude'           => array(),
            'meta_key'          => 'patient_name',
            'meta_value'        => rja_encrypt($_POST['patient-name']),
            'meta_compare'      => 'LIKE',
            'post_type'         => 'patient',
            'suppress_filters'  => true
        );

        $patient = get_posts($args);
        
    }
    ?>
    <?php if ( current_user_can('administrator') || current_user_can('nurse') ): ?>
    <div>
        <form method="post">
            <p><label for="patient-room">Room</label><br />
                <input type="text" id="patient-room" name="patient-room" value="<?php if (isset($_POST['patient-room'])) echo $_POST['patient-room']; ?>" />
            </p>
            <p><input type="submit" id="search-room" name="search-room" value="Search Room" /></p>
            <?php wp_nonce_field( 'token', 'token' ); ?>
        </form>
        <!--<form method="post">
            <p><label for="patient-name">Name</label><br />
                <input type="text" id="patient-name" name="patient-name" />
            </p>
            <p><input type="submit" id="search-name" name="search-name" value="Search Name" /></p>
            <?php wp_nonce_field( 'token', 'token' ); ?>
        </form>-->
    </div>
    <?php if ( $patient == true ): ?>
        <?php foreach( $patient as $patient ): ?>
            <p>Room Number: <a href="<?= get_the_permalink($patient->ID); ?>"><?= $patient->post_title; ?></a> - Patient Name: <?= rja_decrypt($patient->patient_name);?></p>
        <?php endforeach?>
        <?php elseif ( ( isset($_POST['search-room']) && $patient !== true ) || ( isset($_POST['search-name']) && $patient !== true ) ): ?>
        <p>No patient was found on search.</p>
    <?php endif ?>
    <?php else: ?>
        <style>
            .entry-header {display: none;}
        </style>
        <p>You do not have permission to search patients.</p>
    <?php endif ?>
    <?php
}
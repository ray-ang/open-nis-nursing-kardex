<?php

/*
|--------------------------------------------------------------------------
| Open-NIS room Care Summary Shortcodes
|--------------------------------------------------------------------------
*/

// Add Room Page - Before Header
add_action( 'wp', 'rja_page_add_room_header' );

function rja_page_add_room_header()
{

	if ( isset($_POST['add-room']) && wp_verify_nonce($_POST['token'], 'token') ) {

        if ( get_page_by_title( esc_html($_POST['room']), OBJECT, 'room' ) !== null ) {

            exit('Room already exists.');

            // $pid = $_POST['pid'];
            // $link = get_permalink($pid);
            // $room_exists = true;
            // wp_redirect($link);
            ?>
            <!-- <script>
                const msg = document.createElement('p');
                msg.innerHTML = 'Room already exists';

                const addRoomForm = document.querySelector('#add-room-form');
                addRoomForm.insertBefore(msg, addRoomForm.childNodes[1]);
            </script> -->
            <?php
        } else {

            $room = array(
                'post_title' => esc_html($_POST['room']),
                'post_type' => 'room',
                'post_status' => 'Publish'
                );

            $pid = wp_insert_post($room);

            // add_metadata( 'post', $pid, 'room_name', Basic::encrypt($_POST['room-name']) );
            // add_metadata( 'post', $pid, 'room_age', Basic::encrypt($_POST['age']) );
            // add_metadata( 'post', $pid, 'room_sex', Basic::encrypt($_POST['sex']) );
            // add_metadata( 'post', $pid, 'room_date_admission', Basic::encrypt($_POST['admission-date']) );
            // add_metadata( 'post', $pid, 'room_doctor', Basic::encrypt($_POST['doctor']) );
            // add_metadata( 'post', $pid, 'room_reason', Basic::encrypt($_POST['reason']) );
            // add_metadata( 'post', $pid, 'room_allergy', Basic::encrypt($_POST['allergy']) );
            // add_metadata( 'post', $pid, 'room_diet', Basic::encrypt($_POST['diet']) );
            // add_metadata( 'post', $pid, 'room_iv_access', Basic::encrypt($_POST['iv-access']) );
            // add_metadata( 'post', $pid, 'room_monitoring', Basic::encrypt($_POST['monitoring']) );
            // add_metadata( 'post', $pid, 'room_urine', Basic::encrypt($_POST['urine']) );
            // add_metadata( 'post', $pid, 'room_bowel', Basic::encrypt($_POST['bowel']) );
            // add_metadata( 'post', $pid, 'room_history', Basic::encrypt($_POST['history']) );
            // add_metadata( 'post', $pid, 'room_medical_notes', Basic::encrypt($_POST['medical-notes']) );
            // add_metadata( 'post', $pid, 'room_nursing_plan', Basic::encrypt($_POST['nursing-plan']) );

            $link = get_permalink($pid);
            wp_redirect($link);

        }

    }

}

// Add Room Page
add_shortcode( 'open-nis-add-room', 'rja_page_add_room' );

function rja_page_add_room()
{
    ?>
    <?php if ( current_user_can('administrator') || current_user_can('nurse') ): ?>
        <div>
            <form id="add-room-form" method="post">
                <p>
                    <label for="room">Room</label><br />
                    <input type="text" id="room" name="room" value="<?php if ( isset($_POST['room']) ) echo esc_html($_POST['room']); ?>" required /><br />
                    <input type="hidden" id="pid" name="pid" value="<?php the_ID(); ?>" />
                </p>
                <!-- <p><label for="name">Name</label><br />
                    <input type="text" id="room-name" name="room-name" value="<?php if ( isset($_POST['room-name']) ) echo esc_html($_POST['room-name']); ?>" required /><br />
                </p>
                <p><label for="age">Age</label><br />
                    <input type="number" id="age" name="age" value="<?php if ( isset($_POST['age']) ) echo esc_html($_POST['age']); ?>" required />
                </p>
                <p><label for="sex">Sex</label><br />
                    <select id="sex" name="sex" size="2" required />
                        <option value="M" <?php if ( isset($_POST['sex']) && $_POST['sex'] == 'M' ) echo 'selected="selected"'; ?>>Male</option>
                        <option value="F" <?php if ( isset($_POST['sex']) && $_POST['sex'] == 'F' ) echo 'selected="selected"'; ?>>Female</option>
                    </select>
                </p>
                <p><label for="admission-date">Date of Admission</label><br />
                    <input type="date" id="admission-date" name="admission-date" value="<?php if ( isset($_POST['admission-date']) ) echo esc_html($_POST['admission-date']); ?>" required />
                </p>
                <p><label for="doctor">Doctor</label><br />
                    <input type="text" id="doctor" name="doctor" value="<?php if ( isset($_POST['doctor']) ) echo esc_html($_POST['doctor']); ?>" required /><br />
                </p>
                <p><label for="reason">Reason for Admission</label><br />
                    <input type="text" id="reason" name="reason" value="<?php if ( isset($_POST['reason']) ) echo esc_html($_POST['reason']); ?>" required /><br />
                </p>
                <p><label for="allergy">Allergy</label><br />
                    <input type="text" id="allergy" name="allergy" value="<?php if ( isset($_POST['allergy']) ) echo esc_html($_POST['allergy']); ?>" required /><br />
                </p>
                <p><label for="diet">Diet</label><br />
                    <input type="text" id="diet" name="diet" value="<?php if ( isset($_POST['diet']) ) echo esc_html($_POST['diet']); ?>" required /><br />
                </p>
                <p><label for="iv-access">IV Access</label><br />
                    <input type="text" id="iv-access" name="iv-access" value="<?php if ( isset($_POST['iv-access']) ) echo esc_html($_POST['iv-access']); ?>" required /><br />
                </p>
                <p><label for="monitoring">Monitoring</label><br />
                    <input type="text" id="monitoring" name="monitoring" value="<?php if ( isset($_POST['monitoring']) ) echo esc_html($_POST['monitoring']); ?>" required /><br />
                </p>
                <p><label for="urine">Urine</label><br />
                    <input type="text" id="urine" name="urine" value="<?php if ( isset($_POST['urine']) ) echo esc_html($_POST['urine']); ?>" required /><br />
                </p>
                <p><label for="bowel">Bowel Movement</label><br />
                    <input type="text" id="bowel" name="bowel" value="<?php if ( isset($_POST['bowel']) ) echo esc_html($_POST['bowel']); ?>" required /><br />
                </p>
                <p><label for="history">History</label><br />
                    <textarea id="history" name="history" required><?php if ( isset($_POST['history']) ) echo esc_html($_POST['history']); ?></textarea>
                </p>
                <p><label for="medical-notes">Medical Notes</label><br />
                    <textarea id="medical-notes" name="medical-notes" required><?php if ( isset($_POST['medical-notes']) ) echo esc_html($_POST['medical-notes']); ?></textarea>
                </p>
                <p><label for="nursing-plan">Nursing Plan of Care</label><br />
                    <textarea id="nursing-plan" name="nursing-plan" required><?php if ( isset($_POST['nursing-plan']) ) echo esc_html($_POST['nursing-plan']); ?></textarea>
                </p> -->
                <p>
                    <div style="float: left; margin-right: 2rem;"><input type="submit" value="Add Room" id="add-room" name="add-room" /></div>
                    <div style="float: left;"><input type="reset" value="Clear" id="reset" name="reset" /></div>
                </p>
                <?php wp_nonce_field( 'token', 'token' ); ?>
            </form>
        </div>
    <?php else: ?>
        <p>You do not have permission to add a room.</p>
    <?php endif ?>
    <?php
}

// Search Room Page
add_shortcode( 'open-nis-search-room', 'rja_page_search_room' );

function rja_page_search_room()
{

    // Search by Room
    if ( isset($_POST['search-room']) && ! empty($_POST['room-room']) && preg_match('/[a-zA-Z0-9 _#-]/i', $_POST['room-room']) ) {

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $args = array(
            'posts_per_page'    => 3,
            'offset'            => 0,
            'paged'             => $paged,
            's'                 => esc_html($_POST['room-room']),
            'orderby'           => 'post_title',
            'order'             => 'ASC',
            'include'           => array(),
            'exclude'           => array(),
            'post_type'         => 'room',
            'suppress_filters'  => true
        );

        $rooms = get_posts($args);
        
    }

    ?>
    <?php if ( current_user_can('administrator') || current_user_can('nurse') ): ?>
        <div>
            <form method="post">
                <p>
                    <label for="room-room">Room</label><br />
                    <input type="text" id="room-room" name="room-room" value="<?php if ( isset($_POST['room-room']) ) echo esc_html($_POST['room-room']); ?>" required />
                </p>
                <p>
                    <div style="float: left; margin-right: 2rem;"><input type="submit" id="search-room" name="search-room" value="Search Room" /></div>
                    <div style="float: left;"><input type="button" value="Clear" id="reset" name="reset" onclick="clearSearch()" /></div>
                </p>
                <?php wp_nonce_field( 'token', 'token' ); ?>
            </form>
        </div>
        <script>
            function clearSearch() {
                document.querySelector('#room-room').value = '';
            }
        </script>
        <?php if ( isset($rooms) && $rooms == true ): ?>
            <div style="clear: both; margin-top: 6rem;">
            <?php foreach( $rooms as $room ): ?>
                <p>Room Number: <a href="<?= get_the_permalink($room->ID); ?>"><?= $room->post_title; ?></a> - Patient Name: <?= Basic::decrypt($room->room_name);?></p>
            <?php endforeach ?>
            </div>
        <?php elseif ( isset($rooms) && $rooms !== true ): ?>
            <div style="clear: both; margin-top: 6rem;">No room was found on search.</div>
        <?php endif ?>
    <?php else: ?>
        <p>You do not have permission to search rooms.</p>
    <?php endif ?>
    <?php
}
<?php

/*
|--------------------------------------------------------------------------
| Open-NIS room Care Summary Shortcodes
|--------------------------------------------------------------------------
*/

// Add Room Page - before header
add_action( 'wp', 'rja_page_add_room_header' );

function rja_page_add_room_header()
{

	if ( isset($_POST['add-room']) && wp_verify_nonce($_POST['token'], 'token') && ( current_user_can('administrator') || current_user_can('nurse') || current_user_can('nurse_admin') ) ) {

        if ( get_page_by_title( esc_html($_POST['room']), OBJECT, 'room' ) !== null ) {

            Basic::apiResponse( 409, 'Room already exists.' );

        } else {

            $room = array(
                'post_title' => esc_html($_POST['room']),
                'post_type' => 'room',
                'post_status' => 'Publish'
                );

            $pid = wp_insert_post($room);

            add_metadata( 'post', $pid, 'room_name', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_age', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_sex', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_date_admission', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_doctor', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_reason', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_allergy', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_diet', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_iv_access', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_monitoring', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_urine', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_bowel', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_history', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_medical_notes', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_medical_notes', Basic::encrypt('', KARDEX_PASS) );
            add_metadata( 'post', $pid, 'room_nursing_plan', Basic::encrypt('', KARDEX_PASS) );

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
    <?php if ( current_user_can('administrator') || current_user_can('nurse') || current_user_can('nurse_admin') ): ?>
        <div>
            <form id="add-room-form" method="post">
                <p>
                    <label for="room">Room</label><br />
                    <input type="text" id="room" name="room" value="<?php if ( isset($_POST['room']) ) echo esc_html($_POST['room']); ?>" required /><br />
                    <input type="hidden" id="pid" name="pid" value="<?php the_ID(); ?>" />
                </p>
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
    if ( isset($_POST['search-room']) && wp_verify_nonce($_POST['token'], 'token') && ! empty($_POST['room-room']) && preg_match('/[a-zA-Z0-9 _#-]/i', $_POST['room-room']) && ( current_user_can('administrator') || current_user_can('nurse') || current_user_can('nurse_admin') ) ) {

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
    <?php if ( current_user_can('administrator') || current_user_can('nurse') || current_user_can('nurse_admin') ): ?>
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
                <p>Room Number: <a href="<?= get_the_permalink($room->ID); ?>"><?= $room->post_title; ?></a> - Patient Name: <a href="<?= get_the_permalink($room->ID); ?>"><?= Basic::decrypt($room->room_name, KARDEX_PASS);?></a></p>
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
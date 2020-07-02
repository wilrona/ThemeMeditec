<?php
/**
 * Created by IntelliJ IDEA.
 * User: online2
 * Date: 16/03/2018
 * Time: 15:53
 */


function wp_insert_user_customs( $userdata ) {
    global $wpdb;

    if ( $userdata instanceof stdClass ) {
        $userdata = get_object_vars( $userdata );
    } elseif ( $userdata instanceof WP_User ) {
        $userdata = $userdata->to_array();
    }

    // Are we updating or creating?
    if ( ! empty( $userdata['ID'] ) ) {
        $ID = (int) $userdata['ID'];
        $update = true;
        $old_user_data = get_userdata( $ID );

        if ( ! $old_user_data ) {
            return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.' ) );
        }

        // hashed in wp_update_user(), plaintext if called directly
        $user_pass = ! empty( $userdata['user_pass'] ) ? $userdata['user_pass'] : $old_user_data->user_pass;
    } else {
        $update = false;
        // Hash the password
        $user_pass = wp_hash_password( $userdata['user_pass'] );
    }

    $sanitized_user_login = sanitize_user( $userdata['user_login'], true );

    /**
     * Filters a username after it has been sanitized.
     *
     * This filter is called before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $sanitized_user_login Username after it has been sanitized.
     */
    $pre_user_login = apply_filters( 'pre_user_login', $sanitized_user_login );

    //Remove any non-printable chars from the login string to see if we have ended up with an empty username
    $user_login = trim( $pre_user_login );

    // user_login must be between 0 and 60 characters.
    if ( empty( $user_login ) ) {
        return new WP_Error('empty_user_login', __('Cannot create a user with an empty login name.') );
    } elseif ( mb_strlen( $user_login ) > 60 ) {
        return new WP_Error( 'user_login_too_long', __( 'Username may not be longer than 60 characters.' ) );
    }

    if ( ! $update && username_exists( $user_login ) ) {
        return new WP_Error( 'existing_user_login', __( 'Sorry, that username already exists!' ) );
    }

    /**
     * Filters the list of blacklisted usernames.
     *
     * @since 4.4.0
     *
     * @param array $usernames Array of blacklisted usernames.
     */
    $illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

    if ( in_array( strtolower( $user_login ), array_map( 'strtolower', $illegal_logins ) ) ) {
        return new WP_Error( 'invalid_username', __( 'Sorry, that username is not allowed.' ) );
    }

    /*
     * If a nicename is provided, remove unsafe user characters before using it.
     * Otherwise build a nicename from the user_login.
     */
    if ( ! empty( $userdata['user_nicename'] ) ) {
        $user_nicename = sanitize_user( $userdata['user_nicename'], true );
        if ( mb_strlen( $user_nicename ) > 50 ) {
            return new WP_Error( 'user_nicename_too_long', __( 'Nicename may not be longer than 50 characters.' ) );
        }
    } else {
        $user_nicename = mb_substr( $user_login, 0, 50 );
    }

    $user_nicename = sanitize_title( $user_nicename );

    // Store values to save in user meta.
    $meta = array();

    /**
     * Filters a user's nicename before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $user_nicename The user's nicename.
     */
    $user_nicename = apply_filters( 'pre_user_nicename', $user_nicename );

    $raw_user_url = empty( $userdata['user_url'] ) ? '' : $userdata['user_url'];

    /**
     * Filters a user's URL before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $raw_user_url The user's URL.
     */
    $user_url = apply_filters( 'pre_user_url', $raw_user_url );

    $raw_user_email = empty( $userdata['user_email'] ) ? '' : $userdata['user_email'];

    /**
     * Filters a user's email before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $raw_user_email The user's email.
     */
    $user_email = apply_filters( 'pre_user_email', $raw_user_email );

    /*
     * If there is no update, just check for `email_exists`. If there is an update,
     * check if current email and new email are the same, or not, and check `email_exists`
     * accordingly.
     */
    if ( ( ! $update || ( ! empty( $old_user_data ) && 0 !== strcasecmp( $user_email, $old_user_data->user_email ) ) )
        && ! defined( 'WP_IMPORTING' )
        && email_exists( $user_email )
    ) {
        return new WP_Error( 'existing_user_email', __( 'Sorry, that email address is already used!' ) );
    }
    $nickname = empty( $userdata['nickname'] ) ? $user_login : $userdata['nickname'];

    /**
     * Filters a user's nickname before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $nickname The user's nickname.
     */
    $meta['nickname'] = apply_filters( 'pre_user_nickname', $nickname );

    $first_name = empty( $userdata['first_name'] ) ? '' : $userdata['first_name'];

    /**
     * Filters a user's first name before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $first_name The user's first name.
     */
    $meta['first_name'] = apply_filters( 'pre_user_first_name', $first_name );

    $last_name = empty( $userdata['last_name'] ) ? '' : $userdata['last_name'];

    /**
     * Filters a user's last name before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $last_name The user's last name.
     */
    $meta['last_name'] = apply_filters( 'pre_user_last_name', $last_name );

    if ( empty( $userdata['display_name'] ) ) {
        if ( $update ) {
            $display_name = $user_login;
        } elseif ( $meta['first_name'] && $meta['last_name'] ) {
            /* translators: 1: first name, 2: last name */
            $display_name = sprintf( _x( '%1$s %2$s', 'Display name based on first name and last name' ), $meta['first_name'], $meta['last_name'] );
        } elseif ( $meta['first_name'] ) {
            $display_name = $meta['first_name'];
        } elseif ( $meta['last_name'] ) {
            $display_name = $meta['last_name'];
        } else {
            $display_name = $user_login;
        }
    } else {
        $display_name = $userdata['display_name'];
    }

    /**
     * Filters a user's display name before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $display_name The user's display name.
     */
    $display_name = apply_filters( 'pre_user_display_name', $display_name );

    $description = empty( $userdata['description'] ) ? '' : $userdata['description'];

    /**
     * Filters a user's description before the user is created or updated.
     *
     * @since 2.0.3
     *
     * @param string $description The user's description.
     */
    $meta['description'] = apply_filters( 'pre_user_description', $description );

    $meta['rich_editing'] = empty( $userdata['rich_editing'] ) ? 'true' : $userdata['rich_editing'];

    $meta['comment_shortcuts'] = empty( $userdata['comment_shortcuts'] ) || 'false' === $userdata['comment_shortcuts'] ? 'false' : 'true';

    $admin_color = empty( $userdata['admin_color'] ) ? 'fresh' : $userdata['admin_color'];
    $meta['admin_color'] = preg_replace( '|[^a-z0-9 _.\-@]|i', '', $admin_color );

    $meta['use_ssl'] = empty( $userdata['use_ssl'] ) ? 0 : $userdata['use_ssl'];

    $user_registered = empty( $userdata['user_registered'] ) ? gmdate( 'Y-m-d H:i:s' ) : $userdata['user_registered'];

    $meta['show_admin_bar_front'] = empty( $userdata['show_admin_bar_front'] ) ? 'true' : $userdata['show_admin_bar_front'];

    $meta['locale'] = isset( $userdata['locale'] ) ? $userdata['locale'] : '';

    $user_nicename_check = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1" , $user_nicename, $user_login));

    if ( $user_nicename_check ) {
        $suffix = 2;
        while ($user_nicename_check) {
            // user_nicename allows 50 chars. Subtract one for a hyphen, plus the length of the suffix.
            $base_length = 49 - mb_strlen( $suffix );
            $alt_user_nicename = mb_substr( $user_nicename, 0, $base_length ) . "-$suffix";
            $user_nicename_check = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1" , $alt_user_nicename, $user_login));
            $suffix++;
        }
        $user_nicename = $alt_user_nicename;
    }

    $compacted = compact( 'user_pass', 'user_email', 'user_url', 'user_nicename', 'display_name', 'user_registered' );
    $data = wp_unslash( $compacted );

    if ( $update ) {
        if ( $user_email !== $old_user_data->user_email ) {
            $data['user_activation_key'] = '';
        }
        $wpdb->update( $wpdb->users, $data, compact( 'ID' ) );
        $user_id = (int) $ID;
    } else {
        $wpdb->insert( $wpdb->users, $data + compact( 'user_login' ) );
        $user_id = (int) $wpdb->insert_id;
    }

    $user = new WP_User( $user_id );

    /**
     * Filters a user's meta values and keys before the user is created or updated.
     *
     * Does not include contact methods. These are added using `wp_get_user_contact_methods( $user )`.
     *
     * @since 4.4.0
     *
     * @param array $meta {
     *     Default meta values and keys for the user.
     *
     *     @type string   $nickname             The user's nickname. Default is the user's username.
     *     @type string   $first_name           The user's first name.
     *     @type string   $last_name            The user's last name.
     *     @type string   $description          The user's description.
     *     @type bool     $rich_editing         Whether to enable the rich-editor for the user. False if not empty.
     *     @type bool     $comment_shortcuts    Whether to enable keyboard shortcuts for the user. Default false.
     *     @type string   $admin_color          The color scheme for a user's admin screen. Default 'fresh'.
     *     @type int|bool $use_ssl              Whether to force SSL on the user's admin area. 0|false if SSL is
     *                                          not forced.
     *     @type bool     $show_admin_bar_front Whether to show the admin bar on the front end for the user.
     *                                          Default true.
     * }
     * @param WP_User $user   User object.
     * @param bool    $update Whether the user is being updated rather than created.
     */
    $meta = apply_filters( 'insert_user_meta', $meta, $user, $update );

    // Update user meta.
    foreach ( $meta as $key => $value ) {
        update_user_meta( $user_id, $key, $value );
    }

    foreach ( wp_get_user_contact_methods( $user ) as $key => $value ) {
        if ( isset( $userdata[ $key ] ) ) {
            update_user_meta( $user_id, $key, $userdata[ $key ] );
        }
    }

    if ( isset( $userdata['role'] ) ) {
        $user->set_role( $userdata['role'] );
    } elseif ( ! $update ) {
        $user->set_role(get_option('default_role'));
    }
    wp_cache_delete( $user_id, 'users' );
    wp_cache_delete( $user_login, 'userlogins' );

//	if ( $update ) {
//		/**
//		 * Fires immediately after an existing user is updated.
//		 *
//		 * @since 2.0.0
//		 *
//		 * @param int    $user_id       User ID.
//		 * @param object $old_user_data Object containing user's data prior to update.
//		 */
//		do_action( 'profile_update', $user_id, $old_user_data );
//	} else {
//		/**
//		 * Fires immediately after a new user is registered.
//		 *
//		 * @since 1.5.0
//		 *
//		 * @param int $user_id User ID.
//		 */
//		do_action( 'user_register', $user_id );
//	}

    return $user_id;
}

/**
 * Update a user in the database.
 *
 * It is possible to update a user's password by specifying the 'user_pass'
 * value in the $userdata parameter array.
 *
 * If current user's password is being updated, then the cookies will be
 * cleared.
 *
 * @since 2.0.0
 *
 * @see wp_insert_user() For what fields can be set in $userdata.
 *
 * @param array|object|WP_User $userdata An array of user data or a user object of type stdClass or WP_User.
 * @return int|WP_Error The updated user's ID or a WP_Error object if the user could not be updated.
 */
function wp_update_user_custom( $userdata ) {
    if ( $userdata instanceof stdClass ) {
        $userdata = get_object_vars( $userdata );
    } elseif ( $userdata instanceof WP_User ) {
        $userdata = $userdata->to_array();
    }

    $ID = isset( $userdata['ID'] ) ? (int) $userdata['ID'] : 0;
    if ( ! $ID ) {
        return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.' ) );
    }

    // First, get all of the original fields
    $user_obj = get_userdata( $ID );
    if ( ! $user_obj ) {
        return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.' ) );
    }

    $user = $user_obj->to_array();

    // Add additional custom fields
    foreach ( _get_additional_user_keys( $user_obj ) as $key ) {
        $user[ $key ] = get_user_meta( $ID, $key, true );
    }

    // Escape data pulled from DB.
    $user = add_magic_quotes( $user );

//    if ( ! empty( $userdata['user_pass'] ) && $userdata['user_pass'] !== $user_obj->user_pass ) {
//        // If password is changing, hash it now
//        $plaintext_pass        = $userdata['user_pass'];
//        $userdata['user_pass'] = wp_hash_password( $userdata['user_pass'] );
//
//        /**
//         * Filters whether to send the password change email.
//         *
//         * @since 4.3.0
//         *
//         * @see wp_insert_user() For `$user` and `$userdata` fields.
//         *
//         * @param bool  $send     Whether to send the email.
//         * @param array $user     The original user array.
//         * @param array $userdata The updated user array.
//         */
//        $send_password_change_email = apply_filters( 'send_password_change_email', true, $user, $userdata );
//    }
//
//    if ( isset( $userdata['user_email'] ) && $user['user_email'] !== $userdata['user_email'] ) {
//        /**
//         * Filters whether to send the email change email.
//         *
//         * @since 4.3.0
//         *
//         * @see wp_insert_user() For `$user` and `$userdata` fields.
//         *
//         * @param bool  $send     Whether to send the email.
//         * @param array $user     The original user array.
//         * @param array $userdata The updated user array.
//         */
//        $send_email_change_email = apply_filters( 'send_email_change_email', true, $user, $userdata );
//    }

    wp_cache_delete( $user['user_email'], 'useremail' );
    wp_cache_delete( $user['user_nicename'], 'userslugs' );

    // Merge old and new fields with new fields overwriting old ones.
    $userdata = array_merge( $user, $userdata );
    $user_id  = wp_insert_user( $userdata );

//    if ( ! is_wp_error( $user_id ) ) {
//
//        $blog_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
//
//        $switched_locale = false;
//        if ( ! empty( $send_password_change_email ) || ! empty( $send_email_change_email ) ) {
//            $switched_locale = switch_to_locale( get_user_locale( $user_id ) );
//        }
//
//        if ( ! empty( $send_password_change_email ) ) {
//            /* translators: Do not translate USERNAME, ADMIN_EMAIL, EMAIL, SITENAME, SITEURL: those are placeholders. */
//            $pass_change_text = __(
//                'Hi ###USERNAME###,
//
//This notice confirms that your password was changed on ###SITENAME###.
//
//If you did not change your password, please contact the Site Administrator at
//###ADMIN_EMAIL###
//
//This email has been sent to ###EMAIL###
//
//Regards,
//All at ###SITENAME###
//###SITEURL###'
//            );
//
//            $pass_change_email = array(
//                'to'      => $user['user_email'],
//                /* translators: Password change notification email subject. %s: Site name */
//                'subject' => __( '[%s] Password Changed' ),
//                'message' => $pass_change_text,
//                'headers' => '',
//            );
//
//            /**
//             * Filters the contents of the email sent when the user's password is changed.
//             *
//             * @since 4.3.0
//             *
//             * @param array $pass_change_email {
//             *            Used to build wp_mail().
//             *            @type string $to      The intended recipients. Add emails in a comma separated string.
//             *            @type string $subject The subject of the email.
//             *            @type string $message The content of the email.
//             *                The following strings have a special meaning and will get replaced dynamically:
//             *                - ###USERNAME###    The current user's username.
//             *                - ###ADMIN_EMAIL### The admin email in case this was unexpected.
//             *                - ###EMAIL###       The user's email address.
//             *                - ###SITENAME###    The name of the site.
//             *                - ###SITEURL###     The URL to the site.
//             *            @type string $headers Headers. Add headers in a newline (\r\n) separated string.
//             *        }
//             * @param array $user     The original user array.
//             * @param array $userdata The updated user array.
//             */
//            $pass_change_email = apply_filters( 'password_change_email', $pass_change_email, $user, $userdata );
//
//            $pass_change_email['message'] = str_replace( '###USERNAME###', $user['user_login'], $pass_change_email['message'] );
//            $pass_change_email['message'] = str_replace( '###ADMIN_EMAIL###', get_option( 'admin_email' ), $pass_change_email['message'] );
//            $pass_change_email['message'] = str_replace( '###EMAIL###', $user['user_email'], $pass_change_email['message'] );
//            $pass_change_email['message'] = str_replace( '###SITENAME###', $blog_name, $pass_change_email['message'] );
//            $pass_change_email['message'] = str_replace( '###SITEURL###', home_url(), $pass_change_email['message'] );
//
//            wp_mail( $pass_change_email['to'], sprintf( $pass_change_email['subject'], $blog_name ), $pass_change_email['message'], $pass_change_email['headers'] );
//        }
//
//        if ( ! empty( $send_email_change_email ) ) {
//            /* translators: Do not translate USERNAME, ADMIN_EMAIL, NEW_EMAIL, EMAIL, SITENAME, SITEURL: those are placeholders. */
//            $email_change_text = __(
//                'Hi ###USERNAME###,
//
//This notice confirms that your email address on ###SITENAME### was changed to ###NEW_EMAIL###.
//
//If you did not change your email, please contact the Site Administrator at
//###ADMIN_EMAIL###
//
//This email has been sent to ###EMAIL###
//
//Regards,
//All at ###SITENAME###
//###SITEURL###'
//            );
//
//            $email_change_email = array(
//                'to'      => $user['user_email'],
//                /* translators: Email change notification email subject. %s: Site name */
//                'subject' => __( '[%s] Email Changed' ),
//                'message' => $email_change_text,
//                'headers' => '',
//            );
//
//            /**
//             * Filters the contents of the email sent when the user's email is changed.
//             *
//             * @since 4.3.0
//             *
//             * @param array $email_change_email {
//             *            Used to build wp_mail().
//             *            @type string $to      The intended recipients.
//             *            @type string $subject The subject of the email.
//             *            @type string $message The content of the email.
//             *                The following strings have a special meaning and will get replaced dynamically:
//             *                - ###USERNAME###    The current user's username.
//             *                - ###ADMIN_EMAIL### The admin email in case this was unexpected.
//             *                - ###NEW_EMAIL###   The new email address.
//             *                - ###EMAIL###       The old email address.
//             *                - ###SITENAME###    The name of the site.
//             *                - ###SITEURL###     The URL to the site.
//             *            @type string $headers Headers.
//             *        }
//             * @param array $user The original user array.
//             * @param array $userdata The updated user array.
//             */
//            $email_change_email = apply_filters( 'email_change_email', $email_change_email, $user, $userdata );
//
//            $email_change_email['message'] = str_replace( '###USERNAME###', $user['user_login'], $email_change_email['message'] );
//            $email_change_email['message'] = str_replace( '###ADMIN_EMAIL###', get_option( 'admin_email' ), $email_change_email['message'] );
//            $email_change_email['message'] = str_replace( '###NEW_EMAIL###', $userdata['user_email'], $email_change_email['message'] );
//            $email_change_email['message'] = str_replace( '###EMAIL###', $user['user_email'], $email_change_email['message'] );
//            $email_change_email['message'] = str_replace( '###SITENAME###', $blog_name, $email_change_email['message'] );
//            $email_change_email['message'] = str_replace( '###SITEURL###', home_url(), $email_change_email['message'] );
//
//            wp_mail( $email_change_email['to'], sprintf( $email_change_email['subject'], $blog_name ), $email_change_email['message'], $email_change_email['headers'] );
//        }
//
//        if ( $switched_locale ) {
//            restore_previous_locale();
//        }
//    }

    // Update the cookies if the password changed.
//    $current_user = wp_get_current_user();
//    if ( $current_user->ID == $ID ) {
//        if ( isset( $plaintext_pass ) ) {
//            wp_clear_auth_cookie();
//
//            // Here we calculate the expiration length of the current auth cookie and compare it to the default expiration.
//            // If it's greater than this, then we know the user checked 'Remember Me' when they logged in.
//            $logged_in_cookie = wp_parse_auth_cookie( '', 'logged_in' );
//            /** This filter is documented in wp-includes/pluggable.php */
//            $default_cookie_life = apply_filters( 'auth_cookie_expiration', ( 2 * DAY_IN_SECONDS ), $ID, false );
//            $remember            = ( ( $logged_in_cookie['expiration'] - time() ) > $default_cookie_life );
//
//            wp_set_auth_cookie( $ID, $remember );
//        }
//    }

    return $user_id;
}
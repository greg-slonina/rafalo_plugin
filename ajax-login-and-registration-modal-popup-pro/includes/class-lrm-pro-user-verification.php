<?php

/**
 * User Verification class
 *
 * @since 1.14
 *
 * Class LRM_Pro_User_Verification
 */
class LRM_Pro_User_Verification {

	static function link_verification_is_on() {
	    // && LRM_Settings::get()->setting('general_pro/all/allow_user_set_password')
		return 'email-verification-pro' === lrm_setting('redirects/registration/action') || 'email-verification-pro-w-redirect' === lrm_setting('redirects/registration/action');
	}
	
	static function init() {

		add_filter( "lrm/mails/registration/body", array('LRM_Pro_User_Verification', 'registration_mail_body__filter'), 9, 4 );

		if ( ! self::link_verification_is_on() ) {
			return;
		}

		add_action( "lrm/registration_successful", array('LRM_Pro_User_Verification', 'registration_successful__action'), 9, 1 );

		//add_filter( 'authenticate', array('LRM_Pro_User_Verification', 'wp_authenticate_check_verification__filter'), 9, 3 );
		add_filter( 'wp_authenticate_user', array('LRM_Pro_User_Verification', 'wp_authenticate_check_verification__filter'), 9, 2 );
		add_action( 'after_password_reset', array(__CLASS__, 'after_password_reset__action'), 10, 2 );

		if ( isset($_GET['lrm-verify']) ) {
			//add_action( "init", array(__CLASS__, 'maybe_do_verification'), 9 );
			self::maybe_do_verification();
		}
	}

	/**
	 * Active the account while the reset password
	 * @param WP_User $user
	 * @param string $password
	 * @since 2.08
	 */
	static function after_password_reset__action($user, $password) {
		if ( in_array('pending', $user->roles) ) {
			// Make not Pending
			$user->remove_role( 'pending' );

			do_action( 'lrm/user_verification_successful', $user );
		}
	}

	/**
	 * @param WP_User $user
	 * @param string $password
	 *
	 * @return WP_User|WP_Error
	 */
	static function wp_authenticate_check_verification__filter($user, $password) {
		if ( $user && in_array('pending', $user->roles) ) {
			return new WP_Error( 'verification_required', lrm_setting('messages_pro/registration/verification_required') );
		}

		return $user;
	}


	/**
	 * @param $user_id
	 * @since 1.18
     */
	static function registration_successful__action($user_id ) {

		$user = get_user_by( 'id', $user_id );
		$user->add_role('pending');

	}

	/**
	 * @param string $mail_body
	 * @param string $user_login
	 * @param array $userdata
	 *
	 * @param WP_User $user
	 *
	 * @return mixed|WP_Error
	 */
	static function registration_mail_body__filter($mail_body, $user_login, $userdata, $user ) {
		global $wpdb;

		$verify_url = '';

		if ( self::link_verification_is_on() ) {

			// Generate something random for a password reset key.
			$key = wp_generate_password( 18, false );

			// Now insert the key, hashed, into the DB.
			if ( empty( $wp_hasher ) ) {
				require_once ABSPATH . WPINC . '/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
			}
			$hashed = time() . ':' . $wp_hasher->HashPassword( $key );

			$key_saved = $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

			if ( false === $key_saved ) {
				return new WP_Error( 'no_password_key_update', __( 'Could not save password reset key to database.' ) );
			}

			$verify_url = site_url( '?lrm-verify=1&key=' . $key . '&login=' . urlencode($user_login) );

			$after_verify_action = lrm_setting('redirects/registration/email-verification-pro-after-action');
			$refer_url = urlencode($_SERVER["HTTP_REFERER"]);
			$redirect_after = $refer_url;

			switch ($after_verify_action) {
				case 'redirect':
					$redirect_after = LRM_Redirects_Manager::get_redirect('registration', $user->ID);
					break;
				case 'default':
					$redirect_after = false;
					break;
				case 'back':
				case 'login':
					// Leave Refer URL
					break;

			}

            $verify_url = add_query_arg('redirect_to', $redirect_after, $verify_url);
		} else {
			$verify_url = 'Verification is not required';
		}


		$mail_body = str_replace(
			array(
				'{{VERIFY_ACCOUNT_URL}}',
			),
			array(
				$verify_url,
			),
			$mail_body
		);

		return $mail_body;
	}
	
	static function maybe_do_verification() {
		if( empty($_GET['key']) || empty($_GET['login']) ) {
			wp_die( "Wrong Verification params. Make sure that you copied right url." );
		}

		$key = $_GET['key'];
		$login = sanitize_user( urldecode($_GET['login']) );

		$user = get_user_by( 'login', $login );

		if ( !$user ) {
			// Do not show message about missing user to do not allow brute force to check if user exists
			wp_die( "Wrong Verification params. Make sure that you copied right url." );
		}

		$message = str_replace(
			array(
				'{{LOGIN_URL}}',
			),
			array(
				wp_login_url(),
			),
			LRM_Settings::get()->setting('messages_pro/registration/verification_completed')
		);

		// Already Confirmed
		if ( $user && ! in_array('pending', $user->roles) ) {
			wp_die( $message );
		}

		$check_key = check_password_reset_key( $key, $login );

		if ( ! is_wp_error( $check_key ) ) {

			// Make not Pending
			$user->remove_role( 'pending' );

			global $wpdb;
			$key_saved = $wpdb->update( $wpdb->users, array( 'user_activation_key' => '' ), array( 'user_login' => $login ) );

			do_action('lrm/user_verification_successful', $user);

			$after_action = lrm_setting('redirects/registration/email-verification-pro-after-action');

            /**
             * Automatic login after user verification
             * @since 1.19
             */
            if ( $user && 'login' !== $after_action ) {
                wp_clear_auth_cookie();
                wp_set_current_user ( $user->ID );
                wp_set_auth_cookie  ( $user->ID );

                do_action('lrm/user_verification/auto_login_done', $user);
            }

            $redirect_to = !empty( $_GET['redirect_to'] ) ? urldecode($_GET['redirect_to']) : '';

            if ( 'back' === $after_action && $redirect_to ) {
                wp_safe_redirect( $redirect_to );
                exit;
            }
            if ( 'login' === $after_action ) {
                wp_safe_redirect( LRM_Pages_Manager::custom_login_url( wp_login_url($redirect_to), $redirect_to ) );
                exit;
            }

            /**
             * If is set redirect url - skip Success screen
             */

            if ( 'redirect' === $after_action && $redirect_url = LRM_Redirects_Manager::get_redirect('registration', $user->ID) ) {
                wp_safe_redirect( $redirect_url );
                exit;
            }


            wp_die( $message, "Success", 200 );

		} else {

			do_action('lrm/user_verification_failed', $user, $check_key);

			// Show errors
			wp_die( implode( $check_key->get_error_messages(), "<br />\n" ) );

		}
	}

}
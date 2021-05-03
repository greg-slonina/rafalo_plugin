<?php

/**
 * Match Captcha
 *
 * @since 1.71
 *
 * Class LRM_Pro_MatchCaptcha
 */
class LRM_Pro_MatchCaptcha {

    /**
     * Add all necessary hooks
     */
    static function init() {

        if ( ! lrm_setting( 'security/recaptcha/site_key' ) || ! lrm_setting( 'security/recaptcha/secret_key' ) ) {
            return;
        }

    }

    /**
     * Render Match Captcha html
     */
    static function render() {

	    echo '<div class="lrm-math-captcha-wrap">';

	    echo '<span class="lrm-math-captcha-label">', lrm_setting('messages_pro/match_captcha/label'), '</span> ';

	    echo '<span class="lrm-math-captcha-content">' . self::generate_captcha_phrase() . '</span>
			</div>';

    }

    /**
     * Validate Match Captcha answer (expiration + token + answer)
     *
     * @return string
     */
    static function validate() {

    	if ( !isset($_POST['lrm-match-expiration'], $_POST['lrm-match-token'], $_POST['lrm-match-value']) ) {
		    wp_send_json_error(array(
			    'message' => 'Invalid Match Captcha params. Please try to reload the page or clear the page cache!',
			    'html' => self::generate_captcha_phrase(),
		    ));
	    }

	    $expiration = absint($_POST['lrm-match-expiration']);

	    if ( time() > $expiration ) {
		    wp_send_json_error(array(
			    'message' => lrm_setting('messages_pro/match_captcha/timeout'),
			    'html'    => self::generate_captcha_phrase(),
			    'action'  => 'refresh_captcha'
		    ));
	    }

	    $match_value = trim($_POST['lrm-match-value']);

	    if ( !$match_value ) {
		    wp_send_json_error(array(
			    'message' => lrm_setting('messages_pro/match_captcha/missing'),
		    ));
	    }

	    $match_value = absint($match_value);

	    $match_token = trim($_POST['lrm-match-token']);

	    if ( !$match_token || 105 !== strlen($match_token) ) {
		    sleep(1);
		    wp_send_json_error(array(
			    'message' => 'Match Captcha token has been missing or invalid!',
			    'code' => 'mt',
		    ));
	    }

	    // #### Verify the Token ####
	    $remote_ip = LRM_Pro_Security::get_user_ip();

	    $expiration_token = substr($match_token, -40, 40);

	    if ( $expiration_token !== self::_generate_expiration_token($expiration) ) {
		    sleep(1);
		    wp_send_json_error(array(
			    'message' => 'Match Captcha token has been missing or invalid!',
			    'code' => 'expt',
		    ));
	    }

	    $answer_token = substr($match_token, 0, 40);

	    if ( $answer_token !== self::_generate_answer_token($match_value) ) {
		    wp_send_json_error(array(
			    'message' => lrm_setting('messages_pro/match_captcha/invalid'),
			    'code' => 'anst',
		    ));
	    }

        return true;

    }

	/**
	 * Encode chars.
	 *
	 * @param string $string
	 * @return string
	 */
	static function encode_operation( $string ) {
		$chars = str_split( $string );
		$seed = mt_rand( 0, (int) abs( crc32( $string ) / strlen( $string ) ) );

		foreach ( $chars as $key => $char ) {
			$ord = ord( $char );

			// ignore non-ascii chars
			if ( $ord < 128 ) {
				// pseudo "random function"
				$r = ($seed * (1 + $key)) % 100;

				if ( $r > 60 && $char !== '@' ) {

				} // plain character (not encoded), if not @-sign
				elseif ( $r < 45 )
					$chars[$key] = '&#x' . dechex( $ord ) . ';'; // hexadecimal
				else
					$chars[$key] = '&#' . $ord . ';'; // decimal (ascii)
			}
		}

		return sprintf(
			'<input type="text" size="2" length="2" class="has-border" value="%s" disabled readonly/>',
			implode( '', $chars )
		);
	}

	/**
	 * Generate captcha phrase.
	 *
	 * @return array
	 */
	static function generate_captcha_phrase() {
		$ops = array(
			'addition'		 => '+',
			'subtraction'	 => '&#8722;',
			'multiplication' => '&#215;',
			'division'		 => '&#247;',
		);

		$operations = $groups = array();
		$input = '<input type="text" size="2" length="2" class="lrm-match-value has-border" name="lrm-match-value" value="" placeholder="?" required aria-required="true" maxlength="2"/>';

		$Options = [
			'mathematical_operations' => array(
				'addition'		 => true,
				'subtraction'	 => true,
				'multiplication' => false,
				'division'		 => false
			),
			'groups' => array(
				'numbers'	 => true,
				'words'		 => false
			),
			'time' => 300,
		];

		// available operations
		foreach ( $Options['mathematical_operations'] as $operation => $enable ) {
			if ( $enable === true )
				$operations[] = $operation;
		}

		// available groups
		foreach ( $Options['groups'] as $group => $enable ) {
			if ( $enable === true )
				$groups[] = $group;
		}

		// number of groups
		$ao = count( $groups );

		// operation
		$rnd_op = $operations[mt_rand( 0, count( $operations ) - 1 )];
		$number[3] = $ops[$rnd_op];

		// place where to put empty input
		$rnd_input = mt_rand( 0, 2 );

		// which random operation
		switch ( $rnd_op ) {
			case 'addition':
				if ( $rnd_input === 0 ) {
					$number[0] = mt_rand( 1, 10 );
					$number[1] = mt_rand( 1, 89 );
				} elseif ( $rnd_input === 1 ) {
					$number[0] = mt_rand( 1, 89 );
					$number[1] = mt_rand( 1, 10 );
				} elseif ( $rnd_input === 2 ) {
					$number[0] = mt_rand( 1, 9 );
					$number[1] = mt_rand( 1, 10 - $number[0] );
				}

				$number[2] = $number[0] + $number[1];
				break;

			case 'subtraction':
				if ( $rnd_input === 0 ) {
					$number[0] = mt_rand( 2, 10 );
					$number[1] = mt_rand( 1, $number[0] - 1 );
				} elseif ( $rnd_input === 1 ) {
					$number[0] = mt_rand( 11, 99 );
					$number[1] = mt_rand( 1, 10 );
				} elseif ( $rnd_input === 2 ) {
					$number[0] = mt_rand( 11, 99 );
					$number[1] = mt_rand( $number[0] - 10, $number[0] - 1 );
				}

				$number[2] = $number[0] - $number[1];
				break;

			case 'multiplication':
				if ( $rnd_input === 0 ) {
					$number[0] = mt_rand( 1, 10 );
					$number[1] = mt_rand( 1, 9 );
				} elseif ( $rnd_input === 1 ) {
					$number[0] = mt_rand( 1, 9 );
					$number[1] = mt_rand( 1, 10 );
				} elseif ( $rnd_input === 2 ) {
					$number[0] = mt_rand( 1, 10 );
					$number[1] = ($number[0] > 5 ? 1 : ($number[0] === 4 && $number[0] === 5 ? mt_rand( 1, 2 ) : ($number[0] === 3 ? mt_rand( 1, 3 ) : ($number[0] === 2 ? mt_rand( 1, 5 ) : mt_rand( 1, 10 )))));
				}

				$number[2] = $number[0] * $number[1];
				break;

			case 'division':
				$divide = array( 1 => 99, 2 => 49, 3 => 33, 4 => 24, 5 => 19, 6 => 16, 7 => 14, 8 => 12, 9 => 11, 10 => 9 );

				if ( $rnd_input === 0 ) {
					$divide = array( 2 => array( 1, 2 ), 3 => array( 1, 3 ), 4 => array( 1, 2, 4 ), 5 => array( 1, 5 ), 6 => array( 1, 2, 3, 6 ), 7 => array( 1, 7 ), 8 => array( 1, 2, 4, 8 ), 9 => array( 1, 3, 9 ), 10 => array( 1, 2, 5, 10 ) );
					$number[0] = mt_rand( 2, 10 );
					$number[1] = $divide[$number[0]][mt_rand( 0, count( $divide[$number[0]] ) - 1 )];
				} elseif ( $rnd_input === 1 ) {
					$number[1] = mt_rand( 1, 10 );
					$number[0] = $number[1] * mt_rand( 1, $divide[$number[1]] );
				} elseif ( $rnd_input === 2 ) {
					$number[2] = mt_rand( 1, 10 );
					$number[0] = $number[2] * mt_rand( 1, $divide[$number[2]] );
					$number[1] = (int) ($number[0] / $number[2]);
				}

				if ( ! isset( $number[2] ) )
					$number[2] = (int) ($number[0] / $number[1]);

				break;
		}

//		// words
//		if ( $ao === 1 && $groups[0] === 'words' ) {
//			if ( $rnd_input === 0 ) {
//				$number[1] = $this->numberToWords( $number[1] );
//				$number[2] = $this->numberToWords( $number[2] );
//			} elseif ( $rnd_input === 1 ) {
//				$number[0] = $this->numberToWords( $number[0] );
//				$number[2] = $this->numberToWords( $number[2] );
//			} elseif ( $rnd_input === 2 ) {
//				$number[0] = $this->numberToWords( $number[0] );
//				$number[1] = $this->numberToWords( $number[1] );
//			}
//		}
//		// numbers and words
//		elseif ( $ao === 2 ) {
//			if ( $rnd_input === 0 ) {
//				if ( mt_rand( 1, 2 ) === 2 ) {
//					$number[1] = $this->numberToWords( $number[1] );
//					$number[2] = $this->numberToWords( $number[2] );
//				} else
//					$number[$tmp = mt_rand( 1, 2 )] = $this->numberToWords( $number[$tmp] );
//			}
//			elseif ( $rnd_input === 1 ) {
//				if ( mt_rand( 1, 2 ) === 2 ) {
//					$number[0] = $this->numberToWords( $number[0] );
//					$number[2] = $this->numberToWords( $number[2] );
//				} else
//					$number[$tmp = array_rand( array( 0 => 0, 2 => 2 ), 1 )] = $this->numberToWords( $number[$tmp] );
//			}
//			elseif ( $rnd_input === 2 ) {
//				if ( mt_rand( 1, 2 ) === 2 ) {
//					$number[0] = $this->numberToWords( $number[0] );
//					$number[1] = $this->numberToWords( $number[1] );
//				} else
//					$number[$tmp = mt_rand( 0, 1 )] = $this->numberToWords( $number[$tmp] );
//			}
//		}


		// position of empty input
		if ( $rnd_input === 0 )
			$return = $input . ' ' . $number[3] . ' ' . self::encode_operation( $number[1] ) . ' = ' . self::encode_operation( $number[2] );
		elseif ( $rnd_input === 1 )
			$return = self::encode_operation( $number[0] ) . ' ' . $number[3] . ' ' . $input . ' = ' . self::encode_operation( $number[2] );
		elseif ( $rnd_input === 2 )
			$return = self::encode_operation( $number[0] ) . ' ' . $number[3] . ' ' . self::encode_operation( $number[1] ) . ' = ' . $input;

		//$transient_name = 'lrm_';
		$expiration = time() + 300;
		$expiration_token = self::_generate_expiration_token($expiration);
		$answer_token = self::_generate_answer_token($number[$rnd_input]) . self::generate_password(25) . $expiration_token;
		//$session_id = $this->generate_password();

		//set_transient( $transient_name . '_' . $session_id, sha1( AUTH_KEY . $number[$rnd_input] . $session_id . LRM_Pro_Security::get_user_ip(), false ), apply_filters( 'math_captcha_time', Math_Captcha()->options['general']['time'] ) );

		$return .= '<input type="hidden" name="lrm-match-expiration" value="' . esc_attr($expiration) . '"/>';
		$return .= '<input type="hidden" name="lrm-match-token" value="' . esc_attr($answer_token) . '"/>';

		return $return;
	}

	/**
	 * @param $expiration
	 *
	 * @return string
	 */
	private static function _generate_expiration_token( $expiration ) {
		return sha1( AUTH_KEY . $expiration . LRM_Pro_Security::get_user_ip(), false );
	}

	/**
	 * @param $answer
	 *
	 * @return string
	 */
	private static function _generate_answer_token( $answer ) {
		// $_SERVER['REQUEST_URI']
		return sha1( AUTH_KEY . $answer . LRM_Pro_Security::get_user_ip(), false );
	}

	/**
	 * Generate password helper, without wp_rand() call
	 *
	 * @param int $length
	 * @return string
	 */
	private static function generate_password( $length = 50 ) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$password = '';

		for ( $i = 0; $i < $length; $i ++  ) {
			$password .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		return $password;
	}

}
<?php
defined( 'ABSPATH' ) || exit;

/**
 * @since      1.65
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRM_PRO_Roles_Manager {

	public static function init ()
	{
		if ( 'true' !== lrm_setting('user_role/general/on') ) {
			return;
		}

		add_action( "lrm/pre_register_new_user", array(__CLASS__, 'pre_register_new_user__action'), 10 );
		add_action( "lrm/registration_successful", array(__CLASS__, 'registration_successful__action'), 8, 1 );
	}

	/**
	 * Validate POST data
	 */
	static function pre_register_new_user__action() {

		$selected_role = isset($_POST['user_role']) ? absint(trim($_POST['user_role'])) : false;


		if ( false === $selected_role || '' === $selected_role ) {
			wp_send_json_error(array('message' => lrm_setting('messages/registration/no_user_role'), 'for'=>'user_role'));
		}

		$active_roles = self::get_active_roles_flat();
		if ( ! isset($active_roles[$selected_role]) ) {
			wp_send_json_error(array('message' => 'Sorry, but this role does not exists!', 'for'=>'user_role'));
		}

	}
	/**
	 * @param $user_id
	 */
	static function registration_successful__action($user_id ) {

		$selected_role = absint(trim($_POST['user_role']));

		$active_roles = LRM_Field_Roles::_corrected_value( lrm_setting('user_role/general/active_roles') );

		if ( !empty($active_roles['roles'][$selected_role]) ) {

			$user = get_user_by( 'id', $user_id );

			foreach ($active_roles['roles'][$selected_role] as $nkey => $role_to_assign) {
				if ( 0 === $nkey ) {
					$user->set_role( $role_to_assign );
				} else {
					$user->add_role( $role_to_assign );
				}

			}

		}


	}

    /**
     * @return array
     */
    public static function get_active_roles_flat ()
    {

	    //$exists_roles = LRM_Roles_Manager::get_wp_roles_flat();
        $active_roles = LRM_Field_Roles::_corrected_value( lrm_setting('user_role/general/active_roles') );

	    $active_roles_labeled = [];
        foreach ($active_roles['label'] as $active_role_key => $active_role_label) {
        	//if ( isset($exists_roles[$active_role]) ) {
            $active_roles_labeled[$active_role_key] = $active_role_label;
	        //}
        }

        return $active_roles_labeled;

    }

}



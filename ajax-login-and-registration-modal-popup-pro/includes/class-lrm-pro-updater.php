<?php

/**
 * Update related functions and actions.
 * Class LRM_Pro_Updater
 *
 * @since 1.51
 * @updated 1.60 (big update)
 */

defined( 'ABSPATH' ) || exit;

/**
 * LRM_Pro_Updater Class.
 */
class LRM_Pro_Updater extends LRM_Updater_Abstract {

    /**
     * DB updates and callbacks that need to be run per version.
     *
     *
     * @var array
     */
    protected $db_updates = array(
        '1.17' => array(
            '_update_1_17',
        ),
        '1.60' => array(
            '_update_1_60',
        ),
    );

    /**
     * Run the class
     */
    public static function init() {
        new LRM_Pro_Updater();
    }

    public function __construct()
    {
        parent::__construct('lrm_pro', 'lrm_pro_version', LRM_PRO_VERSION);
    }

    /**
     * Update to version 1.17
     */
    public function _update_1_17() {

        // Add new role without Caps
        if ( ! get_role('pending') ) {
            add_role('pending', 'Pending');
        }
    }

    /**
     * Update to version 1.60
     */
    public function _update_1_60() {

       lrm_log( "_update_1_60 runned" );
    }
}

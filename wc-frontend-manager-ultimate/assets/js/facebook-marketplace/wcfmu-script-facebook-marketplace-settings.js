jQuery(document).ready( function($) {
    if( $("#wcfm_facebook_marketplace_settings_form .wcfm-select2").length > 0 ) {
        $("#wcfm_facebook_marketplace_settings_form .wcfm-select2").select2({
            placeholder: wcfm_dashboard_messages.choose_select2 + ' ...'
        });
    }

    // Save Settings
    $('#wcfm_facebook_marketplace_settings_save_button').click(function(event) {
        event.preventDefault();

        $('.wcfm_submit_button').hide();

        // Validations
        $('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
        $wcfm_is_valid_form = true;
        $( document.body ).trigger( 'wcfm_form_validate', $('#wcfm_facebook_marketplace_settings_form') );
        $is_valid = $wcfm_is_valid_form;

        if($is_valid) {
            $('#wcfm_facebook_marketplace_settings_form').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            var data = {
                action      : 'wcfm_ajax_controller',
                controller  : 'wcfm-facebook-marketplace-settings',
                form        : $('#wcfm_facebook_marketplace_settings_form').serialize(),
            }
            $.post(wcfm_params.ajax_url, data, function(response) {
                if(response) {
                    $response_json = $.parseJSON(response);
                    $('.wcfm-message').html('').removeClass('wcfm-success').removeClass('wcfm-error').slideUp();
                    wcfm_notification_sound.play();
                    if($response_json.status) {
                        $('#wcfm_facebook_marketplace_settings_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
                        if( $response_json.file ) $('#wcfm_custom_css-css').attr( 'href', $response_json.file );
                    } else {
                        $('#wcfm_facebook_marketplace_settings_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
                    }
                    wcfmMessageHide();
                    $('#wcfm_facebook_marketplace_settings_form').unblock();
                    $('.wcfm_submit_button').show();
                }
            });
        } else {
            $('.wcfm_submit_button').show();
        }
    });
});

(function ($) {

    /**
     * TIPS
     */
    var tiptip_args = {
        'attribute': 'data-tip',
        'fadeIn'   : 50,
        'fadeOut'  : 50,
        'delay'    : 200
    };

    $('.fbc-tips').tipTip(tiptip_args);

    /**
     * OPERATOR AVATAR MANAGEMENT
     */
    if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {

        //upload
        var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;

        // preview
        $('#fbc_operator_avatar').change(function () {

            var option = $('option:selected', '#fbc_operator_avatar_type').val();

            if (option == 'image') {

                var url = $(this).val();
                var re = new RegExp("(http|ftp|https)://[a-zA-Z0-9@?^=%&amp;:/~+#-_.]*.(gif|jpg|jpeg|png|ico)");

                var preview = $('.fbc-op-avatar .preview img');
                if (re.test(url)) {
                    preview.attr('src', url)

                } else {
                    preview.attr('src', '');
                }

            }

        }).change();

        $(document).on('click', '#fbc_operator_avatar_button', function (e) {
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $('#fbc_operator_avatar_button');
            var field = $('#fbc_operator_avatar');
            var preview = $('.fbc-op-avatar .preview img');
            _custom_media = true;

            wp.media.editor.send.attachment = function (props, attachment) {
                if (_custom_media) {

                    field.val(attachment.url);
                    preview.attr('src', attachment.url).change();

                } else {

                    return _orig_send_attachment.apply(this, [props, attachment]);

                }

            };

            wp.media.editor.open(button);
            return false;
        });

    }

    $('.fbc-op-avatar .add_media').on('click', function () {
        _custom_media = false;
    });

})(jQuery);

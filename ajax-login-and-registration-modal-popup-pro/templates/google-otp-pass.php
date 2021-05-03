<div class="fieldset fieldset--otp">
    <?php $pass_label = esc_attr( lrm_setting('messages_pro/integrations/googleauthenticator_label', true) ); ?>
    <label class="image-replace lrm-password lrm-ficon-key" title="<?= $pass_label; ?>"></label>
    <input name="googleotp" class="full-width has-padding has-border" type="password" aria-label="<?= $pass_label; ?>" placeholder="<?= $pass_label; ?>" required value="">
    <span class="lrm-error-message"></span>
    <?php if ( apply_filters('lrm/login_form/allow_show_pass', true) ): ?>
        <span class="hide-password lrm-ficon-eye" data-show="<?php echo lrm_setting('messages/other/show_pass'); ?>" data-hide="<?php echo lrm_setting('messages/other/hide_pass'); ?>" aria-label="<?php echo lrm_setting('messages/other/show_pass'); ?>"></span>
    <?php endif; ?>
</div>

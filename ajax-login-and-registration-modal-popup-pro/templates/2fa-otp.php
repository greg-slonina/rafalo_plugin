<div class="fieldset fieldset--otp">
    <?php $pass_label = esc_attr( lrm_setting('messages_pro/integrations/googleauthenticator_label', true) ); ?>
    <label class="image-replace lrm-password lrm-ficon-key" title="<?= $pass_label; ?>"></label>
    <input name="<?php echo isset($field_name) ? $field_name : 'otp'; ?>" class="full-width has-padding has-border" type="text" aria-label="<?= $pass_label; ?>" placeholder="<?= $pass_label; ?>" required value="">
    <span class="lrm-error-message"></span>
</div>

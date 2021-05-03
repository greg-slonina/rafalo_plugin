<?php

/**
 * Reset Control
 *
 */
class LRM_Pro_WP_Customize_Control_Button extends WP_Customize_Control {
	public $type = 'button';

	public function enqueue(){
		wp_enqueue_script(
			'lrm-customizer-admin',
			LRM_PRO_URL . '/assets/lrm-customizer.js',
			array(  ),
			LRM_PRO_VERSION,
			true
		);
	}

	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div>
				<button type="button" class="button-secondary lrm-open-modal"><?php echo $this->description; ?></button>
			</div>
		</label>
		<?php
	}
}
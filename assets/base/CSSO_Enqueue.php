<?php

namespace CloudSingleSignOn\base;

class CSSO_Enqueue {

	function csso_register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'wcs_saml_enqueue' ) );
	}

	public function wcs_saml_enqueue() {
		wp_register_style( 'bootstrap_css', csso_get_plugin_url() . '/assets/includes/libs/bootstrap-4.6.2/css/bootstrap.min.css' );
		wp_register_style( 'plugin_style', csso_get_plugin_url() . '/assets/includes/css/plugin-styles.css' );
		wp_register_style( 'leave_review_style', csso_get_plugin_url() . '/assets/includes/css/leave-review.css' );
		wp_register_script( 'bootstrap_bundle', csso_get_plugin_url() . '/assets/includes/libs/bootstrap-4.6.2/js/bootstrap.bundle.min.js' );

		wp_enqueue_style( 'leave_review_style' );

		if ( csso_is_current_page_plugin() ) {
			wp_enqueue_style( 'bootstrap_css' );
			wp_enqueue_style( 'plugin_style' );
			wp_enqueue_script( 'bootstrap_bundle' );
			wp_enqueue_script( 'jquery' );
		}
	}
}

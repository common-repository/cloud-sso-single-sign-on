<?php

namespace CloudSingleSignOn;

class CSSO_Init {

	public static function csso_register_services() {

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		foreach ( self::csso_get_services() as $service ) {
			if ( method_exists( $service, 'csso_register' ) ) {
				$service->csso_register();
			}
		}
	}

	public static function csso_get_services(): array {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'assets/CSSO_utils.php';
		require csso_get_plugin_path() . 'assets/base/CSSO_services.php';
		require csso_get_plugin_path() . 'assets/templates/CSSO_components.php';
		global $services;

		return $services;

	}
}

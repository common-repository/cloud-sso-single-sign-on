<?php

namespace CloudSingleSignOn\base;

class CSSO_Uninstall {


	static function csso_destroy_plugin() {
		self::csso_destroy_options();
		self::csso_destroy_tables();
	}

	private static function csso_destroy_options() {
		foreach ( wp_load_alloptions() as $option_name => $value ) {
			if ( strpos( $option_name, csso_get_plugin_prefix() ) !== false ) {
				delete_option( $option_name );
			}
		}
	}

	private static function csso_destroy_tables() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . csso_get_plugin_prefix() . "environments" );
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . csso_get_plugin_prefix() . "providers" );
	}
}
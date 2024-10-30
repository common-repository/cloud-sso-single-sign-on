<?php

namespace CloudSingleSignOn\base;

class CSSO_ErrorHandler {

	public static function csso_get_last_error() {
		return get_option( csso_get_plugin_prefix() . 'last_error' );
	}

	public static function csso_set_error( $error_message ): bool {
		return update_option( csso_get_plugin_prefix() . 'last_error', $error_message );
	}

	public static function csso_reset_errors() {
		delete_option( csso_get_plugin_prefix() . 'last_error' );
	}
}

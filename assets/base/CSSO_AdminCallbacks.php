<?php

namespace CloudSingleSignOn\base;

class CSSO_AdminCallbacks {
	public function csso_dashboard() {
		return require_once csso_get_plugin_path() . 'assets/templates/CSSO_dashboard.php';
	}
}
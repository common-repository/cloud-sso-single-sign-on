<?php

namespace CloudSingleSignOn\base;

class CSSO_Activate {

	static function csso_activate() {
		if ( ! boolval( get_option( csso_get_plugin_prefix() . 'is_plugin_activated' ) ) ) {
			CSSO_Activate::csso_create_environments_table();
			CSSO_Activate::csso_create_providers_table();
			CSSO_Activate::csso_generate_service_provider_metadata_credentials();
			CSSO_Activate::csso_set_login_button_styles();
			CSSO_Activate::csso_set_plugin_owner();
			update_option( csso_get_plugin_prefix() . 'is_plugin_activated', true );
		}
	}

	private static function csso_create_environments_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . csso_get_plugin_prefix() . 'environments';

		$sql = "CREATE TABLE $table_name 
        ( 
            id INT (10) AUTO_INCREMENT,
            name varchar(100) NOT NULL UNIQUE, 
            url varchar(255),
            PRIMARY KEY  (id)
        )";
		$wpdb->query( $sql );


		//default env
		$wpdb->insert( $table_name, [ 'name' => 'default' ] );

		//default admin env
		$dev_env    = [ 'name' => 'dev', 'url' => csso_get_hostname_by_url( get_home_url() ) ];
		$dev_env_id = $wpdb->insert( $table_name, $dev_env );
		$dev_env    = array_merge( [ 'id' => $dev_env_id ], $dev_env );

		update_option( csso_get_plugin_prefix() . 'admin_environment', json_encode( $dev_env ) );
		update_option( csso_get_plugin_prefix() . 'active_editable_environment', $dev_env['name'] );

	}

	private static function csso_create_providers_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . csso_get_plugin_prefix() . 'providers';

		$sql = "CREATE TABLE $table_name 
        ( 
            id INT (10) AUTO_INCREMENT,
            env_id int NOT NULL,
            provider varchar(150) NOT NULL,
            configured_date text,
            name varchar(255) NOT NULL,
            entity_id_or_issuer varchar(255) NOT NULL,
            custom_sp_entity_id text,
            saml_login_url varchar(255) NOT NULL,
            saml_logout_url varchar (255),
            x509_certificates text NOT NULL,
            attribute_mapping text,
            custom_attributes text,
            role_mapping text,
            avatar_mapping text,
            PRIMARY  KEY  (id)
        )";
		$wpdb->query( $sql );
	}

	private static function csso_generate_service_provider_metadata_credentials() {
		update_option( csso_get_plugin_prefix() . 'sp_entity_id', get_home_url() );
		update_option( csso_get_plugin_prefix() . 'sp_acs_url', get_home_url() );
		update_option( csso_get_plugin_prefix() . 'sp_slo_url', get_home_url() );
		update_option( csso_get_plugin_prefix() . 'sp_name_id_format', 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' );
		update_option( csso_get_plugin_prefix() . 'sp_metadata_url', get_home_url() . '?option=sp_metadata' );
		update_option( csso_get_plugin_prefix() . 'sp_metadata_download_url', get_home_url() . '?option=sp_metadata&download=true' );
		update_option( csso_get_plugin_prefix() . 'sp_x509_cert_download_url', get_home_url() . '?option=sp_x509_certificate' );
		self::csso_generate_x509_certificate();
	}

	private static function csso_generate_x509_certificate() {

		openssl_pkey_export( openssl_pkey_new( array(
			"private_key_bits" => 2048,
			"private_key_type" => OPENSSL_KEYTYPE_RSA,
		) ), $private_key )
		and update_option( csso_get_plugin_prefix() . 'sp_x509_private_key', $private_key );

		$dn = array(
			"countryName"            => "UK",
			"stateOrProvinceName"    => "Buckinghamshire",
			"localityName"           => "Gerrards Cross",
			"organizationName"       => "Cloud Infrastructure Services Ltd",
			"organizationalUnitName" => "IT Dept",
			"commonName"             => "Cloud Infrastructure Services",
			"emailAddress"           => "Andrew@cloudinfrastructureservices.co.uk"
		);

		$csr = openssl_csr_new( $dn, $private_key, array( 'digest_alg' => 'sha256' ) );

		openssl_x509_export(
			openssl_csr_sign(
				$csr,
				null,
				$private_key,
				3650,
				array( 'digest_alg' => 'sha256' )
			), $crt_out
		)
		and update_option( csso_get_plugin_prefix() . 'sp_x509_certificate', $crt_out );
	}

	private static function csso_set_login_button_styles() {
		update_option( csso_get_plugin_prefix() . 'login_button_styles', json_encode( csso_get_login_button_default_styles() ) );
	}

	private static function csso_set_plugin_owner() {
		update_option( csso_get_plugin_prefix() . 'plugin_owner', json_encode( wp_get_current_user() ) );
		update_option( csso_get_plugin_prefix() . 'plugin_activation_date', date( 'Y-m-d' ) );
	}
}

<?php

namespace CloudSingleSignOn\base;

abstract class CSSO_AttributeMapping {
	const BaseAttributes = 'attribute_mapping';
	const CustomAttributes = 'custom_attributes';
	const RoleMapping = 'role_mapping';
	const AvatarMapping = 'avatar_mapping';
}

class CSSO_ProvidersManager {
	/**
	 * @var CSSO_EnvironmentService
	 */
	private $environment_service;
	private $table_name;
	/**
	 * @var \wpdb
	 */
	private $wpdb;
	private $current_environment;
	private $configured_providers;

	public function csso_register() {
		global $environment_service;
		global $wpdb;

		$this->environment_service = $environment_service;
		$this->wpdb                = $wpdb;
		$this->current_environment = $environment_service->csso_get_current_environment();
		$this->csso_set_table_name();
		$this->csso_set_configured_providers();
	}

	private function csso_set_table_name() {
		$this->table_name = $this->wpdb->prefix . csso_get_plugin_prefix() . 'providers';
	}

	private function csso_set_configured_providers() {
		$all_providers = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE env_id = %d ORDER BY provider", $this->current_environment['id'] ), ARRAY_A );
		usort($all_providers, function ($a, $b) {
			$t1 = strtotime($a['configured_date']);
			$t2 = strtotime($b['configured_date']);
			return $t1 - $t2;
		});

		$all_providers = array_map(function ($provider){
			$provider['x509_certificates'] = json_decode($provider['x509_certificates'], true);
			return $provider;
		}, $all_providers);

		if (!wpcsso_fs()->is_plan_or_trial('enterprise') && count($all_providers) > 1) {
			$this->configured_providers = $this->csso_downgrade_identity_providers($all_providers);
			return;
		}
		$this->configured_providers = $all_providers;
	}

	public function csso_add_new($provider_slug, $name, $issuer, $login_url, $logout_url, $certificates ) {
		$this->wpdb->insert( $this->table_name, array(
				'env_id'              => $this->current_environment['id'],
				'configured_date'     => date("Y-m-d H:i:s"),
				'provider'            => $provider_slug,
				'name'                => $name,
				'entity_id_or_issuer' => $issuer,
				'saml_login_url'      => $login_url,
				'saml_logout_url'     => $logout_url,
				'x509_certificates'    => json_encode($certificates),
				'role_mapping'        => json_encode( [
					'default_role' => 'subscriber'
				] )
			)
		);
	}

	public function csso_create_or_replace( $provider ) {
		$this->wpdb->replace( $this->table_name, $provider );
	}

	public function csso_delete_identity_provider( $provider_id ) {
		$this->wpdb->delete( $this->table_name, array(
				'id' => $provider_id,
			)
		);
	}

	private function csso_delete_identity_providers(array $ids) {
		$id_placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
		$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->table_name} WHERE id IN (" . $id_placeholders . ")", $ids ) );
	}

	public function csso_is_config_exist( $provider_slug ): bool {
		foreach ( $this->configured_providers as $conf_provider ) {
			if ( $conf_provider['provider'] == $provider_slug ) {
				return true;
			}
		}

		return false;
	}

	public function csso_get_config_from_selected() {
		global $supported_providers;
		$selected_provider = $supported_providers->csso_get_selected();
		foreach ( $this->configured_providers as $conf_provider ) {
			if ( $conf_provider['provider'] == $selected_provider['slug'] ) {
				return $conf_provider;
			}
		}

		return null;
	}

	public function csso_get_one_by( $column, $value ) {
		return csso_search_in_array( $this->configured_providers, strval($column), strval($value) );
	}

	public function csso_update_attribute_mapping( string $mapping_key, array $data, int $provider_id ) {
		$this->wpdb->update( $this->table_name, [ $mapping_key => json_encode( $data ) ], [ 'id' => $provider_id ] );
	}

	public function csso_update($provider_slug, $name, $issuer, $login_url, $logout_url, $certificates ) {
		$data  = [
			'name'                => $name,
			'entity_id_or_issuer' => $issuer,
			'saml_login_url'      => $login_url,
			'saml_logout_url'     => $logout_url,
			'x509_certificates'    => json_encode($certificates)
		];
		$where = [ 'provider' => $provider_slug, 'env_id' => $this->current_environment['id'] ];
		$this->wpdb->update( $this->table_name, $data, $where );
	}

	public function csso_update_provider_field(string $field_name, string $new_value, string $provider_slug) {
		$data  = [
			$field_name => $new_value,
		];
		$where = [ 'provider' => $provider_slug, 'env_id' => $this->current_environment['id'] ];
		$this->wpdb->update( $this->table_name, $data, $where );
	}

	public function csso_get_attribute_mapping( string $mapping_key, $provider_id ) {
		foreach ( $this->configured_providers as $conf_provider ) {
			if ( $conf_provider['id'] == $provider_id ) {
				return json_decode( $conf_provider[ $mapping_key ], true );
			}
		}

		return null;
	}

	public function csso_get_config_by_env_id( int $env_id ) {
		return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE env_id = %d", $env_id ), ARRAY_A );
	}

	public function csso_get_providers_shortcodes(): array {
		return array_keys( $this->csso_get_providers_shortcodes_callbacks() );
	}

	public function csso_get_providers_shortcodes_callbacks() {
		return array_reduce( $this->csso_get_all_by_current_env(), function ( $curr, $next ) {
			$curr[ strtoupper( "saml_{$next['provider']}_button" ) ] = function () use ( $next ) {
				return csso_login_widget( $next, home_url(), csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'buttons_as_short_code' ) ) );
			};

			return $curr;
		}, [] );
	}

	public function csso_get_all_by_current_env() {
		return $this->configured_providers;
	}

	public function csso_generate_provider_login_url( $provider ): string {
		$entity_id = $this->csso_get_provider_sp_entity_id( $provider );
		$authnRequest = new \LightSaml\Model\Protocol\AuthnRequest();
		$authnRequest
			->setAssertionConsumerServiceURL( get_option( csso_get_plugin_prefix() . 'sp_acs_url' ) )
			->setProtocolBinding( \LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST )
			->setID( \LightSaml\Helper::generateID() )
			->setIssueInstant( new \DateTime() )
			->setDestination( $provider['saml_login_url'] )
			->setIssuer( new \LightSaml\Model\Assertion\Issuer( $entity_id ) );

		$bindingFactory  = new \LightSaml\Binding\BindingFactory();
		$redirectBinding = $bindingFactory->create( \LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT );

		$messageContext = new \LightSaml\Context\Profile\MessageContext();
		$messageContext->setMessage( $authnRequest );


		if ( csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'sp_enable_signing' ) ) ) {
			$certificate = \LightSaml\Credential\X509Certificate::asString( get_option( csso_get_plugin_prefix() . 'sp_x509_certificate' ) );
			$privateKey  = \LightSaml\Credential\KeyHelper::createPrivateKey( get_option( csso_get_plugin_prefix() . 'sp_x509_private_key' ), '' );
			$authnRequest->setSignature( new \LightSaml\Model\XmlDSig\SignatureWriter( $certificate, $privateKey ) );
		}

		/** @var \Symfony\Component\HttpFoundation\RedirectResponse $httpResponse */
		$httpResponse = $redirectBinding->send( $messageContext );

		return $httpResponse->getTargetUrl();
	}

	private function csso_downgrade_identity_providers($all_providers): array {
		$first_configured = current($all_providers);
		$ids_to_delete = array_map(function ($provider) {
			return $provider['id'];
		}, array_filter($all_providers, function ($provider) use ($first_configured) {
			return $provider['id'] !== $first_configured['id'];
		}));
		$this->csso_delete_identity_providers($ids_to_delete);
		return [$first_configured];
	}

	private function csso_get_provider_sp_entity_id( $provider ) {
		if (empty($provider['custom_sp_entity_id'])) {
			return get_option( csso_get_plugin_prefix() . 'sp_entity_id' );
		}
		return $provider['custom_sp_entity_id'];
	}
}


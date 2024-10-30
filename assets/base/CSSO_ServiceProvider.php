<?php

namespace CloudSingleSignOn\base;

class CSSO_ServiceProvider {


	static function csso_get_xml_metadata() {
		$enable_signing = csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'sp_enable_signing' ));
		$enable_encryption = csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'sp_enable_encryption' ));
		$entityDescriptor = new \LightSaml\Model\Metadata\EntityDescriptor();
		$entityDescriptor
			->setID( \LightSaml\Helper::generateID() )
			->setEntityID( get_option( csso_get_plugin_prefix() . 'sp_entity_id' ) );

		$entityDescriptor->addItem(
			$spSsoDescriptor = ( new \LightSaml\Model\Metadata\SpSsoDescriptor() )
				->setWantAssertionsSigned( true )
				->setAuthnRequestsSigned( $enable_signing )
				->addNameIDFormat( get_option( csso_get_plugin_prefix() . 'sp_name_id_format' ) )
		);

		if ( $enable_signing ) {
			$spSsoDescriptor->addKeyDescriptor(
				$keyDescriptor = ( new \LightSaml\Model\Metadata\KeyDescriptor() )
					->setUse( \LightSaml\Model\Metadata\KeyDescriptor::USE_SIGNING )
					->setCertificate( \LightSaml\Credential\X509Certificate::asString( get_option( csso_get_plugin_prefix() . 'sp_x509_certificate' ) ) )
			);
		}

		if ( $enable_encryption ) {
			$spSsoDescriptor->addKeyDescriptor(
				$keyDescriptor = ( new \LightSaml\Model\Metadata\KeyDescriptor() )
					->setUse( \LightSaml\Model\Metadata\KeyDescriptor::USE_ENCRYPTION )
					->setCertificate( \LightSaml\Credential\X509Certificate::asString( get_option( csso_get_plugin_prefix() . 'sp_x509_certificate' ) ) )
			);
		}

		$spSsoDescriptor->addAssertionConsumerService(
			$acs = ( new \LightSaml\Model\Metadata\AssertionConsumerService() )
				->setBinding( \LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST )
				->setLocation( get_option( csso_get_plugin_prefix() . 'sp_acs_url' ) )
		);

		$spSsoDescriptor->addSingleLogoutService(
			$slo = ( new \LightSaml\Model\Metadata\SingleLogoutService() )
				->setBinding( \LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT )
				->setLocation( get_option( csso_get_plugin_prefix() . 'sp_slo_url' ) )
		);

		if ( get_option( csso_get_plugin_prefix() . 'sp_org_name' ) ) {
			$entityDescriptor->addOrganization(
				$org = ( new \LightSaml\Model\Metadata\Organization() )
					->setOrganizationName( get_option( csso_get_plugin_prefix() . 'sp_org_name' ) )
					->setOrganizationDisplayName( get_option( csso_get_plugin_prefix() . 'sp_org_display_name' ) )
					->setOrganizationURL( get_option( csso_get_plugin_prefix() . 'sp_org_url' ) )
			);
		}

		if ( get_option( csso_get_plugin_prefix() . 'sp_technical_name' ) ) {
			$entityDescriptor->addContactPerson(
				$org = ( new \LightSaml\Model\Metadata\ContactPerson() )
					->setContactType( 'technical' )
					->setGivenName( get_option( csso_get_plugin_prefix() . 'sp_technical_name' ) )
					->setEmailAddress( get_option( csso_get_plugin_prefix() . 'sp_technical_email' ) )
			);
		}

		if ( get_option( csso_get_plugin_prefix() . 'sp_support_name' ) ) {
			$entityDescriptor->addContactPerson(
				$org = ( new \LightSaml\Model\Metadata\ContactPerson() )
					->setContactType( 'support' )
					->setGivenName( get_option( csso_get_plugin_prefix() . 'sp_support_name' ) )
					->setEmailAddress( get_option( csso_get_plugin_prefix() . 'sp_support_email' ) )
			);
		}

		$serializationContext = new \LightSaml\Model\Context\SerializationContext();
		$entityDescriptor->serialize( $serializationContext->getDocument(), $serializationContext );

		return $serializationContext->getDocument()->saveXML();
	}

	public static function csso_update_metadata($home_url) {
		update_option( csso_get_plugin_prefix() . 'sp_entity_id', $home_url );
		update_option( csso_get_plugin_prefix() . 'sp_acs_url', $home_url );
		update_option( csso_get_plugin_prefix() . 'sp_slo_url', $home_url );
		update_option( csso_get_plugin_prefix() . 'sp_name_id_format', 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' );
		update_option( csso_get_plugin_prefix() . 'sp_metadata_url', $home_url . '?option=sp_metadata' );
		update_option( csso_get_plugin_prefix() . 'sp_metadata_download_url', $home_url . '?option=sp_metadata&download=true' );
		update_option( csso_get_plugin_prefix() . 'sp_x509_cert_download_url', $home_url . '?option=sp_x509_certificate' );
	}
}
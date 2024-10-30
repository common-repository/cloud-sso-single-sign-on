<?php

namespace CloudSingleSignOn\base;

use LightSaml\Model\Assertion\Assertion;

abstract class CSSO_AssertionType {
	const UNECRYPTED = 'unencrypted';
	const ENCRYPTED = 'encrypted';
}

class CSSO_ResponseValidator {

	private $response;
	/**
	 * @var Assertion |null
	 */
	private $assertion;
	private $assertion_type;
	private $provider;
	private $name_id_format;
	private $name_id_value;
	private $attributes;
	private $last_error;
	/**
	 * @var CSSO_ProvidersManager
	 */
	private $providers_manager;
	/**
	 * @var string|null
	 */
	private $session_index;


	public function __construct() {
		global $providers_manager;
		$this->providers_manager = $providers_manager;
		$this->response          = $this->csso_get_saml_response();
	}

	private function csso_get_saml_response() {
		$request        = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
		$bindingFactory = new \LightSaml\Binding\BindingFactory();
		$messageContext = new \LightSaml\Context\Profile\MessageContext();
		$binding        = $bindingFactory->getBindingByRequest( $request );
		$binding->receive( $request, $messageContext );

		return $messageContext->asResponse();
	}

	private function csso_validate_response() {
		if ($error_message = $this->response->getStatus()->getStatusMessage()) {
			$this->csso_set_error($error_message);
		}
	}

	public function csso_validate() {
		$this->csso_validate_response();
		$this->csso_parse_assertion();
		$this->csso_validate_assertion();
	}

	private function csso_parse_assertion(): void {
		if ($this->last_error) return;
		if ( $this->response->getFirstEncryptedAssertion() ) {
			if ( csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'sp_enable_encryption' ) ) ) {
				$this->assertion      = $this->csso_decrypt_assertion( $this->response->getFirstEncryptedAssertion() );
				$this->assertion_type = CSSO_AssertionType::ENCRYPTED;

				return;
			}
			$this->csso_set_error( 'The response is encrypted, please enable encryption on the service provider settings page!' );

			return;
		}

		if ( $this->response->getFirstAssertion() ) {
			$this->assertion      = $this->response->getFirstAssertion();
			$this->assertion_type = CSSO_AssertionType::UNECRYPTED;

			return;
		}

		$this->csso_set_error( 'Cant find saml assertion' );
	}

	private function csso_decrypt_assertion( ?\LightSaml\Model\Assertion\EncryptedElement $assertion ): ?Assertion {
		$credential = new \LightSaml\Credential\X509Credential(
			\LightSaml\Credential\X509Certificate::asString( get_option( csso_get_plugin_prefix() . 'sp_x509_certificate' ) ),
			\LightSaml\Credential\KeyHelper::createPrivateKey( get_option( csso_get_plugin_prefix() . 'sp_x509_private_key' ), '' )
		);

		$decryptDeserializeContext = new \LightSaml\Model\Context\DeserializationContext();
		/** @var \LightSaml\Model\Assertion\EncryptedAssertionReader $reader */
		$reader = $assertion;

		try {
			return $reader->decryptMultiAssertion( [ $credential ], $decryptDeserializeContext );
		} catch ( \Exception $ex ) {
			$this->csso_set_error( 'Invalid decryption certificate' );

			return null;
		}
	}

	private function csso_set_error( $error_message ) {
		$this->last_error = $error_message;
	}

	private function csso_validate_assertion() {
		if ($this->last_error) return;
		if ( ! $provider = $this->providers_manager->csso_get_one_by( 'entity_id_or_issuer', $this->assertion->getIssuer()->getValue() ) ) {
			$this->csso_set_error( 'Cant find provider in assertion' );

			return;
		}
		$this->provider = $provider;

		if ( ! $this->csso_validate_signature( $this->assertion, $this->response, $this->provider ) ) {
			$this->csso_set_error( 'Invalid x509 certificate' );

			return;
		}

		if ( ! $name_id_format = $this->assertion->getSubject()->getNameID() ) {
			$this->csso_set_error( 'Cant detect name id format' );

			return;
		}
		$this->name_id_format = $name_id_format->getFormat();
		$this->name_id_value  = $name_id_format->getValue();
		$this->session_index  = $this->assertion->getFirstAuthnStatement()->getSessionIndex();

		if ( ! $this->csso_validate_name_id_format( $this->assertion ) ) {
			$this->csso_set_error( 'Invalid name id format. Name Id format must be ' . get_option( csso_get_plugin_prefix() . 'sp_name_id_format' ) );

			return;
		}

		if ( $attributes = $this->assertion->getFirstAttributeStatement() ) {
			$attributes = $this->csso_reduce_attributes( $attributes->getAllAttributes() );
		} else {
			$attributes = [];
		}
		$this->attributes = $attributes;
	}

    private function csso_validate_signature(?Assertion $assertion, ?\LightSaml\Model\Protocol\Response $response, $provider): bool {
        return boolval(count(array_filter($provider['x509_certificates'], function ($cert) use ($assertion, $response) {
            return $this->csso_validate_certificate($assertion, $response, $cert);
        })));
    }

	private function csso_validate_certificate(?Assertion $assertion, ?\LightSaml\Model\Protocol\Response $response, $cert ): bool {
		try {
			if ( ! openssl_get_publickey( $cert ) ) {
				return false;
			}
			$key = \LightSaml\Credential\KeyHelper::createPublicKey(
				\LightSaml\Credential\X509Certificate::asString( $cert )
			);

			/** @var \LightSaml\Model\XmlDSig\SignatureXmlReader $signatureReader */
			$signatureReader = $assertion->getSignature() ?? $response->getSignature();

			return $signatureReader->validate( $key );
		} catch ( \Exception $ex ) {
			return false;
		}
	}

	private function csso_validate_name_id_format( ?Assertion $assertion ): bool {
		$sp_name_id_format       = get_option( csso_get_plugin_prefix() . 'sp_name_id_format' );
		$name_id_format_from_idp = $assertion->getSubject()->getNameID()->getFormat();

		return strtolower( $sp_name_id_format ) === strtolower( $name_id_format_from_idp );
	}

	private function csso_reduce_attributes( array $attributes ): array {

		$get_attribute_value = function ( $attr ) {
			$all_values = $attr->getAllAttributeValues();
			return count( $all_values ) > 1 ? $all_values : $attr->getFirstAttributeValue();
		};

		return array_reduce( $attributes, function ( $curr, $next ) use ( $get_attribute_value ) {
			$attr_name                 = explode( "/", $next->getName() );
			$curr[ end( $attr_name ) ] = $get_attribute_value( $next );

			return $curr;
		}, [] );
	}

	public function csso_get_assertion_type(): string {
		return $this->assertion_type;
	}

	public function csso_get_provider(): array {
		return $this->provider;
	}

	public function csso_get_name_id_format(): string {
		return $this->name_id_format;
	}

	public function csso_get_name_id_value(): string {
		return $this->name_id_value;
	}

	public function csso_get_attributes(): array {
		return $this->attributes;
	}

	public function csso_get_last_error() {
		return $this->last_error;
	}

	public function csso_get_session_index(): string {
		return $this->session_index;
	}
}
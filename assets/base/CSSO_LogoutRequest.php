<?php

namespace CloudSingleSignOn\base;

abstract class CSSO_StatusCode {
	const Success = 'urn:oasis:names:tc:SAML:2.0:status:Success';
}

class CSSO_LogoutRequest {

	public static function csso_validate_response() {
		$request        = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
		$bindingFactory = new \LightSaml\Binding\BindingFactory();
		$messageContext = new \LightSaml\Context\Profile\MessageContext();
		$binding        = $bindingFactory->getBindingByRequest( $request );
		$binding->receive( $request, $messageContext );

		$status = $messageContext->getMessage()->getStatus()->getStatusCode()->getValue();
		if ( $status === CSSO_StatusCode::Success ) {
			wp_redirect( get_option( csso_get_plugin_prefix() . 'redirect_to' ) );
		}
	}
}
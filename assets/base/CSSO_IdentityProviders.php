<?php

namespace CloudSingleSignOn\base;

abstract class CSSO_IdentityProviders {
	const Okta = 'okta';
	const AzureAd = 'azureAd';
	const ADFS = 'adfs';
	const AzureB2C = 'azureB2C';
	const Office365 = 'office365';
	const Auth0 = 'auth0';
	const SalesForce = 'salesForce';
	const GoogleApps = 'googleApps';
	const OneLogin = 'oneLogin';
	const KeyCloak = 'keyCloak';
	const Shibboleth = 'shibboleth';
}
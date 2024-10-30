<?php

namespace CloudSingleSignOn\base;

class CSSO_SupportedProviders
{
    private  $providers ;
    public function csso_register()
    {
        $this->csso_set_supported_providers();
    }
    
    private function csso_set_supported_providers()
    {
        $this->providers = [
            CSSO_IdentityProviders::AzureAd    => [
            'name'             => 'Azure AD',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/azure-ad.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/office-365-azure-ad-sso/',
            'slug'             => CSSO_IdentityProviders::AzureAd,
            'avatar_mapping'   => true,
        ],
            CSSO_IdentityProviders::AzureB2C   => [
            'name'             => 'Azure AD B2C',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/azure-ad.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-single-sign-on-using-azure-b2c-saml-login/',
            'slug'             => CSSO_IdentityProviders::AzureB2C,
            'avatar_mapping'   => false,
        ],
            CSSO_IdentityProviders::ADFS       => [
            'name'             => 'ADFS',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/adfs.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/adfs-sso-for-wordpress/',
            'slug'             => CSSO_IdentityProviders::ADFS,
            'avatar_mapping'   => false,
        ],
            CSSO_IdentityProviders::Office365  => [
            'name'             => 'Office 365 SSO',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/office365-sso.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-office-365-login-wp-cloud-sso/',
            'slug'             => CSSO_IdentityProviders::Office365,
            'avatar_mapping'   => true,
        ],
            CSSO_IdentityProviders::Okta       => [
            'name'             => 'Okta',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/okta.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-sso-using-okta-as-saml-idp/',
            'slug'             => CSSO_IdentityProviders::Okta,
            'avatar_mapping'   => false,
        ],
            CSSO_IdentityProviders::SalesForce => [
            'name'             => 'Sales Force',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/salesforce.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-sso-using-salesforce-saml-login-idp/',
            'slug'             => CSSO_IdentityProviders::SalesForce,
            'avatar_mapping'   => false,
        ],
            CSSO_IdentityProviders::GoogleApps => [
            'name'             => 'Google Apps',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/google-apps.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-google-apps-login-single-sign-on-saml-idp/',
            'slug'             => CSSO_IdentityProviders::GoogleApps,
            'avatar_mapping'   => false,
        ],
            CSSO_IdentityProviders::Auth0      => [
            'name'             => 'Auth0',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/auth0.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-sso-using-auth0-saml-idp-wp-auth0-login/',
            'slug'             => CSSO_IdentityProviders::Auth0,
            'avatar_mapping'   => false,
        ],
            CSSO_IdentityProviders::OneLogin   => [
            'name'             => 'One Login',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/onelogin.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-single-sign-on-using-onelogin-as-saml-idp/',
            'slug'             => CSSO_IdentityProviders::OneLogin,
            'avatar_mapping'   => false,
        ],
            CSSO_IdentityProviders::KeyCloak   => [
            'name'             => 'KeyCloak',
            'image_url'        => csso_get_plugin_url() . 'assets/resources/images/providers/keycloak.png',
            'setup_guide_link' => 'https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-sso-using-keycloak-as-saml-idp/',
            'slug'             => CSSO_IdentityProviders::KeyCloak,
            'avatar_mapping'   => false,
        ],
        ];
    }
    
    public function csso_get_all()
    {
        return $this->providers;
    }
    
    public function csso_exist( $providerName ) : bool
    {
        return isset( $this->providers[$providerName] );
    }
    
    public function csso_get_selected()
    {
        
        if ( get_option( csso_get_plugin_prefix() . 'selected_provider' ) ) {
            return $this->providers[get_option( csso_get_plugin_prefix() . 'selected_provider' )];
        } else {
            return null;
        }
    
    }
    
    public function csso_get( $provider_slug )
    {
        return $this->providers[$provider_slug];
    }

}
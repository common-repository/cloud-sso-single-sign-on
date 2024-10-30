=== Cloud SAML SSO - Single Sign On Login ===
Contributors: cloudinfrastructureservices, freemius
Author URI: https://cloudinfrastructureservices.co.uk/
Plugin URL: https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/
Donate link: https://cloudinfrastructureservices.co.uk/
Requires at Least: 5.7
Requires PHP: 7.2
Tested Up To: 6.5
Tags: sso, single sign on, Azure AD, Office 365, SAML, login, Google login, okta, login security
Stable tag: 1.0.16
License: GPLv2 or later

WordPress SSO using your SAML identity provider to enable single sign on using Azure AD, Office 365, Okta, Azure B2C, ADFS, KeyCloak, OneLogin, Salesforce, Google Apps Gsuite, Shibboleth, Auth0 and more..

== Description ==

= WP Cloud SSO - SAML Single Sign On (WordPress Login Security) =

WordPress Single Sign On by Cloud Infrastructure Services Ltd. Our <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/">WordPress SSO</a> plugin offers WordPress SAML SSO Single Sign On for your WordPress logins.  Login to WordPress (WP) using Azure AD, Azure B2C, Okta, ADFS, Keycloak, OneLogin, Salesforce, Google Apps (G Suite), Shibboleth, Auth0 and other IdPs (Identity Providers). It acts as SAML SP (Service Provider) which can be configured to establish a trust between our WordPress SSO plugin and your IDP to securely authenticate and enable SSO / Login for your users into the WordPress (WP) site.

The following video explains the features of WP Cloud SSO plugin.

https://youtu.be/T-flVowguAE

== WP Cloud SSO - WordPress SSO Features ==

*   **Unlimited SSO Authentications** With your SAML IdP. Auto redirect your user logins to WordPress, authenticating against your IDP for SSO authentication. Automate the user experience with auto redirect, no need for username / password for a Single Sign On experience using your IDP to authorise the logins.
*   **SAML Attribute Mapping** Map user attributes from your identity provider to your WordPress user profiles. For example (Name, Username, Email, Job Title, Department, Telephone, City, Profile Picture, & more)
*   **Protect WordPress, Auto-Redirect to IDP** Protect your WordPress site (Auto-Redirect to IdP). Only authorised users can login to WordPress. Restrict WordPress to only logged in users by redirecting the users to your IdP if logged in session is not found
*   **WordPress Role Mapping** Automatically assign WordPress roles to users based on SAML IDP group membership. Restrict access to WordPress based on IDP Groups. Making WordPress secure and controlling WordPress user permissions.
*   **Multiple WordPress Environments Support** Manage multiple WordPress environments (Prod, Dev, Staging, Test). Migrate between test/staging environments keeping your SSO config and attribute mappings inplace. Supports platforms like (WP Engine, Pantheon, Sub domains & more).
*   **WordPress Single Signout** Logs the user out of your Identity Provider on logout from WordPress site acting as Service Provider (SP) and terminates current login session on both ends.
*   **SAML Avatar Profile Pic Sync** If you're using Office 365, you have the option to sync your AzureAD / Office365 profile pics to your WordPress users avatar
*   **Auto-sync IdP Configuration from metadata** Easily upload your IDP metadata XML file, making it easier to sync your SAML metadata to WordPress
*   **Widget, Shortcode to add SAML IDP Login Link** Customise your login page with our WordPress widget login customiser. Use shortcode to place login links anywhere on WordPress
*   **Multiple SAML IDP Support** Add as many SAML Identiry providers to allow your users to login from any IDP

= Free Version Features =
*   **Unlimited Authentications** With 1 SAML Identity provider
*   **Basic Attribute Mapping** Basic Attribute Mapping (Email, First Name, Last Name, Display Name)
*   **Widget, Shortcode to add SAML IDP Login Link**
*   **Protect WordPress, Auto-Redirect to IDP**

= Premium Version Features 10 Day Free Trial =
*   **All features from Free Version**
*   **Advanced Attribute Mapping** Map any SAML user attributes to your WordPress users, for example (Job Title, Department, Telephone, City, Employee ID, etc etc)
*   **WordPress Role Mapping** Use IDP groups to map WordPress roles based on group membership (Default, Administrator, Editor, Author, Contributor, Subscriber). Allowing you to secure access to WordPress logins.
*   **SAML Single Logout** Allows the user to logout of your IDP when logging out of WordPress and terminates current login session on both ends.
*   **Auto-sync IdP Configuration from metadata** Sync IDP metadata to WordPress automatically.
*   **Dedicated WordPress Support Team** Access to our WordPress support team if you have any questions or assistance setting up WP Cloud SSO.

= Enterprise Version Features 10 Day Free Trial =
*   **All features from Free Version & Premium**
*   **Multiple Environments Support / Migration (Dev, Staging, Prod)** Add Multiple Environments SSO support (Staging, Dev, Test). Merge SSO Settings / Copy between evironments. Also works with (WP Engine, Pantheonsite, Kinsta, sub domains & Other WordPress Managed Platforms)
*   **Multiple SAML IDP Support (Add unlimited SAML IDPs)** Add as many IDPs as needed, no restrictions. Add unlimited amount of SAML identity providers.
*   **Sync SAML User Pics to WordPress Avatar** Sync supported SAML provider user pictures to WordPress user avatars. For example Office 365 user profile pictures.

== List of Supported SAML Identity Providers ==

WP Cloud SSO supports the following SAML Single Sign On Providers. Full setup instructions to enable SSO for your WordPress Logins.


* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/office-365-azure-ad-sso/" target="_blank">WordPress Azure AD SSO</a> (Setup Azure AD SAML SSO for your WordPress Azure AD Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-office-365-login-wp-cloud-sso/" target="_blank">WordPress Office 365 Login</a> (Setup Office 365 SAML SSO for your WordPress Office 365 Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/adfs-sso-for-wordpress/" target="_blank">WordPress ADFS SSO</a> (Setup Microsoft ADFS SAML SSO for your WordPress ADFS Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-single-sign-on-using-azure-b2c-saml-login/" target="_blank">WordPress Azure B2C SSO</a> (Setup Azure B2C SAML SSO for your WordPress Azure B2C Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-sso-using-salesforce-saml-login-idp/" target="_blank">WordPress Salesforce SSO</a> (Setup Salesforce SAML SSO for your WordPress Salesforce Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-google-apps-login-single-sign-on-saml-idp/" target="_blank">WordPress Google Apps / GSuite SSO</a> (Setup Google Apps / GSuite SAML SSO for your WordPress Google Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-single-sign-on-using-onelogin-as-saml-idp/" target="_blank">WordPress OneLogin SSO</a> (Setup OneLogin SAML SSO for your WordPress OneLogin Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-sso-using-okta-as-saml-idp/" target="_blank">WordPress Okta SSO</a> (Setup Okta SAML SSO for your WordPress Okta Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-sso-using-keycloak-as-saml-idp/" target="_blank">WordPress KeyCloak SSO</a> (Setup KeyCloak SAML SSO for your WordPress KeyCloak Logins)
* <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/wordpress-sso-using-auth0-saml-idp-wp-auth0-login/" target="_blank">WordPress Auth0 SSO</a> (Setup Auth0 SAML SSO for your WordPress Auth0 Logins)


> Check our website for more details on <a href="https://cloudinfrastructureservices.co.uk/wordpress-sso-single-sign-on/">WordPress Single Sign On</a> using WP Cloud SSO plugn and setup documentation.


== Frequently Asked Questions ==

= Do you support WordPress MultiSite Network SSO? =

Not at the moment, we only support single sites. We hope to release this feature in the future.

= Do you support multiple environments like WP Engine, Pantheon etc? =

Yes, upgrade to our Enterprise Plan and the license allows you to use multiple environments like test, staging, dev, etc

== Screenshots ==

1. Setup WordPress with your SAML IDP
2. Setup Attribute mappings from your IDP
3. Setup WordPress Role mappings based on Group membership
4. Style your login buttons
5. Setup Multiple WordPress Environments for SSO



-----

== Upgrade Notice ==

= 1.0.15 =
* Update for WordPress version 6.4

= 1.0.14 =
* Updated Freemius SDK to the latest version

= 1.0.13 =
* Fix login errors from previous update

= 1.0.12 =
* Update for WordPress version 6.1

= 1.0.11 =
* Fixed plugin defects.
* Added supporting Azure B2C IDP.
* Added multiple IDP certificates functionality.
* Added possibility to use Custom SP Entity ID for identity providers.
* Added single logout functionality for Auth0, KeyCloak, OneLogin identity providers.

= 1.0.10 =
* Updating of image assets.
* Optimization plugin.
* Fix plugin defects.
* Add functionality to test configured providers.

= 1.0.9 =
* First release.

== Changelog ==

= 1.0.15 =
* Update for WordPress version 6.4

= 1.0.14 =
* Updated Freemius SDK to the latest version

= 1.0.13 =
* Fix login errors from previous update

= 1.0.12 =
* Update for WordPress version 6.1

= 1.0.11 =
* Fixed plugin defects.
* Added supporting Azure B2C IDP.
* Added multiple IDP certificates functionality.
* Added possibility to use Custom SP Entity ID for identity providers.
* Added single logout functionality for Auth0, KeyCloak, OneLogin identity providers.

= 1.0.10 =
* Updating of image assets.
* Optimization plugin.
* Fix plugin defects.
* Add functionality to test configured providers.

= 1.0.9 =
* First release.

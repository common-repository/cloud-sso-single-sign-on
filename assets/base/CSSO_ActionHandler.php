<?php

namespace CloudSingleSignOn\base;

abstract class CSSO_SamlActions
{
    const  LOGIN = 'saml_login' ;
    const  TEST_PROVIDER_CONFIGURATION = 'saml_test_provider_configuration' ;
    const  LOGOUT = 'saml_logout' ;
}
class CSSO_ActionHandler
{
    function csso_register()
    {
        $this->csso_handle_actions();
        add_action( 'admin_notices', [ $this, 'csso_handle_plugin_errors' ] );
        add_action( 'admin_notices', [ $this, 'csso_handle_ssl_warning' ] );
    }
    
    function csso_handle_actions()
    {
        global  $supported_providers ;
        global  $environment_service ;
        global  $providers_manager ;
        if ( csso_is_multiple_environments_enabled() && $this->csso_is_domain_changed() ) {
            CSSO_ServiceProvider::csso_update_metadata( get_home_url() );
        }
        
        if ( $this->csso_is_request_action_called( CSSO_SamlActions::LOGIN ) ) {
            $provider = $providers_manager->csso_get_one_by( 'id', sanitize_text_field( $_REQUEST['provider_id'] ) );
            $login_url = $providers_manager->csso_generate_provider_login_url( $provider );
            $this->csso_set_latest_action( CSSO_SamlActions::LOGIN );
            update_option( csso_get_plugin_prefix() . 'redirect_to', csso_non_cacheable_url( sanitize_url( $_REQUEST['redirect_to'] ) ) );
            wp_redirect( ( $provider ? $login_url : sanitize_url( $_SERVER['HTTP_REFERER'] ) ) );
            exit;
        }
        
        if ( $this->csso_is_request_action_called( CSSO_SamlActions::TEST_PROVIDER_CONFIGURATION ) ) {
            
            if ( $this->csso_is_request_nonce_field_verified( CSSO_SamlActions::TEST_PROVIDER_CONFIGURATION, '_wpnonce' ) ) {
                $provider = $providers_manager->csso_get_one_by( 'id', intval( $_REQUEST['provider_id'] ) );
                $this->csso_set_latest_action( CSSO_SamlActions::TEST_PROVIDER_CONFIGURATION );
                wp_redirect( $providers_manager->csso_generate_provider_login_url( $provider ) );
                exit;
            } else {
                $this->csso_nonce_field_verify_error();
            }
        
        }
        if ( $this->csso_is_request_action_called( CSSO_SamlActions::LOGOUT ) ) {
            
            if ( $this->csso_is_request_nonce_field_verified( 'logout_user', '_wpnonce' ) ) {
                $user = wp_get_current_user();
                wp_logout();
            } else {
                $this->csso_nonce_field_verify_error();
            }
        
        }
        if ( $this->csso_is_post_action_called( 'update_option' ) ) {
            
            if ( $this->csso_is_post_nonce_field_verified( 'update_option' ) ) {
                update_option( sanitize_text_field( $_POST['option_name'] ), sanitize_text_field( $_POST['option_value'] ) );
                $environment_service->csso_register();
                $providers_manager->csso_register();
            }
        
        }
        
        if ( $this->csso_is_request_option_called( 'sp_metadata' ) ) {
            $download_option = isset( $_REQUEST['download'] ) && boolval( $_REQUEST['download'] );
            header( 'content-type: text/xml' );
            if ( csso_get_boolean_value( $download_option ) ) {
                header( 'Content-Disposition: attachment; filename="SP_Metadata.xml"' );
            }
            echo  CSSO_ServiceProvider::csso_get_xml_metadata() ;
            exit;
        }
        
        
        if ( $this->csso_is_request_option_called( 'sp_x509_certificate' ) ) {
            header( 'Content-Disposition: attachment; filename="cert.crt"' );
            echo  get_option( csso_get_plugin_prefix() . 'sp_x509_certificate' ) ;
            exit;
        }
        
        if ( $this->csso_is_post_action_called( 'update_login_button_styles' ) ) {
            
            if ( $this->csso_is_post_nonce_field_verified( 'update_login_button_styles' ) ) {
                $this->csso_update_login_button_styles();
            } else {
                $this->csso_nonce_field_verify_error();
            }
        
        }
        if ( $this->csso_is_request_action_called( 'reset_saml_button_styles' ) ) {
            
            if ( $this->csso_is_request_nonce_field_verified( 'reset_saml_button_styles', '_wpnonce' ) ) {
                update_option( csso_get_plugin_prefix() . 'login_button_styles', json_encode( csso_get_login_button_default_styles() ) );
                $this->csso_redirect_to_referer();
            } else {
                $this->csso_nonce_field_verify_error();
            }
        
        }
        
        if ( $this->csso_is_saml_post_response_exist() && $this->csso_get_latest_action() === CSSO_SamlActions::LOGIN ) {
            CSSO_LoginUser::csso_handle_login();
            exit;
        }
        
        if ( $this->csso_is_saml_post_response_exist() && $this->csso_get_latest_action() === CSSO_SamlActions::TEST_PROVIDER_CONFIGURATION ) {
            
            if ( file_exists( csso_get_plugin_path() . 'assets/templates/CSSO_test_results.php' ) ) {
                require csso_get_plugin_path() . 'assets/templates/CSSO_test_results.php';
                exit;
            }
        
        }
        
        if ( $this->csso_is_saml_get_response_exist() && $this->csso_get_latest_action() === CSSO_SamlActions::LOGOUT ) {
            CSSO_LogoutRequest::csso_validate_response();
            exit;
        }
        
        if ( $this->csso_is_post_action_called( 'attribute_mapping' ) ) {
            
            if ( $this->csso_is_post_nonce_field_verified( 'attribute_mapping' ) ) {
                $this->csso_handle_update_attribute_mapping();
                $providers_manager->csso_register();
            } else {
                $this->csso_nonce_field_verify_error();
            }
        
        }
        if ( $this->csso_is_post_action_called( 'role_mapping' ) ) {
            
            if ( $this->csso_is_post_nonce_field_verified( 'role_mapping' ) ) {
                $this->csso_handle_update_role_mapping();
                $providers_manager->csso_register();
            } else {
                $this->csso_nonce_field_verify_error();
            }
        
        }
        
        if ( $this->csso_is_post_action_called( 'delete_config' ) ) {
            $provider_id = intval( $_POST['selected_provider'] );
            $providers_manager->csso_delete_identity_provider( $provider_id );
            $providers_manager->csso_register();
        }
        
        if ( isset( $_GET['selected_provider'] ) ) {
            update_option( csso_get_plugin_prefix() . 'selected_provider', sanitize_text_field( $_GET['selected_provider'] ) );
        }
        
        if ( !wpcsso_fs()->is_plan_or_trial( 'enterprise' ) ) {
            $selected_providers = $providers_manager->csso_get_all_by_current_env();
            $selected_provider = get_option( csso_get_plugin_prefix() . 'selected_provider' );
            if ( count( $selected_providers ) && $selected_provider !== current( $selected_providers )['provider'] ) {
                update_option( csso_get_plugin_prefix() . 'selected_provider', current( $selected_providers )['provider'] );
            }
        }
        
        if ( $this->csso_is_post_action_called( 'idp_config' ) ) {
            
            if ( $this->csso_is_post_nonce_field_verified( 'idp_config' ) ) {
                $provider_slug = get_option( csso_get_plugin_prefix() . 'selected_provider' );
                $name = sanitize_text_field( $_POST['saml_identity_name'] );
                $issuer = sanitize_text_field( $_POST['saml_issuer'] );
                $login_url = sanitize_text_field( $_POST['saml_login_url'] );
                $logout_url = csso_sanitize_post_text_field_or_default( 'saml_logout_url' );
                $certificates = array_unique( array_map( function ( $cert ) {
                    return csso_sanitize_certificate( $cert );
                }, $_POST['x509'] ) );
                
                if ( $providers_manager->csso_is_config_exist( $provider_slug ) ) {
                    $providers_manager->csso_update(
                        $provider_slug,
                        $name,
                        $issuer,
                        $login_url,
                        $logout_url,
                        $certificates
                    );
                } elseif ( $supported_providers->csso_exist( $provider_slug ) ) {
                    
                    if ( $providers_manager->csso_get_one_by( 'entity_id_or_issuer', $issuer ) ) {
                        CSSO_ErrorHandler::csso_set_error( 'This provider already configured, this entity ID or Issuer already exist!' );
                        return;
                    }
                    
                    $providers_manager->csso_add_new(
                        $provider_slug,
                        $name,
                        $issuer,
                        $login_url,
                        $logout_url,
                        $certificates
                    );
                }
                
                $providers_manager->csso_register();
            } else {
                $this->csso_nonce_field_verify_error();
            }
        
        }
        if ( $this->csso_is_post_action_called( 'change_provider_custom_sp_entity_id' ) ) {
            
            if ( $this->csso_is_post_nonce_field_verified( 'change_provider_custom_sp_entity_id' ) ) {
                $provider_slug = get_option( csso_get_plugin_prefix() . 'selected_provider' );
                $new_value = sanitize_text_field( $_POST['custom_sp_entity_id'] );
                $providers_manager->csso_update_provider_field( 'custom_sp_entity_id', $new_value, $provider_slug );
                $providers_manager->csso_register();
            }
        
        }
        
        if ( $this->csso_is_post_action_called( 'set_organization_settings' ) ) {
            $name = sanitize_text_field( $_POST[csso_get_plugin_prefix() . 'sp_org_name'] );
            $display_name = sanitize_text_field( $_POST[csso_get_plugin_prefix() . 'sp_org_display_name'] );
            $url = sanitize_text_field( $_POST[csso_get_plugin_prefix() . 'sp_org_url'] );
            $technical_name = sanitize_text_field( $_POST[csso_get_plugin_prefix() . 'sp_technical_name'] );
            $technical_email = sanitize_text_field( $_POST[csso_get_plugin_prefix() . 'sp_technical_email'] );
            $support_name = sanitize_text_field( $_POST[csso_get_plugin_prefix() . 'sp_support_name'] );
            $support_email = sanitize_text_field( $_POST[csso_get_plugin_prefix() . 'sp_support_email'] );
            $enable_signing = csso_sanitize_post_text_field_or_default( csso_get_plugin_prefix() . 'sp_enable_signing' );
            $enable_encryption = csso_sanitize_post_text_field_or_default( csso_get_plugin_prefix() . 'sp_enable_encryption' );
            update_option( csso_get_plugin_prefix() . 'sp_org_name', $name );
            update_option( csso_get_plugin_prefix() . 'sp_org_display_name', $display_name );
            update_option( csso_get_plugin_prefix() . 'sp_org_url', $url );
            update_option( csso_get_plugin_prefix() . 'sp_technical_name', $technical_name );
            update_option( csso_get_plugin_prefix() . 'sp_technical_email', $technical_email );
            update_option( csso_get_plugin_prefix() . 'sp_support_name', $support_name );
            update_option( csso_get_plugin_prefix() . 'sp_support_email', $support_email );
            update_option( csso_get_plugin_prefix() . 'sp_enable_signing', $enable_signing );
            update_option( csso_get_plugin_prefix() . 'sp_enable_encryption', $enable_encryption );
            return;
        }
        
        if ( $this->csso_is_get_action_called( 'dismiss_review_notification' ) ) {
            
            if ( $this->csso_is_request_nonce_field_verified( 'dismiss_review_notification', '_wpnonce' ) ) {
                update_option( csso_get_plugin_prefix() . 'is_review_notification_must_shown', false );
                $this->csso_redirect_to_referer();
            } else {
                $this->csso_nonce_field_verify_error();
            }
        
        }
        if ( $this->csso_is_leave_review_notification_must_shown_for_current_user() ) {
            add_action( 'admin_notices', [ $this, 'csso_show_leave_review_notification' ] );
        }
        
        if ( $this->csso_is_plugin_used_7days_or_more() && !$this->csso_is_7days_leave_review_notification_must_shown() ) {
            update_option( csso_get_plugin_prefix() . 'is_review_notification_must_shown', true );
            update_option( csso_get_plugin_prefix() . 'is_7days_leave_review_notification_shown', true );
        }
    
    }
    
    private function csso_is_request_action_called( string $action_name ) : bool
    {
        return isset( $_REQUEST['action'] ) && sanitize_text_field( $_REQUEST['action'] ) === $action_name;
    }
    
    private function csso_set_latest_action( $action )
    {
        update_option( csso_get_plugin_prefix() . "latest_action", $action );
    }
    
    private function csso_is_request_nonce_field_verified( string $action_name, string $nonce_name = 'nonce' ) : bool
    {
        return isset( $_REQUEST[$nonce_name] ) && wp_verify_nonce( sanitize_text_field( $_REQUEST[$nonce_name] ), $action_name );
    }
    
    private function csso_nonce_field_verify_error()
    {
        print 'Sorry, your nonce did not verify.';
        exit;
    }
    
    private function csso_is_post_action_called( string $action_name ) : bool
    {
        return isset( $_POST['action'] ) && sanitize_text_field( $_POST['action'] ) === $action_name;
    }
    
    private function csso_is_post_nonce_field_verified( string $action_name ) : bool
    {
        return isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), $action_name );
    }
    
    private function csso_is_request_option_called( string $option_name ) : bool
    {
        return isset( $_REQUEST['option'] ) && sanitize_text_field( $_REQUEST['option'] ) === $option_name;
    }
    
    private function csso_update_login_button_styles()
    {
        $all_styles = json_decode( get_option( csso_get_plugin_prefix() . 'login_button_styles' ), true );
        foreach ( csso_sanitize_text_array( $_POST ) as $style_names => $value ) {
            $style_names = explode( '$', $style_names );
            $key = ( in_array( 'parent', $style_names ) ? 'parent' : 'button' );
            $index = ( in_array( 'parent', $style_names ) ? 1 : 0 );
            $value = ( is_numeric( $value ) ? "{$value}px" : $value );
            if ( isset( $all_styles[$key][$style_names[$index]] ) ) {
                $all_styles[$key][$style_names[$index]] = $value;
            }
        }
        update_option( csso_get_plugin_prefix() . 'login_button_styles', json_encode( $all_styles ) );
    }
    
    private function csso_redirect_to_referer()
    {
        wp_redirect( $_SERVER['HTTP_REFERER'] );
        exit;
    }
    
    private function csso_is_saml_post_response_exist() : bool
    {
        return isset( $_POST['SAMLResponse'] );
    }
    
    private function csso_get_latest_action()
    {
        return get_option( csso_get_plugin_prefix() . "latest_action" );
    }
    
    private function csso_is_saml_get_response_exist() : bool
    {
        return isset( $_GET['SAMLResponse'] );
    }
    
    private function csso_get_http_redirect_single_sign_service( \LightSaml\Model\Metadata\IdpSsoDescriptor $descriptor ) : ?\LightSaml\Model\Metadata\SingleSignOnService
    {
        return array_values( $descriptor->getAllSingleSignOnServicesByBinding( \LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT ) )[0] ?? null;
    }
    
    private function csso_get_use_key_descriptor( array $key_descriptors ) : ?array
    {
        return array_values( array_filter( $key_descriptors, function ( \LightSaml\Model\Metadata\KeyDescriptor $descriptor ) {
            return $descriptor->getUse() === 'signing';
        } ) ) ?? null;
    }
    
    private function csso_handle_update_attribute_mapping()
    {
        global  $providers_manager ;
        $data = [
            'first_name'   => csso_sanitize_post_text_field_or_default( 'first_name' ),
            'last_name'    => csso_sanitize_post_text_field_or_default( 'last_name' ),
            'nickname'     => csso_sanitize_post_text_field_or_default( 'nickname' ),
            'display_name' => csso_sanitize_post_text_field_or_default( 'display_name' ),
        ];
        if ( isset( $_POST['action'] ) && isset( $_POST['provider_id'] ) ) {
            $providers_manager->csso_update_attribute_mapping( sanitize_text_field( $_POST['action'] ), $data, intval( $_POST['provider_id'] ) );
        }
    }
    
    private function csso_handle_update_role_mapping()
    {
        global  $providers_manager ;
        $data = [
            'default_role' => csso_sanitize_post_text_field_or_default( 'default_role' ),
        ];
        if ( isset( $_POST['action'] ) && isset( $_POST['provider_id'] ) ) {
            $providers_manager->csso_update_attribute_mapping( sanitize_text_field( $_POST['action'] ), $data, intval( $_POST['provider_id'] ) );
        }
    }
    
    private function csso_is_get_action_called( string $action_name ) : bool
    {
        return isset( $_GET['action'] ) && sanitize_text_field( $_GET['action'] ) === $action_name;
    }
    
    private function csso_is_leave_review_notification_must_shown_for_current_user() : bool
    {
        return csso_is_current_user_plugin_owner() && csso_is_review_notification_must_be_shown();
    }
    
    private function csso_is_plugin_used_7days_or_more() : bool
    {
        $format = 'Y-m-d';
        $date = new \DateTime( get_option( csso_get_plugin_prefix() . 'plugin_activation_date' ) );
        $date->modify( '+1 week' );
        return strtotime( ( new \DateTime() )->format( $format ) ) >= strtotime( $date->format( $format ) );
    }
    
    private function csso_is_7days_leave_review_notification_must_shown()
    {
        return csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'is_7days_leave_review_notification_shown' ) );
    }
    
    public function csso_show_leave_review_notification()
    {
        if ( !isset( $_GET['page'] ) || csso_is_current_page_plugin() ) {
            csso_leave_review_notification();
        }
    }
    
    public function csso_handle_plugin_errors()
    {
        global  $environment_service ;
        
        if ( CSSO_ErrorHandler::csso_get_last_error() && csso_is_current_page_plugin() ) {
            ?>
			<div class="notice notice-error is-dismissible">
				<p>
					Error!
					<?php 
            echo  CSSO_ErrorHandler::csso_get_last_error() ;
            ?>
				</p>
			</div>
			<?php 
        }
    
    }
    
    public function csso_handle_ssl_warning()
    {
        if ( !is_ssl() && csso_is_current_page_plugin() ) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    Warning! Your domain is using the HTTP protocol.
                    This protocol is not supported by our plugin and by most of the Identity Providers.
                    Please consider moving to a secured HTTPS protocol by
                    <a target="_blank"
                       href="https://cloudinfrastructureservices.co.uk/how-to-setup-wordpress-on-linux-with-apache-lets-encrypt-certs-on-azure-aws-gcp/">
                        installing HTTPs SSL certificate on your web server
                    </a>
                </p>
            </div>
			<?php 
        }
    }
    
    private function csso_is_current_url( string $url ) : bool
    {
        return get_home_url() . $_SERVER['REQUEST_URI'] === $url;
    }
    
    private function csso_is_domain_changed() : bool
    {
        return get_home_url() !== get_option( csso_get_plugin_prefix() . 'sp_entity_id' );
    }

}
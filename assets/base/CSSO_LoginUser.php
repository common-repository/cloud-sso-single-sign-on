<?php

namespace CloudSingleSignOn\base;

use  WP_User ;
class CSSO_LoginUser
{
    public static function csso_handle_login()
    {
        $validator = new CSSO_ResponseValidator();
        $validator->csso_validate();
        
        if ( $error = $validator->csso_get_last_error() ) {
            esc_html_e( 'Error: ' . $error );
            exit;
        }
        
        $name_id_value = $validator->csso_get_name_id_value();
        $provider = $validator->csso_get_provider();
        $attributes = $validator->csso_get_attributes();
        $user = self::csso_get_user_by_email( $name_id_value );
        $custom_attributes = json_decode( $provider[CSSO_AttributeMapping::CustomAttributes], true );
        self::csso_map_attributes( $provider, $attributes, $user->ID );
        self::csso_map_role( $provider, $attributes, $user );
        self::csso_remove_user_unused_custom_attributes( $user->ID, $custom_attributes );
        self::csso_make_auth( $user->ID );
    }
    
    private static function csso_get_user_by_email( string $email )
    {
        if ( email_exists( $email ) ) {
            return get_user_by( 'email', $email );
        }
        return new WP_User( wp_create_user( self::csso_generate_user_name( $email ), wp_generate_password( 10, false ), $email ) );
    }
    
    private static function csso_generate_user_name( $email )
    {
        $user_name = explode( '@', $email )[0];
        return ( validate_username( $user_name ) && !username_exists( $user_name ) ? $user_name : $email );
    }
    
    private static function csso_map_attributes( ?array $provider, array $provider_attributes, int $user_id )
    {
        $base_attrs = json_decode( $provider[CSSO_AttributeMapping::BaseAttributes], true );
        if ( $base_attrs ) {
            foreach ( $base_attrs as $key => $value ) {
                
                if ( isset( $provider_attributes[$value] ) ) {
                    wp_update_user( [
                        'ID' => $user_id,
                        $key => $provider_attributes[$value],
                    ] );
                } else {
                    wp_update_user( [
                        'ID' => $user_id,
                        $key => "",
                    ] );
                }
            
            }
        }
    }
    
    private static function csso_map_role( array $provider, array $provider_attributes, WP_User $wp_user )
    {
        $role_mapping_rules = json_decode( $provider[CSSO_AttributeMapping::RoleMapping], true );
        $default_role = $role_mapping_rules['default_role'] ?? null;
        $wp_user->set_role( $default_role );
    }
    
    private static function csso_remove_user_unused_custom_attributes( int $user_id, ?array $custom_attributes = null )
    {
        if ( !$custom_attributes ) {
            $custom_attributes = [];
        }
        $existing_user_meta = array_values( array_filter( array_keys( get_user_meta( $user_id ) ), function ( $meta_key ) {
            return strpos( $meta_key, csso_get_plugin_prefix() ) !== false && $meta_key !== csso_get_plugin_prefix() . 'user_avatar';
        } ) );
        foreach ( $existing_user_meta as $meta_key ) {
            $without_plugin_prefix = str_replace( csso_get_plugin_prefix(), "", $meta_key );
            if ( wpcsso_fs()->is_free_plan() && !wpcsso_fs()->is_trial() || !in_array( $without_plugin_prefix, array_keys( $custom_attributes ) ) ) {
                delete_user_meta( $user_id, $meta_key );
            }
        }
    }
    
    private static function csso_make_auth( int $user_id )
    {
        wp_set_auth_cookie( $user_id, true );
        wp_redirect( get_option( csso_get_plugin_prefix() . 'redirect_to' ) );
        self::csso_increment_count_logins();
    }
    
    private static function csso_increment_count_logins()
    {
        $count = get_option( csso_get_plugin_prefix() . 'count_logins' );
        $count = ( $count ? $count + 1 : 1 );
        if ( $count == 1 ) {
            update_option( csso_get_plugin_prefix() . 'is_review_notification_must_shown', true );
        }
        update_option( csso_get_plugin_prefix() . 'count_logins', $count );
    }

}
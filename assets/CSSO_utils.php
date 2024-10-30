<?php

abstract class ButtonTypes
{
    const  PRIMARY = '' ;
    const  SUCCESS = 'success' ;
    const  DANGER = 'danger' ;
    const  LIGHT = 'light' ;
}
function csso_get_plugin_url() : string
{
    return plugin_dir_url( dirname( __FILE__ ) );
}

function csso_get_plugin_path() : string
{
    return plugin_dir_path( dirname( __FILE__ ) );
}

function csso_get_current_uri() : string
{
    $parts = parse_url( home_url() );
    return "{$parts['scheme']}://{$parts['host']}" . add_query_arg( null, null );
}

function csso_get_plugin_prefix() : string
{
    return 'csso_';
}

function csso_get_boolean_value( $value )
{
    return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
}

function csso_is_multiple_environments_enabled()
{
    return csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'enable_multiple_environments' ) );
}

function csso_get_hostname_by_url( $url ) : string
{
    if ( strpos( $url, 'pantheon' ) !== false || strpos( $url, 'wpengine' ) !== false ) {
        return explode( '.', $url )[0];
    }
    return $url;
}

function csso_concat_query_args( $arg, $value ) : string
{
    return add_query_arg( $arg, $value, csso_get_current_uri() );
}

function csso_sanitize_certificate( $certificate ) : string
{
    $certificate = preg_replace( "/[\r\n]+/", "", $certificate );
    $certificate = str_replace( "-", "", $certificate );
    $certificate = str_replace( "BEGIN CERTIFICATE", "", $certificate );
    $certificate = str_replace( "END CERTIFICATE", "", $certificate );
    $certificate = str_replace( " ", "", $certificate );
    $certificate = chunk_split( $certificate, 64, "\r\n" );
    $certificate = "-----BEGIN CERTIFICATE-----\r\n" . $certificate . "-----END CERTIFICATE-----";
    return $certificate;
}

function get_pricing_url() : string
{
    return admin_url() . '/admin.php?page=' . csso_get_plugin_prefix() . 'dashboard-pricing';
}

function handle_premium_version( string $class = "" ) : string
{
    return "prem-info overflow-section {$class}";
}

function handle_enterprise_version( string $class = '' ) : string
{
    return "prem-info {$class}";
}

function handle_idp_config_permission( $count_providers, $element_name ) : string
{
    
    if ( $count_providers === 0 ) {
        return '';
    } else {
        switch ( $element_name ) {
            case 'provider':
                return 'only-one-provider';
            default:
                return 'prem-info';
        }
    }

}

function csso_search_in_array( $arr, $search_column, $value )
{
    $found = array_search( $value, array_column( $arr, $search_column ), true );
    if ( $found !== false ) {
        return $arr[$found];
    }
    return null;
}

function csso_array_to_styles_attribute( $arr ) : string
{
    return implode( ';', array_reduce( array_keys( $arr ), function ( $curr, $next ) use( $arr ) {
        $curr[] = "{$next}:{$arr[$next]}";
        return $curr;
    }, [] ) );
}

function csso_trim_px( string $str )
{
    return str_replace( 'px', '', $str );
}

function csso_get_login_button_default_styles() : array
{
    return [
        'button' => [
        'display'          => 'flex',
        'align-items'      => 'center',
        'justify-content'  => 'center',
        'box-sizing'       => 'initial',
        'overflow'         => 'hidden',
        'font-family'      => 'sans-serif',
        'width'            => 'fit-content',
        'height'           => '32px',
        'border-radius'    => '5px',
        'background-color' => '#2271b1',
        'color'            => '#ffffff',
        'font-size'        => '13px',
        'font-weight'      => 'initial',
        'padding'          => '0 12px',
        'border'           => 'none',
        'outline'          => 'none',
        'cursor'           => 'pointer',
        'text-decoration'  => 'none',
    ],
        'parent' => [
        'display'         => 'flex',
        'justify-content' => 'center',
        'margin-bottom'   => '10px',
        'white-space'     => 'nowrap',
    ],
    ];
}

function csso_get_empty_provider() : array
{
    return [
        'id'   => null,
        'name' => 'AzureAD',
    ];
}

function csso_get_plugin_owner()
{
    return json_decode( get_option( csso_get_plugin_prefix() . 'plugin_owner' ) );
}

function csso_is_current_user_plugin_owner() : bool
{
    return isset( csso_get_plugin_owner()->ID ) && csso_get_plugin_owner()->ID === wp_get_current_user()->ID;
}

function csso_is_review_notification_must_be_shown()
{
    return csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'is_review_notification_must_shown' ) );
}

function csso_get_option_value( $option_name ) : string
{
    $option = get_option( $option_name );
    return ( isset( $option ) ? esc_attr( $option ) : '' );
}

function csso_get_plugin_page_name() : string
{
    return csso_get_plugin_prefix() . 'dashboard';
}

function csso_is_current_page_plugin() : bool
{
    return isset( $_REQUEST['page'] ) && sanitize_text_field( $_REQUEST['page'] ) == csso_get_plugin_page_name();
}

function csso_custom_file_get_contents( $path ) : string
{
    $content = @file_get_contents( $path );
    
    if ( $content === false ) {
        throw new Exception( "Cannot access '{$path}' to read contents." );
    } else {
        return $content;
    }

}

function csso_sanitize_text_array( array $array ) : array
{
    return array_map( 'sanitize_text_field', $array );
}

function csso_sanitize_number_array( array $array ) : array
{
    return array_map( 'intval', $array );
}

function csso_sanitize_post_text_field_or_default( string $string, $default = '' ) : ?string
{
    return ( isset( $_POST[$string] ) ? sanitize_text_field( $_POST[$string] ) : $default );
}

function csso_non_cacheable_url( $url ) : string
{
    return add_query_arg( [
        'q' => date( 'Y-m-d_H:i:s' ),
    ], $url );
}

function csso_is_request_failed( $response ) : bool
{
    $response = json_decode( $response, true );
    return !is_null( $response ) && !is_null( $response['error'] );
}

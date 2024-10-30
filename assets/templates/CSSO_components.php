<?php

use  CloudSingleSignOn\base\CSSO_IdentityProviders ;
function csso_page_header( string $title )
{
    ?>
    <div class="page-header-wrapper">
        <h1 class="m-0 page-header"><?php 
    esc_html_e( $title );
    ?></h1>
    </div>
	<?php 
}

function csso_environment_selector()
{
    global  $environment_service ;
    ?>
    <div class="d-flex align-items-center">
        <p class="text-secondary mr-2">Select environment: </p>
        <form action="" method="post">
            <select name="option_value" onchange="this.form.submit()">'
				<?php 
    foreach ( $environment_service->csso_get_all_envs() as $env ) {
        ?>
                    <option type="submit"
                            value='<?php 
        esc_attr_e( $env['name'] );
        ?>' <?php 
        selected( get_option( csso_get_plugin_prefix() . 'active_editable_environment' ), $env['name'] );
        ?> >
						<?php 
        esc_html_e( $env['name'] );
        ?>
                    </option>
					<?php 
    }
    ?>
            </select>
            <input type="hidden" name="action" value="update_option">
            <input type="hidden" name="option_name"
                   value="<?php 
    esc_attr_e( csso_get_plugin_prefix() . 'active_editable_environment' );
    ?>">
			<?php 
    wp_nonce_field( 'update_option', 'nonce' );
    ?>
        </form>
    </div>
	<?php 
}

function csso_submit_button( ?string $title = null, ?string $class = null, ?string $width = null )
{
    if ( empty($title) ) {
        $title = 'Save';
    }
    if ( empty($class) ) {
        $class = '';
    }
    if ( empty($width) ) {
        $width = '230px';
    }
    ?>
    <button class="wcs-btn wcs-save-btn <?php 
    esc_attr_e( $class );
    ?>" style="width: <?php 
    esc_attr_e( $width );
    ?>">
		<?php 
    esc_html_e( $title );
    ?>
    </button>
	<?php 
}

function csso_form_separator()
{
    ?>
    <div class="form-sep my-4">
        <span class="bg-secondary rounded-circle p-2 text-white">OR</span>
    </div>
	<?php 
}

function csso_link_button(
    string $link,
    string $title,
    ?string $type = '',
    bool $filled = true,
    string $class = '',
    bool $active = false,
    $open_in_new_tab = false
)
{
    $button_classes = ( $filled ? 'wcs-btn ' . csso_get_button_class_for_type( $type, $active ) : 'wcs-link-btn ' . csso_get_button_class_for_type( $type, $active, true ) );
    ?>
    <a class="<?php 
    esc_attr_e( $button_classes );
    ?>  <?php 
    esc_attr_e( $class );
    ?>"
       href="<?php 
    echo  esc_url( $link ) ;
    ?>"
       target="<?php 
    esc_attr_e( ( $open_in_new_tab ? '_blank' : '' ) );
    ?>">
		<?php 
    esc_html_e( $title );
    ?>
    </a>
	<?php 
}

function csso_get_button_class_for_type( string $type, bool $active, bool $is_link = false ) : string
{
    $active = ( $active ? '-active' : '' );
    $type = ( !empty($type) ? "-{$type}" : '' );
    return ( $is_link ? "wcs{$type}{$active}-link-btn" : "wcs{$type}{$active}-btn" );
}

function csso_provider_login_link( $provider, $redirect_url, $button = false ) : string
{
    $title = "Login with {$provider['name']}";
    $action = \CloudSingleSignOn\base\CSSO_SamlActions::LOGIN;
    $url = csso_non_cacheable_url( get_home_url() . '?action=' . $action . '&provider_id=' . $provider['id'] . '&redirect_to=' . $redirect_url );
    $button_styles = json_decode( get_option( csso_get_plugin_prefix() . 'login_button_styles' ), true );
    $css_styles = csso_array_to_styles_attribute( $button_styles['button'] );
    $button_parent_styles = csso_array_to_styles_attribute( $button_styles['parent'] );
    $styles = ( $button ? $css_styles : '' );
    $data = "\n    <div style='" . esc_attr( $button_parent_styles ) . "'>\n    <a href='" . esc_url( $url ) . "' style='" . esc_attr( $styles ) . "' class='saml-login-button'>\n        <p style='margin:0; font-size: inherit'>\n\t\t\t" . esc_html( $title ) . "\n        </p>\n    </a>\n    </div>";
    return $data;
}

function csso_user_logout_info( WP_User $user ) : string
{
    $action = \CloudSingleSignOn\base\CSSO_SamlActions::LOGOUT;
    $logout_url = csso_non_cacheable_url( wp_nonce_url( site_url() . '?action=' . $action . '&redirect_to=' . site_url(), 'logout_user' ) );
    $button_styles = json_decode( get_option( csso_get_plugin_prefix() . 'login_button_styles' ), true );
    $button_parent_styles = csso_array_to_styles_attribute( $button_styles['parent'] );
    $data = "\n    <div style='" . esc_attr( $button_parent_styles ) . "'>\n    <p>\n        Hello, " . esc_html( $user->display_name ) . " |\n        <a href=" . esc_url( $logout_url ) . ">Logout</a>\n    </p>\n    </div>";
    return $data;
}

function csso_login_widget( $provider, $redirect_url, $button = false ) : string
{
    $current_user = wp_get_current_user();
    if ( !$provider ) {
        return '';
    }
    
    if ( $current_user->ID ) {
        return csso_user_logout_info( $current_user );
    } else {
        return csso_provider_login_link( $provider, $redirect_url, $button );
    }

}

function csso_option_switcher(
    $option_name,
    $label,
    bool $reversed = false,
    $label_class = ''
)
{
    $checked = csso_get_boolean_value( get_option( $option_name ) );
    ?>
    <form method="post" action="">
        <div class="row flex-nowrap align-items-center <?php 
    esc_attr_e( ( $reversed ? 'flex-row-reverse justify-content-end' : '' ) );
    ?>">
            <input type="hidden" name="option_value" value="false">
			<?php 
    if ( function_exists( 'wp_nonce_field' ) ) {
        wp_nonce_field( 'update_option', 'nonce' );
    }
    ?>
            <label class="switch flex-shrink-0 m-0 ">
                <input type="checkbox"
                       onchange="this.value = !this.value; this.form.submit()"
                       name="option_value"
                       value="<?php 
    esc_attr_e( $checked );
    ?>"
					<?php 
    checked( $checked );
    ?>
                >
                <span class="slider round"></span>
            </label>
            <input type="hidden" name="action" value="update_option">
            <input type="hidden" name="option_name" value="<?php 
    esc_attr_e( $option_name );
    ?>">
            <p class="text-main <?php 
    esc_attr_e( ( $reversed ? 'col-4 col-xl-2 p-0' : 'ml-3' ) );
    ?>"><?php 
    esc_html_e( $label );
    ?></p>
        </div>
    </form>
	<?php 
}

function csso_provider_section( $provider )
{
    global  $supported_providers ;
    $selected_provider = $supported_providers->csso_get_selected();
    ?>
    <div class="provider-section text-wrap text-center">
        <a class="provider-select-button <?php 
    esc_attr_e( ( $selected_provider['slug'] == $provider['slug'] ? 'selected-provider' : '' ) );
    ?>"
           href="<?php 
    echo  esc_url( csso_concat_query_args( 'selected_provider', $provider['slug'] ) ) ;
    ?>">
            <img class="provider-logo" src="<?php 
    echo  esc_url( $provider['image_url'] ) ;
    ?>" width="20px" alt="">
        </a>
        <p class="provider-name text-break m-0 mt-2"><?php 
    esc_html_e( $provider['name'] );
    ?></p>
    </div>
	<?php 
}

function csso_leave_review_notification()
{
    $notification_dismiss_url = wp_nonce_url( site_url() . '?action=dismiss_review_notification', 'dismiss_review_notification' );
    ?>
    <div class="notice leave-review-wrapper ">
        <a href="<?php 
    echo  esc_url( $notification_dismiss_url ) ;
    ?>" class="leave-review-dismiss-button">
            <img src="<?php 
    echo  esc_url( csso_get_plugin_url() . 'assets/resources/images/dismiss.svg' ) ;
    ?>" alt="">
        </a>
        <div style="display: flex">
            <img width="96px" height="96px"
                 src="<?php 
    esc_attr_e( csso_get_plugin_url() . 'assets/resources/images/logo.png' );
    ?>">
            <div class="leave-review-content-wrapper">
                <p class="leave-review-main-text">
                    Thank you for using WP Cloud SSO. To help support our team, can you spare 30 seconds to leave us a
                    review on WordPress :)️
                </p>
                <div>
					<?php 
    foreach ( range( 0, 4 ) as $item ) {
        ?>
                        <img src="<?php 
        esc_attr_e( csso_get_plugin_url() . 'assets/resources/images/leave-review-star.png' );
        ?>"
                             width="32" alt="">
						<?php 
    }
    ?>
                </div>
                <a href="https://wordpress.org/plugins/cloud-sso-single-sign-on" target="_blank"
                   class="leave-review-wordpress-link">Click Here to Leave Review on WordPress</a>
            </div>
        </div>
    </div>
	<?php 
}

<?php

namespace CloudSingleSignOn\base;

use WP_Widget;

class CSSO_LoginWidget extends WP_Widget {

	function __construct() {
		parent::__construct(
			csso_get_plugin_prefix() . 'login_widget',
			__( 'Single Sign-On Link', csso_get_plugin_prefix() . 'widget_domain' ),
			array( 'description' => __( 'Single Sign-On Link', csso_get_plugin_prefix() . 'widget_domain' ), )
		);
	}

	public function widget( $args, $instance ) {
		global $providers_manager;

		if ( ! count( $providers_manager->csso_get_all_by_current_env() ) ) {
			return;
		}

		$provider = $providers_manager->csso_get_one_by( 'provider', $instance['provider_name'] );
		echo $args['before_widget'];
		echo csso_login_widget( $provider, home_url(), csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'buttons_as_widget' ) ) );
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		global $providers_manager;
		$providers = $providers_manager->csso_get_all_by_current_env();

		if ( ! count( $providers ) ) {
			?>
            <p>Please, configure providers first</p>
			<?php
		}
		?>
        <label for="<?php esc_attr_e( $this->get_field_id( 'provider_name' ) ); ?>">Select provider:</label>
        <select id="<?php esc_attr_e( $this->get_field_id( 'provider_name' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'provider_name' ) ); ?>" class="widefat" style="width:100%;">
            <option value="empty">----------------</option>
			<?php foreach ( $providers_manager->csso_get_all_by_current_env() as $provider ) { ?>
                <option <?php selected( $instance['provider_name'], $provider['provider'] ); ?> value="<?php esc_attr_e( $provider['provider'] ); ?>"><?php esc_html_e( $provider['name'] ); ?></option>
			<?php } ?>
        </select>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                  = array();
		$instance['provider_name'] = ( ! empty( $new_instance['provider_name'] ) ) ? strip_tags( $new_instance['provider_name'] ) : '';

		return $instance;
	}
}
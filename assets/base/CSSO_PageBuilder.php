<?php

namespace CloudSingleSignOn\base;

use WP_User;

class CSSO_PageBuilder {
	private $callbacks;
	private $pages;
	private $subpages;
	/**
	 * @var CSSO_ProvidersManager
	 */
	private $providers_manager;

	public function csso_register() {
		$this->csso_set_default_values();
		$this->csso_register_shortcodes();
		$this->csso_build_login_widget();
		add_action( 'admin_menu', array( $this, 'csso_build_admin_menu' ) );
		add_action( 'login_form', array( $this, 'csso_build_login_page' ) );
		add_action( 'show_user_profile', array( $this, 'csso_custom_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'csso_custom_user_profile_fields' ) );
		add_filter( 'get_avatar', array( $this, 'csso_change_avatar_path' ), 100, 6 );
	}

	private function csso_set_default_values() {
		global $providers_manager;
		$this->providers_manager = $providers_manager;
		$this->callbacks         = new CSSO_AdminCallbacks();
	}

	public function csso_register_shortcodes() {
		foreach ( $this->providers_manager->csso_get_providers_shortcodes_callbacks() as $short_code => $callback ) {
			add_shortcode( $short_code, $callback );
		}
	}

	public function csso_build_login_widget() {
		add_action( 'widgets_init', function () {
			register_widget( CSSO_LoginWidget::class );
		} );
	}

	public function csso_build_admin_menu() {
		$this->csso_set_pages();
		$this->csso_set_subpages();
		$this->csso_register_pages();
	}

	private function csso_set_pages() {
		$this->pages = [
			[
				'page_title' => 'Custom Menu',
				'menu_title' => 'WP Cloud SSO',
				'capability' => 'manage_options',
				'menu_slug'  => csso_get_plugin_page_name(),
				'callback'   => array( $this->callbacks, 'csso_dashboard' ),
				'icon_url'   => csso_get_plugin_url() . 'assets/resources/images/wp-cloud-sso-icon20.png',
				'position'   => 110,
			],
		];
	}

	private function csso_set_subpages() {
		$this->subpages = [
			[
				'parent_slug' => csso_get_plugin_page_name(),
				'page_title'  => 'Plugin configuration',
				'menu_title'  => 'Plugin configuration',
				'capability'  => 'manage_options',
				'menu_slug'   => csso_get_plugin_page_name(),
				'callback'    => array( $this->callbacks, 'csso_dashboard' ),
			],
		];
	}

	private function csso_register_pages() {
		foreach ( $this->pages as $page ) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}
		foreach ( $this->subpages as $subpage ) {
			add_submenu_page( $subpage['parent_slug'], $subpage['page_title'], $subpage['menu_title'], $subpage['capability'], $subpage['menu_slug'], $subpage['callback'] );
		}
	}

	public function csso_build_login_page() {
		$providers = $this->providers_manager->csso_get_all_by_current_env();

		if ( ! empty( $providers ) && csso_get_boolean_value( get_option( csso_get_plugin_prefix() . 'buttons_on_login_page' ) ) ) {
			foreach ( $providers as $provider ) {
				echo csso_provider_login_link( $provider, site_url() . '/wp-admin', true );
			}
		}
		$logo_url = csso_get_plugin_url() . '/assets/resources/images/wp-cloud-secure-logo.png';
		$html     = "
		    <script>
                window.onload = function () {
                    let form = document.querySelector('form#loginform')
                    let element = csso_get_logo_element()
                    form.insertBefore(element, form.firstChild)
                }
                function csso_get_logo_element() {
                    let div = document.createElement('div')
                    div.innerHTML = `<a target='_blank' href='https://cloudinfrastructureservices.co.uk/'>
                                        <div style='display: flex; justify-content: center; padding: 30px 0'>
                                            <img src='" . esc_url( $logo_url ) . "'>
                                        </div>
                                    </a>`
                    return div.firstChild
                }
            </script>
		";
		echo $html;
	}

	public function csso_change_avatar_path( $img, $id_or_email, $size, $def, $alt, $args ) {
		$base64 = get_user_meta( $id_or_email, csso_get_plugin_prefix() . 'user_avatar', true );
		if ( $base64 ) {
			$class = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );

			if ( ! $args['found_avatar'] || $args['force_default'] ) {
				$class[] = 'avatar-default';
			}

			if ( $args['class'] ) {
				if ( is_array( $args['class'] ) ) {
					$class = array_merge( $class, $args['class'] );
				} else {
					$class[] = $args['class'];
				}
			}

			// Add `loading` attribute.
			$extra_attr = $args['extra_attr'];
			$loading    = $args['loading'];

			if ( in_array( $loading, array(
					'lazy',
					'eager'
				), true ) && ! preg_match( '/\bloading\s*=/', $extra_attr ) ) {
				if ( ! empty( $extra_attr ) ) {
					$extra_attr .= ' ';
				}

				$extra_attr .= "loading='{$loading}'";
			}

			$img = sprintf(
				"<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>",
				esc_attr( $args['alt'] ),
				esc_attr( $base64 ),
				' ',
				esc_attr( implode( ' ', $class ) ),
				esc_attr( (int) $args['height'] ),
				esc_attr( (int) $args['width'] ),
				esc_html( $extra_attr )
			);

			$args['url'] = $base64;
		}

		return $img;
	}

	public function csso_custom_user_profile_fields( WP_User $user ) {
		$custom_metas = array_filter( get_user_meta( $user->ID ), function ( $meta_key ) {
			return strpos( $meta_key, csso_get_plugin_prefix() ) !== false && $meta_key !== csso_get_plugin_prefix() . 'user_avatar';
		}, ARRAY_FILTER_USE_KEY );

		$parse_usermeta = function ($value) {
			if ($decoded_value = json_decode(current($value), true)) {
				return is_array($value) ? implode(', ', $decoded_value) : $value;
			}
			return current($value);
		};

		if ( $custom_metas ) { ?>
            <h2>WP Cloud SSO Custom Fields</h2>
            <table class="form-table" role="presentation">
                <tbody>
				<?php foreach ( $custom_metas as $meta_key => $value ) {
					$clean_key = str_replace( csso_get_plugin_prefix(), '', $meta_key );
					?>
                    <tr>
                        <th>
                            <label for="<?php esc_attr_e( $meta_key ); ?>">
								<?php esc_attr_e( $clean_key ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   name="<?php esc_attr_e( $meta_key ); ?>"
                                   id="<?php esc_attr_e( $meta_key ); ?>"
                                   value="<?php esc_attr_e( $parse_usermeta($value) ); ?>"
                                   disabled="disabled"
                                   class="regular-text">
                        </td>
                    </tr>
				<?php } ?>
                </tbody>
            </table>
		<?php }
	}
}
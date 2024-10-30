<?php
global $supported_providers;
global $providers_manager;
$current_provider     = $supported_providers->csso_get_selected();
$configured_providers = $providers_manager->csso_get_all_by_current_env();
$selected_config      = $providers_manager->csso_get_config_from_selected();
$attribute_mapping    = $providers_manager->csso_get_attribute_mapping( 'attribute_mapping', $selected_config['id'] ?? null ) ?? null;
$custom_attributes    = $providers_manager->csso_get_attribute_mapping( 'custom_attributes', $selected_config['id'] ?? null ) ?? null;
$role_mapping         = $providers_manager->csso_get_attribute_mapping( 'role_mapping', $selected_config['id'] ?? null ) ?? null;

function csso_is_selected_and_configured( array $configured_providers, ?array $current_provider ): bool {
	foreach ( $configured_providers as $provider ) {
		if ( $provider['provider'] === $current_provider['slug'] ) {
			return true;
		}
	}

	return false;
}

$is_group = ! empty( $attribute_mapping['group'] );
?>

<script>
    function csso_delete_row(event) {
        let child = event.target.parentElement;
        let parent = child.parentElement;
        parent.removeChild(child);
    }
</script>

<div>
    <div class="overflow-section">
        <div class="select-provider-section">
            <div>
				<?php
				if ( ! count( $configured_providers ) ) {
					?>
                    <div>
                        <h3 class="text-header-main">
                            No configured IDP found.
                        </h3>
                        <p class="text-main mt-2">
                            Please configure IDP first
                        </p>
						<?php csso_link_button( 'admin.php?page=' . csso_get_plugin_page_name() . '&tab=setup_identity_provider', 'Identity Provider Setup', ButtonTypes::PRIMARY, true, 'mt-3' ); ?>
                    </div>
				<?php } ?>
            </div>
            <div class="<?php esc_attr_e( $configured_providers ? '' : 'd-none' ); ?>">
                <h1 class="page-header">Configured Identity Providers</h1>
                <div class="row justify-content-center available-providers">
					<?php foreach ( $configured_providers as $conf_provider ) {
						$provider = $supported_providers->csso_get( $conf_provider['provider'] );
						csso_provider_section( $provider );
					} ?>
                </div>
            </div>
        </div>
    </div>
	<?php
	if ( csso_is_selected_and_configured( $configured_providers, $current_provider ) ) { ?>
        <div class="row flex-xl-nowrap flex-lg-wrap mt-4 overflow-section">
            <div class="col-lg-12 col-xl-6 p-0 pr-xl-2">

                <div class="page-section section-without-overflow">
					<?php csso_page_header( 'Attribute Mapping' ); ?>
                    <div class="section-wrapper">
                        <form method="post" action="">
							<?php
							if ( function_exists( 'wp_nonce_field' ) ) {
								wp_nonce_field( 'attribute_mapping', 'nonce' );
							} ?>
                            <input type="hidden" name="action" value="attribute_mapping"/>
                            <input type="hidden" name="provider_id" value="<?php esc_attr_e( $selected_config['id'] ); ?>"/>

                            <div class="row align-items-center justify-content-between">
                                <p class="text-main ">Username</p>
                                <div class="col-8 p-0">
                                    <input type="text" value="NameID" readonly class="wcs-input-field w-100">
                                </div>
                            </div>

                            <div class="row align-items-center justify-content-between mt-4">
                                <p class="text-main ">Email</p>
                                <div class="col-8 p-0">
                                    <input type="text" value="NameID" readonly class="wcs-input-field w-100">
                                </div>
                            </div>

                            <div class="row align-items-center justify-content-between mt-4">
                                <p class="text-main ">First Name</p>
                                <div class="col-8 p-0">
                                    <input type="text" name="first_name"
                                           placeholder="Enter attribute name"
                                           class="wcs-input-field w-100"
                                           value="<?php esc_attr_e( $attribute_mapping['first_name'] ?? '' ); ?>"
                                           pattern="^[a-zA-Z0-9_]+$"
                                           title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                           maxlength="50"
                                    >
                                </div>
                            </div>

                            <div class="row align-items-center justify-content-between mt-4">
                                <p class="text-main ">Last Name</p>
                                <div class="col-8 p-0">
                                    <input type="text" name="last_name"
                                           placeholder="Enter attribute name"
                                           class="wcs-input-field w-100"
                                           value="<?php esc_attr_e( $attribute_mapping['last_name'] ?? '' ); ?>"
                                           pattern="^[a-zA-Z0-9_]+$"
                                           title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                           maxlength="50"
                                    ></div>
                            </div>

                            <div class="row align-items-center justify-content-between mt-4">
                                <p class="text-main ">Nickname</p>
                                <div class="col-8 p-0">
                                    <input type="text" name="nickname"
                                           placeholder="Enter attribute name"
                                           class="wcs-input-field w-100"
                                           value="<?php esc_attr_e( $attribute_mapping['nickname'] ?? '' ); ?>"
                                           pattern="^[a-zA-Z0-9_]+$"
                                           title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                           maxlength="50"
                                    >
                                </div>
                            </div>

                            <div class="row align-items-center justify-content-between mt-4">
                                <p class="text-main ">Display Name</p>
                                <div class="col-8 p-0">
                                    <input type="text" name="display_name"
                                           placeholder="Enter attribute name"
                                           class="wcs-input-field w-100"
                                           value="<?php esc_attr_e( $attribute_mapping['display_name'] ?? '' ); ?>"
                                           pattern="^[a-zA-Z0-9_]+$"
                                           title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                           maxlength="50"
                                    >
                                </div>
                            </div>
                            <div class="<?php esc_attr_e(handle_premium_version( 'mt-3' )); ?>">
                                <div class="row align-items-center justify-content-between <?php esc_attr_e(wpcsso_fs()->can_use_premium_code__premium_only() ? 'mt-4' : '')?>">
                                    <p class="text-main">Group</p>
                                    <div class="col-8 p-0">
                                        <input type="text" name="group"
                                               placeholder="Enter attribute name" class="wcs-input-field w-100"
                                               value="<?php esc_attr_e($attribute_mapping['group'] ?? '' ); ?>"
                                               pattern="^[a-zA-Z0-9_]+$"
                                               title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                               maxlength="50"
                                        >
                                    </div>
                                    <p class="prem-feature-description text-main">
                                        Group field is available in Premium and Enterprise versions of the plugin.
                                        <a class="text-main" href='<?php echo esc_url(get_pricing_url()); ?>'>
                                            Click here to upgrade your plan
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
								<?php csso_submit_button(); ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-xl-6 p-0 pl-xl-2 mt-4 mt-xl-0">
                <div class="page-section section-without-overflow <?php esc_attr_e(handle_premium_version( 'p-0' ));?>">
					<?php csso_page_header( 'Custom Attributes' ); ?>
                    <div class="section-wrapper">
                        <form method="post" class="" action="">
                            <p class="prem-feature-description text-main">
                                Custom Attributes Mapping is available in Premium and Enterprise versions of the plugin.
                                <a class="text-main" href='<?php echo esc_url(get_pricing_url()); ?>'>
                                    Click here to upgrade your plan
                                </a>
                            </p>
							<?php
							if ( function_exists( 'wp_nonce_field' ) ) {
								wp_nonce_field( 'custom_attributes', 'nonce' );
							} ?>
                            <input type="hidden" name="action" value="custom_attributes"/>
                            <input type="hidden" name="provider_id" value="<?php esc_attr_e( $selected_config['id'] ); ?>"/>
							<?php if ( $custom_attributes ) {
								foreach ( $custom_attributes as $name => $value ) { ?>
                                    <div class="row align-items-center flex-nowrap mb-4">
                                        <div class="col-4 p-0">
                                            <input type="text" name="custom_name[]"
                                                   placeholder="Enter custom attribute name"
                                                   class="w-100 wcs-input-field"
                                                   value="<?php esc_attr_e( $name ); ?>" required
                                                   pattern="^[a-zA-Z0-9_]+$"
                                                   title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                                   maxlength="50"
                                            >
                                        </div>
                                        <div class="flex-grow-1 ml-3">
                                            <input type="text" name="custom_value[]"
                                                   placeholder="Enter provider attribute name"
                                                   class="w-100 wcs-input-field"
                                                   value="<?php esc_attr_e( $value ); ?>" required
                                                   pattern="^[a-zA-Z0-9_]+$"
                                                   title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                                   maxlength="50"
                                            >
                                        </div>
                                        <button class="wcs-btn <?php esc_attr_e(wpcsso_fs()->can_use_premium_code__premium_only() ? 'wcs-danger-btn' : 'wcs-btn-disabled'); ?> ml-3" onclick="csso_delete_row(event)">Delete
                                        </button>
                                    </div>
								<?php }
							} ?>
                            <div class="row align-items-center flex-nowrap">
                                <div class="col-4 p-0">
                                    <input type="text" name="custom_name[]"
                                           placeholder="Enter custom attribute name" class="w-100 wcs-input-field"
                                           value=""
                                           pattern="^[a-zA-Z0-9_]+$"
                                           title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                           maxlength="50"
                                    >
                                </div>
                                <div class="flex-grow-1 p-0 ml-3">
                                    <input type="text" name="custom_value[]"
                                           placeholder="Enter provider attribute name" class="w-100 wcs-input-field"
                                           value=""
                                           pattern="^[a-zA-Z0-9_]+$"
                                           title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                           maxlength="50"
                                    >
                                </div>
                                <button class="wcs-btn ml-3 invisible">delete</button>
                            </div>
                            <div class="mt-4">
								<?php csso_submit_button(null, wpcsso_fs()->can_use_premium_code__premium_only() ? '' : 'wcs-btn-disabled'); ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="page-section mt-4">
			<?php csso_page_header( 'Role Mapping' ); ?>
            <div class="section-wrapper">
                <form method="post" class="" action="">
					<?php
					if ( function_exists( 'wp_nonce_field' ) ) {
						wp_nonce_field( 'role_mapping', 'nonce' );
					} ?>
                    <input type="hidden" name="action" value="role_mapping"/>
                    <input type="hidden" name="provider_id" value="<?php esc_attr_e( $selected_config['id'] ); ?>"/>

                    <div class="row flex-nowrap justify-content-xl-start justify-content-lg-between align-items-center">
                        <p class="text-main col-xl-2 col-lg-3 p-0">Default Role</p>
                        <div class="col-xl-6 col-8 p-0">
                            <select name="default_role" class="wcs-input-field col-8">
								<?php
								foreach ( [ 'Subscriber', 'Contributor', 'Author', 'Editor', 'Administrator' ] as $role ) {
									?>
                                    <option <?php selected( $role_mapping['default_role'], strtolower( $role ) ) ?>
                                            value="<?php esc_attr_e( strtolower( $role ) ); ?>">
										<?php esc_html_e( $role ); ?>
                                    </option>
									<?php
								}
								?>
                            </select>
                        </div>
                    </div>
                    <div class="<?php esc_attr_e($is_group ? '' : 'group-empty '); ?> <?php esc_attr_e(handle_premium_version( 'mt-3' )); ?>">
						<?php
						if ( ! $is_group and wpcsso_fs()->can_use_premium_code__premium_only() ) {
							?>
                            <h3 class="text-header-main mt-4">Specify the group attribute name to use these fields</h3>
						    <?php						}
						?>
                        <p class="prem-feature-description text-main">Customized Role Mapping is available in Premium
                            and Enterprise versions of the plugin. <a class="text-main" href='<?php echo esc_url(get_pricing_url()); ?>'>Click
                                here to upgrade your plan</a></p>

                        <div class="row flex-nowrap justify-content-xl-start justify-content-between align-items-center <?php esc_attr_e(wpcsso_fs()->can_use_premium_code__premium_only() ? 'mt-4' : ''); ?>">
                            <p class="text-main col-xl-2 col-lg-auto col-md-auto p-0">Administrator</p>
                            <div class="col-xl-6 col-8 p-0">
                                <input type="text"
                                       name="administrator"
                                       placeholder="Use semi-colon(;) to separate values"
                                       class="w-100 wcs-input-field"
									<?php disabled( ! $is_group ); ?>
                                       value="<?php esc_attr_e( $role_mapping['administrator'] ?? '' ); ?>"
                                       pattern="(\S+?)"
                                       title="No spacing in-between words"
                                       maxlength="200"
                                >
                            </div>
                        </div>

                        <div class="row flex-nowrap justify-content-xl-start justify-content-lg-between align-items-center mt-4">
                            <p class="text-main col-xl-2 col-lg-3 p-0">Editor</p>
                            <div class="col-xl-6 col-8 p-0">
                                <input type="text"
                                       name="editor"
                                       placeholder="Use semi-colon(;) to separate values"
                                       class="w-100 wcs-input-field"
									<?php disabled( ! $is_group ) ?>
                                       value="<?php esc_attr_e( $role_mapping['editor'] ?? '' ); ?>"
                                       pattern="(\S+?)"
                                       title="No spacing in-between words"
                                       maxlength="200"
                                >
                            </div>
                        </div>

                        <div class="row flex-nowrap justify-content-xl-start justify-content-lg-between align-items-center mt-4">
                            <p class="text-main col-xl-2 col-lg-3 p-0">Author</p>
                            <div class="col-xl-6 col-8 p-0">
                                <input type="text"
                                       name="author"
                                       placeholder="Use semi-colon(;) to separate values"
                                       class="w-100 wcs-input-field"
									<?php disabled( ! $is_group ); ?>
                                       value="<?php esc_attr_e( $role_mapping['author'] ?? '' ); ?>"
                                       pattern="(\S+?)"
                                       title="No spacing in-between words"
                                       maxlength="200"
                                >
                            </div>
                        </div>

                        <div class="row flex-nowrap justify-content-xl-start justify-content-lg-between align-items-center mt-4">
                            <p class="text-main col-xl-2 col-lg-3 p-0">Contributor</p>
                            <div class="col-xl-6 col-8 p-0">
                                <input type="text"
                                       name="contributor"
                                       placeholder="Use semi-colon(;) to separate values"
                                       class="w-100 wcs-input-field"
									<?php disabled( ! $is_group ) ?>
                                       value="<?php esc_attr_e( $role_mapping['contributor'] ?? '' ); ?>"
                                       pattern="(\S+?)"
                                       title="No spacing in-between words"
                                       maxlength="200"
                                >
                            </div>
                        </div>

                        <div class="row flex-nowrap justify-content-xl-start justify-content-lg-between align-items-center mt-4">
                            <p class="text-main col-xl-2 col-lg-3 p-0">Subscriber</p>
                            <div class="col-xl-6 col-8 p-0">
                                <input type="text"
                                       name="subscriber"
                                       placeholder="Use semi-colon(;) to separate values"
                                       class="w-100 wcs-input-field"
									<?php disabled( ! $is_group ); ?>
                                       value="<?php esc_attr_e( $role_mapping['subscriber'] ?? '' ); ?>"
                                       pattern="(\S+?)"
                                       title="No spacing in-between words"
                                       maxlength="200"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
						<?php csso_submit_button(); ?>
                    </div>
                </form>
            </div>
        </div>

	<?php } ?>
</div>

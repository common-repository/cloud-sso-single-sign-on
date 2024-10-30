<div class="page-section">
	<?php csso_page_header( 'Configure Service Provider' ) ?>
    <div class="row flex-xl-nowrap flex-lg-wrap section-wrapper ">
        <div class="col-lg-12 col-xl-6 p-0 pr-xl-5 pr-lg-0">
            <p class="text-header-main mb-4">Organization Settings</p>
            <form action="" method="post" class="form-section-wrapper">
                <input type="hidden" name="action" value="set_organization_settings"/>
                <div class="service-provider-field">
                    <p class="text-main">Organization Name</p>
                    <input
                            type="text"
                            class="wcs-input-field"
                            name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_org_name' ); ?>"
                            value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_org_name' ) ); ?>"
                            pattern="\S(.*\S)?"
                            title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                            maxlength="200"
                    >
                </div>
                <div class="service-provider-field">
                    <p class="text-main">Organization Display Name</p>
                    <input
                            type="text"
                            class="wcs-input-field"
                            name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_org_display_name' ); ?>"
                            value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_org_display_name' ) ); ?>"
                            pattern="\S(.*\S)?"
                            title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                            maxlength="200"
                    >
                </div>
                <div class="service-provider-field">
                    <p class="text-main">Organization Url</p>
                    <input
                            type="url"
                            class="wcs-input-field"
                            name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_org_url' ); ?>"
                            value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_org_url' ) ); ?>"
                            pattern="(\S+?)"
                            title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                            maxlength="200"
                    >
                </div>
                <div class="service-provider-field">
                    <p class="text-main">Contact Person Name (Technical)</p>
                    <input
                            type="text"
                            class="wcs-input-field"
                            name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_technical_name' ); ?>"
                            value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_technical_name' ) ); ?>"
                            pattern="\S(.*\S)?"
                            title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                            maxlength="200"
                    >
                </div>
                <div class="service-provider-field">
                    <p class="text-main">Contact Person Email (Technical)</p>
                    <input
                            type="email"
                            class="wcs-input-field"
                            name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_technical_email' ); ?>"
                            value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_technical_email' ) ); ?>"
                            pattern="(\S+?)"
                            title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                            maxlength="200"
                    >
                </div>
                <div class="service-provider-field">
                    <p class="text-main">Contact Person Name (Support)</p>
                    <input
                            type="text"
                            class="wcs-input-field"
                            name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_support_name' ); ?>"
                            value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_support_name' ) ); ?>"
                            pattern="\S(.*\S)?"
                            title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                            maxlength="200"
                    >
                </div>
                <div class="service-provider-field">
                    <p class="text-main">Contact Person Email (Support)</p>
                    <input
                            type="email"
                            class="wcs-input-field"
                            name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_support_email' ); ?>"
                            value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_support_email' ) ); ?>"
                            pattern="(\S+?)"
                            title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                            maxlength="200"
                    >
                </div>
                <div class="service-provider-field">
                    <p class="text-main">Enable signing</p>
                    <div class="service-provider-organization-checkbox">
                        <input
                                value="on"
                                type="checkbox"
                                name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_enable_signing' ); ?>"
							<?php checked( boolval( csso_get_option_value( csso_get_plugin_prefix() . 'sp_enable_signing' ) ) ) ?>
                        >
                    </div>
                </div>
                <div class="service-provider-field">
                    <p class="text-main">Enable encryption</p>
                    <div class="service-provider-organization-checkbox">
                        <input
                                value="on"
                                type="checkbox"
                                name="<?php esc_attr_e( csso_get_plugin_prefix() . 'sp_enable_encryption' ); ?>"
							<?php checked( boolval( csso_get_option_value( csso_get_plugin_prefix() . 'sp_enable_encryption' ) ) ) ?>
                        >
                    </div>
                </div>

				<?php csso_submit_button() ?>
            </form>
        </div>
        <div class="col-lg-12 col-xl-6 p-0 pl-xl-2 pr-xl-5 pl-lg-0 mt-0 mt-sm-5 mt-xl-0">
            <div class="form-section-wrapper">
                <p class="text-header-main mb-4">Note the following to configure the IDP</p>
                <div class="service-provider-field">
                    <p class="text-main">SP EntityID</p>
                    <div class="service-provider-copy-field">
                        <input
                                class="wcs-input-field"
                                type='text'
                                name='sp_entity_id'
                                value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_entity_id' ) ); ?>"
                                readonly
                        >
                        <button class='btn ml-2 sp-copy-button service-provider-copy-btn'></button>
                    </div>
                </div>
                <div class="service-provider-field">
                    <p class="text-main">ACS (Assertion Consumer Service) URL</p>
                    <div class="service-provider-copy-field">
                        <input
                                class="wcs-input-field"
                                type='text'
                                name='sp_acs_url'
                                value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_acs_url' ) ); ?>"
                                readonly
                        >
                        <button class='btn ml-2 sp-copy-button service-provider-copy-btn'></button>
                    </div>
                </div>
                <div class="service-provider-field">
                    <p class="text-main">SLO (Single Logout) URL</p>
                    <div class="service-provider-copy-field">
                        <input
                                class="wcs-input-field"
                                type='text'
                                name='sp_acs_url'
                                value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_slo_url' ) ); ?>"
                                readonly
                        >
                        <button class='btn ml-2 sp-copy-button service-provider-copy-btn'></button>
                    </div>
                </div>
                <div class="service-provider-field">
                    <p class="text-main">NAMEID Format</p>
                    <div class="service-provider-copy-field">
                        <input
                                class="wcs-input-field"
                                type='text'
                                name='sp_acs_url'
                                value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_name_id_format' ) ); ?>"
                                readonly
                        >
                        <button class='btn ml-2 sp-copy-button service-provider-copy-btn'></button>
                    </div>
                </div>
                <div class="service-provider-field download-link">
                    <p class="text-main">Metadata URL</p>
                    <div class="service-provider-copy-field">
                        <input
                                class="wcs-input-field"
                                type='text'
                                name='sp_acs_url'
                                value="<?php esc_attr_e( csso_get_option_value( csso_get_plugin_prefix() . 'sp_metadata_url' ) ); ?>"
                                readonly
                        >
                        <button class='btn ml-2 sp-copy-button service-provider-copy-btn'></button>
                    </div>
                </div>
                <div class="service-provider-field">
                    <p class="text-main"></p>
                    <div class="service-provider-copy-field">
						<?php csso_link_button( get_option( csso_get_plugin_prefix() . 'sp_metadata_download_url' ), 'Download XML file', ButtonTypes::PRIMARY, false ); ?>
                    </div>
                </div>
                <div class="service-provider-field align-items-start download-link">
                    <p class="text-main ">x509 certificate</p>
                    <div class="service-provider-copy-field">
                        <textarea rows='6' wrap='soft' type='text' class='wcs-input-field w-100'
                                  readonly><?php echo esc_textarea( get_option( csso_get_plugin_prefix() . 'sp_x509_certificate' ) ); ?></textarea>
                        <button class='btn ml-2 sp-copy-button service-provider-copy-btn'></button>
                    </div>
                </div>
                <div class="service-provider-field">
                    <p class="text-main"></p>
                    <div class="service-provider-copy-field">
						<?php csso_link_button( get_option( csso_get_plugin_prefix() . 'sp_x509_cert_download_url' ), 'Download x509 certificate', ButtonTypes::PRIMARY, false ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {

        $('.sp-copy-button').tooltip({placement: 'top', title: 'Click to copy'})
            .on('click', function () {
                let targetElement = $(this)
                navigator.clipboard.writeText(targetElement.closest('div').find('input, textarea').val())
                targetElement.attr('data-original-title', "Copied!").tooltip('show');
                setTimeout(function () {
                    targetElement.tooltip('hide').attr('data-original-title', "Click to copy").blur()
                }, 1000);
            })
    })
</script>

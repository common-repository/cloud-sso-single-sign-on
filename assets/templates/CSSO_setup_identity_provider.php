<?php

global  $supported_providers ;
global  $providers_manager ;
$current_provider = $supported_providers->csso_get_selected();
$configured_providers = $providers_manager->csso_get_all_by_current_env();
$available_providers = csso_get_available_providers( $supported_providers, $configured_providers );
$selected_config = $providers_manager->csso_get_config_from_selected();
$avatar_mapping = $providers_manager->csso_get_attribute_mapping( 'avatar_mapping', $selected_config['id'] ?? null ) ?? null;
$identity_name = $selected_config['name'] ?? null;
$issuer = $selected_config['entity_id_or_issuer'] ?? null;
$custom_sp_entity_id = $selected_config['custom_sp_entity_id'] ?? null;
$login_url = $selected_config['saml_login_url'] ?? null;
$certificates = $selected_config['x509_certificates'] ?? null;
function csso_get_available_providers( \CloudSingleSignOn\base\CSSO_SupportedProviders $supported_providers, array $configured_providers ) : array
{
    return array_filter( $supported_providers->csso_get_all(), function ( $provider ) use( $configured_providers ) {
        return !in_array( $provider['slug'], array_column( $configured_providers, 'provider' ) );
    } );
}

function csso_is_provider_configured( $current_provider, $configured_providers ) : bool
{
    foreach ( $configured_providers as $configured_provider ) {
        if ( $current_provider['slug'] === $configured_provider['provider'] ) {
            return true;
        }
    }
    return false;
}

?>

<script>
    function csso_checkMetadataFile() {
        jQuery("#metadata-file").prop("required", true);
        jQuery("#metadata-url").prop("required", false);
        jQuery("#metadata-submit-button").click();
    }

    function csso_checkMetadataUrl() {
        jQuery("#metadata-file").prop("required", false);
        jQuery("#metadata-url").prop("required", true);
        jQuery("#metadata-submit-button").click();
    }

    function csso_verifyCert(e) {
        if (csso_checkCert(e.target.value)) {
            document.getElementById('invalid-format').classList.add('hidden')
            jQuery('.wcs-save-btn').removeClass('wcs-btn-disabled')
        } else {
            document.getElementById('invalid-format').classList.remove('hidden')
            jQuery('.wcs-save-btn').addClass('wcs-btn-disabled')
        }
    }

    function csso_checkCert(cert) {
        if (cert === '') {
            return true;
        }
        let begin = cert.slice(0, 28)
        let end = cert.slice(-26)
        return begin === "-----BEGIN CERTIFICATE-----\n" && end === "\n-----END CERTIFICATE-----";
    }
</script>

<div>
    <div class="d-flex justify-content-between overflow-section">
		<?php 

if ( count( $available_providers ) ) {
    ?>
            <div class="select-provider-section available-providers <?php 
    esc_attr_e( handle_idp_config_permission( count( $configured_providers ), 'prem-info-block' ) );
    ?>">
                <p class="prem-feature-description text-main">
                    Multiple SAML IDP is available Enterprise versions of the plugin.
                    <a class="text-main" href='<?php 
    echo  esc_url( get_pricing_url() ) ;
    ?>'>
                        Click here to upgrade your plan
                    </a>
                </p>
                <h1 class="page-header">Please select Identity Provider</h1>
                <div class="row justify-content-center available-providers <?php 
    esc_attr_e( handle_idp_config_permission( count( $configured_providers ), 'provider' ) );
    ?>">
					<?php 
    foreach ( $available_providers as $provider ) {
        csso_provider_section( $provider );
    }
    ?>
                </div>
            </div>
		<?php 
}

?>

		<?php 

if ( count( $configured_providers ) ) {
    ?>
            <div class="select-provider-section ml-3 <?php 
    esc_attr_e( ( $available_providers ? 'w-50' : 'w-100' ) );
    ?>">
                <h1 class="page-header">Configured Identity Providers</h1>
                <div class="row justify-content-center available-providers">
					<?php 
    foreach ( $configured_providers as $conf_provider ) {
        $provider = $supported_providers->csso_get( $conf_provider['provider'] );
        csso_provider_section( $provider );
    }
    ?>
                </div>
            </div>
		<?php 
}

?>
    </div>
	<?php 

if ( $current_provider ) {
    ?>
        <div class="page-section mt-4">
			<?php 
    csso_page_header( 'Configure Identity Provider' );
    ?>

            <div class="mt-2 d-flex align-items-center section-wrapper">
                <div class="d-flex align-items-center">
                    <img class="selected-provider-logo" src="<?php 
    echo  esc_url( $current_provider['image_url'] ) ;
    ?>" alt="">
                </div>
                <div class="d-flex flex-column ml-4">
                    <p class="text-header-main"><?php 
    esc_html_e( $current_provider['name'] );
    ?></p>
                    <div class="provider-control-buttons-section d-flex align-items-center mt-3">
						<?php 
    csso_link_button(
        $current_provider['setup_guide_link'],
        'Setup Guide',
        ButtonTypes::SUCCESS,
        true,
        '',
        false,
        true
    );
    ?>

                        <?php 
    
    if ( csso_is_provider_configured( $current_provider, $configured_providers ) ) {
        $action = \CloudSingleSignOn\base\CSSO_SamlActions::TEST_PROVIDER_CONFIGURATION;
        $url = csso_non_cacheable_url( wp_nonce_url( site_url() . '?action=' . $action . '&provider_id=' . $selected_config['id'], $action ) );
        ?>
                            <button class="wcs-btn ml-2 wcs-light-btn" onclick="window.open('<?php 
        echo  esc_url( $url ) ;
        ?>', '_blank', 'height=900,width=1000');">
                                Test configuration
                            </button>
                            <?php 
    }
    
    ?>

                        <form method="post" class="ml-2" action="">
							<?php 
    if ( function_exists( 'wp_nonce_field' ) ) {
        wp_nonce_field( 'avatar_mapping', 'nonce' );
    }
    ?>
                            <input type="hidden" name="action" value="avatar_mapping"/>
                            <input type="hidden" name="provider_id" value="<?php 
    esc_attr_e( $selected_config['id'] );
    ?>"/>

							<?php 
    
    if ( csso_is_provider_configured( $current_provider, $configured_providers ) ) {
        ?>
                                <div class="prem-info p-2 mx-3">
                                    <button class="wcs-btn wcs-btn-disabled" type="button">Avatar Mapping</button>
                                    <p class="prem-feature-description text-main">Avatar Mapping is available in Enterprise versions of the plugin.
                                        <a class="text-main" href='<?php 
        echo  esc_url( get_pricing_url() ) ;
        ?>'>
                                            Click here to upgrade your plan
                                        </a>
                                    </p>
                                </div>
								<?php 
    }
    
    ?>
                        </form>

                        <form method="post" class="" action="">
                            <input type="hidden" name="action" value="delete_config"/>
                            <input type="hidden" name="selected_provider" value="<?php 
    esc_attr_e( $selected_config['id'] );
    ?>"/>
                            <button type="button" data-toggle="modal" data-target="#deleteConfigModal"
                                    class='wcs-btn wcs-danger-btn ml-2 <?php 
    esc_attr_e( ( csso_is_provider_configured( $current_provider, $configured_providers ) ? '' : 'd-none' ) );
    ?>'>
                                Delete config
                            </button>

                            <div class="modal fade" id="deleteConfigModal" tabindex="-1" role="dialog"
                                 aria-labelledby="deleteConfigModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3 class="text-header-main" id="deleteConfigModalLongTitle">Delete
                                                config</h3>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <p class="modal-body text-main">
                                            This action will delete all configs (including attribute mapping) for
                                            selected
                                            provider. Continue?
                                        </p>
                                        <div class="modal-footer">
                                            <button type="submit" class="wcs-btn wcs-danger-btn">Delete</button>
                                            <button type="button" class="wcs-btn wcs-light-btn" data-dismiss="modal">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="configure-idp-section mt-2">
                <div class="d-flex section_x_wrapper">

                    <div class="nav" id="nav-tab" role="tablist">
                        <div class="tab-button-section button-section active" id="enter-idp-manually-tab-link" data-toggle="tab" data-target="#enter-idp-manually-tab" type="button" role="tab" aria-controls="enter-idp-manually-tab" aria-selected="true">
                            <p class="text-main text-main-bolder px-3 mb-2">
                                Enter IDP Metadata Manually
                            </p>
                            <div class="tab-divider"></div>
                        </div>
                        <div class="tab-button-section button-section" id="upload-idp-tab-link" data-toggle="tab" data-target="#upload-idp-tab" type="button" role="tab" aria-controls="upload-idp-tab-link" aria-selected="false">
                            <p class="text-main text-main-bolder px-3 mb-2">
                                Upload IDP Metadata
                            </p>
                            <div class="tab-divider hidden"></div>
                        </div>
                        <?php 
    if ( csso_is_provider_configured( $current_provider, $configured_providers ) ) {
        ?>
                            <div class="tab-button-section button-section" id="idp-saml-settings-tab-link" data-toggle="tab" data-target="#idp-saml-settings-tab" type="button" role="tab" aria-controls="idp-saml-settings-tab" aria-selected="false">
                                <p class="text-main text-main-bolder px-3 mb-2">
                                    Saml Settings
                                </p>
                                <div class="tab-divider hidden"></div>
                            </div>
                            <?php 
    }
    ?>
                    </div>
                </div>
                <div class="section-divider"></div>
            </div>


            <div class="section-wrapper tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="enter-idp-manually-tab" role="tabpanel" aria-labelledby="enter-idp-manually-tab-link">
                    <form method="post" id="enter-idp-manually-form" action="">
		                <?php 
    if ( function_exists( 'wp_nonce_field' ) ) {
        wp_nonce_field( 'idp_config', 'nonce' );
    }
    ?>
                        <input type="hidden" name="action" value="idp_config"/>
                        <div class="row flex-md-wrap align-items-start">
                            <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">Identity Provider Name</p>
                            <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
                                <input type="text" name="saml_identity_name"
                                       placeholder="Identity Provider name like AzureAD, Okta, SalesForce"
                                       class="w-100 wcs-input-field"
                                       pattern="(\S+?)"
                                       title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)"
                                       maxlength="35"
                                       value="<?php 
    esc_attr_e( $identity_name );
    ?>" required>
                            </div>
                        </div>
                        <div class="row flex-md-wrap align-items-start mt-xl-4 mt-md-2">
                            <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">IdP Entity ID or Issuer</p>
                            <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
                                <input type="text" name="saml_issuer" id="saml_issuer"
                                       placeholder="Identity Provider Entity ID or Issuer"
                                       class="w-100 wcs-input-field"
                                       pattern="(\S+?)"
                                       title="Value must be URL"
                                       maxlength="200"
                                       value="<?php 
    esc_attr_e( $issuer );
    ?>" required="">
                                <p class="mt-1 text-main"><b>Note</b> : You can find the <b>EntityID</b> in Your
                                    IdP-Metadata XML
                                    file enclosed in <code class="code-transparent">EntityDescriptor</code> tag having
                                    attribute as <code
                                            class="code-transparent">entityID</code></p>
                            </div>
                        </div>
                        <div class="row flex-md-wrap align-items-start mt-xl-4 mt-md-2">
                            <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">SAML Login URL</p>
                            <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
                                <input type="url" name="saml_login_url"
                                       placeholder="Single Sign On Service URL (HTTP-Redirect binding) of your IdP"
                                       class="w-100 wcs-input-field"
                                       pattern="(\S+?)"
                                       title="Value must be URL"
                                       maxlength="200"
                                       value="<?php 
    esc_attr_e( $login_url );
    ?>" required="">
                                <p class="mt-1 text-main"><b>Note</b> : You can find the <b>SAML Login URL</b> in Your
                                    IdP-Metadata
                                    XML
                                    file enclosed in <code class="code-transparent">SingleSignOnService</code> tag (Binding
                                    type:
                                    HTTP-Redirect)</p>
                            </div>
                        </div>
                        <div class="row flex-md-wrap align-items-start mt-xl-4 mt-md-2 <?php 
    esc_attr_e( handle_premium_version() );
    ?>">
                            <p class="prem-feature-description text-main">
                                SAML Single Logout is available in Premium and Enterprise versions of the plugin.
                                <a class="text-main" href='<?php 
    echo  esc_url( get_pricing_url() ) ;
    ?>'>
                                    Click here to upgrade your plan
                                </a>
                            </p>
                            <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">SAML Logout URL</p>
                            <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
		                        <?php 
    ?>
                                    <input type="url"
                                           placeholder="Single logout URL of your IdP"
                                           class="w-100 wcs-input-field"
                                           disabled>
                                    <p class="mt-1 text-main"><b>Note</b> : You can find the <b>SAML logout URL</b> in Your
                                        IdP-Metadata XML
                                        file enclosed in <code class="code-transparent">SingleLogoutService</code></p>
		                        <?php 
    ?>
                            </div>
                        </div>
                        <div class="row flex-md-wrap align-items-start mt-xl-4 mt-md-2">
                            <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">X.509 Certificate</p>
                            <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
                                <div class="certificates">
                                    <div class="nav nav-tabs cert-tab-links" id="nav-tab" role="tablist">
						                <?php 
    
    if ( $certificates && count( $certificates ) ) {
        foreach ( $certificates as $index => $cert ) {
            ?>
                                                <button
                                                        class="nav-link <?php 
            esc_attr_e( ( $index === 0 ? 'active' : '' ) );
            ?>"
                                                        id="<?php 
            esc_attr_e( 'x509_cert' . $index );
            ?>"
                                                        data-toggle="tab"
                                                        data-target="<?php 
            esc_attr_e( '#x509-cert-tab' . $index );
            ?>"
                                                        type="button"
                                                        role="tab"
                                                        aria-controls="<?php 
            esc_attr_e( 'x509-cert-tab' . $index );
            ?>"
                                                        aria-selected="<?php 
            esc_attr_e( ( $index === 0 ? 'true' : 'false' ) );
            ?>">
									                <?php 
            esc_html_e( $index + 1 );
            ?>
                                                </button>
								                <?php 
        }
    } else {
        ?>
                                            <button
                                                    class="nav-link active"
                                                    id="x509_cert1"
                                                    data-toggle="tab"
                                                    data-target="#x509-cert-tab1"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="x509-cert-tab1"
                                                    aria-selected="true">
                                                1
                                            </button>
							                <?php 
    }
    
    ?>
                                        <span type="button" class="wcs-link-btn d-flex align-items-center px-2" id="add-cert-button">
                                        Add certificate
                                    </span>
						                <?php 
    if ( isset( $certificates ) && count( $certificates ) > 1 ) {
        ?>
                                            <span type="button" class="wcs-link-btn wcs-danger-link-btn d-flex align-items-center px-2" id="delete-cert-button">
                                            Delete certificate
                                        </span>
							                <?php 
    }
    ?>
                                    </div>
                                    <div class="tab-content certificate-tabs" id="nav-tabContent">
						                <?php 
    
    if ( $certificates && count( $certificates ) ) {
        foreach ( $certificates as $index => $cert ) {
            ?>
                                                <div
                                                        class="tab-pane fade <?php 
            esc_attr_e( ( $index === 0 ? 'show active' : '' ) );
            ?>"
                                                        id="<?php 
            esc_attr_e( 'x509-cert-tab' . $index );
            ?>"
                                                        role="tabpanel"
                                                        aria-labelledby="<?php 
            esc_attr_e( 'x509_cert' . $index );
            ?>">

                                            <textarea rows="4" cols="5" name="x509[]" oninput="csso_verifyCert(event);"
                                                      placeholder="Copy and Paste the content from the downloaded certificate or copy the content enclosed in X509Certificate tag (has parent tag KeyDescriptor use=signing) in IdP-Metadata XML file"
                                                      class="w-100 wcs-input-field mt-2"
                                                      required=""><?php 
            echo  esc_textarea( $cert ) ;
            ?></textarea>
                                                </div>

								                <?php 
        }
    } else {
        ?>
                                            <div
                                                    class="tab-pane fade show active"
                                                    id="x509-cert-tab1"
                                                    role="tabpanel"
                                                    aria-labelledby="x509_cert1">

                                            <textarea rows="4" cols="5" name="x509[]" oninput="csso_verifyCert(event);"
                                                      placeholder="Copy and Paste the content from the downloaded certificate or copy the content enclosed in X509Certificate tag (has parent tag KeyDescriptor use=signing) in IdP-Metadata XML file"
                                                      class="w-100 wcs-input-field mt-2"
                                                      required=""></textarea>
                                            </div>
							                <?php 
    }
    
    ?>
                                    </div>
                                </div>
                                <p class="py-1 text-main text-danger hidden" id="invalid-format">Invalid
                                    Format</p>
                                <p class="mt-1 text-main"><b>Note</b> : Format of the certificate - <br><b
                                            class="text-secondary">-----BEGIN
                                        CERTIFICATE-----<br>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br>-----END
                                        CERTIFICATE-----</b></p>
                            </div>
                        </div>
                        <button type="button" class="wcs-btn wcs-save-btn mt-4" id="manually-save-button">
                            Save
                        </button>

                        <input type="submit" id="manually-submit-button" class="d-none"/>
                    </form>
                </div>
                <div class="tab-pane fade <?php 
    esc_attr_e( handle_premium_version() );
    ?>" id="upload-idp-tab" role="tabpanel" aria-labelledby="upload-idp-tab-link">
                    <form method="post" id="upload-idp-form" action="" enctype="multipart/form-data">
                        <p class="prem-feature-description text-main" style="right: 1.9rem; top: -2.1rem;">
                            Auto-sync IDP Configuration from metadata is available in Premium and Enterprise versions of the plugin.
                            <a class="text-main" href='<?php 
    echo  esc_url( get_pricing_url() ) ;
    ?>'>
                                Click here to upgrade your plan
                            </a>
                        </p>
                        <div>
			                <?php 
    if ( function_exists( 'wp_nonce_field' ) ) {
        wp_nonce_field( 'idp_config_upload', 'nonce' );
    }
    ?>
                            <input type="hidden" name="action" value="idp_config_upload"/>
                            <div class="row flex-md-wrap align-items-start">
                                <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">Identity Provider
                                    Name</p>
                                <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
                                    <input type="text" name="saml_identity_metadata_provider"
                                           placeholder="Identity Provider name like AzureAD, Okta, SalesForce"
                                           class="w-100 wcs-input-field"
                                           value="<?php 
    esc_attr_e( $identity_name );
    ?>" required=""
                                           pattern="(\S+?)"
                                           maxlength="35"
                                           title="Only English letters, numbers and underscore is allowed (no spacing in-between words is allowed)">
                                </div>
                            </div>

                            <div>
                                <div class="row flex-md-wrap align-items-start mt-xl-4 mt-md-2">
                                    <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">Upload metadata</p>
                                    <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
                                        <input type="file" class="wcs-input-field p-0" id="metadata-file" required=""
                                               name="metadata_file" accept=".xml">
                                    </div>
                                </div>
                                <button type="button" class="wcs-btn wcs-save-btn mt-4 <?php 
    esc_attr_e( ( wpcsso_fs()->can_use_premium_code__premium_only() ? '' : 'wcs-btn-disabled' ) );
    ?>" id="upload-file-button"
                                        onclick="csso_checkMetadataFile()">
                                    Upload
                                </button>
                            </div>

			                <?php 
    csso_form_separator();
    ?>

                            <div>
                                <div class="row flex-md-wrap align-items-start mt-xl-4 mt-md-2">
                                    <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">Enter metadata
                                        URL</p>
                                    <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
                                        <input type="url" name="metadata_url" id="metadata-url"
                                               placeholder="Enter metadata URL of your IdP"
                                               class="w-100 wcs-input-field" value="" required=""
                                               pattern="(\S+?)"
                                               title="Value must be URL"
                                               maxlength="200">
                                    </div>
                                </div>
                                <button type="button" class="wcs-btn wcs-save-btn mt-4 <?php 
    esc_attr_e( ( wpcsso_fs()->can_use_premium_code__premium_only() ? '' : 'wcs-btn-disabled' ) );
    ?>" id="metadata_url_button"
                                        onclick="csso_checkMetadataUrl()">
                                    Fetch Metadata
                                </button>
                            </div>

                            <input type="submit" id="metadata-submit-button" style="display:none"/>
                        </div>
                    </form>
                </div>
                <?php 
    
    if ( csso_is_provider_configured( $current_provider, $configured_providers ) ) {
        ?>
                    <div class="tab-pane fade" id="idp-saml-settings-tab" role="tabpanel" aria-labelledby="idp-saml-settings-tab-link">
                        <form method="post" action="">
	                        <?php 
        if ( function_exists( 'wp_nonce_field' ) ) {
            wp_nonce_field( 'change_provider_custom_sp_entity_id', 'nonce' );
        }
        ?>
                            <input type="hidden" name="action" value="change_provider_custom_sp_entity_id">
                            <div class="row flex-md-wrap align-items-start">
                                <p class="text-main col-xl-2 col-l-2 col-md-12 col-sm-12 p-0 mt-3">Custom SP Entity ID</p>
                                <div class="col-xl-8 col-l-8 mt-0 mt-md-2 p-0">
                                    <input type="url" name="custom_sp_entity_id" id="custom_sp_entity_id"
                                           placeholder="Custom SP entity ID"
                                           class="w-100 wcs-input-field"
                                           pattern="(\S+?)"
                                           title="Value must be URL"
                                           maxlength="200"
                                           value="<?php 
        esc_attr_e( $custom_sp_entity_id );
        ?>">
                                    <p class="mt-1 text-main">
                                        <b>Note,</b> If this field is empty <b>SP Entity ID</b> will be used from Service Provider page
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
				                <?php 
        csso_submit_button();
        ?>
                            </div>
                        </form>
                    </div>
                    <?php 
    }
    
    ?>
            </div>
        </div>
	<?php 
}

?>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('span#add-cert-button').on('click', function () {
            const parent = $('div.cert-tab-links');
            const countTabs = parent.find('[role="tab"]').size();
            const linkId = countTabs + 1;

            $('.certificate-tabs').append(`
                <div
                    class="tab-pane fade"
                    id="x509-cert-tab${linkId}"
                    role="tabpanel"
                    aria-labelledby="x509_cert${linkId}">

                    <textarea rows="4" cols="5" name="x509[]" oninput="csso_verifyCert(event);"
                        placeholder="Copy and Paste the content from the downloaded certificate or copy the content enclosed in X509Certificate tag (has parent tag KeyDescriptor use=signing) in IdP-Metadata XML file"
                        class="w-100 wcs-input-field mt-2"
                        required=""></textarea>
                </div>
            `);

            $(`
                <button
                    class="nav-link"
                    id="x509_cert${linkId}"
                    data-toggle="tab"
                    data-target="#x509-cert-tab${linkId}"
                    type="button"
                    role="tab"
                    aria-controls="x509-cert-tab${linkId}"
                    aria-selected="false">
                    ${linkId}
                </button>
            `).insertBefore(this).tab('show');


            if (countTabs === 1) {
                $(`
                    <span type="button" class="wcs-link-btn wcs-danger-link-btn d-flex align-items-center px-2" id="delete-cert-button">
                        Delete certificate
                    </span>
                `).insertAfter(this);
            }
        })

        $(document.body).on('click', 'span#delete-cert-button', function() {
            const parent = $('.cert-tab-links');
            const activeLink = parent.find('button.nav-link.active');
            const prevLink = activeLink.prev();
            const nextLink = activeLink.next()
            const firstLink = parent.children(':first')
            const deletedTabId = activeLink.attr('aria-controls');
            const activeTab = $(`#${deletedTabId}`);

            activeLink.remove();
            activeTab.remove();

            const tabToShow = activeLink.is(firstLink) ? nextLink : prevLink;
            tabToShow.tab('show')

            const countTabs = parent.find('[role="tab"]').size();

            const tabLinks = parent.find('[role="tab"]').toArray().map((link) => {
                return { link : $(link), tab : $(`#${$(link).attr('aria-controls')}`)};
            }, {})

            tabLinks.forEach(({link, tab}, i) => {
                i += 1;
                link
                    .text(i)
                    .attr('id', `x509_cert${i}`)
                    .attr('data-target', `#x509-cert-tab${i}`)
                    .attr('aria-controls', `x509-cert-tab${i}`)
                tab
                    .attr('id', `x509-cert-tab${i}`)
                    .attr('aria-labelledby', `x509_cert${i}`)
            })

            if (countTabs === 1) {
                $('#delete-cert-button').remove()
            }
        })

        $('.tab-button-section').on('shown.bs.tab', function (event) {
            $(event.relatedTarget).find('div.tab-divider').addClass('hidden')
            $(event.target).find('div.tab-divider').removeClass('hidden')
        })

        $(document).on('click', '#manually-save-button', function() {
            if ($(this).hasClass('wcs-btn-disabled')) {
                return;
            }

            const emptyFields = $('.certificate-tabs').find('textarea').filter(function () {
                return $(this).val() === ''
            })

            if (emptyFields.size()) {
                const firstInvalidField = emptyFields.get(0).closest('div.tab-pane')
                const fieldId = $(firstInvalidField).attr('aria-labelledby')
                $(`#${fieldId}`).tab('show');
            }

            setTimeout(function () {
                $('#manually-submit-button').click()
            }, 150)
        })
    })

</script>




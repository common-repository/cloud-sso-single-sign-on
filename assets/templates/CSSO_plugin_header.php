<?php

function csso_is_tab_active( $tab_name ) : bool
{
    return isset( $_REQUEST['tab'] ) && sanitize_text_field( $_REQUEST['tab'] ) == $tab_name;
}

?>
<div class="plugin-header mb-3 mb-lg-4 pt-2">
    <div class="d-flex justify-content-between align-items-center">
        <img class="wcs-header-logo" width="132px" height="132px"
             src="<?php 
echo  esc_url( csso_get_plugin_url() . 'assets/resources/images/logo.png' ) ;
?>">
        <div class="d-flex flex-column">
	            <?php 
?>
            <div class="d-flex flex-wrap justify-content-end">
				<?php 
csso_link_button(
    'admin.php?page=' . csso_get_plugin_page_name() . '&tab=setup_identity_provider',
    'Identity Provider Setup',
    ButtonTypes::PRIMARY,
    true,
    'mt-2',
    csso_is_tab_active( 'setup_identity_provider' )
);
csso_link_button(
    'admin.php?page=' . csso_get_plugin_page_name() . '&tab=service_provider_metadata',
    'Service Provider Metadata',
    ButtonTypes::PRIMARY,
    true,
    'ml-2 mt-2',
    csso_is_tab_active( 'service_provider_metadata' )
);
csso_link_button(
    'admin.php?page=' . csso_get_plugin_page_name() . '&tab=attribute_mapping',
    'Attribute/Role Mapping',
    ButtonTypes::PRIMARY,
    true,
    'ml-2 mt-2',
    csso_is_tab_active( 'attribute_mapping' )
);
csso_link_button(
    'admin.php?page=' . csso_get_plugin_page_name() . '&tab=sso_links',
    'SSO Links',
    ButtonTypes::PRIMARY,
    true,
    'ml-2 mt-2',
    csso_is_tab_active( 'sso_links' )
);
csso_link_button(
    'admin.php?page=' . csso_get_plugin_page_name() . '&tab=multiple_environments',
    'Multiple Environments',
    ButtonTypes::PRIMARY,
    true,
    'ml-2 mt-2',
    csso_is_tab_active( 'multiple_environments' )
);
?>
            </div>
        </div>
    </div>
</div>

<?php 
?>

<div>
    <div class="page-section mt-4 <?php 
esc_attr_e( handle_enterprise_version( 'p-0' ) );
?>">
        <p class="prem-feature-description text-main" style="right: 1.9rem; top: -1.9rem;">
            Multiple Environments Support/Migration is available in Enterprise version of the plugin.
            <a class="text-main" href='<?php 
echo  esc_url( get_pricing_url() ) ;
?>'>
                Click here to upgrade your plan
            </a>
        </p>
		<?php 
csso_page_header( 'Setup Multiple Environments' );
?>
        <div class="section-wrapper">
            <div class="<?php 
esc_attr_e( ( wpcsso_fs()->can_use_premium_code__premium_only() ? '' : 'deactivate-button' ) );
?>">
				<?php 
csso_option_switcher( csso_get_plugin_prefix() . 'enable_multiple_environments', "Multiple Environments", true );
?>
            </div>
			<?php 
?>
        </div>
    </div>

	<?php 
?>
</div>
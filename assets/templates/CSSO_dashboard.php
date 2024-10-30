<div class="page-wrapper">
	<?php
	settings_errors();
	require csso_get_plugin_path() . 'assets/templates/CSSO_plugin_header.php';

	if ( isset( $_GET['tab'] ) ) {
		$page = csso_get_plugin_path() . 'assets/templates/' . strtoupper( csso_get_plugin_prefix() ) . sanitize_text_field( $_GET['tab'] ) . '.php';
		if ( file_exists( $page ) ) {
			require $page;
		} else {
			?>
            <p class="text-main">Page not found</p>
			<?php
		}
	} else {
		require csso_get_plugin_path() . 'assets/templates/CSSO_setup_identity_provider.php';
	}

	require csso_get_plugin_path() . 'assets/templates/CSSO_plugin_footer.php'
	?>
</div>

<script>
    jQuery(document).ready(function ($) {

        $(document).delegate('.wcs-btn-disabled', 'click', function (e) {
            $(this)
            e.preventDefault()
        })
    })

    jQuery(document).ready(function ($) {

        let premSections = $('.prem-info')
        premSections.hover(function() {
                $(this).find('.prem-feature-description').css('display', 'block')
            },
            function() {
                $(this).find('.prem-feature-description').css('display', 'none')
            })

        premSections.prepend('<img class="prem-padlock" src="<?php echo esc_url(csso_get_plugin_url().'/assets/resources/images/padlock.png') ?>"/>')
        premSections.find('input, form, button').prop('disabled', true)
    })
</script>
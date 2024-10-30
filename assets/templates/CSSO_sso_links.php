<?php
global $providers_manager;
$button_styles        = json_decode( get_option( csso_get_plugin_prefix() . 'login_button_styles' ), true )['button'];
$button_parent_styles = json_decode( get_option( csso_get_plugin_prefix() . 'login_button_styles' ), true )['parent'];


function csso_is_manually_button_width_selected( $button_styles ): bool {
	return is_numeric( csso_trim_px( $button_styles['width'] ) );
}

?>
<div class="">
    <div class="page-section mb-4">
		<?php csso_page_header( 'Configure SSO links' ) ?>
        <div class="section-wrapper ">
            <div>
                <p class="text-header-main mb-4">
                    Configure saml login button
                </p>
                <div class="mb-4">
					<?php
					csso_option_switcher(
						csso_get_plugin_prefix() . 'buttons_on_login_page',
						"Add a Single Sign on buttons on the Wordpress login page"
					);
					?>
                </div>
                <div class="mb-4">
					<?php
					csso_option_switcher(
						csso_get_plugin_prefix() . 'buttons_as_short_code',
						"Use buttons as ShortCode"
					);
					?>
                </div>
                <div class="md-4">
					<?php
					csso_option_switcher(
						csso_get_plugin_prefix() . 'buttons_as_widget',
						"Use buttons as Widget"
					);
					?>
                </div>
            </div>
        </div>
    </div>
    <div class="page-section mb-4">
		<?php csso_page_header( 'Customize SSO Button' ) ?>
        <div class="section-wrapper">
            <form class="" method="post" action="">
                <div class="d-flex">
                    <div class="col-xl-4 col-lg-4 p-0">
                        <p class="text-header-main mb-4">Customize Button</p>
                        <div class="d-flex flex-column ">
                            <div class="d-flex align-items-center mb-4">
                                <p class="text-main m-0 p-0 col-xl-6 col-lg-7 ">Button position relative to parent </p>
                                <select name="parent$justify-content" class="col-xl-4 col-lg-4   wcs-input-field button-position-selector">
                                    <option <?php selected( $button_parent_styles['justify-content'], 'flex-start' ); ?>
                                            value="flex-start">Left
                                    </option>
                                    <option <?php selected( $button_parent_styles['justify-content'], 'center' ); ?>value="center">
                                        Center
                                    </option>
                                    <option <?php selected( $button_parent_styles['justify-content'], 'flex-end' ); ?>value="flex-end">
                                        Right
                                    </option>
                                </select>
                            </div>
                            <div class="d-flex align-items-center">
                                <p class="text-main m-0 p-0 col-xl-6 col-lg-7">Button width </p>
                                <select name="width" class="col-xl-4 col-lg-4 wcs-input-field button-width-selector">
                                    <option <?php selected( $button_styles['width'], 'fit-content' ); ?>value="fit-content">
                                        Fit-content
                                    </option>
                                    <option <?php selected( $button_styles['width'], '100%' ); ?> value="100%">
                                        Full-width
                                    </option>
                                    <option <?php esc_html_e( is_numeric( csso_trim_px( $button_styles['width'] ) ) ? 'selected' : '' ); ?>value="manually">
                                        Manually
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 p-0">
                        <p class="text-header-main mb-4">Theme</p>
                        <div class="d-flex align-items-center mb-4">
                            <p class="text-main m-0 p-0 col-xl-3 col-lg-4">Button Color</p>
                            <div class="d-flex align-items-center flex-grow-1 color-selection-wrapper">
                                <input class="col-xl-4 col-lg-5 wcs-input-field button-style-value color-selection-field"
                                       type="text"
                                       name="background-color"
                                       pattern="(\S+?)"
                                       title="Only alphabets, numbers is allowed"
                                       maxlength="30"
                                       value="<?php esc_attr_e( $button_styles['background-color'] ); ?>"
                                       required>
                                <div class="d-flex ml-2 p-0 col-xl-5 color-picker-size">
                                    <input class="color-picker w-100 h-100"
                                           type="color"
                                           value="<?php esc_attr_e( $button_styles['background-color'] ); ?>">
                                </div>

                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <p class="text-main m-0 p-0 col-xl-3 col-lg-4">Font Color</p>
                            <div class="d-flex align-items-center flex-grow-1 color-selection-wrapper">
                                <input class="col-xl-4 col-lg-5 button-style-value wcs-input-field color-selection-field"
                                       type="text"
                                       name="color"
                                       pattern="(\S+?)"
                                       title="Only alphabets, numbers is allowed"
                                       maxlength="30"
                                       value="<?php esc_attr_e( $button_styles['color'] ); ?>"
                                       required>
                                <div class="d-flex ml-2 p-0 color-picker-size">
                                    <input class="color-picker w-100 h-100"
                                           type="color"
                                           value="<?php esc_attr_e( $button_styles['color'] ); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <p class="text-main m-0 p-0 col-xl-3 col-lg-4">Font Size</p>
                            <div class="d-flex align-items-center">
                                <input class="col-xl-4 col-lg-3 wcs-input-field button-style-value disable-webkit"
                                       type="text"
                                       name="font-size"
                                       pattern="^(50)|([1-4]\d)|([1-9])"
                                       title="Font Size must be between 1 and 50"
                                       value="<?php esc_attr_e( csso_trim_px( $button_styles['font-size'] ) ); ?>"
                                       required>
                                <button class="ml-2 wcs-btn counter-button wcs-btn-square"
                                        type="button"
                                        value="-">
                                    -
                                </button>
                                <button class="ml-2 wcs-btn counter-button wcs-btn-square"
                                        type="button"
                                        value="+">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 p-0">
                        <p class="text-header-main mb-4">Size of Icons</p>
                        <div class="d-flex mb-4 button-width-field"
                             style="<?php esc_attr_e( ! csso_is_manually_button_width_selected( $button_styles ) ? 'display: none !important' : '' ); ?>">
                            <div class="d-flex align-items-center flex-grow-1">
                                <p class="text-main m-0 p-0 col-xl-2 col-lg-3">Width </p>
                                <div class="d-flex align-items-center">
                                    <input class="col-xl-4 col-lg-3 wcs-input-field button-style-value disable-webkit"
                                           type="text"
                                           name="width"
                                           pattern="^(400)|([1-3]\d{2})|([1-9]\d)|([1-9])"
                                           title="Width must be between 1 and 400"
                                           value="<?php esc_attr_e( csso_trim_px( $button_styles['width'] ) ); ?>"
                                            <?php esc_attr_e( ! csso_is_manually_button_width_selected( $button_styles ) ? 'disabled' : '' ); ?>
                                           required
                                    >
                                    <button class="ml-2 wcs-btn counter-button wcs-btn-square"
                                            type="button"
                                            value="-">
                                        -
                                    </button>
                                    <button class="ml-2 wcs-btn counter-button wcs-btn-square"
                                            type="button"
                                            value="+">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="d-flex align-items-center flex-grow-1">
                                <p class="text-main m-0 p-0 col-xl-2 col-lg-3">Height</p>
                                <div class="d-flex align-items-center">
                                    <input class="col-xl-4 col-lg-3 wcs-input-field button-style-value disable-webkit"
                                           type="text"
                                           name="height"
                                           pattern="^(100)|([1-9]\d)|([1-9])"
                                           title="Height must be between 1 and 100"
                                           value="<?php esc_attr_e( csso_trim_px( $button_styles['height'] ) ); ?>"
                                           required
                                    >
                                    <button class="ml-2 wcs-btn counter-button wcs-btn-square"
                                            type="button"
                                            value="-">
                                        -
                                    </button>
                                    <button class="ml-2 wcs-btn counter-button wcs-btn-square"
                                            type="button"
                                            value="+">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="d-flex align-items-center flex-grow-1">
                                <p class="text-main m-0 p-0 col-xl-2 col-lg-3">Curve</p>
                                <div class="d-flex align-items-center">
                                    <input class="col-xl-4 col-lg-3 wcs-input-field button-style-value disable-webkit"
                                           type="text"
                                           name="border-radius"
                                           pattern="^(50)|([1-4]\d)|([1-9])"
                                           title="Curve must be between 1 and 50"
                                           value="<?php esc_attr_e( csso_trim_px( $button_styles['border-radius'] ) ); ?>"
                                           required
                                    >
                                    <button class="ml-2 wcs-btn counter-button wcs-btn-square"
                                            type="button"
                                            value="-">
                                        -
                                    </button>
                                    <button class="ml-2 wcs-btn counter-button wcs-btn-square"
                                            type="button"
                                            value="+">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="action" value="update_login_button_styles">
				<?php wp_nonce_field( 'update_login_button_styles', 'nonce' ); ?>
                <div class="mt-4 mb-3">
                    <div class="section-divider"></div>
                    <p class="text-header-main mb-4 mt-4">Button preview</p>
                    <div class="col-4 p-0">
						<?php
						echo csso_provider_login_link( csso_get_empty_provider(), '', true );
						?>
                    </div>
                </div>
                <div>
                    <div class="section-divider overflow-section"></div>
                    <div class="d-flex mt-4">
						<?php
						$width_button = 'auto';
						csso_submit_button( 'Save Changes',null, $width_button );
						?>
                        <div class="ml-2">
							<?php
							$reset_url = wp_nonce_url( site_url() . '?action=reset_saml_button_styles', 'reset_saml_button_styles' );
							csso_link_button( $reset_url, 'Reset styles', ButtonTypes::LIGHT );
							?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="page-section">
		<?php csso_page_header( 'Shortcodes' ) ?>
        <div class="section-wrapper">
			<?php
			$short_codes = $providers_manager->csso_get_providers_shortcodes();

			if ( count( $short_codes ) ) {
				?>
                <div class="available-shortcodes">
                    <div class="pb-3">
                        <p class="text-main text-main-bolder mb-3">Available HTML shortcodes for current environment: </p> <?php
						foreach ( $short_codes as $short_code ) {
							?>
                            <code class='code-block mb-2'>[<?php esc_html_e( $short_code ); ?>]</code><br>
							<?php
						}
						?>
                    </div>
                    <div class="pt-4">
                        <p class="text-main text-main-bolder mb-2">Usage:</p>
                        <div class="d-flex align-items-center mb-2">
                            <p class="text-main m-0">For PHP page:</p>
                            <code class="code-block ml-3">echo do_shortcode(['<?php esc_html_e( $short_codes[0] ); ?>'])</code>
                        </div>
                        <div class="d-flex align-items-center">
                            <p class="text-main m-0">For HTML page:</p>
                            <code class="code-block ml-3">[<?php esc_html_e( $short_codes[0] ); ?>]</code>
                        </div>
                    </div>
                </div>
				<?php
			} else {
				?>
                <p class="text-main">No available providers</p>
				<?php
			}
			?>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {

        let samlLoginButton = $('.saml-login-button')
        let buttonWidthSection = $('.button-width-field')
        let buttonWidthSelector = $('.button-width-selector')
        let buttonLimits = {
            'font-size': {'min': 1, 'max': 50},
            'width': {'min': 1, 'max': 400},
            'height': {'min': 1, 'max': 100},
            'border-radius': {'min': 1, 'max': 50},
        }

        $('.color-picker').on('change', function () {
            $(this).closest('div.color-selection-wrapper').find('input.button-style-value').val($(this).val()).change();
        })

        $('.counter-button').on('click', function () {
            let input = $(this).closest('div').find('input');
            let attributeName = input.attr('name');
            let value = calcValue(input.val(), $(this).attr('value'));
            input.val(valueInDiapason(buttonLimits[attributeName], value)).change();
        })

        $('.button-position-selector').on('change', function () {
            samlLoginButton.closest('div').css('justify-content', $(this).val());
        })

        buttonWidthSelector.on('change', function () {
            if ($(this).val() !== 'manually') {
                buttonWidthSection
                    .attr('style', 'display: none !important')
                    .find('input.button-style-value').prop('disabled', true)
                samlLoginButton.css($(this).attr('name'), $(this).val())
                return
            }
            samlLoginButton.css($(this).attr('name'), 'initial')
            buttonWidthSection
                .show()
                .find('input.button-style-value')
                .prop('disabled', false)
                .val(Math.round(samlLoginButton.width()))
        })

        $('.button-style-value').on('change', function () {
            let value = $(this).val();
            if ($(this).hasClass('color-selection-field')) {
                $(this).closest('div').find('input.color-picker').val($(this).val())
            }

            if ($(this).attr('name') && buttonLimits[$(this).attr('name')]) {
                value = valueInDiapason(buttonLimits[$(this).attr('name')], value);
            }

            samlLoginButton.css($(this).attr('name'), isNaN(value) ? value : toPx(value));
        })

        const valueInDiapason = (buttonLimits, value) => {
            let minValue = buttonLimits.min;
            let maxValue = buttonLimits.max;
            if (value < minValue) {
                return minValue;
            } else if (value > maxValue) {
                return maxValue;
            }
            return value;
        }

        const toPx = (str) => str + 'px';
        const calcValue = (currentValue, op) => {
            currentValue = Number(currentValue)
            op === '+' ? currentValue++ : currentValue--
            return currentValue;
        }

    })
</script>

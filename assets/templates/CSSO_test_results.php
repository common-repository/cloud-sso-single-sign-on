<?php

use CloudSingleSignOn\base\CSSO_ResponseValidator;

$response_validator = new CSSO_ResponseValidator();
$response_validator->csso_validate();

$bootstrap_url = csso_get_plugin_url() . 'assets/includes/libs/bootstrap-4.6.2/css/bootstrap.min.css';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="<?php echo esc_url($bootstrap_url)?>">
    <title>Test Result</title>
</head>

<body>

<?php

if ( $response_validator->csso_get_last_error() ) {
	?>
    <div class="bg-danger p-4">
        <p class="text-light m-0">Tests failed</p>
    </div>

    <div>
        <p class="font-weight-bold my-3 px-4">Error: <?php esc_html_e( $response_validator->csso_get_last_error() ); ?></p>
    </div>
	<?php
}

if ( ! $response_validator->csso_get_last_error() ) {
	?>
    <div class="bg-success p-4">
        <p class="text-light m-0">Tests passed</p>
    </div>

    <div class="mt-4">
        <p class="font-weight-bold px-4">Received assertion: </p>
        <table class="table">
            <tbody>
            <tr class="d-flex">
                <td class="col-4 pl-4">Assertion type</td>
                <td class="col-8"><?php esc_html_e( $response_validator->csso_get_assertion_type() ); ?></td>
            </tr>
            <tr class="d-flex">
                <td class="col-4 pl-4">Name ID Format</td>
                <td class="col-8"><?php esc_html_e( $response_validator->csso_get_name_id_format() ); ?></td>
            </tr>
            <tr class="d-flex">
                <td class="col-4 pl-4">Name ID Value</td>
                <td class="col-8"><?php esc_html_e( $response_validator->csso_get_name_id_value() ); ?></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        <p class="font-weight-bold px-4">Received attributes: </p>
        <table class="table">
            <tbody>
			<?php

			foreach ( $response_validator->csso_get_attributes() as $attribute_key => $attribute_value ) {
				?>
                <tr class="d-flex">
                    <td class="col-4 pl-4"><?php esc_html_e( $attribute_key ); ?></td>
                    <td class="col-8 pl-4"><?php
                        if ($attribute_value) {
	                        esc_html_e( is_array($attribute_value) ? implode(',', $attribute_value) : $attribute_value );
                        }
                        ?></td>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
    </div>
	<?php
}
?>

</body>
</html>
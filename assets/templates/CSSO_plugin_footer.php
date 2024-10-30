<?php

use CloudSingleSignOn\base\CSSO_ErrorHandler;

CSSO_ErrorHandler::csso_reset_errors();
?>

<footer class="pt-5 ">
	<?php csso_link_button(
		'https://infrasos.com/office-365-reporting/?utm_source=wp-cloud-sso-plugin&utm_medium=plugin-ui&utm_campaign=wp-cloud-sso',
		'Enable Office 365 Reporting & Management with InfraSOS',
		ButtonTypes::SUCCESS,
		true,
		'',
		false,
		true
	);
	?>
</footer>

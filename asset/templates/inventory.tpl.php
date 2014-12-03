<div id="content_holder">
	<div id="main">
		<div id="content">
			<div class="paper">
				<div>
					<div id="heading-left">
						<div id="logo"></div>
						<div id="title">Assets<span id="page-heading">: inventory</span></div>
						<?= $_CONTROL->db; ?>
						<div class="cf"></div>
					</div>
					<div id="heading-right"></div>
					<div class="cf"></div>
				</div>
				<div id="data-container">
					<div id="data">

						<?php

							$strXhtml = '<thead><th colspan="4">APPLE COMPUTERS</th></thead>';

							foreach ($_CONTROL->apple_computers as $arrAppleComputers) {
								$strXhtml .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $arrAppleComputers['serial_number'], $arrAppleComputers['type'], $arrAppleComputers['model'], $arrAppleComputers['description']);
							}

							$strXhtml .= '<thead><th colspan="4">OTHER COMPUTERS</th></thead>';

							foreach ($_CONTROL->other_computers as $arrOtherComputers) {
								$strXhtml .= sprintf('<tr><td>%s</td><td>%s</td><td></td><td>%s</td></tr>', $arrOtherComputers['serial_number'], $arrOtherComputers['type'], $arrOtherComputers['description']);
							}

							printf('<table id="newspaper-b">%s</table>', $strXhtml);

						?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>


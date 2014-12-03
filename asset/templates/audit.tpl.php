<div id="content_holder">
	<div id="main">
		<div id="content">
			<div class="paper">
				<div>
					<div id="heading-left">
						<div id="logo"></div>
						<div id="title">Asset Tracking<span id="page-heading">: audit</span></div>
						<?= $_CONTROL->db; ?>
						<div class="cf"></div>
					</div>
					<div id="heading-right">
						<div id="login-details"><?php require_once "menu.inc.php"; ?></div>
					</div>
					<div class="cf"></div>
				</div>
				<div>
					<div id="controls-right">
						<span id="btnRunAudit" class="button">Run Audit</span>
					</div>
				</div>
				<div class="cf"></div>
				<div id="results-container">
					<div id="data"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="error-dialog">
	<div class="dialog-container">
		<div id="error"></div>
	</div>
	<div class="cf"></div>
	<div class="dialog-buttons">
		<span class="cancel-button" id="btnErrorClose">Close</span>
	</div>
</div>

<div id="scratchpad-container">
	<div id="sratchpad-panel">
		<span id="scratchpad-title">scratchpad</span>
		<textarea id="scratchpad"></textarea>
		<div class="cf"></div>
		<span id="btnClearScratchpad" class="button"/>Clear</span>
	</div>
</div>
<div id="content_holder">
	<div id="main">
		<div id="content">
			<div class="paper">
				<div>
					<div id="heading-left">
						<div id="logo"></div>
						<div id="title">Asset Tracking<span id="page-heading">: reporting</span></div>
						<?= $_CONTROL->db; ?>
						<div class="cf"></div>
					</div>
					<div id="heading-right">
						<div id="login-details"><?php require_once "menu.inc.php"; ?></div>
					</div>
					<div class="cf"></div>
				</div>
				<div id="report-container">
					<div id="tabs">
						<ul>
							<li><a href="#tab-1">Assets by Asset ID</a></li>
							<li><a href="#tab-2">Assets by Employee</a></li>
							<li><a href="#tab-3">Assets by Department</a></li>
							<li><a href="#tab-4">Assets by Group</a></li>
							<li><a href="#tab-5">Asset History</a></li>
							<li><a href="#tab-6">Asset Movement</a></li>
							<li><a href="#tab-7">Employees</a></li>
						</ul>

						<div id="tab-1">
							<div class="tab-wrapper">
								<div class="btnExport button">Export to Excel</div>
								<div class="btnPrint button">Print Preview</div>
								<div class="cf"></div>
								<div id="report-1"><?= $_CONTROL->assetsbyassetid; ?></div>
							</div>
						</div>

						<div id="tab-2">
							<div class="tab-wrapper">
								<div class="btnExport button">Export to Excel</div>
								<div class="btnPrint button">Print Preview</div>
								<div class="cf"></div>
								<div id="report-2"><?= $_CONTROL->assetsbyemployee; ?></div>
							</div>
						</div>

						<div id="tab-3">
							<div class="tab-wrapper">
								<div class="btnExport button">Export to Excel</div>
								<div class="btnPrint button">Print Preview</div>
								<div class="cf"></div>
								<div id="report-3"><?= $_CONTROL->assetsbydepartment; ?></div>
							</div>
						</div>

						<div id="tab-4">
							<div class="tab-wrapper">
								<div class="btnExport button">Export to Excel</div>
								<div class="btnPrint button">Print Preview</div>
								<div class="cf"></div>
								<div id="report-4"><?= $_CONTROL->assetsbygroup; ?></div>
							</div>
						</div>

						<div id="tab-5">
							<div class="tab-wrapper">
								<div id="filter-wrapper-asset-history"><label for="txtFilter" id="lblFilter"><strong>Filter: Asset ID/Name</strong></label><input type="text" id="txtFilter"/></div>
								<div class="btnExport button">Export to Excel</div>
								<div class="btnPrint button">Print Preview</div>
								<div class="cf"></div>
								<div id="report-5"><?= $_CONTROL->assethistory; ?></div>
							</div>
						</div>

						<div id="tab-6">
							<div class="tab-wrapper">
								<div id="filter-wrapper-asset-movement">
									<label for="txtStartDate" id="lblDateRange"><strong>Filter: Date Range</strong></label><input type="text" id="txtStartDate"/> to<input type="text" id="txtEndDate"/>
									<div id="btnLastMonth" class="button">Last Month</div>
									<div id="btnThisMonth" class="button">This Month</div>
									<div id="btnClear" class="button">Clear</div>
								</div>
								<div class="btnExport button">Export to Excel</div>
								<div class="btnPrint button">Print Preview</div>
								<div class="cf"></div>
								<div id="report-6"><?= $_CONTROL->assetmovement; ?></div>
							</div>
						</div>

						<div id="tab-7">
							<div class="tab-wrapper">
								<div class="btnExport button">Export to Excel</div>
								<div class="btnPrint button">Print Preview</div>
								<div class="cf"></div>
								<div id="report-7"><?= $_CONTROL->employees; ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="print-dialog">
	<div id="output"></div>
	<div class="cf"></div>
	<div class="dialog-buttons">
		<span class="cancel-button" id="btnPrintCancel">Cancel</span>
		<span class="print-button" id="btnPrint">Print</span>
	</div>
</div>

<div id="report"></div>

<div id="debug">
	<p>
		<strong>
			Login Cookie: <?= $_CONTROL->logincookie; ?><br/>
			Last Active: <?= $_CONTROL->lastactive; ?><br/>
			IP Address: <?= $_CONTROL->ipaddress; ?><br/>
		</strong>
		<?php printf('<pre>%s</pre>', print_r($_SESSION['arrUser'], true)); ?>
		<?php printf('<pre>%s</pre>', print_r($_SESSION, true)); ?>
	</p>
</div>

<div id="scratchpad-container">
	<div id="sratchpad-panel">
		<span id="scratchpad-title">scratchpad</span>
		<div class="cf"></div>
		<span id="scratchpad-has-content">&#10003;</span>
		<textarea id="scratchpad"></textarea>
		<div class="cf"></div>
		<span id="btnClearScratchpad" class="menu control"/>Clear</span>
	</div>
</div>
<!--
<div id="inventory-container">
	<div id="inventory-panel">
		<span id="inventory-title">inventory</span>
		<div id="inventory"></div>
	</div>
</div>
//-->
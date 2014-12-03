<div id="content_holder">
	<div id="main">
		<div id="content">
			<div class="paper">
				<div>
					<div id="heading-left">
						<div id="logo"></div>
						<div id="title">Asset Tracking<span id="page-heading">: admin</span></div>
						<?= $_CONTROL->db; ?>
						<div class="cf"></div>
					</div>
					<div id="heading-right">
						<div id="login-details"><?php require_once "menu.inc.php"; ?></div>
					</div>
					<div class="cf"></div>
				</div>
				<div>
					<div id="controls-left">
						<div id="data-entry">				
							<div class="fl">
								<h3>Asset ID&hellip;</h3>
								<input type="text" id="txtAssetID"/>
							</div>
							<div class="fl">
								<h3>Employee&hellip;</h3>
								<input type="text" id="txtEmployee"/>
							</div>
							<div class="fl">
								<h3>Department&hellip;</h3>
								<select id="selDepartment"></select>
							</div>
							<div class="fl">
								<h3>Group&hellip;</h3>
								<select id="selGroup"></select>
							</div>
						</div>
					</div>
					<div id="controls-right">
						<span id="btnAddAsset" class="button">Add Asset</span>
						<span id="btnAddEmployee" class="button">Add Employee</span>
					</div>
				</div>
				<div class="cf"></div>
				<div id="data-container">
					<div id="data"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="edit-asset-dialog">
	<div class="dialog-container">
		<div id="edit-asset-details">
			<fieldset id="edit-asset-id">
				<legend><strong>Asset ID</strong></legend>
				<input type="text" id="txtEditAssetID"/>
				<span id="id-duplicate-check"></span>
			</fieldset>
			<fieldset id="edit-asset-serial-number">
				<legend><strong>Serial Number</strong></legend>
				<input type="text" id="txtEditAssetSerialNumber"/>
				<span id="serial-number-duplicate-check"></span>
				<div class="cf"></div>
				<input type="checkbox" id="chkNASerialNumber"/><label for="chkNASerialNumber">Missing/Unknown</label>
			</fieldset>
			<div id="edit-asset-detail">
				<fieldset>
					<legend><strong>Asset Details</strong></legend>
					<label for="selEditAssetType">Asset Type</label><select id="selEditAssetType"></select>
					<div class="cf"></div>
					<label for="txtEditAssetDescription">Description</label><input type="text" id="txtEditAssetDescription"/>
					<div class="cf"></div>
					<!--
					<label for="txtEditAssetModel">Model<span id="get-model-info"></span></label><textarea id="txtEditAssetModel"></textarea>
					<div class="cf"></div>
					//-->
					<label for="selEditAssetGroup">Group</label><select id="selEditAssetGroup"></select>
					<div class="cf"></div>
					<label for="selEditAssetStatus">Status</label><select id="selEditAssetStatus"></select>
					<div class="cf"></div>
					<label for="txtEditAssetHostname">Hostname</label><input type="text" id="txtEditAssetHostname"/>
					<div class="cf"></div>
					<label for="txtEditAssetMacEth">MAC Address (Wired)</label><input type="text" id="txtEditAssetMacEth"/>
					<div class="cf"></div>
					<label for="txtEditAssetMacWlan">MAC Address (Wireless)</label><input type="text" id="txtEditAssetMacWlan"/>
				</fieldset>
			</div>
		</div>
		<div id="edit-asset-transfer">
			<fieldset>
				<legend><strong>Assignment</strong></legend>
				<label for="selEditAssetEmployee">Employee</label><select id="selEditAssetEmployee"></select>
				<div class="cf"></div>
				<label for="selEditAssetDepartment">Department</label><select id="selEditAssetDepartment"></select>
			</fieldset>
		</div>
		<div id="edit-asset-history">
			<fieldset>
				<legend><strong>History</strong></legend>
				<label for="chkEditAssetIgnore">Ignore this change</label><input type="checkbox" id="chkEditAssetIgnore"/>
				<div class="cf"></div>
				<label for="txtEditAssetDate">Date</label><input type="text" id="txtEditAssetDate"/>
				<label for="txtEditAssetNotes">Notes</label>
				<div class="cf"></div>
				<textarea id="txtEditAssetNotes"></textarea>
			</fieldset>
		</div>
		<div id="edit-asset-misc">
			<fieldset>
				<legend><strong>Miscellaneous</strong></legend>
				<label for="txtEditAssetPurchaseOrderId">Purchase Order #</label><input type="text" id="txtEditAssetPurchaseOrderId"/>
			</fieldset>
		</div>
	</div>
	<div class="cf"></div>
	<div class="dialog-buttons">
		<span class="delete-button" id="btnEditAssetDelete">Delete</span>
		<span class="cancel-button" id="btnEditAssetCancel">Cancel</span>
		<span class="save-button" id="btnEditAssetSave">Save</span>
	</div>
	<div id="edit-asset-overlay"></div>
	<div id="edit-asset-model-list"></div>
</div>

<div id="edit-employee-dialog">
	<div class="dialog-container">
		<div id="edit-employee-detail">
			<fieldset id="edit-employee">
				<legend><strong>Employee Details</strong></legend>
				<label for="txtEditEmployeeFirstName" id="lblEditEmployeeFirstName">First Name</label><input type="text" id="txtEditEmployeeFirstName"/>
				<div class="cf"></div>
				<label for="txtEditEmployeeLastName" id="lblEditEmployeeLastName">Last Name</label><input type="text" id="txtEditEmployeeLastName"/>
				<div class="cf"></div>
				<label for="selEditEmployeeManager">Manager</label><select id="selEditEmployeeManager"></select>
				<div class="cf"></div>
				<label for="selEditEmployeeDepartment">Department</label><select id="selEditEmployeeDepartment"></select>
				<div class="cf"></div>
				<label for="chkEditEmployeeLocal">Local</label><input type="checkbox" id="chkEditEmployeeLocal"/>
				<div class="cf"></div>
				<label for="txtEditEmployeeRemoteLocation" id="lblEditEmployeeRemoteLocation">Remote Location</label><input type="text" id="txtEditEmployeeRemoteLocation"/>
				<div class="cf"></div>
				<label for="chkEditEmployeeExit">Exit</label><input type="checkbox" id="chkEditEmployeeExit"/>
			</fieldset>
		</div>
	</div>
	<div class="cf"></div>
	<div class="dialog-buttons">
		<span class="cancel-button" id="btnEditEmployeeCancel">Cancel</span>
		<span class="save-button" id="btnEditEmployeeSave">Save</span>
	</div>
</div>

<div id="history-dialog">
	<div class="dialog-container">
		<div id="history"></div>
	</div>
	<div class="cf"></div>
	<div class="dialog-buttons">
		<span class="cancel-button" id="btnAssetHistoryClose">Close</span>
	</div>
</div>

<div id="warning-dialog">
	<div class="dialog-container">
		<div class="warning">
			<div class="warning-logo"></div>
		</div>
		<div class="warning-info">
			<p>
				You are about to delete an asset (<strong>S/N <span id="asset-serial-number"></span></strong>). <span class="warning-message">This action is permanent and cannot be undone!</span>
			</p>
			<p>
				This asset has <span id="history-events"></span> associated with it.
			</p>
		</div>
	</div>
	<div class="cf"></div>
	<div class="dialog-buttons">
		<span class="cancel-button" id="btnDeleteCancel">Cancel</span>
		<span class="delete-confirm-button" id="btnDeleteConfirm">Delete</span>
	</div>
</div>

<div id="info-container">
	<div id="info-panel"></div>
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
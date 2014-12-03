var admin = {
  
	// global vars
	/*
	something: 10,
	somethingElse: location.hash,
	*/
	employee_id: 0,
	manager_id: 0,
	department_id: 0,
	//remote_location: null,
	asset_id: 0,
	versal_id: null,
	serial_number: null,
	type_id: 0,
	group_id: 0,
	status_id: 0,
	remote_location_hint: 'ST, USA or Country',
	inventory_open: false,
	scratchpad_open: false,
	acl: 0,

	assignment_dirty: false,

	init: function(options) {
		// set defaults
		/*
		var defaultSettings = {
			someOption: false,
			fadeLogo: false
		},

		settings = $.extend({}, this.defaultSettings, options),
		*/

		base = this;

		// set up tabs control
		/*
		$('#tabs').tabs({
			show: function(event, ui) {
				var current_tab = $(this).tabs('option', 'selected');
				last_tab = current_tab;
			}
		});
		*/

		// set up dialogs
		$('#edit-asset-dialog').dialog({
            width: 800,
            height: 480,
            modal: true,
            autoOpen: false,
            resizable: false,
            beforeClose: function(event, ui) {
                admin.cleanAssetDialog();
            }
        });

		$('#edit-employee-dialog').dialog({
            width: 405,
            height: 400,
            modal: true,
            autoOpen: false,
            resizable: false,
            beforeClose: function(event, ui) {
                admin.cleanEmployeeDialog();
            }
        });

		$('#history-dialog').dialog({
            title: 'Asset History',
            width: 800,
            height: 500,
            modal: true,
            autoOpen: false,
            resizable: false,
            beforeClose: function(event, ui) {
                admin.cleanHistoryDialog();
            }
        });

		$('#warning-dialog').dialog({
            width: 300,
            height: 250,
            modal: true,
            autoOpen: false,
            resizable: false
        });

		// bind control events
		$('#txtAssetID').bind('focus', $.proxy(this, 'resetFields'));
		$('#chkNASerialNumber').bind('change', function() {
			if ($('#chkNASerialNumber').prop('checked')) {
				$('#txtEditAssetSerialNumber').val("[n/a]");
				$('#txtEditAssetSerialNumber').prop('disabled', true);
			} else {
				$('#txtEditAssetSerialNumber').val("");
				$('#txtEditAssetSerialNumber').prop('disabled', false);
			}
		});
		$('#txtEmployee').bind('focus', $.proxy(this, 'resetFields'));
		$('#selDepartment').bind('focus', $.proxy(this, 'resetFields'));
		$('#selGroup').bind('focus', $.proxy(this, 'resetFields'));

		$('#txtAssetID').bind('keyup', $.proxy(this, 'getAssets'));
		$('#txtEmployee').bind('keyup', $.proxy(this, 'getEmployees'));
		$('#selDepartment').bind('change', $.proxy(this, 'getDepartments'));
		$('#selGroup').bind('change', $.proxy(this, 'getGroups'));

		$('#txtEditEmployeeRemoteLocation').bind('focus', function() {
			if ($('#txtEditEmployeeRemoteLocation').val() == admin.remote_location_hint) {
				$('#txtEditEmployeeRemoteLocation').val("");
				$('#txtEditEmployeeRemoteLocation').removeClass('hint');
			}
		});
		$('#get-model-info').bind('click', function() {
			if ($('#txtEditAssetSerialNumber').val() != '') {
				admin.getModelInfo();
			}
		});

		$('#txtEditEmployeeRemoteLocation').bind('blur', function() {
			if ($('#txtEditEmployeeRemoteLocation').val() == '') {
				$('#txtEditEmployeeRemoteLocation').addClass('hint');
				$('#txtEditEmployeeRemoteLocation').val(admin.remote_location_hint);
			}
		});

		$('#inventory-title').bind('click', $.proxy(this, 'toggleInventory'));
		$('#inventory').bind('keyup', $.proxy(this, 'toggleInventory'));
		$('#inventory').bind('blur', $.proxy(this, 'toggleInventory'));

		$('#scratchpad-title').bind('click', $.proxy(this, 'toggleScratchpad'));
		$('#scratchpad').bind('keyup', $.proxy(this, 'saveScratchpad'));
		$('#scratchpad').bind('blur', $.proxy(this, 'saveScratchpad'));

		// buttons
		$('#btnAddAsset').bind('click', $.proxy(this, 'createAsset'));
		$('#btnAddEmployee').bind('click', $.proxy(this, 'createEmployee'));
		$('#btnClearScratchpad').bind('click', $.proxy(this, 'clearScratchpad'));

		// dialog events
		$('#txtEditAssetSerialNumber').bind('keyup', $.proxy(this, 'checkForExistingSerialNumber'));
		$('#selEditAssetType').bind('change', function() {
			switch (parseInt($('#selEditAssetType').val())) {
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				case 17:
				case 19:
				case 21:
				case 22:
					$('#txtEditAssetModel').prop('disabled', false);
					$('#txtEditAssetModel').css({'backgroundColor': '#fff'});
					$('#get-model-info').show();
					break;
				default:
					$('#txtEditAssetModel').prop('disabled', true);
					$('#txtEditAssetModel').css({'backgroundColor': '#eee'});
					$('#get-model-info').hide();
			}
		});
		$('#selEditAssetStatus').bind('change', function() {
			$('#selEditAssetEmployee').prop('disabled', ($('#selEditAssetStatus').val() > 1)?true:false);
			$('#selEditAssetDepartment').prop('disabled', ($('#selEditAssetStatus').val() > 1)?true:false);
		});

		$('#selEditAssetEmployee').bind('change', function() {
			$('#selEditAssetDepartment').val("0");
			admin.assignment_dirty = true;
		});

		$('#selEditAssetDepartment').bind('change', function() {
			$('#selEditAssetEmployee').val("0");
			admin.assignment_dirty = true;
		});
	
		$('#chkEditAssetIgnore').bind('click', function() {
			$('#txtEditAssetDate').prop('disabled', $('#chkEditAssetIgnore').prop('checked'));
			$('#txtEditAssetNotes').prop('disabled', $('#chkEditAssetIgnore').prop('checked'));
		});

		$('#chkEditEmployeeLocal').bind('change', function() {
			$('#txtEditEmployeeRemoteLocation').prop('disabled', $('#chkEditEmployeeLocal').prop('checked'));
			if ($('#chkEditEmployeeLocal').prop('checked')) {
				$('#txtEditEmployeeRemoteLocation').addClass('hint');
				$('#txtEditEmployeeRemoteLocation').val(admin.remote_location_hint);
			}
		});

		/*
		$('#txtEditEmployeeRemoteLocation').bind('keyup', function() {
			$('#chkEditEmployeeLocal').prop('checked', false);
		});
		*/

		// dialog button events
		$('#btnEditAssetDelete').bind('click', $.proxy(this, 'deleteAsset', false));
		$('#btnEditAssetCancel').bind('click', $.proxy(this, 'closeDialog', 'edit-asset-dialog', true));
		$('#btnEditAssetSave').bind('click', $.proxy(this, 'saveAsset'));
		
		$('#btnEditEmployeeCancel').bind('click', $.proxy(this, 'closeDialog', 'edit-employee-dialog', true));
		$('#btnEditEmployeeSave').bind('click', $.proxy(this, 'saveEmployee'));

		$('#btnAssetHistoryClose').bind('click', $.proxy(this, 'closeDialog', 'history-dialog', false));

		$('#btnDeleteConfirm').bind('click', $.proxy(this, 'deleteAsset', true));
		$('#btnDeleteCancel').bind('click', $.proxy(this, 'closeDialog', 'warning-dialog', false));

		// do stuff
		admin.resizePaper();
		admin.loadScratchpad();
		ajax.getAcl();
		ajax.updateInfo();

		// set focus
		$('#txtAssetID').focus();

		ajax.getDepartmentSelectionList();
		ajax.getGroupSelectionList();
	},

	createAsset: function() {
		this.asset_id = -1;
		this.type_id = 1;
    	this.group_id = 0;
    	this.status_id = 10;
    	this.employee_id = 0;
    	this.department_id = 0;
    	ajax.getTypeList();
		ajax.getStatusList();
		ajax.getGroupList();
		ajax.getEmployeeAssignmentList();
		ajax.getDepartmentAssignmentList();

		$('#selEditAssetEmployee').prop('disabled', (admin.status_id > 1)?true:false);
		$('#selEditAssetDepartment').prop('disabled', (admin.status_id > 1)?true:false);

		$('#txtEditAssetDate').val(this.getDateString());

		this.showDialog('edit-asset-dialog', 'Add Asset');
	},

	createEmployee: function() {
		this.employee_id = -1;
		this.manager_id = 0;
    	this.department_id = 0;
		ajax.getEmployeeList();
		ajax.getDepartmentList();
		$('#chkEditEmployeeLocal').prop('checked', true);
		$('#txtEditEmployeeRemoteLocation').prop('disabled', true);

		$('#txtEditEmployeeRemoteLocation').val(admin.remote_location_hint);
		$('#txtEditEmployeeRemoteLocation').addClass('hint');

		this.showDialog('edit-employee-dialog', 'Add Employee');
	},

	refreshView: function() {
		if ($('#txtAssetID').val().length > 0) {
			this.getAssets();
		}
		if ($('#txtEmployee').val().length > 0) {
			this.getEmployees();
		}
		if ($('#selDepartment').val() > 0) {
			this.getDepartments();
		}
		if ($('#selGroup').val() > 0) {
			this.getGroups();
		}
	},

	resetFields: function() {
		$('#txtAssetID').val("");
		$('#txtEmployee').val("");
		$('#selDepartment').val("0");
		$('#selGroup').val("0");
	},

	getAssets: function() {
		//if ($('#txtSerialNumber').val().length > 1) {
        	ajax.getAssets();
        //}
	},

	getEmployees: function() {
    	ajax.getEmployees();
	},

	getDepartments: function() {
		if ($('#selDepartment').val() > 0) {
        	ajax.getDepartments();
        }
	},

	getGroups: function() {
		if ($('#selGroup').val() > 0) {
        	ajax.getGroups();
        }
	},

	checkForExistingAssetID: function() {
		$('#id-duplicate-check').html('Checking&hellip;');
		$('#id-duplicate-check').show();
		ajax.checkForExistingAssetID();
	},

	checkForExistingSerialNumber: function() {
		$('#serial-number-duplicate-check').html('Checking&hellip;');
		$('#serial-number-duplicate-check').show();
		ajax.checkForExistingSerialNumber();
	},

	getModelInfo: function() {
		switch (this.type_id) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 17:
			case 19:
			case 21:
			case 22:
				ajax.getModelInfo();
		}
	},

	deleteAsset: function(confirmed) {
		if (!confirmed) {
			ajax.getAssetData();
		} else {
			ajax.deleteAsset();
		}
	},

	saveAsset: function() {
		$('#edit-asset-id legend').removeClass('error');
		$('#edit-asset-serial-number legend').removeClass('error');

		if ($('#txtEditAssetID').val() == '') {
			$('#edit-asset-id legend').addClass('error');

			return false;
		}

		if ($('#txtEditAssetSerialNumber').val() == '') {
			$('#edit-asset-serial-number legend').addClass('error');

			return false;
		}
		ajax.saveAsset();
	},

	saveEmployee: function() {
		if ($('#txtEditEmployeeFirstName').val() == '' || $('#txtEditEmployeeLastName').val() == '') {
			$('#edit-employee legend').addClass('error');
			$('#lblEditEmployeeFirstName').addClass('error');
			$('#lblEditEmployeeLastName').addClass('error');

     	   return false;
		}
		ajax.saveEmployee();
	},

	cleanAssetDialog: function() {
		$('#txtEditAssetID').val("");
		$('#txtEditAssetSerialNumber').val("");
		$('#txtEditAssetSerialNumber').prop('disabled', false);
		$('#chkNASerialNumber').prop('checked', false);
		$('#selEditAssetType').empty();
        $('#txtEditAssetDescription').val("");
        $('#txtEditAssetModel').val("");
		$('#txtEditAssetModel').css({'backgroundColor': '#fff'});
        $('#txtEditAssetModel').prop('disabled', false);
        $('#get-model-info').show();
        $('#selEditAssetGroup').empty();
        $('#selEditAssetStatus').empty();
        $('#txtEditAssetHostname').val("");
		$('#txtEditAssetMacEth').val("");
        $('#txtEditAssetMacWlan').val("");
        $('#selEditAssetEmployee').empty();
        $('#selEditAssetDepartment').empty();
        $('#chkEditAssetIgnore').prop('checked', false);
        $('#txtEditAssetDate').val("");
        $('#txtEditAssetNotes').val("");
        $('#txtEditAssetPurchaseOrderId').val("");

        $('#duplicate-check').hide();
        $('#duplicate-check').removeClass('error');
        $('#btnEditAssetDelete').css('display', 'none');
        $('#btnEditAssetSave').removeClass('save-button-disabled');

        this.serial_number = null;
        this.assignment_dirty = false;

        $('#edit-asset-serial-number legend').removeClass('error');
        $('#edit-asset-model-list').html("");
        $('#edit-asset-model-list').hide();
        $('#edit-asset-overlay').hide();
        $('#txtEditAssetModel').css({'color': '#000'});
	},

	cleanEmployeeDialog: function() {
		$('#txtEditEmployeeFirstName').val("");
        $('#txtEditEmployeeLastName').val("");
        $('#selEditEmployeeManager').empty();
        $('#selEditEmployeeDepartment').empty();
        $('#chkEditEmployeeLocal').prop('checked', false);
        $('#txtEditEmployeeRemoteLocation').val("");
        $('#txtEditEmployeeRemoteLocation').prop('disabled', true);
        $('#chkEditEmployeeExit').prop('checked', false);

        $('#edit-employee legend').removeClass('error');
        $('#lblEditEmployeeFirstName').removeClass('error');
        $('#lblEditEmployeeLastName').removeClass('error');
	},

	cleanHistoryDialog: function() {
		return;
	},

	toggleInventory: function() {
		if (admin.scratchpad_open) {
			admin.toggleScratchpad();
		}

		if (!admin.inventory_open) {
			admin.inventory_open = true;
			$('#inventory-container').animate({
				opacity: 1,
				right: '+=662'
			}, 500, 'easeOutQuint', function() {
				$('#inventory-title').addClass('open');
				$('#inventory').focus();
			});
		} else {
			admin.inventory_open = false;
			admin.scrollToTop($('#inventory'));
			$('#inventory-container').animate({
				opacity: .95,
				right: '-=662'
			}, 500, 'easeOutCubic', function() {
				$('#inventory-title').removeClass('open');
				/*
				$('#scratchpad-container').animate({
					opacity: .95,
					zIndex: 2
				}, 0);
				*/
			});
		}
	},

	loadInventory: function() {
		ajax.loadInventory();
	},

	toggleScratchpad: function() {
		if (admin.inventory_open) {
			admin.toggleInventory();
		}

		if (!admin.scratchpad_open) {
			admin.scratchpad_open = true;
			$('#scratchpad-container').animate({
				opacity: 1,
				right: '+=267'
			}, 500, 'easeOutQuint', function() {
				$('#scratchpad-title').addClass('open');
				$('#scratchpad').focus();
			});
		} else {
			admin.scratchpad_open = false;
			$('#scratchpad-container').animate({
				opacity: .95,
				right: '-=267'
			}, 500, 'easeOutCubic', function() {
				$('#scratchpad-title').removeClass('open');
			});
		}
	},

	loadScratchpad: function() {
		$('#scratchpad').val(localStorage.getItem('scratchpad'));

		if ($('#scratchpad').val().length > 0) {
			$('#scratchpad-has-content').show();
		}
	},

	saveScratchpad: function() {
		if ($('#scratchpad').val() != '') {
			$('#scratchpad-has-content').show();
		} else {
			$('#scratchpad-has-content').hide();
		}
		localStorage.setItem('scratchpad', $('#scratchpad').val());
	},

	clearScratchpad: function() {
		$('#scratchpad').val("");
		admin.saveScratchpad();
	},

	bindDynamicControls: function() {
		$('.inventory-serial-number').bind('click', function() {
			admin.resetFields();
			$('#txtAssetID').val($(this).html());
			admin.getAssets();
			admin.asset_id = $(this).attr('id').replace('inventory_', '');
			ajax.getAssetDetails();
			admin.toggleInventory();
		});
	},

	showDialog: function(dlgName, title) {
		$('#' + dlgName).dialog('option', 'title', title);
		$('#' + dlgName).dialog('open');
	},

	closeDialog: function(dlgName, clearTitle) {
		if (clearTitle) {
			$('#' + dlgName).dialog('option', 'title', '');
		}
		$('#' + dlgName).dialog('close');
	},

	resizePaper: function() {
		$('.paper').css('min-height', $(window).height() - 170);
		$('#data-container').css('height', $(window).height() - 190);
	},

	getDateString: function() {
		var today = new Date();
		var d = today.getDate();
		var m = today.getMonth() + 1;
		var y = today.getFullYear();
		d = (d < 10)?'0' + d:d;
		m = (m < 10)?'0' + m:m;

		return m + '/' + d + '/' + y;
	},

	pad: function(value) {
		value = value + '';
		width = 6;
		return value.length >= width ? value : new Array(width - value.length + 1).join('0') + value;
	},

	scrollToTop: function(elem, speed) {
		if (speed == null) {
			speed = 'slow';
		};
		elem.animate({"scrollTop": 0}, speed);
	}
};

$(document).ready(function() {
	admin.init({
		//options
		/*
        someOption: true
        */
    });
});

$(window).resize(function() {
	admin.resizePaper();
});
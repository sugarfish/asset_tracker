/**
 * Ajax Functions:
 * @param string mode
 * @param array items
 */

var ajax = {

	getDepartmentSelectionList: function() {
		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getDepartmentList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    if (output.length == 0) {
    	    	$('#selDepartment').prop('disabled', true);
    	    }
    	    var xhtml = '<option>Select&hellip;</option>';
    	    for (i in output) {
    	    	xhtml += '<option value="' + output[i]['id'] + '">' + output[i]['department'] + '</option>';
    	    }
    	    $('#selDepartment').append(xhtml);
	    });
	},

	getGroupSelectionList: function() {
		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getGroupList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    if (output.length == 0) {
    	    	$('#selGroup').prop('disabled', true);
    	    }
    	    var xhtml = '<option>Select&hellip;</option>';
    	    for (i in output) {
    	    	xhtml += '<option value="' + output[i]['id'] + '">' + output[i]['group'] + '</option>';
    	    }
    	    $('#selGroup').append(xhtml);
	    });
	},

	getAssets: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getAssets', "search": $('#txtAssetID').val()}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '';
    	    var history = '';
    	    var has_history = '';
    	    for (i in output) {
    	    	model = (output[i]['model'] != '')?'<span class="model-panel">' + output[i]['model'] + '</span>':'';
    	    	history = (admin.acl == 1)?' class="history" id="asset_' + output[i]['asset_id'] + '"':'';
    	    	has_history = (output[i]['has_history'] == 1)?'<span id="history_' + output[i]['asset_id'] + '" class="button has-history">history</span>':'';
    	 		if (output[i]['asset_department'] == '') {
					if (output[i]['status_id'] == 1) {
						xhtml += '<tr><td>' + output[i]['name'] + '</td><td>' + output[i]['manager'] + '</td><td>' + output[i]['department'] + '</td><td>' + output[i]['type'] + '</td><td>' + output[i]['description'] + '</td><td>' + model + '</td><td' + history + '>' + admin.pad(output[i]['versal_id']) + '</td><td><span title="Status|Assigned" class="tips status-1"></span></td><td>' + has_history + '</td></tr>';
	    			} else {
	    				xhtml += '<tr><td></td><td></td><td></td><td>' + output[i]['type'] + '</td><td>' + output[i]['description'] + '</td><td>' + model + '</td><td' + history + '>' + admin.pad(output[i]['versal_id']) + '</td><td><span title="Status|' + output[i]['status'] + '" class="tips status-' + output[i]['status_id'] + '"></span></td><td>' + has_history + '</td></tr>';
	    			}
	    		} else {
	    			xhtml += '<tr><td></td><td></td><td>' + output[i]['asset_department'] + '</td><td>' + output[i]['type'] + '</td><td>' + output[i]['description'] + '</td><td>' + model + '</td><td' + history + '>' + admin.pad(output[i]['versal_id']) + '</td><td><span title="Status|' + output[i]['status'] + '" class="tips status-' + output[i]['status_id'] + '"></span></td><td>' + has_history + '</td></tr>';
	    		}
	    	}
	    	$('#data').html('<table id="newspaper-b"><tbody>' + xhtml + '</tbody></table>');
			$('table td.history').on('click', function() {
				admin.asset_id = $(this).attr('id').replace('asset_', '');
				ajax.getAssetDetails();
			});
			$('table span.has-history').on('click', function() {
				admin.asset_id = $(this).attr('id').replace('history_', '');
				ajax.getAssetHistory();
			});

				$('span.tips').cluetip({
					splitTitle: '|',
					width: 150,
					arrows: true,
					tracking: true,
					cluetipClass: 'jtip'
				});
	    });
	},

	getAssetDetails: function() {
		//console.log(admin.asset_id);
		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getAssetDetails', "id": admin.asset_id}, function(data) {
    	    var output = $.parseJSON(data);
    	    $('#txtEditAssetID').val(output['versal_id']);
    	    $('#txtEditAssetSerialNumber').val(output['serial_number']);
    	    if (output['na_missing'] == 1) {
				$('#chkNASerialNumber').prop('checked', true);
				$('#txtEditAssetSerialNumber').prop('disabled', true);
			}
    	    admin.versal_id = output['versal_id'];
    	    admin.serial_number = output['serial_number'];
    	    $('#txtEditAssetDescription').val(output['description']);
    	    $('#txtEditAssetModel').val(output['model']);
    	    $('#txtEditAssetHostname').val(output['hostname']);
    	    $('#txtEditAssetMacEth').val(output['mac_eth']);
    	    $('#txtEditAssetMacWlan').val(output['mac_wlan']);
    	    $('#txtEditAssetPurchaseOrderId').val(output['purchase_order_id']);
    	    admin.type_id = output['type_id'];
    	    admin.group_id = output['group_id'];
    	    admin.status_id = output['status_id'];
    	    admin.employee_id = output['employee_id'];
    	    admin.department_id = output['department_id'];
    	    ajax.getTypeList();
			ajax.getStatusList();
			ajax.getGroupList();
			ajax.getEmployeeAssignmentList();
			ajax.getDepartmentAssignmentList();

			$('#selEditAssetEmployee').prop('disabled', (admin.status_id > 1)?true:false);
			$('#selEditAssetDepartment').prop('disabled', (admin.status_id > 1)?true:false);

			$('#txtEditAssetDate').val(admin.getDateString());

			$('#btnEditAssetDelete').css('display', 'inline-block');

			switch (admin.type_id) {
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
					break;
				default:
					$('#txtEditAssetModel').prop('disabled', true);
					$('#txtEditAssetModel').css({'backgroundColor': '#eee'});
					$('#get-model-info').hide();
			}

			admin.showDialog('edit-asset-dialog', 'Edit Asset');
	    });
	},

	getTypeList: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getTypeList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '';
    	    var selected = '';
    	    for (i in output) {
				selected = (admin.type_id === output[i]['id'])?' selected="selected"':'';
    	    	xhtml += '<option value="' + output[i]['id'] + '"' + selected + '>' + output[i]['type'] + '</option>';
    	    }
    	    $('#selEditAssetType').append(xhtml);
	    });
	},

	getStatusList: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getStatusList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '';
    	    var selected = '';
    	    for (i in output) {
				selected = (admin.status_id === output[i]['id'])?' selected="selected"':'';
    	    	xhtml += '<option value="' + output[i]['id'] + '"' + selected + '>' + output[i]['status'] + '</option>';
    	    }
    	    $('#selEditAssetStatus').append(xhtml);
	    });
	},

	getGroupList: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getGroupList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '<option value="0">N/A</option>';
    	    var selected = '';
    	    for (i in output) {
				selected = (admin.group_id === output[i]['id'])?' selected="selected"':'';
    	    	xhtml += '<option value="' + output[i]['id'] + '"' + selected + '>' + output[i]['group'] + '</option>';
    	    }
    	    $('#selEditAssetGroup').append(xhtml);
	    });
	},

	getEmployees: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getEmployees', "search": $('#txtEmployee').val()}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '';
    	    var prevName = '';
    	    var history = '';
    	    var details = '';
    	    var model = '';
    	    var serial_number = '';
    	    var has_history = '';
    	    var exit = '';
    	    for (i in output) {
    	    	if (output[i]['serial_number'] != null) {
    	    		serial_number = output[i]['serial_number'];
    	    		history = (admin.acl == 1)?' class="history" id="asset_' + output[i]['asset_id'] + '"':'';
    	    	} else {
    	    		serial_number = '';
    	    		history = '';
    	    	}
    	    	details = (admin.acl == 1)?' class="details" id="employee_' + output[i]['employee_id'] + '"':'';
    	    	model = (output[i]['model'] != '')?'<span class="model-panel">' + output[i]['model'] + '</span>':'';
    	    	has_history = (output[i]['has_history'] == 1)?'<span id="history_' + output[i]['asset_id'] + '" class="button has-history">history</span>':'';
    	    	exit = (output[i]['exit'] == 1)?' class="exit"':'';
    	    	if (output[i]['name'] === prevName) {
    	    		xhtml += '<tr' + exit + '><td></td><td></td><td></td><td>' + output[i]['type'] + '</td><td>' + output[i]['description'] + '</td><td>' + model + '</td><td' + history + '>' + serial_number + '</td><td>' + has_history + '</td></tr>';
    	    	} else {
    	    		xhtml += '<tr' + exit + '><td' + details + '>' + output[i]['name'] + '</td><td>' + (output[i]['exit'] == 0?output[i]['manager']:'') + '</td><td>' + (output[i]['exit'] == 0?output[i]['department']:'') + '</td><td>' + output[i]['type'] + '</td><td>' + output[i]['description'] + '</td><td>' + model + '</td><td' + history + '>' + serial_number + '</td><td>' + has_history + '</td></tr>';
    	    	}
				prevName = output[i]['name'];
	    	}
	    	$('#data').html('<table id="newspaper-b"><tbody>' + xhtml + '</tbody></table>');
			$('table td.details').on('click', function() {
				admin.employee_id = $(this).attr('id').replace('employee_', '');
				ajax.getEmployeeDetails();
			});
			$('table td.history').on('click', function() {
				admin.asset_id = $(this).attr('id').replace('asset_', '');
				ajax.getAssetDetails();
			});
			$('table span.has-history').on('click', function() {
				admin.asset_id = $(this).attr('id').replace('history_', '');
				ajax.getAssetHistory();
			});
	    });
	},

	getEmployeeDetails: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getEmployeeDetails', "id": admin.employee_id}, function(data) {
    	    var output = $.parseJSON(data);
    	    $('#txtEditEmployeeFirstName').val(output['firstname']);
    	    $('#txtEditEmployeeLastName').val(output['lastname']);
    	    admin.manager_id = output['manager_id'];
    	    admin.department_id = output['department_id'];
    	    ajax.getEmployeeList();
			ajax.getDepartmentList();
			if (output['local'] == 1) {
				$('#chkEditEmployeeLocal').prop('checked', true);				
				$('#txtEditEmployeeRemoteLocation').prop('disabled', true);
				$('#txtEditEmployeeRemoteLocation').addClass('hint');
				$('#txtEditEmployeeRemoteLocation').val(admin.remote_location_hint);
			} else {
				$('#txtEditEmployeeRemoteLocation').removeClass('hint');
				$('#txtEditEmployeeRemoteLocation').val(output['remote_location']);
				$('#txtEditEmployeeRemoteLocation').prop('disabled', false);
			}
			if (output['exit'] == 1) {
				$('#chkEditEmployeeExit').prop('checked', true);
			}

			admin.showDialog('edit-employee-dialog', 'Edit Employee');
	    });
	},

	getEmployeeList: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getEmployeeList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '<option value="0">N/A</option>';
    	    var selected = '';
    	    for (i in output) {
				selected = (admin.manager_id === output[i]['id'])?' selected="selected"':'';
    	    	xhtml += '<option value="' + output[i]['id'] + '"' + selected + '>' + output[i]['name'] + '</option>';
    	    }
    	    $('#selEditEmployeeManager').append(xhtml);
	    });
	},

	getDepartmentList: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getDepartmentList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '<option value="0">N/A</option>';
    	    for (i in output) {
    	    	selected = (admin.department_id === output[i]['id'])?' selected="selected"':'';
    	    	xhtml += '<option value="' + output[i]['id'] + '"' + selected + '>' + output[i]['department'] + '</option>';
    	    }
    	    $('#selEditEmployeeDepartment').append(xhtml);
	    });
	},

	getEmployeeAssignmentList: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getEmployeeList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '<option value="0">N/A</option>';
    	    var selected = '';
    	    for (i in output) {
				selected = (admin.employee_id === output[i]['id'])?' selected="selected"':'';
    	    	xhtml += '<option value="' + output[i]['id'] + '"' + selected + '>' + output[i]['name'] + '</option>';
    	    }
    	    $('#selEditAssetEmployee').append(xhtml);
	    });
	},

	getDepartmentAssignmentList: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getDepartmentList'}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '<option value="0">N/A</option>';
    	    var selected = '';
    	    for (i in output) {
    	    	selected = (admin.department_id === output[i]['id'])?' selected="selected"':'';
    	    	xhtml += '<option value="' + output[i]['id'] + '"' + selected + '>' + output[i]['department'] + '</option>';
    	    }
    	    $('#selEditAssetDepartment').append(xhtml);
	    });
	},

	getDepartments: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getDepartments', "id": $('#selDepartment').val()}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '';
    	    var history = '';
    	    var has_history = '';
    	    for (i in output) {
    	    	history = (admin.acl == 1)?' class="history" id="asset_' + output[i]['asset_id'] + '"':'';
    	    	has_history = (output[i]['has_history'] == 1)?'<span id="history_' + output[i]['asset_id'] + '" class="button has-history">history</span>':'';
				xhtml += '<tr><td>' + output[i]['type'] + '</td><td>' + output[i]['description'] + '</td><td' + history + '>' + output[i]['serial_number'] + '</td><td>' + output[i]['group'] + '</td><td>' + has_history + '</td></tr>';
	    	}
	    	$('#data').html('<table id="newspaper-b"><tbody>' + xhtml + '</tbody></table>');
			$('table td.history').on('click', function() {
				admin.asset_id = $(this).attr('id').replace('asset_', '');
				ajax.getAssetDetails();
			});
			$('table span.has-history').on('click', function() {
				admin.asset_id = $(this).attr('id').replace('history_', '');
				ajax.getAssetHistory();
			});
	    });
	},

	getGroups: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getGroups', "id": $('#selGroup').val()}, function(data) {
    	    var output = $.parseJSON(data);
    	    var xhtml = '';
    	    var history = '';
    	    var has_history = '';
    	    for (i in output) {
    	    	history = (admin.acl == 1)?' class="history" id="asset_' + output[i]['asset_id'] + '"':'';
    	    	has_history = (output[i]['has_history'] == 1)?'<span id="history_' + output[i]['asset_id'] + '" class="button has-history">history</span>':'';
				xhtml += '<tr><td>' + output[i]['type'] + '</td><td>' + output[i]['description'] + '</td><td' + history + '>' + output[i]['serial_number'] + '</td><td>' + output[i]['group'] + '</td><td>' + has_history + '</td></tr>';
	    	}
	    	$('#data').html('<table id="newspaper-b"><tbody>' + xhtml + '</tbody></table>');
			$('table td.history').on('click', function() {
				admin.asset_id = $(this).attr('id').replace('asset_', '');
				ajax.getAssetDetails();
			});
			$('table span.has-history').on('click', function() {
				admin.asset_id = $(this).attr('id').replace('history_', '');
				ajax.getAssetHistory();
			});
	    });
	},

	getAssetHistory: function() {
		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getAssetHistory', "id": admin.asset_id}, function(data) {
    	    var output = $.parseJSON(data);

			var xhtml = '<table id="newspaper-c"><thead><th>ASSET ID</th><th>TYPE</th><th>DESCRIPTION</th><th>STATUS</th><th>GROUP</th></thead>';
			xhtml += '<tbody><tr><td>' + admin.pad(output['details']['versal_id']) + '</td><td>' + output['details']['type'] + '</td><td>' + output['details']['description'] + '</td><td>' + output['details']['status'] + '</td><td>' + output['details']['group'] + '</td></tr></tbody>';
			xhtml += '</table>';

			xhtml += '<table id="newspaper-c">';
			xhtml += '<thead>';
			xhtml += '<tr class="noborder"><th>REF.</th><th>DATE</th><th colspan="3">STATUS</th><th colspan="3">EMPLOYEE</th><th colspan="3">DEPARTMENT</th></tr>';
			xhtml += '<tr><th colspan="2"></th><th>FROM</th><th>&#8658;</th><th>TO</th><th>FROM</th><th>&#8658;</th><th>TO</th><th>FROM</th><th>&#8658;</th><th>TO</th></tr>';
			xhtml += '</thead>';

    	    for (i in output['history']) {
				xhtml += '<tbody><tr><td>' + output['history'][i]['reference_id'] + '</td><td><nobr>' + output['history'][i]['date'] + '</nobr></td><td>' + output['history'][i]['from_status'] + '</td><td></td><td>' + output['history'][i]['to_status'] + '</td><td>' + output['history'][i]['from_employee'] + '</td><td></td><td>' + output['history'][i]['to_employee'] + '</td><td>' + output['history'][i]['from_department'] + '</td><td></td><td>' + output['history'][i]['to_department'] + '</td></tr></tbody>';
    		}
    		xhtml += '</table>';
    		$('#history').html(xhtml);

			admin.showDialog('history-dialog');
	    });
	},

	checkForExistingAssetID: function() {
		if ($('#txtEditAssetID').val() == '' || $('#txtEditAssetID').val() == admin.versal_id) {
			$('#id-duplicate-check').html('');

			return;
		}

		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'checkForExistingAssetID', "versal_id": $('#txtEditAssetID').val()}, function(data) {
    	    var output = $.parseJSON(data);

    	    if (output == 'duplicate') {
    	    	$('#id-duplicate-check').addClass('error');
    	    	$('#id-duplicate-check').html('Duplicate ID');
    	    	$('#btnEditAssetSave').addClass('save-button-disabled');
    	    } else {
    	    	$('#id-duplicate-check').removeClass('error');
    	    	$('#id-duplicate-check').html('Ok.');
    	    	$('#btnEditAssetSave').removeClass('save-button-disabled');
    	    }
   		});
	},

	checkForExistingSerialNumber: function() {
		if ($('#txtEditAssetSerialNumber').val() == '' || $('#txtEditAssetSerialNumber').val().toUpperCase() == admin.serial_number) {
			$('#serial-number-duplicate-check').html('');

			return;
		}

		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'checkForExistingSerialNumber', "serial_number": $('#txtEditAssetSerialNumber').val()}, function(data) {
    	    var output = $.parseJSON(data);

    	    if (output == 'duplicate') {
    	    	$('#serial-number-duplicate-check').addClass('error');
    	    	$('#serial-number-duplicate-check').html('Duplicate Serial Number');
    	    	$('#btnEditAssetSave').addClass('save-button-disabled');
    	    } else {
    	    	$('#serial-number-duplicate-check').removeClass('error');
    	    	$('#serial-number-duplicate-check').html('Ok.');
    	    	$('#btnEditAssetSave').removeClass('save-button-disabled');
    	    }
   		});
	},

	/* save/delete functions... */

	deleteAsset: function() {
		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'deleteAsset', "id": admin.asset_id}, function() {
			admin.closeDialog('edit-asset-dialog');
			admin.closeDialog('warning-dialog');
			admin.refreshView();
			ajax.updateInfo();
		});
	},

	getAssetData: function() {
		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getAssetData', "id": admin.asset_id}, function(data) {
    	    var output = $.parseJSON(data);

    	    $('#asset-serial-number').html(output.serial_number);

    	    switch (output.history_events) {
    	    	case 0:
    	    		$('#history-events').html('0 history events');
    	    		break;
    	    	case 1:
    	    		$('#history-events').html('<span class="error">1 history event</span>');
    	    		break;
    	    	default:
    	    		$('#history-events').html('<span class="error">' + output.history_events + ' history events</span>');
    	    }

			admin.showDialog('warning-dialog', 'Delete Asset?');
		});
	},

	loadInventory: function() {
		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getInventoryData'}, function(data) {
			//console.log(data);
    	    var output = $.parseJSON(data);

    	    var xhtml = '<table class="box-table-a">';

    	    if (output.apple_computers.length > 0) {
	    	    xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">APPLE COMPUTERS (' + output.apple_computers.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
	    	    for (i in output.apple_computers) {
	    	    	if (admin.acl == 1) {
						xhtml += '<tr><td class="inventory-serial-number" id="inventory_' + output.apple_computers[i].asset_id + '">' + output.apple_computers[i].serial_number + '</td><td>' + output.apple_computers[i].type + '</td><td>' + output.apple_computers[i].description + '</td></tr>';
					} else {
						xhtml += '<tr><td>' + output.apple_computers[i].serial_number + '</td><td>' + output.apple_computers[i].type + '</td><td>' + output.apple_computers[i].description + '</td></tr>';
					}
				}
				xhtml += '<tr><td colspan="4" class="spacer"></td></tr>';
			}

			if (output.other_computers.length > 0) {
				xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">OTHER COMPUTERS (' + output.other_computers.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
			    for (i in output.other_computers) {
			    	if (admin.acl == 1) {
						xhtml += '<tr><td class="inventory-serial-number" id="inventory_' + output.other_computers[i].asset_id + '">' + output.other_computers[i].serial_number + '</td><td>' + output.other_computers[i].type + '</td><td>' + output.other_computers[i].description + '</td></tr>';
					} else {
						xhtml += '<tr><td>' + output.other_computers[i].serial_number + '</td><td>' + output.other_computers[i].type + '</td><td>' + output.other_computers[i].description + '</td></tr>';
					}
				}
				xhtml += '<tr><td colspan="4" class="spacer"></td></tr>';
			}

			if (output.phones_tablets.length > 0) {
				xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">PHONES/TABLETS (' + output.phones_tablets.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
	    	    for (i in output.phones_tablets) {
	    	    	if (admin.acl == 1) {
						xhtml += '<tr><td class="inventory-serial-number" id="inventory_' + output.phones_tablets[i].asset_id + '">' + output.phones_tablets[i].serial_number + '</td><td>' + output.phones_tablets[i].type + '</td><td>' + output.phones_tablets[i].description + '</td></tr>';
					} else {
						xhtml += '<tr><td>' + output.phones_tablets[i].serial_number + '</td><td>' + output.phones_tablets[i].type + '</td><td>' + output.phones_tablets[i].description + '</td></tr>';
					}
				}
				xhtml += '<tr><td colspan="4" class="spacer"></td></tr>';
			}

			if (output.monitors_displays.length > 0) {
				xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">MONITORS/DISPLAYS (' + output.monitors_displays.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
	    	    for (i in output.monitors_displays) {
	    	    	if (admin.acl == 1) {
						xhtml += '<tr><td class="inventory-serial-number" id="inventory_' + output.monitors_displays[i].asset_id + '">' + output.monitors_displays[i].serial_number + '</td><td>' + output.monitors_displays[i].type + '</td><td>' + output.monitors_displays[i].description + '</td></tr>';
					} else {
						xhtml += '<tr><td>' + output.monitors_displays[i].serial_number + '</td><td>' + output.monitors_displays[i].type + '</td><td>' + output.monitors_displays[i].description + '</td></tr>';
					}
				}
				xhtml += '<tr><td colspan="4" class="spacer"></td></tr>';
			}

			if (output.miscellaneous.length > 0) {
				xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">MISCELLANEOUS (' + output.miscellaneous.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
	    	    for (i in output.miscellaneous) {
	    	    	if (admin.acl == 1) {
						xhtml += '<tr><td class="inventory-serial-number" id="inventory_' + output.miscellaneous[i].asset_id + '">' + output.miscellaneous[i].serial_number + '</td><td>' + output.miscellaneous[i].type + '</td><td>' + output.miscellaneous[i].description + '</td></tr>';
					} else {
						xhtml += '<tr><td>' + output.miscellaneous[i].serial_number + '</td><td>' + output.miscellaneous[i].type + '</td><td>' + output.miscellaneous[i].description + '</td></tr>';
					}
				}
			}

			xhtml += '</table>';

			$('#inventory').html(xhtml);
			admin.bindDynamicControls();
		});
	},

	saveAsset: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php",
	    	{
	    		"mode": 'saveAsset',
	    		"asset_id": admin.asset_id,
	    		"versal_id": $('#txtEditAssetID').val(),
	    		"serial_number": $('#txtEditAssetSerialNumber').val(),
	    		"na_missing": $('#chkNASerialNumber').is(':checked'),
	    		"type_id": $('#selEditAssetType').val(),
	    		"description": $('#txtEditAssetDescription').val(),
	    		"model": $('#txtEditAssetModel').val(),
	    		"group_id": $('#selEditAssetGroup').val(),
	    		"status_id": $('#selEditAssetStatus').val(),
	    		"hostname": $('#txtEditAssetHostname').val(),
	    		"mac_eth": $('#txtEditAssetMacEth').val(),
	    		"mac_wlan": $('#txtEditAssetMacWlan').val(),
	    		"purchase_order_id": $('#txtEditAssetPurchaseOrderId').val(),
	    		"assigned_employee_id": $('#selEditAssetEmployee').val(),
	    		"assigned_department_id": $('#selEditAssetDepartment').val(),
	    		"ignore_change": $('#chkEditAssetIgnore').is(':checked'),
	    		"date": $('#txtEditAssetDate').val(),
	    		"notes": $('#txtEditAssetNotes').val(),
	    		"assignment_dirty": admin.assignment_dirty
	    	}, function(data) {
    	    var response = $.parseJSON(data);

    	    if (response.result == 'failed') {
    	    	$('#edit-asset-serial-number legend').addClass('error');
    	    } else {
				admin.refreshView();
				admin.closeDialog('edit-asset-dialog', true);
			}
			ajax.updateInfo();
	    });
	},

	saveEmployee: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php",
	    	{
	    		"mode": 'saveEmployee',
	    		"employee_id": admin.employee_id,
	    		"firstname": $('#txtEditEmployeeFirstName').val(),
	    		"lastname": $('#txtEditEmployeeLastName').val(),
	    		"manager_id": $('#selEditEmployeeManager').val(),
	    		"department_id": $('#selEditEmployeeDepartment').val(),
	    		"local": $('#chkEditEmployeeLocal').is(':checked'),
	    		"remote_location": $('#txtEditEmployeeRemoteLocation').val(),
	    		"exit": $('#chkEditEmployeeExit').is(':checked')
	    	}, function(data) {
    	    var response = $.parseJSON(data);

			if (response.result == 'failed') {
    	    	$('#edit-employee legend').addClass('error');
				$('#lblEditEmployeeFirstName').addClass('error');
				$('#lblEditEmployeeLastName').addClass('error');
    	    } else {
				admin.refreshView();
				admin.closeDialog('edit-employee-dialog', true);
	    	}
	    	ajax.updateInfo();
	    });
	},

	updateInfo: function() {
		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getNumberOfAssetsAndUsers'}, function(data) {
    	    var output = $.parseJSON(data);

    	   	var xhtml = '<div id="info">';
			xhtml += '<span id="assets"><strong>Assets:</strong> ' + output['assets'] + ' <span id="assets_assigned_inventory">[Assigned: ' + output['assets_assigned'] + ', Inventory: ' + output['assets_inventory'] + ']</span></span>';
			xhtml += '<span id="users"><strong>Users:</strong> ' + output['users'] + '</assets>';
			xhtml += '</div>';
			$('#info-panel').html(xhtml);

			admin.loadInventory();
		});
	},

	getModelInfo: function() {
		$('#txtEditAssetModel').css({'color': '#ccc'});
		var serial_number = $('#txtEditAssetSerialNumber').val();
		var last_three = serial_number.substr(serial_number.length - 3);

		$.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getModelInfo', "last_three": last_three}, function(data) {
    	    var output = $.parseJSON(data);

    	    if (output.model_info.length < 2) {
    	    	$('#txtEditAssetModel').val(output.model_info[0]);
		   	    $('#txtEditAssetModel').css({'color': '#000'});
    	    } else {

    	    	var xhtml = '<div class="model-details not-listed">None of these</div>';
	    	    for (i in output.model_info) {
		    	    //console.log(output.model_info[i]);
		    	    xhtml += '<div class="model-details">' + output.model_info[i] + '</div>';
		    	}

		    	$('#edit-asset-model-list').html(xhtml);
		    	$('#edit-asset-overlay').show();
		    	$('#edit-asset-model-list').show();
		    	
		    	$('.model-details').bind('click', function() {
		    		if (!$(this).hasClass('not-listed')) {
			    		$('#txtEditAssetModel').val($(this).html());
			    	} else {
			    		$('#txtEditAssetModel').val("");
			    	}

			    	admin.scrollToTop($('#edit-asset-model-list'), 'fast');
			    	$('#edit-asset-model-list').queue(function() {
				    	$('#edit-asset-overlay').hide();
				    	$('#edit-asset-model-list').hide();
				    	$('#edit-asset-model-list').html("");
				    	$('#txtEditAssetModel').css({'color': '#000'});
				    	$(this).dequeue();
			    	});
		    	});
		    }
		});
	},

	getAcl: function() {
	    $.post("/_user/classes/ajax/AjaxAdmin.class.php", {"mode": 'getAcl'}, function(data) {
    	    var output = $.parseJSON(data);
    	    admin.acl = output['acl'];
    	    if (admin.acl == 1) {
				$('#btnAddAsset').css('display', 'inline-block');
				$('#btnAddEmployee').css('display', 'inline-block');
			}
	    });
	}

}
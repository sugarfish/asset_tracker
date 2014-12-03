/**
 * Ajax Functions:
 * @param string mode
 * @param array items
 */

var ajax = {

	filterAssetHistory: function() {
		$.post("/_user/classes/ajax/AjaxReporting.class.php", {"mode": 'getFilteredAssetHistory', "filter": $('#txtFilter').val()}, function(data) {
    	    $('#report-5').html(data);
    	    reporting.scrollToTop($('#report-5'));
	    });
	},

	filterAssetMovement: function() {
		$.post("/_user/classes/ajax/AjaxReporting.class.php", {"mode": 'getFilteredAssetMovement', "start_date": $('#txtStartDate').val(), "end_date": $('#txtEndDate').val()}, function(data) {
    	    $('#report-6').html(data);
    	    reporting.scrollToTop($('#report-6'));
	    });		
	},

	getLastMonthDateRange: function() {
		$.post("/_user/classes/ajax/AjaxReporting.class.php", {"mode": 'getLastMonthDateRange'}, function(data) {
    	    var output = $.parseJSON(data);
    	    $('#txtStartDate').removeClass('hint');
    	    $('#txtEndDate').removeClass('hint');
    	    $('#txtStartDate').val(output.start);
    	    $('#txtEndDate').val(output.end);
    	    ajax.filterAssetMovement();
	    });
	},

	getThisMonthDateRange: function() {
		$.post("/_user/classes/ajax/AjaxReporting.class.php", {"mode": 'getThisMonthDateRange'}, function(data) {
    	    var output = $.parseJSON(data);
    	    $('#txtStartDate').removeClass('hint');
    	    $('#txtEndDate').removeClass('hint');
    	    $('#txtStartDate').val(output.start);
    	    $('#txtEndDate').val(output.end);
    	    ajax.filterAssetMovement();
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
					xhtml += '<tr><td>' + output.apple_computers[i].serial_number + '</td><td>' + output.apple_computers[i].type + '</td><td>' + output.apple_computers[i].description + '</td></tr>';
				}
				xhtml += '<tr><td colspan="4" class="spacer"></td></tr>';
			}

			if (output.other_computers.length > 0) {
				xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">OTHER COMPUTERS (' + output.other_computers.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
			    for (i in output.other_computers) {
					xhtml += '<tr><td>' + output.other_computers[i].serial_number + '</td><td>' + output.other_computers[i].type + '</td><td>' + output.other_computers[i].description + '</td></tr>';
				}
				xhtml += '<tr><td colspan="4" class="spacer"></td></tr>';
			}

			if (output.phones_tablets.length > 0) {
				xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">PHONES/TABLETS (' + output.phones_tablets.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
	    	    for (i in output.phones_tablets) {
					xhtml += '<tr><td>' + output.phones_tablets[i].serial_number + '</td><td>' + output.phones_tablets[i].type + '</td><td>' + output.phones_tablets[i].description + '</td></tr>';
				}
				xhtml += '<tr><td colspan="4" class="spacer"></td></tr>';
			}

			if (output.monitors_displays.length > 0) {
				xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">MONITORS/DISPLAYS (' + output.monitors_displays.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
	    	    for (i in output.monitors_displays) {
					xhtml += '<tr><td>' + output.monitors_displays[i].serial_number + '</td><td>' + output.monitors_displays[i].type + '</td><td>' + output.monitors_displays[i].description + '</td></tr>';
				}
				xhtml += '<tr><td colspan="4" class="spacer"></td></tr>';
			}

			if (output.miscellaneous.length > 0) {
				xhtml += '<thead>';
				xhtml += '<tr class="noborder"><th colspan="4">MISCELLANEOUS (' + output.miscellaneous.length + ')</th></tr>';
				xhtml += '<tr class="noborder"><th class="serial-number">Serial Number</th><th class="type">Type</th><th class="description">Description</th></tr>';
				xhtml += '</thead>';
	    	    for (i in output.miscellaneous) {
					xhtml += '<tr><td>' + output.miscellaneous[i].serial_number + '</td><td>' + output.miscellaneous[i].type + '</td><td>' + output.miscellaneous[i].description + '</td></tr>';
				}
			}

			xhtml += '</table>';

			$('#inventory').html(xhtml);
			//reporting.bindDynamicControls();
		});
	},

	getAcl: function() {
	    $.post("/_user/classes/ajax/AjaxReporting.class.php", {"mode": 'getAcl'}, function(data) {
    	    var output = $.parseJSON(data);
    	    reporting.acl = output['acl'];
	    });
	}
}

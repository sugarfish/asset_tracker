/**
 * Ajax Functions:
 * @param string mode
 * @param array items
 */

var ajax = {

	runAudit: function() {
		ajax.getAssignedNotAssigned();
	},

	getAssignedNotAssigned: function() {
		$.post("/_user/classes/ajax/AjaxAudit.class.php", {"mode": 'getAssignedNotAssigned'}, function(data) {
    	    //$('#results-container').html(data);
    	    var output = $.parseJSON(data);
    	    var xhtml = '<table class="audit"><thead><tr><th colspan="2">"Assigned" Assets With No Assignment</th></tr>';
    	    xhtml += '<tr class="subhead"><th>asset id</th><th>serial number</th></tr></thead><tbody>';
    	    if (output.length == 0) {
    	    	xhtml += '<tr><td colspan="2">Ok.</td></tr>';
    	    } else {
	    	  	for (i in output) {
	    	    	xhtml += '<tr><td>' + output[i]['asset_id'] + '</td><td>' + output[i]['serial_number'] + '</td></tr>';
	    	    }
	    	}
    	    xhtml += '</tbody></table>';
    	    $('#results-container').html(xhtml);

			ajax.getDuplicateAssignments();
	    });
	},

	getDuplicateAssignments: function() {
		$.post("/_user/classes/ajax/AjaxAudit.class.php", {"mode": 'getDuplicateAssignments'}, function(data) {
    	    //$('#results-container').html(data);
    	    var output = $.parseJSON(data);
    	    var xhtml = '<table class="audit"><thead><tr><th colspan="4">Duplicate Assignments</th></tr>';
    	    xhtml += '<tr class="subhead"><th>asset id</th><th>serial number</th><th>employee id</th><th>department id</th></tr></thead><tbody>';
    	    if (output.length == 0) {
    	    	xhtml += '<tr><td colspan="4">Ok.</td></tr>';
    	    } else {
	    	  	for (i in output) {
	    	    	xhtml += '<tr><td>' + output[i]['asset_id'] + '</td><td>' + output[i]['serial_number'] + '</td><td>' + output[i]['employee_id'] + '</td><td>' + output[i]['department_id'] + '</td></tr>';
	    	    }
	    	}
    	    xhtml += '</tbody></table>';
    	    $('#results-container').append(xhtml);

    	    ajax.getExitsWithAssets();
	    });
	},

	getExitsWithAssets: function() {
		$.post("/_user/classes/ajax/AjaxAudit.class.php", {"mode": 'getExitsWithAssets'}, function(data) {
    	    //$('#results-container').html(data);
    	    var output = $.parseJSON(data);
    	    var xhtml = '<table class="audit"><thead><tr><th colspan="4">Ex-Employees with Assets</th></tr>';
    	    xhtml += '<tr class="subhead"><th>employee id</th><th>name</th></tr></thead><tbody>';
    	    if (output.length == 0) {
    	    	xhtml += '<tr><td colspan="2">Ok.</td></tr>';
    	    } else {
	    	  	for (i in output) {
	    	    	xhtml += '<tr><td>' + output[i]['employee_id'] + '</td><td>' + output[i]['name'] + '</td></tr>';
	    	    }
	    	}
    	    xhtml += '</tbody></table>';
    	    $('#results-container').append(xhtml);

    	    ajax.getEmployeesWithMultipleComputingDevices();
	    });
	},

	getEmployeesWithMultipleComputingDevices: function() {
		$.post("/_user/classes/ajax/AjaxAudit.class.php", {"mode": 'getEmployeesWithMultipleComputingDevices'}, function(data) {
    	    //$('#results-container').html(data);
    	    var output = $.parseJSON(data);
    	    var xhtml = '<table class="audit"><thead><tr><th colspan="4">Employees with More Than One Computing Device</th></tr>';
    	    xhtml += '<tr class="subhead"><th>employee id</th><th>name</th><th>asset</th><th>serial number (asset id)</th></tr></thead><tbody>';
    	    if (output.length == 0) {
    	    	xhtml += '<tr><td colspan="4">Ok.</td></tr>';
    	    } else {
	    	  	for (i in output) {
	    	    	xhtml += '<tr><td>' + i + '</td><td>' + output[i]['name'] + '</td>';
	    	    	var first = true;
	    	    	for (j in output[i]['computers']) {
	    	    		if (first) {
	    	    			xhtml += '<td>' + output[i]['computers'][j]['type'] + '</td><td>' + output[i]['computers'][j]['serial_number'] + ' (' + output[i]['computers'][j]['asset_id'] + ')</td></tr>';
	    	    			first = false;
	    	    		} else {
	    	    			xhtml += '<tr><td></td><td></td><td>' + output[i]['computers'][j]['type'] + '</td><td>' + output[i]['computers'][j]['serial_number'] + ' (' + output[i]['computers'][j]['asset_id'] + ')</td></tr>';
	    	    		}
	    	    	}
	    	    }
	    	}
    	    xhtml += '</tbody></table>';
    	    $('#results-container').append(xhtml);
	    });
	},

	getAcl: function() {
	    $.post("/_user/classes/ajax/AjaxAudit.class.php", {"mode": 'getAcl'}, function(data) {
    	    var output = $.parseJSON(data);
    	    audit.acl = output['acl'];
    	    if (audit.acl == 1) {
				$('#btnRunAudit').css('display', 'inline-block');
			} else {
				$('#results-container').html('<span class="error_message">Insufficient privileges to run audit.</span>');
			}
	    });
	}

}
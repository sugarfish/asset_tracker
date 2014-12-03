var reporting = {
  
	// global vars
	inventory_open: false,
	scratchpad_open: false,
	acl: 0,

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
		$('#tabs').tabs({
			show: function(event, ui) {
				var current_tab = $(this).tabs('option', 'active');
				last_tab = current_tab;
			}
		});

		$('#print-dialog').dialog({
            title: 'Edit Asset',
            width: 400,
            height: 400,
            modal: true,
            autoOpen: false,
            resizable: false,
            beforeClose: function(event, ui) {
                //...
            }
        });

		// bind control events
		$('#txtFilter').bind('keyup', $.proxy(this, 'filterAssetHistory'));

		$('#txtStartDate').bind('keyup', $.proxy(this, 'filterAssetMovement'));
		$('#txtEndDate').bind('keyup', $.proxy(this, 'filterAssetMovement'));

		$('#txtStartDate').bind('focus', function() {
			if ($('#txtStartDate').val() == 'mmddyyyy') {
				$('#txtStartDate').val("");
				$('#txtStartDate').removeClass('hint');
			}
		});
		$('#txtStartDate').bind('blur', function() {
			if ($('#txtStartDate').val() == '') {
				$('#txtStartDate').addClass('hint');
				$('#txtStartDate').val('mmddyyyy');
			}
		});
		$('#txtEndDate').bind('focus', function() {
			if ($('#txtEndDate').val() == 'mmddyyyy') {
				$('#txtEndDate').val("");
				$('#txtEndDate').removeClass('hint');
			}
		});
		$('#txtEndDate').bind('blur', function() {
			if ($('#txtEndDate').val() == '') {
				$('#txtEndDate').addClass('hint');
				$('#txtEndDate').val('mmddyyyy');
			}
		});

		$('#inventory-title').bind('click', $.proxy(this, 'toggleInventory'));
		$('#inventory').bind('keyup', $.proxy(this, 'toggleInventory'));
		$('#inventory').bind('blur', $.proxy(this, 'toggleInventory'));

		$('#scratchpad-title').bind('click', $.proxy(this, 'toggleScratchpad'));
		$('#scratchpad').bind('keyup', $.proxy(this, 'saveScratchpad'));
		$('#scratchpad').bind('blur', $.proxy(this, 'saveScratchpad'));

		/*
		$('#txtStartDate').keyup(function(e) {
			if (e.keyCode == 8) {
				$('#txtStartDate').val("");
			}
		});

		$('#txtEndDate').keyup(function(e) {
			if (e.keyCode == 8) {
				$('#txtEndDate').val("");
			}
		});
		*/

		// buttons
		$('#btnClearScratchpad').bind('click', $.proxy(this, 'clearScratchpad'));

		$('#btnLastMonth').bind('click', function() {
			ajax.getLastMonthDateRange();
		});

		$('#btnThisMonth').bind('click', function() {
			ajax.getThisMonthDateRange();
		});

		$('#btnClear').bind('click', function() {
			$('#txtStartDate').addClass('hint');
			$('#txtStartDate').val('mmddyyyy');
			$('#txtEndDate').addClass('hint');
			$('#txtEndDate').val('mmddyyyy');
			ajax.filterAssetMovement();
		});

		$('.btnPrint').bind('click', function() {
			var source = 'report-' + $(this).parent().parent().attr('id').replace('tab-', '');
			$('#output').html($('#' + source).html());
			$('#report').html($('#' + source).html());
			reporting.showDialog('print-dialog', 'Print Preview');
		});

		$('.btnExport').bind('click', function() {
			var id = 'report-' + $(this).parent().parent().attr('id').replace('tab-', '');
			var filter = ($('#txtFilter').val() != '')?$('#txtFilter').val():'-';
			var start_date = ($('#txtStartDate').val() != '')?$('#txtStartDate').val():'-';
			var end_date = ($('#txtEndDate').val() != '')?$('#txtEndDate').val():'-';
			window.open("/asset/tracking/export/report/" + id + "/filter/" + filter + '/start_date/' + start_date + '/end_date/' + end_date);
		});

		// dialog button events
		$('#btnPrintCancel').bind('click', $.proxy(this, 'closeDialog', 'print-dialog'));
		$('#btnPrint').bind('click', function() {
			reporting.closeDialog('print-dialog');
			window.print();
		});

		// dialog events
		//

		// dialog button events
		//

		// do stuff
		reporting.resizePaper();
		reporting.loadInventory();
		reporting.loadScratchpad();
		$('#txtStartDate').addClass('hint');
		$('#txtStartDate').val('mmddyyyy');
		$('#txtEndDate').addClass('hint');
		$('#txtEndDate').val('mmddyyyy');
		ajax.getAcl();
	},

	filterAssetHistory: function() {
		ajax.filterAssetHistory();
	},

	filterAssetMovement: function() {
		/*
		if ($('#txtStartDate').val().length == 8 && $('#txtStartDate').val() != 'mmddyyyy' && $('#txtEndDate').val() == 'mmddyyyy') {
			$('#txtEndDate').focus();
		}
		*/

		if ($('#txtStartDate').val().length == 8 && $('#txtStartDate').val() != 'mmddyyyy' && $('#txtEndDate').val().length == 8 && $('#txtEndDate').val() != 'mmddyyyy') {
			ajax.filterAssetMovement();
		}
	},

	getLastMonthDateRange: function() {
		ajax.getLastMonthDateRange();
	},

	getThisMonthDateRange: function() {
		ajax.getThisMonthDateRange();
	},

	showDialog: function(dlgName, title) {
		$('#' + dlgName).dialog('option', 'title', title);
		$('#' + dlgName).dialog('open');
	},

	closeDialog: function(dlgName) {
		$('#' + dlgName).dialog('option', 'title', '');
		$('#' + dlgName).dialog('close');
	},

	resizePaper: function() {
		if ($(window).height() - 200 > 200) {
			$('.paper').css('min-height', $(window).height() - 154);
			$('#report').css('height', $(window).height() - 144);
			$('#report-container').css('height', $(window).height() - 144);
			$('#report-1').css('height', $(window).height() - 254);
			$('#report-2').css('height', $(window).height() - 254);
			$('#report-3').css('height', $(window).height() - 254);
			$('#report-4').css('height', $(window).height() - 254);
			$('#report-5').css('height', $(window).height() - 254);
			$('#report-6').css('height', $(window).height() - 254);
			$('#report-7').css('height', $(window).height() - 254);
		}
	},

	toggleInventory: function() {
		if (reporting.scratchpad_open) {
			reporting.toggleScratchpad();
		}

		if (!reporting.inventory_open) {
			reporting.inventory_open = true;
			/*
			$('#scratchpad-container').animate({
				opacity: 0,
				zIndex: 0
			}, 0);
			*/
			$('#inventory-container').animate({
				opacity: 1,
				right: '+=662'
			}, 250, 'easeOutQuint', function() {
				$('#inventory-title').addClass('open');
				$('#inventory').focus();
			});
		} else {
			reporting.inventory_open = false;
			reporting.scrollToTop($('#inventory'));
			$('#inventory-container').animate({
				opacity: .95,
				right: '-=662'
			}, 250, 'easeOutCubic', function() {
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
		if (reporting.inventory_open) {
			reporting.toggleInventory();
		}

		if (!reporting.scratchpad_open) {
			reporting.scratchpad_open = true;
			$('#scratchpad-container').animate({
				opacity: 1,
				right: '+=267'
			}, 250, 'easeOutQuint', function() {
				$('#scratchpad-title').addClass('open');
				$('#scratchpad').focus();
			});
		} else {
			reporting.scratchpad_open = false;
			$('#scratchpad-container').animate({
				opacity: .95,
				right: '-=267'
			}, 250, 'easeOutCubic', function() {
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
		reporting.saveScratchpad();
	},

	scrollToTop: function(elem) {
		elem.animate({"scrollTop": 0}, 'slow');
	}
};

$(document).ready(function() {
	reporting.init({
		//options
		/*
        someOption: true
        */
    });
});

$(window).resize(function() {
	reporting.resizePaper();
});
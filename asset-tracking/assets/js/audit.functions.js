var audit = {
  
	// global vars
	/*
	something: 10,
	somethingElse: location.hash,
	*/
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

		$('#error-dialog').dialog({
            width: 800,
            height: 400,
            modal: true,
            autoOpen: false,
            resizable: false,
            beforeClose: function(event, ui) {
                $('#error').html("");
            }
        });

		// bind control events
		$('#btnRunAudit').bind('click', $.proxy(this, 'runAudit'));

		// dialog button events
		$('#btnErrorCancel').bind('click', $.proxy(this, 'closeDialog', 'error-dialog', true));

		$('#scratchpad-title').bind('click', $.proxy(this, 'openScratchpad'));
		$('#scratchpad').bind('keyup', $.proxy(this, 'saveScratchpad'));
		$('#scratchpad').bind('blur', $.proxy(this, 'saveScratchpad'));
		$('#btnClearScratchpad').bind('click', $.proxy(this, 'clearScratchpad'));

		// do stuff
		audit.resizePaper();
		audit.loadScratchpad();
		ajax.getAcl();
	},

	runAudit: function() {
		ajax.runAudit();
	},

	resizePaper: function() {
		if ($(window).height() - 200 > 200) {
			$('.paper').css('min-height', $(window).height() - 192);
			$('#results-container').css('height', $(window).height() - 212);
		}
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

	openScratchpad: function() {
		if (!audit.scratchpad_open) {
			audit.scratchpad_open = true;
			$('#scratchpad-container').animate({
				opacity: 1,
				right: '+=267',
			}, 250, 'easeOutQuint', function() {
				$('#scratchpad-title').addClass('open');
				$('#scratchpad').focus();
			});
		} else {
			audit.scratchpad_open = false;
			$('#scratchpad-container').animate({
				opacity: .95,
				right: '-=267',
			}, 250, 'easeOutCubic', function() {
				$('#scratchpad-title').removeClass('open');
			});
		}
	},

	loadScratchpad: function() {
		$('#scratchpad').val(localStorage.getItem('scratchpad'));

		if ($('#scratchpad').val().length > 0) {
			audit.openScratchpad();
		}
	},

	saveScratchpad: function() {
		localStorage.setItem('scratchpad', $('#scratchpad').val());
	},

	clearScratchpad: function() {
		$('#scratchpad').val("");
		audit.saveScratchpad();
	}
};

$(document).ready(function() {
	audit.init({
		//options
		/*
        someOption: true
        */
    });
});

$(window).resize(function() {
	audit.resizePaper();
});
var sf = {
	initialize: function() {

		// validate email address
		this.isValidEmailAddress = function(val) {
			var filter = /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;
			if (filter.test(val)) {
				return true;
			}
		}

		// validate url
		this.isValidHttpAddress = function(val) {
			if (val.substr(0, 7) == 'http://' || val.substr(0, 8) == 'https://') {
				return true;
			}
		}

		// escape string
		this.escapeString = function(val) {
			return escape(val).replace(new RegExp( "\\+", "g" ), "%2B");
		}

		// show/hide the spinner
		this.showHideBusy =function(show) {
			if (getElem('busy')) {
				if (show) {
					//var left = (document.documentElement.clientWidth - 220) / 2;
					//var top = (document.documentElement.clientHeight - 19) / 2;
					var left = 3;
					var top = 5;
					setStyle('busy', 'left', left + 'px');
					setStyle('busy', 'top', top + 'px');
					setStyle('busy', 'display', 'block');
				} else {
					window.setTimeout("setStyle('busy', 'display', 'none')", 1000);
				}
			}
		}

		this.isNumber = function(val, bool) {
			// bool = true ensures that decimal points are counted as numeric
			if (bool) {
				var numbers = "0123456789.";
			} else {
				var numbers = "0123456789";
			}
			var isNumeric = true;
			var character;

			for (var i=0; i<val.length && isNumeric == true; i++) { 
				character = val.charAt(i); 
				if (numbers.indexOf(character) == -1) {
					isNumeric = false;
				}
			}
			return isNumeric;
		}

		this.chr = function(val) {
			return String.fromCharCode(val);
		}

		// miscellaneous functions //
		this.writeCookie = function(name, value, days) {
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days*24*60*60*1000));
				var expires = "; expires=" + date.toGMTString();
			} else {
				var expires = "";
			}
			document.cookie = name + "=" + value + expires + "; path=/; domain=192.168.1.102";
		}

		this.readCookie = function(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for (var i=0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1, c.length);
				}
				if (c.indexOf(nameEQ) == 0) {
					return c.substring(nameEQ.length, c.length);
				}
			}
			return null;
		}

		this.eraseCookie = function(name) {
			writeCookie(name, "", -1);
		}

		// helper functions
		this.log_error = function(vars, comment) {
			ajaxFunc('logError', [vars, comment]);
		}

	}
}

sf.initialize();

// string prototypes //
// trim functions
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
};
		
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
};

String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
};

// capitalize function
String.prototype.capitalize = function() {
	var sInput = this;
	var sOutput, cTemp, sPre, sPost, sLength;
	var sTemp = sInput.toLowerCase();
	var sLength = sTemp.length;
	if (sLength > 0) {
		for (i = 0; i < sLength; i++) {
			if (i == 0) {
				cTemp = sTemp.substring(0, 1).toUpperCase();
				sPost = sTemp.substring(1, sLength);
				sTemp = cTemp + sPost;
			} else {
				cTemp = sTemp.substring(i, i+1);
				if (cTemp == " " && i < (sLength-1)) {
					cTemp = sTemp.substring(i+1, i+2).toUpperCase();
					sPre = sTemp.substring(0, i+1);
					sPost = sTemp.substring(i+2, sLength);
					sTemp = sPre + cTemp + sPost;
				}
			}
		}
	}
	sOutput = sTemp;
	return sOutput;
};

$(document).ready(function() {
	
	// global modal dialog function
	$('.popup').each(function() {
		var $link = $(this);
		var $dialog = $('<div></div>')
			.load($link.attr('href') + ' #' + $link.attr('id'))
			.dialog({
				autoOpen: false,
				modal: true,
				title: $link.attr('title'),
				width: $link.attr('width') + 'px',
				height: $link.attr('height'),
				resizable: false
			});

		$link.click(function() {
			$dialog.dialog('open');
			$(".first").blur();

			return false;
		});
	});

});

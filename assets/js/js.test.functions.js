/*
 * JavaScript: HTML5 + JavaScript
 * Author: Ian Atkin
 * Copyright (c) 2011 Ian Atkin
 */

var testJS = {
  
	hashLimit: 10,
	pageHash: location.hash,
	canvas: null,
	context: null,
	x: 270,
	y: 170,
	d: 'u',
	topLimit: 10,
	bottomLimit: 400,
	leftLimit: 0,
	rightLimit: 590,
	color: '#09f',

	init: function(options) {
		var defaultSettings = {
			someOption: false,
			fadeLogo: false
		},

		settings = $.extend({}, this.defaultSettings, options),

		base = this;

		// check for hash and validate value    
		if (this.pageHash && (parseInt((this.pageHash.substring(1)), 10) <= this.hashLimit)) {
			this.messageFunction(parseInt(this.pageHash.replace('#', ''), 10));
		}

		// set up tabs control
		$('#tabs').tabs({
			show: function(event, ui) {
				//var current_tab = $(this).tabs('option', 'selected');
				//last_tab = current_tab;
			}
		});

		// set up tags control
		$('#tag_list').tagsInput({
			addText: 'add tag',
			removeText: 'remove tag',
			width: 380,
			height: 170,
			delimiter: ',',
			sort: true,
			allowDuplicates: false,
			dirtyOnEdit: true
		});

		// set up the colorSwitch object
		colorSwitcher.init();

		// run ajax calls
		ajax.getMessage(15);
		ajax.getTags(11);

		// set up the canvas
		//canvas = document.getElementById('the-canvas');
		canvas = $('#canvas');
		canvas = canvas[0]; // jquery returns an array (?), so we take the first element and stuff it back into the canvas property
		context = canvas.getContext('2d');

		canvas.setAttribute('width', '600');
		canvas.setAttribute('height', '400');

		this.drawShape();

		// bind control events
		$('html').bind('keydown', $.proxy(this, 'keyControls'));
		$('#html5-logo').bind('click', $.proxy(this, 'messageFunction'));

		if (settings.fadeLogo === true) {
			setTimeout(function() {
				$('#html5-logo').fadeTo(1000, .3);
			}, 1500);

			$('#html5-logo').hover(
				function() {
					$('#html5-logo').fadeTo(300, 1);
				},
				function() {
					$('#html5-logo').fadeTo(300, .3);
				}
			);

			setTimeout(function(){
				$('header').fadeTo(300, 0);
			}, 1500);

			$('header').hover(
				function() {
					$('header').fadeTo(300, 1);
				},
				function() {
					$('header').fadeTo(300, 0);
				}
			);
		}

		// ensure focus stays on window and not embedded iframes/objects
		$(window).load(function() {
			this.focus();
		});

        if (settings.someOption) {
            // set up the poo object
            poo.init();
        }
	},

	messageFunction: function(msg) {
		if (typeof(msg) == 'object') {
			$("#events").animate({scrollTop: $("#events").attr("scrollHeight")}, 100);
			this.x = 270;
			this.y = 170;
			this.d = 'u';
			this.color = '#09f';
			this.drawShape();
			$('#events').html('');
            $('#events').append('[reset] ');
		} else {
			$('#events').append(msg + ' ');
			$("#events").animate({scrollTop: $("#events").attr("scrollHeight")}, 100);
		}
	},

	keyControls: function(event) {
		switch(event.keyCode) {
			// left, up, and page up keys
			case 33:
				this.messageFunction('page up');
				this.d = 'u';
				this.y = this.y - 50;
				if (this.y < this.topLimit) {
					this.y = this.topLimit;
				}
				this.drawShape();
				return false;
			case 37:
				this.messageFunction('left');
				this.d = 'l';
				this.x = this.x - 10;
				if (this.x < this.leftLimit) {
					this.x = this.leftLimit;
				}
				this.drawShape();
				return false;
			case 38:
				this.messageFunction('up');
				this.d = 'u';
				this.y = this.y - 10;
				if (this.y < this.topLimit) {
					this.y = this.topLimit;
				}
				this.drawShape();
				return false;
			// right, down, spacebar, and page down keys
			case 32:
				this.messageFunction('switch color ');
				colorSwitcher.switchColor();
				return false;
			case 34:
				this.messageFunction('page down');
				this.d = 'd';
				this.y = this.y + 50;
				if (this.y > this.bottomLimit) {
					this.y = this.bottomLimit;
				}
				this.drawShape();
				return false;
			case 39:
				this.messageFunction('right');
				this.d = 'r';
				this.x = this.x + 10;
				if (this.x > this.rightLimit) {
					this.x = this.rightLimit;
				}
				this.drawShape();
				return false;
			case 40:
				this.messageFunction('down');
				this.d = 'd';
				this.y = this.y + 10;
				if (this.y > this.bottomLimit) {
					this.y = this.bottomLimit;
				}
				this.drawShape();
				return false;
		}
	},

	drawShape: function() {
		canvas.width = canvas.width;
		context.strokeStyle = this.color;

		switch (this.d) {
			case 'u':
				context.beginPath();
				context.moveTo(this.x, this.y);
				context.lineTo(this.x+5, this.y-10);
				context.lineTo(this.x+10, this.y);
				context.lineWidth = 2;
				context.lineJoin = "round";
				context.stroke();
				break;
			case 'd':
				context.beginPath();
				context.moveTo(this.x, this.y-10);
				context.lineTo(this.x+5, this.y);
				context.lineTo(this.x+10, this.y-10);
				context.lineWidth = 2;
				context.lineJoin = "round";
				context.stroke();
				break;
			case 'l':
				context.beginPath();
				context.moveTo(this.x+10, this.y-5);
				context.lineTo(this.x, this.y);
				context.lineTo(this.x+10, this.y+5);
				context.lineWidth = 2;
				context.lineJoin = "round";
				context.stroke();
				break;
			case 'r':
				context.beginPath();
				context.moveTo(this.x, this.y-5);
				context.lineTo(this.x+10, this.y);
				context.lineTo(this.x, this.y+5);
				context.lineWidth = 2;
				context.lineJoin = "round";
				context.stroke();
		}

		this.updateCoords();
	},

	updateCoords: function() {
		$('#coords').html('x: ' + this.x + ', y: ' + this.y);
	},
};

var colorSwitcher = {
	// a different class
	init: function() {},

	switchColor: function() {
		testJS.color = (testJS.color == '#09f')?'#f00':'#09f';
		testJS.drawShape();
	}
};

var poo = {
    // a bogus class
    init: function() {
        alert('I made poo!');
    }
};

$(document).ready(function() {
	testJS.init({
        fadeLogo: true,
        someOption: true
    });
	//alert(testJS.color);
});


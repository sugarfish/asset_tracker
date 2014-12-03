/**
/**
 * File: jquery.tags.js
 * Created on: Sun Apr 03 19:47 CST 2011
 *
 * @author Ian
 *
 * @copyright  2010 Ian Atkin
 * @license    http://www.ianatkin.info/
 *
 * Usage:
 * 		Call the following (once per app, per input field):
 * 		$('#input_field').tagsInput( { [options] });
 *
 * Options:
 * 		Option Name				Default Value
 * 		addText (string)		'add tag'
 * 		removeText (string)		'remove tag'
 * 		width					'300px'
 * 		height					'100px'
 * 		delimiter				','
 * 		sort					true
 * 		allowDuplicates			false
 * 		dirtyOnEdit				true
 *
 * Methods:
 * 
 * 		insertTags(string)	- use this to insert the list of tags from the db
 * 		compileTags()		- returns a string containing all tags delimited by whatever is specified in the options
 * 		clearTags()			- removes all tags
 * 		addTag(string)		- adds a single tag
 * 		removeTag(string)	- removes the specified tag
 */

(function($) {

	var delimiter = new Array();
	var inserting = false;
	var error = false;

	$.fn.insertTags = function(value) {
		inserting = true;
		var tagslist = value.split(delimiter[id]);
		if (tagslist[0] == '') {
			tagslist = new Array();
		}
		for (i=0; i<tagslist.length; i++) {
			this.addTag(tagslist[i]);
		}
		inserting = false;
		return false;
	}

	$.fn.compileTags = function() {
		this.each(function() {
			id = $(this).attr('id');
			var old = $(this).val().split(delimiter[id]);
			if (settings.sort) {
				$.fn.sortTags(old);
			}
			$('#' + id + '_tagsinput .tag').remove();
			str = '';
			for (i=0; i<old.length; i++) {
				str = str + old[i];
				if (i<old.length-1) {
					str = str + delimiter[id];
				}
			}
			$.fn.tagsInput.importTags(this, str);
		});
		return str;
	}

	$.fn.clearTags = function() {
		this.each(function() {
			id = $(this).attr('id');
			var old = $(this).val().split(delimiter[id]);
			$('#' + id + '_tagsinput .tag').remove();
			str = '';
			$.fn.tagsInput.importTags(this, str);
		});
		return false;
	}

	$.fn.addTag = function(value, options) {
		var options = $.extend({focus:false}, options);
		this.each(function() {
			id = $(this).attr('id');
			var tagslist = $(this).val().split(delimiter[id]);
			if (tagslist[0] == '') {
				tagslist = new Array();
				dupelist = new Array();
			}
			value = $.trim(value);
			if (!settings.allowDuplicates) {
				if ($.inArray(value, dupelist) != -1) {
					if (!inserting) {
						error = true;
					}
					return false;
				}
			}
			if (value !='') {
				$('<span class="tag">' + value + '&nbsp;&nbsp;<a href="#" title="' + settings.removeText + '" onclick="return $(\'#' + id + '\').removeTag(\'' + escape(value) + '\');">x</a></span>').insertBefore('#' + id + '_addTag');
				tagslist.push(value);
				dupelist.push(value);
				$('#' + id + '_tag').val('');
				if (options.focus) {
					$('#' + id + '_tag').focus();
				} else {		
					$('#' + id + '_tag').blur();
				}
			}
			$.fn.tagsInput.updateTagsField(this, tagslist);
		});
		return false;
	}

	$.fn.removeTag = function(value) {
		this.each(function() {
			id = $(this).attr('id');
			var old = $(this).val().split(delimiter[id]);
			$('#' + id + '_tagsinput .tag').remove();
			str = '';
			for (i=0; i<old.length; i++) {
				if (escape(old[i])!=value) {
					str = str + delimiter[id] + old[i];
				}
			}
			$.fn.tagsInput.importTags(this, str);
			if (settings.dirtyOnEdit) {
				$.fn.setDirty();
			}
		});
		return false;
	}

	$.fn.tagsInput = function(options) {

		settings = $.extend({
			addText: 'add tag',
			removeText: 'remove tag',
			width: '300px',
			height: '100px',
			hide: true,
			delimiter: ',',
			sort: true,
			allowDuplicates: false,
			dirtyOnEdit: true,
			autocomplete: {selectFirst: false}
		}, options);

		this.each(function() { 
			if (settings.hide) { 
				$(this).hide();				
			}
				
			id = $(this).attr('id')
			
			data = jQuery.extend({
				pid:id,
				real_input: '#'+id,
				holder: '#'+id+'_tagsinput',
				input_wrapper: '#'+id+'_addTag',
				fake_input: '#'+id+'_tag'
			},settings);
	
			delimiter[id] = data.delimiter;

			$('<div id="' + id + '_tagsinput" class="tagsinput"><div id="' + id + '_addTag"><input id="' + id + '_tag" value="" default="' + settings.addText + '" /></div><div class="tags_clear"></div></div>').insertAfter(this);

			$(data.holder).css('width', settings.width);
			$(data.holder).css('height', settings.height);

			if ($(data.real_input).val()!='') {
				$.fn.tagsInput.importTags($(data.real_input), $(data.real_input).val());
			} else {
				$(data.fake_input).val($(data.fake_input).attr('default'));
				$(data.fake_input).css('color', '#666');
			}

			$(data.holder).bind('click', data, function(event) {
				$(event.data.fake_input).focus();
			});

			// create a new tag
			$(data.fake_input).bind('keypress', data, function(event) {
				if (event.which==event.data.delimiter.charCodeAt(0) || event.which==13) {
					$(event.data.real_input).addTag($(event.data.fake_input).val(), {focus:true});
					if (!error) {
						if (settings.dirtyOnEdit) {
							$.fn.setDirty();
						}
					}
					error = false;
					return false;
				}
			});

			$(data.fake_input).bind('focus', data, function(event) {
				if ($(event.data.fake_input).val()==$(event.data.fake_input).attr('default')) {
					$(event.data.fake_input).val('');
				}
				$(event.data.fake_input).css('color', '#000');
			});

			if (settings.autocomplete_url != undefined) {
				$(data.fake_input).autocomplete(settings.autocomplete_url, settings.autocomplete).bind('result', data, function(event, data, formatted) {
					if (data) {
						d = data + "";	
						$(event.data.real_input).addTag(d, {focus:true});
					}
				});;

				$(data.fake_input).bind('blur', data, function(event) {
					if ($(event.data.fake_input).val() != $(event.data.fake_input).attr('default')) {
						$(event.data.real_input).addTag($(event.data.fake_input).val(), {focus:false});						
					}

					$(event.data.fake_input).val($(event.data.fake_input).attr('default'));
					$(event.data.fake_input).css('color', '#666');
					return false;
				});

			} else {
					// create a new tag on tab
					$(data.fake_input).bind('blur', data, function(event) {
						var d = $(this).attr('default');
						if ($(event.data.fake_input).val()!='' && $(event.data.fake_input).val()!=d) {
							event.preventDefault();
							$(event.data.real_input).addTag($(event.data.fake_input).val(), {focus:true});
						} else {
							$(event.data.fake_input).val($(event.data.fake_input).attr('default'));
							$(event.data.fake_input).css('color', '#666');
						}
						return false;
					});
			
			}
			$(data.fake_input).blur();
		});
		return this;
	}

	$.fn.tagsInput.updateTagsField = function(obj, tagslist) {
		id = $(obj).attr('id');
		$(obj).val(tagslist.join(delimiter[id]));
	}

	$.fn.tagsInput.importTags = function(obj, val) {
		$(obj).val('');
		id = $(obj).attr('id');
		var tags = val.split(delimiter[id]);
		for (i=0; i<tags.length; i++) {
			$(obj).addTag(tags[i], {focus:false});
		}
	}

	$.fn.sortTags = function(tagslist) {
		var sorted = '';
		tagslist.sort();
		$.each(tagslist, function(item_a, item_b) {sorted += (item_b) + ',';});
	}

	$.fn.setDirty = function() {
		// dummy
		alert('Clean me, Seymour!');
	}
})($);

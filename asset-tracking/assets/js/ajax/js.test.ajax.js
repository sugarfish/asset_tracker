/*
 * JavaScript: HTML5 + JavaScript
 * Author: Ian Atkin
 * Copyright (c) 2011 Ian Atkin
 */

var ajax = {

	getMessage: function(message_id) {
		$.post("/_user/classes/ajax/AjaxJSTest.class.php", {"mode": 'getMessage', 'message_id': message_id}, function(data) {
			message = $.parseJSON(data);
			$('#message').html(message);
		});
	},

	getTags: function(item_id) {
		$.post("/_user/classes/ajax/AjaxJSTest.class.php", {"mode": 'getTags', 'item_id': item_id}, function(data) {
			tags = $.parseJSON(data);

			var tags_delimited = '';
			for (i in tags) {
				tags_delimited += tags[i] + ',';
			}
			$('#tag_list').clearTags();
			$('#tag_list').insertTags(tags_delimited);
		});
	}
}

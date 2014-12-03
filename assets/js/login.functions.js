var login = {
  
	// global vars
	/*
	something: 10,
	somethingElse: location.hash,
	*/

	init: function() {

		base = this;

		// set focus
		$('#txtUsername').focus();
	}
};

$(document).ready(function() {
	login.init();
});
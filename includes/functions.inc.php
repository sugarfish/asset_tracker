<?php
	// Special Print Functions / Shortcuts
	/**
	 * Standard Print function.  To aid with possible cross-scripting vulnerabilities,
	 * this will automatically perform Application::HtmlEntities() unless otherwise specified.
	 *
	 * @param string $strString string value to print
	 * @param boolean $blnHtmlEntities perform HTML escaping on the string first
	 */
	function _p($strString, $blnHtmlEntities = true) {
		// Standard Print
		if ($blnHtmlEntities && (gettype($strString) != 'object'))
			print(Application::HtmlEntities($strString));
		else
			print($strString);
	}

	/**
	 * Standard Print as Block function.  To aid with possible cross-scripting vulnerabilities,
	 * this will automatically perform Application::HtmlEntities() unless otherwise specified.
	 * 
	 * Difference between _b() and _p() is that _b() will convert any linebreaks to <br/> tags.
	 * This allows _b() to print any "block" of text that will have linebreaks in standard HTML.
	 *
	 * @param string $strString
	 * @param boolean $blnHtmlEntities
	 */
	function _b($strString, $blnHtmlEntities = true) {
		// Text Block Print
		if ($blnHtmlEntities && (gettype($strString) != 'object'))
			print(nl2br(Application::HtmlEntities($strString)));
		else
			print(nl2br($strString));
	}

	/**
	 * Prints an array to the console or error log with correct formatting
	 * 
	 * @param boolean $blnErrorLog
	 *
	 */
	 function _print_array($arrArray, $blnErrorLog = false) {
		if (!$blnErrorLog) {
			print '<pre>' . print_r($arrArray, true) . '</pre>';
		} else {
			error_log(print_r($arrArray, true));
		}
	 }
?>
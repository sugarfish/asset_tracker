<?php
	// dump vars to error_log
	
	$strVarDump = "\n -- BEGIN VARIABLE DUMP --\n";

	if (count($_GET) > 0) {
		$strVarDump .= "\n -- BEGIN GET VARS --\n";
		$strVarDump .= print_r($_GET, true);
	}

	if (count($_POST) > 0) {
		$strVarDump .= "\n -- BEGIN POST VARS --\n";
		$strVarDump .= print_r($_POST, true);
	}

	if (count($_SESSION) > 0) {
		$strVarDump .= "\n -- BEGIN SESSION VARS --\n";
		$strVarDump .= print_r($_SESSION, true);
	}

	if (count($_COOKIE) > 0) {
		$strVarDump .= "\n -- BEGIN COOKIE VARS --\n";
		$strVarDump .= print_r($_COOKIE, true);
	}

	$strVarDump .= "\n -- END VARIABLE DUMP --\n\n";

	error_log($strVarDump);
?>

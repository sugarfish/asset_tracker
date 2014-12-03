<?php
	ini_set('include_path', '.:/var/www/asset-track/includes');
	require_once "prepend.inc.php";

	RoutingHandler::Run();
?>

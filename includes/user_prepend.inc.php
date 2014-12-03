<?php
///////////////////////////////////
// Define Server-specific constants
///////////////////////////////////	
/*
 * This assumes that the configuration and functions include files are in the same directory
 * as this prepend include file.
 */
require_once "user_configuration.inc.php";
	
/**
  * Global Include file
  *
  * Includes all necessary class files and includes
  */

// Ensure prepend.inc is only executed once
if (!defined('__USER_PREPEND_INCLUDED__')) {
	define('__USER_PREPEND_INCLUDED__', 1);

	// USER CLASSES
	require_once __USER_CLASSES__ . '/_enumerations.inc.php';
	require_once __USER_CLASSES__ . '/models/SessionModel.class.php';

	// page classes
	require_once __USER_CLASSES__ . '/page/Header.class.php';
	require_once __USER_CLASSES__ . '/page/Footer.class.php';
	require_once __USER_CLASSES__ . '/page/PageFooter.class.php';

	// data models
	require_once __DOCROOT__ . '/test/models/TestModel.class.php';
	require_once __DOCROOT__ . '/asset/models/AdminModel.class.php';
	require_once __DOCROOT__ . '/asset/models/ReportingModel.class.php';
	require_once __DOCROOT__ . '/asset/models/AuditModel.class.php';
	require_once __DOCROOT__ . '/asset/models/InventoryModel.class.php';

	require_once __USER_CLASSES__ . '/UserFunctions.class.php';
	require_once __USER_CLASSES__ . '/Session.class.php';
}
?>

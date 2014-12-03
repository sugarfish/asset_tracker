<?php
/**
 * File: prepend.inc.php
 * Created on: Mon Feb 25 12:04 PDT 2013
 *
 * @author Ian
 *
 * Global Include file
 *
 * Includes all necessary class files and includes
 */

// Ensure prepend.inc is only executed once
if (!defined('__PREPEND_INCLUDED__')) {
	define('__PREPEND_INCLUDED__', 1);

	///////////////////////////////////
	// Define Server-specific constants
	///////////////////////////////////	
	/*
	 * This assumes that the configuration and functions include files are in the same directory
	 * as this prepend include file.
	 */
	require_once "configuration.inc.php";
	require_once "functions.inc.php";

	// CLASSES
	require_once __CLASSES__ . '/RoutingHandler.class.php';
	require_once __CLASSES__ . '/Router.class.php';

	require_once __CLASSES__ . '/_enumerations.inc.php';

	require_once __CLASSES__ . '/ModelBase.class.php';

	require_once __CLASSES__ . '/Application.class.php';
	require_once __CLASSES__ . '/BrowserType.class.php';
	require_once __CLASSES__ . '/CustomException.class.php';
	require_once __CLASSES__ . '/MimeType.class.php';
	require_once __CLASSES__ . '/String.class.php';

	// controller classes
	require_once __CLASSES__ . '/controller/TemplateManager.class.php';
	require_once __CLASSES__ . '/controller/RequestHandler.class.php';
	require_once __CLASSES__ . '/controller/ControllerBase.class.php';
	require_once __CLASSES__ . '/controller/Controller.class.php';
	require_once __CLASSES__ . '/controller/UrlHandler.class.php';
	require_once __CLASSES__ . '/controller/ControlHandler.class.php';

	// page classes
	require_once __CLASSES__ . '/page/HeaderBase.class.php';
	require_once __CLASSES__ . '/page/FooterBase.class.php';
	require_once __CLASSES__ . '/page/PageNavigator.class.php';

	// ajax
	require_once __CLASSES__ . '/ajax/AjaxBase.class.php';

	// email
	require_once __CLASSES__ . '/email/EmailServer.class.php';
	require_once __CLASSES__ . '/email/EmailMessage.class.php';
	require_once __CLASSES__ . '/email/EmailAttachment.class.php';

	// USER CLASSES
	/**
	 * load any user classes that have been set up for prepend;
	 * this is intended as a helper, but it's just as easy to include files on an as-needed basis
	 */
	require_once 'user_prepend.inc.php';

	///////////////////////////////////
	// Initialize the Application
	///////////////////////////////////
	Application::Initialize();
}
?>

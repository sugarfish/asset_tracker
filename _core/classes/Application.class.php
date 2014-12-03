<?php
/**
 * File: Application.class.php
 * Created on: Mon Sep 15 00:10 CDT 2008
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Application-wide helper class
 *
 * @package org.sugarfish.core
 * @abstract
 * @name Application
 */

abstract class Application {

	/**
	 * Internal bitmask signifying which BrowserType the user is using;
	 * use the Application::IsBrowser() method to do browser checking
	 * @access public
	 * @static
	 * @var integer $BrowserType
	 */
	public static $BrowserType = BrowserType::Unsupported;

	/**
	 * Path of the "web root" or "document root" of the web server
	 * @access public
	 * @static
	 * @var string $DocumentRoot
	 */
	public static $DocumentRoot;

	/**
	 * Boolean value set to true if the current page is secure
	 * @access public
	 * @static
	 * @var string $SecurePage
	 */
	public static $SecurePage;

	/**
	 * The full Request URI that was requested;
	 * so for "http://www.domain.com/folder/script.php/15/25/?item=15&value=22"
	 * Application::$RequestUri would be "/folder/script.php/15/25/?item=15&value=22"
	 * @access public
	 * @static
	 * @var string $RequestUri
	 */
	public static $RequestUri;

	/**
	 * The IP address of the server running the script/PHP application;
	 * this is either the LOCAL_ADDR or the SERVER_ADDR server constant, depending
	 * on the server type, OS and configuration
	 * @access public
	 * @static
	 * @var string $ServerAddress
	 */
	public static $ServerAddress;

	/**
	 * The IP address of the client machine
	 * @access public
	 * @static
	 * @var string $RemoteAddress
	 */
	public static $RemoteAddress;

	/**
	 * The encoding type for the application (e.g. UTF-8, ISO-8859-1, etc.)
	 * @access public
	 * @static
	 * @var string $EncodingType
	 */
	public static $EncodingType = 'utf-8';

	/**
	 * Tells the application whether or not to render a page
	 * @access public
	 * @static
	 * @var boolean $NoRender
	 * @deprecated
	 */
	/*
	public static $NoRender = false;
	*/

	/**
	 * Tells the application which layout to use
	 * @access public
	 * @static
	 * @var string $Layout
	 * @deprecated
	 */
	/*
	public static $Layout;
	*/

	/**
	 * Supplies the ControlHandler with the next control ID for an anonymous control
	 * i.e. when an explicit control ID has not been supplied
	 * @access public
	 * @static
	 * @var integer $NextControlId
	 * @deprecated
	 */
	/*
	public static $NextControlId = 0;
	*/

	/**
	* This faux constructor method throws a caller exception;
	* the Application object should never be instantiated
	* @access public
	* @static
	* @return void
	*/
	public final function __construct() {
		throw new CustomException(Exceptions::STATIC_CLASS_INSTANTIATION, "Application should never be instantiated. All methods and variables are publically statically accessible.");
	}

	/**
	 * This should be the first call to initialize all the static variables
	 * the application object also has static methods that are miscellaneous web
	 * development utilities, etc.
	 * @access public
	 * @static
	 * @return void
	 */
	public static function Initialize() {
		// Setup Server Address
		self::$ServerAddress = $_SERVER['SERVER_ADDR'];
		self::$RemoteAddress = $_SERVER['REMOTE_ADDR'];

		// Setup RequestUri
		self::$RequestUri = $_SERVER['REQUEST_URI'];

		// Setup DocumentRoot
		self::$DocumentRoot = trim(__DOCROOT__);

		// Setup SecurePage
		self::$SecurePage = ($_SERVER['HTTPS'] == 'on')?true:false;

		if (!defined('__DEFAULT_MODULE__')) {
			define('__DEFAULT_MODULE__', 'index');
		}

		if (!defined('__DEFAULT_CONTROLLER__')) {
			define('__DEFAULT_CONTROLLER__', 'index');
		}

		if (!defined('__DEFAULT_ACTION__')) {
			define('__DEFAULT_ACTION__', 'index');
		}

		// Setup Browser Type
		if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
			$strUserAgent = trim(strtolower($_SERVER['HTTP_USER_AGENT']));

			if (strpos($strUserAgent, 'msie') !== false) {
				// Internet Explorer
				Application::$BrowserType = BrowserType::InternetExplorer;

			} else if ((strpos($strUserAgent, 'firefox') !== false) || (strpos($strUserAgent, 'iceweasel') !== false)) {
				// Firefox
				Application::$BrowserType = BrowserType::Firefox;

			} else if (strpos($strUserAgent, 'safari') !== false) {
				// Safari
				Application::$BrowserType = BrowserType::Safari;

			} else if (strpos($strUserAgent, 'chrome') !== false) {
				// Chrome
				Application::$BrowserType = BrowserType::Chrome;

			} else {
				// Unsupported
				Application::$BrowserType = BrowserType::Unsupported;
			}
		}
	}

	/**
	 * Used in conjunction with Application::$Browser for browser detection
	 * @access public
	 * @static
	 * @return void
	 */
	public static function IsBrowser($intBrowserType) {
		return ($intBrowserType & self::$BrowserType);
	}

	/**
	 * This will redirect the user to a new web location. This can be a relative or absolute web path, or it
	 * can be an entire URL.
	 * @access public
	 * @static
	 * @return void
	 */
	public static function Redirect($strLocation) {
		/**
	 	 * This will set Layout to None which prevents the rendering of a template
	 	 */
		$_CONTROLLER->Layout = Layout::None;

		ob_clean();

		if (array_key_exists('DOCUMENT_ROOT', $_SERVER) && ($_SERVER['DOCUMENT_ROOT'])) {
			header(sprintf('Location: %s', $strLocation));
		} else {
			printf('<script type="text/javascript">document.location = "%s";</script>', $strLocation);
		}
		exit();
	}

	/**
	 * This will set NoRender which prevents the rendering of a 'page'
	 * This is automatically called during a redirect
	 * @access public
	 * @static
	 * @return void
	 * @deprecated
	 */
	/*
	public static function SetNoRender() {
		$_CONTROLLER->SetNoRender;
	}
	*/

	/**
	 * This will set the layout
	 * @access public
	 * @static
	 * @return void
	 * @deprecated
	 */
	/*
	public static function SetLayout($strLayout) {
		self::$Layout = $strLayout;
	}
	*/

	/**
	 * This will close the window. It will immediately end processing of the rest of the script.
	 * @access public
	 * @static
	 * @return void
	 */
	public static function CloseWindow() {
		ob_clean();

		print('<script type="text/javascript">window.close();</script>');
	}

	/**
	 * Gets the value of the item $strItem. Will return NULL if it doesn't exist.
	 * @access public
	 * @static
	 * @param string $strItem
	 * @return string
	 */
	public static function Request($strItem) {
		$arrParams = RoutingHandler::GetParams();
		if (is_null($strItem)) {
			return $arrParams;
		}
		if (array_key_exists($strItem, $arrParams)) {
			return $arrParams[$strItem];
		} else {
			return null;
		}
	}

	/**
	 * Global/Central HtmlEntities command to perform the PHP equivalent of htmlentities.
	 * @access public
	 * @static
	 * @param string $strText
	 * @return string
	 */
	public static function HtmlEntities($strText) {
		return htmlentities($strText, ENT_COMPAT, self::$EncodingType);
	}

	/**
	 * Logs messages to the error log if __LOG_VERBOSE__ is set to true
	 * @access public
	 * @static
	 * @param mixed $mixValue any message or value
	 * return void
	 */
	public static function Log($mixValue) {
		if (__LOG_VERBOSE__) {
			if (is_object($mixValue) || is_array($mixValue)) {
				error_log(print_r($mixValue, true));
			} else {
				error_log($mixValue);
			}
		}
	}
}
?>

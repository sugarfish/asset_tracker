<?php
/**
 * File: UrlHandler.class.php
 * Created on: Sun Sep 19 12:48 CDT 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Helper class providing methods to build URL's and parameter lists
 *
 * @package org.sugarfish.core
 * @name UrlHandler
 */

class UrlHandler {

	/**
	* This faux constructor method throws a caller exception;
	* the UrlHandler object should never be instantiated
	* @final
	* @return void
	*/
	public final function __construct() {
		try {
			$strMessage = "UrlHandler should never be instantiated. All methods and variables are publically statically accessible";
			throw new CustomException(Exceptions::STATIC_CLASS_INSTANTIATION, $strMessage);
		} catch (CustomException $e) {
			print $e;
			exit;
		}
	}

	/**
	 * Takes an array of arguments and transforms them into a usable URL
	 *
	 * Usage:
	 *     UrlHandler::BuildUrl(
	 *			array(
	 *				'module'  	 => $strModule,
	 *				'controller' => $strController,
	 *				'action' 	 => $strAction
	 *			)
	 *		)
	 *
	 * @access public
	 * @static
	 * @param array $arrArgs
	 * @return string $strUrl
	 */
	public static function BuildUrl($arrArgs = null) {
		if (
			!array_key_exists('module', $arrArgs) ||
			!array_key_exists('controller', $arrArgs) ||
			!array_key_exists('action', $arrArgs)
		) {
			throw new CustomException(Exceptions::ERROR, 'URL must contain a Module, Controller and Action');
		}

		$strUrl = sprintf('/%s/%s/%s', $arrArgs['module'], $arrArgs['controller'], $arrArgs['action']);

		if (array_key_exists('params', $arrArgs)) {
			$strUrl .= self::BuildParams($arrArgs['params']);
		}

		return $strUrl;
	}

	/**
	 * Takes an array of arguments and transforms them into a list of parameters for inclusion in a URL
	 * @access protected
	 * @static
	 * @param array $arrParams
	 * @return string $strUrl
	 */
	protected static function BuildParams($arrParams) {
		$strUrl = null;
		foreach ($arrParams as $strName => $strValue) {
			$strUrl .= sprintf('/%s/%s', $strName, $strValue);
		}

		return $strUrl;
	}
}
?>

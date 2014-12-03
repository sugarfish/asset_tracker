<?php
/**
 * File: Router.class.php
 * Created on: Thu Mar 28 11:31 PDT 2013
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Handles custom routing
 *
 * @package org.sugarfish.core
 * @name Router
 */

class Router {

	/**
     * Array that holds custom-defined routes
     * @access private
     * @var object $arrRoutes
     */
	private static $arrRoutes = array();

	/**
     * Adds a custom route to $arrRoutes
     * @access public
     * @param string $strName
     * @param mixed $mixRoute
     * @return void
     */
	public static function AddRoute($strName, $mixRoute) {
		try {
			if (!array_key_exists($strName, self::$arrRoutes)) {
				self::$arrRoutes[$strName] = $mixRoute;
			} else {
				throw new CustomException(Exceptions::ERROR, sprintf("Duplicate custom route name defined: '%s'", $strName));
			}
		} catch(CustomException $e) {
			header(HttpResponse::NOT_FOUND);
			print $e;
			exit;
		}
	}

	/**
	 * Public getter method for private/protected properties 
	 * @access public
	 * @return mixed
	 */
	public function __get($strName) {
		switch ($strName) {
			case 'Routes':
				return self::$arrRoutes;
		}
	}
}
?>

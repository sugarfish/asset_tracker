<?php
/**
 * File: RequestHandler.class.php
 * Created on: Thu Aug 26 00:13 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Handles request parameters and adds them to the list of controller parameters
 *
 * @package org.sugarfish.core
 * @name RequestHandler
 */

class RequestHandler {

	/**
	 * Append all GET and POST parameters to this RequestHandler
	 * @access public
	 * @return void
	 */
	public function Append() {

		if (count((array)$_GET) > 0) {
			foreach ($_GET as $strParam => $mixValue) {
				$this->$strParam = $mixValue;
			}
		}

		if (count((array)$_POST) > 0) {
			foreach ($_POST as $strParam => $mixValue) {
				$this->$strParam = $mixValue;
			}
		}
	}
}
?>

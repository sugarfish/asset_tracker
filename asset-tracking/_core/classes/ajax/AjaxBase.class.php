<?php
/**
 * File: AjaxBase.class.php
 * Created on: Mon Jul 3 21:50 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Base class for AJAX calls
 *
 * This class provides base functionality for all extended AJAX classes
 * 
 * @package org.sugarfish.core
 * @name AjaxBase
 */

class AjaxBase {

	/**
	 * Class constructor
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->Initialize();

		// is this request a POST or GET?
		$arrArgs = empty($_GET)?$_POST:$_GET;

		if (array_key_exists('mode', $arrArgs)) {
			// get the contained method
			$strMethod = $arrArgs['mode'];

			// remove the 'mode'
			unset($arrArgs['mode']);

			// run the contained method
			//Application::Log(sprintf('Running AJAX (%s): Mode: %s; Arguments:<pre>%s</pre>', get_class($this), $strMethod, print_r($arrArgs, true)));
			$this->$strMethod($arrArgs);
		}
	}

	/**
	 * Placeholder method, overridden in extended classes where it's intended to
	 * provide a place to instantiate models, etc.
	 * @access protected
	 * @return void
	 */
	protected function Initialize() {}
}
?>

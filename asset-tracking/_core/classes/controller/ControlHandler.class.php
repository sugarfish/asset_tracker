<?php
/**
 * File: ControlHandler.class.php
 * Created on: Sun Jul 18 11:10 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Handles properties and appends them to the list of controls
 *
 * @package org.sugarfish.core
 * @name ControlHandler
 */

class ControlHandler {

	/**
	 * Append control to this ControlHandler;
	 * uses provided Control ID or selects the next auto-generated ID provided by the controller
	 * @param $mixControl
	 * @param string $strControlId
	 * @return ControlHandler object
	 */
	public function Append($mixControl, $strControlId = null) {
		if (is_null($strControlId)) {
			// we need to create a sequential control id
			$strControlId = sprintf('c%s', $_CONTROLLER->ControlId);
			$_CONTROLLER->IncrementControlId;
		} else {
			// check for non-alphanumeric id
			try {
				$strMatches = array();

				/**
				 * allowed characters:
				 *     A-Z
				 *     a-z
				 *     0-9
				 *     _ (underscore)
				 */
				$strPattern = '/[A-Za-z0-9_]*/';
				preg_match($strPattern, $strControlId, $strMatches);
				if (!(count($strMatches) && ($strMatches[0] == $strControlId))) {
					throw new CustomException(Exceptions::ILLEGAL_CONTROL_ID, sprintf("Illagel Control ID: '%s'; valid characters are A-Z, a-z, 0-9, or _ (underscore)", $strControlId));
				}
			} catch (CustomException $e) {
				print $e;
				exit;
			}

			// check if id exists already or if it resembles an auto-generated id
			try {
				if (preg_match("/c([0-9]+)/", $strControlId)) {
					throw new CustomException(Exceptions::ILLEGAL_CONTROL_ID, sprintf("Illegal Control ID: '%s'; resembles auto-generated Control ID", $strControlId));
				}
				if (isset($this->$strControlId)) {
					throw new CustomException(Exceptions::CONTROL_EXISTS, sprintf("Control '%s' already exists", $strControlId));
				}
			} catch (CustomException $e) {
				print $e;
				exit;
			}
		}

		$this->$strControlId = $mixControl;

		return $this;
	}
}
?>

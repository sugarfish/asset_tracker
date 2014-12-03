<?php
/**
 * File: FooterBase.class.php
 * Created on: Sun Jul 25 23:23 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Base class for page footer
 *
 * Renders post-body elements
 *
 * @package org.sugarfish.core
 * @name FooterBase
 */

class FooterBase implements TemplateManager {

	/**
	 * Boolean, do we have an instance?
	 *
	 * @access protected
	 * @var boolean $blnInstance
	 */
	protected static $blnInstance;

	/**
	 * Class constructor
	 * @access public
	 * @return void
	 */
	public function __construct() {
		if (!self::$blnInstance) {
			self::$blnInstance = true;
		} else {
			try {
				$strMessage = "Header can only be instantiated once";
				throw new CustomException(Exceptions::MULTIPLE_SINGLETON_INSTANTIATION, $strMessage);
			} catch (CustomException $e) {
				print $e;
				exit;
			}
		}
	}

	/**
	 * Renders the footer on the page
	 * @access public
	 * @return void
	 */
	public function Render() {
		$strOutput = $this->GetFooterContent();
		$strOutput .= "</body>\n";
		$strOutput .= "</html>";

		print $strOutput;
	}

	/**
	 * Overridden method to generate footer content
	 * @access protected
	 * @return void
	 */
	protected function GetFooterContent() {}
}
?>

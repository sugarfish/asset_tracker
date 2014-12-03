<?php
/**
 * File: ControllerBase.class.php
 * Created on: Sat Jul 17 01:06 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Base class that provides functionality for the rendering of the view
 *
 * @package org.sugarfish.core
 * @name ControllerBase
 */

abstract class ControllerBase implements TemplateManager {

	/**
	 * Storage object for all properties to be sent to the template
	 * @access public
	 * @var object $_CONTROL
	 */
	public $_CONTROL;

	/**
	 * Storage object for all parameters to be sent to the template
	 * @access public
	 * @var object $_PARAM
	 */
	public $_PARAM;

	/**
	 * Determines which layout will be used during rendering
	 * @access private
	 * @var string $strLayout
	 */
	private $strLayout = Layout::NotSet;

	/**
	 * Property to store the current custom template; otherwise the current Action is used
	 * to determine the template file to be used
	 * @access private
	 * @var string $strTemplate
	 */
	private $strTemplate;

	/**
	 * Stores the numeric value of the next auto-generated Control ID to be used
	 * @access private
	 * @var integer $intControlId
	 */
	private $intControlId = 0;

	/**
	 * Class constructor
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_CONTROL = new ControlHandler;
		$this->_PARAM = new RequestHandler;
		$this->Initialize();
	}

	/**
	 * Placeholder method, overridden in extended classes where it's intended to provide
	 * a place to instantiate models, headers, footers, and other template-wide properties
	 * @access protected
	 * @return void
	 */
	protected function Initialize() {
		try {
			throw new CustomException(Exceptions::INVALID_INSTANTIATION_ATTEMPT, "ControllerBase should never be initialized directly");
		} catch (CustomException $e) {
			print $e;
			exit;
		}
	}

	/**
	 * Placeholder method, overridden in the Controller class where it's specific
	 * functionality is used to render the page
	 * @access public
	 * @return void
	 */
	public function Render() {}

	/**
	 * Gather up any controls and pass them onto the template; ensures that at least an empty object exists
	 * @access protected
	 * @return object $objControl
	 */
	protected function GetControls() {
		/**
		 * First, we test the _CONTROL object to see if it has params;
		 * if it does then we pass it on to '$_CONTROL';
		 * this means we can use it without referencing '$this'
		 * all over the template;
		 * then we unset the original object, freeing some memory
		 */
		if (count((array)$this->_CONTROL) > 0) {
			$objControl = $this->_CONTROL;
		}
		unset($this->_CONTROL);

		return $objControl;
	}

	/**
	 * Gather up any parameters and pass them onto the template; ensures that at least an empty object exists
	 * @access protected
	 * @return object $objParam
	 */
	protected function GetParams() {
		/**
		 * First, we test the _PARAM object to see if it has params;
		 * if it does then we pass it on to '$_PARAM';
		 * this means we can use it without referencing '$this'
		 * all over the template;
		 * then we unset the original object, freeing some memory
		 */
		$this->_PARAM->Append();

		if (count((array)$this->_PARAM) > 0) {
			$objParam = $this->_PARAM;
		}
		unset($this->_PARAM);

		return $objParam;
	}

	/**
	 * Handle any calls to undefined actions; is intended to be overridden in controller classes
	 * to provide nice error pages
	 * @access protected
	 * @param string $strController
	 * @param string $strAction
	 * @return object $objControl
	 */
	public function ActionErrorHandler($strController, $strAction) {
		throw new CustomException(Exceptions::ERROR, sprintf("Call to undefined action: '%s->%s()'", $strController, $strAction));
	}

	/**
	 * Set $strLayout
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetLayout($strValue) {
		$this->strLayout = $strValue;
		return $this;
	}

	/**
	 * Set $strTemplate
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetTemplate($strValue) {
		$this->strTemplate = $strValue;
		return $this;
	}

	/**
	 * Set $blnNoRender
	 * @access public
	 * @return object $this
	 * @deprecated
	 */
	/*
	public function SetNoRender() {
		$this->blnNoRender = true;
		return $this;
	}
	*/

	/**
	 * Increment $intControlId
	 * @access public
	 * @return object $this
	 */
	public function IncrementControlId() {
		$this->intControlId++;
		return $this;
	}

	/**
	 * Public setter method for private/protected properties 
	 * @access public
	 * @return void
	 */
	public function __set($strName, $strValue) {
		switch ($strName) {
			case 'Layout':
				$this->strLayout = $strValue;
				break;
			case 'Template':
				$this->strTemplate = $strValue;
				break;
			/**
			 * @deprecated
			 */
			/*
			case 'NoRender':
				$this->blnNoRender = true;
				break;
			*/
			default:
				$this->$strName = $strValue;
		}
	}

	/**
	 * Public getter method for private/protected properties 
	 * @access public
	 * @return mixed
	 */
	public function __get($strName) {
		switch ($strName) {
			case 'Layout':
				return $this->strLayout;
			case 'Template':
				return $this->strTemplate;
			/**
			 * @deprecated
			 */
			/*
			case 'NoRender':
				return $this->blnNoRender;
			*/
			case 'ControlId`':
				return $this->intControlId;
		}
	}
}
?>

<?php
/**
 * File: Controller.class.php
 * Created on: Wed Sep 8 17:19 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Extended class that provides functionality for the rendering of the view
 *
 * @package org.sugarfish.core
 * @name Controller
 */

class Controller extends ControllerBase {

	/**
	 * Placeholder method, overridden in extended classes where it's intended to provide
	 * a place to instantiate models, headers, footers, and other template-wide properties
	 * @access protected
	 * @return void
	 */
	protected function Initialize() {}

	/**
	 * Main rendering method; determines the template to be used (or none)
	 * and renders the page
	 * @access public
	 * @return void
	 */
	public function Render() {
		$_CONTROL = $this->GetControls();
		$_PARAM = $this->GetParams();

		if (empty($_CONTROL)) {
			unset($_CONTROL);
		}

		if (empty($_PARAM)) {
			unset($_PARAM);
		}

		$arrDefinedVars = get_defined_vars();
		if (!empty($arrDefinedVars)) {
			// output the contents of the ControlHandler object to the error log
			//Application::Log(String::ArrayFormat($arrDefinedVars));
		}

		// find the template file
		/**
		 * these lines replace the old re-parse nonsense;
		 * basically, the application was written to have static access to these properties,
		 * but I strangely forgot this and parsed the URL again. D'oh!
		 */
		$strModule = RoutingHandler::GetModule();
		$strTemplate = str_replace('Action', '', RoutingHandler::GetAction());

		if ($strModule == __DEFAULT_MODULE__ && is_null($this->Template)) {
			$strTemplateFile = sprintf('%s/%s/templates/index.tpl.php', __DOCROOT__, __DEFAULT_MODULE__);
		} else {
			if (is_null($this->Template)) {
				$strTemplateFile = sprintf('%s/%s/templates/%s.tpl.php', __DOCROOT__, $strModule, $strTemplate);
			} else {
				$strTemplateFile = sprintf('%s/%s/templates/%s.tpl.php', __DOCROOT__, $strModule, $this->Template);
			}
		}

		// if we're redirecting then we're not rendering a template, even if there is one available
		if ($this->Layout != Layout::None) {
			try {
				if (file_exists($strTemplateFile)) {
					ob_start();
					require_once($strTemplateFile);
					$strEvaluatedTemplate = ob_get_contents();
					ob_end_clean();

					print $strEvaluatedTemplate;
				} else {
					// if the template file is missing, throw an exception
					throw new CustomException(Exceptions::TEMPLATE_NOT_FOUND, sprintf('Template: "%s" does not exist', $strTemplateFile));
				}
			} catch (CustomException $e) {
				print $e;
				exit;
			}
		}
	}
}
?>

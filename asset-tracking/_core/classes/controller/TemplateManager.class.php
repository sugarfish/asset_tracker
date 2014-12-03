<?php
/**
 * File: TemplateManager.class.php
 * Created on: Mon Jun 1 23:00 CST 2009
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Interface providing the Render method
 *
 * @package org.sugarfish.core
 * @name TemplateManager
 */

interface TemplateManager {

	/**
	 * Placeholder method, overridden in extended classes
	 * @access public
	 * @return void
	 */
	public function Render();
} 
?>

<?php
/**
 * File: HeaderBase.class.php
 * Created on: Mon Sep 6 22:58 CST 2010
 *
 * @author Ian Atkin
 *
 * @license http://www.gnu.org/copyleft/lesser.html
 */

 /**
 * Base class for page header
 *
 * Renders pre-body elements
 * 
 * @package org.sugarfish.core
 * @name HeaderBase
 */

class HeaderBase implements TemplateManager {

	/**
	 * Boolean, do we have an instance?
	 * @access protected
	 * @var boolean $blnInstance
	 */
	protected static $blnInstance;

	/**
	 * Stores the current PageId; PageId is used as a key for the loading of a set of JavaScript files
	 * @access protected
	 * @var string $strPageId
	 */
	protected $strPageId = 'NotSet';

	/**
	 * Stores the current page title
	 * @access protected
	 * @var string $strPageTitle
	 */
	protected $strPageTitle;

	/**
	 * Stores the current meta-description
	 * @access protected
	 * @var string $strMetaDesc
	 */
	protected $strMetaDesc;

	/**
	 * Stores the current meta-keywords
	 * @access protected
	 * @var string $strMetaKeywords
	 */
	protected $strMetaKeywords;

	/**
	 * Stores the current XML menu file
	 * @access protected
	 * @var string $strXmlMenuFile
	 */
	protected $strXmlMenuFile;
	
	/**
	 * Stores the current XML sub-menu file
	 * @access protected
	 * @var string $strXmlSubMenuFile
	 */
	protected $strXmlSubMenuFile;
	
	/**
	 * Used by the Menu class to determine the current menu item
	 * @access protected
	 * @var string $strPageKey
	 */
	protected $strPageKey;
	
	/**
	 * Used by the Menu class to determine the current sub-menu item
	 * @access protected
	 * @var string $strSubPageKey
	 */
	protected $strSubPageKey;

	/**
	 * JavaScriptMode allows a set of pages to share one set of script files
	 * @access protected
	 * @var string $strJavaScriptMode
	 */
	protected $strJavaScriptMode;
	
	/**
	 * Stores an XML object
	 * @access protected
	 * @var object $objXml
	 */
	protected $objXml;
	
	/**
	 * Stores CSS include tags
	 * @access protected
	 * @var string $strCss
	 */
	protected $strCss;

	/**
	 * Stores JavaScript include tags
	 * @access protected
	 * @var string $strJavaScript
	 */
	protected $strJavaScript;
	
	/**
	 * Stores a JavaScript snippet for inclusion on a page
	 * @access protected
	 * @var string $strInlineJavaScript
	 */
	protected $strInlineJavaScript;

	/**
	 * Stores the name of a CSS XML file
	 * @access protected
	 * @var string $strCssXmlFile
	 */
	protected $strCssXmlFile = 'css.xml';

	/**
	 * Stores the name of a JavaScript XML file
	 * @access protected
	 * @var string $strJavaScriptXmlFile
	 */
	protected $strJavaScriptXmlFile = 'js.xml';

	/**
	 * Stores a body tag snippet; can be used to insert additional lines before the template is rendered
	 * @access protected
	 * @var string $strBodyTag
	 */
	protected $strBodyTag = null;

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
	 * Renders the header on the page
	 * @access public
	 * @return void
	 */
	public function Render() {
		try {
			if (!empty($this->strPageId)) {

				// construct CSS
				if (!is_null($this->strCssXmlFile)) {
					$this->GetCss();
				}

				// construct JavaScript
				if (!is_null($this->strJavaScriptXmlFile)) {
					$this->GetJavaScript();
				}

				$strOutput = $this->GetDocType();

				if (!Application::$SecurePage) {
					$strOutput .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n\n";
				} else {
					$strOutput .= "<html>\n\n";
				}

				$strOutput .= $this->GetHeadTag();

				if (!empty($this->strBodyTag)) {
					$strOutput .= "<body>\n\n";
				} else {
					$strOutput .= $this->strBodyTag;
				}

				$strOutput .= $this->GetHeaderContent();

				print $strOutput;
			} else {
				throw new CustomException(Exceptions::PAGE_ID_NOT_SET, "Page ID not set when Render() called");
			}
		} catch (CustomException $e) {
			print $e;
			exit;
		}
	}

	/**
	 * Define DOCTYPE and any other text to include before <html> tag
	 * @access protected
	 * @return string $strOutput
	 */
	protected function GetDocType() {
		$strOutput = "<!DOCTYPE html>\n\n";

		return $strOutput;
	}

	/**
	 * Define content of <head> tag
	 * @access protected
	 * @return string $strOutput
	 */
	protected function GetHeadTag() {
		$strOutput = "<head>\n";

		if (!is_null($this->strPageTitle)) {
			$strOutput .= sprintf("\t<title>!!!%s</title>\n", $this->strPageTitle);
		}

		$strOutput .= sprintf("\t<meta http-equiv=\"content-type\" content=\"text/html; %s/\">\n", Application::$EncodingType);

		if (!Application::$SecurePage) {
			$strOutput .= sprintf("\t<link rel=\"shortcut icon\" href=\"%s/favicon.ico\" />\n", __DOMAIN__);
		} else {
			$strOutput .= sprintf("\t<link rel=\"shortcut icon\" href=\"%s/favicon.ico\" />", __SECURE_DOMAIN__);
		}

		$strOutput .= $this->strCss;
		$strOutput .= $this->strJavaScript;

		if (!is_null($this->strInlineJavaScript)) {
			$strOutput .= sprintf("%s\n", $this->strInlineJavaScript);
		}

		$strOutput .= "</head>\n\n";

		return $strOutput;
	}

	/**
	 * Insert CSS tags based on CssMode or PageId
	 * @access protected
	 * @return void
	 */
	protected function GetCss() {
		$strFilename = sprintf('%s/%s', __XML__, $this->strCssXmlFile);
		if (!file_exists($strFilename)) {
			try {
				$strMessage = sprintf('Warning: CSS XML file (%s) not found.', $strFilename);
				Application::Log($strMessage);
			} catch (CustomException $e) {
				print $e;
				exit;
			}
			return;
		}
		$this->objXml = simplexml_load_file($strFilename);

		$this->ParseXml('global', 'css');

		// if strCssMode is null then load the CSS off of the page name
		$strPageId = is_null($this->strCssMode)?$this->strPageId:$this->strCssMode;
		$this->ParseXml($strPageId, 'css');
	}

	/**
	 * Insert JavaScript tags based on JavaScriptMode or PageId
	 * @access protected
	 * @return void
	 */
	protected function GetJavaScript() {
		$strFilename = sprintf('%s/%s', __XML__, $this->strJavaScriptXmlFile);
		if (!file_exists($strFilename)) {
			try {
				$strMessage = sprintf('Warning: JavaScript XML file (%s) not found.', $strFilename);
				Application::Log($strMessage);
			} catch (CustomException $e) {
				print $e;
				exit;
			}
			return;
		}
		$this->objXml = simplexml_load_file($strFilename);

		$this->ParseXml('global', 'js');

		// if strJavaScriptMode is null then load the JavaScript off of the page name
		$strPageId = is_null($this->strJavaScriptMode)?$this->strPageId:$this->strJavaScriptMode;
		$this->ParseXml($strPageId, 'js');
	}

	/**
	 * Determine which CSS and/or JavaScript files to include
	 * @access protected
	 * @param string $strSearch
	 * @return void
	 */
	protected function ParseXml($strSearch, $strFileType) {
		$arrResult = $this->objXml->xpath($strSearch . '/file');
		while(list( , $strFile) = each($arrResult)) {
			foreach ($strFile->attributes() as $keyFile => $strAttr) {
				if ($keyFile == 'type') {
					$strType = $strAttr;
				}
				$strMedia = 'all';
				if ($keyFile == 'media') {
					$strMedia = $strAttr;
				}
			}

			switch ($strFileType) {
				case 'css':
					switch ($strType) {
						case 'internal':
							$this->strCss .= sprintf("\t<link rel=\"stylesheet\" href=\"%s/%s?v=%s\" type=\"text/css\" media=\"%s\"/>\n", __CSS__, $strFile, __ASSET_VERSION__, $strMedia);
							break;
						case 'external':
							$this->strCss .= sprintf("\tlink rel=\"stylesheet\" href=\"%s\" type=\"text/css\" media=\"%s\"/>\n", $strFile, $strMedia);
					}
					break;
				case 'js':
					switch ($strType) {
						case 'internal':
							$this->strJavaScript .= sprintf("\t<script src=\"%s/%s?v=%s\" type=\"text/javascript\" charset=\"utf-8\"></script>\n", __JAVASCRIPT__, $strFile, __ASSET_VERSION__);
							break;
						case 'external':
							$this->strJavaScript .= sprintf("\t<script src=\"%s\" type=\"text/javascript\" charset=\"utf-8\"></script>\n", $strFile);
					}
			}
		}
	}

	/**
	 * Define header content for menus, etc. to be rendered at the top of the <body> tag
	 * @access protected
	 * @return void
	 */
	protected function GetHeaderContent() {}

	/**
	 * Set $strPageId
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetPageId($strValue) {
		$this->strPageId = $strValue;
		return $this;
	}

	/**
	 * Set $strPageTitle
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetPageTitle($strValue) {
		$this->strPageTitle = $strValue;
		return $this;
	}

	/**
	 * Set $strMetaDesc
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetMetaDesc($strValue) {
		$this->strMetaDesc = $strValue;
		return $this;
	}

	/**
	 * Set $strMetaKeywords
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetMetaKeywords($strValue) {
		$this->strMetaKeywords = $strValue;
		return $this;
	}

	/**
	 * Set $strMetaDesc
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetMenuFile($strValue) {
		$this->strXmlMenuFile = $strValue;
		return $this;
	}

	/**
	 * Set $strPageKey
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetPageKey($strValue) {
		$this->strPageKey = $strValue;
		return $this;
	}

	/**
	 * Set $strXmlSubMenuFile
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetSubMenuFile($strValue) {
		$this->strXmlSubMenuFile = $strValue;
		return $this;
	}

	/**
	 * Set $strSubPageKey
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetSubPageKey($strValue) {
		$this->strSubPageKey = $strValue;
		return $this;
	}

	/**
	 * Set $strCssMode
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetCssMode($strValue) {
		$this->strCssMode = $strValue;
		return $this;
	}

	/**
	 * Set $strJavaScriptMode
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetJavaScriptMode($strValue) {
		$this->strJavaScriptMode = $strValue;
		return $this;
	}

	/**
	 * Set $strCssXmlFile
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetCssXmlFile($strValue) {
		$this->strCssXmlFile = $strValue;
		return $this;
	}

	/**
	 * Set $strJavaScriptXmlFile
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetJavaScriptXmlFile($strValue) {
		$this->strJavaScriptXmlFile = $strValue;
		return $this;
	}

	/**
	 * Set $strInlineJavaScript
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetInlineJavaScript($strValue) {
		$this->strInlineJavaScript = $strValue;
		return $this;
	}

	/**
	 * Set $strBodyTag
	 * @access public
	 * @param string $strValue
	 * @return object $this
	 */
	public function SetBodyTag($strValue) {
		$this->strBodyTag = $strValue;
		return $this;
	}

	/**
	 * Setter function
	 * @access public
	 * @param string $strName
	 * @param mixed $mixValue
	 * @return mixed
	 */
	public function __set($strName, $mixValue) {
		switch ($strName) {
			case 'PageId':
				return ($this->strPageId = $mixValue);
			case 'PageTitle':
				return ($this->strPageTitle = $mixValue);
			case 'MetaDesc':
				return ($this->strMetaDesc = $mixValue);
			case 'MetaKeywords':
				return ($this->strMetaKeywords = $mixValue);
			case 'MenuFile':
				return ($this->strXmlMenuFile = $mixValue);
			case 'PageKey':
				return ($this->strPageKey = $mixValue);
			case 'SubMenuFile':
				return ($this->strXmlSubMenuFile = $mixValue);
			case 'SubPageKey':
				return ($this->strSubPageKey = $mixValue);
			case 'CssMode':
				return ($this->strCssMode = $mixValue);
			case 'JavaScriptMode':
				return ($this->strJavaScriptMode = $mixValue);
			case 'CssXmlFile':
				return ($this->strCssXmlFile = $mixValue);
			case 'JavaScriptXmlFile':
				return ($this->strJavaScriptXmlFile = $mixValue);
			case 'InlineJavaScript':
				return ($this->strInlineJavaScript = $mixValue);
			case 'BodyTag':
				return ($this->strBodyTag = $mixValue);
		}
	}

	public function __get($strName) {
		switch ($strName) {
			case 'PageId':
				return $this->strPageId;
			case 'PageTitle':
				return $this->strPageTitle;
			case 'MetaDesc':
				return $this->strMetaDesc;
			case 'MetaKeywords':
				return $this->strMetaKeywords;
			case 'MenuFile':
				return $this->strXmlMenuFile;
			case 'PageKey':
				return $this->strPageKey;
			case 'SubMenuFile':
				return $this->strXmlSubMenuFile;
			case 'SubPageKey':
				return $this->strSubPageKey;
			case 'CssMode':
				return $this->strCssMode;
			case 'JavaScriptMode':
				return $this->strJavaScriptMode;
			case 'CssXmlFile':
				return $this->strCssXmlFile;
			case 'JavaScriptXmlFile':
				return $this->strJavaScriptXmlFile;
			case 'InlineJavaScript':
				return $this->strInlineJavaScript;
			case 'BodyTag':
				return $this->strBodyTag;
		}
	}
}
?>

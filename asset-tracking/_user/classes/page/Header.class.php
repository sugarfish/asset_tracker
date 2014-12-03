<?php
class Header extends HeaderBase {

	public function __construct() {
		parent::__construct();

		$this->strMetaDesc = 'Make a man a fire and keep him warm for a day, set a man on fire and keep him warm for the rest of his life.';
		$this->strMetaKeywords = 'versal assets database cactus bubbles';
	}

	protected function GetDocType() {
		$strOutput = "<!DOCTYPE html>\n\n";

		return $strOutput;
	}

	protected function GetHeadTag() {
		$strOutput = "<head>\n";
		$strOutput .= sprintf("\t<title>%s</title>\n", ($this->strPageTitle == '')?'Versal':$this->strPageTitle . ' | Versal.');
		$strOutput .= sprintf("\t<meta http-equiv=\"content-type\" content=\"text/html; charset=%s\" />\n", Application::$EncodingType);
		$strOutput .= "\t<meta http-equiv=\"imagetoolbar\" content=\"false\" />\n";
		$strOutput .= sprintf("\t<meta name=\"description\" content=\"%s\" />\n", isset($this->strMetaDesc)?$this->strMetaDesc:'description');
		$strOutput .= sprintf("\t<meta name=\"keywords\" content=\"%s\" />\n", isset($this->strMetaKeywords)?$this->strMetaKeywords:'keywords');
		$strOutput .= "\t<meta name=\"author\" content=\"Ian Atkin\" />\n";
		$strOutput .= "\t<link rev=\"made\" href=\"mailto:ian@versal.com\" />\n";
		$strOutput .= "\t<link rel=\"home\" href=\"http://versal.com/\" />\n";

		$strOutput .= "\t<link rel=\"shortcut icon\" href=\"/favicon.ico\" />\n";

		$strOutput .= $this->strCss;
		$strOutput .= $this->strJavaScript;

		if (!is_null($this->strInlineJavaScript)) {
                        $strOutput .= sprintf("%s\n", $this->strInlineJavaScript);
                }

		$strOutput .= "</head>\n\n";
        
        $strOutput .= "<body>\n\n";

		return $strOutput;
	}
}
?>

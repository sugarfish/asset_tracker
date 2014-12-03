<?php
ini_set('include_path', '.:/var/www/asset-track/includes');
require_once "prepend.inc.php";

class AjaxTest extends AjaxBase {

	protected function Initialize() {
		$this->objTestModel = new TestModel;
	}

	protected function test($arrArgs) {
		//print json_encode(array("strReturn" => $arrArgs['str'], "strCrap" => $arrArgs['crap']));
		print json_encode(array("strReturn" => $arrArgs['str']));
		/* or plain text
		print $arrArgs['str'];
		*/
	}

	protected function getSerialNumbers($arrArgs) {
		Application::Log(json_encode($this->objTestModel->GetSerialNumbers($arrArgs['str'])));
		print json_encode($this->objTestModel->GetSerialNumbers($arrArgs['str']));
	}

	protected function getEmployees($arrArgs) {
		Application::Log(json_encode($this->objTestModel->GetEmployees($arrArgs['str'])));
		print json_encode($this->objTestModel->GetEmployees($arrArgs['str']));
	}
}

new AjaxTest;
?>

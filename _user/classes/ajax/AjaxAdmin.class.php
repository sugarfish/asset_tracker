<?php
ini_set('include_path', '.:/var/www/asset-track/includes');
require_once "prepend.inc.php";

class AjaxAdmin extends AjaxBase {

	protected function Initialize() {
		$this->objAdminModel = new AdminModel;
	}

/*
	protected function getDepartmentSelectionList() {
		print json_encode($this->objAdminModel->GetDepartmentList());
	}
*/

	protected function getAssets($arrArgs) {
		print json_encode($this->objAdminModel->GetAssets($arrArgs['search']));
	}

	protected function getEmployees($arrArgs) {
		print json_encode($this->objAdminModel->GetEmployees($arrArgs['search']));
	}

	protected function getEmployeeDetails($arrArgs) {
		print json_encode($this->objAdminModel->GetEmployeeDetails($arrArgs['id']));
	}

	protected function getEmployeeList() {
		print json_encode($this->objAdminModel->GetEmployeeList());
	}

	protected function getDepartmentList() {
		print json_encode($this->objAdminModel->GetDepartmentList());
	}

	protected function getAssetDetails($arrArgs) {
		print json_encode($this->objAdminModel->GetAssetDetails($arrArgs['id']));
	}

	protected function getTypeList() {
		print json_encode($this->objAdminModel->GetTypeList());
	}

	protected function getGroupList() {
		print json_encode($this->objAdminModel->GetGroupList());
	}

	protected function getStatusList() {
		print json_encode($this->objAdminModel->GetStatusList());
	}

	protected function getDepartments($arrArgs) {
		print json_encode($this->objAdminModel->GetDepartments($arrArgs['id']));
	}

	protected function getGroups($arrArgs) {
		print json_encode($this->objAdminModel->GetGroups($arrArgs['id']));
	}

	protected function getAssetHistory($arrArgs) {
		print json_encode($this->objAdminModel->GetAssetHistory($arrArgs['id']));
	}

	protected function checkForExistingSerialNumber($arrArgs) {
		if ($this->objAdminModel->CheckForExistingSerialNumber($arrArgs['serial_number'])) {
			print json_encode('duplicate');
		} else {
			print json_encode('ok');
		}
	}

	protected function getAssetData($arrArgs) {
		print json_encode($this->objAdminModel->GetAssetData($arrArgs['id']));
	}

	protected function getInventoryData() {
		print json_encode($this->objAdminModel->GetInventoryData());
	}

	protected function deleteAsset($arrArgs) {
		$this->objAdminModel->DeleteAsset($arrArgs['id']);
	}

	protected function saveAsset($arrArgs) {
		print json_encode($this->objAdminModel->SaveAsset($arrArgs));
	}

	protected function saveEmployee($arrArgs) {
		print json_encode($this->objAdminModel->SaveEmployee($arrArgs));
	}

	protected function getNumberOfAssetsAndUsers() {
		print json_encode(
			array(
				'assets' => $this->objAdminModel->GetNumberOfAssets(),
				'assets_assigned' => $this->objAdminModel->GetNumberOfAssignedAssets(),
				'assets_inventory' => $this->objAdminModel->GetNumberOfInventoryAssets(),
				'users' => $this->objAdminModel->GetNumberOfUsers()
			)
		);
	}

	protected function getModelInfo($arrArgs) {
		// http://everymac.com/ultimate-mac-lookup/

    	$strUrl = sprintf('http://everymac.com/ultimate-mac-lookup/?search_keywords=%s', $arrArgs['last_three']);

		$hdlCurl = curl_init();

		curl_setopt($hdlCurl, CURLOPT_URL, $strUrl);
		curl_setopt($hdlCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($hdlCurl, CURLOPT_FOLLOWLOCATION, true);

		$strResult = curl_exec($hdlCurl);

		$objDOM = new DOMDocument;
   		@$objDOM->loadHTML($strResult);

		$objXpath = new DomXPath($objDOM);
		$strClass = "detail_title";
		$objNodes = $objXpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $strClass ')]");

		$arrModels = array();

		//$i = 1;
		foreach ($objNodes as $objItem) {
			//printf('<pre>%s: %s</pre><hr/>', $i, trim($objItem->nodeValue));
			//$i++;
			if (!in_array(trim($objItem->nodeValue), $arrModels)) {
				array_push($arrModels, trim($objItem->nodeValue));
			}
		}

		print json_encode(array('model_info' => $arrModels));

		curl_close($hdlCurl);
    }

/* version 1: sucked
	protected function getModelInfo($arrArgs) {
    	$strUrl = sprintf('http://everymac.com/ultimate-mac-lookup/?search_keywords=%s', $arrArgs['last_three']);

		$hdlCurl = curl_init();

		curl_setopt($hdlCurl, CURLOPT_URL, $strUrl);
		curl_setopt($hdlCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($hdlCurl, CURLOPT_FOLLOWLOCATION, true);

		$strResult = curl_exec($hdlCurl);

		$objDOM = new DOMDocument;
   		@$objDOM->loadHTML($strResult);

		$objTDs = $objDOM->getElementsByTagName('td');

		$strDetails = $objTDs->item(0)->nodeValue;

		if (strlen($strDetails) == 2) {
			$strDetails = '?';
		}

		print json_encode(array('model_info' => $strDetails));

		curl_close($hdlCurl);
    }
*/

	protected function getAcl() {
		session_start();
		print json_encode(array('acl' => $_SESSION['arrUser']['type']));
	}
}

new AjaxAdmin;
?>

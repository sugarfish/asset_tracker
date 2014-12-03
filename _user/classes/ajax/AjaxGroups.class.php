<?php
ini_set('include_path', '.:/var/www/asset-track/includes');
require_once "prepend.inc.php";

class AjaxGroups extends AjaxBase {

	protected function Initialize() {
		$this->objGroupsModel = new GroupsModel;
	}

	protected function getGroups($arrArgs) {
		print json_encode($this->objGroupsModel->GetGroups($arrArgs['firstname'], $arrArgs['lastname'], $arrArgs['group_name'], $arrArgs['group_email_address']));
	}
}

new AjaxGroups;
?>

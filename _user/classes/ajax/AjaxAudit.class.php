<?php
ini_set('include_path', '.:/var/www/asset-track/includes');
require_once "prepend.inc.php";

class AjaxAudit extends AjaxBase {

	protected function Initialize() {
		$this->objAuditModel = new AuditModel;
	}

/*
	protected function getDepartmentSelectionList() {
		print json_encode($this->objAdminModel->GetDepartmentList());
	}
*/

	protected function getAssignedNotAssigned($arrArgs) {
		print json_encode($this->objAuditModel->GetAssignedNotAssigned());
	}

	protected function getDuplicateAssignments($arrArgs) {
		print json_encode($this->objAuditModel->GetDuplicateAssignments());
	}

	protected function getExitsWithAssets($arrArgs) {
		print json_encode($this->objAuditModel->GetExitsWithAssets());
	}

	protected function getEmployeesWithMultipleComputingDevices($arrArgs) {
		print json_encode($this->objAuditModel->GetEmployeesWithMultipleComputingDevices());
	}

	protected function getAcl() {
		session_start();
		print json_encode(array('acl' => $_SESSION['arrUser']['type']));
	}
}

new AjaxAudit;
?>

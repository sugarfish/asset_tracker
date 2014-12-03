<?php
class AuditModel extends ModelBase {

	private $objDb;

	public function __construct() {
		$this->objDb = ModelBase::getInstance();
	}

	/**
	 * assets that have status 'assigned' but aren't actually
	 * connected to an employee or department
	 */
	public function GetAssignedNotAssigned() {
		$objStmt = $this->objDb->prepare("
			SELECT a.asset_id, a.serial_number FROM asset AS a
			WHERE (
				a.asset_id NOT IN (SELECT ea.asset_id FROM employee_asset AS ea)
				AND a.asset_id NOT IN (SELECT da.asset_id FROM department_asset AS da)
			)
			AND a.status_id = 1
			ORDER BY a.asset_id;
		");
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strSerialNumber);

		$arrAssets = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['asset_id'] = $intAssetId;
			$arrRow['serial_number'] = $strSerialNumber;
			array_push($arrAssets, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrAssets;
	}

	/**
	 * assets that have are assigned to more
	 * than one employee/department
	 */
	public function GetDuplicateAssignments() {
		$objStmt = $this->objDb->prepare("
			SELECT a.asset_id, a.serial_number, ea.employee_id, da.department_id FROM asset AS a
			JOIN employee_asset AS ea ON a.asset_id = ea.asset_id
			JOIN department_asset AS da ON a.asset_id = da.asset_id
			WHERE (
				a.asset_id IN (SELECT ea.asset_id FROM employee_asset AS ea)
				AND a.asset_id IN (SELECT da.asset_id FROM department_asset AS da)
			)
			AND a.status_id = 1
			ORDER BY a.asset_id;
		");
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strSerialNumber, $intEmployeeId, $intDepartmentId);

		$arrAssets = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['asset_id'] = $intAssetId;
			$arrRow['serial_number'] = $strSerialNumber;
			$arrRow['employee_id'] = $intEmployeeId;
			$arrRow['department_id'] = $intDepartmentId;
			array_push($arrAssets, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrAssets;
	}

	/**
	 * ex-employees with assigned assets
	 */
	public function GetExitsWithAssets() {
		$objStmt = $this->objDb->prepare("
			SELECT e.employee_id, CONCAT(e.firstname, ' ', e.lastname) FROM employee AS e
			WHERE e.employee_id IN (SELECT ea.employee_id FROM employee_asset AS ea) AND e.exit = 1
			ORDER BY e.lastname, e.firstname;
		");
		$objStmt->execute();
		$objStmt->bind_result($intEmployeeId, $strName);

		$arrEmployees = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['employee_id'] = $intEmployeeId;
			$arrRow['name'] = $strName;
			array_push($arrEmployees, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrEmployees;
	}

	/**
	 * employees with multiple computing devices
	 */
	public function GetEmployeesWithMultipleComputingDevices() {
		$objStmt = $this->objDb->prepare("
			SELECT e.employee_id, CONCAT(e.firstname, ' ', e.lastname) FROM employee_asset AS ea
			JOIN asset AS a ON a.asset_id = ea.asset_id
			JOIN employee AS e ON e.employee_id = ea.employee_id
			WHERE a.type_id IN (1,2,3,4,5,6,7,8,9,10,11)
			GROUP BY e.employee_id
			HAVING COUNT(DISTINCT a.asset_id) > 1;
		");
		$objStmt->execute();
		$objStmt->bind_result($intEmployeeId, $strName);

		$arrEmployees = array();

		while ($objStmt->fetch()) {
			$arrEmployees[$intEmployeeId]['name'] = $strName;
		}
		$objStmt->close();

		foreach ($arrEmployees as $intEmployeeId => $strName) {
			$objStmt = $this->objDb->prepare("
				SELECT a.asset_id, a.serial_number, t.type FROM asset AS a
				JOIN employee_asset AS ea ON a.asset_id = ea.asset_id
				JOIN type AS t on t.type_id = a.type_id
				WHERE ea.employee_id = ? AND a.type_id IN (1,2,3,4,5,6,7,8,9,10,11);
			");
			$objStmt->bind_param('i', $intEmployeeId);
			$objStmt->execute();
			$objStmt->bind_result($intAssetId, $strSerialNumber, $strType);

			$arrAssets = array();

			while ($objStmt->fetch()) {
				$arrRow = array();
				$arrRow['asset_id'] = $intAssetId;
				$arrRow['serial_number'] = $strSerialNumber;
				$arrRow['type'] = $strType;
				array_push($arrAssets, $arrRow);
				unset($arrRow);
			}
			Application::Log($arrAssets);
			$arrEmployees[$intEmployeeId]['computers'] = $arrAssets;
			unset($arrAssets);
			$objStmt->close();
		}

		return $arrEmployees;
	}
}
?>

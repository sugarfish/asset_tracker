<?php
class AdminModel extends ModelBase {

	private $objDb;

	public function __construct() {
		$this->objDb = ModelBase::getInstance();
	}

	public function GetAssets($strSearch) {
		$strSearch = '%' . $strSearch . '%';
		$objStmt = $this->objDb->prepare("
			SELECT
				a.asset_id,
				a.versal_id,
				CONCAT(e.lastname, ', ', e.firstname) AS name,
				CONCAT(m.lastname, ', ', m.firstname) AS manager,
				d.department,
				dd.department AS asset_department,
				t.type,
				a.description,
				a.model,
				a.serial_number,
				a.status_id,
				s.status
			FROM
				asset AS a
			LEFT JOIN employee_asset AS ea ON ea.asset_id = a.asset_id
			LEFT JOIN department_asset AS da ON da.asset_id = a.asset_id
			LEFT JOIN employee AS e ON e.employee_id = ea.employee_id
			LEFT JOIN employee AS m ON m.employee_id = e.manager_id
			LEFT JOIN department AS d ON d.department_id = e.department_id
			LEFT JOIN department AS dd ON dd.department_id = da.department_id
			LEFT JOIN type AS t ON t.type_id = a.type_id
			JOIN status AS s ON s.status_id = a.status_id
			WHERE
				a.versal_id LIKE ?;
		");
		$objStmt->bind_param('s', $strSearch);
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $intVersalId, $strName, $strManager, $strDepartment, $strAssetDepartment, $strType, $strDescription, $strModel, $strSerialNumber, $intStatusId, $strStatus);

		$arrSerialNumbers = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['asset_id'] = $intAssetId;
			$arrRow['versal_id'] = $intVersalId;
			$arrRow['name'] = (is_null($strName))?'':$strName;
			$arrRow['manager'] = (is_null($strManager))?'':$strManager;
			$arrRow['department'] = (is_null($strDepartment))?'':$strDepartment;
			$arrRow['asset_department'] = (is_null($strAssetDepartment))?'':$strAssetDepartment;
			$arrRow['type'] = (is_null($strType))?'':$strType;
			$arrRow['description'] = (is_null($strDescription))?'':$strDescription;
			$arrRow['model'] = (is_null($strModel))?'':$strModel;
			$arrRow['serial_number'] = $strSerialNumber;
			$arrRow['status_id'] = $intStatusId;
			$arrRow['status'] = $strStatus;
			array_push($arrSerialNumbers, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		foreach ($arrSerialNumbers as $intKey => $arrItem) {
			$arrSerialNumbers[$intKey]['has_history'] = $this->HasHistory($arrSerialNumbers[$intKey]['asset_id']);
		}

		return $arrSerialNumbers;
	}

	public function GetEmployees($strSearch) {
		$strSearch = '%' . $strSearch . '%';

		$objStmt = $this->objDb->prepare("
			SELECT
				e.employee_id,
				e.exit,
				ea.asset_id,
				CONCAT(e.lastname, ', ', e.firstname) AS name,
				CONCAT(m.lastname, ', ', m.firstname) AS manager,
				d.department,
				t.type,
				a.description,
				a.model,
				a.serial_number
			FROM
				employee AS e
			LEFT JOIN employee_asset AS ea ON ea.employee_id = e.employee_id
			LEFT JOIN asset AS a ON a.asset_id = ea.asset_id
			LEFT JOIN employee AS m ON m.employee_id = e.manager_id
			LEFT JOIN department AS d ON d.department_id = e.department_id
			LEFT JOIN type AS t ON t.type_id = a.type_id
			WHERE
				(e.firstname LIKE ? OR e.lastname LIKE ?)
			ORDER BY e.lastname, e.firstname, t.type
		");

		$objStmt->bind_param('ss', $strSearch, $strSearch);
		$objStmt->execute();
		$objStmt->bind_result($intEmployeeId, $intExit, $intAssetId, $strName, $strManager, $strDepartment, $strType, $strDescription, $strModel, $strSerialNumber);

		$arrEmployees = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['employee_id'] = $intEmployeeId;
			$arrRow['exit'] = $intExit;
			$arrRow['asset_id'] = $intAssetId;
			$arrRow['name'] = (is_null($strName))?'':$strName;
			$arrRow['manager'] = (is_null($strManager))?'':$strManager;
			$arrRow['department'] = (is_null($strDepartment))?'':$strDepartment;
			$arrRow['type'] = (is_null($strType))?'':$strType;
			$arrRow['description'] = (is_null($strDescription))?'':$strDescription;
			$arrRow['model'] = (is_null($strModel))?'':$strModel;
			$arrRow['serial_number'] = $strSerialNumber;
			array_push($arrEmployees, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		foreach ($arrEmployees as $intKey => $arrItem) {
			$arrEmployees[$intKey]['has_history'] = $this->HasHistory($arrEmployees[$intKey]['asset_id']);
		}

		return $arrEmployees;
	}

	public function GetEmployeeDetails($intEmployeeId) {
		$objStmt = $this->objDb->prepare("
			SELECT
				e.firstname,
				e.lastname,
				e.manager_id,
				e.department_id,
				e.local,
				e.remote_location,
				e.exit
			FROM
				employee AS e
			WHERE e.employee_id = ?;
		");
		$objStmt->bind_param('i', $intEmployeeId);
		$objStmt->execute();
		$objStmt->bind_result($strFirstName, $strLastName, $intManagerId, $intDepartmentId, $intLocal, $strRemoteLocation, $intExit);

		$arrEmployeeDetails = array();

		if ($objStmt->fetch()) {
			$arrEmployeeDetails['firstname'] = $strFirstName;
			$arrEmployeeDetails['lastname'] = $strLastName;
			$arrEmployeeDetails['manager_id'] = $intManagerId;
			$arrEmployeeDetails['department_id'] = $intDepartmentId;
			$arrEmployeeDetails['local'] = $intLocal;
			$arrEmployeeDetails['remote_location'] = $strRemoteLocation;
			$arrEmployeeDetails['exit'] = $intExit;
		}
		$objStmt->close();

		return $arrEmployeeDetails;
	}

	public function GetEmployeeList() {
		$objStmt = $this->objDb->prepare("
			SELECT
				e.employee_id AS id,
				CONCAT(e.lastname, ', ', e.firstname) AS name
			FROM
				employee AS e
			WHERE e.exit = 0
			ORDER BY e.lastname, e.firstname;
		");
		$objStmt->execute();
		$objStmt->bind_result($intEmployeeId, $strName);

		$arrEmployees = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['id'] = $intEmployeeId;
			$arrRow['name'] = $strName;
			array_push($arrEmployees, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrEmployees;
	}

	public function GetDepartmentList() {
		$objStmt = $this->objDb->prepare("
			SELECT
				d.department_id,
				d.department
			FROM
				department AS d
			ORDER by d.department;
		");
		$objStmt->execute();
		$objStmt->bind_result($intDepartmentId, $strDepartment);

		$arrDepartments = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['id'] = $intDepartmentId;
			$arrRow['department'] = $strDepartment;
			array_push($arrDepartments, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrDepartments;
	}

	public function GetAssetDetails($intAssetId) {
		$arrAssetDetails = array();

		$objStmt = $this->objDb->prepare("
			SELECT
				a.versal_id,
				a.serial_number,
				a.na_missing,
				a.description,
				a.model,
				a.hostname,
				a.mac_address_eth,
				a.mac_address_wlan,
				a.purchase_order_id,
				a.type_id,
				a.group_id,
				a.status_id
			FROM
				asset AS a
			WHERE a.asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->bind_result($intVersalId, $strSerialNumber, $intNASerialNumber, $strDescription, $strModel, $strHostname, $strMacEth, $strMacWlan, $strPurchaseOrderId, $intTypeId, $intGroupId, $intStatusId);

		if ($objStmt->fetch()) {
			$arrAssetDetails['versal_id'] = $intVersalId;
			$arrAssetDetails['serial_number'] = $strSerialNumber;
			$arrAssetDetails['na_missing'] = $intNASerialNumber;
			$arrAssetDetails['description'] = $strDescription;
			$arrAssetDetails['model'] = $strModel;
			$arrAssetDetails['hostname'] = $strHostname;
			$arrAssetDetails['mac_eth'] = $strMacEth;
			$arrAssetDetails['mac_wlan'] = $strMacWlan;
			$arrAssetDetails['purchase_order_id'] = $strPurchaseOrderId;
			$arrAssetDetails['type_id'] = $intTypeId;
			$arrAssetDetails['group_id'] = $intGroupId;
			$arrAssetDetails['status_id'] = $intStatusId;
		}
		$objStmt->close();

		$objStmt = $this->objDb->prepare("
			SELECT
				ea.employee_id
			FROM
				employee_asset AS ea
			WHERE ea.asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->bind_result($intEmployeeId);

		if ($objStmt->fetch()) {
			$arrAssetDetails['employee_id'] = $intEmployeeId;
		}
		$objStmt->close();

		if (is_null($arrAssetDetails['employee_id'])) {
			$objStmt = $this->objDb->prepare("
				SELECT
					da.department_id
				FROM
					department_asset AS da
				WHERE da.asset_id = ?;
			");
			$objStmt->bind_param('i', $intAssetId);
			$objStmt->execute();
			$objStmt->bind_result($intDepartmentId);

			if ($objStmt->fetch()) {
				$arrAssetDetails['department_id'] = $intDepartmentId;
			}
			$objStmt->close();
		}

		return $arrAssetDetails;
	}

	public function GetTypeList() {
		$objStmt = $this->objDb->prepare("
			SELECT
				t.type_id,
				t.type
			FROM
				type AS t
			ORDER BY t.type;
		");
		$objStmt->execute();
		$objStmt->bind_result($intId, $strType);

		$arrTypes = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['id'] = $intId;
			$arrRow['type'] = $strType;
			array_push($arrTypes, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrTypes;
	}

	public function GetGroupList() {
		$objStmt = $this->objDb->prepare("
			SELECT
				g.group_id,
				g.group
			FROM
				`group` AS g;
		");
		$objStmt->execute();
		$objStmt->bind_result($intId, $strGroup);

		$arrGroups = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['id'] = $intId;
			$arrRow['group'] = $strGroup;
			array_push($arrGroups, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrGroups;
	}

	public function GetStatusList() {
		$objStmt = $this->objDb->prepare("
			SELECT
				s.status_id,
				s.status
			FROM
				status AS s;
		");
		$objStmt->execute();
		$objStmt->bind_result($intStatusId, $strStatus);

		$arrStates = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['id'] = $intStatusId;
			$arrRow['status'] = $strStatus;
			array_push($arrStates, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrStates;
	}

	public function GetDepartments($intDepartmentId) {
		$objStmt = $this->objDb->prepare("
			SELECT
				a.asset_id,
				t.type,
				a.description,
				a.serial_number,
				g.group,
				s.status
			FROM
				asset AS a
			LEFT JOIN department_asset AS da ON da.asset_id = a.asset_id
			LEFT JOIN type AS t ON t.type_id = a.type_id
			LEFT JOIN `group` AS g ON g.group_id = a.group_id
			JOIN status AS s ON a.status_id = s.status_id
			WHERE
				da.department_id LIKE ?
			ORDER BY `group`, t.type, a.description;
		");
		$objStmt->bind_param('i', $intDepartmentId);
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strType, $strDescription, $strSerialNumber, $strGroup, $strStatus);

		$arrSerialNumbers = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['asset_id'] = $intAssetId;
			$arrRow['type'] = (is_null($strType))?'':$strType;
			$arrRow['description'] = (is_null($strDescription))?'':$strDescription;
			$arrRow['serial_number'] = $strSerialNumber;
			$arrRow['group'] = (is_null($strGroup))?'':$strGroup;
			$arrRow['status'] = $strStatus;
			array_push($arrSerialNumbers, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		foreach ($arrSerialNumbers as $intKey => $arrItem) {
			$arrSerialNumbers[$intKey]['has_history'] = $this->HasHistory($arrSerialNumbers[$intKey]['asset_id']);
		}

		return $arrSerialNumbers;
	}

	public function GetGroups($intGroupId) {
		$objStmt = $this->objDb->prepare("
			SELECT
				a.asset_id,
				t.type,
				a.description,
				a.serial_number,
				g.group,
				s.status
			FROM
				asset AS a
			LEFT JOIN department_asset AS da ON da.asset_id = a.asset_id
			LEFT JOIN type AS t ON t.type_id = a.type_id
			LEFT JOIN `group` AS g ON g.group_id = a.group_id
			JOIN status AS s ON a.status_id = s.status_id
			WHERE
				a.group_id LIKE ?
			ORDER BY `group`, t.type, a.description;
		");
		$objStmt->bind_param('i', $intGroupId);
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strType, $strDescription, $strSerialNumber, $strGroup, $strStatus);

		$arrSerialNumbers = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['asset_id'] = $intAssetId;
			$arrRow['type'] = (is_null($strType))?'':$strType;
			$arrRow['description'] = (is_null($strDescription))?'':$strDescription;
			$arrRow['serial_number'] = $strSerialNumber;
			$arrRow['group'] = (is_null($strGroup))?'':$strGroup;
			$arrRow['status'] = $strStatus;
			array_push($arrSerialNumbers, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		foreach ($arrSerialNumbers as $intKey => $arrItem) {
			$arrSerialNumbers[$intKey]['has_history'] = $this->HasHistory($arrSerialNumbers[$intKey]['asset_id']);
		}

		return $arrSerialNumbers;
	}

	public function GetAssetHistory($intAssetId) {
		$objStmt = $this->objDb->prepare("
			SELECT DISTINCT
				a.versal_id,
				a.serial_number,
				t.type,
				a.description,
				s.status,
				g.group
			FROM
				history AS h
			JOIN asset AS a ON a.asset_id = h.asset_id
			JOIN type AS t ON t.type_id = a.type_id
			LEFT JOIN `group` AS g ON g.group_id = a.group_id
			JOIN status AS s ON s.status_id = a.status_id
			WHERE h.asset_id = ?
			GROUP BY h.asset_id;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->bind_result($intVersalId, $strSerialNumber, $strType, $strDescription, $strStatus, $strGroup);

		if ($objStmt->fetch()) {
			$arrAsset['details']['versal_id'] = $intVersalId;
			$arrAsset['details']['serial_number'] = $strSerialNumber;
			$arrAsset['details']['type'] = $strType;
			$arrAsset['details']['description'] = (is_null($strDescription))?'':$strDescription;
			$arrAsset['details']['status'] = $strStatus;
			$arrAsset['details']['group'] = (is_null($strGroup))?'':$strGroup;
		}
		$objStmt->close();

		$intIndex = 0;

		$objStmt = $this->objDb->prepare("
			SELECT
				h.history_id,
				h.transfer_date,
				fs.status AS from_status,
				ts.status AS to_status,
				CONCAT(fe.lastname, ', ', fe.firstname) AS from_employee,
				CONCAT(te.lastname, ', ', te.firstname) AS to_employee,
				fd.department AS from_department,
				td.department AS to_department
			FROM
				history AS h
			LEFT JOIN status AS fs ON fs.status_id = h.from_status_id
			LEFT JOIN status AS ts ON ts.status_id = h.to_status_id
			LEFT JOIN employee AS fe ON fe.employee_id = h.from_employee_id
			LEFT JOIN employee AS te ON te.employee_id = h.to_employee_id
			LEFT JOIN department AS fd ON fd.department_id = h.from_department_id
			LEFT JOIN department AS td ON td.department_id = h.to_department_id
			WHERE h.asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->bind_result($intReferenceId, $strTransferDate, $strFromStatus, $strToStatus, $strFromEmployee, $strToEmployee, $strFromDepartment, $strToDepartment);

		while ($objStmt->fetch()) {
			$arrTransfer = array();
			$arrTransfer['reference_id'] = sprintf('AT%s', str_pad($intReferenceId, 6, '0', STR_PAD_LEFT));
			$arrTransfer['date'] = sprintf('%s-%s-%s', substr($strTransferDate, 5, 2), substr($strTransferDate, 8, 2), substr($strTransferDate, 0, 4));
			$arrTransfer['from_status'] = (is_null($strFromStatus))?'':$strFromStatus;
			$arrTransfer['to_status'] = (is_null($strToStatus))?'':$strToStatus;
			$arrTransfer['from_employee'] = (is_null($strFromEmployee))?'':$strFromEmployee;
			$arrTransfer['to_employee'] = (is_null($strToEmployee))?'':$strToEmployee;
			$arrTransfer['from_department'] = (is_null($strFromDepartment))?'':$strFromDepartment;
			$arrTransfer['to_department'] = (is_null($strToDepartment))?'':$strToDepartment;
			$arrAsset['history'][$intIndex] = $arrTransfer;
			unset($arrTransfer);
			$intIndex++;
		}
		$objStmt->close();

		return $arrAsset;
	}

	public function HasHistory($intAssetId) {
		$blnHasHistory = false;

		$objStmt = $this->objDb->prepare("
			SELECT DISTINCT history_id FROM history WHERE asset_id = ? GROUP BY asset_id;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->bind_result($intAssetId);

		if ($objStmt->fetch()) {
			$blnHasHistory = true;
		}
		$objStmt->close();

		return $blnHasHistory;
	}

	public function CheckForExistingSerialNumber($strSerialNumber) {
		$objStmt = $this->objDb->prepare("
			SELECT a.asset_id FROM asset AS a WHERE a.serial_number = ?;
		");
		$objStmt->bind_param('s', $strSerialNumber);
		$objStmt->execute();
		$objStmt->bind_result($intAssetId);
		$objStmt->store_result();

		Application::Log($strSerialNumber);
		Application::Log($objStmt->num_rows);

		if ($objStmt->num_rows > 0) {
			$blnResult = true;
		} else {
			$blnResult = false;
		}

		return $blnResult;
	}

	public function GetAssetData($intAssetId) {
		$objStmt = $this->objDb->prepare("
			SELECT a.serial_number, COUNT(h.history_id) FROM history AS h
			JOIN asset AS a ON h.asset_id = a.asset_id
			WHERE h.asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->bind_result($strSerialNumber, $intHistoryEvents);

		if ($objStmt->fetch()) {
			$strSerialNumber = $strSerialNumber;
			$intHistoryEvents = $intHistoryEvents;
		}
		$objStmt->close();

		return array('serial_number' => $strSerialNumber, 'history_events' => $intHistoryEvents);
	}

	public function GetInventoryData() {
		// apple computers
		$objStmt = $this->objDb->prepare("
			SELECT a.asset_id, a.serial_number, t.type, a.description, a.model FROM asset AS a
			JOIN type AS t ON t.type_id = a.type_id
			WHERE a.status_id = 2 AND a.type_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 19)
			ORDER BY a.type_id;
		");
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strSerialNumber, $strType, $strDescription, $strModel);

		$arrAppleComputers = array();

		while ($objStmt->fetch()) {
			$arrItem = array();
			$arrItem['asset_id'] = $intAssetId;
			$arrItem['serial_number'] = $strSerialNumber;
			$arrItem['type'] = $strType;
			$arrItem['description'] = (is_null($strDescription))?'':$strDescription;
			$arrItem['model'] = ($strModel == '')?'':$strModel;
			array_push($arrAppleComputers, $arrItem);
			unset($arrItem);
		}
		$objStmt->close();

		// other computers
		$objStmt = $this->objDb->prepare("
			SELECT a.asset_id, a.serial_number, t.type, a.description, a.model FROM asset AS a
			JOIN type AS t ON t.type_id = a.type_id
			WHERE a.status_id = 2 AND a.type_id IN (10, 11, 24, 28)
			ORDER BY a.type_id;
		");
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strSerialNumber, $strType, $strDescription, $strModel);

		$arrOtherComputers = array();

		while ($objStmt->fetch()) {
			$arrItem = array();
			$arrItem['asset_id'] = $intAssetId;
			$arrItem['serial_number'] = $strSerialNumber;
			$arrItem['type'] = $strType;
			$arrItem['description'] = (is_null($strDescription))?'':$strDescription;
			$arrItem['model'] = ($strModel == '')?'':$strModel;
			array_push($arrOtherComputers, $arrItem);
			unset($arrItem);
		}
		$objStmt->close();

		// phones/tablets
		$objStmt = $this->objDb->prepare("
			SELECT a.asset_id, a.serial_number, t.type, a.description, a.model FROM asset AS a
			JOIN type AS t ON t.type_id = a.type_id
			WHERE a.status_id = 2 AND a.type_id IN (17, 21, 22, 23, 25, 32, 33)
			ORDER BY a.type_id;
		");
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strSerialNumber, $strType, $strDescription, $strModel);

		$arrPhonesTablets = array();

		while ($objStmt->fetch()) {
			$arrItem = array();
			$arrItem['asset_id'] = $intAssetId;
			$arrItem['serial_number'] = $strSerialNumber;
			$arrItem['type'] = $strType;
			$arrItem['description'] = (is_null($strDescription))?'':$strDescription;
			$arrItem['model'] = ($strModel == '')?'':$strModel;
			array_push($arrPhonesTablets, $arrItem);
			unset($arrItem);
		}
		$objStmt->close();

		// monitors/displays
		$objStmt = $this->objDb->prepare("
			SELECT a.asset_id, a.serial_number, t.type, a.description, a.model FROM asset AS a
			JOIN type AS t ON t.type_id = a.type_id
			WHERE a.status_id = 2 AND a.type_id IN (12, 13, 14, 20, 29)
			ORDER BY a.type_id;
		");
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strSerialNumber, $strType, $strDescription, $strModel);

		$arrMonitorsDisplays = array();

		while ($objStmt->fetch()) {
			$arrItem = array();
			$arrItem['asset_id'] = $intAssetId;
			$arrItem['serial_number'] = $strSerialNumber;
			$arrItem['type'] = $strType;
			$arrItem['description'] = (is_null($strDescription))?'':$strDescription;
			$arrItem['model'] = ($strModel == '')?'':$strModel;
			array_push($arrMonitorsDisplays, $arrItem);
			unset($arrItem);
		}
		$objStmt->close();

		// miscellaneous
		$objStmt = $this->objDb->prepare("
			SELECT a.asset_id, a.serial_number, t.type, a.description, a.model FROM asset AS a
			JOIN type AS t ON t.type_id = a.type_id
			WHERE a.status_id = 2 AND a.type_id IN (15, 16, 26, 27, 30, 31, 34)
			ORDER BY a.type_id;
		");
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $strSerialNumber, $strType, $strDescription, $strModel);

		$arrMiscellaneous = array();

		while ($objStmt->fetch()) {
			$arrItem = array();
			$arrItem['asset_id'] = $intAssetId;
			$arrItem['serial_number'] = $strSerialNumber;
			$arrItem['type'] = $strType;
			$arrItem['description'] = (is_null($strDescription))?'':$strDescription;
			$arrItem['model'] = ($strModel == 0)?'':$strModel;
			array_push($arrMiscellaneous, $arrItem);
			unset($arrItem);
		}
		$objStmt->close();

		return array(
			'apple_computers' => $arrAppleComputers,
			'other_computers' => $arrOtherComputers,
			'phones_tablets' => $arrPhonesTablets,
			'monitors_displays' => $arrMonitorsDisplays,
			'miscellaneous' => $arrMiscellaneous
		);
	}

	public function GetNumberOfAssets() {
		$objStmt = $this->objDb->prepare("
			SELECT
				COUNT(a.asset_id)
			FROM
				asset AS a
			WHERE a.status_id IN (1, 2);
		");
		$objStmt->execute();
		$objStmt->bind_result($intCount);

		if ($objStmt->fetch()) {
			$intCount = $intCount;
		}
		$objStmt->close();

		return $intCount;
	}

	public function GetNumberOfAssignedAssets() {
		$objStmt = $this->objDb->prepare("
			SELECT
				COUNT(a.asset_id)
			FROM
				asset AS a
			WHERE a.status_id IN (1);
		");
		$objStmt->execute();
		$objStmt->bind_result($intCount);

		if ($objStmt->fetch()) {
			$intCount = $intCount;
		}
		$objStmt->close();

		return $intCount;
	}

	public function GetNumberOfInventoryAssets() {
		$objStmt = $this->objDb->prepare("
			SELECT
				COUNT(a.asset_id)
			FROM
				asset AS a
			WHERE a.status_id IN (2);
		");
		$objStmt->execute();
		$objStmt->bind_result($intCount);

		if ($objStmt->fetch()) {
			$intCount = $intCount;
		}
		$objStmt->close();

		return $intCount;
	}

	public function GetNumberOfUsers() {
		$objStmt = $this->objDb->prepare("
			SELECT
				COUNT(e.employee_id)
			FROM
				employee AS e
			WHERE e.exit = 0;
		");
		$objStmt->execute();
		$objStmt->bind_result($intCount);

		if ($objStmt->fetch()) {
			$intCount = $intCount;
		}
		$objStmt->close();

		return $intCount;
	}

	/* save/delete functions... */

	public function SaveAsset($arrArgs) {
		$arrArgs['na_missing'] = ($arrArgs['na_missing'] == 'true')?true:false;
		$arrArgs['versal_id'] = $arrArgs['versal_id'];
		$arrArgs['serial_number'] = ($arrArgs['na_missing'])?'[n/a]':strtoupper($arrArgs['serial_number']);
		$arrArgs['description'] = (empty($arrArgs['description']))?null:$arrArgs['description'];
		$arrArgs['model'] = (empty($arrArgs['model']))?null:$arrArgs['model'];
		$arrArgs['hostname'] = (empty($arrArgs['hostname']))?null:$arrArgs['hostname'];
		$arrArgs['mac_eth'] = (empty($arrArgs['mac_eth']))?null:$arrArgs['mac_eth'];
		$arrArgs['mac_wlan'] = (empty($arrArgs['mac_wlan']))?null:$arrArgs['mac_wlan'];
		$arrArgs['purchase_order_id'] = (empty($arrArgs['purchase_order_id']))?null:$arrArgs['purchase_order_id'];

		if ($arrArgs['asset_id'] == -1) {

			// save new asset

			$objStmt = $this->objDb->prepare("
				INSERT INTO asset VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
			");
			$objStmt->bind_param('iiisiissssss', $arrArgs['type_id'], $arrArgs['group_id'], $arrArgs['versal_id'], $arrArgs['serial_number'], $arrArgs['na_missing'], $arrArgs['status_id'], $arrArgs['description'], $arrArgs['model'], $arrArgs['hostname'], $arrArgs['mac_eth'], $arrArgs['mac_wlan'], $arrArgs['purchase_order_id']);
			$objResult = $objStmt->execute();
			//Application::Log($objStmt->error);
			$arrArgs['asset_id'] = $objStmt->insert_id;
			$objStmt->close();

			if (!$objResult) {
				return array('result' => 'failed');
			}

			$this->SaveHistory($arrArgs, true);

			if ($arrArgs['status_id'] != 1) {
				return array('result' => 'success');
			}

			if ($arrArgs['status_id'] == 1 && $arrArgs['assignment_dirty'] == true) {

				if ($arrArgs['assigned_employee_id'] > 0) {
					// assign to employee
					$objStmt = $this->objDb->prepare("
						INSERT INTO employee_asset VALUES(?, ?);
					");
					$objStmt->bind_param('ii', $arrArgs['assigned_employee_id'], $arrArgs['asset_id']);
					$objResult = $objStmt->execute();
					$objStmt->close();

					return array('result' => 'success');
				} elseif ($arrArgs['assigned_department_id'] > 0) {
					// assign to department
					$objStmt = $this->objDb->prepare("
						INSERT INTO department_asset VALUES(?, ?);
					");
					$objStmt->bind_param('ii', $arrArgs['assigned_department_id'], $arrArgs['asset_id']);
					$objResult = $objStmt->execute();
					$objStmt->close();

					return array('result' => 'success');
				}
			} else {
				return array('result' => 'success');
			}

		} else {
			// save existing asset

			$this->SaveHistory($arrArgs);

			$objStmt = $this->objDb->prepare("
				UPDATE asset AS a
				SET
					a.versal_id = ?,
					a.serial_number = ?,
					a.na_missing = ?,
					a.type_id = ?,
					a.description = ?,
					a.model = ?,
					a.group_id = ?,
					a.status_id = ?,
					a.hostname = ?,
					a.mac_address_eth = ?,
					a.mac_address_wlan = ?,
					a.purchase_order_id = ?
				WHERE a.asset_id = ?;
			");
			$objStmt->bind_param('isiissiissssi', $arrArgs['versal_id'], $arrArgs['serial_number'], $arrArgs['na_missing'], $arrArgs['type_id'], $arrArgs['description'], $arrArgs['model'], $arrArgs['group_id'], $arrArgs['status_id'], $arrArgs['hostname'], $arrArgs['mac_eth'], $arrArgs['mac_wlan'], $arrArgs['purchase_order_id'], $arrArgs['asset_id']);
			$objResult = $objStmt->execute();
			$objStmt->close();

			if ($arrArgs['status_id'] != 1) {
				$this->RemoveAssignments($arrArgs['asset_id']);
				return array('result' => 'success');
			}

			if ($arrArgs['status_id'] == 1 && $arrArgs['assignment_dirty'] == true) {
				$this->RemoveAssignments($arrArgs['asset_id']);
				//return array('result' => 'success');

				if ($arrArgs['assigned_employee_id'] > 0) {
					// assign to employee
					$objStmt = $this->objDb->prepare("
						INSERT INTO employee_asset VALUES(?, ?);
					");
					$objStmt->bind_param('ii', $arrArgs['assigned_employee_id'], $arrArgs['asset_id']);
					$objResult = $objStmt->execute();
					$objStmt->close();

				} elseif ($arrArgs['assigned_department_id'] > 0) {
					// assign to department
					$objStmt = $this->objDb->prepare("
						INSERT INTO department_asset VALUES(?, ?);
					");
					$objStmt->bind_param('ii', $arrArgs['assigned_department_id'], $arrArgs['asset_id']);
					$objResult = $objStmt->execute();
					$objStmt->close();
				}
			}

			return array('result' => 'success');
		}
	}

	public function DeleteAsset($intAssetId) {
		// asset
		$objStmt = $this->objDb->prepare("
			DELETE FROM asset
			WHERE asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->close();

		// employee_asset
		$objStmt = $this->objDb->prepare("
			DELETE FROM employee_asset
			WHERE asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->close();

		// department_asset
		$objStmt = $this->objDb->prepare("
			DELETE FROM department_asset
			WHERE asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->close();

		// history
		$objStmt = $this->objDb->prepare("
			DELETE FROM history
			WHERE asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objStmt->execute();
		$objStmt->close();
	}

	private function RemoveAssignments($intAssetId) {
		// remove current assignments
		$objStmt = $this->objDb->prepare("
			DELETE FROM employee_asset WHERE asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objResult = $objStmt->execute();
		$objStmt->close();

		$objStmt = $this->objDb->prepare("
			DELETE FROM department_asset WHERE asset_id = ?;
		");
		$objStmt->bind_param('i', $intAssetId);
		$objResult = $objStmt->execute();
		$objStmt->close();
	}

	private function SaveHistory($arrArgs, $blnNew = false) {
		$arrArgs['ignore_change'] = ($arrArgs['ignore_change'] == 'true')?true:false;

		if (!$arrArgs['ignore_change']) {
			$strNotes = $arrArgs['notes'];
			$intToStatusId = $arrArgs['status_id'];
			$intToEmployeeId = ($arrArgs['assigned_employee_id'] == 0)?null:$arrArgs['assigned_employee_id'];
			$intToDepartmentId = ($arrArgs['assigned_department_id'] == 0)?null:$arrArgs['assigned_department_id'];

			// test current values against existing values
			$objStmt = $this->objDb->prepare("
				SELECT a.status_id FROM asset AS a
				WHERE a.asset_id = ?;
			");
			$objStmt->bind_param('i', $arrArgs['asset_id']);
			$objResult = $objStmt->execute();
			$objStmt->bind_result($intStatusId);

			if ($objStmt->fetch()) {
				$intFromStatusId = $intStatusId;
			}
			$objStmt->close();

			$objStmt = $this->objDb->prepare("
				SELECT ea.employee_id FROM employee_asset AS ea
				WHERE ea.asset_id = ?;
			");
			$objStmt->bind_param('i', $arrArgs['asset_id']);
			$objResult = $objStmt->execute();
			$objStmt->bind_result($intEmployeeId);

			if ($objStmt->fetch()) {
				$intFromEmployeeId = $intEmployeeId;
			}
			$objStmt->close();

			$objStmt = $this->objDb->prepare("
				SELECT da.department_id FROM department_asset AS da
				WHERE da.asset_id = ?;
			");
			$objStmt->bind_param('i', $arrArgs['asset_id']);
			$objResult = $objStmt->execute();
			$objStmt->bind_result($intDepartmentId);

			if ($objStmt->fetch()) {
				$intFromDepartmentId = $intDepartmentId;
			}
			$objStmt->close();

			if ($intFromStatusId != $intToStatusId || $intFromEmployeeId != $intToEmployeeId || $intFromDepartmentId != $intToDepartmentId || $blnNew) {

				if ($intFromStatusId == $intToStatusId) {
					$intFromStatusId = null;
					$intToStatusId = ($blnNew)?$intToStatusId:null;
				}
				if ($intFromEmployeeId == $intToEmployeeId) {
					$intToEmployeeId = null;
				}
				if ($intFromDepartmentId == $intToDepartmentId) {
					$intToDepartmentId = null;
				}

				// write history event
				$arrDate = explode('/', $arrArgs['date']);
				$objDateTime = new DateTime;
				$objDateTime->setDate(intval($arrDate[2]), intval($arrDate[0]), intval($arrDate[1]));
				$strDate = $objDateTime->format('Y-m-d');

				$objStmt = $this->objDb->prepare("
					INSERT INTO history VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?);
				");
				$objStmt->bind_param('issiiiiii', $arrArgs['asset_id'], $strDate, $strNotes, $intFromStatusId, $intToStatusId, $intFromEmployeeId, $intToEmployeeId, $intFromDepartmentId, $intToDepartmentId);
				$objResult = $objStmt->execute();
				$objStmt->close();
			}
		}
	}

	public function SaveEmployee($arrArgs) {
		$arrArgs['local'] = ($arrArgs['local'] == 'true')?1:0;
		$arrArgs['exit'] = ($arrArgs['exit'] == 'true')?1:0;
		$strLastname = ucwords($arrArgs['lastname']);
		$strFirstname = ucwords($arrArgs['firstname']);
		$strRemoteLocation = ($arrArgs['remote_location'] == 'ST, USA or Country' || $arrArgs['local'])?null:$arrArgs['remote_location'];

		if ($arrArgs['employee_id'] == -1) {
			// save new employee
			$objStmt = $this->objDb->prepare("
				INSERT INTO employee VALUES(NULL, ?, ?, ?, ?, ?, ?, ?);
			");
			$objStmt->bind_param('ssiiisi', $strLastname, $strFirstname, $arrArgs['department_id'], $arrArgs['manager_id'], $arrArgs['local'], $strRemoteLocation, $arrArgs['exit']);

			$objResult = $objStmt->execute();
			$objStmt->close();

			return array('result' => (!$objResult)?'failed':'success');
		} else {
			// save existing employee
			$objStmt = $this->objDb->prepare("
				UPDATE employee AS e
				SET
					e.firstname = ?,
					e.lastname = ?,
					e.department_id = ?,
					e.manager_id = ?,
					e.local = ?,
					e.remote_location = ?,
					e.exit = ?
				WHERE e.employee_id = ?;
			");
			$objStmt->bind_param('ssiiisii', $arrArgs['firstname'], $arrArgs['lastname'], $arrArgs['department_id'], $arrArgs['manager_id'], $arrArgs['local'], $strRemoteLocation, $arrArgs['exit'], $arrArgs['employee_id']);

			$objResult = $objStmt->execute();
			$objStmt->close();

			return (objResult)?array('result' => 'success'):array('result' => 'failed');
		}
	}

	/*** dev ***/
	/*
	public function UpdateModel($intAssetId, $strModel) {
		$objStmt = $this->objDb->prepare("
			UPDATE asset AS a
			SET
				a.model = ?
			WHERE a.asset_id = ?;
		");
		$objStmt->bind_param('si', $strModel, $intAssetId);
		$objResult = $objStmt->execute();
		$objStmt->close();
	}
	*/
}
?>

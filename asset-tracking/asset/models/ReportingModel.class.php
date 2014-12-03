<?php
class ReportingModel extends ModelBase {

	private $objDb;

	public function __construct() {
		$this->objDb = ModelBase::getInstance();
	}

	public function GetAssetsByAssetID() {
		$objStmt = $this->objDb->prepare("
			SELECT
				CONCAT(e.lastname, ', ', e.firstname) AS name,
				CONCAT(m.lastname, ', ', m.firstname) AS manager,
				d.department,
				dd.department AS asset_department,
				t.type,
				a.description,
				a.versal_id,
				a.serial_number,
				s.status,
				a.purchase_order_id
			FROM
				asset AS a
			LEFT JOIN employee_asset AS ea ON ea.asset_id = a.asset_id
			LEFT JOIN department_asset AS da ON da.asset_id = a.asset_id
			LEFT JOIN employee AS e ON e.employee_id = ea.employee_id
			LEFT JOIN employee AS m ON m.employee_id = e.manager_id
			LEFT JOIN department AS d ON d.department_id = e.department_id
			LEFT JOIN department AS dd ON dd.department_id = da.department_id
			LEFT JOIN type AS t ON t.type_id = a.type_id
			JOIN status AS s ON a.status_id = s.status_id
			ORDER BY e.lastname, e.firstname, t.type, s.status;
		");
		$objStmt->execute();
		$objStmt->bind_result($strName, $strManager, $strDepartment, $strAssetDepartment, $strType, $strDescription, $intVersalId, $strSerialNumber, $strStatus, $strPurchaseOrderId);

		$arrAssets = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['name'] = (is_null($strName))?'':$strName;
			$arrRow['manager'] = (is_null($strManager))?'':$strManager;
			$arrRow['department'] = (is_null($strDepartment))?'':$strDepartment;
			$arrRow['asset_department'] = (is_null($strAssetDepartment))?'':$strAssetDepartment;
			$arrRow['type'] = (is_null($strType))?'':$strType;
			$arrRow['description'] = (is_null($strDescription))?'':$strDescription;
			$arrRow['versal_id'] = str_pad($intVersalId, 6, '0', STR_PAD_LEFT);
			$arrRow['serial_number'] = (is_null($strSerialNumber))?'N/A':$strSerialNumber;
			$arrRow['status'] = $strStatus;
			$arrRow['purchase_order_id'] = $strPurchaseOrderId;
			array_push($arrAssets, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrAssets;
	}

	public function GetAssetsByEmployee() {
		$objStmt = $this->objDb->prepare("
			SELECT
				CONCAT(e.lastname, ', ', e.firstname) AS name,
				CONCAT(m.lastname, ', ', m.firstname) AS manager,
				d.department,
				e.local,
				e.remote_location,
				t.type,
				a.description,
				a.versal_id,
				a.purchase_order_id
			FROM
				asset AS a
			LEFT JOIN employee_asset AS ea ON ea.asset_id = a.asset_id
			LEFT JOIN department_asset AS da ON da.asset_id = a.asset_id
			JOIN employee AS e ON e.employee_id = ea.employee_id
			LEFT JOIN employee AS m ON m.employee_id = e.manager_id
			LEFT JOIN department AS d ON d.department_id = e.department_id
			LEFT JOIN type AS t ON t.type_id = a.type_id
			ORDER BY e.lastname, e.firstname, t.type;
		");
		$objStmt->execute();
		$objStmt->bind_result($strName, $strManager, $strDepartment, $intLocal, $strRemoteLocation, $strType, $strDescription, $intVersalId, $strPurchaseOrderId);

		$arrAssets = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['name'] = (is_null($strName))?'':$strName;
			$arrRow['manager'] = (is_null($strManager))?'':$strManager;
			$arrRow['department'] = (is_null($strDepartment))?'':$strDepartment;
			$arrRow['location'] = ($intLocal == 1)?'CA, USA':$strRemoteLocation;
			$arrRow['type'] = (is_null($strType))?'':$strType;
			$arrRow['description'] = (is_null($strDescription))?'':$strDescription;
			$arrRow['versal_id'] = str_pad($intVersalId, 6, '0', STR_PAD_LEFT);
			$arrRow['purchase_order_id'] = $strPurchaseOrderId;
			array_push($arrAssets, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrAssets;
	}

	public function GetAssetsByDepartment() {
		$objStmt = $this->objDb->prepare("
			SELECT
				d.department AS asset_department,
				t.type,
				a.description,
				a.versal_id
			FROM
				asset AS a
			JOIN department_asset AS da ON da.asset_id = a.asset_id
			LEFT JOIN department AS d ON d.department_id = da.department_id
			LEFT JOIN type AS t ON t.type_id = a.type_id
			ORDER BY asset_department, t.type;
		");
		$objStmt->execute();
		$objStmt->bind_result($strAssetDepartment, $strType, $strDescription, $intVersalId);

		$arrAssets = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['asset_department'] = (is_null($strAssetDepartment))?'':$strAssetDepartment;
			$arrRow['type'] = (is_null($strType))?'':$strType;
			$arrRow['description'] = (is_null($strDescription))?'':$strDescription;
			$arrRow['versal_id'] = str_pad($intVersalId, 6, '0', STR_PAD_LEFT);
			array_push($arrAssets, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrAssets;
	}

	public function GetAssetsByGroup() {
		$objStmt = $this->objDb->prepare("
			SELECT
				g.group AS asset_group,
				t.type,
				a.description,
				a.versal_id
			FROM
				asset AS a
			JOIN `group` AS g ON g.group_id = a.group_id
			LEFT JOIN type AS t ON t.type_id = a.type_id
			ORDER BY g.group, t.type;
		");
		$objStmt->execute();
		$objStmt->bind_result($strGroup, $strType, $strDescription, $intVersalId);

		$arrAssets = array();

		while ($objStmt->fetch()) {
			$arrRow = array();
			$arrRow['group'] = $strGroup;
			$arrRow['type'] = (is_null($strType))?'':$strType;
			$arrRow['description'] = (is_null($strDescription))?'':$strDescription;
			$arrRow['versal_id'] = str_pad($intVersalId, 6, '0', STR_PAD_LEFT);
			array_push($arrAssets, $arrRow);
			unset($arrRow);
		}
		$objStmt->close();

		return $arrAssets;
	}

	public function GetFilteredAssetHistory($strFilter = null) {
		$strFilter = '%' . $strFilter . '%';
		$arrAssets = array();

		$objStmt = $this->objDb->prepare("
			SELECT DISTINCT
				h.asset_id,
				a.versal_id,
				t.type,
				a.description,
				s.status,
				g.group
			FROM
				history AS h
			JOIN asset AS a ON a.asset_id = h.asset_id
			LEFT JOIN employee_asset AS ea ON ea.asset_id = a.asset_id
			LEFT JOIN employee AS e ON e.employee_id = ea.employee_id OR e.employee_id = h.from_employee_id OR e.employee_id = h.to_employee_id
			JOIN type AS t ON t.type_id = a.type_id
			LEFT JOIN `group` AS g ON g.group_id = a.group_id
			JOIN status AS s ON s.status_id = a.status_id
			WHERE a.versal_id LIKE ? OR e.firstname LIKE ? OR e.lastname LIKE ?
			GROUP BY h.asset_id;
		");
		$objStmt->bind_param('sss', $strFilter, $strFilter, $strFilter);
		$objStmt->execute();
		$objStmt->bind_result($intAssetId, $intVersalId, $strType, $strDescription, $strStatus, $strGroup);

		while ($objStmt->fetch()) {
			$arrAssets[$intAssetId]['details']['versal_id'] = str_pad($intVersalId, 6, '0', STR_PAD_LEFT);
			$arrAssets[$intAssetId]['details']['type'] = $strType;
			$arrAssets[$intAssetId]['details']['description'] = (is_null($strDescription))?'':$strDescription;
			$arrAssets[$intAssetId]['details']['status'] = $strStatus;
			$arrAssets[$intAssetId]['details']['group'] = (is_null($strGroup))?'':$strGroup;
		}
		$objStmt->close();

		foreach ($arrAssets as $intAssetId => $arrAsset) {
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
				$arrTransfer['date'] =  sprintf('%s-%s-%s', substr($strTransferDate, 5, 2), substr($strTransferDate, 8, 2), substr($strTransferDate, 0, 4));
				$arrTransfer['from_status'] = (is_null($strFromStatus))?'':$strFromStatus;
				$arrTransfer['to_status'] = (is_null($strToStatus))?'':$strToStatus;
				$arrTransfer['from_employee'] = (is_null($strFromEmployee))?'':$strFromEmployee;
				$arrTransfer['to_employee'] = (is_null($strToEmployee))?'':$strToEmployee;
				$arrTransfer['from_department'] = (is_null($strFromDepartment))?'':$strFromDepartment;
				$arrTransfer['to_department'] = (is_null($strToDepartment))?'':$strToDepartment;
				$arrAssets[$intAssetId]['history'][$intIndex] = $arrTransfer;
				unset($arrTransfer);
				$intIndex++;
			}
			$objStmt->close();
		}

		return $arrAssets;
	}

	public function GetFilteredAssetMovement($strStartDate = null, $strEndDate = null) {
		$strStartDate = (empty($strStartDate) || $strStartDate == 'mmddyyyy')?'2000-01-01':sprintf('%s-%s-%s', substr($strStartDate, 4, 4), substr($strStartDate, 0, 2), substr($strStartDate, 2, 2));
		$strEndDate = (empty($strEndDate)  || $strEndDate == 'mmddyyyy')?'2999-12-01':sprintf('%s-%s-%s', substr($strEndDate, 4, 4), substr($strEndDate, 0, 2), substr($strEndDate, 2, 2));

		$arrMovements = array();

		$objStmt = $this->objDb->prepare("
			SELECT
				h.history_id,
				h.asset_id,
				h.transfer_date,
				h.notes,
				a.versal_id,
				a.purchase_order_id,
				t.type,
				a.description,
				s.status,
				g.group,
				fs.status AS from_status,
				ts.status AS to_status,
				CONCAT(fe.lastname, ', ', fe.firstname) AS from_employee,
				CONCAT(te.lastname, ', ', te.firstname) AS to_employee,
				fd.department AS from_department,
				td.department AS to_department
			FROM
				history AS h
			JOIN asset AS a ON a.asset_id = h.asset_id
			LEFT JOIN employee_asset AS ea ON ea.asset_id = a.asset_id
			LEFT JOIN employee AS e ON e.employee_id = ea.employee_id OR e.employee_id = h.from_employee_id OR e.employee_id = h.to_employee_id
			JOIN type AS t ON t.type_id = a.type_id
			JOIN status AS s ON s.status_id = a.status_id
			LEFT JOIN `group` AS g ON g.group_id = a.group_id

			LEFT JOIN status AS fs ON fs.status_id = h.from_status_id
			LEFT JOIN status AS ts ON ts.status_id = h.to_status_id
			LEFT JOIN employee AS fe ON fe.employee_id = h.from_employee_id
			LEFT JOIN employee AS te ON te.employee_id = h.to_employee_id
			LEFT JOIN department AS fd ON fd.department_id = h.from_department_id
			LEFT JOIN department AS td ON td.department_id = h.to_department_id

			WHERE h.transfer_date BETWEEN ? AND ?
			ORDER BY h.transfer_date DESC, t.type;
		");
		$objStmt->bind_param('ss', $strStartDate, $strEndDate);
		$objStmt->execute();
		$objStmt->bind_result($intReferenceId, $intAssetId, $strTransferDate, $strNotes, $intVersalId, $strPurchaseOrderId, $strType, $strDescription, $strStatus, $strGroup, $strFromStatus, $strToStatus, $strFromEmployee, $strToEmployee, $strFromDepartment, $strToDepartment);

		while ($objStmt->fetch()) {
			$arrTransfer = array();
			$arrTransfer['date'] = $strTransferDate;
			$arrTransfer['reference_id'] = sprintf('AT%s', str_pad($intReferenceId, 6, '0', STR_PAD_LEFT));
			$arrTransfer['versal_id'] = str_pad($intVersalId, 6, '0', STR_PAD_LEFT);
			$arrTransfer['purchase_order_id'] = (is_null($strPurchaseOrderId))?'-':$strPurchaseOrderId;
			$arrTransfer['type'] = $strType;
			$arrTransfer['description'] = (is_null($strDescription))?'':$strDescription;
			$arrTransfer['status'] = $strStatus;
			$arrTransfer['group'] = (is_null($strGroup))?'':$strGroup;
			$arrTransfer['notes'] = (is_null($strNotes))?'':$strNotes;
			$arrTransfer['from_status'] = (is_null($strFromStatus))?'':$strFromStatus;
			$arrTransfer['to_status'] = (is_null($strToStatus))?'':$strToStatus;
			$arrTransfer['from_employee'] = (is_null($strFromEmployee))?'':$strFromEmployee;
			$arrTransfer['to_employee'] = (is_null($strToEmployee))?'':$strToEmployee;
			$arrTransfer['from_department'] = (is_null($strFromDepartment))?'':$strFromDepartment;
			$arrTransfer['to_department'] = (is_null($strToDepartment))?'':$strToDepartment;
			$arrMovements[$intReferenceId] = $arrTransfer;
			unset($arrTransfer);
			$intIndex++;
		}
		$objStmt->close();

		return $arrMovements;
	}

	public function GetEmployeesByManager() {
		$arrManagers = array();

		$objStmt = $this->objDb->prepare("
			SELECT 
				e.employee_id,
				CONCAT(e.lastname, ', ', e.firstname) AS name,
				d.department,
				e.remote_location
			FROM
				employee AS e
			JOIN department AS d ON d.department_id = e.department_id
			WHERE e.exit = 0
			ORDER BY d.department, e.lastname, e.firstname;
		");
		$objStmt->execute();
		$objStmt->bind_result($intManagerId, $strName, $strDepartment, $strRemoteLocation);

		while ($objStmt->fetch()) {
			$arrManagers[$intManagerId]['name'] = $strName;
			$arrManagers[$intManagerId]['department'] = $strDepartment;
			$arrManagers[$intManagerId]['location'] = ($strRemoteLocation == '')?'CA, USA':$strRemoteLocation;
		}
		$objStmt->close();

		foreach ($arrManagers as $intManagerId => $arrManager) {
			$objStmt = $this->objDb->prepare("
				SELECT
					e.employee_id,
					CONCAT(e.firstname, ' ', e.lastname),
					e.remote_location
				FROM employee AS e
				JOIN employee AS em ON e.manager_id = em.employee_id
				WHERE em.employee_id = ? AND e.exit = 0
				ORDER BY e.lastname, e.firstname;
			");
			$objStmt->bind_param('i', $intManagerId);
			$objStmt->execute();
			$objStmt->bind_result($intEmployeeId, $strName, $strRemoteLocation);

			$arrStaff = array();

			while ($objStmt->fetch()) {
				$arrStaff[$intEmployeeId]['name'] = $strName;
				$arrStaff[$intEmployeeId]['location'] = ($strRemoteLocation == '')?'CA, USA':$strRemoteLocation;;
			}

			if (count($arrStaff) > 0) {
				$arrManagers[$intManagerId]['staff'] = $arrStaff;
			} else {
				unset($arrManagers[$intManagerId]);
			}
			unset($arrStaff);

			$objStmt->close();
		}

		$arrManagers = array();

		$objStmt = $this->objDb->prepare("
			SELECT 
				e.employee_id,
				CONCAT(e.lastname, ', ', e.firstname) AS name,
				d.department,
				e.remote_location,
				e.exit
			FROM
				employee AS e
			JOIN department AS d ON d.department_id = e.department_id
			ORDER BY d.department, e.lastname, e.firstname;
		");
		$objStmt->execute();
		$objStmt->bind_result($intManagerId, $strName, $strDepartment, $strRemoteLocation, $intExit);

		while ($objStmt->fetch()) {
			$arrManagers[$intManagerId]['name'] = sprintf('%s%s', $strName, ($intExit == 0)?'':' [exit]');
			$arrManagers[$intManagerId]['department'] = $strDepartment;
			$arrManagers[$intManagerId]['location'] = ($strRemoteLocation == '')?'CA, USA':$strRemoteLocation;
		}
		$objStmt->close();

		foreach ($arrManagers as $intManagerId => $arrManager) {
			$objStmt = $this->objDb->prepare("
				SELECT
					e.employee_id,
					CONCAT(e.firstname, ' ', e.lastname),
					e.remote_location
				FROM employee AS e
				JOIN employee AS em ON e.manager_id = em.employee_id
				WHERE em.employee_id = ? AND e.exit = 0
				ORDER BY e.lastname, e.firstname;
			");
			$objStmt->bind_param('i', $intManagerId);
			$objStmt->execute();
			$objStmt->bind_result($intEmployeeId, $strName, $strRemoteLocation);

			$arrStaff = array();

			while ($objStmt->fetch()) {
				$arrStaff[$intEmployeeId]['name'] = $strName;
				$arrStaff[$intEmployeeId]['location'] = ($strRemoteLocation == '')?'CA, USA':$strRemoteLocation;;
			}

			if (count($arrStaff) > 0) {
				$arrManagers[$intManagerId]['staff'] = $arrStaff;
			} else {
				unset($arrManagers[$intManagerId]);
			}
			unset($arrStaff);

			$objStmt->close();
		}

		return $arrManagers;
	}
}
?>

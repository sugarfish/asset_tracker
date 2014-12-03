<?php
class InventoryModel extends ModelBase {

	private $objDb;

	public function __construct() {
		$this->objDb = ModelBase::getInstance();
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
			$arrItem['model'] = ($strModel == 0)?'':$strModel;
			array_push($arrOtherComputers, $arrItem);
			unset($arrItem);
		}
		$objStmt->close();

		return array(
			'apple_computers' => $arrAppleComputers,
			'other_computers' => $arrOtherComputers
		);
	}
}
?>

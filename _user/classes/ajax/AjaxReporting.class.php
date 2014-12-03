<?php
ini_set('include_path', '.:/var/www/asset-track/includes');
require_once "prepend.inc.php";

class AjaxReporting extends AjaxBase {

	protected function Initialize() {
		$this->objReportingModel = new ReportingModel;
	}

	protected function getFilteredAssetHistory($arrArgs) {
		$arrItems = $this->objReportingModel->GetFilteredAssetHistory($arrArgs['filter']);

		$strXhtml = '';
		foreach ($arrItems as $intAssetId => $arrItem) {
			$strXhtml .= '<p>';
			$strXhtml .= '<table id="newspaper-a"><thead><th>ASSET ID</th><th>TYPE</th><th>DESCRIPTION</th><th>STATUS</th><th>GROUP</th></thead>';
			$strXhtml .= sprintf('<tbody><tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr></tbody>', $arrItem['details']['versal_id'], $arrItem['details']['type'], $arrItem['details']['description'], $arrItem['details']['status'], $arrItem['details']['assets']['group']);
			$strXhtml .= '</table>';

			$strXhtml .= '<table id="newspaper-a">';
			$strXhtml .= '<thead>';
			$strXhtml .= '<tr class="noborder"><th>REF.</th><th>DATE</th><th colspan="3">STATUS</th><th colspan="3">EMPLOYEE</th><th colspan="3">DEPARTMENT</th></tr>';
			$strXhtml .= '<tr><th colspan="2"></th><th>FROM</th><th>&#8658;</th><th>TO</th><th>FROM</th><th>&#8658;</th><th>TO</th><th>FROM</th><th>&#8658;</th><th>TO</th></tr>';
			$strXhtml .= '</thead>';
			foreach ($arrItem['history'] as $arrTransfer) {
				$strXhtml .= sprintf('<tbody><tr><td>%s</td><td><nobr>%s</nobr></td><td>%s</td><td></td><td>%s</td><td>%s</td><td></td><td>%s</td><td>%s</td><td></td><td>%s</td></tr></tbody>',
					$arrTransfer['reference_id'],
					$arrTransfer['date'],
					empty($arrTransfer['from_status'])?'-':$arrTransfer['from_status'],
					empty($arrTransfer['to_status'])?'-':$arrTransfer['to_status'],
					empty($arrTransfer['from_employee'])?'-':$arrTransfer['from_employee'],
					empty($arrTransfer['to_employee'])?'-':$arrTransfer['to_employee'],
					empty($arrTransfer['from_department'])?'-':$arrTransfer['from_department'],
					empty($arrTransfer['to_department'])?'-':$arrTransfer['to_department']
				);
			}
			$strXhtml .= '</table>';
			$strXhtml .= '</p>';
		}

		print $strXhtml;
	}

	protected function getFilteredAssetMovement($arrArgs) {
		$arrItems = $this->objReportingModel->GetFilteredAssetMovement($arrArgs['start_date'], $arrArgs['end_date']);

		$objDateTime = new DateTime;
		$strCurrentDate = null;
		$strXhtml = '';
		foreach ($arrItems as $intReferenceId => $arrItem) {
			if ($strCurrentDate != $arrItem['date']) {
				$strCurrentDate = $arrItem['date'];
				$objDateTime->setDate(substr($strCurrentDate, 0, 4), substr($strCurrentDate, 5, 2), substr($strCurrentDate, 8, 2));
				$strXhtml .= '<div class="asset-movement-date">';
				$strXhtml .= $objDateTime->format('F jS, Y');
				$strXhtml .= '</div>';
			}
			$strXhtml .= '<div class="asset-movement">';
			$strXhtml .= '<table id="newspaper-a"><thead><tr><th>ASSET ID</th><th>TYPE</th><th>DESCRIPTION</th><th>STATUS</th><th>GROUP</th><th>NOTES</th></tr></thead>';
			$strXhtml .= sprintf('<tbody><tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr></tbody>', $arrItem['versal_id'], $arrItem['type'], $arrItem['description'], $arrItem['status'], $arrItem['group'], $arrItem['notes']);
			$strXhtml .= '</table>';

			$strXhtml .= '<table id="newspaper-a">';
			$strXhtml .= '<thead>';
			$strXhtml .= '<tr class="noborder"><th>REF.</th><th>PO #</th><th colspan="3">STATUS</th><th colspan="3">EMPLOYEE</th><th colspan="3">DEPARTMENT</th></tr>';
			$strXhtml .= '<tr><th colspan="2"></th><th>FROM</th><th>&#8658;</th><th>TO</th><th>FROM</th><th>&#8658;</th><th>TO</th><th>FROM</th><th>&#8658;</th><th>TO</th></tr>';
			$strXhtml .= '</thead>';
			$strXhtml .= sprintf('<tbody><tr><td>%s</td><td><nobr>%s</nobr></td><td>%s</td><td></td><td>%s</td><td>%s</td><td></td><td>%s</td><td>%s</td><td></td><td>%s</td></tr></tbody>',
				$arrItem['reference_id'],
				$arrItem['purchase_order_id'],
				empty($arrItem['from_status'])?'-':$arrItem['from_status'],
				empty($arrItem['to_status'])?'-':$arrItem['to_status'],
				empty($arrItem['from_employee'])?'-':$arrItem['from_employee'],
				empty($arrItem['to_employee'])?'-':$arrItem['to_employee'],
				empty($arrItem['from_department'])?'-':$arrItem['from_department'],
				empty($arrItem['to_department'])?'-':$arrItem['to_department']
			);
			$strXhtml .= '</table>';
			$strXhtml .= '</div>';
		}

		print $strXhtml;
	}

	protected function getLastMonthDateRange() {
		$objDateTime = new DateTime();
		$objDateTime->setDate($objDateTime->format('Y'), $objDateTime->format('m') - 1, 1);
		$strStartDate = $objDateTime->format('mdY');

		$objDateTime->setDate($objDateTime->format('Y'), $objDateTime->format('m'), cal_days_in_month(CAL_GREGORIAN, $objDateTime->format('m'), $objDateTime->format('Y')));
		$strEndDate = $objDateTime->format('mdY');

		print json_encode(
			array(
				'start' => $strStartDate,
				'end' => $strEndDate
			)
		);
	}

	protected function getThisMonthDateRange() {
		$objDateTime = new DateTime();
		$objDateTime->setDate($objDateTime->format('Y'), $objDateTime->format('m'), 1);
		$strStartDate = $objDateTime->format('mdY');

		$objDateTime->setDate($objDateTime->format('Y'), $objDateTime->format('m'), cal_days_in_month(CAL_GREGORIAN, $objDateTime->format('m'), $objDateTime->format('Y')));
		$strEndDate = $objDateTime->format('mdY');

		print json_encode(
			array(
				'start' => $strStartDate,
				'end' => $strEndDate
			)
		);
	}

	protected function getAcl() {
		session_start();
		print json_encode(array('acl' => $_SESSION['arrUser']['type']));
	}
}

new AjaxReporting;
?>

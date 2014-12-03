<?php
class trackingController extends Controller {

	protected function Initialize() {
		Session::Initialize();
		$this->objReportingModel = new ReportingModel;

		$this->objHeader = new Header;
		$this->objFooter = new Footer;
	}

	public function adminAction() {
		$arrUser = Session::GetUser(true);
		$this->_CONTROL->Append(ucwords(substr($arrUser['name'], 0, strpos($arrUser['name'], chr(32)))), 'name');
		$this->_CONTROL->Append((strpos(__DB__, 'dev') !== false)?'<div style="color:red">Development Database</div>':'', 'db');

		$this->objHeader
			->SetPageId('admin')
			->SetPageTitle('Asset Tracking');
		
		/**
		 * Set a different template name...
		 * $this->Template = 'index';
		 */
	}

	public function reportingAction() {
		$arrUser = Session::GetUser(true);
		$this->_CONTROL->Append(ucwords(substr($arrUser['name'], 0, strpos($arrUser['name'], chr(32)))), 'name');
		$this->_CONTROL->Append((strpos(__DB__, 'dev') !== false)?'<div style="color:red">Development Database</div>':'', 'db');

		$this->objHeader
			->SetPageId('reporting')
			->SetPageTitle('Asset Tracking - Reporting');

		/* All Assets by Asset ID */
		$arrItems = $this->objReportingModel->GetAssetsByAssetID();

		$strXhtml = '<table id="newspaper-a"><thead><tr><th>ASSET ID</th><th>SERIAL NUMBER</th><th>EMPLOYEE</th><th>DEPARTMENT</th><th>ITEM</th><th>DESCRIPTION</th><th>STATUS</th><th>PO #</th></tr></thead>';
		Application::Log($arrItems);
		foreach ($arrItems as $arrItem) {
			$strXhtml .= sprintf(
				'<tbody><tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr></tbody>',
				$arrItem['versal_id'],
				$arrItem['serial_number'],
				$arrItem['name'],
				empty($arrItem['asset_department'])?$arrItem['department']:$arrItem['asset_department'],
				$arrItem['type'],
				$arrItem['description'],
				$arrItem['status'],
				$arrItem['purchase_order_id']
			);
		}
		$strXhtml .= '</table>';

		$this->_CONTROL->Append($strXhtml, 'assetsbyassetid');

		/* All Assets by Employee */
		$arrItems = $this->objReportingModel->GetAssetsByEmployee();

		$strXhtml = '<table id="newspaper-a"><thead><tr><th>EMPLOYEE</th><th>DEPARTMENT</th><th>LOCATION</th><th>ASSET ID</th><th>ITEM</th><th>DESCRIPTION</th><th>PO #</th></tr></thead>';
		foreach ($arrItems as $arrItem) {
			$strXhtml .= sprintf(
				'<tbody><tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr></tbody>',
				$arrItem['name'],
				$arrItem['department'],
				$arrItem['location'],
				$arrItem['versal_id'],
				$arrItem['type'],
				$arrItem['description'],
				$arrItem['purchase_order_id']
			);
		}
		$strXhtml .= '</table>';

		$this->_CONTROL->Append($strXhtml, 'assetsbyemployee');

		/* Department Assets */
		$arrItems = $this->objReportingModel->GetAssetsByDepartment();

		$strXhtml = '<table id="newspaper-a"><thead><tr><th>DEPARTMENT</th><th>ASSET ID</th><th>ITEM</th><th>DESCRIPTION</th></tr></thead>';
		foreach ($arrItems as $arrItem) {
			$strXhtml .= sprintf('
				<tbody><tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr></tbody>',
				$arrItem['asset_department'],
				$arrItem['versal_id'],
				$arrItem['type'],
				$arrItem['description']
			);
		}
		$strXhtml .= '</table>';

		$this->_CONTROL->Append($strXhtml, 'assetsbydepartment');

		/* Group Assets */
		$arrItems = $this->objReportingModel->GetAssetsByGroup();

		$strXhtml = '<table id="newspaper-a"><thead><tr><th>GROUP</th><th>ASSET ID</th><th>ITEM</th><th>DESCRIPTION</th></tr></thead>';
		foreach ($arrItems as $arrItem) {
			$strXhtml .= sprintf('
				<tbody><tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr></tbody>',
				$arrItem['group'],
				$arrItem['versal_id'],
				$arrItem['type'],
				$arrItem['description']
			);
		}
		$strXhtml .= '</table>';

		$this->_CONTROL->Append($strXhtml, 'assetsbygroup');

		/* Asset History */
		$arrItems = $this->objReportingModel->GetFilteredAssetHistory();

		$strXhtml = '';
		foreach ($arrItems as $intAssetId => $arrItem) {
			$strXhtml .= '<p>';
			$strXhtml .= '<table id="newspaper-a"><thead><tr><th>ASSET ID</th><th>TYPE</th><th>DESCRIPTION</th><th>STATUS</th><th>GROUP</th></tr></thead>';
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

		$this->_CONTROL->Append($strXhtml, 'assethistory');

		/* Asset Movement */
		$arrItems = $this->objReportingModel->GetFilteredAssetMovement();

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

		$this->_CONTROL->Append($strXhtml, 'assetmovement');

		/* Employees (by Manager) */
		$arrManagers = $this->objReportingModel->GetEmployeesByManager();

		$strXhtml = '';
		foreach ($arrManagers as $intManagerId => $arrManager) {
			$strXhtml .= '<div class="employees">';
			$strXhtml .= '<table id="newspaper-a"><thead><th>MANAGER</th><th>DEPARTMENT</th><th>LOCATION</th></thead>';
			$strXhtml .= sprintf('<tbody><tr><td>%s</td><td>%s</td><td>%s</td></tr></tbody>', $arrManager['name'], $arrManager['department'], $arrManager['location']);
			$strXhtml .= '</table>';

			$strXhtml .= '<table id="newspaper-a">';
			$strXhtml .= '<thead>';
			$strXhtml .= '<tr><th></th><th>EMPLOYEE</th><th>LOCATION</th></tr>';
			$strXhtml .= '</thead>';
			foreach ($arrManager['staff'] as $arrStaff) {
				$strXhtml .= sprintf('<tbody><tr><td></td><td>%s</td><td>%s</td></tr></tbody>', $arrStaff['name'], $arrStaff['location']);
			}
			$strXhtml .= '</table>';
			$strXhtml .= '</div>';
		}

		$this->_CONTROL->Append($strXhtml, 'employees');

	}

	public function exportAction() {
		$this->SetLayout(Layout::None);

		switch(Application::Request('report')) {
			case 'report-1':
				$strFilename = "asset_tracking_asset_id_export_" . date('Ymd') . ".xls";
				$arrData = $this->objReportingModel->GetAssetsByAssetID();
				break;
			case 'report-2':
				$strFilename = "asset_tracking_employees_export_" . date('Ymd') . ".xls";
				$arrData = $this->objReportingModel->GetAssetsByEmployee();
				break;
			case 'report-3':
				$strFilename = "asset_tracking_departments_export_" . date('Ymd') . ".xls";
				$arrData = $this->objReportingModel->GetAssetsByDepartment();
				break;
			case 'report-4':
				$strFilename = "asset_tracking_groups_export_" . date('Ymd') . ".xls";
				$arrData = $this->objReportingModel->GetAssetsByGroup();
				break;
			case 'report-5':
				$strFilename = "asset_tracking_history_export_" . date('Ymd') . ".xls";
				$strFilter = Application::Request('filter');

				$arrData = $this->objReportingModel->GetFilteredAssetHistory($strFilter);
				break;
			case 'report-6':
				$strFilename = "asset_tracking_movement_export_" . date('Ymd') . ".xls";
				$strStartDate = Application::Request('start_date');
				$strEndDate = Application::Request('end_date');
				Application::Log($strStartDate . ' > ' . $strEndDate);

				$arrData = $this->objReportingModel->GetFilteredAssetMovement($strStartDate, $strEndDate);
				break;
			case 'report-7':
				$strFilename = "employees_by_manager_export_" . date('Ymd') . ".xls";
				$arrData = $this->objReportingModel->GetEmployeesByManager();
		}

		header(sprintf("Content-Disposition: attachment; filename=%s", $strFilename));
		header(sprintf("Content-Type: %s", MimeType::MsExcel));

		print '<html><table>';

		$strReport = Application::Request('report');

		switch ($strReport) {
			case 'report-5':
				foreach ($arrData as $arrAsset) {
	  				printf("%s\r\n", $this->BuildRow(array_keys($arrAsset['details']), true));
		  			printf("%s\r\n", $this->BuildRow(array_values($arrAsset['details'])));

		  			$blnFlag = false;
	  				foreach ($arrAsset['history'] as $arrItem) {
	  					if (!$blnFlag) {
	  						printf("%s\r\n", $this->BuildRow(array_keys($arrItem), true));
			  				$blnFlag = true;
	  					}
	  					printf("%s\r\n", $this->BuildRow(array_values($arrItem)));
	  				}
	  				print '<tr><td colspan="8"></td></tr>';
	  			}
	  			break;

			case 'report-6':
				foreach ($arrData as $arrAsset) {
	  				printf("%s\r\n", $this->BuildRow(array_keys($arrAsset), true));
		  			printf("%s\r\n", $this->BuildRow(array_values($arrAsset)));

		  			/*
		  			$blnFlag = false;
	  				foreach ($arrAsset['history'] as $arrItem) {
	  					if (!$blnFlag) {
	  						printf("%s\r\n", $this->BuildRow(array_keys($arrItem), true));
			  				$blnFlag = true;
	  					}
	  					printf("%s\r\n", $this->BuildRow(array_values($arrItem)));
	  				}
	  				print '<tr><td colspan="8"></td></tr>';
	  				*/
	  			}
	  			break;

	  		case 'report-7':
	  			foreach ($arrData as $arrManager) {
	  				printf("%s\r\n", str_replace('Staff', '', $this->BuildRow(array_keys($arrManager), true)));
		  			printf("%s\r\n", str_replace('Array', '', $this->BuildRow(array_values($arrManager))));

		  			$blnFlag = false;
	  				foreach ($arrManager['staff'] as $arrStaff) {
	  					if (!$blnFlag) {
	  						printf("%s\r\n", $this->BuildRow(array_keys($arrStaff), true));
			  				$blnFlag = true;
	  					}
	  					printf("%s\r\n", $this->BuildRow(array_values($arrStaff)));
	  				}
	  				print '<tr><td colspan="4"></td></tr>';
	  			}
	  			break;

	  		default:
	  			$blnFlag = false;

				foreach ($arrData as $arrRow) {
				    if (!$blnFlag) {
						printf("%s\r\n", $this->BuildRow(array_keys($arrRow), true));
		      			$blnFlag = true;
		    		}
		    		array_walk($arrRow, array($this, 'CleanData'));
		    		printf("%s\r\n", $this->BuildRow(array_values($arrRow)));
		  		}
		  		exit;
		}

	  	print '</table></html>';
	}

	/** audit method **/

	public function auditAction() {
		$arrUser = Session::GetUser(true);
		$this->_CONTROL->Append(ucwords(substr($arrUser['name'], 0, strpos($arrUser['name'], chr(32)))), 'name');
		$this->_CONTROL->Append((strpos(__DB__, 'dev') !== false)?'<div style="color:red">Development Database</div>':'', 'db');

		$this->objHeader
			->SetPageTitle('audit')
			->SetPageId('audit');
	}

	public function loginAction() {
		$this->objHeader
			->SetPageTitle('login')
			->SetPageId('login');

        if(Application::Request('pg') == 1) {
            Session::LogIn(Application::Request('txtUsername'), Application::Request('txtPassword'));
            $this->_CONTROL->Append(Session::$strError, 'error');
        }
	}

    public function logoutAction() {
        Session::LogOut();
    }

	private function BuildRow($arrValues, $blnHeading = false) {
		$strRow = '';
		foreach ($arrValues as $strValue) {
			$strValue = str_replace('_', chr(32), $strValue);
			$strRow .= (!$blnHeading)?sprintf('<td style="text-align:left">%s</td>', $strValue):sprintf('<td style="text-align:left"><strong>%s</strong></td>', ucwords($strValue));
		}
		$strRow = (!$blnHeading)?sprintf('<tr style="background:#f2fae1">%s</tr>', $strRow):sprintf('<tr style="background:#d5f7a1">%s</tr>', $strRow);

		return $strRow;
	}

    private function CleanData(&$strValue) {
    	$strValue = preg_replace("/\r?\n/", "\\n", preg_replace("/\t/", "\\t", $strValue));
    }

	public function ActionErrorHandler($strController, $strAction) {
		$this->objHeader
			->SetPageTitle('Oh no... the page is not here!');
	}
}
?>

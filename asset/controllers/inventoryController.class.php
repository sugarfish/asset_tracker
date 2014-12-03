<?php
class inventoryController extends Controller {

	protected function Initialize() {
		Session::Initialize();
		$this->InventoryModel = new InventoryModel;

		$this->objHeader = new Header;
		$this->objFooter = new Footer;

		$this->objHeader
			->SetPageId('inventory')
			->SetPageTitle('Computers in Inventory');
	}

	public function lookupAction() {
		$this->SetTemplate('inventory');

		$arrInventory = $this->InventoryModel->GetInventoryData();

		$this->_CONTROL->Append($arrInventory['apple_computers'], 'apple_computers');
		$this->_CONTROL->Append($arrInventory['other_computers'], 'other_computers');
	}

	public function ActionErrorHandler($strController, $strAction) {
		$this->objHeader
			->SetPageTitle('Oh no... the page is not here!');
	}
}
?>

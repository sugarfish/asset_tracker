<?php

class indexController extends Controller {

	protected function Initialize() {
		Session::Initialize();

		$this->objHeader = new Header();
		$this->objFooter = new Footer();

		$this->objHeader
			->SetPageId('home');
	}

	public function indexAction() {
		$this->SetLayout(Layout::None);
		Application::Redirect(
			UrlHandler::BuildUrl(
				array(
					'module'  	 => 'asset',
					'controller' => 'tracking',
					'action' 	 => 'admin'
				)
			)
		);
	}
}
?>

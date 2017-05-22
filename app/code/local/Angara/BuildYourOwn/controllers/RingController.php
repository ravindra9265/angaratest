<?php
class Angara_BuildYourOwn_RingController extends Mage_Core_Controller_Front_Action
{
	private $_categoryId = 470;
	public function indexAction(){
		$byoModel = Mage::getModel('buildyourown/jewelry_ring');
		$userStartWithPreference = $this->getRequest()->getParam('startWithPref');
		if($userStartWithPreference){
			$byoModel->setUserStartWithPreference($userStartWithPreference);
		}
		else{
			$userStartWithPreference = $byoModel->getUserStartWithPreference();
		}
		$startWithSetting = ($userStartWithPreference == 'setting');
		if(!$byoModel->hasDiamondSelected() && !$byoModel->hasSettingSelected()){
			if($startWithSetting){
				$this->_forward('settings');
			}
			else{
				$this->_forward('diamonds');
			}
		}
		else if($byoModel->hasDiamondSelected() && !$byoModel->hasSettingSelected()){
			$this->_forward('settings');
		}
		else if($byoModel->hasSettingSelected() && !$byoModel->hasDiamondSelected()){
			$this->_forward('diamonds');
		}
		else{
			$this->_forward('review');
		}
	}
	
	public function filterDiamondsAction(){
		$filterParams = $this->getRequest()->getParam('userFilters');
		$ringModel = Mage::getModel('buildyourown/jewelry_ring')->setUserFilters($filterParams);
		$this->_forward('diamondGrid');
	}
	
	public function selectDiamondAction(){
		$diamondId = $this->getRequest()->getParam('diamondId');
		Mage::getModel('buildyourown/jewelry_ring')->selectDiamond($diamondId);
		$this->_forward('index');
	}
	
	public function removeDiamondAction(){
		Mage::getModel('buildyourown/jewelry_ring')->selectDiamond(false);
		//$this->_forward('index');
		$this->_redirect('*/*/diamonds');
	}
	
	public function removeDiamondPairAction(){
		Mage::getModel('buildyourown/jewelry_ring')->selectDiamondPair(false);
		$this->_forward('index');
	}
	
	public function removeSettingAction(){
		Mage::getModel('buildyourown/jewelry_ring')->selectSetting(false);
		//$this->_forward('index');
		$this->_redirect('*/*/index');
	}
	
	public function diamondsAction(){
		$byoJewelryModel = Mage::getModel('buildyourown/jewelry_ring');
		Mage::register('byoJewelryModel', $byoJewelryModel);
		$this->loadLayout();
		$this->getLayout()->getBlock("head")->setTitle($this->__("Build Your Own Ring or Engagement Ring"));
		$this->getLayout()->getBlock("head")->setDescription($this->__("Angara makes it easy to create your own custom diamond Engagement Ring in 3 steps - choose a diamond, choose your setting, and we'll do the rest."));
		/*$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"  => Mage::getBaseUrl()
		));

      	$breadcrumbs->addCrumb("build your own ring", array("label" => $this->__("Build Your Own Ring"),"title" => $this->__("Build Your Own Ring")));*/
      	$this->renderLayout();
	}
	
	public function diamondGridAction(){
		$page = (int)$this->getRequest()->getParam('page', 1);
		$sortBy = $this->getRequest()->getParam('sidx');
		$sortDir = $this->getRequest()->getParam('sord');
		$ringModel = Mage::getModel('buildyourown/jewelry_ring')->setUserFilters(array('sortBy' => $sortBy, 'sortDir' => $sortDir));
		
		$diamondsFeed = $ringModel->getDiamondFeed($page);
		if($diamondsFeed){
			$result = array();
			$result['page'] = $page;
			$result['total'] = ceil($diamondsFeed['search_results']['total_diamonds_found'] / 50);
			$result['records'] = $diamondsFeed['search_results']['total_diamonds_found'];
			$diamonds = $diamondsFeed['diamonds'];
			foreach($diamonds as $iCounter => $diamond){
				$result['rows'][$iCounter]['id'] = $diamond['diamond_id'];
				$result['rows'][$iCounter]['cell'] = array($diamond['diamond_id'],$diamond['shape'],(($diamond['size'] == '') ? 'None' : $diamond['size']),(($diamond['color'] == '') ? 'None' : $diamond['color']),(($diamond['clarity'] == '') ? 'None' : $diamond['clarity']),(($diamond['cut'] == '') ? 'None' : $diamond['cut']),Mage::helper('core')->currency($diamond['total_sales_price_in_currency'], true, false));
			}
			echo json_encode($result);
		}
	}
	
	public function settingsAction(){
		$_category = Mage::getModel('catalog/category')->load($this->_categoryId);
		$redirect = $_category->getUrl($_category);
		$this->getResponse()->setRedirect($redirect);
	}
	
	public function reviewAction(){
		$setting = Mage::getModel('buildyourown/jewelry_ring')->getSettingSelected();
		if($setting['productId']){
			$product = Mage::getModel('catalog/product')->load($setting['productId']);
			if($product->getId()){
				$this->getResponse()->setRedirect('/catalog/product/view/id/'.$product->getId());
			}
			else{
				$this->_forward('settings');
			}
		}
		else{
			$this->_forward('settings');
		}
	}
}
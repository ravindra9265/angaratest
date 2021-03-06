<?php
class Angara_Promotions_Block_Adminhtml_Channel_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("channel_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("promotions")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("promotions")->__("Item Information"),
				"title" => Mage::helper("promotions")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("promotions/adminhtml_channel_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

<?php
class Angara_Productbase_Block_Adminhtml_Stones_Weights extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
    {
     //where is the controller
     $this->_controller = 'adminhtml_stones_weights';
     $this->_blockGroup = 'productbase';
 
     //text in the admin header
     $this->_headerText = 'Angara productbase management';
 
     //value of the add button
     $this->_addButtonLabel = 'Add a stone weight';
 
     parent::__construct();
     }
    
}

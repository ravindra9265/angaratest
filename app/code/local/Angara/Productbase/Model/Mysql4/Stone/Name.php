<?php
class Angara_Productbase_Model_Mysql4_Stone_Name extends Mage_Core_Model_Mysql4_Abstract 
{
    public function _construct()
    {
       $this->_init('productbase/stonename', 'id');
    }
}